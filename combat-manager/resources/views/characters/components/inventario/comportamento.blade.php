    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('characterInventoryV10', (
                initialItems,
                initialWallet,
                rarityLabels,
                natureLabels,
                abilityLabels,
                recoveryLabels,
                weaponTraitDefinitions,
                masteryDefinitions,
                urls,
                inventoryCanCreateItems = true
            ) => ({
                items: initialItems,
                rarityLabels,
                natureLabels,
                abilityLabels,
                recoveryLabels,
                weaponTraitDefinitions,
                masteryDefinitions,
                urls,

                // V66.2.43: a futura regra opcional chega por um contrato do
                // servidor. O bloqueio definitivo deverá ocorrer na rota POST.
                canCreateInventoryItems: inventoryCanCreateItems === true,

                detailOpen: false,
                detailItem: null,

                // V66.1.29 — acompanha o patamar já visto de cada Vestígio
                // por personagem. A primeira visita não dispara uma ascensão falsa.
                vestigeMilestoneStorageKey: @js('spellbound-vestiges-seen-v1-' . $character->id),
                vestigeSeenStages: null,
                vestigeGlowingItemIds: {},
                vestigeGlowTimers: {},
                vestigeNotice: null,
                vestigeNoticeQueue: [],
                vestigeNoticeTimer: null,

                editorOpen: false,
                editingId: null,
                form: null,

                editorTooltip: {
                    open: false,
                    title: '',
                    text: '',
                    left: 0,
                    top: 0,
                },
                modulePickerOpen: false,
                saving: false,
                saveError: null,
                deleteConfirmOpen: false,

                // Uso único: confirmação de consumo e descarte do item já utilizado.
                singleUseConfirmOpen: false,
                pendingSingleUse: null,
                discardUsedConfirmOpen: false,
                pendingDiscardItem: null,

                imageFile: null,
                imagePreview: null,

                foundItemsOpen: false,
                foundLootItems: [],
                foundLootCurrencies: [],
                foundLootLoading: false,
                foundLootLoaded: false,
                foundLootError: null,
                foundLootBusyItemId: null,
                foundLootBusyCurrencyId: null,
                foundLootCurrencyDrafts: {},

                equippedDrawerOpen: false,
                attunementPickerOpen: false,

                /*
                |--------------------------------------------------------------------------
                | Gavetas dos grupos do inventário
                |--------------------------------------------------------------------------
                */

                inventoryGroupOpen: {
                    wonderful: true,
                    technological: true,
                    mundane: true,
                },

                inventoryGroupStorageKey:
                    @js($inventoryGroupStorageKey),

                /*
                |--------------------------------------------------------------------------
                | Pesquisa por categoria
                |--------------------------------------------------------------------------
                |
                | Cada gaveta possui seu próprio termo de pesquisa.
                |
                */

                inventoryGroupSearch: {
                    wonderful: '',
                    technological: '',
                    mundane: '',
                },

                attunementWarningOpen: false,
                pendingAttunementItem: null,
                // Preferência individual e ajustável. A sintonização dos itens
                // continua sendo salva pelas rotas existentes do personagem.
                attunementSettingsOpen: false,
                attunementSlotLimit: 3,
                attunementSlotStorageKey: @js('spellbound-attunement-slots-v1-' . $character->id),

                busyItemId: null,
                busyFeatureKey: null,

                wallet: {
                    copper: parseInt(initialWallet?.copper) || 0,
                    silver: parseInt(initialWallet?.silver) || 0,
                    electrum: parseInt(initialWallet?.electrum) || 0,
                    gold: parseInt(initialWallet?.gold) || 0,
                    platinum: parseInt(initialWallet?.platinum) || 0,
                },
                walletOpen: false,
                walletSaving: false,
                walletPendingSave: false,
                walletTimer: null,

                inventorySyncBusy: false,
                inventorySyncVersion: null,
                inventorySyncTimer: null,
                inventoryVisibilityHandler: null,

                init() {
                    this.items =
                        this.items.map(
                            item =>
                                this.normalizeItem(
                                    item
                                )
                        );

                    this.restoreInventoryGroupDrawers();
                    this.restoreAttunementSlots();
                    this.syncVestigeMilestones();
                    this.startInventorySync();
                },

                destroy() {
                    if (this.vestigeNoticeTimer !== null) {
                        clearTimeout(this.vestigeNoticeTimer);
                        this.vestigeNoticeTimer = null;
                    }
                    Object.values(this.vestigeGlowTimers).forEach(timer => clearTimeout(timer));
                    this.vestigeGlowTimers = {};
                    if (this.inventorySyncTimer !== null) {
                        clearInterval(this.inventorySyncTimer);
                        this.inventorySyncTimer = null;
                    }

                    if (this.inventoryVisibilityHandler) {
                        document.removeEventListener(
                            'visibilitychange',
                            this.inventoryVisibilityHandler
                        );
                        this.inventoryVisibilityHandler = null;
                    }
                },

                startInventorySync() {
                    if (!this.urls.sync || this.inventorySyncTimer !== null) {
                        return;
                    }

                    this.inventoryVisibilityHandler = () => {
                        if (!document.hidden) {
                            this.refreshInventory();
                        }
                    };

                    document.addEventListener(
                        'visibilitychange',
                        this.inventoryVisibilityHandler
                    );

                    // Primeira consulta silenciosa; os itens já vêm do Blade.
                    this.refreshInventory(true);

                    // Apenas enquanto a aba estiver visível.
                    this.inventorySyncTimer = setInterval(
                        () => this.refreshInventory(),
                        3000
                    );
                },

                async refreshInventory(silent = false) {
                    if (
                        this.inventorySyncBusy
                        || !this.urls.sync
                        || document.hidden
                        || this.saving
                        || this.busyItemId !== null
                        || this.busyFeatureKey !== null
                        || this.editorOpen
                        || this.deleteConfirmOpen
                        || this.singleUseConfirmOpen
                        || this.discardUsedConfirmOpen
                    ) {
                        return;
                    }

                    this.inventorySyncBusy = true;

                    try {
                        const url = new URL(
                            this.urls.sync,
                            window.location.origin
                        );

                        if (this.inventorySyncVersion) {
                            url.searchParams.set(
                                'version',
                                this.inventorySyncVersion
                            );
                        }

                        const response = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            cache: 'no-store',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        if (!data?.success) {
                            return;
                        }

                        if (Array.isArray(data.items)) {
                            const previous = this.items;
                            const oldIds = new Set(
                                previous.map(item => Number(item.id))
                            );
                            const previousById = new Map(
                                previous.map(item => [Number(item.id), item])
                            );
                            const next = data.items.map(raw => {
                                const incomingItem = this.normalizeItem(raw);
                                const existingItem = previousById.get(Number(incomingItem.id));

                                // Reutiliza a referência do Alpine se nada mudou.
                                return existingItem
                                    && JSON.stringify(existingItem) === JSON.stringify(incomingItem)
                                        ? existingItem
                                        : incomingItem;
                            });
                            const nextIds = new Set(
                                next.map(item => Number(item.id))
                            );
                            const incoming = next.filter(
                                item => !oldIds.has(Number(item.id))
                            );

                            const oldEquipped = previous.filter(
                                item => !!item.equipped
                            );

                            // Não dispara reconstrução dos cards em consultas sem mudanças.
                            if (
                                next.length !== previous.length
                                || next.some((item, index) => item !== previous[index])
                            ) {
                                this.items = next;
                                this.syncVestigeMilestones();
                            }

                            // A Party mantém sua própria lista para transferências.
                            // Sincronizamos essa lista sem reabrir a Party.
                            window.dispatchEvent(new CustomEvent(
                                'character-inventory-synced',
                                { detail: { items: next } }
                            ));

                            // Se um item visualizado mudou, atualiza o modal sem
                            // fechá-lo; se saiu do inventário, fecha o detalhe.
                            if (this.detailItem) {
                                const updatedDetail = next.find(
                                    item => Number(item.id) === Number(this.detailItem.id)
                                );
                                if (updatedDetail) {
                                    if (this.detailItem !== updatedDetail) {
                                        this.detailItem = updatedDetail;
                                    }
                                } else {
                                    this.closeDetail();
                                }
                            }

                            // Atualiza atributos derivados de equipamento na
                            // ficha, caso um item equipado tenha sido removido
                            // ou alterado em outra sessão.
                            for (const previousItem of oldEquipped) {
                                const updatedItem = next.find(
                                    item => Number(item.id) === Number(previousItem.id)
                                );
                                if (
                                    !updatedItem
                                    || JSON.stringify(updatedItem) !== JSON.stringify(previousItem)
                                ) {
                                    window.dispatchEvent(new CustomEvent(
                                        'character-equipment-updated',
                                        {
                                            detail: {
                                                item: updatedItem || {
                                                    id: previousItem.id,
                                                    deleted: true,
                                                },
                                            },
                                        }
                                    ));
                                }
                            }

                            const notices = Array.isArray(data.notifications)
                                ? data.notifications
                                : [];
                            if (!silent && incoming.length > 0 && notices.length === 0) {
                                const message = incoming.length === 1
                                    ? `Novo item no inventário: ${incoming[0].name}.`
                                    : `${incoming.length} novos itens no inventário.`;
                                this.showToast(message);
                            }
                        }

                        // Transferências podem ter avisos pendentes mesmo quando
                        // a versão do inventário não mudou. Não dependem da
                        // identificação de itens novos (nem do modo silencioso).
                        for (const notice of (data.notifications ?? [])) {
                            if (notice?.type !== 'item_transfer') continue;
                            window.dispatchEvent(new CustomEvent('spellbound-notify', {
                                detail: {
                                    title: 'Item recebido!',
                                    message: String(notice.message ?? ''),
                                    variant: 'success',
                                    duration: 6000,
                                },
                            }));
                        }

                        if (typeof data.version === 'string') {
                            this.inventorySyncVersion = data.version;
                        }
                    } catch (error) {
                        // Falhas temporárias não interrompem o uso da ficha.
                        console.debug('Sincronização do inventário indisponível:', error);
                    } finally {
                        this.inventorySyncBusy = false;
                    }
                },

                get totalCount() {
                    return this.items.length;
                },

                get foundLootCount() {
                    return this.foundLootItems.length + this.foundLootCurrencies.length;
                },

                get hasFoundLoot() {
                    return this.foundLootCount > 0;
                },

                get walletTotalCoins() {
                    return [
                        this.wallet.copper,
                        this.wallet.silver,
                        this.wallet.electrum,
                        this.wallet.gold,
                        this.wallet.platinum,
                    ].reduce(
                        (total, value) => total + Math.max(0, parseInt(value) || 0),
                        0
                    );
                },

                get equippedItems() {
                    return this.items
                        .filter(item => !!item.equipped)
                        .sort((a, b) => {
                            if (!!a.attuned !== !!b.attuned) {
                                return a.attuned ? -1 : 1;
                            }

                            return String(a.name ?? '').localeCompare(String(b.name ?? ''));
                        });
                },

                /*
                |--------------------------------------------------------------------------
                | Pesquisa / ordenação por categoria
                |--------------------------------------------------------------------------
                */

                normalizeInventorySearch(value) {
                    return String(
                        value
                        ?? ''
                    )
                        .normalize('NFD')
                        .replace(
                            /[\u0300-\u036f]/g,
                            ''
                        )
                        .toLocaleLowerCase('pt-BR')
                        .trim();
                },

                /*
                |--------------------------------------------------------------------------
                | V66.1.28 — Artefatos e Vestígios na ficha do personagem
                | Apenas as etapas desbloqueadas pela proficiência são apresentadas.
                | Uma intervenção manual do Mestre exige persistência por personagem
                | no backend; nunca usar current_stage global do Cofre como override.
                |--------------------------------------------------------------------------
                */
                isArtifact(item) {
                    return item?.rarity === 'artifact';
                },

                // Tiptap do Cofre: renderização por lista de elementos permitidos.
                // Nunca injeta HTML bruto de um item no modal ou no Inventário.
                formatItemRichText(raw, compact = false) {
                    const source = String(raw ?? '').trim();
                    if (!source) return '';
                    const escape = value => String(value).replace(/[&<>"']/g, c => ({
                        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
                    })[c]);
                    const legacyMarks = value => escape(value)
                        .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                    // Compatibilidade com itens anteriores à introdução do Tiptap.
                    let markup = source;
                    const richPattern = /<\/?(?:p|div|br|strong|b|em|i|u|s|del|ul|ol|li|blockquote|h[1-6]|a|code|pre|hr)\b/i;
                    if (!richPattern.test(markup) && /&lt;\/?(?:p|strong|em|h[1-6]|ul|li|a)\b/i.test(markup)) {
                        const decoder = document.createElement('textarea');
                        decoder.innerHTML = markup;
                        markup = decoder.value;
                    }
                    if (!richPattern.test(markup)) {
                        if (compact) return legacyMarks(markup.replace(/\r\n?/g, '\n').replace(/\n+/g, ' '));
                        const blocks=[];
                        let group=[];
                        const flush=()=>{if(group.length){blocks.push('<p>'+group.join('<br>')+'</p>');group=[];}};
                        for (const line of markup.replace(/\r\n?/g,'\n').split('\n')) {
                            if (/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/.test(line)) {flush();blocks.push('<hr>');}
                            else if (!line.trim()) flush();
                            else group.push(legacyMarks(line));
                        }
                        flush();
                        return blocks.join('');
                    }
                    const doc = new DOMParser().parseFromString(markup, 'text/html');
                    const allowed = new Set(['p','br','strong','em','u','s','ul','ol','li','blockquote','h2','h3','h4','code','pre','hr','a']);
                    const blocked = new Set(['script','style','iframe','object','embed','svg','math','template','form','video','audio','img']);
                    const clean = node => {
                        if (node.nodeType===3) return legacyMarks(node.textContent);
                        if (node.nodeType!==1) return '';
                        const tag=node.tagName.toLowerCase();
                        if (blocked.has(tag)) return '';
                        if (tag==='br' || tag==='hr') return compact ? ' ' : '<'+tag+'>';
                        const name=({b:'strong', i:'em', del:'s', strike:'s', div:'p', h1:'h2', h5:'h4', h6:'h4'})[tag] || tag;
                        const body=Array.from(node.childNodes).map(clean).join('');
                        if (!allowed.has(name)) return body;
                        if (compact && ['h2','h3','h4'].includes(name)) return '<strong>'+body+'</strong> ';
                        if (compact && name==='li') return '• '+body+' ';
                        if (compact && name==='blockquote') return '<em>'+body+'</em> ';
                        if (compact && ['p','ul','ol','pre'].includes(name)) return body+' ';
                        if (name==='a') {
                            const href=(node.getAttribute('href') || '').trim();
                            if (!/^(https?:\/\/|mailto:|\/[^\/])/i.test(href)) return body;
                            return '<a href="'+escape(href)+'" target="_blank" rel="noopener noreferrer">'+body+'</a>';
                        }
                        return '<'+name+'>'+body+'</'+name+'>';
                    };
                    return Array.from(doc.body.childNodes).map(clean).join('').trim();
                },

                isVestige(item) {
                    const enabled = item?.properties?.vestige?.enabled;
                    return this.isArtifact(item)
                        && (enabled === true || enabled === 1 || enabled === '1');
                },

                vestigeStageLabel(key) {
                    return ({
                        dormant: 'Dormente',
                        awakened: 'Desperta',
                        exalted: 'Exaltada',
                    })[key] ?? 'Dormente';
                },

                // Resumo compacto exclusivo dos cards. O estágio dormant é
                // apresentado como "Inerte" aqui, sem mudar os dados nem o modal.
                vestigeCardStages(item) {
                    if (!this.isVestige(item)) return [];
                    const keys = ['dormant', 'awakened', 'exalted'];
                    const labels = ['Inerte', 'Desperta', 'Exaltada'];
                    const current = this.vestigeEffectiveStage(item);
                    const currentIndex = Math.max(0, keys.indexOf(current));
                    return keys.map((key, index) => ({
                        key,
                        label: labels[index],
                        current: index === currentIndex,
                        reached: index <= currentIndex,
                    }));
                },

                vestigeEffectiveStage(item) {
                    if (!this.isVestige(item)) return null;

                    const proficiency = Number(this.urls?.characterProficiency);
                    if (Number.isFinite(proficiency) && proficiency >= 6) return 'exalted';
                    if (Number.isFinite(proficiency) && proficiency >= 4) return 'awakened';
                    return 'dormant';
                },

                vestigeUnlockedStages(item) {
                    if (!this.isVestige(item)) return [];

                    const keys = ['dormant', 'awakened', 'exalted'];
                    const current = this.vestigeEffectiveStage(item);
                    const limit = keys.indexOf(current);
                    const ranges = {
                        dormant: '+2 a +3',
                        awakened: '+4 a +5',
                        exalted: '+6',
                    };
                    const stages = item.properties?.vestige?.stages ?? {};

                    return keys.slice(0, limit + 1).map(key => {
                        const raw = stages[key] ?? {};
                        return {
                            key,
                            label: this.vestigeStageLabel(key),
                            range: ranges[key],
                            active: key === current,
                            lore: String(raw.lore ?? ''),
                            features: (Array.isArray(raw.features) ? raw.features : [])
                                .filter(feature => feature && (
                                    String(feature.title ?? '').trim()
                                    || String(feature.description ?? '').trim()
                                )),
                        };
                    });
                },

                vestigeFeatureUsageLabel(feature) {
                    const usage = feature?.usage;
                    if (!(usage?.enabled === true || usage?.enabled === 1 || usage?.enabled === '1')) {
                        return 'Passiva';
                    }
                    const max = Math.max(1, parseInt(usage.max, 10) || 1);
                    const recovery = this.recoveryLabels?.[usage.recovery]
                        ?? usage.recovery_custom
                        ?? 'Uso';
                    return `Até ${max} uso${max !== 1 ? 's' : ''} · ${recovery}`;
                },

                // A ficha define a proficiência; o Cofre apenas define os poderes.
                // O patamar do personagem pode mudar ao recarregar a ficha ou por
                // um evento de progressão. Não usamos current_stage global do Vault.
                applyVestigeProficiency(detail) {
                    const value = detail?.character?.proficiency_bonus
                        ?? detail?.proficiency_bonus
                        ?? detail?.proficiencyBonus
                        ?? detail?.progression?.proficiency_bonus;
                    if (value === null || value === undefined || value === '') return;
                    const parsed = Number(value);
                    if (!Number.isInteger(parsed) || parsed < 2) return;
                    if (parsed === Number(this.urls.characterProficiency)) return;
                    this.urls.characterProficiency = parsed;
                    this.syncVestigeMilestones();
                },

                syncVestigeMilestones() {
                    if (this.vestigeSeenStages === null) {
                        let stored = {};
                        try {
                            const raw = localStorage.getItem(this.vestigeMilestoneStorageKey);
                            const parsed = raw ? JSON.parse(raw) : {};
                            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                                stored = parsed;
                            }
                        } catch (_) {
                            // Navegação privada pode bloquear localStorage.
                        }
                        this.vestigeSeenStages = stored;
                    }

                    const ranks = { dormant: 0, awakened: 1, exalted: 2 };
                    const seen = { ...this.vestigeSeenStages };

                    for (const item of this.items) {
                        if (!this.isVestige(item) || item.id === null || item.id === undefined) continue;
                        const id = String(item.id);
                        const stage = this.vestigeEffectiveStage(item);
                        const earlier = seen[id];
                        const sameItem = earlier && earlier.name === String(item.name ?? '');
                        if (
                            sameItem
                            && Object.prototype.hasOwnProperty.call(ranks, earlier.stage)
                            && ranks[stage] > ranks[earlier.stage]
                        ) {
                            this.announceVestigeAscension(item, stage);
                        }
                        seen[id] = { name: String(item.name ?? ''), stage };
                    }

                    this.vestigeSeenStages = seen;
                    try {
                        localStorage.setItem(this.vestigeMilestoneStorageKey, JSON.stringify(seen));
                    } catch (_) {
                        // A notificação funciona na sessão atual mesmo sem storage.
                    }
                },

                announceVestigeAscension(item, stage) {
                    const id = String(item.id);
                    const label = stage === 'exalted' ? 'Exaltou' : 'Despertou';
                    this.vestigeNoticeQueue.push({
                        id,
                        name: String(item.name ?? 'O Vestígio'),
                        stage: this.vestigeStageLabel(stage),
                        title: `${String(item.name ?? 'O Vestígio')} ${label}!`,
                    });

                    this.vestigeGlowingItemIds = { ...this.vestigeGlowingItemIds, [id]: true };
                    if (this.vestigeGlowTimers[id]) clearTimeout(this.vestigeGlowTimers[id]);
                    this.vestigeGlowTimers[id] = setTimeout(() => {
                        const next = { ...this.vestigeGlowingItemIds };
                        delete next[id];
                        this.vestigeGlowingItemIds = next;
                        delete this.vestigeGlowTimers[id];
                    }, 16000);

                    this.showNextVestigeNotice();
                },

                showNextVestigeNotice() {
                    if (this.vestigeNotice || this.vestigeNoticeQueue.length === 0) return;
                    this.vestigeNotice = this.vestigeNoticeQueue.shift();
                    this.vestigeNoticeTimer = setTimeout(() => {
                        this.vestigeNotice = null;
                        this.vestigeNoticeTimer = null;
                        this.$nextTick(() => this.showNextVestigeNotice());
                    }, 6500);
                },

                dismissVestigeNotice() {
                    if (this.vestigeNoticeTimer !== null) clearTimeout(this.vestigeNoticeTimer);
                    this.vestigeNoticeTimer = null;
                    this.vestigeNotice = null;
                    this.$nextTick(() => this.showNextVestigeNotice());
                },

                rarityRank(item) {
                    const ranks = {
                        common: 1,
                        uncommon: 2,
                        rare: 3,
                        very_rare: 4,
                        legendary: 5,
                        artifact: 6,
                    };

                    return (
                        ranks[
                            item?.rarity
                        ]
                        ?? 0
                    );
                },

                inventoryNatureLabel(item) {
                    return {
                        wonderful:
                            'Item Maravilhoso',

                        technological:
                            'Item Tecnológico',

                        mundane:
                            'Item Mundano',
                    }[
                        this.itemNature(
                            item
                        )
                    ]
                    ?? '';
                },

                rawInventoryItems(
                    nature
                ) {
                    return this.items.filter(
                        item =>
                            this.itemNature(
                                item
                            ) === nature
                    );
                },

                groupSearchValue(
                    nature
                ) {
                    return this.normalizeInventorySearch(
                        this.inventoryGroupSearch[
                            nature
                        ]
                        ?? ''
                    );
                },

                groupSearchActive(
                    nature
                ) {
                    return (
                        this.groupSearchValue(
                            nature
                        ) !== ''
                    );
                },

                matchesInventoryGroupSearch(
                    item,
                    nature
                ) {
                    const query =
                        this.groupSearchValue(
                            nature
                        );

                    if (!query) {
                        return true;
                    }

                    const weapon =
                        item?.properties
                            ?.weapon;

                    const haystack =
                        [
                            item?.name,
                            item?.rarity,
                            item?.rarity_label,
                            this.rarityLabel(
                                item
                            ),
                            this.inventoryNatureLabel(
                                item
                            ),
                            weapon?.weapon_type,
                            this.classificationLine(
                                item
                            ),
                            this.weaponDetailLine(
                                item
                            ),
                        ]
                            .filter(Boolean)
                            .map(
                                value =>
                                    this.normalizeInventorySearch(
                                        value
                                    )
                            )
                            .join(' ');

                    return haystack.includes(
                        query
                    );
                },

                visibleInventoryItems(
                    nature
                ) {
                    return this.rawInventoryItems(
                        nature
                    ).filter(
                        item =>
                            this.matchesInventoryGroupSearch(
                                item,
                                nature
                            )
                    );
                },

                get wonderfulItems() {
                    return this.visibleInventoryItems(
                        'wonderful'
                    );
                },

                get technologicalItems() {
                    return this.visibleInventoryItems(
                        'technological'
                    );
                },

                get mundaneItems() {
                    return this.visibleInventoryItems(
                        'mundane'
                    );
                },

                groupVisibleCount(
                    nature
                ) {
                    return this.visibleInventoryItems(
                        nature
                    ).length;
                },

                groupTotalCount(
                    nature
                ) {
                    return this.rawInventoryItems(
                        nature
                    ).length;
                },

                get inventoryGroups() {
                    return [
                        {
                            key: 'wonderful',
                            title: 'Itens Maravilhosos',
                            subtitle: 'Itens mágicos, artefatos e criações especiais.',
                            items: this.wonderfulItems,
                            total: this.groupTotalCount(
                                'wonderful'
                            ),
                        },
                        {
                            key: 'technological',
                            title: 'Itens Tecnológicos',
                            subtitle: 'Tecnologia, dispositivos e equipamentos especiais.',
                            items: this.technologicalItems,
                            total: this.groupTotalCount(
                                'technological'
                            ),
                        },
                        {
                            key: 'mundane',
                            title: 'Itens Mundanos',
                            subtitle: 'Equipamentos e objetos comuns.',
                            items: this.mundaneItems,
                            total: this.groupTotalCount(
                                'mundane'
                            ),
                        },
                    ].filter(
                        group =>
                            group.total > 0
                    );
                },

                /*
                |--------------------------------------------------------------------------
                | Gavetas das categorias
                |--------------------------------------------------------------------------
                */

                isInventoryGroupOpen(key) {
                    return (
                        this.inventoryGroupOpen[
                            key
                        ]
                        ?? true
                    );
                },

                isInventoryGroupExpanded(key) {
                    return this.isInventoryGroupOpen(
                        key
                    );
                },

                toggleInventoryGroup(key) {
                    this.inventoryGroupOpen[
                        key
                    ] =
                        !this.isInventoryGroupOpen(
                            key
                        );

                    this.persistInventoryGroupDrawers();
                },

                restoreInventoryGroupDrawers() {
                    try {
                        const saved =
                            JSON.parse(
                                localStorage.getItem(
                                    this.inventoryGroupStorageKey
                                )
                                ?? '{}'
                            );

                        [
                            'wonderful',
                            'technological',
                            'mundane',
                        ].forEach(
                            key => {
                                if (
                                    typeof saved?.[
                                        key
                                    ] === 'boolean'
                                ) {
                                    this.inventoryGroupOpen[
                                        key
                                    ] =
                                        saved[key];
                                }
                            }
                        );
                    } catch (error) {
                        /*
                        | Se localStorage estiver indisponível,
                        | usamos o estado padrão.
                        */
                    }
                },

                persistInventoryGroupDrawers() {
                    try {
                        localStorage.setItem(
                            this.inventoryGroupStorageKey,
                            JSON.stringify(
                                this.inventoryGroupOpen
                            )
                        );
                    } catch (error) {
                        /*
                        | Persistência é apenas conveniência de UX.
                        */
                    }
                },

                get attunedItems() {
                    return this.items.filter(
                        item =>
                            this.itemNature(item) === 'wonderful'
                            && item.requires_attunement
                            && item.attuned
                    );
                },

                get eligibleAttunementItems() {
                    return this.items.filter(
                        item =>
                            this.itemNature(item) === 'wonderful'
                            && item.requires_attunement
                            && !item.attuned
                    );
                },

                restoreAttunementSlots() {
                    try {
                        const raw = window.localStorage.getItem(this.attunementSlotStorageKey);
                        const value = Number(raw);
                        if (raw !== null && Number.isInteger(value)) {
                            this.attunementSlotLimit = Math.min(12, Math.max(0, value));
                        }
                    } catch (_) { /* Disponível apenas enquanto a página estiver aberta. */ }
                    // Reduzir o limite nunca altera os vínculos já persistidos no Laravel.
                    // Itens excedentes continuam visíveis numa lista separada.
                },

                setAttunementSlotLimit(value) {
                    const requested = Math.round(Number(value));
                    if (!Number.isFinite(requested)) return;
                    this.attunementSlotLimit = Math.max(0, Math.min(12, requested));
                    try {
                        window.localStorage.setItem(this.attunementSlotStorageKey, String(this.attunementSlotLimit));
                    } catch (_) { /* Preferência temporária se armazenamento não disponível. */ }
                },

                get primaryAttunementSlots() {
                    return Array.from({length: this.attunementSlotLimit}, (_, index) => this.attunedItems[index] ?? null);
                },

                get extraAttunedItems() {
                    return this.attunedItems.slice(this.attunementSlotLimit);
                },

                blankProperties() {
                    return {
                        nature: 'wonderful',
                        inventory: { equippable: false },
                        custom_properties: [],
                        features: [],
                        optional_rules: {},
                    };
                },

                blankWeapon() {
                    return {
                        category: 'simple',
                        category_custom: '',
                        weapon_type: '',
                        magic_bonus: 0,
                        traits: [],
                        damage: {
                            count: 1,
                            die: 'd4',
                            ability: 'dexterity',
                            ability_custom: '',
                            type: 'Perfurante',
                        },
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

                        /*
                        | A fórmula defensiva pertence ao item.
                        |
                        | Leve: Destreza completa.
                        | Média: Destreza limitada.
                        | Pesada: sem Destreza.
                        */
                        dexterity_mode: 'full',
                        dexterity_cap: 2,

                        /*
                        | Armaduras mágicas/custom podem adicionar outros
                        | modificadores de atributo à própria fórmula.
                        */
                        ability_modifiers: [],
                    };
                },

                armorDexterityDefaults(
                    category
                ) {
                    if (
                        category
                        === 'light'
                    ) {
                        return {
                            mode: 'full',
                            cap: 2,
                        };
                    }

                    if (
                        category
                        === 'medium'
                    ) {
                        return {
                            mode: 'capped',
                            cap: 2,
                        };
                    }

                    return {
                        mode: 'none',
                        cap: 2,
                    };
                },

                normalizeArmorData(
                    raw
                ) {
                    const source =
                        raw
                        && typeof raw === 'object'
                        && !Array.isArray(raw)
                            ? raw
                            : {};

                    const armor = {
                        ...this.blankArmor(),
                        ...source,
                    };

                    const category =
                        [
                            'light',
                            'medium',
                            'heavy',
                            'custom',
                        ].includes(
                            armor.category
                        )
                            ? armor.category
                            : 'custom';

                    const defaults =
                        this.armorDexterityDefaults(
                            category
                        );

                    let dexterityMode =
                        armor.dexterity_mode;

                    if (
                        ![
                            'none',
                            'full',
                            'capped',
                        ].includes(
                            dexterityMode
                        )
                    ) {
                        dexterityMode =
                            defaults.mode;
                    }

                    let dexterityCap =
                        Math.max(
                            0,
                            parseInt(
                                armor.dexterity_cap
                            ) || defaults.cap
                        );

                    if (
                        dexterityMode
                        !== 'capped'
                    ) {
                        dexterityCap =
                            defaults.cap;
                    }

                    const abilityModifiers =
                        Array.isArray(
                            armor.ability_modifiers
                        )
                            ? [
                                ...new Set(
                                    armor.ability_modifiers
                                        .map(
                                            value =>
                                                String(
                                                    value
                                                    ?? ''
                                                )
                                        )
                                        .filter(
                                            ability =>
                                                [
                                                    'strength',
                                                    'constitution',
                                                    'intelligence',
                                                    'wisdom',
                                                    'charisma',
                                                ].includes(
                                                    ability
                                                )
                                        )
                                ),
                            ]
                            : [];

                    return {
                        ...armor,
                        category,
                        category_custom:
                            category === 'custom'
                                ? (
                                    armor.category_custom
                                    ?? ''
                                )
                                : '',
                        armor_type:
                            armor.armor_type
                            ?? '',
                        base_ac:
                            Math.max(
                                0,
                                parseInt(
                                    armor.base_ac
                                ) || 0
                            ),
                        magic_bonus:
                            parseInt(
                                armor.magic_bonus
                            ) || 0,
                        dexterity_mode:
                            dexterityMode,
                        dexterity_cap:
                            dexterityCap,
                        ability_modifiers:
                            abilityModifiers,
                    };
                },

                setArmorCategory(
                    category
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                    ) {
                        return;
                    }

                    const armor =
                        this.form.properties.armor;

                    armor.category =
                        category;

                    const defaults =
                        this.armorDexterityDefaults(
                            category
                        );

                    armor.dexterity_mode =
                        defaults.mode;

                    armor.dexterity_cap =
                        defaults.cap;
                },

                setArmorDexterityMode(
                    mode
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                        || ![
                            'none',
                            'full',
                            'capped',
                        ].includes(
                            mode
                        )
                    ) {
                        return;
                    }

                    this.form.properties.armor.dexterity_mode =
                        mode;

                    if (
                        mode === 'capped'
                        && (
                            parseInt(
                                this.form.properties.armor.dexterity_cap
                            ) || 0
                        ) <= 0
                    ) {
                        this.form.properties.armor.dexterity_cap =
                            2;
                    }
                },

                toggleArmorAbilityModifier(
                    ability
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                        || ![
                            'strength',
                            'constitution',
                            'intelligence',
                            'wisdom',
                            'charisma',
                        ].includes(
                            ability
                        )
                    ) {
                        return;
                    }

                    const current =
                        Array.isArray(
                            this.form.properties.armor.ability_modifiers
                        )
                            ? [
                                ...this.form.properties.armor.ability_modifiers
                            ]
                            : [];

                    if (
                        current.includes(
                            ability
                        )
                    ) {
                        this.form.properties.armor.ability_modifiers =
                            current.filter(
                                entry =>
                                    entry !== ability
                            );

                        return;
                    }

                    this.form.properties.armor.ability_modifiers = [
                        ...current,
                        ability,
                    ];
                },

                armorDexterityRuleLabel(
                    armor
                ) {
                    if (!armor) {
                        return '';
                    }

                    if (
                        armor.dexterity_mode
                        === 'full'
                    ) {
                        return 'DES completa';
                    }

                    if (
                        armor.dexterity_mode
                        === 'capped'
                    ) {
                        return (
                            'DES máx. +'
                            + Math.max(
                                0,
                                parseInt(
                                    armor.dexterity_cap
                                ) || 0
                            )
                        );
                    }

                    return 'Sem DES';
                },

                armorAbilityShortLabel(
                    ability
                ) {
                    return {
                        strength: 'FOR',
                        constitution: 'CON',
                        intelligence: 'INT',
                        wisdom: 'SAB',
                        charisma: 'CAR',
                    }[
                        ability
                    ]
                    ?? String(
                        ability
                        ?? ''
                    ).toUpperCase();
                },

                armorFormulaText(
                    armor
                ) {
                    if (!armor) {
                        return '';
                    }

                    const base =
                        Math.max(
                            0,
                            parseInt(
                                armor.base_ac
                            ) || 0
                        );

                    const parts = [
                        `CA ${base}`,
                    ];

                    if (
                        armor.dexterity_mode
                        === 'full'
                    ) {
                        parts.push(
                            '+ DES'
                        );
                    }

                    if (
                        armor.dexterity_mode
                        === 'capped'
                    ) {
                        parts.push(
                            '+ DES'
                            + ' (máx. +'
                            + Math.max(
                                0,
                                parseInt(
                                    armor.dexterity_cap
                                ) || 0
                            )
                            + ')'
                        );
                    }

                    (
                        armor.ability_modifiers
                        ?? []
                    ).forEach(
                        ability => {
                            parts.push(
                                '+ '
                                + this.armorAbilityShortLabel(
                                    ability
                                )
                            );
                        }
                    );

                    const magic =
                        parseInt(
                            armor.magic_bonus
                        ) || 0;

                    if (magic !== 0) {
                        parts.push(
                            (
                                magic > 0
                                    ? '+'
                                    : ''
                            )
                            + magic
                            + ' mágico'
                        );
                    }

                    return parts.join(
                        ' '
                    );
                },

                setArmorDexterityCap(
                    cap
                ) {
                    if (
                        !this.form
                        || !this.form.properties?.armor
                    ) {
                        return;
                    }

                    const armor =
                        this.form.properties.armor;

                    armor.dexterity_mode =
                        'capped';

                    armor.dexterity_cap =
                        Math.max(
                            0,
                            parseInt(
                                cap
                            ) || 0
                        );
                },

                shieldTotalBonus(
                    shield
                ) {
                    if (!shield) {
                        return 0;
                    }

                    return (
                        (
                            parseInt(
                                shield.ac_bonus
                            ) || 0
                        )
                        + (
                            parseInt(
                                shield.magic_bonus
                            ) || 0
                        )
                    );
                },

                blankShield() {
                    return {
                        label: 'Escudo',
                        ac_bonus: 2,
                        magic_bonus: 0,
                    };
                },

                blankFeature() {
                    return {
                        title: '',
                        description: '',

                        // Estado apenas da interface do editor. Não é salvo no item.
                        editor_open: true,

                        usage: {
                            enabled: false,

                            /*
                            | spend: começa cheio e vai diminuindo.
                            | build: começa vazio e vai acumulando.
                            */
                            counter_mode: 'spend',

                            current: 1,
                            max: 1,
                            recovery: 'day',
                            recovery_custom: '',
                        },
                    };
                },

                blankForm() {
                    return {
                        name: '',
                        description: '',
                        image_url: null,
                        remove_image: false,
                        equipped: false,
                        rarity: 'common',
                        requires_attunement: false,
                        attuned: false,
                        properties: this.blankProperties(),
                        ability_bonuses: {},
                        modifiers: {},
                        is_cursed: false,
                        curse_description: null,
                    };
                },

                normalizeItem(raw) {
                    const properties = this.normalizeProperties(raw?.properties, raw);

                    return {
                        id: raw.id,
                        name: raw.name ?? '',
                        type: properties.nature,
                        description: raw.description ?? '',
                        image_url: raw.image_url ?? null,
                        equipped: !!raw.equipped,
                        is_magical: properties.nature === 'wonderful',
                        rarity: raw.rarity ?? 'common',
                        rarity_label: raw.rarity_label ?? this.rarityLabels[raw.rarity] ?? null,
                        requires_attunement: !!raw.requires_attunement,
                        attuned: !!raw.attuned,
                        armor_class: raw.armor_class ?? null,
                        damage: raw.damage ?? null,
                        attack_bonus: raw.attack_bonus ?? null,
                        damage_bonus: raw.damage_bonus ?? null,
                        ability_bonuses: raw.ability_bonuses ?? {},
                        properties,
                        modifiers: raw.modifiers ?? {},
                        notes: raw.notes ?? '',
                        is_cursed: !!raw.is_cursed,
                        curse_revealed: !!raw.curse_revealed,
                        curse_description: raw.curse_description ?? null,
                    };
                },

                normalizeProperties(raw, rawItem = null) {
                    const properties = raw && typeof raw === 'object' && !Array.isArray(raw)
                        ? JSON.parse(JSON.stringify(raw))
                        : {};

                    let nature = properties.nature;

                    if (!['wonderful', 'mundane', 'technological'].includes(nature)) {
                        if (rawItem?.type === 'technological') {
                            nature = 'technological';
                        } else if (rawItem?.is_magical) {
                            nature = 'wonderful';
                        } else {
                            nature = 'mundane';
                        }
                    }

                    properties.nature = nature;
                    properties.inventory = {
                        equippable: !!properties.inventory?.equippable,
                    };

                    if (properties.weapon && typeof properties.weapon === 'object') {
                        properties.weapon = this.normalizeWeapon(properties.weapon);
                    } else {
                        delete properties.weapon;
                    }

                    if (properties.armor && typeof properties.armor === 'object') {
                        properties.armor =
                            this.normalizeArmorData(
                                properties.armor
                            );
                    } else {
                        delete properties.armor;
                    }

                    if (properties.shield && typeof properties.shield === 'object') {
                        properties.shield = {
                            ...this.blankShield(),
                            ...properties.shield,
                        };
                    } else {
                        delete properties.shield;
                    }

                    /*
                    | Armadura e Escudo são equipamento por definição.
                    | Isso garante que itens antigos também ganhem o botão Equipar.
                    */
                    if (
                        properties.armor
                        || properties.shield
                    ) {
                        properties.inventory.equippable =
                            true;
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
                            usage: this.normalizeFeatureUsage(feature?.usage),

                            // Controle visual do accordion. Não vai para o payload final.
                            editor_open: !!feature?.editor_open,
                        }))
                        : [];

                    properties.optional_rules = properties.optional_rules && typeof properties.optional_rules === 'object'
                        ? properties.optional_rules
                        : {};

                    return properties;
                },

                normalizeWeapon(raw) {
                    const weapon = {
                        ...this.blankWeapon(),
                        ...raw,
                    };

                    if (Array.isArray(raw.damage_parts) && !raw.damage && raw.damage_parts[0]) {
                        weapon.damage = {
                            count: parseInt(raw.damage_parts[0].count) || 1,
                            die: raw.damage_parts[0].die ?? 'd4',
                            ability: 'none',
                            ability_custom: '',
                            type: raw.damage_parts[0].type ?? '',
                        };
                    } else {
                        weapon.damage = {
                            ...this.blankWeapon().damage,
                            ...(raw.damage ?? {}),
                        };
                    }

                    if (['Arma Simples', 'Simples'].includes(raw.category)) {
                        weapon.category = 'simple';
                    } else if (['Arma Marcial', 'Marcial'].includes(raw.category)) {
                        weapon.category = 'martial';
                    } else if (!['simple', 'martial', 'custom'].includes(weapon.category)) {
                        weapon.category = 'custom';
                        weapon.category_custom = raw.category ?? '';
                    }

                    weapon.traits = Array.isArray(raw.traits)
                        ? raw.traits.map(entry => {
                            const key = entry?.key ?? '';
                            const custom = !!entry?.custom;
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

                    if (Array.isArray(raw.masteries)) {
                        weapon.masteries = raw.masteries.map(entry => {
                            const key = entry?.key ?? '';
                            const custom = !!entry?.custom;
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
                        });
                    } else if (raw.mastery) {
                        const legacyKey = String(raw.mastery).toLowerCase();
                        const definition = this.masteryDefinitions.find(
                            current => current.key === legacyKey
                        );

                        weapon.masteries = [{
                            key: legacyKey,
                            name: definition?.name ?? raw.mastery,
                            description: definition?.description ?? raw.mastery_description ?? '',
                            custom: false,
                        }];
                    } else {
                        weapon.masteries = [];
                    }

                    weapon.extra_damage = Array.isArray(raw.extra_damage)
                        ? raw.extra_damage.map(part => ({
                            count: Math.max(1, parseInt(part?.count) || 1),
                            die: part?.die ?? 'd6',
                            type: part?.type ?? '',
                        }))
                        : [];

                    return weapon;
                },

                normalizeFeatureUsage(raw) {
                    /*
                    |--------------------------------------------------------------------------
                    | Booleano do tracker
                    |--------------------------------------------------------------------------
                    |
                    | Dados vindos de FormData / snapshots podem chegar como strings
                    | ("0", "1", "false", "true"). Em JavaScript, a string
                    | "0" é truthy; portanto não podemos testar apenas !raw.enabled.
                    |
                    | Uma habilidade só deve possuir tracker quando o valor indicar
                    | explicitamente que ele está habilitado.
                    |
                    */
                    const enabledRaw =
                        raw?.enabled;

                    let enabled = false;

                    if (typeof enabledRaw === 'boolean') {
                        enabled = enabledRaw;
                    } else if (typeof enabledRaw === 'number') {
                        enabled = enabledRaw !== 0;
                    } else if (typeof enabledRaw === 'string') {
                        enabled = [
                            '1',
                            'true',
                            'on',
                            'yes',
                        ].includes(
                            enabledRaw
                                .trim()
                                .toLowerCase()
                        );
                    }

                    if (
                        !raw
                        || !enabled
                    ) {
                        return {
                            enabled: false,
                            counter_mode: 'spend',
                            current: 1,
                            max: 1,
                            recovery: 'day',
                            recovery_custom: '',
                        };
                    }

                    const max =
                        Math.max(
                            1,
                            parseInt(
                                raw.max
                            ) || 1
                        );

                    const counterMode =
                        [
                            'spend',
                            'build',
                        ].includes(
                            raw.counter_mode
                        )
                            ? raw.counter_mode
                            : 'spend';

                    const parsedCurrent =
                        parseInt(
                            raw.current
                        );

                    const defaultCurrent =
                        counterMode === 'build'
                            ? 0
                            : max;

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                Number.isFinite(
                                    parsedCurrent
                                )
                                    ? parsedCurrent
                                    : defaultCurrent
                            )
                        );

                    const recovery =
                        Object.prototype
                            .hasOwnProperty
                            .call(
                                this.recoveryLabels,
                                raw.recovery
                            )
                            ? raw.recovery
                            : 'day';

                    return {
                        enabled: true,
                        counter_mode:
                            counterMode,
                        current,
                        max,
                        recovery,
                        recovery_custom:
                            raw.recovery_custom
                            ?? '',
                    };
                },

                itemNature(item) {
                    return item?.properties?.nature ?? (item?.is_magical ? 'wonderful' : 'mundane');
                },

                natureLabel(item) {
                    return this.natureLabels[this.itemNature(item)] ?? 'Item';
                },

                rarityLabel(item) {
                    return item?.rarity_label || this.rarityLabels[item?.rarity] || null;
                },

                classificationLine(item) {
                    const pieces = [this.natureLabel(item)];
                    const rarity = this.rarityLabel(item);

                    if (rarity) pieces.push(rarity);

                    let text = pieces.join(', ');

                    if (item?.requires_attunement) {
                        text += ' (Sintonizável)';
                    }

                    return text;
                },

                cardClassificationLine(item) {
                    const rarity =
                        this.rarityLabel(
                            item
                        );

                    let text =
                        rarity
                        || this.natureLabel(
                            item
                        );

                    if (
                        item?.requires_attunement
                    ) {
                        text += ' · Sintonizável';
                    }

                    return text;
                },

                // Linha opcional: detalhes mecânicos curtos no card,
                // sem levar descrições extensas para a listagem.
                cardTechnicalLine(item) {
                    const weapon = item?.properties?.weapon;
                    if (!weapon) return '';

                    const parts = [
                        this.weaponCategoryLabel(weapon),
                        this.weaponDamageLabel(weapon),
                    ].filter(Boolean);
                    return parts.join(' · ');
                },

                // Resumo textual seguro. A descrição integral permanece no modal.
                cardItemDescription(item) {
                    return String(item?.description ?? '')
                        .replace(/<[^>]*>/g, ' ')
                        .replace(/&nbsp;|&#160;/gi, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                },

                // Um único tooltip flutuante evita recortes no card de altura fixa.
                showInventorySkillTooltip(feature, event) {
                    const description = String(feature?.description ?? '').trim();

                    if (!description || !event?.currentTarget) {
                        this.inventorySkillTooltip = null;
                        return;
                    }

                    const rect = event.currentTarget.getBoundingClientRect();
                    const width = Math.min(310, Math.max(220, window.innerWidth - 24));
                    const left = Math.min(
                        Math.max(12, rect.left),
                        Math.max(12, window.innerWidth - width - 12)
                    );
                    const above = rect.top > 210;

                    this.inventorySkillTooltip = {
                        name: String(feature?.title || 'Habilidade'),
                        description,
                        left,
                        top: above ? rect.top - 9 : rect.bottom + 9,
                        above,
                    };
                },

                weaponCategoryLabel(weapon) {
                    if (!weapon) return '';
                    if (weapon.category === 'simple') return 'Arma Simples';
                    if (weapon.category === 'martial') return 'Arma Marcial';

                    return weapon.category_custom || 'Arma';
                },

                armorCategoryLabel(armor) {
                    if (!armor) return '';

                    return {
                        light: 'Armadura Leve',
                        medium: 'Armadura Média',
                        heavy: 'Armadura Pesada',
                        custom: armor.category_custom || 'Armadura',
                    }[armor.category] ?? 'Armadura';
                },

                abilityLabel(damage) {
                    if (!damage || damage.ability === 'none') return '';
                    if (damage.ability === 'custom') return damage.ability_custom || 'Custom';

                    return this.abilityLabels[damage.ability] ?? damage.ability ?? '';
                },

                weaponDamageLabel(weapon) {
                    if (!weapon?.damage) return '';

                    const count = Math.max(1, parseInt(weapon.damage.count) || 1);
                    const die = weapon.damage.die || '';
                    const ability = this.abilityLabel(weapon.damage);
                    const damageType = weapon.damage.type || '';
                    let text = die ? `${count}${die}` : '';

                    if (ability) text += `${text ? ' + ' : ''}${ability}`;
                    if (damageType) text += `${text ? ' · ' : ''}${damageType}`;

                    return text;
                },

                weaponSummary(item) {
                    const weapon = item?.properties?.weapon;
                    if (!weapon) return '';

                    return [
                        this.weaponCategoryLabel(weapon),
                        weapon.weapon_type || null,
                        ...(weapon.traits ?? []).map(trait => trait.name).filter(Boolean),
                        this.weaponDamageLabel(weapon) || null,
                    ].filter(Boolean).join(' · ');
                },

                /*
                |--------------------------------------------------------------------------
                | Apresentação enxuta no modal
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | Apresentação editorial no modal
                |--------------------------------------------------------------------------
                |
                | Exemplo:
                |
                | Arma Marcial - Katana (1d10 Cortante/Energia)
                | Propriedades: Acuidade, Leve
                | Maestrias: (Sap)
                |
                */

                weaponDetailDamage(item) {
                    const weapon =
                        item?.properties?.weapon;

                    const damage =
                        weapon?.damage;

                    if (!damage) {
                        return '';
                    }

                    const count =
                        Math.max(
                            1,
                            parseInt(
                                damage.count
                            ) || 1
                        );

                    const die =
                        damage.die
                        || '';

                    const dice =
                        die
                            ? `${count}${die}`
                            : '';

                    const damageTypes = [];

                    if (damage.type) {
                        damageTypes.push(
                            damage.type
                        );
                    }

                    (
                        weapon?.extra_damage
                        ?? []
                    ).forEach(
                        extra => {
                            if (
                                extra?.type
                                && !damageTypes.includes(
                                    extra.type
                                )
                            ) {
                                damageTypes.push(
                                    extra.type
                                );
                            }
                        }
                    );

                    return [
                        dice,
                        damageTypes.length
                            ? damageTypes.join('/')
                            : null,
                    ]
                        .filter(Boolean)
                        .join(' ');
                },

                weaponDetailLine(item) {
                    const weapon =
                        item?.properties?.weapon;

                    if (!weapon) {
                        return '';
                    }

                    const identity =
                        [
                            this.weaponCategoryLabel(
                                weapon
                            ),

                            weapon.weapon_type
                                || null,
                        ]
                            .filter(Boolean)
                            .join(' - ');

                    const damage =
                        this.weaponDetailDamage(
                            item
                        );

                    if (
                        identity
                        && damage
                    ) {
                        return `${identity} (${damage})`;
                    }

                    return identity
                        || damage;
                },

                weaponPropertiesText(item) {
                    return (
                        item?.properties
                            ?.weapon
                            ?.traits
                        ?? []
                    )
                        .map(
                            trait =>
                                String(
                                    trait?.name
                                    ?? ''
                                ).trim()
                        )
                        .filter(Boolean)
                        .join(', ');
                },

                weaponMasteriesText(item) {
                    return (
                        item?.properties
                            ?.weapon
                            ?.masteries
                        ?? []
                    )
                        .map(
                            mastery => {
                                const name =
                                    String(
                                        mastery?.name
                                        ?? ''
                                    ).trim();

                                return name
                                    ? `(${name})`
                                    : null;
                            }
                        )
                        .filter(Boolean)
                        .join(' ');
                },

                nonWeaponMechanicalLines(item) {
                    const lines = [];

                    const armor =
                        this.armorSummary(item);

                    const shield =
                        this.shieldSummary(item);

                    if (armor) {
                        lines.push(armor);
                    }

                    if (shield) {
                        lines.push(shield);
                    }

                    (
                        item?.properties
                            ?.custom_properties
                        ?? []
                    ).forEach(
                        entry => {
                            const line =
                                [
                                    entry.title,
                                    entry.value,
                                ]
                                    .filter(Boolean)
                                    .join(' · ');

                            if (line) {
                                lines.push(line);
                            }
                        }
                    );

                    return lines;
                },

                armorSummary(item) {
                    const armor =
                        item?.properties?.armor;

                    if (!armor) {
                        return '';
                    }

                    return [
                        this.armorCategoryLabel(
                            armor
                        ),
                        armor.armor_type
                            || null,
                        this.armorFormulaText(
                            armor
                        ),
                    ]
                        .filter(Boolean)
                        .join(' · ');
                },

                shieldSummary(item) {
                    const shield =
                        item?.properties?.shield;

                    if (!shield) {
                        return '';
                    }

                    const base =
                        parseInt(
                            shield.ac_bonus
                        ) || 0;

                    const magic =
                        parseInt(
                            shield.magic_bonus
                        ) || 0;

                    return [
                        shield.label
                            || 'Escudo',
                        `Base ${base >= 0 ? '+' : ''}${base}`,
                        `Mágico ${magic >= 0 ? '+' : ''}${magic}`,
                        `Total +${base + magic}`,
                    ]
                        .filter(Boolean)
                        .join(' · ');
                },

                mechanicalLines(item) {
                    const lines = [];
                    const weapon = this.weaponSummary(item);
                    const armor = this.armorSummary(item);
                    const shield = this.shieldSummary(item);

                    if (weapon) lines.push(weapon);
                    if (armor) lines.push(armor);
                    if (shield) lines.push(shield);

                    (item?.properties?.custom_properties ?? []).forEach(entry => {
                        const line = [entry.title, entry.value].filter(Boolean).join(' · ');
                        if (line) lines.push(line);
                    });

                    return lines;
                },

                mechanicalPreview(item) {
                    return this.mechanicalLines(item).slice(0, 2).join('  •  ');
                },

                hasModule(module) {
                    return !!this.form?.properties?.[module];
                },

                showEditorTooltip(
                    event,
                    title,
                    content
                ) {
                    const tooltipText =
                        String(
                            content
                            ?? ''
                        ).trim();

                    if (!tooltipText) {
                        return;
                    }

                    const target =
                        event?.currentTarget;

                    if (!target) {
                        return;
                    }

                    const rect =
                        target.getBoundingClientRect();

                    const viewportWidth =
                        window.innerWidth;

                    const viewportHeight =
                        window.innerHeight;

                    const width =
                        Math.min(
                            390,
                            viewportWidth - 24
                        );

                    const estimatedHeight =
                        Math.min(
                            250,
                            viewportHeight - 24
                        );

                    let left =
                        rect.left
                        + (
                            rect.width / 2
                        )
                        - (
                            width / 2
                        );

                    left =
                        Math.max(
                            12,
                            Math.min(
                                left,
                                viewportWidth
                                - width
                                - 12
                            )
                        );

                    let top =
                        rect.bottom
                        + 8;

                    if (
                        top
                        + estimatedHeight
                        > viewportHeight
                        - 12
                    ) {
                        top =
                            Math.max(
                                12,
                                rect.top
                                - estimatedHeight
                                - 8
                            );
                    }

                    this.editorTooltip = {
                        open: true,
                        title:
                            String(
                                title
                                ?? ''
                            ).trim(),
                        text:
                            tooltipText,
                        left,
                        top,
                    };
                },

                hideEditorTooltip() {
                    this.editorTooltip.open =
                        false;
                },

                openCreate() {
                    if (!this.canCreateInventoryItems) {
                        this.showToast('O Mestre desativou a criação de itens nesta campanha.');
                        return;
                    }
                    this.editingId = null;
                    this.form = this.blankForm();
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                    this.editorOpen = true;
                },

                openEdit(item) {
                    const normalized = this.normalizeItem(item);

                    this.detailOpen = false;
                    this.detailItem = null;
                    this.editingId = normalized.id;
                    this.form = {
                        ...this.blankForm(),
                        ...JSON.parse(JSON.stringify(normalized)),
                        properties: this.normalizeProperties(normalized.properties, normalized),
                        remove_image: false,
                    };
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = normalized.image_url ?? null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                    this.editorOpen = true;
                },

                closeEditor() {
                    if (this.saving) return;

                    this.hideEditorTooltip();

                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.editorOpen = false;
                    this.editingId = null;
                    this.form = null;
                    this.modulePickerOpen = false;
                    this.imageFile = null;
                    this.imagePreview = null;
                    this.saveError = null;
                    this.deleteConfirmOpen = false;
                },

                openDetail(item) {
                    this.detailItem = this.normalizeItem(item);
                    this.detailOpen = true;
                },

                closeDetail() {
                    this.detailOpen = false;
                    this.detailItem = null;
                },

                setNature(nature) {
                    if (!this.form) return;

                    this.form.properties.nature = ['wonderful', 'mundane', 'technological'].includes(nature)
                        ? nature
                        : 'wonderful';

                    if (this.form.properties.nature !== 'wonderful') {
                        this.form.requires_attunement = false;
                        this.form.attuned = false;
                    }
                },

                setEquippable(value) {
                    if (!this.form) return;

                    this.form.properties.inventory.equippable = !!value;

                    if (!value) {
                        this.form.equipped = false;
                    }
                },

                addModule(module) {
                    if (!this.form) return;

                    if (module === 'weapon' && !this.form.properties.weapon) {
                        this.form.properties.weapon = this.blankWeapon();
                    }

                    if (module === 'armor' && !this.form.properties.armor) {
                        this.form.properties.armor =
                            this.blankArmor();

                        this.form.properties.inventory.equippable =
                            true;
                    }

                    if (module === 'shield' && !this.form.properties.shield) {
                        this.form.properties.shield =
                            this.blankShield();

                        this.form.properties.inventory.equippable =
                            true;
                    }

                    if (module === 'custom') {
                        this.addCustomProperty();
                    }

                    this.modulePickerOpen = false;
                },

                removeModule(module) {
                    if (!this.form) return;
                    delete this.form.properties[module];
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

                traitSelected(key) {
                    return !!this.form?.properties?.weapon?.traits?.some(
                        trait => trait.key === key && !trait.custom
                    );
                },

                toggleTrait(definition) {
                    const traits = this.form.properties.weapon.traits;
                    const index = traits.findIndex(
                        trait => trait.key === definition.key && !trait.custom
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
                    return !!this.form?.properties?.weapon?.masteries?.some(
                        mastery => mastery.key === key && !mastery.custom
                    );
                },

                toggleMastery(definition) {
                    const masteries = this.form.properties.weapon.masteries;
                    const index = masteries.findIndex(
                        mastery => mastery.key === definition.key && !mastery.custom
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

                addExtraDamage() {
                    this.form.properties.weapon.extra_damage.push({
                        count: 1,
                        die: 'd6',
                        type: '',
                    });
                },

                removeExtraDamage(index) {
                    this.form.properties.weapon.extra_damage.splice(index, 1);
                },

                addFeature() {
                    // Mantém somente uma habilidade aberta por vez para o editor
                    // continuar confortável mesmo em itens com muitas habilidades.
                    this.form.properties.features.forEach(
                        feature => feature.editor_open = false
                    );

                    const feature = this.blankFeature();
                    feature.editor_open = true;

                    this.form.properties.features.push(feature);
                },

                toggleFeatureEditor(feature) {
                    const shouldOpen = !feature.editor_open;

                    this.form.properties.features.forEach(
                        entry => entry.editor_open = false
                    );

                    feature.editor_open = shouldOpen;
                },

                removeFeature(index) {
                    this.form.properties.features.splice(index, 1);
                },

                setFeatureUsage(
                    feature,
                    enabled
                ) {
                    feature.usage.enabled =
                        !!enabled;

                    if (!enabled) {
                        feature.usage.counter_mode =
                            'spend';

                        feature.usage.current =
                            1;

                        feature.usage.max =
                            1;

                        feature.usage.recovery =
                            'day';

                        feature.usage.recovery_custom =
                            '';

                        return;
                    }

                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    if (
                        ![
                            'spend',
                            'build',
                        ].includes(
                            feature.usage.counter_mode
                        )
                    ) {
                        feature.usage.counter_mode =
                            'spend';
                    }

                    /*
                    | Ao habilitar um tracker novo:
                    | - consumo começa cheio;
                    | - acúmulo começa vazio.
                    */
                    feature.usage.current =
                        feature.usage.counter_mode
                        === 'build'
                            ? 0
                            : feature.usage.max;
                },

                setFeatureCounterMode(
                    feature,
                    mode
                ) {
                    if (
                        ![
                            'spend',
                            'build',
                        ].includes(
                            mode
                        )
                    ) {
                        return;
                    }

                    feature.usage.counter_mode =
                        mode;

                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    feature.usage.current =
                        mode === 'build'
                            ? 0
                            : feature.usage.max;
                },

                clampFeatureUsage(feature) {
                    feature.usage.max =
                        Math.max(
                            1,
                            parseInt(
                                feature.usage.max
                            ) || 1
                        );

                    feature.usage.current =
                        Math.max(
                            0,
                            Math.min(
                                feature.usage.max,
                                parseInt(
                                    feature.usage.current
                                ) || 0
                            )
                        );
                },

                recoveryLabel(usage) {
                    if (!usage?.enabled) return '';
                    if (usage.recovery === 'custom') {
                        return usage.recovery_custom || 'Custom';
                    }

                    return this.recoveryLabels[usage.recovery] ?? usage.recovery ?? '';
                },

                trackedFeatureEntries(item) {
                    const features = item?.properties?.features ?? [];

                    return features
                        .map((feature, index) => ({
                            feature,
                            index,
                        }))
                        .filter(entry => entry.feature?.usage?.enabled);
                },

                singleUseFeatureEntries(item) {
                    return this.trackedFeatureEntries(item)
                        .filter(
                            entry =>
                                entry.feature?.usage?.recovery
                                === 'single_use'
                        );
                },

                isSingleUseItem(item) {
                    const tracked =
                        this.trackedFeatureEntries(item);

                    if (tracked.length === 0) {
                        return false;
                    }

                    const singleUse =
                        tracked.filter(
                            entry =>
                                entry.feature?.usage?.recovery
                                === 'single_use'
                        );

                    /*
                    | Um item só é tratado como descartável por inteiro quando
                    | todos os trackers ativos dele são de Uso Único. Habilidades
                    | passivas não interferem nessa regra.
                    */
                    return (
                        singleUse.length > 0
                        && singleUse.length === tracked.length
                    );
                },

                singleUseFeatureUsed(feature) {
                    const usage =
                        feature?.usage;

                    return (
                        !!usage?.enabled
                        && usage.recovery === 'single_use'
                        && (parseInt(usage.current) || 0) <= 0
                    );
                },

                singleUseItemUsed(item) {
                    if (!this.isSingleUseItem(item)) {
                        return false;
                    }

                    const entries =
                        this.singleUseFeatureEntries(item);

                    return (
                        entries.length > 0
                        &&
                        entries.every(
                            entry =>
                                this.singleUseFeatureUsed(
                                    entry.feature
                                )
                        )
                    );
                },

                requestSingleUse(item, featureIndex) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (
                        !usage?.enabled
                        || usage.recovery !== 'single_use'
                    ) {
                        return;
                    }

                    if (this.singleUseFeatureUsed(feature)) {
                        this.showToast(
                            this.isSingleUseItem(item)
                                ? 'Este item já foi usado.'
                                : 'Este uso único já foi gasto.',
                            true
                        );
                        return;
                    }

                    const max =
                        Math.max(
                            1,
                            parseInt(usage.max) || 1
                        );

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                parseInt(usage.current) || 0
                            )
                        );

                    this.pendingSingleUse = {
                        itemId: parseInt(item.id),
                        featureIndex: parseInt(featureIndex),
                        itemName: item?.name ?? 'Item',
                        featureName: feature?.title ?? 'Habilidade',
                        marksWholeItem: this.isSingleUseItem(item),
                        current,
                        max,
                        remainingAfterUse:
                            Math.max(
                                0,
                                current - 1
                            ),
                        isLastCharge:
                            current <= 1,
                    };

                    this.singleUseConfirmOpen = true;
                },

                cancelSingleUse() {
                    if (this.busyFeatureKey !== null) return;

                    this.singleUseConfirmOpen = false;
                    this.pendingSingleUse = null;
                },

                requestDiscardUsedItem(item) {
                    if (!item || !this.singleUseItemUsed(item)) {
                        return;
                    }

                    this.pendingDiscardItem = {
                        id: parseInt(item.id),
                        name: item?.name ?? 'Item',
                    };

                    this.discardUsedConfirmOpen = true;
                },

                cancelDiscardUsedItem() {
                    if (this.busyItemId !== null) return;

                    this.discardUsedConfirmOpen = false;
                    this.pendingDiscardItem = null;
                },

                handleFeatureRecoveryChange(feature) {
                    if (!feature?.usage) return;

                    if (feature.usage.recovery === 'single_use') {
                        /*
                        | Uso Único significa que as cargas gastas não voltam.
                        | O item pode possuir 1, 3, 5 ou qualquer outro máximo.
                        | Ele só fica definitivamente "Usado" ao chegar a 0.
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

                /*
                |--------------------------------------------------------------------------
                | Habilidades visíveis nos cards
                |--------------------------------------------------------------------------
                |
                | Diferente de trackedFeatureEntries(), esta lista inclui também
                | habilidades passivas. Assim itens como a Cursiva não ficam com
                | a parte inferior vazia só porque suas habilidades não possuem
                | rastreador.
                |
                */

                inventorySkillTooltip: null,

                cardFeatureEntries(item) {
                    if (this.isVestige(item)) {
                        return this.vestigeUnlockedStages(item)
                            .slice().reverse()
                            .flatMap(stage => stage.features.map((feature, index) => ({
                                feature: {
                                    ...feature,
                                    usage: this.normalizeFeatureUsage(feature?.usage),
                                },
                                index: `${stage.key}-${index}`,
                                vestige: true,
                                stage: stage.key,
                            })));
                    }

                    const features =
                        item?.properties?.features
                        ?? [];

                    return features
                        .map(
                            (
                                feature,
                                index
                            ) => ({
                                feature,
                                index,
                                vestige: false,
                            })
                        )
                        .filter(
                            entry =>
                                entry.feature
                                && (
                                    String(
                                        entry.feature.title
                                        ?? ''
                                    ).trim()
                                    || String(
                                        entry.feature.description
                                        ?? ''
                                    ).trim()
                                )
                        );
                },

                // No item consumido, a habilidade usada deve aparecer antes
                // das passivas no card (sem alterar dados nem a ordem no modal).
                cardVisibleFeatureEntries(item) {
                    const entries = this.cardFeatureEntries(item);
                    if (!this.singleUseItemUsed(item)) return entries.slice(0, 2);
                    return [
                        ...entries.filter(entry => this.singleUseFeatureUsed(entry.feature)),
                        ...entries.filter(entry => !this.singleUseFeatureUsed(entry.feature)),
                    ].slice(0, 2);
                },

                // Prévia enxuta: uma habilidade por cartão. Quando existir
                // rastreador utilizável, ele tem prioridade sobre passivas.
                cardHighlightFeatureEntries(item) {
                    const entries = this.cardFeatureEntries(item);
                    const actionable = entries.find(entry =>
                        entry.feature?.usage?.enabled
                        && !this.singleUseFeatureUsed(entry.feature)
                    );
                    return actionable ? [actionable] : entries.slice(0, 1);
                },

                handleImage(event) {
                    const file = event.target.files?.[0] ?? null;
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        this.saveError = 'A imagem deve ter no máximo 5 MB.';
                        event.target.value = '';
                        return;
                    }

                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.imageFile = file;
                    this.form.remove_image = false;
                    this.imagePreview = URL.createObjectURL(file);
                },

                removeImage() {
                    if (this.imagePreview && this.imagePreview.startsWith('blob:')) {
                        URL.revokeObjectURL(this.imagePreview);
                    }

                    this.imageFile = null;
                    this.imagePreview = null;
                    this.form.remove_image = true;
                },

                cleanProperties() {
                    const properties = this.normalizeProperties(this.form.properties, this.form);

                    properties.custom_properties = properties.custom_properties
                        .map(entry => ({
                            title: String(entry.title ?? '').trim(),
                            value: String(entry.value ?? '').trim(),
                            description: String(entry.description ?? '').trim(),
                        }))
                        .filter(entry => entry.title || entry.value || entry.description);

                    properties.features = properties.features
                        .map(feature => ({
                            title: String(feature.title ?? '').trim(),
                            description: String(feature.description ?? '').trim(),
                            usage: this.normalizeFeatureUsage(feature.usage),
                        }))
                        .filter(feature => feature.title || feature.description);

                    if (properties.weapon) {
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
                            .map(part => ({
                                count: Math.max(1, parseInt(part.count) || 1),
                                die: String(part.die ?? '').trim(),
                                type: String(part.type ?? '').trim(),
                            }))
                            .filter(part => part.die || part.type);
                    }

                    return properties;
                },

                buildPayload() {
                    const properties = this.cleanProperties();
                    const wonderful = properties.nature === 'wonderful';
                    const equippable = !!properties.inventory?.equippable;
                    const requiresAttunement = wonderful && !!this.form.requires_attunement;

                    return {
                        name: String(this.form.name ?? '').trim(),
                        type: properties.nature,
                        description: String(this.form.description ?? '').trim() || null,
                        quantity: 1,
                        weight: null,
                        equipped: equippable ? !!this.form.equipped : false,
                        is_magical: wonderful,
                        rarity: this.form.rarity || 'common',
                        requires_attunement: requiresAttunement,
                        attuned: requiresAttunement ? !!this.form.attuned : false,
                        ability_bonuses: this.form.ability_bonuses ?? {},
                        modifiers: this.form.modifiers ?? {},
                        properties,
                    };
                },

                appendFormValue(formData, key, value) {
                    if (Array.isArray(value)) {
                        value.forEach((entry, index) => {
                            this.appendFormValue(formData, `${key}[${index}]`, entry);
                        });
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

                buildFormData(payload, editing) {
                    const formData = new FormData();

                    if (editing) {
                        formData.append('_method', 'PATCH');
                    }

                    Object.entries(payload).forEach(([key, value]) => {
                        this.appendFormValue(formData, key, value);
                    });

                    if (this.imageFile) {
                        formData.append('image', this.imageFile);
                    }

                    if (this.form.remove_image) {
                        formData.append('remove_image', '1');
                    }

                    return formData;
                },

                baseHeaders() {
                    return {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                            ?? '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    };
                },

                jsonHeaders() {
                    return {
                        ...this.baseHeaders(),
                        'Content-Type': 'application/json',
                    };
                },

                async saveItem() {
                    if (this.saving || !this.form) return;

                    // Criar e editar têm permissões distintas.
                    if (this.editingId === null && !this.canCreateInventoryItems) {
                        this.saveError = 'O Mestre desativou a criação de itens nesta campanha.';
                        return;
                    }

                    const payload = this.buildPayload();

                    if (!payload.name) {
                        this.saveError = 'Informe um nome para o item.';
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
                            headers: this.baseHeaders(),
                            body: this.buildFormData(payload, editing),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível salvar o item.')
                            );
                        }

                        const item = this.normalizeItem(data.item);
                        this.upsertItem(item);

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item:
                                            data.item,
                                    },
                                }
                            )
                        );

                        this.closeEditor();
                        this.openDetail(item);
                        this.showToast(editing ? 'Item atualizado.' : 'Item criado.');
                    } catch (error) {
                        this.saveError = error.message ?? 'Não foi possível salvar o item.';
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteItem() {
                    if (!this.editingId || this.saving) return;

                    this.saving = true;

                    try {
                        const response = await fetch(
                            this.urls.destroy.replace('__ITEM__', this.editingId),
                            {
                                method: 'DELETE',
                                headers: this.baseHeaders(),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível excluir o item.'
                            );
                        }

                        const id = parseInt(this.editingId);

                        this.items = this.items.filter(
                            item => parseInt(item.id) !== id
                        );

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item: {
                                            id,
                                            deleted: true,
                                        },
                                    },
                                }
                            )
                        );

                        this.editorOpen = false;
                        this.editingId = null;
                        this.form = null;
                        this.deleteConfirmOpen = false;

                        this.showToast('Item removido do inventário.');
                    } catch (error) {
                        this.saveError = error.message ?? 'Não foi possível excluir o item.';
                    } finally {
                        this.saving = false;
                    }
                },

                upsertItem(raw) {
                    const item = this.normalizeItem(raw);
                    const index = this.items.findIndex(
                        current => parseInt(current.id) === parseInt(item.id)
                    );

                    if (index >= 0) {
                        this.items[index] = item;
                    } else {
                        this.items.push(item);
                    }

                    if (
                        this.detailItem
                        && parseInt(this.detailItem.id) === parseInt(item.id)
                    ) {
                        this.detailItem = item;
                    }

                    return item;
                },

                async toggleEquipped(item) {
                    if (
                        this.busyItemId !== null
                        || !item?.properties?.inventory?.equippable
                    ) {
                        return;
                    }

                    this.busyItemId = item.id;
                    const previous = !!item.equipped;
                    item.equipped = !previous;

                    try {
                        const response = await fetch(
                            this.urls.equipped.replace('__ITEM__', item.id),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    equipped: item.equipped,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível alterar o equipamento.'
                            );
                        }

                        // Mantém a identidade dos cards no Alpine.
                        // Atualiza apenas o item alterado, sem recriar a lista.
                        const savedItem = this.normalizeItem(data.item);
                        const currentItem = this.items.find(
                            candidate => Number(candidate.id) === Number(item.id)
                        );

                        if (currentItem) {
                            Object.assign(currentItem, savedItem);
                        } else {
                            this.upsertItem(data.item);
                        }

                        if (
                            this.detailItem
                            && Number(this.detailItem.id) === Number(item.id)
                            && this.detailItem !== currentItem
                        ) {
                            Object.assign(this.detailItem, savedItem);
                        }

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item:
                                            data.item,
                                    },
                                }
                            )
                        );
                    } catch (error) {
                        item.equipped = previous;
                        this.showToast(error.message, true);
                    } finally {
                        this.busyItemId = null;
                    }
                },

                requestAttunement(item) {
                    if (!item || item.attuned || !item.requires_attunement) return;

                    if (this.attunedItems.length >= this.attunementSlotLimit) {
                        this.pendingAttunementItem = item;
                        this.attunementWarningOpen = true;
                        return;
                    }

                    this.applyAttunement(item, true);
                },

                confirmExtraAttunement() {
                    const item = this.pendingAttunementItem;

                    if (!item) {
                        this.attunementWarningOpen = false;
                        return;
                    }

                    this.pendingAttunementItem = null;
                    this.attunementWarningOpen = false;
                    this.attunementPickerOpen = false;

                    this.applyAttunement(item, true);
                },

                async applyAttunement(item, attuned) {
                    if (this.busyItemId !== null) return;

                    this.busyItemId = item.id;
                    const previous = !!item.attuned;
                    item.attuned = !!attuned;

                    try {
                        const response = await fetch(
                            this.urls.attunement.replace('__ITEM__', item.id),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    attuned: !!attuned,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível alterar a sintonização.'
                            );
                        }

                        this.upsertItem(data.item);
                    } catch (error) {
                        item.attuned = previous;
                        this.showToast(
                            error.message ?? 'Não foi possível alterar a sintonização.',
                            true
                        );
                    } finally {
                        this.busyItemId = null;
                    }
                },

                async persistFeatureUses(item, featureIndex, next) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (!usage?.enabled) return null;
                    if (this.busyFeatureKey !== null) return null;

                    const key = `${item.id}:${featureIndex}`;
                    const max = Math.max(1, parseInt(usage.max) || 1);
                    const current = Math.max(
                        0,
                        Math.min(max, parseInt(usage.current) || 0)
                    );

                    const normalizedNext = Math.max(
                        0,
                        Math.min(max, parseInt(next) || 0)
                    );

                    if (normalizedNext === current) {
                        return item;
                    }

                    this.busyFeatureKey = key;
                    usage.current = normalizedNext;

                    try {
                        const response = await fetch(
                            this.urls.featureUses
                                .replace('__ITEM__', item.id)
                                .replace('__FEATURE__', featureIndex),
                            {
                                method: 'PATCH',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({
                                    current: normalizedNext,
                                }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível atualizar os usos.'
                            );
                        }

                        return this.upsertItem(data.item);
                    } catch (error) {
                        usage.current = current;
                        this.showToast(
                            error.message ?? 'Não foi possível atualizar os usos.',
                            true
                        );
                        return null;
                    } finally {
                        this.busyFeatureKey = null;
                    }
                },

                async changeFeatureUses(item, featureIndex, delta) {
                    const feature =
                        item?.properties?.features?.[featureIndex];

                    const usage =
                        feature?.usage;

                    if (!usage?.enabled) return;
                    if (this.busyFeatureKey !== null) return;

                    const max = Math.max(1, parseInt(usage.max) || 1);
                    const current = Math.max(
                        0,
                        Math.min(max, parseInt(usage.current) || 0)
                    );

                    /*
                    | Uso Único não possui botão de "desfazer".
                    | O gasto passa pela confirmação própria e, depois de usado,
                    | o item fica aguardando descarte.
                    */
                    if (usage.recovery === 'single_use') {
                        if (delta < 0) {
                            if (current <= 0) {
                                this.showToast(
                                    'Este item já foi usado.',
                                    true
                                );
                                return;
                            }

                            this.requestSingleUse(
                                item,
                                featureIndex
                            );
                            return;
                        }

                        if (current <= 0 && delta > 0) {
                            this.showToast(
                                'Itens de uso único não podem ser restaurados pelo contador.',
                                true
                            );
                            return;
                        }
                    }

                    const next = Math.max(
                        0,
                        Math.min(max, current + delta)
                    );

                    if (next === current) return;

                    await this.persistFeatureUses(
                        item,
                        featureIndex,
                        next
                    );
                },

                async confirmSingleUse() {
                    const pending =
                        this.pendingSingleUse;

                    if (!pending || this.busyFeatureKey !== null) {
                        return;
                    }

                    const item =
                        this.items.find(
                            current =>
                                parseInt(current.id)
                                === parseInt(pending.itemId)
                        )
                        ?? (
                            parseInt(this.detailItem?.id)
                            === parseInt(pending.itemId)
                                ? this.detailItem
                                : null
                        );

                    if (!item) {
                        this.cancelSingleUse();
                        return;
                    }

                    const featureIndex =
                        parseInt(pending.featureIndex);

                    this.singleUseConfirmOpen = false;
                    this.pendingSingleUse = null;

                    const feature =
                        item?.properties
                            ?.features
                            ?.[featureIndex];

                    const usage =
                        feature?.usage;

                    const max =
                        Math.max(
                            1,
                            parseInt(usage?.max) || 1
                        );

                    const current =
                        Math.max(
                            0,
                            Math.min(
                                max,
                                parseInt(
                                    usage?.current
                                ) || 0
                            )
                        );

                    if (current <= 0) {
                        this.showToast(
                            'Este item já está sem cargas.',
                            true
                        );
                        return;
                    }

                    const updated =
                        await this.persistFeatureUses(
                            item,
                            featureIndex,
                            current - 1
                        );

                    if (!updated) return;

                    const updatedFeature =
                        updated?.properties
                            ?.features
                            ?.[featureIndex];

                    const remaining =
                        Math.max(
                            0,
                            parseInt(
                                updatedFeature
                                    ?.usage
                                    ?.current
                            ) || 0
                        );

                    this.showToast(
                        this.singleUseItemUsed(updated)
                            ? 'Última carga gasta. O item agora está Usado e pode ser descartado.'
                            : (
                                remaining > 0
                                    ? (
                                        'Uso registrado. Restam '
                                        + remaining
                                        + ' carga'
                                        + (
                                            remaining === 1
                                                ? ''
                                                : 's'
                                        )
                                        + '.'
                                    )
                                    : 'Uso registrado.'
                            )
                    );
                },

                async confirmDiscardUsedItem() {
                    const pending =
                        this.pendingDiscardItem;

                    if (!pending || this.busyItemId !== null) {
                        return;
                    }

                    const item =
                        this.items.find(
                            current =>
                                parseInt(current.id)
                                === parseInt(pending.id)
                        );

                    if (!item || !this.singleUseItemUsed(item)) {
                        this.cancelDiscardUsedItem();
                        return;
                    }

                    this.busyItemId = item.id;

                    try {
                        const response = await fetch(
                            this.urls.destroy.replace('__ITEM__', item.id),
                            {
                                method: 'DELETE',
                                headers: this.baseHeaders(),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            throw new Error(
                                data?.message ?? 'Não foi possível jogar o item fora.'
                            );
                        }

                        const id = parseInt(item.id);

                        this.items = this.items.filter(
                            current => parseInt(current.id) !== id
                        );

                        if (
                            this.detailItem
                            && parseInt(this.detailItem.id) === id
                        ) {
                            this.closeDetail();
                        }

                        window.dispatchEvent(
                            new CustomEvent(
                                'character-equipment-updated',
                                {
                                    detail: {
                                        item: {
                                            id,
                                            deleted: true,
                                        },
                                    },
                                }
                            )
                        );

                        this.discardUsedConfirmOpen = false;
                        this.pendingDiscardItem = null;
                        this.showToast('Item descartado do inventário.');
                    } catch (error) {
                        this.showToast(
                            error.message ?? 'Não foi possível jogar o item fora.',
                            true
                        );
                    } finally {
                        this.busyItemId = null;
                    }
                },

                openFoundItems() {
                    this.foundItemsOpen = true;
                    this.loadFoundLoot();
                },

                normalizeFoundLootItem(raw) {
                    return {
                        id: parseInt(raw?.id) || 0,
                        loot_drop_id: parseInt(raw?.loot_drop_id) || 0,
                        drop_name: raw?.drop_name ?? '',
                        campaign_name: raw?.campaign_name ?? '',
                        name: raw?.name ?? '',
                        type: raw?.type ?? 'mundane',
                        description: raw?.description ?? '',
                        image_url: raw?.image_url ?? null,
                        quantity_remaining: Math.max(0, parseInt(raw?.quantity_remaining) || 0),
                        weight: raw?.weight === null || raw?.weight === undefined || raw?.weight === ''
                            ? null
                            : Number(raw.weight),
                        is_magical: !!raw?.is_magical,
                        rarity: raw?.rarity ?? null,
                        rarity_label: raw?.rarity_label ?? (raw?.rarity ? (this.rarityLabels?.[raw.rarity] ?? raw.rarity) : null),
                        requires_attunement: !!raw?.requires_attunement,
                        armor_class: raw?.armor_class ?? null,
                        damage: raw?.damage ?? null,
                        attack_bonus: raw?.attack_bonus ?? null,
                        damage_bonus: raw?.damage_bonus ?? null,
                        ability_bonuses: raw?.ability_bonuses ?? {},
                        properties: raw?.properties ?? {},
                        modifiers: raw?.modifiers ?? {},
                        notes: raw?.notes ?? '',
                    };
                },

                normalizeFoundLootCurrency(raw) {
                    const coins = raw?.coins ?? {};

                    return {
                        id: parseInt(raw?.id) || 0,
                        loot_drop_id: parseInt(raw?.loot_drop_id) || 0,
                        drop_name: raw?.drop_name ?? '',
                        campaign_name: raw?.campaign_name ?? '',
                        label: raw?.label ?? 'Moedas',
                        distribution_mode: raw?.distribution_mode ?? 'first_claim',
                        coins: {
                            copper: this.normalizeMoney(coins?.copper),
                            silver: this.normalizeMoney(coins?.silver),
                            electrum: this.normalizeMoney(coins?.electrum),
                            gold: this.normalizeMoney(coins?.gold),
                            platinum: this.normalizeMoney(coins?.platinum),
                        },
                    };
                },

                foundLootNatureLabel(item) {
                    const nature = item?.type ?? (item?.is_magical ? 'wonderful' : 'mundane');
                    return this.natureLabels?.[nature] ?? String(nature ?? 'Item');
                },

                foundLootCoinLines(entry) {
                    const labels = {
                        copper: 'PC',
                        silver: 'PP',
                        electrum: 'PE',
                        gold: 'PO',
                        platinum: 'PL',
                    };

                    return Object.entries(labels)
                        .map(([coin, label]) => ({
                            coin,
                            label,
                            amount: this.normalizeMoney(entry?.coins?.[coin]),
                        }))
                        .filter(line => line.amount > 0);
                },

                foundLootCurrencyModeLabel(entry) {
                    if (entry?.distribution_mode === 'split') {
                        return 'Sua parte já está separada';
                    }

                    if (entry?.distribution_mode === 'open') {
                        return 'Caixa comum da Party';
                    }

                    return 'Reservado para você';
                },

                ensureFoundLootCurrencyDraft(entry) {
                    const id = parseInt(entry?.id) || 0;

                    if (!id) return;

                    if (!this.foundLootCurrencyDrafts[id]) {
                        this.foundLootCurrencyDrafts[id] = {
                            copper: 0,
                            silver: 0,
                            electrum: 0,
                            gold: 0,
                            platinum: 0,
                        };
                    }
                },

                foundLootCurrencyDraftAmount(entry, coin) {
                    this.ensureFoundLootCurrencyDraft(entry);

                    return this.normalizeMoney(
                        this.foundLootCurrencyDrafts?.[entry?.id]?.[coin]
                    );
                },

                async loadFoundLoot(silent = false) {
                    if (this.foundLootLoading) return;

                    this.foundLootLoading = true;
                    if (!silent) this.foundLootError = null;

                    try {
                        const response = await fetch(this.urls.foundLootIndex, {
                            method: 'GET',
                            headers: this.baseHeaders(),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível carregar os itens encontrados.')
                            );
                        }

                        this.foundLootItems = Array.isArray(data?.items)
                            ? data.items
                                .map(item => this.normalizeFoundLootItem(item))
                                .filter(item => item.id > 0)
                            : [];

                        this.foundLootCurrencies = Array.isArray(data?.currencies)
                            ? data.currencies
                                .map(entry => this.normalizeFoundLootCurrency(entry))
                                .filter(entry => entry.id > 0)
                            : [];

                        for (const entry of this.foundLootCurrencies) {
                            this.ensureFoundLootCurrencyDraft(entry);
                        }

                        this.foundLootLoaded = true;
                        this.foundLootError = null;
                    } catch (error) {
                        this.foundLootError = error?.message
                            ?? 'Não foi possível carregar os itens encontrados.';

                        if (!silent) {
                            this.showToast(this.foundLootError, true);
                        }
                    } finally {
                        this.foundLootLoading = false;
                    }
                },

                async claimFoundLootItem(item) {
                    const itemId = parseInt(item?.id) || 0;

                    if (
                        !itemId
                        || this.foundLootBusyItemId !== null
                        || this.foundLootBusyCurrencyId !== null
                    ) {
                        return;
                    }

                    this.foundLootBusyItemId = itemId;

                    try {
                        const response = await fetch(
                            this.urls.foundLootClaimItem.replace('__LOOT_ITEM__', itemId),
                            {
                                method: 'POST',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({ quantity: 1 }),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível adicionar o item ao inventário.')
                            );
                        }

                        if (data?.item) {
                            this.upsertItem(data.item);
                        }

                        const remaining = Math.max(0, parseInt(data?.quantity_remaining) || 0);

                        if (remaining > 0) {
                            const found = this.foundLootItems.find(
                                current => parseInt(current.id) === itemId
                            );

                            if (found) found.quantity_remaining = remaining;
                        } else {
                            this.foundLootItems = this.foundLootItems.filter(
                                current => parseInt(current.id) !== itemId
                            );
                        }

                        this.showToast(data?.message ?? 'Item adicionado ao inventário.');
                    } catch (error) {
                        this.showToast(
                            error?.message ?? 'Não foi possível adicionar o item ao inventário.',
                            true
                        );

                        await this.loadFoundLoot(true);
                    } finally {
                        this.foundLootBusyItemId = null;
                    }
                },

                async claimFoundLootCurrency(entry) {
                    const currencyId = parseInt(entry?.id) || 0;

                    if (
                        !currencyId
                        || this.foundLootBusyCurrencyId !== null
                        || this.foundLootBusyItemId !== null
                    ) {
                        return;
                    }

                    this.ensureFoundLootCurrencyDraft(entry);

                    const payload = {};

                    if (entry?.distribution_mode === 'open') {
                        for (const coin of [
                            'copper',
                            'silver',
                            'electrum',
                            'gold',
                            'platinum',
                        ]) {
                            payload[coin] =
                                this.foundLootCurrencyDraftAmount(
                                    entry,
                                    coin
                                );
                        }

                        const hasAmount =
                            Object.values(payload)
                                .some(value => value > 0);

                        if (!hasAmount) {
                            this.showToast(
                                'Informe quanto você quer retirar da caixa da Party.',
                                true
                            );
                            return;
                        }
                    }

                    this.foundLootBusyCurrencyId = currencyId;

                    try {
                        const response = await fetch(
                            this.urls.foundLootClaimCurrency.replace('__CURRENCY__', currencyId),
                            {
                                method: 'POST',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify(payload),
                            }
                        );

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (data?.message ?? 'Não foi possível adicionar as moedas à bolsa.')
                            );
                        }

                        if (data?.wallet) {
                            this.wallet = {
                                copper: this.normalizeMoney(data.wallet.copper),
                                silver: this.normalizeMoney(data.wallet.silver),
                                electrum: this.normalizeMoney(data.wallet.electrum),
                                gold: this.normalizeMoney(data.wallet.gold),
                                platinum: this.normalizeMoney(data.wallet.platinum),
                            };
                        }

                        this.foundLootCurrencyDrafts[currencyId] = {
                            copper: 0,
                            silver: 0,
                            electrum: 0,
                            gold: 0,
                            platinum: 0,
                        };

                        await this.loadFoundLoot(true);

                        this.showToast(
                            data?.message
                            ?? 'Moedas adicionadas à bolsa.'
                        );
                    } catch (error) {
                        this.showToast(
                            error?.message ?? 'Não foi possível adicionar as moedas à bolsa.',
                            true
                        );

                        await this.loadFoundLoot(true);
                    } finally {
                        this.foundLootBusyCurrencyId = null;
                    }
                },

                normalizeMoney(value) {
                    const digits = String(value ?? '')
                        .replace(/\D/g, '');

                    return Math.max(
                        0,
                        parseInt(digits, 10) || 0
                    );
                },

                formatMoney(value) {
                    return String(
                        this.normalizeMoney(value)
                    ).replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ','
                    );
                },

                handleMoneyInput(currency, event) {
                    const value = this.normalizeMoney(
                        event?.target?.value
                    );

                    this.wallet[currency] = value;

                    if (event?.target) {
                        event.target.value = this.formatMoney(value);
                    }

                    this.saveWallet();
                },

                saveWallet() {
                    clearTimeout(this.walletTimer);

                    this.walletTimer = setTimeout(() => {
                        this.walletTimer = null;
                        this.persistWallet();
                    }, 300);
                },

                async persistWallet() {
                    if (this.walletSaving) {
                        this.walletPendingSave = true;
                        return;
                    }

                    const payload = {
                        copper: this.normalizeMoney(this.wallet.copper),
                        silver: this.normalizeMoney(this.wallet.silver),
                        electrum: this.normalizeMoney(this.wallet.electrum),
                        gold: this.normalizeMoney(this.wallet.gold),
                        platinum: this.normalizeMoney(this.wallet.platinum),
                    };

                    this.walletSaving = true;
                    this.walletPendingSave = false;

                    try {
                        const response = await fetch(this.urls.wallet, {
                            method: 'PATCH',
                            headers: this.jsonHeaders(),
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const errors = data?.errors
                                ? Object.values(data.errors).flat().filter(Boolean)
                                : [];

                            throw new Error(
                                errors.length
                                    ? errors.join(' ')
                                    : (
                                        data?.message
                                        ?? 'Não foi possível salvar as moedas.'
                                    )
                            );
                        }
                    } catch (error) {
                        this.showToast(
                            error?.message
                            ?? 'Não foi possível salvar as moedas.',
                            true
                        );
                    } finally {
                        this.walletSaving = false;

                        const current = {
                            copper: this.normalizeMoney(this.wallet.copper),
                            silver: this.normalizeMoney(this.wallet.silver),
                            electrum: this.normalizeMoney(this.wallet.electrum),
                            gold: this.normalizeMoney(this.wallet.gold),
                            platinum: this.normalizeMoney(this.wallet.platinum),
                        };

                        const changedDuringRequest =
                            Object.keys(payload).some(
                                key => current[key] !== payload[key]
                            );

                        if (
                            this.walletPendingSave
                            || changedDuringRequest
                        ) {
                            this.walletPendingSave = false;
                            this.saveWallet();
                        }
                    }
                },

                showToast(message, error = false) {
                    // Contrato único: os próximos componentes podem usar
                    // exatamente o mesmo evento e a mesma apresentação.
                    window.dispatchEvent(new CustomEvent('spellbound-notify', {
                        detail: {
                            title: error ? 'Ação não concluída' : 'Inventário',
                            message: String(message ?? ''),
                            variant: error ? 'error' : 'success',
                        },
                    }));
                },
            }));
        });
    </script>