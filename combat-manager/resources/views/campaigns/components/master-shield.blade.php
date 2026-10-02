@php
    $storedMasterNotes =
        $campaign->master_notes
        ?? '';

    $decodedMasterNotes =
        is_string($storedMasterNotes)
        && trim($storedMasterNotes) !== ''
            ? json_decode(
                $storedMasterNotes,
                true
            )
            : null;

    if (
        is_array($decodedMasterNotes)
        &&
        isset($decodedMasterNotes['pages'])
        &&
        is_array($decodedMasterNotes['pages'])
        &&
        count($decodedMasterNotes['pages']) > 0
    ) {
        $masterNotePages =
            collect(
                $decodedMasterNotes['pages']
            )
                ->map(
                    fn ($page, $index) => [
                        'id' =>
                            (string) (
                                $page['id']
                                ?? 'page-' . ($index + 1)
                            ),

                        'title' =>
                            (string) (
                                $page['title']
                                ?? ''
                            ),

                        'content' =>
                            (string) (
                                $page['content']
                                ?? ''
                            ),
                    ]
                )
                ->values()
                ->all();

        $activeMasterNoteId =
            (string) (
                $decodedMasterNotes['active_page_id']
                ?? $masterNotePages[0]['id']
            );
    } else {
        /*
        | Compatibilidade com as anotações antigas:
        | o texto simples vira automaticamente a primeira página.
        */
        $masterNotePages = [
            [
                'id' =>
                    'page-1',

                'title' =>
                    '',

                'content' =>
                    is_string($storedMasterNotes)
                        ? $storedMasterNotes
                        : '',
            ],
        ];

        $activeMasterNoteId =
            'page-1';
    }

    $masterNotesInitialSnapshot =
        json_encode(
            [
                'pages' =>
                    $masterNotePages,

                'active_page_id' =>
                    $activeMasterNoteId,
            ],
            JSON_UNESCAPED_UNICODE
            |
            JSON_UNESCAPED_SLASHES
        );

    $hiddenPartyUserIds =
        $campaign
            ->members
            ->filter(
                fn ($member) =>
                    (bool) (
                        $member->hidden_from_party
                        ?? false
                    )
            )
            ->map(
                fn ($member) =>
                    (int) $member->user_id
            )
            ->values()
            ->all();

    /*
    | Limite real de upload do PHP.
    | O front-end usa este valor para avisar antes de tentar enviar um arquivo
    | que o servidor descartaria por upload_max_filesize/post_max_size.
    */
    $parseIniBytes = static function ($value): int {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    };

    $phpUploadMaxBytes =
        $parseIniBytes(ini_get('upload_max_filesize'));

    $phpPostMaxBytes =
        $parseIniBytes(ini_get('post_max_size'));

    $positiveTransportLimits =
        array_values(
            array_filter(
                [
                    $phpUploadMaxBytes,
                    $phpPostMaxBytes > 0
                        ? max(0, $phpPostMaxBytes - (256 * 1024))
                        : 0,
                ],
                fn ($value) => $value > 0
            )
        );

    $shopTransportLimitBytes =
        count($positiveTransportLimits) > 0
            ? min($positiveTransportLimits)
            : 12 * 1024 * 1024;

    /*
    | Mantemos uma pequena margem para o multipart/form-data.
    */
    $shopSafeTransportLimitBytes =
        max(
            256 * 1024,
            (int) floor($shopTransportLimitBytes * 0.96)
        );

    $shopPortraitUploadLimitBytes =
        min(
            8 * 1024 * 1024,
            $shopSafeTransportLimitBytes
        );

    $shopBackgroundUploadLimitBytes =
        min(
            12 * 1024 * 1024,
            $shopSafeTransportLimitBytes
        );
@endphp


<section
    x-data="{
        liveUrl:
            @js(
                route(
                    'campaigns.master.live',
                    $campaign
                )
            ),

        removeMemberUrlTemplate:
            @js(
                route(
                    'campaigns.master.members.destroy',
                    [
                        'campaign' =>
                            $campaign,

                        'user' =>
                            '__USER__',
                    ]
                )
            ),

        memberVisibilityUrlTemplate:
            @js(
                route(
                    'campaigns.master.members.visibility',
                    [
                        'campaign' =>
                            $campaign,

                        'user' =>
                            '__USER__',
                    ]
                )
            ),

        notesUrl:
            @js(
                route(
                    'campaigns.master.notes.update',
                    $campaign
                )
            ),

        vaultItemsUrl:
            @js(
                route(
                    'master.vault.items.index'
                )
            ),

        lootIndexUrl:
            @js(
                route(
                    'master.loot.index',
                    $campaign
                )
            ),

        lootStoreUrl:
            @js(
                route(
                    'master.loot.store',
                    $campaign
                )
            ),

        lootPresentationUpdateUrlTemplate:
            @js(
                route(
                    'master.loot.presentation.update',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        lootQuickReleaseUrl:
            @js(
                route(
                    'master.loot.quick-release',
                    $campaign
                )
            ),

        lootQuickCurrencyReleaseUrl:
            @js(
                route(
                    'master.loot.quick-currency-release',
                    $campaign
                )
            ),

        shopStoreUrl:
            @js(
                route(
                    'master.shops.store',
                    $campaign
                )
            ),

        workspaceFolderStoreUrl:
            @js(
                route(
                    'workspace-folders.store'
                )
            ),

        workspaceFolderUpdateUrlTemplate:
            @js(
                route(
                    'workspace-folders.update',
                    [
                        'workspaceFolder' => '__FOLDER__',
                    ]
                )
            ),

        workspaceFolderDeleteUrlTemplate:
            @js(
                route(
                    'workspace-folders.destroy',
                    [
                        'workspaceFolder' => '__FOLDER__',
                    ]
                )
            ),

        workspaceFolderMoveItemUrl:
            @js(
                route(
                    'workspace-folder-items.move'
                )
            ),

        workspaceFolderReorderUrl:
            @js(
                route(
                    'workspace-folders.reorder'
                )
            ),

        workspaceFolderScopeType:
            'campaign.shops',

        workspaceFolderScopeId:
            @js(
                (int) $campaign->id
            ),

        workspaceLootFolderScopeType:
            'campaign.loot',

        workspaceLootFolderScopeId:
            @js(
                (int) $campaign->id
            ),

        shopOpenUrlTemplate:
            @js(
                route(
                    'master.shops.open',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        shopCloseUrlTemplate:
            @js(
                route(
                    'master.shops.close',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        shopPresentationUpdateUrlTemplate:
            @js(
                route(
                    'master.shops.presentation.update',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        shopAddItemUrlTemplate:
            @js(
                route(
                    'master.shops.items.store',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        shopUpdateItemUrlTemplate:
            @js(
                route(
                    'master.shops.items.update',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                        'lootItem' => '__ITEM__',
                    ]
                )
            ),

        lootDeleteDropUrlTemplate:
            @js(
                route(
                    'master.loot.destroy',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',
                    ]
                )
            ),

        lootAddItemUrlTemplate:
            @js(
                route(
                    'master.loot.items.store',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',
                    ]
                )
            ),

        lootReleaseItemUrlTemplate:
            @js(
                route(
                    'master.loot.items.release',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'lootItem' =>
                            '__ITEM__',
                    ]
                )
            ),

        lootWithdrawItemUrlTemplate:
            @js(
                route(
                    'master.loot.items.withdraw',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'lootItem' =>
                            '__ITEM__',
                    ]
                )
            ),

        lootReleaseAllItemsUrlTemplate:
            @js(
                route(
                    'master.loot.items.release-all',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',
                    ]
                )
            ),

        lootDeleteItemUrlTemplate:
            @js(
                route(
                    'master.loot.items.destroy',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'lootItem' =>
                            '__ITEM__',
                    ]
                )
            ),

        lootReverseClaimUrlTemplate:
            @js(
                route(
                    'master.loot.items.claims.reverse',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'lootItem' =>
                            '__ITEM__',

                        'claim' =>
                            '__CLAIM__',
                    ]
                )
            ),

        lootItemImageUrlTemplate:
            @js(
                route(
                    'master.loot.items.image',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'lootItem' =>
                            '__ITEM__',
                    ]
                )
            ),

        lootCurrencyStoreUrlTemplate:
            @js(
                route(
                    'master.loot.currency.store',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',
                    ]
                )
            ),

        lootCurrencyReleaseUrlTemplate:
            @js(
                route(
                    'master.loot.currency.release',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'currency' =>
                            '__CURRENCY__',
                    ]
                )
            ),

        lootCurrencyWithdrawUrlTemplate:
            @js(
                route(
                    'master.loot.currency.withdraw',
                    [
                        'campaign' =>
                            $campaign,

                        'lootDrop' =>
                            '__DROP__',

                        'currency' =>
                            '__CURRENCY__',
                    ]
                )
            ),

        lootCloseDropUrlTemplate:
            @js(
                route(
                    'master.loot.close',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => '__DROP__',
                    ]
                )
            ),

        csrf:
            @js(
                csrf_token()
            ),

        players:
            @js(
                $masterShieldInitial['players']
                ?? []
            ),

        loading:
            false,

        loadedOnce:
            true,

        liveError:
            null,

        timer:
            null,

        drawer:
            null,

        characterTarget:
            null,

        characterModalOpen:
            false,

        characterTab:
            'summary',

        inventoryItemOpenId:
            null,

        messageTarget:
            null,

        removingMemberUserId:
            null,

        memberVisibilityUserId:
            null,

        hiddenMemberUserIds:
            @js(
                $hiddenPartyUserIds
            ),

        /*
        |--------------------------------------------------------------------------
        | Vault -> Espólios
        |--------------------------------------------------------------------------
        */
        lootLoaded:
            false,

        lootLoading:
            false,

        lootError:
            null,

        lootDrops:
            [],

        lootFolders:
            [],

        lootFolderOpenState:
            {},

        lootFolderMenuId:
            null,

        lootFolderMenuX:
            0,

        lootFolderMenuY:
            0,

        lootFolderModalOpen:
            false,

        lootFolderEditingId:
            null,

        lootFolderName:
            '',

        lootFolderDescription:
            '',

        lootFolderContent:
            '',

        lootFolderColor:
            '#6B1D14',

        lootFolderTextColor:
            '#FFF8F0',

        lootFolderImageFile:
            null,

        lootFolderImagePreview:
            null,

        lootFolderRemoveImage:
            false,

        lootFolderSaving:
            false,

        lootFolderFeedback:
            null,

        lootFolderFeedbackType:
            'info',

        lootFolderDraggingDropId:
            null,

        lootFolderDropTarget:
            null,

        lootActiveFolderId:
            null,

        lootNewFolderId:
            null,

        lootShops:
            [],

        shopFolders:
            [],

        shopFolderSystemReady:
            true,

        shopFolderSystemMessage:
            null,

        shopFolderOpenState:
            {},

        shopFolderMenuId:
            null,

        shopFolderModalOpen:
            false,

        shopFolderEditingId:
            null,

        shopFolderName:
            '',

        shopFolderDescription:
            '',

        shopFolderContent:
            '',

        shopFolderColor:
            '#8A4B20',

        shopFolderTextColor:
            '#53150F',

        shopFolderImageFile:
            null,

        shopFolderImagePreview:
            null,

        shopFolderRemoveImage:
            false,

        shopFolderSaving:
            false,

        shopFolderDraggingShopId:
            null,

        shopFolderDropTarget:
            null,

        shopActiveFolderId:
            null,

        shopFolderMenuX:
            0,

        shopFolderMenuY:
            0,

        shopNewFolderId:
            null,

        shopProductModalOpen:
            false,

        shopStockCategoryOpenKey:
            null,

        shopkeepers:
            [],

        lootWorkspaceMode:
            'loot',

        lootActiveShopId:
            null,

        shopContentTab:
            'stock',

        shopItemDetailOpenId:
            null,

        shopStockSearch:
            '',

        shopStockFilter:
            'all',

        lootAutoRefreshAt:
            0,

        lootQuickDrop:
            null,

        vaultItems:
            [],

        lootActiveDropId:
            null,

        lootDropMenuId:
            null,

        lootDropMenuTop:
            0,

        lootDropMenuLeft:
            0,

        lootRewardMenuOpen:
            false,

        lootRewardTypeFilter:
            'all',

        lootDropEditorOpen:
            false,

        lootDropEditName:
            '',

        lootDropEditDescription:
            '',

        lootDropImageFile:
            null,

        lootDropImagePreview:
            null,

        lootDropRemoveImage:
            false,

        lootDropSaving:
            false,

        lootDropFeedback:
            null,

        lootDropFeedbackType:
            'info',

        lootContentTab:
            'items',

        lootDropSearch:
            '',

        lootDropStatusFilter:
            'all',

        lootDropSort:
            'recent',

        lootItemSearch:
            '',

        lootItemStatusFilter:
            'all',

        lootDeletingDropId:
            null,

        lootVaultSearch:
            '',

        lootPickerOpen:
            false,

        lootQuickOpen:
            false,

        lootQuickSearch:
            '',

        lootQuickSelectedItem:
            null,

        lootQuickQuantity:
            1,

        lootQuickVisibility:
            'party',

        lootQuickCharacterIds:
            [],

        lootQuickReleasing:
            false,

        lootQuickError:
            null,

        lootQuickNotice:
            null,

        lootQuickTab:
            'available',

        lootQuickComposerOpen:
            false,

        lootQuickComposerMode:
            'item',

        lootQuickExpandedItemId:
            null,

        lootQuickExpandedCurrencyId:
            null,

        lootQuickItemMenuId:
            null,

        lootQuickCurrencyLabel:
            '',

        lootQuickCurrencyAmounts:
            {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            },

        lootQuickCurrencyDistribution:
            'open',

        lootQuickCurrencyCharacterIds:
            [],

        lootQuickCurrencyReleasing:
            false,

        lootQuickActingCurrencyId:
            null,

        lootCreateOpen:
            false,

        lootNewDropName:
            '',

        lootNewDropDescription:
            '',

        lootCreating:
            false,

        lootAddingVaultId:
            null,

        lootActingItemId:
            null,

        lootReleasingAll:
            false,

        lootDeletingItemId:
            null,

        lootReversingClaimId:
            null,

        lootReleaseItemId:
            null,

        lootReleaseVisibility:
            'party',

        lootReleaseCharacterIds:
            [],

        lootCurrencyFormOpen:
            false,

        lootCurrencyLabel:
            '',

        lootCurrencyAmounts:
            {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            },

        lootAddingCurrency:
            false,

        lootActingCurrencyId:
            null,

        lootReleaseCurrencyId:
            null,

        lootCurrencyReleaseVisibility:
            'party',

        lootCurrencyDistribution:
            'open',

        lootCurrencyReleaseCharacterIds:
            [],

        lootCoinDefinitions:
            [
                { key: 'copper', abbr: 'PC', label: 'Cobre' },
                { key: 'silver', abbr: 'PP', label: 'Prata' },
                { key: 'electrum', abbr: 'PE', label: 'Electro' },
                { key: 'gold', abbr: 'PO', label: 'Ouro' },
                { key: 'platinum', abbr: 'PL', label: 'Platina' },
            ],

        shopCreateOpen:
            false,

        shopNewName:
            '',

        shopNewDescription:
            '',

        shopNewShopkeeperNpcId:
            '',

        shopNewAttendantName:
            '',

        shopNewShopkeeperOpen:
            false,

        shopNewShopkeeperSearch:
            '',

        shopNewImageFile:
            null,

        shopNewImagePreview:
            null,

        shopNewBackgroundFile:
            null,

        shopNewBackgroundPreview:
            null,

        shopPortraitUploadLimitBytes:
            @js($shopPortraitUploadLimitBytes),

        shopBackgroundUploadLimitBytes:
            @js($shopBackgroundUploadLimitBytes),

        shopServerUploadLimitBytes:
            @js($shopTransportLimitBytes),

        shopNewImageFeedback:
            null,

        shopNewImageFeedbackType:
            'info',

        shopNewBackgroundFeedback:
            null,

        shopNewBackgroundFeedbackType:
            'info',

        shopEditImageFeedback:
            null,

        shopEditImageFeedbackType:
            'info',

        shopEditBackgroundFeedback:
            null,

        shopEditBackgroundFeedbackType:
            'info',

        shopNewHasCounter:
            true,

        shopNewCounterColor:
            '#6B3A24',

        shopNewBackgroundColor:
            '#6F4A33',

        shopNewImageZoom:
            100,

        shopNewImagePosition:
            'center',

        shopNewBackgroundZoom:
            100,

        shopNewBackgroundPosition:
            'center',

        shopCreating:
            false,

        shopPresentationOpen:
            false,

        shopPresentationSaving:
            false,

        shopEditName:
            '',

        shopEditDescription:
            '',

        shopEditShopkeeperNpcId:
            '',

        shopEditAttendantName:
            '',

        shopEditShopkeeperOpen:
            false,

        shopEditShopkeeperSearch:
            '',

        shopEditImageFile:
            null,

        shopEditImagePreview:
            null,

        shopEditRemoveImage:
            false,

        shopEditBackgroundFile:
            null,

        shopEditBackgroundPreview:
            null,

        shopEditRemoveBackground:
            false,

        shopEditHasCounter:
            true,

        shopEditCounterColor:
            '#6B3A24',

        shopEditBackgroundColor:
            '#6F4A33',

        shopEditImageZoom:
            100,

        shopEditImagePosition:
            'center',

        shopEditBackgroundZoom:
            100,

        shopEditBackgroundPosition:
            'center',

        shopSearch:
            '',

        shopPickerOpen:
            false,

        shopHeaderMenuOpen:
            false,

        shopPriceHelpOpen:
            false,

        shopItemAddedNotice:
            '',

        shopItemAddedNoticeVisible:
            false,

        shopItemAddedNoticeToken:
            0,

        shopVaultSearch:
            '',

        shopVaultNature:
            'all',

        shopVaultRarity:
            'all',

        shopSelectedVaultItem:
            null,

        shopAddQuantity:
            1,

        shopPriceAmounts:
            {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            },

        shopAdding:
            false,

        shopEditingItemId:
            null,

        shopEditPriceAmounts:
            {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            },

        shopUpdatingItemId:
            null,

        notePages:
            @js(
                $masterNotePages
            ),

        activeNoteId:
            @js(
                $activeMasterNoteId
            ),

        editorTitle:
            '',

        editorContent:
            '',

        notesDraftKey:
            @js(
                'spellbound:campaign:'
                . $campaign->id
                . ':master-notebook-draft'
            ),

        savedNotesSnapshot:
            @js(
                $masterNotesInitialSnapshot
            ),

        notesSaving:
            false,

        notesPendingSave:
            false,

        notesRevision:
            0,

        notesSavedRevision:
            0,

        notesStatus:
            'saved',

        notesTimer:
            null,

        notesSavedAt:
            null,

        notesError:
            null,

        init() {
            this.restoreLocalDraft();
            this.loadActiveEditor();

            /*
            | Se havia um rascunho local de uma falha anterior, tentamos
            | persistir automaticamente assim que a tela abre.
            */
            if (
                this.notesSnapshot()
                !==
                this.savedNotesSnapshot
            ) {
                this.queueNotesSave(
                    false,
                    300
                );
            }

            this.refresh();

            this.timer =
                window.setInterval(
                    () => {
                        if (!document.hidden) {
                            this.refresh();

                            if (
                                this.drawer === 'loot'
                                && this.lootLoaded
                                && !this.lootLoading
                                && !this.lootPickerOpen
                                && !this.lootQuickOpen
                                && !this.shopPickerOpen
                                && !this.lootCreateOpen
                                && !this.shopCreateOpen
                                && this.lootReleaseItemId === null
                                && !this.lootCurrencyFormOpen
                                && this.lootReleaseCurrencyId === null
                                && this.lootActingItemId === null
                                && this.lootActingCurrencyId === null
                                && this.shopUpdatingItemId === null
                                && (
                                    Date.now()
                                    - this.lootAutoRefreshAt
                                ) >= 6000
                            ) {
                                this.loadLootWorkspace(true);
                            }
                        }
                    },
                    1200
                );

            document.addEventListener(
                'visibilitychange',
                () => {
                    if (!document.hidden) {
                        this.refresh();
                    }
                }
            );
        },

        destroy() {
            if (this.timer) {
                window.clearInterval(
                    this.timer
                );
            }

            if (this.notesTimer) {
                window.clearTimeout(
                    this.notesTimer
                );
            }
        },

        async refresh() {
            if (this.loading) {
                return;
            }

            this.loading =
                true;

            try {
                const response =
                    await fetch(
                        this.liveUrl,
                        {
                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
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
                        ??
                        'Não foi possível atualizar o Escudo.'
                    );
                }

                if (
                    Array.isArray(
                        data?.players
                    )
                ) {
                    this.players =
                        data.players;

                    this.syncHiddenMemberUserIds(
                        data.players
                    );
                }

                this.liveError =
                    null;

                this.loadedOnce =
                    true;
            } catch (error) {
                console.error(
                    error
                );

                this.liveError =
                    error?.message
                    ?? 'Falha ao atualizar o estado ao vivo';

                this.loadedOnce =
                    true;
            } finally {
                this.loading =
                    false;
            }
        },

        activeNoteIndex() {
            return this.notePages
                .findIndex(
                    page =>
                        page.id
                        ===
                        this.activeNoteId
                );
        },

        activeNote() {
            const index =
                this.activeNoteIndex();

            return index >= 0
                ? this.notePages[index]
                : null;
        },

        noteLabel(
            page,
            index
        ) {
            const title =
                String(
                    page?.title
                    ?? ''
                ).trim();

            return title !== ''
                ? title
                : `Página ${index + 1}`;
        },

        newNotePageId() {
            return `page-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
        },

        notesSnapshot() {
            return JSON.stringify({
                pages:
                    this.notePages,

                active_page_id:
                    this.activeNoteId,
            });
        },

        /*
        |--------------------------------------------------------------------------
        | Editor local
        |--------------------------------------------------------------------------
        |
        | O textarea não escreve mais diretamente dentro de activeNote().
        | Primeiro escrevemos em campos simples e sincronizamos a página atual.
        | Isso impede o conteúdo de desaparecer ao trocar de aba.
        |
        */

        loadActiveEditor() {
            const page =
                this.activeNote();

            this.editorTitle =
                page?.title
                ?? '';

            this.editorContent =
                page?.content
                ?? '';
        },

        commitActiveEditor() {
            const index =
                this.activeNoteIndex();

            if (index < 0) {
                return;
            }

            this.notePages[index].title =
                this.editorTitle;

            this.notePages[index].content =
                this.editorContent;
        },

        editorChanged() {
            this.commitActiveEditor();
            this.persistLocalDraft();

            this.notesRevision +=
                1;

            this.queueNotesSave(
                false,
                1800
            );
        },

        selectNotePage(id) {
            if (
                id
                ===
                this.activeNoteId
            ) {
                return;
            }

            if (
                !this.notePages.some(
                    page =>
                        page.id === id
                )
            ) {
                return;
            }

            /*
            | Primeiro gravamos o que está no editor na página antiga.
            */
            this.commitActiveEditor();

            this.activeNoteId =
                id;

            this.loadActiveEditor();
            this.persistLocalDraft();

            /*
            | Troca de página salva imediatamente. Não dependemos do usuário
            | aguardar o debounce normal depois de clicar em outra aba.
            */
            this.notesRevision +=
                1;

            this.queueNotesSave(
                false,
                0
            );
        },

        addNotePage() {
            this.commitActiveEditor();

            if (
                this.notePages.length
                >=
                40
            ) {
                return;
            }

            const page = {
                id:
                    this.newNotePageId(),

                title:
                    '',

                content:
                    '',
            };

            this.notePages.push(
                page
            );

            this.activeNoteId =
                page.id;

            this.loadActiveEditor();
            this.persistLocalDraft();

            this.notesRevision +=
                1;

            this.queueNotesSave(
                false,
                0
            );

            this.$nextTick(
                () => {
                    this.$refs
                        .noteTitle
                        ?.focus();
                }
            );
        },

        removeActiveNotePage() {
            this.commitActiveEditor();

            if (
                this.notePages.length
                <=
                1
            ) {
                return;
            }

            const currentIndex =
                this.activeNoteIndex();

            if (
                currentIndex
                <
                0
            ) {
                return;
            }

            if (
                !window.confirm(
                    'Excluir esta página de anotações?'
                )
            ) {
                return;
            }

            this.notePages.splice(
                currentIndex,
                1
            );

            const nextIndex =
                Math.min(
                    currentIndex,
                    this.notePages.length - 1
                );

            this.activeNoteId =
                this.notePages[
                    nextIndex
                ].id;

            this.loadActiveEditor();
            this.persistLocalDraft();

            this.notesRevision +=
                1;

            this.queueNotesSave(
                false,
                0
            );
        },

        persistLocalDraft() {
            try {
                window.localStorage
                    .setItem(
                        this.notesDraftKey,
                        this.notesSnapshot()
                    );
            } catch (error) {
                console.warn(
                    'Não foi possível manter o rascunho local do caderno.',
                    error
                );
            }
        },

        clearLocalDraft() {
            try {
                window.localStorage
                    .removeItem(
                        this.notesDraftKey
                    );
            } catch (error) {
                // O backend já confirmou o save; o rascunho é apenas fallback.
            }
        },

        restoreLocalDraft() {
            try {
                const raw =
                    window.localStorage
                        .getItem(
                            this.notesDraftKey
                        );

                if (!raw) {
                    return;
                }

                const draft =
                    JSON.parse(
                        raw
                    );

                if (
                    !Array.isArray(
                        draft?.pages
                    )
                    ||
                    draft.pages.length
                    ===
                    0
                ) {
                    return;
                }

                this.notePages =
                    draft.pages;

                const requestedActive =
                    String(
                        draft?.active_page_id
                        ?? ''
                    );

                this.activeNoteId =
                    this.notePages.some(
                        page =>
                            page.id
                            ===
                            requestedActive
                    )
                        ? requestedActive
                        : this.notePages[0].id;

                /*
                | O snapshot salvo continua sendo o estado vindo do servidor.
                | Assim o rascunho restaurado será reenviado automaticamente.
                */
            } catch (error) {
                console.warn(
                    'Rascunho local do caderno inválido.',
                    error
                );
            }
        },

        queueNotesSave(
            bumpRevision = true,
            delay = 1800
        ) {
            this.commitActiveEditor();

            if (bumpRevision) {
                this.notesRevision +=
                    1;
            }

            const snapshot =
                this.notesSnapshot();

            this.notesStatus =
                snapshot
                ===
                this.savedNotesSnapshot
                    ? 'saved'
                    : 'dirty';

            if (this.notesTimer) {
                window.clearTimeout(
                    this.notesTimer
                );

                this.notesTimer =
                    null;
            }

            if (
                snapshot
                ===
                this.savedNotesSnapshot
            ) {
                return;
            }

            this.notesTimer =
                window.setTimeout(
                    () => {
                        this.notesTimer =
                            null;

                        this.saveNotes();
                    },
                    Math.max(
                        0,
                        Number(delay) || 0
                    )
                );
        },

        async saveNotes() {
            this.commitActiveEditor();

            if (
                this.notesSaving
            ) {
                this.notesPendingSave =
                    true;

                return;
            }

            const currentSnapshot =
                this.notesSnapshot();

            if (
                currentSnapshot
                ===
                this.savedNotesSnapshot
            ) {
                this.notesStatus =
                    'saved';

                this.clearLocalDraft();

                return;
            }

            const revision =
                this.notesRevision;

            const pagesSnapshot =
                JSON.parse(
                    JSON.stringify(
                        this.notePages
                    )
                );

            const activePageSnapshot =
                this.activeNoteId;

            const savedSnapshot =
                JSON.stringify({
                    pages:
                        pagesSnapshot,

                    active_page_id:
                        activePageSnapshot,
                });

            this.notesSaving =
                true;

            this.notesPendingSave =
                false;

            this.notesStatus =
                'saving';

            this.notesError =
                null;

            try {
                const response =
                    await fetch(
                        this.notesUrl,
                        {
                            method:
                                'PATCH',

                            headers: {
                                'Accept':
                                    'application/json',

                                'Content-Type':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    this.csrf,
                            },

                            credentials:
                                'same-origin',

                            body:
                                JSON.stringify({
                                    pages:
                                        pagesSnapshot,

                                    active_page_id:
                                        activePageSnapshot,
                                }),
                        }
                    );

                const data =
                    await response
                        .json()
                        .catch(
                            () => ({})
                        );

                if (!response.ok) {
                    const validationMessage =
                        data?.errors
                            ? Object.values(
                                data.errors
                            )
                                .flat()
                                .filter(Boolean)[0]
                            : null;

                    throw new Error(
                        validationMessage
                        ??
                        data?.message
                        ??
                        `Falha ao salvar (${response.status}).`
                    );
                }

                /*
                | Nunca substituímos notePages pela resposta do servidor.
                | Se houve digitação durante a requisição, o texto local vence.
                */

                this.savedNotesSnapshot =
                    savedSnapshot;

                this.notesSavedRevision =
                    Math.max(
                        this.notesSavedRevision,
                        revision
                    );

                this.notesSavedAt =
                    data?.updated_at
                    ?? null;

                if (
                    this.notesSnapshot()
                    ===
                    savedSnapshot
                ) {
                    this.notesStatus =
                        'saved';

                    this.clearLocalDraft();
                } else {
                    this.notesStatus =
                        'dirty';

                    this.persistLocalDraft();
                }
            } catch (error) {
                console.error(
                    'Autosave do Caderno do Mestre:',
                    error
                );

                this.notesError =
                    error?.message
                    ?? 'Não foi possível salvar o caderno.';

                this.notesStatus =
                    'error';

                this.persistLocalDraft();
            } finally {
                this.notesSaving =
                    false;

                if (
                    this.notesPendingSave
                    ||
                    this.notesSnapshot()
                    !==
                    this.savedNotesSnapshot
                ) {
                    this.notesPendingSave =
                        false;

                    this.queueNotesSave(
                        false,
                        900
                    );
                }
            }
        },

        notesStatusLabel() {
            switch (
                this.notesStatus
            ) {
                case 'dirty':
                    return 'Aguardando...';

                case 'saving':
                    return 'Salvando...';

                case 'error':
                    return 'Não salvo';

                default:
                    return 'Salvo';
            }
        },

        get lootActiveDrop() {
            const id =
                parseInt(
                    this.lootActiveDropId
                );

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootDrops.find(
                drop =>
                    parseInt(drop?.id)
                    === id
            ) ?? null;
        },

        get lootMenuDrop() {
            const id =
                parseInt(
                    this.lootDropMenuId
                );

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootDrops.find(
                drop =>
                    parseInt(drop?.id)
                    === id
            ) ?? null;
        },

        get lootActiveShop() {
            const id =
                parseInt(
                    this.lootActiveShopId
                );

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootShops.find(
                shop =>
                    parseInt(shop?.id)
                    === id
            ) ?? null;
        },

        get shopActiveFolder() {
            const id = parseInt(this.shopActiveFolderId);

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.shopFolders.find(
                folder => parseInt(folder?.id) === id
            ) ?? null;
        },

        get shopMenuFolder() {
            const id = parseInt(this.shopFolderMenuId);

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.shopFolders.find(
                folder => parseInt(folder?.id) === id
            ) ?? null;
        },

        normalizeShopFolder(folder) {
            const color = String(folder?.color ?? '').trim();

            return {
                ...folder,
                id: parseInt(folder?.id) || null,
                position: Math.max(0, parseInt(folder?.position) || 0),
                color: /^#[0-9A-Fa-f]{6}$/.test(color)
                    ? color.toUpperCase()
                    : '#8A4B20',
                text_color: /^#[0-9A-Fa-f]{6}$/.test(String(folder?.text_color ?? '').trim())
                    ? String(folder.text_color).trim().toUpperCase()
                    : '#53150F',
                name: String(folder?.name ?? 'Pasta'),
                description: String(folder?.description ?? ''),
                content: String(folder?.content ?? ''),
            };
        },

        normalizeLootFolder(folder) {
            return this.normalizeShopFolder(folder);
        },

        lootBustImageUrl(url) {
            const value = String(url ?? '').trim();

            if (!value) {
                return null;
            }

            const hashIndex = value.indexOf('#');
            const hash = hashIndex >= 0
                ? value.slice(hashIndex)
                : '';
            const base = hashIndex >= 0
                ? value.slice(0, hashIndex)
                : value;
            const separator = base.includes('?')
                ? '&'
                : '?';

            return base
                + separator
                + '_sbv='
                + Date.now()
                + hash;
        },

        get lootActiveFolder() {
            const id = parseInt(this.lootActiveFolderId);

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootFolders.find(
                folder => parseInt(folder?.id) === id
            ) ?? null;
        },

        get lootMenuFolder() {
            const id = parseInt(this.lootFolderMenuId);

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootFolders.find(
                folder => parseInt(folder?.id) === id
            ) ?? null;
        },

        lootFolderById(folderId) {
            const id = parseInt(folderId);

            if (!Number.isFinite(id)) {
                return null;
            }

            return this.lootFolders.find(
                folder => parseInt(folder?.id) === id
            ) ?? null;
        },

        lootFolderStorageKey() {
            return (
                'spellbound:campaign:'
                + this.workspaceLootFolderScopeId
                + ':loot-folders'
            );
        },

        loadLootFolderOpenState() {
            let saved = {};

            try {
                saved = JSON.parse(
                    localStorage.getItem(
                        this.lootFolderStorageKey()
                    ) || '{}'
                ) || {};
            } catch (error) {
                saved = {};
            }

            const next = {};

            for (const folder of this.lootFolders) {
                const key = String(folder.id);
                next[key] = Object.prototype.hasOwnProperty.call(saved, key)
                    ? !!saved[key]
                    : false;
            }

            this.lootFolderOpenState = next;
        },

        saveLootFolderOpenState() {
            try {
                localStorage.setItem(
                    this.lootFolderStorageKey(),
                    JSON.stringify(this.lootFolderOpenState)
                );
            } catch (error) {
                // Storage indisponível não quebra o workspace.
            }
        },

        lootFolderIsOpen(folderId) {
            return this.lootFolderOpenState?.[String(folderId)] === true;
        },

        toggleLootFolderMenu(folder, event = null) {
            const id = parseInt(folder?.id);
            if (!Number.isFinite(id)) return;

            if (parseInt(this.lootFolderMenuId) === id) {
                this.lootFolderMenuId = null;
                return;
            }

            const trigger = event?.currentTarget ?? event?.target ?? null;
            const rect = trigger?.getBoundingClientRect?.();
            const menuWidth = 180;
            const menuHeight = 92;

            if (rect) {
                let x = rect.right + 8;
                if (x + menuWidth > window.innerWidth - 12) {
                    x = Math.max(12, rect.left - menuWidth - 8);
                }

                let y = rect.top;
                if (y + menuHeight > window.innerHeight - 12) {
                    y = Math.max(12, window.innerHeight - menuHeight - 12);
                }

                this.lootFolderMenuX = x;
                this.lootFolderMenuY = y;
            }

            this.lootDropMenuId = null;
            this.lootFolderMenuId = id;
        },

        toggleLootFolder(folderId) {
            const key = String(folderId);

            this.lootFolderOpenState = {
                ...this.lootFolderOpenState,
                [key]: !this.lootFolderIsOpen(folderId),
            };

            this.saveLootFolderOpenState();
        },

        lootFolderDrops(folderId) {
            const id = parseInt(folderId);

            return this.lootVisibleDrops()
                .filter(drop => parseInt(drop?.folder_id) === id)
                .sort((a, b) => {
                    const pa = Number.isFinite(parseInt(a?.folder_position))
                        ? parseInt(a.folder_position)
                        : 999999;
                    const pb = Number.isFinite(parseInt(b?.folder_position))
                        ? parseInt(b.folder_position)
                        : 999999;

                    if (pa !== pb) return pa - pb;

                    return String(a?.name ?? '').localeCompare(
                        String(b?.name ?? ''),
                        'pt-BR'
                    );
                });
        },

        lootUnfiledDrops() {
            return this.lootVisibleDrops()
                .filter(drop => !parseInt(drop?.folder_id));
        },

        lootFolderDropCount(folderId) {
            const id = parseInt(folderId);

            return this.lootDrops
                .filter(drop => parseInt(drop?.folder_id) === id)
                .length;
        },

        selectLootFolder(folder) {
            if (!folder?.id) return;

            this.lootActiveFolderId = parseInt(folder.id);
            this.lootActiveDropId = null;
            this.lootDropMenuId = null;
            this.lootFolderMenuId = null;

            const key = String(folder.id);
            if (!this.lootFolderIsOpen(folder.id)) {
                this.lootFolderOpenState = {
                    ...this.lootFolderOpenState,
                    [key]: true,
                };
                this.saveLootFolderOpenState();
            }
        },

        openLootFolderEditor(folder = null) {
            this.lootFolderEditingId = folder?.id ? parseInt(folder.id) : null;
            this.lootFolderName = String(folder?.name ?? '');
            this.lootFolderDescription = String(folder?.description ?? '');
            this.lootFolderContent = String(folder?.content ?? '');
            this.lootFolderColor = String(folder?.color ?? '#6B1D14');
            this.lootFolderTextColor = String(folder?.text_color ?? '#FFF8F0');
            this.lootFolderImageFile = null;
            this.lootFolderImagePreview = folder?.image_url ?? null;
            this.lootFolderRemoveImage = false;
            this.lootFolderFeedback = null;
            this.lootFolderFeedbackType = 'info';
            this.lootFolderModalOpen = true;
            this.lootFolderMenuId = null;
        },

        closeLootFolderEditor() {
            if (this.lootFolderSaving) return;

            this.lootFolderModalOpen = false;
            this.lootFolderEditingId = null;
            this.lootFolderName = '';
            this.lootFolderDescription = '';
            this.lootFolderContent = '';
            this.lootFolderColor = '#6B1D14';
            this.lootFolderTextColor = '#FFF8F0';
            this.lootFolderImageFile = null;
            this.lootFolderImagePreview = null;
            this.lootFolderRemoveImage = false;
            this.lootFolderFeedback = null;
            this.lootFolderFeedbackType = 'info';
        },

        handleLootFolderImage(event) {
            const file = event?.target?.files?.[0] ?? null;
            if (!file) return;

            this.lootFolderFeedback = null;
            this.lootFolderFeedbackType = 'info';

            if (file.size > 8 * 1024 * 1024) {
                this.lootError = 'A capa da pasta deve ter no máximo 8 MB.';
                this.lootFolderFeedback = 'A imagem precisa ter no máximo 8 MB.';
                this.lootFolderFeedbackType = 'error';
                event.target.value = '';
                return;
            }

            this.lootFolderImageFile = file;
            this.lootFolderRemoveImage = false;

            const reader = new FileReader();
            reader.onload = () => {
                this.lootFolderImagePreview = typeof reader.result === 'string'
                    ? reader.result
                    : null;

                this.lootFolderFeedback =
                    'Nova imagem selecionada. Clique em “Salvar pasta” para aplicar a alteração.';
                this.lootFolderFeedbackType = 'info';
            };
            reader.onerror = () => {
                this.lootFolderImageFile = null;
                this.lootFolderImagePreview = null;
                this.lootError = 'Não foi possível carregar a capa da pasta.';
                this.lootFolderFeedback = 'Não foi possível carregar esta imagem.';
                this.lootFolderFeedbackType = 'error';
            };
            reader.readAsDataURL(file);
        },

        removeLootFolderImage() {
            this.lootFolderImageFile = null;
            this.lootFolderImagePreview = null;
            this.lootFolderRemoveImage = true;
            this.lootFolderFeedback =
                'A imagem será removida quando você clicar em “Salvar pasta”.';
            this.lootFolderFeedbackType = 'info';
        },

        async saveLootFolder() {
            if (this.lootFolderSaving) return;

            const name = String(this.lootFolderName ?? '').trim();
            if (!name) {
                this.lootError = 'Informe um nome para a pasta.';
                this.lootFolderFeedback = 'Informe um nome para a pasta antes de salvar.';
                this.lootFolderFeedbackType = 'error';
                return;
            }

            const editingId = this.lootFolderEditingId;
            const imageChanged = !!this.lootFolderImageFile;
            const imageRemoved = !!this.lootFolderRemoveImage;
            const pendingImagePreview = this.lootFolderImagePreview;

            this.lootFolderSaving = true;
            this.lootError = null;
            this.lootFolderFeedback = 'Salvando alterações...';
            this.lootFolderFeedbackType = 'info';

            try {
                const form = new FormData();
                form.append('name', name);
                form.append('description', String(this.lootFolderDescription ?? '').trim());
                form.append('content', String(this.lootFolderContent ?? '').trim());
                form.append('color', String(this.lootFolderColor || '#6B1D14'));
                form.append('text_color', String(this.lootFolderTextColor || '#FFF8F0'));

                if (editingId === null) {
                    form.append('scope_type', this.workspaceLootFolderScopeType);
                    form.append('scope_id', String(this.workspaceLootFolderScopeId));
                } else {
                    form.append('remove_image', imageRemoved ? '1' : '0');
                }

                if (this.lootFolderImageFile) {
                    form.append('image', this.lootFolderImageFile);
                }

                const data = await this.lootJson(
                    editingId
                        ? this.workspaceFolderUpdateUrl(editingId)
                        : this.workspaceFolderStoreUrl,
                    {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: form,
                    }
                );

                const folder = this.normalizeLootFolder(data?.folder ?? {});

                if (imageRemoved) {
                    folder.image_url = null;
                } else if (imageChanged) {
                    folder.image_url = folder?.image_url
                        ? this.lootBustImageUrl(folder.image_url)
                        : pendingImagePreview;
                }

                if (editingId) {
                    const index = this.lootFolders.findIndex(
                        row => parseInt(row?.id) === parseInt(editingId)
                    );
                    if (index >= 0) this.lootFolders.splice(index, 1, folder);
                } else {
                    this.lootFolders.push(folder);
                    this.lootFolders.sort(
                        (a, b) => (parseInt(a?.position) || 0) - (parseInt(b?.position) || 0)
                    );
                }

                this.lootActiveFolderId = folder.id;
                this.lootFolderOpenState = {
                    ...this.lootFolderOpenState,
                    [String(folder.id)]: true,
                };
                this.saveLootFolderOpenState();

                if (editingId) {
                    this.lootFolderEditingId = folder.id;
                    this.lootFolderName = String(folder?.name ?? name);
                    this.lootFolderDescription = String(folder?.description ?? '');
                    this.lootFolderImageFile = null;
                    this.lootFolderImagePreview = folder?.image_url ?? null;
                    this.lootFolderRemoveImage = false;
                    this.lootFolderFeedback = imageRemoved
                        ? 'Pasta atualizada. A imagem foi removida.'
                        : (imageChanged
                            ? 'Pasta atualizada. A nova imagem já está aplicada.'
                            : 'Pasta atualizada com sucesso.');
                    this.lootFolderFeedbackType = 'success';
                } else {
                    this.closeLootFolderEditor();
                }
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível salvar a pasta.';
                this.lootFolderFeedback =
                    error?.message ?? 'Não foi possível salvar a pasta.';
                this.lootFolderFeedbackType = 'error';
            } finally {
                this.lootFolderSaving = false;
            }
        },

        async deleteLootFolder(folder) {
            const folderId = parseInt(folder?.id);
            if (!folderId) return;

            const confirmed = window.confirm(
                `Excluir a pasta “${folder?.name ?? 'Pasta'}”?\n\nOs Espólios não serão excluídos. Eles voltarão para “Sem pasta”.`
            );
            if (!confirmed) return;

            this.lootError = null;

            try {
                await this.lootJson(
                    this.workspaceFolderDeleteUrl(folderId),
                    {
                        method: 'DELETE',
                        headers: this.lootHeaders(),
                    }
                );

                this.lootFolders = this.lootFolders.filter(
                    row => parseInt(row?.id) !== folderId
                );

                this.lootDrops = this.lootDrops.map(drop =>
                    parseInt(drop?.folder_id) === folderId
                        ? { ...drop, folder_id: null, folder_position: null }
                        : drop
                );

                if (parseInt(this.lootActiveFolderId) === folderId) {
                    this.lootActiveFolderId = null;
                }

                delete this.lootFolderOpenState[String(folderId)];
                this.saveLootFolderOpenState();
                this.lootFolderMenuId = null;
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível excluir a pasta.';
            }
        },

        startLootDropDrag(drop, event) {
            const id = parseInt(drop?.id);
            if (!id) return;

            this.lootFolderDraggingDropId = id;
            this.lootFolderDropTarget = null;

            if (event?.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', String(id));
            }
        },

        endLootDropDrag() {
            this.lootFolderDraggingDropId = null;
            this.lootFolderDropTarget = null;
        },

        async dropLootIntoFolder(folderId, event = null) {
            event?.preventDefault?.();

            const dropId = parseInt(
                this.lootFolderDraggingDropId
                || event?.dataTransfer?.getData('text/plain')
            );

            if (!dropId) {
                this.endLootDropDrag();
                return;
            }

            const normalizedFolderId = folderId ? parseInt(folderId) : null;
            const drop = this.lootDrops.find(
                row => parseInt(row?.id) === dropId
            );

            if (!drop) {
                this.endLootDropDrag();
                return;
            }

            if ((parseInt(drop?.folder_id) || null) === normalizedFolderId) {
                this.endLootDropDrag();
                return;
            }

            this.lootError = null;

            try {
                const data = await this.lootJson(
                    this.workspaceFolderMoveItemUrl,
                    {
                        method: 'PATCH',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify({
                            scope_type: this.workspaceLootFolderScopeType,
                            scope_id: this.workspaceLootFolderScopeId,
                            item_type: 'loot',
                            item_id: dropId,
                            folder_id: normalizedFolderId,
                        }),
                    }
                );

                const index = this.lootDrops.findIndex(
                    row => parseInt(row?.id) === dropId
                );

                if (index >= 0) {
                    this.lootDrops.splice(index, 1, {
                        ...this.lootDrops[index],
                        folder_id: data?.folder_id ?? null,
                        folder_position: data?.position ?? null,
                    });
                }
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível mover o Espólio.';
            } finally {
                this.endLootDropDrag();
            }
        },

        openLootCreate(folderId = null) {
            this.lootNewFolderId = folderId ? parseInt(folderId) : null;
            this.lootCreateOpen = true;
            this.shopCreateOpen = false;
        },

        shopFolderStorageKey() {
            return (
                'spellbound:campaign:'
                + this.workspaceFolderScopeId
                + ':shop-folders'
            );
        },

        loadShopFolderOpenState() {
            let saved = {};

            try {
                saved = JSON.parse(
                    localStorage.getItem(
                        this.shopFolderStorageKey()
                    ) || '{}'
                ) || {};
            } catch (error) {
                saved = {};
            }

            const next = {};

            for (const folder of this.shopFolders) {
                const key = String(folder.id);

                /*
                | Pastas novas começam recolhidas.
                | Só abrimos uma pasta quando o Mestre escolhe fazê-lo.
                */
                next[key] = Object.prototype.hasOwnProperty.call(saved, key)
                    ? !!saved[key]
                    : false;
            }

            this.shopFolderOpenState = next;
        },

        saveShopFolderOpenState() {
            try {
                localStorage.setItem(
                    this.shopFolderStorageKey(),
                    JSON.stringify(this.shopFolderOpenState)
                );
            } catch (error) {
                // Storage indisponível não quebra a interface.
            }
        },

        shopFolderIsOpen(folderId) {
            return this.shopFolderOpenState?.[String(folderId)] === true;
        },

        selectShopFolder(folder) {
            const id = parseInt(folder?.id);
            if (!Number.isFinite(id)) return;

            this.shopActiveFolderId = id;
            this.lootActiveShopId = null;
            this.lootDropMenuId = null;
            this.shopFolderMenuId = null;
            this.shopHeaderMenuOpen = false;
            this.shopItemDetailOpenId = null;

            if (!this.shopFolderIsOpen(id)) {
                this.shopFolderOpenState = {
                    ...this.shopFolderOpenState,
                    [String(id)]: true,
                };
                this.saveShopFolderOpenState();
            }
        },

        toggleShopFolderMenu(folder, event = null) {
            const id = parseInt(folder?.id);
            if (!Number.isFinite(id)) return;

            if (parseInt(this.shopFolderMenuId) === id) {
                this.shopFolderMenuId = null;
                return;
            }

            const trigger = event?.currentTarget ?? event?.target ?? null;
            const rect = trigger?.getBoundingClientRect?.();
            const menuWidth = 180;
            const menuHeight = 118;

            if (rect) {
                let x = rect.right + 8;
                if (x + menuWidth > window.innerWidth - 12) {
                    x = Math.max(12, rect.left - menuWidth - 8);
                }

                let y = rect.top;
                if (y + menuHeight > window.innerHeight - 12) {
                    y = Math.max(12, window.innerHeight - menuHeight - 12);
                }

                this.shopFolderMenuX = x;
                this.shopFolderMenuY = y;
            }

            this.lootDropMenuId = null;
            this.shopFolderMenuId = id;
        },

        toggleShopFolder(folderId) {
            const key = String(folderId);
            this.shopFolderOpenState = {
                ...this.shopFolderOpenState,
                [key]: !this.shopFolderIsOpen(folderId),
            };
            this.saveShopFolderOpenState();
        },

        shopFolderShops(folderId) {
            const id = parseInt(folderId);

            return this.lootVisibleShops()
                .filter(shop => parseInt(shop?.folder_id) === id)
                .sort((a, b) => {
                    const pa = Number.isFinite(parseInt(a?.folder_position))
                        ? parseInt(a.folder_position)
                        : 999999;
                    const pb = Number.isFinite(parseInt(b?.folder_position))
                        ? parseInt(b.folder_position)
                        : 999999;

                    if (pa !== pb) return pa - pb;

                    return String(a?.name ?? '').localeCompare(
                        String(b?.name ?? ''),
                        'pt-BR'
                    );
                });
        },

        shopUnfiledShops() {
            return this.lootVisibleShops()
                .filter(shop => !parseInt(shop?.folder_id));
        },

        shopFolderShopCount(folderId) {
            const id = parseInt(folderId);
            return this.lootShops
                .filter(shop => parseInt(shop?.folder_id) === id)
                .length;
        },

        openShopFolderEditor(folder = null) {
            this.shopFolderEditingId = folder?.id ? parseInt(folder.id) : null;
            this.shopFolderName = String(folder?.name ?? '');
            this.shopFolderDescription = String(folder?.description ?? '');
            this.shopFolderContent = String(folder?.content ?? '');
            this.shopFolderColor = String(folder?.color ?? '#8A4B20');
            this.shopFolderTextColor = String(folder?.text_color ?? '#53150F');
            this.shopFolderImageFile = null;
            this.shopFolderImagePreview = folder?.image_url ?? null;
            this.shopFolderRemoveImage = false;
            this.shopFolderModalOpen = true;
            this.shopFolderMenuId = null;
        },

        closeShopFolderEditor() {
            if (this.shopFolderSaving) return;

            this.shopFolderModalOpen = false;
            this.shopFolderEditingId = null;
            this.shopFolderName = '';
            this.shopFolderDescription = '';
            this.shopFolderContent = '';
            this.shopFolderColor = '#8A4B20';
            this.shopFolderTextColor = '#53150F';
            this.shopFolderImageFile = null;
            this.shopFolderImagePreview = null;
            this.shopFolderRemoveImage = false;
        },

        handleShopFolderImage(event) {
            const file = event?.target?.files?.[0] ?? null;
            if (!file) return;

            if (file.size > 8 * 1024 * 1024) {
                this.lootError = 'A imagem da pasta deve ter no máximo 8 MB.';
                event.target.value = '';
                return;
            }

            this.shopFolderImageFile = file;
            this.shopFolderRemoveImage = false;

            const reader = new FileReader();
            reader.onload = () => {
                this.shopFolderImagePreview = typeof reader.result === 'string'
                    ? reader.result
                    : null;
            };
            reader.onerror = () => {
                this.shopFolderImageFile = null;
                this.shopFolderImagePreview = null;
                this.lootError = 'Não foi possível carregar a imagem da pasta.';
            };
            reader.readAsDataURL(file);
        },

        removeShopFolderImage() {
            this.shopFolderImageFile = null;
            this.shopFolderImagePreview = null;
            this.shopFolderRemoveImage = true;
        },

        workspaceFolderUpdateUrl(folderId) {
            return this.workspaceFolderUpdateUrlTemplate
                .replace('__FOLDER__', folderId);
        },

        workspaceFolderDeleteUrl(folderId) {
            return this.workspaceFolderDeleteUrlTemplate
                .replace('__FOLDER__', folderId);
        },

        async saveShopFolder() {
            if (this.shopFolderSaving) return;

            const name = String(this.shopFolderName ?? '').trim();
            if (!name) {
                this.lootError = 'Informe um nome para a pasta.';
                return;
            }

            this.shopFolderSaving = true;
            this.lootError = null;

            try {
                const form = new FormData();
                form.append('name', name);
                form.append('description', String(this.shopFolderDescription ?? '').trim());
                form.append('content', String(this.shopFolderContent ?? '').trim());
                form.append('color', String(this.shopFolderColor || '#8A4B20'));
                form.append('text_color', String(this.shopFolderTextColor || '#53150F'));

                if (this.shopFolderEditingId === null) {
                    form.append('scope_type', this.workspaceFolderScopeType);
                    form.append('scope_id', String(this.workspaceFolderScopeId));
                } else {
                    form.append('remove_image', this.shopFolderRemoveImage ? '1' : '0');
                }

                if (this.shopFolderImageFile) {
                    form.append('image', this.shopFolderImageFile);
                }

                const editingId = this.shopFolderEditingId;
                const data = await this.lootJson(
                    editingId
                        ? this.workspaceFolderUpdateUrl(editingId)
                        : this.workspaceFolderStoreUrl,
                    {
                        method: 'POST',
                        headers: this.lootHeaders(),
                        body: form,
                    }
                );

                const folder = this.normalizeShopFolder(data?.folder ?? {});

                if (editingId) {
                    const index = this.shopFolders.findIndex(
                        current => parseInt(current?.id) === parseInt(folder?.id)
                    );
                    if (index >= 0) this.shopFolders.splice(index, 1, folder);
                } else {
                    this.shopFolders.push(folder);
                    this.shopFolders.sort(
                        (a, b) => (parseInt(a?.position) || 0) - (parseInt(b?.position) || 0)
                    );
                    this.shopFolderOpenState = {
                        ...this.shopFolderOpenState,
                        [String(folder.id)]: false,
                    };
                    this.saveShopFolderOpenState();
                }

                this.closeShopFolderEditor();
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível salvar a pasta.';
            } finally {
                this.shopFolderSaving = false;
            }
        },

        async deleteShopFolder(folder) {
            if (!folder?.id) return;

            if (!window.confirm(
                `Excluir a pasta “${folder.name}”? As lojas não serão apagadas; elas irão para Sem pasta.`
            )) return;

            this.lootError = null;

            try {
                await this.lootJson(
                    this.workspaceFolderDeleteUrl(folder.id),
                    {
                        method: 'DELETE',
                        headers: this.lootHeaders(),
                    }
                );

                const folderId = parseInt(folder.id);
                this.shopFolders = this.shopFolders.filter(
                    current => parseInt(current?.id) !== folderId
                );

                if (parseInt(this.shopActiveFolderId) === folderId) {
                    this.shopActiveFolderId = null;
                }
                this.lootShops = this.lootShops.map(
                    shop => parseInt(shop?.folder_id) === folderId
                        ? { ...shop, folder_id: null, folder_position: null }
                        : shop
                );
                delete this.shopFolderOpenState[String(folderId)];
                this.saveShopFolderOpenState();
                this.shopFolderMenuId = null;
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível excluir a pasta.';
            }
        },

        async moveShopFolder(folder, direction) {
            const id = parseInt(folder?.id);
            if (!id || ![-1, 1].includes(direction)) return;

            const rows = [...this.shopFolders].sort(
                (a, b) => (parseInt(a?.position) || 0) - (parseInt(b?.position) || 0)
            );
            const index = rows.findIndex(row => parseInt(row?.id) === id);
            const target = index + direction;

            if (index < 0 || target < 0 || target >= rows.length) return;

            [rows[index], rows[target]] = [rows[target], rows[index]];
            rows.forEach((row, rowIndex) => row.position = rowIndex);
            this.shopFolders = rows;

            try {
                await this.lootJson(
                    this.workspaceFolderReorderUrl,
                    {
                        method: 'PATCH',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify({
                            scope_type: this.workspaceFolderScopeType,
                            scope_id: this.workspaceFolderScopeId,
                            folder_ids: rows.map(row => row.id),
                        }),
                    }
                );
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível reorganizar as pastas.';
                await this.loadLootWorkspace(true);
            }
        },

        startShopDrag(shop, event) {
            const id = parseInt(shop?.id);
            if (!id) return;

            this.shopFolderDraggingShopId = id;
            this.shopFolderDropTarget = null;

            if (event?.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', String(id));
            }
        },

        endShopDrag() {
            this.shopFolderDraggingShopId = null;
            this.shopFolderDropTarget = null;
        },

        async dropShopIntoFolder(folderId, event = null) {
            event?.preventDefault?.();

            const shopId = parseInt(
                this.shopFolderDraggingShopId
                || event?.dataTransfer?.getData('text/plain')
            );
            if (!shopId) {
                this.endShopDrag();
                return;
            }

            const normalizedFolderId = folderId ? parseInt(folderId) : null;
            const shop = this.lootShops.find(row => parseInt(row?.id) === shopId);

            if (!shop) {
                this.endShopDrag();
                return;
            }

            if ((parseInt(shop?.folder_id) || null) === normalizedFolderId) {
                this.endShopDrag();
                return;
            }

            this.lootError = null;

            try {
                const data = await this.lootJson(
                    this.workspaceFolderMoveItemUrl,
                    {
                        method: 'PATCH',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify({
                            scope_type: this.workspaceFolderScopeType,
                            scope_id: this.workspaceFolderScopeId,
                            item_type: 'shop',
                            item_id: shopId,
                            folder_id: normalizedFolderId,
                        }),
                    }
                );

                const index = this.lootShops.findIndex(
                    row => parseInt(row?.id) === shopId
                );
                if (index >= 0) {
                    this.lootShops.splice(index, 1, {
                        ...this.lootShops[index],
                        folder_id: data?.folder_id ?? null,
                        folder_position: data?.position ?? null,
                    });
                }

                /*
                | Mover uma loja não deve abrir a pasta automaticamente.
                | O estado visual continua sob controle do Mestre.
                */
            } catch (error) {
                this.lootError = error?.message ?? 'Não foi possível mover a loja.';
            } finally {
                this.endShopDrag();
            }
        },

        shopItemIsConsumable(item) {
            const rawType = String(item?.type ?? '').toLowerCase().trim();
            if (['potion', 'scroll', 'consumable'].includes(rawType)) return true;

            const features = Array.isArray(item?.properties?.features)
                ? item.properties.features
                : [];

            return features.some(feature => {
                const rawEnabled = feature?.usage?.enabled;
                const enabled = rawEnabled === true
                    || rawEnabled === 1
                    || ['1', 'true', 'on', 'yes'].includes(
                        String(rawEnabled ?? '').toLowerCase().trim()
                    );

                return enabled
                    && String(feature?.usage?.recovery ?? '').toLowerCase().trim() === 'single_use';
            });
        },

        shopStockCategoryKey(item) {
            if (this.shopItemIsConsumable(item)) return 'consumables';

            if (
                !!item?.is_magical
                || String(item?.type ?? '').toLowerCase().trim() === 'wonderful'
            ) return 'magical';

            return 'mundane';
        },

        shopStockSections() {
            const groups = { consumables: [], magical: [], mundane: [] };

            for (const item of this.shopVisibleStockItems()) {
                groups[this.shopStockCategoryKey(item)].push(item);
            }

            return [
                {
                    key: 'consumables',
                    title: 'Consumíveis',
                    description: 'Poções, pergaminhos e recursos de uso permanente.',
                    items: groups.consumables,
                },
                {
                    key: 'magical',
                    title: 'Itens Mágicos',
                    description: 'Artefatos e equipamentos encantados.',
                    items: groups.magical,
                },
                {
                    key: 'mundane',
                    title: 'Itens Mundanos',
                    description: 'Equipamentos, ferramentas e objetos comuns.',
                    items: groups.mundane,
                },
            ];
        },

        shopDefaultStockCategoryKey(shop = null) {
            const target = shop ?? this.lootActiveShop;
            const items = Array.isArray(target?.items) ? target.items : [];

            for (const key of ['consumables', 'magical', 'mundane']) {
                if (items.some(item => this.shopStockCategoryKey(item) === key)) {
                    return key;
                }
            }

            return 'mundane';
        },

        shopStockCategoryIsOpen(key) {
            return this.shopStockCategoryOpenKey === key;
        },

        toggleShopStockCategory(key) {
            this.shopStockCategoryOpenKey = this.shopStockCategoryOpenKey === key
                ? null
                : key;
        },

        lootVisibleShops() {
            const search = String(
                this.shopSearch
                ?? ''
            )
                .trim()
                .toLowerCase();

            return [...this.lootShops]
                .filter(shop => {
                    if (!search) return true;

                    return [
                        shop?.name,
                        shop?.description,
                    ]
                        .filter(Boolean)
                        .some(value =>
                            String(value)
                                .toLowerCase()
                                .includes(search)
                        );
                })
                .sort((a, b) => {
                    const aClosed = a?.status === 'closed' ? 1 : 0;
                    const bClosed = b?.status === 'closed' ? 1 : 0;

                    if (aClosed !== bClosed) {
                        return aClosed - bClosed;
                    }

                    return (parseInt(b?.id) || 0)
                        - (parseInt(a?.id) || 0);
                });
        },

        shopPriceParts(item) {
            return this.lootCoinDefinitions
                .map(coin => ({
                    ...coin,
                    amount: Math.max(
                        0,
                        parseInt(
                            item?.['price_' + coin.key]
                        ) || 0
                    ),
                }))
                .filter(coin => coin.amount > 0);
        },

        shopPriceInline(item) {
            const parts = this.shopPriceParts(item);

            return parts.length
                ? parts
                    .map(coin => `${coin.amount} ${coin.abbr}`)
                    .join(' · ')
                : 'Sem preço';
        },

        shopCopperValueInline(value) {
            let remaining = Math.max(
                0,
                parseInt(value) || 0
            );

            const definitions = [
                { value: 1000, abbr: 'PL' },
                { value: 100, abbr: 'PO' },
                { value: 50, abbr: 'PE' },
                { value: 10, abbr: 'PP' },
                { value: 1, abbr: 'PC' },
            ];

            const parts = [];

            for (const coin of definitions) {
                const amount = Math.floor(
                    remaining / coin.value
                );

                if (amount > 0) {
                    parts.push(`${amount} ${coin.abbr}`);
                    remaining -= amount * coin.value;
                }
            }

            return parts.length
                ? parts.join(' · ')
                : '0 PC';
        },

        shopSalesCount(shop) {
            return (Array.isArray(shop?.items) ? shop.items : [])
                .reduce(
                    (total, item) =>
                        total
                        +
                        (Array.isArray(item?.claims)
                            ? item.claims.filter(
                                claim =>
                                    (claim?.claim_type ?? 'loot')
                                    === 'shop'
                            ).length
                            : 0),
                    0
                );
        },

        shopItemSales(item) {
            return (Array.isArray(item?.claims) ? item.claims : [])
                .filter(
                    claim =>
                        (claim?.claim_type ?? 'loot')
                        === 'shop'
                        && !claim?.reversed_at
                );
        },

        shopItemDetailsOpen(item) {
            return (
                parseInt(this.shopItemDetailOpenId)
                ===
                parseInt(item?.id)
            );
        },

        get shopActiveItem() {
            const shop =
                this.lootActiveShop;

            const id =
                parseInt(
                    this.shopItemDetailOpenId
                );

            if (
                !shop
                ||
                !Number.isFinite(id)
            ) {
                return null;
            }

            return (
                Array.isArray(shop?.items)
                    ? shop.items
                    : []
            ).find(
                item =>
                    parseInt(item?.id)
                    ===
                    id
            ) ?? null;
        },

        selectShopItem(item) {
            const id =
                parseInt(
                    item?.id
                );

            if (!Number.isFinite(id)) {
                return;
            }

            if (
                this.shopEditingItemId !== null
                &&
                parseInt(this.shopEditingItemId)
                !==
                id
            ) {
                this.closeShopPriceEditor();
            }

            this.shopItemDetailOpenId =
                id;

            this.shopProductModalOpen =
                true;
        },

        closeShopProductModal() {
            if (
                this.shopUpdatingItemId
                !== null
            ) {
                return;
            }

            this.shopProductModalOpen =
                false;

            this.closeShopPriceEditor();
        },

        toggleShopItemDetails(item) {
            const id =
                parseInt(
                    item?.id
                );

            if (!Number.isFinite(id)) {
                return;
            }

            this.shopItemDetailOpenId =
                parseInt(this.shopItemDetailOpenId) === id
                    ? null
                    : id;
        },

        closeShopItemDetails() {
            this.shopItemDetailOpenId =
                null;

            this.closeShopPriceEditor();
        },

        shopVisibleStockItems() {
            const shop =
                this.lootActiveShop;

            const items =
                Array.isArray(shop?.items)
                    ? [...shop.items]
                    : [];

            const normalize =
                value =>
                    String(value ?? '')
                        .normalize('NFD')
                        .replace(
                            /[\u0300-\u036f]/g,
                            ''
                        )
                        .toLocaleLowerCase(
                            'pt-BR'
                        )
                        .trim();

            const query =
                normalize(
                    this.shopStockSearch
                );

            const filter =
                String(
                    this.shopStockFilter
                    ?? 'all'
                );

            return items
                .filter(item => {
                    const remaining =
                        Math.max(
                            0,
                            parseInt(
                                item?.quantity_remaining
                            ) || 0
                        );

                    if (
                        filter === 'available'
                        &&
                        remaining <= 0
                    ) {
                        return false;
                    }

                    if (
                        filter === 'soldout'
                        &&
                        remaining > 0
                    ) {
                        return false;
                    }

                    if (
                        filter === 'magical'
                        &&
                        !item?.is_magical
                    ) {
                        return false;
                    }

                    if (!query) {
                        return true;
                    }

                    const haystack =
                        normalize(
                            [
                                item?.name,
                                item?.description,
                                item?.type,
                                item?.rarity,
                                this.lootVaultClassification(
                                    item
                                ),
                                ...(
                                    Array.isArray(item?.tags)
                                        ? item.tags
                                        : []
                                ),
                            ]
                                .filter(Boolean)
                                .join(' ')
                        );

                    return haystack.includes(
                        query
                    );
                })
                .sort(
                    (a, b) => {
                        const aEmpty =
                            (
                                parseInt(
                                    a?.quantity_remaining
                                ) || 0
                            ) <= 0
                                ? 1
                                : 0;

                        const bEmpty =
                            (
                                parseInt(
                                    b?.quantity_remaining
                                ) || 0
                            ) <= 0
                                ? 1
                                : 0;

                        if (
                            aEmpty
                            !==
                            bEmpty
                        ) {
                            return aEmpty - bEmpty;
                        }

                        return String(
                            a?.name ?? ''
                        ).localeCompare(
                            String(
                                b?.name ?? ''
                            ),
                            'pt-BR'
                        );
                    }
                );
        },

        shopStockFilterCount(
            mode = 'all'
        ) {
            const items =
                Array.isArray(
                    this.lootActiveShop?.items
                )
                    ? this.lootActiveShop.items
                    : [];

            if (mode === 'available') {
                return items.filter(
                    item =>
                        (
                            parseInt(
                                item?.quantity_remaining
                            ) || 0
                        ) > 0
                ).length;
            }

            if (mode === 'soldout') {
                return items.filter(
                    item =>
                        (
                            parseInt(
                                item?.quantity_remaining
                            ) || 0
                        ) <= 0
                ).length;
            }

            if (mode === 'magical') {
                return items.filter(
                    item =>
                        !!item?.is_magical
                ).length;
            }

            return items.length;
        },

        shopSignedNumber(value) {
            if (
                value === null
                || value === undefined
                || value === ''
            ) {
                return null;
            }

            const number = Number(value);

            if (!Number.isFinite(number)) {
                return String(value);
            }

            return number > 0
                ? `+${number}`
                : String(number);
        },

        shopItemDetailStats(item) {
            const rows = [];

            const add = (label, value) => {
                if (
                    value === null
                    || value === undefined
                    || value === ''
                    || value === false
                ) {
                    return;
                }

                rows.push({
                    label,
                    value: String(value),
                });
            };

            const weight = Number(item?.weight ?? 0);

            if (Number.isFinite(weight) && weight > 0) {
                add(
                    'Peso',
                    `${Number.isInteger(weight) ? weight : weight.toFixed(2)} kg`
                );
            }

            add('Classe de Armadura', item?.armor_class);
            add('Dano', item?.damage);
            add(
                'Bônus de ataque',
                this.shopSignedNumber(item?.attack_bonus)
            );
            add(
                'Bônus de dano',
                this.shopSignedNumber(item?.damage_bonus)
            );

            if (item?.is_magical) {
                add('Natureza', 'Mágico');
            }

            if (item?.requires_attunement) {
                add('Sintonia', 'Necessária');
            }

            const weapon =
                item?.properties?.weapon
                && typeof item.properties.weapon === 'object'
                    ? item.properties.weapon
                    : null;

            if (weapon) {
                add(
                    'Tipo de arma',
                    weapon?.weapon_type
                    || weapon?.category
                );

                const damage = weapon?.damage;

                if (
                    damage
                    && typeof damage === 'object'
                    && damage?.die
                ) {
                    add(
                        'Dano base',
                        `${Math.max(1, parseInt(damage?.count) || 1)}${damage.die}`
                    );
                }

                add(
                    'Bônus mágico',
                    this.shopSignedNumber(weapon?.magic_bonus)
                );
            }

            const armor =
                item?.properties?.armor
                && typeof item.properties.armor === 'object'
                    ? item.properties.armor
                    : null;

            if (armor) {
                add(
                    'Tipo de armadura',
                    armor?.armor_type
                    || armor?.category
                );
                add('CA base', armor?.base_ac);
                add(
                    'Bônus mágico',
                    this.shopSignedNumber(armor?.magic_bonus)
                );
            }

            const shield =
                item?.properties?.shield
                && typeof item.properties.shield === 'object'
                    ? item.properties.shield
                    : null;

            if (shield) {
                add(
                    'Escudo',
                    shield?.label
                    || 'Escudo'
                );

                const shieldBonus =
                    Number(shield?.base_bonus ?? 0)
                    +
                    Number(shield?.magic_bonus ?? 0);

                if (Number.isFinite(shieldBonus) && shieldBonus !== 0) {
                    add(
                        'Bônus do escudo',
                        this.shopSignedNumber(shieldBonus) + ' CA'
                    );
                }
            }

            const abilities =
                item?.ability_bonuses
                && typeof item.ability_bonuses === 'object'
                    ? item.ability_bonuses
                    : {};

            for (const [ability, value] of Object.entries(abilities)) {
                const signed = this.shopSignedNumber(value);

                if (signed && signed !== '0') {
                    add(
                        `Bônus ${String(ability).toUpperCase()}`,
                        signed
                    );
                }
            }

            return rows;
        },

        shopItemDetailRules(item) {
            const rows = [];

            const push = (title, description) => {
                const text = String(description ?? '').trim();

                if (!text) {
                    return;
                }

                rows.push({
                    title: String(title || 'Propriedade'),
                    description: text,
                });
            };

            const weapon =
                item?.properties?.weapon
                && typeof item.properties.weapon === 'object'
                    ? item.properties.weapon
                    : null;

            for (
                const trait of
                (Array.isArray(weapon?.traits) ? weapon.traits : [])
            ) {
                push(
                    trait?.name
                    || trait?.label
                    || 'Propriedade de arma',
                    trait?.description
                    || trait?.text
                    || trait?.value
                );
            }

            for (
                const mastery of
                (Array.isArray(weapon?.masteries) ? weapon.masteries : [])
            ) {
                push(
                    mastery?.name
                    || mastery?.label
                    || 'Maestria',
                    mastery?.description
                    || mastery?.text
                    || mastery?.value
                );
            }

            const modifiers =
                Array.isArray(item?.modifiers)
                    ? item.modifiers
                    : [];

            for (const modifier of modifiers) {
                if (typeof modifier === 'string') {
                    push('Modificador', modifier);
                    continue;
                }

                if (
                    modifier
                    && typeof modifier === 'object'
                ) {
                    push(
                        modifier?.name
                        || modifier?.label
                        || 'Modificador',
                        modifier?.description
                        || modifier?.text
                        || modifier?.value
                    );
                }
            }

            const features =
                Array.isArray(
                    item?.properties?.features
                )
                    ? item.properties.features
                    : [];

            for (const feature of features) {
                push(
                    feature?.title
                    || 'Habilidade',
                    feature?.description
                );
            }

            if (item?.is_cursed) {
                push(
                    'Maldição',
                    item?.curse_description
                    || 'Este item é amaldiçoado.'
                );
            }

            return rows;
        },

        get lootCharacters() {
            const rows = [];
            const seen = new Set();

            for (const player of this.players) {
                const character = player?.character;
                const id = parseInt(character?.id);

                if (!character || !Number.isFinite(id) || seen.has(id)) {
                    continue;
                }

                seen.add(id);

                rows.push({
                    id,
                    name:
                        character?.name
                        ?? player?.player_name
                        ?? `Personagem ${id}`,
                    player_name:
                        player?.player_name
                        ?? '',
                    image_url:
                        character?.image_url
                        ?? null,
                });
            }

            return rows;
        },

        normalizeShopVaultValue(value) {
            return String(
                value
                ?? ''
            )
                .normalize('NFD')
                .replace(
                    /[\u0300-\u036f]/g,
                    ''
                )
                .toLocaleLowerCase(
                    'pt-BR'
                )
                .trim();
        },

        shopVaultItemNature(item) {
            const rawType =
                this.normalizeShopVaultValue(
                    item?.type
                );

            const magicalByType =
                [
                    'wonderful',
                    'magic',
                    'magical',
                    'item magico',
                    'item mágico',
                ].includes(
                    rawType
                );

            return (
                item?.is_magical === true
                || item?.is_magical === 1
                || item?.is_magical === '1'
                || magicalByType
            )
                ? 'magical'
                : 'mundane';
        },

        shopVaultRarityLabel(value) {
            const normalized =
                this.normalizeShopVaultValue(
                    value
                );

            const labels = {
                'common':
                    'Comum',
                'comum':
                    'Comum',
                'uncommon':
                    'Incomum',
                'incomum':
                    'Incomum',
                'rare':
                    'Raro',
                'raro':
                    'Raro',
                'very rare':
                    'Muito Raro',
                'very_rare':
                    'Muito Raro',
                'muito raro':
                    'Muito Raro',
                'legendary':
                    'Lendário',
                'lendario':
                    'Lendário',
                'lendário':
                    'Lendário',
                'artifact':
                    'Artefato',
                'artefato':
                    'Artefato',
            };

            return labels[
                normalized
            ]
                ?? String(
                    value
                    ?? ''
                ).trim();
        },

        shopVaultRarityOptions() {
            const rows =
                new Map();

            for (
                const item
                of this.vaultItems
            ) {
                const raw =
                    String(
                        item?.rarity
                        ?? ''
                    ).trim();

                if (!raw) {
                    continue;
                }

                const key =
                    this.normalizeShopVaultValue(
                        raw
                    );

                if (!rows.has(key)) {
                    rows.set(
                        key,
                        {
                            value:
                                key,
                            label:
                                this.shopVaultRarityLabel(
                                    raw
                                ),
                        }
                    );
                }
            }

            const order = [
                'comum',
                'common',
                'incomum',
                'uncommon',
                'raro',
                'rare',
                'muito raro',
                'very rare',
                'very_rare',
                'lendario',
                'lendário',
                'legendary',
                'artefato',
                'artifact',
            ];

            return [
                ...rows.values(),
            ].sort(
                (a, b) => {
                    const aIndex =
                        order.indexOf(
                            a.value
                        );

                    const bIndex =
                        order.indexOf(
                            b.value
                        );

                    if (
                        aIndex !== -1
                        || bIndex !== -1
                    ) {
                        if (aIndex === -1) {
                            return 1;
                        }

                        if (bIndex === -1) {
                            return -1;
                        }

                        if (
                            aIndex
                            !==
                            bIndex
                        ) {
                            return (
                                aIndex
                                - bIndex
                            );
                        }
                    }

                    return a.label.localeCompare(
                        b.label,
                        'pt-BR'
                    );
                }
            );
        },

        shopVaultActiveFilterCount() {
            let total = 0;

            if (
                String(
                    this.shopVaultSearch
                    ?? ''
                ).trim()
            ) {
                total++;
            }

            if (
                this.shopVaultNature
                !==
                'all'
            ) {
                total++;
            }

            if (
                this.shopVaultRarity
                !==
                'all'
            ) {
                total++;
            }

            return total;
        },

        clearShopVaultFilters() {
            this.shopVaultSearch =
                '';

            this.shopVaultNature =
                'all';

            this.shopVaultRarity =
                'all';
        },

        shopFilteredVaultItems() {
            const query =
                this.normalizeShopVaultValue(
                    this.shopVaultSearch
                );

            const nature =
                String(
                    this.shopVaultNature
                    ?? 'all'
                );

            const rarity =
                String(
                    this.shopVaultRarity
                    ?? 'all'
                );

            return this.vaultItems.filter(
                item => {
                    const searchText =
                        [
                            item?.name,
                            item?.description,
                            item?.type,
                            item?.rarity,
                            ...(
                                Array.isArray(
                                    item?.tags
                                )
                                    ? item.tags
                                    : []
                            ),
                        ]
                            .filter(Boolean)
                            .join(' ');

                    const searchMatch =
                        !query
                        ||
                        this.normalizeShopVaultValue(
                            searchText
                        ).includes(
                            query
                        );

                    const natureMatch =
                        nature === 'all'
                        ||
                        this.shopVaultItemNature(
                            item
                        )
                        === nature;

                    const itemRarity =
                        this.normalizeShopVaultValue(
                            item?.rarity
                        );

                    const rarityMatch =
                        rarity === 'all'
                        ||
                        itemRarity
                        === rarity;

                    return (
                        searchMatch
                        && natureMatch
                        && rarityMatch
                    );
                }
            );
        },

        get lootFilteredVaultItems() {
            const query =
                String(
                    this.lootVaultSearch
                    ?? ''
                )
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    )
                    .toLocaleLowerCase(
                        'pt-BR'
                    )
                    .trim();

            if (!query) {
                return this.vaultItems;
            }

            return this.vaultItems.filter(
                item => {
                    const properties =
                        item?.properties
                        && typeof item.properties === 'object'
                            ? item.properties
                            : {};

                    const haystack =
                        [
                            item?.name,
                            item?.description,
                            item?.type,
                            item?.rarity,
                            ...(Array.isArray(item?.tags)
                                ? item.tags
                                : []),
                            properties?.weapon?.weapon_type,
                            properties?.armor?.armor_type,
                        ]
                            .filter(Boolean)
                            .join(' ')
                            .normalize('NFD')
                            .replace(
                                /[\u0300-\u036f]/g,
                                ''
                            )
                            .toLocaleLowerCase(
                                'pt-BR'
                            );

                    return haystack.includes(
                        query
                    );
                }
            );
        },

        get lootFilteredQuickVaultItems() {
            const query =
                String(
                    this.lootQuickSearch
                    ?? ''
                )
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    )
                    .toLocaleLowerCase(
                        'pt-BR'
                    )
                    .trim();

            if (!query) {
                return this.vaultItems;
            }

            return this.vaultItems.filter(
                item => {
                    const properties =
                        item?.properties
                        && typeof item.properties === 'object'
                            ? item.properties
                            : {};

                    const haystack =
                        [
                            item?.name,
                            item?.description,
                            item?.type,
                            item?.rarity,
                            ...(Array.isArray(item?.tags)
                                ? item.tags
                                : []),
                            properties?.weapon?.weapon_type,
                            properties?.armor?.armor_type,
                        ]
                            .filter(Boolean)
                            .join(' ')
                            .normalize('NFD')
                            .replace(
                                /[\u0300-\u036f]/g,
                                ''
                            )
                            .toLocaleLowerCase(
                                'pt-BR'
                            );

                    return haystack.includes(
                        query
                    );
                }
            );
        },

        get lootQuickItems() {
            const items =
                Array.isArray(
                    this.lootQuickDrop?.items
                )
                    ? [
                        ...this.lootQuickDrop.items,
                    ]
                    : [];

            return items.sort(
                (a, b) =>
                    parseInt(b?.id || 0)
                    -
                    parseInt(a?.id || 0)
            );
        },

        get lootQuickAvailableItems() {
            return this.lootQuickItems
                .filter(
                    item =>
                        item?.release_status
                        ===
                        'released'
                        &&
                        parseInt(
                            item?.quantity_remaining
                            ?? 0
                        )
                        >
                        0
                );
        },

        get lootQuickHistoryItems() {
            return this.lootQuickItems;
        },

        get lootQuickCurrencies() {
            const currencies =
                Array.isArray(
                    this.lootQuickDrop?.currencies
                )
                    ? [
                        ...this.lootQuickDrop.currencies,
                    ]
                    : [];

            return currencies.sort(
                (a, b) =>
                    parseInt(b?.id || 0)
                    -
                    parseInt(a?.id || 0)
            );
        },

        lootQuickCurrencyHasRemaining(entry) {
            return this.lootCoinDefinitions
                .some(
                    coin =>
                        this.lootCoinAmount(
                            entry,
                            coin.key,
                            true
                        ) > 0
                );
        },

        get lootQuickAvailableCurrencies() {
            return this.lootQuickCurrencies
                .filter(
                    entry =>
                        entry?.release_status
                        ===
                        'released'
                        &&
                        this.lootQuickCurrencyHasRemaining(
                            entry
                        )
                );
        },

        get lootQuickHistoryCurrencies() {
            return this.lootQuickCurrencies;
        },

        get lootQuickActiveOfferCount() {
            return (
                this.lootQuickAvailableItems.length
                +
                this.lootQuickAvailableCurrencies.length
            );
        },

        get lootQuickRecentItems() {
            return this.lootQuickItems
                .slice(
                    0,
                    12
                );
        },

        lootQuickPickedQuantity(item) {
            return this.lootClaims(item)
                .filter(
                    claim =>
                        !claim?.reversed_at
                )
                .reduce(
                    (total, claim) =>
                        total
                        +
                        Math.max(
                            0,
                            parseInt(
                                claim?.quantity
                                ?? 0
                            )
                            || 0
                        ),
                    0
                );
        },

        lootQuickClaimRows(item) {
            return [
                ...this.lootClaims(item),
            ].sort(
                (a, b) =>
                    new Date(
                        b?.claimed_at
                        ?? 0
                    ).getTime()
                    -
                    new Date(
                        a?.claimed_at
                        ?? 0
                    ).getTime()
            );
        },

        lootQuickClaimCharacter(claim) {
            const current =
                this.lootClaimCurrentCharacter(
                    claim
                );

            const original =
                this.lootClaimOriginalCharacter(
                    claim
                );

            const id =
                parseInt(
                    current?.id
                    ?? original?.id
                );

            if (id) {
                const campaignCharacter =
                    this.lootCharacters.find(
                        character =>
                            parseInt(
                                character?.id
                            )
                            ===
                            id
                    );

                if (campaignCharacter) {
                    return campaignCharacter;
                }
            }

            return current
                ?? original
                ?? null;
        },

        lootQuickStatusLabel(item) {
            const remaining =
                parseInt(
                    item?.quantity_remaining
                    ?? 0
                )
                || 0;

            if (remaining <= 0) {
                return 'Esgotado';
            }

            if (
                item?.release_status
                ===
                'withdrawn'
            ) {
                return 'Recolhido';
            }

            if (
                item?.release_status
                ===
                'released'
            ) {
                return 'Disponível';
            }

            return 'Oculto';
        },

        lootQuickStatusTone(item) {
            const remaining =
                parseInt(
                    item?.quantity_remaining
                    ?? 0
                )
                || 0;

            if (remaining <= 0) {
                return 'is-depleted';
            }

            if (
                item?.release_status
                ===
                'withdrawn'
            ) {
                return 'is-withdrawn';
            }

            return item?.release_status
                ===
                'released'
                    ? 'is-live'
                    : 'is-hidden';
        },

        lootQuickCurrencyClaimRows(entry) {
            return [
                ...this.lootCurrencyClaims(
                    entry
                ),
            ].sort(
                (a, b) =>
                    new Date(
                        b?.claimed_at
                        ?? 0
                    ).getTime()
                    -
                    new Date(
                        a?.claimed_at
                        ?? 0
                    ).getTime()
            );
        },

        lootQuickCurrencyClaimCharacter(claim) {
            const id =
                parseInt(
                    claim?.character_id
                    ?? claim?.character?.id
                );

            if (id) {
                const campaignCharacter =
                    this.lootCharacters.find(
                        character =>
                            parseInt(
                                character?.id
                            )
                            ===
                            id
                    );

                if (campaignCharacter) {
                    return campaignCharacter;
                }
            }

            return claim?.character
                ?? null;
        },

        lootQuickCurrencyStatusLabel(entry) {
            if (
                !this.lootQuickCurrencyHasRemaining(
                    entry
                )
            ) {
                return 'Esgotado';
            }

            if (
                entry?.release_status
                ===
                'withdrawn'
            ) {
                return 'Recolhido';
            }

            if (
                entry?.release_status
                ===
                'released'
            ) {
                return 'Disponível';
            }

            return 'Oculto';
        },

        lootQuickCurrencyStatusTone(entry) {
            if (
                !this.lootQuickCurrencyHasRemaining(
                    entry
                )
            ) {
                return 'is-depleted';
            }

            if (
                entry?.release_status
                ===
                'withdrawn'
            ) {
                return 'is-withdrawn';
            }

            return entry?.release_status
                ===
                'released'
                    ? 'is-live'
                    : 'is-hidden';
        },

        lootQuickCurrencyDistributionLabel(entry) {
            if (
                entry?.distribution_mode
                ===
                'split'
            ) {
                return 'Dividido';
            }

            if (
                entry?.distribution_mode
                ===
                'first_claim'
            ) {
                return 'Personagem';
            }

            return 'Party';
        },

        toggleQuickCurrencyHistoryDetails(currencyId) {
            const id =
                parseInt(currencyId);

            this.lootQuickExpandedCurrencyId =
                parseInt(
                    this.lootQuickExpandedCurrencyId
                )
                ===
                id
                    ? null
                    : id;

            this.lootQuickExpandedItemId =
                null;
        },

        quickCurrencyRecipientIds(entry) {
            return (
                Array.isArray(
                    entry?.recipients
                )
                    ? entry.recipients
                    : []
            )
                .map(
                    recipient =>
                        parseInt(
                            recipient?.character_id
                            ?? recipient?.character?.id
                        )
                )
                .filter(
                    id =>
                        Number.isFinite(id)
                        && id > 0
                );
        },

        quickCurrencyRecipientSelected(characterId) {
            const id =
                parseInt(characterId);

            return this.lootQuickCurrencyCharacterIds
                .some(
                    current =>
                        parseInt(current)
                        ===
                        id
                );
        },

        toggleQuickCurrencyRecipient(characterId) {
            const id =
                parseInt(characterId);

            if (!Number.isFinite(id)) {
                return;
            }

            if (
                this.lootQuickCurrencyDistribution
                ===
                'first_claim'
            ) {
                this.lootQuickCurrencyCharacterIds =
                    [id];

                return;
            }

            const index =
                this.lootQuickCurrencyCharacterIds
                    .findIndex(
                        current =>
                            parseInt(current)
                            ===
                            id
                    );

            if (index >= 0) {
                this.lootQuickCurrencyCharacterIds =
                    this.lootQuickCurrencyCharacterIds
                        .filter(
                            current =>
                                parseInt(current)
                                !==
                                id
                        );

                return;
            }

            this.lootQuickCurrencyCharacterIds =
                [
                    ...this.lootQuickCurrencyCharacterIds,
                    id,
                ];
        },

        setQuickCurrencyDistribution(mode) {
            this.lootQuickCurrencyDistribution =
                mode;

            if (mode === 'open') {
                this.lootQuickCurrencyCharacterIds =
                    [];

                return;
            }

            if (
                mode === 'first_claim'
                &&
                this.lootQuickCurrencyCharacterIds.length > 1
            ) {
                this.lootQuickCurrencyCharacterIds =
                    [
                        this.lootQuickCurrencyCharacterIds[0],
                    ];
            }
        },

        toggleQuickHistoryDetails(itemId) {
            const id =
                parseInt(itemId);

            this.lootQuickExpandedItemId =
                parseInt(
                    this.lootQuickExpandedItemId
                )
                ===
                id
                    ? null
                    : id;

            this.lootQuickItemMenuId =
                null;
        },

        toggleQuickItemMenu(itemId) {
            const id =
                parseInt(itemId);

            this.lootQuickItemMenuId =
                parseInt(
                    this.lootQuickItemMenuId
                )
                ===
                id
                    ? null
                    : id;
        },

        quickLootRecipientIds(item) {
            return (
                Array.isArray(
                    item?.recipients
                )
                    ? item.recipients
                    : []
            )
                .map(
                    recipient =>
                        parseInt(
                            recipient?.character_id
                            ?? recipient?.character?.id
                        )
                )
                .filter(
                    id =>
                        Number.isFinite(id)
                        && id > 0
                );
        },

        lootNatureLabel(type) {
            return {
                wonderful:
                    'Item Maravilhoso',

                technological:
                    'Item Tecnológico',

                mundane:
                    'Item Mundano',
            }[type] ?? 'Item';
        },

        lootRarityLabel(rarity) {
            return {
                common:
                    'Comum',

                uncommon:
                    'Incomum',

                rare:
                    'Raro',

                very_rare:
                    'Muito Raro',

                legendary:
                    'Lendário',

                artifact:
                    'Artefato',
            }[rarity] ?? (
                rarity
                    ? String(rarity)
                    : ''
            );
        },

        lootVaultClassification(item) {
            const parts = [
                this.lootNatureLabel(
                    item?.type
                ),
            ];

            const rarity =
                this.lootRarityLabel(
                    item?.rarity
                );

            if (rarity) {
                parts.push(
                    rarity
                );
            }

            let label =
                parts.join(', ');

            if (
                item?.requires_attunement
            ) {
                label +=
                    ' (Sintonizável)';
            }

            return label;
        },

        lootAudienceLabel(entry) {
            if (entry?.visibility_mode !== 'restricted') {
                return 'Party inteira';
            }

            const names =
                (Array.isArray(entry?.recipients) ? entry.recipients : [])
                    .map(recipient => recipient?.character?.name)
                    .filter(Boolean);

            return names.length
                ? names.join(', ')
                : 'Personagens específicos';
        },

        lootDistributionLabel(mode) {
            if (mode === 'split') {
                return 'Divisão igual';
            }

            if (mode === 'open') {
                return 'Caixa livre da Party';
            }

            return 'Um personagem';
        },

        lootVisibleDrops() {
            const search = String(
                this.lootDropSearch
                ?? ''
            )
                .trim()
                .toLowerCase();

            const rows = this.lootDrops
                .filter(drop => {
                    if (
                        this.lootDropStatusFilter === 'released'
                        && drop?.status === 'closed'
                    ) {
                        return false;
                    }

                    if (
                        this.lootDropStatusFilter === 'closed'
                        && drop?.status !== 'closed'
                    ) {
                        return false;
                    }

                    if (!search) {
                        return true;
                    }

                    return [
                        drop?.name,
                        drop?.description,
                    ]
                        .filter(Boolean)
                        .some(value =>
                            String(value)
                                .toLowerCase()
                                .includes(search)
                        );
                });

            return [...rows].sort((a, b) => {
                if (this.lootDropSort === 'name') {
                    return String(a?.name ?? '')
                        .localeCompare(
                            String(b?.name ?? ''),
                            'pt-BR'
                        );
                }

                if (this.lootDropSort === 'status') {
                    const order = {
                        released: 0,
                        draft: 1,
                        closed: 2,
                    };

                    return (order[a?.status] ?? 9)
                        - (order[b?.status] ?? 9);
                }

                return (parseInt(b?.id) || 0)
                    - (parseInt(a?.id) || 0);
            });
        },

        lootDropFolder(drop) {
            const folderId =
                parseInt(drop?.folder_id);

            if (!Number.isFinite(folderId)) {
                return null;
            }

            return this.lootFolderById(folderId);
        },

        lootDropFolderName(drop) {
            return this.lootDropFolder(drop)?.name
                ?? 'Sem pasta';
        },

        lootDropCoverUrl(drop) {
            const ownImage =
                String(
                    drop?.image_url
                    ?? ''
                ).trim();

            /*
            | A capa da pasta não é usada como foto automática do Espólio.
            | Cada Espólio começa visualmente sem foto e só exibe uma imagem
            | quando o Mestre escolhe uma especificamente para ele.
            */
            return ownImage || null;
        },

        lootDropRewardCount(drop) {
            return this.lootDropItemCount(drop)
                + this.lootDropCurrencyCount(drop);
        },

        lootVisibleItems(drop) {
            if (this.lootRewardTypeFilter === 'coins') {
                return [];
            }

            const search = String(
                this.lootItemSearch
                ?? ''
            )
                .trim()
                .toLowerCase();

            return (Array.isArray(drop?.items) ? drop.items : [])
                .filter(item => {
                    if (
                        this.lootItemStatusFilter !== 'all'
                        && item?.release_status !== this.lootItemStatusFilter
                    ) {
                        return false;
                    }

                    if (!search) {
                        return true;
                    }

                    return [
                        item?.name,
                        item?.description,
                        item?.type,
                        item?.rarity,
                    ]
                        .filter(Boolean)
                        .some(value =>
                            String(value)
                                .toLowerCase()
                                .includes(search)
                        );
                });
        },

        lootVisibleCurrencies(drop) {
            if (this.lootRewardTypeFilter === 'items') {
                return [];
            }

            const search = String(
                this.lootItemSearch
                ?? ''
            )
                .trim()
                .toLowerCase();

            return (Array.isArray(drop?.currencies) ? drop.currencies : [])
                .filter(entry => {
                    if (
                        this.lootItemStatusFilter !== 'all'
                        && entry?.release_status !== this.lootItemStatusFilter
                    ) {
                        return false;
                    }

                    if (!search) {
                        return true;
                    }

                    return [
                        entry?.label,
                        'moedas',
                        'tesouro',
                        this.lootCurrencyInline(entry, false),
                        this.lootCurrencyInline(entry, true),
                    ]
                        .filter(Boolean)
                        .some(value =>
                            String(value)
                                .toLowerCase()
                                .includes(search)
                        );
                });
        },

        lootVisibleRewardCount(drop) {
            return this.lootVisibleItems(drop).length
                + this.lootVisibleCurrencies(drop).length;
        },

        lootDropItemCount(drop) {
            return Array.isArray(drop?.items)
                ? drop.items.length
                : 0;
        },

        lootDropCurrencyCount(drop) {
            return Array.isArray(drop?.currencies)
                ? drop.currencies.length
                : 0;
        },

        lootDropClaimCount(drop) {
            const itemClaims =
                (Array.isArray(drop?.items) ? drop.items : [])
                    .reduce(
                        (total, item) =>
                            total
                            +
                            (Array.isArray(item?.claims)
                                ? item.claims.length
                                : 0),
                        0
                    );

            const currencyClaims =
                (Array.isArray(drop?.currencies) ? drop.currencies : [])
                    .reduce(
                        (total, entry) =>
                            total
                            +
                            (Array.isArray(entry?.claims)
                                ? entry.claims.length
                                : 0),
                        0
                    );

            return itemClaims + currencyClaims;
        },

        lootCurrencyClaims(entry) {
            return Array.isArray(entry?.claims)
                ? entry.claims
                : [];
        },

        lootCurrencyClaimInline(claim) {
            const parts = this.lootCoinDefinitions
                .map(coin => ({
                    ...coin,
                    amount: Math.max(
                        0,
                        parseInt(claim?.[coin.key]) || 0
                    ),
                }))
                .filter(coin => coin.amount > 0);

            return parts.length
                ? parts
                    .map(coin => `${coin.amount} ${coin.abbr}`)
                    .join(' · ')
                : '0';
        },

        lootCoinAmount(entry, coin, remaining = false) {
            const suffix = remaining ? '_remaining' : '_total';

            return Math.max(
                0,
                parseInt(entry?.[coin + suffix]) || 0
            );
        },

        lootCurrencyParts(entry, remaining = false) {
            return this.lootCoinDefinitions
                .map(coin => ({
                    ...coin,
                    amount:
                        this.lootCoinAmount(
                            entry,
                            coin.key,
                            remaining
                        ),
                }))
                .filter(coin => coin.amount > 0);
        },

        lootCurrencyInline(entry, remaining = false) {
            const parts =
                this.lootCurrencyParts(
                    entry,
                    remaining
                );

            return parts.length
                ? parts
                    .map(
                        coin =>
                            `${coin.amount} ${coin.abbr}`
                    )
                    .join(' · ')
                : '0';
        },

        lootCurrencyRecipientInline(recipient) {
            const parts =
                this.lootCoinDefinitions
                    .map(coin => ({
                        ...coin,
                        amount:
                            Math.max(
                                0,
                                parseInt(
                                    recipient?.[
                                        coin.key + '_allocated'
                                    ]
                                ) || 0
                            ),
                    }))
                    .filter(coin => coin.amount > 0);

            return parts.length
                ? parts
                    .map(
                        coin =>
                            `${coin.amount} ${coin.abbr}`
                    )
                    .join(' · ')
                : '';
        },

        lootDropStatusLabel(status, closedReason = null) {
            if (status === 'closed') {
                return closedReason === 'depleted'
                    ? 'Concluído'
                    : 'Encerrado';
            }

            return {
                draft:
                    'Preparado',

                released:
                    'Em circulação',
            }[status] ?? 'Espólio';
        },

        lootItemStatusLabel(status) {
            return {
                hidden:
                    'Oculto dos Players',

                released:
                    'Liberado',

                withdrawn:
                    'Recolhido',
            }[status] ?? 'Oculto';
        },

        lootStatusClass(status) {
            if (
                status
                ===
                'released'
            ) {
                return (
                    'border-emerald-300/65 '
                    + 'bg-emerald-50 '
                    + 'text-emerald-800'
                );
            }

            if (
                status
                ===
                'withdrawn'
                ||
                status
                ===
                'closed'
            ) {
                return (
                    'border-[#cdbb9f]/65 '
                    + 'bg-[#efe9dc] '
                    + 'text-[#7d604d]'
                );
            }

            return (
                'border-amber-300/65 '
                + 'bg-amber-50 '
                + 'text-amber-800'
            );
        },

        lootHeaders(
            json = false
        ) {
            const headers = {
                'Accept':
                    'application/json',

                'X-Requested-With':
                    'XMLHttpRequest',

                'X-CSRF-TOKEN':
                    this.csrf,
            };

            if (json) {
                headers[
                    'Content-Type'
                ] =
                    'application/json';
            }

            return headers;
        },

        async lootJson(
            url,
            options = {}
        ) {
            const response =
                await fetch(
                    url,
                    options
                );

            const raw =
                await response.text();

            let data = {};

            if (raw) {
                try {
                    data =
                        JSON.parse(raw);
                } catch (error) {
                    data = {};
                }
            }

            if (!response.ok) {
                const errors =
                    data?.errors
                        ? Object
                            .values(
                                data.errors
                            )
                            .flat()
                            .filter(Boolean)
                        : [];

                const message =
                    response.status === 413
                        ? (
                            'O arquivo enviado é maior do que o servidor aceita. '
                            + 'Escolha uma imagem mais leve ou aumente '
                            + 'upload_max_filesize e post_max_size no PHP.'
                        )
                        : (
                            errors.length
                                ? errors.join(' ')
                                : (
                                    data?.message
                                    ?? data?.error
                                    ?? (
                                        response.status >= 500
                                            ? (
                                                'Erro interno do servidor (HTTP '
                                                + response.status
                                                + '). Veja storage/logs/laravel.log.'
                                            )
                                            : (
                                                'Falha na requisição (HTTP '
                                                + response.status
                                                + ').'
                                            )
                                    )
                                )
                        );

                console.error(
                    '[Spellbound / Master Shield]',
                    {
                        url,
                        status:
                            response.status,
                        response:
                            raw,
                    }
                );

                throw new Error(
                    message
                );
            }

            return data;
        },

        normalizeLootDrop(drop) {
            const counterColor =
                String(drop?.shop_counter_color ?? '').trim();

            const backgroundColor =
                String(drop?.shop_background_color ?? '').trim();

            return {
                ...drop,

                shop_has_counter:
                    !(
                        drop?.shop_has_counter === false
                        || drop?.shop_has_counter === 0
                        || String(drop?.shop_has_counter ?? '1') === '0'
                    ),

                shop_counter_color:
                    /^#[0-9A-Fa-f]{6}$/.test(counterColor)
                        ? counterColor.toUpperCase()
                        : '#6B3A24',

                shop_background_color:
                    /^#[0-9A-Fa-f]{6}$/.test(backgroundColor)
                        ? backgroundColor.toUpperCase()
                        : '#6F4A33',

                shop_attendant_name:
                    String(drop?.shop_attendant_name ?? ''),

                shop_image_zoom:
                    Math.max(
                        70,
                        Math.min(
                            180,
                            parseInt(drop?.shop_image_zoom) || 100
                        )
                    ),

                shop_image_position:
                    ['top', 'center', 'bottom'].includes(
                        String(drop?.shop_image_position ?? '')
                    )
                        ? String(drop.shop_image_position)
                        : 'center',

                shop_background_zoom:
                    Math.max(
                        100,
                        Math.min(
                            180,
                            parseInt(drop?.shop_background_zoom) || 100
                        )
                    ),

                shop_background_position:
                    ['top', 'center', 'bottom'].includes(
                        String(drop?.shop_background_position ?? '')
                    )
                        ? String(drop.shop_background_position)
                        : 'center',

                items:
                    Array.isArray(
                        drop?.items
                    )
                        ? drop.items
                        : [],

                currencies:
                    Array.isArray(
                        drop?.currencies
                    )
                        ? drop.currencies
                        : [],
            };
        },

        chooseDefaultLootDrop() {
            if (this.lootActiveFolderId) {
                this.lootActiveDropId = null;
                return;
            }

            if (this.lootDropStatusFilter === 'all') {
                if (this.lootActiveDrop) {
                    return;
                }

                const anyDrop =
                    this.lootDrops.find(
                        drop => drop?.status !== 'closed'
                    )
                    ?? this.lootDrops[0]
                    ?? null;

                this.lootActiveDropId =
                    anyDrop?.id
                    ?? null;

                return;
            }

            if (
                this.lootActiveDrop
                && (
                    (
                        this.lootDropStatusFilter === 'released'
                        && this.lootActiveDrop?.status !== 'closed'
                    )
                    ||
                    (
                        this.lootDropStatusFilter === 'closed'
                        && this.lootActiveDrop?.status === 'closed'
                    )
                )
            ) {
                return;
            }

            const preferred =
                this.lootDrops.find(
                    drop =>
                        this.lootDropStatusFilter === 'released'
                            ? drop?.status !== 'closed'
                            : drop?.status === 'closed'
                )
                ?? null;

            this.lootActiveDropId =
                preferred?.id
                ?? null;
        },

        chooseDefaultShop() {
            /*
            | Lojas seguem a mesma navegação explícita dos Espólios.
            | Nada abre automaticamente ao entrar no workspace.
            | Mantemos apenas uma seleção que ainda exista após um refresh.
            */
            const activeShopStillExists =
                this.lootActiveShopId
                && this.lootShops.some(
                    shop => parseInt(shop?.id) === parseInt(this.lootActiveShopId)
                );

            const activeFolderStillExists =
                this.shopActiveFolderId
                && this.shopFolders.some(
                    folder => parseInt(folder?.id) === parseInt(this.shopActiveFolderId)
                );

            if (activeShopStillExists) {
                this.shopActiveFolderId = null;
            } else if (activeFolderStillExists) {
                this.lootActiveShopId = null;
            } else {
                this.lootActiveShopId = null;
                this.shopActiveFolderId = null;
            }

            this.shopStockCategoryOpenKey = null;
        },

        async loadLootWorkspace(
            force = false
        ) {
            if (
                this.lootLoading
            ) {
                return;
            }

            if (
                this.lootLoaded
                &&
                !force
            ) {
                return;
            }

            this.lootLoading =
                true;

            this.lootError =
                null;

            try {
                const [
                    vaultResponse,
                    lootResponse,
                ] =
                    await Promise.all([
                        this.lootJson(
                            this.vaultItemsUrl,
                            {
                                headers:
                                    this.lootHeaders(),
                            }
                        ),

                        this.lootJson(
                            this.lootIndexUrl,
                            {
                                headers:
                                    this.lootHeaders(),
                            }
                        ),
                    ]);

                this.vaultItems =
                    Array.isArray(
                        vaultResponse?.items
                    )
                        ? vaultResponse.items
                        : [];

                this.lootDrops =
                    Array.isArray(
                        lootResponse?.drops
                    )
                        ? lootResponse.drops.map(
                            drop =>
                                this.normalizeLootDrop(
                                    drop
                                )
                        )
                        : [];

                this.lootFolders =
                    Array.isArray(
                        lootResponse?.loot_folders
                    )
                        ? lootResponse.loot_folders
                            .map(
                                folder =>
                                    this.normalizeLootFolder(
                                        folder
                                    )
                            )
                            .sort(
                                (a, b) =>
                                    (parseInt(a?.position) || 0)
                                    -
                                    (parseInt(b?.position) || 0)
                            )
                        : [];

                this.loadLootFolderOpenState();

                this.lootQuickDrop =
                    lootResponse?.quick_drop
                        ? this.normalizeLootDrop(
                            lootResponse.quick_drop
                        )
                        : null;

                this.lootShops =
                    Array.isArray(
                        lootResponse?.shops
                    )
                        ? lootResponse.shops.map(
                            shop =>
                                this.normalizeLootDrop(
                                    shop
                                )
                        )
                        : [];

                this.shopFolderSystemReady =
                    lootResponse?.folder_system_ready
                    !== false;

                this.shopFolderSystemMessage =
                    lootResponse?.folder_system_message
                    ?? null;

                this.shopFolders =
                    Array.isArray(
                        lootResponse?.shop_folders
                    )
                        ? lootResponse.shop_folders
                            .map(
                                folder =>
                                    this.normalizeShopFolder(
                                        folder
                                    )
                            )
                            .sort(
                                (a, b) =>
                                    (parseInt(a?.position) || 0)
                                    -
                                    (parseInt(b?.position) || 0)
                            )
                        : [];

                this.loadShopFolderOpenState();

                /*
                | A navegação de Espólios possui dois níveis exclusivos:
                | pasta OU Espólio. Se um Espólio está aberto, ele sempre
                | tem prioridade sobre a pasta onde está armazenado.
                |
                | Antes, lootActiveFolderId permanecia preenchido ao abrir
                | um Espólio. No próximo refresh, este bloco interpretava
                | isso como a pasta ainda ativa e zerava o Espólio,
                | fazendo a tela voltar automaticamente para a pasta.
                */
                const activeLootDropStillExists =
                    this.lootActiveDropId
                    && this.lootDrops.some(
                        drop =>
                            parseInt(drop?.id)
                            === parseInt(this.lootActiveDropId)
                    );

                const activeLootFolderStillExists =
                    this.lootActiveFolderId
                    && this.lootFolders.some(
                        folder =>
                            parseInt(folder?.id)
                            === parseInt(this.lootActiveFolderId)
                    );

                if (activeLootDropStillExists) {
                    this.lootActiveFolderId = null;
                } else if (activeLootFolderStillExists) {
                    this.lootActiveDropId = null;
                } else {
                    /*
                    | Ao entrar em Espólios, não abrimos nada automaticamente.
                    | O Mestre começa pela visão geral e escolhe conscientemente
                    | a pasta ou o Espólio que deseja administrar.
                    */
                    this.lootActiveDropId = null;
                    this.lootActiveFolderId = null;
                }

                this.shopkeepers =
                    Array.isArray(lootResponse?.shopkeepers)
                        ? lootResponse.shopkeepers
                        : [];

                this.chooseDefaultShop();

                this.lootAutoRefreshAt =
                    Date.now();

                this.lootLoaded =
                    true;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível carregar os Espólios.';
            } finally {
                this.lootLoading =
                    false;
            }
        },

        toggleLootDropMenu(drop, event = null) {
            if (
                this.drawer !== 'loot'
                || this.lootWorkspaceMode !== 'loot'
            ) {
                this.lootDropMenuId = null;
                return;
            }

            const dropId =
                parseInt(drop?.id);

            if (!Number.isFinite(dropId)) {
                return;
            }

            if (
                parseInt(this.lootDropMenuId)
                === dropId
            ) {
                this.lootDropMenuId = null;
                return;
            }

            const button =
                event?.currentTarget
                ?? null;

            if (button?.getBoundingClientRect) {
                const rect =
                    button.getBoundingClientRect();

                const menuWidth =
                    174;

                const menuHeight =
                    112;

                const viewportWidth =
                    window.innerWidth
                    || document.documentElement.clientWidth
                    || 1024;

                const viewportHeight =
                    window.innerHeight
                    || document.documentElement.clientHeight
                    || 768;

                this.lootDropMenuLeft =
                    Math.max(
                        10,
                        Math.min(
                            rect.right - menuWidth,
                            viewportWidth - menuWidth - 10
                        )
                    );

                const opensUp =
                    rect.bottom + 7 + menuHeight
                    > viewportHeight - 10;

                this.lootDropMenuTop =
                    opensUp
                        ? Math.max(
                            10,
                            rect.top - menuHeight - 7
                        )
                        : rect.bottom + 7;
            }

            this.lootFolderMenuId = null;
            this.lootDropMenuId = dropId;
        },

        lootDropMenuStyle() {
            return [
                `top:${Math.round(this.lootDropMenuTop || 0)}px`,
                `left:${Math.round(this.lootDropMenuLeft || 0)}px`,
            ].join(';');
        },

        selectLootDrop(drop) {
            if (!drop?.id) {
                return;
            }

            /*
            | Entrar no Espólio fecha a visão da pasta. O folder_id
            | continua disponível dentro do próprio drop para sabermos
            | de onde ele veio, sem manter os dois estados ativos.
            */
            this.lootActiveDropId =
                parseInt(drop.id);

            this.lootActiveFolderId =
                null;

            this.lootDropMenuId =
                null;

            this.lootFolderMenuId =
                null;

            this.lootRewardMenuOpen =
                false;

            this.lootCurrencyFormOpen =
                false;
        },

        lootPresentationUpdateUrl(dropId) {
            return this.lootPresentationUpdateUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                );
        },

        openLootDropEditor(drop = null) {
            const target =
                drop?.id
                    ? drop
                    : this.lootActiveDrop;

            if (!target?.id) {
                return;
            }

            this.lootDropEditName =
                String(target?.name ?? '');

            this.lootDropEditDescription =
                String(target?.description ?? '');

            this.lootDropImageFile =
                null;

            this.lootDropImagePreview =
                target?.image_url
                    ?? null;

            this.lootDropRemoveImage =
                false;

            this.lootDropFeedback =
                null;

            this.lootDropFeedbackType =
                'info';

            this.lootDropEditorOpen =
                true;

            this.lootRewardMenuOpen =
                false;
        },

        closeLootDropEditor() {
            if (this.lootDropSaving) {
                return;
            }

            this.lootDropEditorOpen =
                false;

            this.lootDropEditName =
                '';

            this.lootDropEditDescription =
                '';

            this.lootDropImageFile =
                null;

            this.lootDropImagePreview =
                null;

            this.lootDropRemoveImage =
                false;

            this.lootDropFeedback =
                null;

            this.lootDropFeedbackType =
                'info';
        },

        handleLootDropImage(event) {
            const file =
                event?.target?.files?.[0]
                ?? null;

            if (!file) {
                return;
            }

            this.lootDropFeedback =
                null;

            this.lootDropFeedbackType =
                'info';

            if (file.size > 8 * 1024 * 1024) {
                this.lootError =
                    'A foto do Espólio deve ter no máximo 8 MB.';

                this.lootDropFeedback =
                    'A imagem precisa ter no máximo 8 MB.';

                this.lootDropFeedbackType =
                    'error';

                event.target.value =
                    '';

                return;
            }

            this.lootDropImageFile =
                file;

            this.lootDropRemoveImage =
                false;

            const reader =
                new FileReader();

            reader.onload = () => {
                this.lootDropImagePreview =
                    typeof reader.result === 'string'
                        ? reader.result
                        : null;

                this.lootDropFeedback =
                    'Nova foto selecionada. Clique em “Salvar alterações” para aplicar.';

                this.lootDropFeedbackType =
                    'info';
            };

            reader.onerror = () => {
                this.lootDropImageFile =
                    null;

                this.lootDropImagePreview =
                    null;

                this.lootError =
                    'Não foi possível carregar a foto do Espólio.';

                this.lootDropFeedback =
                    'Não foi possível carregar esta imagem.';

                this.lootDropFeedbackType =
                    'error';
            };

            reader.readAsDataURL(file);
        },

        removeLootDropImage() {
            this.lootDropImageFile =
                null;

            this.lootDropImagePreview =
                null;

            this.lootDropRemoveImage =
                true;

            this.lootDropFeedback =
                'A foto será removida quando você clicar em “Salvar alterações”.';

            this.lootDropFeedbackType =
                'info';
        },

        async saveLootDropPresentation() {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                || this.lootDropSaving
            ) {
                return;
            }

            const name =
                String(
                    this.lootDropEditName
                    ?? ''
                ).trim();

            if (!name) {
                this.lootError =
                    'Informe um nome para o Espólio.';

                this.lootDropFeedback =
                    'Informe um nome para o Espólio antes de salvar.';

                this.lootDropFeedbackType =
                    'error';

                return;
            }

            const imageChanged =
                !!this.lootDropImageFile;

            const imageRemoved =
                !!this.lootDropRemoveImage;

            const pendingImagePreview =
                this.lootDropImagePreview;

            this.lootDropSaving =
                true;

            this.lootError =
                null;

            this.lootDropFeedback =
                'Salvando alterações...';

            this.lootDropFeedbackType =
                'info';

            try {
                const form =
                    new FormData();

                form.append(
                    'name',
                    name
                );

                form.append(
                    'description',
                    String(
                        this.lootDropEditDescription
                        ?? ''
                    ).trim()
                );

                form.append(
                    'remove_image',
                    imageRemoved
                        ? '1'
                        : '0'
                );

                if (this.lootDropImageFile) {
                    form.append(
                        'image',
                        this.lootDropImageFile
                    );
                }

                const data =
                    await this.lootJson(
                        this.lootPresentationUpdateUrl(
                            drop.id
                        ),
                        {
                            method:
                                'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    this.csrf,
                            },

                            body:
                                form,
                        }
                    );

                const updated =
                    this.normalizeLootDrop(
                        data?.drop
                        ?? {}
                    );

                if (imageRemoved) {
                    updated.image_url =
                        null;

                    updated.has_custom_image =
                        false;
                } else if (imageChanged) {
                    updated.image_url =
                        updated?.image_url
                            ? this.lootBustImageUrl(
                                updated.image_url
                            )
                            : pendingImagePreview;

                    updated.has_custom_image =
                        true;
                }

                const index =
                    this.lootDrops.findIndex(
                        row =>
                            parseInt(row?.id)
                            === parseInt(updated?.id)
                    );

                if (index >= 0) {
                    this.lootDrops.splice(
                        index,
                        1,
                        updated
                    );
                }

                this.lootActiveDropId =
                    parseInt(updated.id);

                this.lootDropEditName =
                    String(updated?.name ?? name);

                this.lootDropEditDescription =
                    String(updated?.description ?? '');

                this.lootDropImageFile =
                    null;

                this.lootDropImagePreview =
                    updated?.image_url
                    ?? null;

                this.lootDropRemoveImage =
                    false;

                this.lootDropFeedback =
                    imageRemoved
                        ? 'Espólio atualizado. A foto foi removida.'
                        : (imageChanged
                            ? 'Espólio atualizado. A nova foto já está aplicada.'
                            : 'Espólio atualizado com sucesso.');

                this.lootDropFeedbackType =
                    'success';
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível atualizar o Espólio.';

                this.lootDropFeedback =
                    error?.message
                    ?? 'Não foi possível atualizar o Espólio.';

                this.lootDropFeedbackType =
                    'error';
            } finally {
                this.lootDropSaving =
                    false;
            }
        },

        async createLootDrop() {
            if (
                this.lootCreating
            ) {
                return;
            }

            const name =
                String(
                    this.lootNewDropName
                    ?? ''
                ).trim();

            if (!name) {
                this.lootError =
                    'Informe um nome para o Espólio.';

                return;
            }

            this.lootCreating =
                true;

            this.lootError =
                null;

            try {
                const data =
                    await this.lootJson(
                        this.lootStoreUrl,
                        {
                            method:
                                'POST',

                            headers:
                                this.lootHeaders(
                                    true
                                ),

                            body:
                                JSON.stringify({
                                    name,

                                    description:
                                        String(
                                            this.lootNewDropDescription
                                            ?? ''
                                        ).trim()
                                        || null,

                                    folder_id:
                                        this.lootNewFolderId
                                            ? parseInt(this.lootNewFolderId)
                                            : null,
                                }),
                        }
                    );

                const drop =
                    this.normalizeLootDrop(
                        data?.drop
                        ?? {}
                    );

                this.lootDrops.unshift(
                    drop
                );

                this.lootActiveDropId =
                    parseInt(drop.id);

                /*
                | O novo Espólio pode ter sido criado dentro de uma pasta,
                | mas ao abrir sua ficha a visualização ativa passa a ser
                | exclusivamente a do Espólio.
                */
                this.lootActiveFolderId =
                    null;

                this.lootNewDropName =
                    '';

                this.lootNewDropDescription =
                    '';

                this.lootNewFolderId =
                    null;

                this.lootCreateOpen =
                    false;

                this.lootPickerOpen =
                    true;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível criar o Espólio.';
            } finally {
                this.lootCreating =
                    false;
            }
        },

        selectShop(shop) {
            if (!shop?.id) {
                return;
            }

            this.shopActiveFolderId = null;
            this.shopFolderMenuId = null;

            this.lootActiveShopId =
                shop.id;

            this.shopItemDetailOpenId =
                null;

            this.shopProductModalOpen =
                false;

            this.shopContentTab =
                'stock';

            this.shopStockSearch =
                '';

            this.shopStockFilter =
                'all';

            /*
            | Ao trocar de loja, todas as categorias começam recolhidas.
            */
            this.shopStockCategoryOpenKey =
                null;

            this.shopHeaderMenuOpen =
                false;

            this.closeShopPriceEditor();
        },

        shopkeeperById(id) {
            const parsed = parseInt(id);

            if (!parsed) {
                return null;
            }

            return this.shopkeepers.find(
                npc => parseInt(npc?.id) === parsed
            ) ?? null;
        },

        shopkeeperPreviewUrl(id) {
            return this.shopkeeperById(id)?.image_url ?? null;
        },

        shopNewSelectedShopkeeper() {
            return this.shopkeeperById(
                this.shopNewShopkeeperNpcId
            );
        },

        shopNewFilteredShopkeepers() {
            const query = String(
                this.shopNewShopkeeperSearch
                ?? ''
            )
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLocaleLowerCase('pt-BR')
                .trim();

            if (!query) {
                return this.shopkeepers;
            }

            return this.shopkeepers.filter(npc =>
                String(npc?.name ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLocaleLowerCase('pt-BR')
                    .includes(query)
            );
        },

        shopNewPortraitPreviewUrl() {
            return this.shopNewImagePreview
                || this.shopkeeperPreviewUrl(this.shopNewShopkeeperNpcId)
                || '';
        },

        shopNewAttendantDisplayName() {
            return this.shopNewSelectedShopkeeper()?.name
                || String(this.shopNewAttendantName ?? '').trim()
                || 'Sem atendente definido';
        },

        shopEditSelectedShopkeeper() {
            return this.shopkeeperById(
                this.shopEditShopkeeperNpcId
            );
        },

        shopEditFilteredShopkeepers() {
            const query = String(
                this.shopEditShopkeeperSearch
                ?? ''
            )
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLocaleLowerCase('pt-BR')
                .trim();

            if (!query) {
                return this.shopkeepers;
            }

            return this.shopkeepers.filter(npc =>
                String(npc?.name ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLocaleLowerCase('pt-BR')
                    .includes(query)
            );
        },

        shopEditPortraitPreviewUrl() {
            return this.shopEditImagePreview
                || this.shopkeeperPreviewUrl(this.shopEditShopkeeperNpcId)
                || '';
        },

        shopEditAttendantDisplayName() {
            return this.shopEditSelectedShopkeeper()?.name
                || String(this.shopEditAttendantName ?? '').trim()
                || 'Sem atendente definido';
        },

        formatShopFileSize(bytes) {
            const value =
                Math.max(
                    0,
                    Number(bytes) || 0
                );

            if (value < 1024) {
                return Math.round(value) + ' B';
            }

            if (value < 1024 * 1024) {
                return (
                    (value / 1024).toFixed(1)
                    + ' KB'
                );
            }

            return (
                (value / (1024 * 1024)).toFixed(1)
                + ' MB'
            );
        },

        shopUploadFeedbackStyle(type) {
            if (type === 'error') {
                return {
                    borderColor: '#F1B7AE',
                    background: '#FFF1EF',
                    color: '#9F1D16',
                };
            }

            if (type === 'warning') {
                return {
                    borderColor: '#E6C98E',
                    background: '#FFF8E8',
                    color: '#8B5D16',
                };
            }

            if (type === 'success') {
                return {
                    borderColor: '#B9D9C7',
                    background: '#EFFAF3',
                    color: '#21623D',
                };
            }

            return {
                borderColor: '#D8C7AB',
                background: '#F8F3EC',
                color: '#76553F',
            };
        },

        inspectShopImageFile(file) {
            return new Promise(
                (resolve, reject) => {
                    const url =
                        URL.createObjectURL(file);

                    const image =
                        new Image();

                    image.onload = () => {
                        const result = {
                            width:
                                image.naturalWidth
                                || image.width
                                || 0,

                            height:
                                image.naturalHeight
                                || image.height
                                || 0,
                        };

                        URL.revokeObjectURL(url);
                        resolve(result);
                    };

                    image.onerror = () => {
                        URL.revokeObjectURL(url);
                        reject(
                            new Error(
                                'Não foi possível ler as dimensões da imagem.'
                            )
                        );
                    };

                    image.src = url;
                }
            );
        },

        async validateShopUploadFile(
            file,
            kind = 'background'
        ) {
            const isPortrait =
                kind === 'portrait';

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (
                !file
                || !allowedTypes.includes(
                    String(file.type || '').toLowerCase()
                )
            ) {
                return {
                    ok: false,
                    type: 'error',
                    message:
                        'Formato não suportado. Use JPG, PNG ou WEBP.',
                };
            }

            const maxBytes =
                isPortrait
                    ? this.shopPortraitUploadLimitBytes
                    : this.shopBackgroundUploadLimitBytes;

            if (
                maxBytes > 0
                && file.size > maxBytes
            ) {
                const serverLimit =
                    this.formatShopFileSize(
                        this.shopServerUploadLimitBytes
                    );

                return {
                    ok: false,
                    type: 'error',
                    message:
                        'Imagem muito pesada: '
                        + this.formatShopFileSize(file.size)
                        + '. O limite seguro desta instalação é '
                        + this.formatShopFileSize(maxBytes)
                        + ' por arquivo'
                        + (
                            this.shopServerUploadLimitBytes > 0
                                ? ' (limite do PHP: ' + serverLimit + ').'
                                : '.'
                        ),
                };
            }

            let dimensions;

            try {
                dimensions =
                    await this.inspectShopImageFile(file);
            } catch (error) {
                return {
                    ok: false,
                    type: 'error',
                    message:
                        error?.message
                        || 'Não foi possível analisar a imagem escolhida.',
                };
            }

            const width =
                Math.max(
                    0,
                    parseInt(dimensions?.width) || 0
                );

            const height =
                Math.max(
                    0,
                    parseInt(dimensions?.height) || 0
                );

            const pixels =
                width * height;

            const maxSide =
                isPortrait
                    ? 6000
                    : 8192;

            const maxPixels =
                isPortrait
                    ? 24000000
                    : 36000000;

            if (
                width <= 0
                || height <= 0
            ) {
                return {
                    ok: false,
                    type: 'error',
                    message:
                        'A imagem não possui dimensões válidas.',
                };
            }

            if (
                width > maxSide
                || height > maxSide
                || pixels > maxPixels
            ) {
                return {
                    ok: false,
                    type: 'error',
                    message:
                        'Imagem grande demais para a vitrine: '
                        + width
                        + '×'
                        + height
                        + ' px. '
                        + (
                            isPortrait
                                ? 'Use até 6000 px por lado e 24 megapixels.'
                                : 'Use até 8192 px por lado e 36 megapixels.'
                        ),
                };
            }

            let type = 'success';
            let extra = '';

            if (
                !isPortrait
                && width / height < 2.4
            ) {
                type = 'warning';
                extra =
                    ' A vitrine do Player é muito horizontal, então haverá '
                    + 'recorte acima e abaixo. A prévia mostra esse recorte real.';
            } else if (
                !isPortrait
                && (
                    width < 1600
                    || height < 500
                )
            ) {
                type = 'warning';
                extra =
                    ' A imagem é válida, mas uma resolução maior tende a ficar '
                    + 'mais nítida na vitrine.';
            } else if (
                isPortrait
                && (
                    width < 500
                    || height < 700
                )
            ) {
                type = 'warning';
                extra =
                    ' A imagem é válida, mas pode perder nitidez com zoom alto.';
            }

            return {
                ok: true,
                type,
                width,
                height,
                message:
                    (
                        isPortrait
                            ? 'Retrato pronto: '
                            : 'Cenário pronto: '
                    )
                    + width
                    + '×'
                    + height
                    + ' px · '
                    + this.formatShopFileSize(file.size)
                    + '.'
                    + extra,
            };
        },

        revokeShopPreviewUrl(url) {
            if (
                typeof url === 'string'
                && url.startsWith('blob:')
            ) {
                URL.revokeObjectURL(url);
            }
        },

        async handleShopNewImage(event) {
            const input =
                event?.target ?? null;

            const file =
                input?.files?.[0] ?? null;

            if (!file) {
                return;
            }

            this.shopNewImageFeedback =
                'Analisando retrato...';

            this.shopNewImageFeedbackType =
                'info';

            const validation =
                await this.validateShopUploadFile(
                    file,
                    'portrait'
                );

            if (!validation.ok) {
                this.shopNewImageFeedback =
                    validation.message;

                this.shopNewImageFeedbackType =
                    'error';

                this.lootError =
                    validation.message;

                if (input) {
                    input.value = '';
                }

                return;
            }

            this.revokeShopPreviewUrl(
                this.shopNewImagePreview
            );

            this.shopNewImageFile =
                file;

            this.shopNewImagePreview =
                URL.createObjectURL(file);

            this.shopNewImageFeedback =
                validation.message
                + ' Clique em Criar loja para salvar.';

            this.shopNewImageFeedbackType =
                validation.type;

            this.lootError =
                null;

            if (input) {
                input.value = '';
            }
        },

        clearShopNewImage() {
            this.revokeShopPreviewUrl(
                this.shopNewImagePreview
            );

            this.shopNewImageFile = null;
            this.shopNewImagePreview = null;

            this.shopNewImageFeedback =
                'Retrato removido da nova loja.';

            this.shopNewImageFeedbackType =
                'info';
        },

        async handleShopNewBackground(event) {
            const input =
                event?.target ?? null;

            const file =
                input?.files?.[0] ?? null;

            if (!file) {
                return;
            }

            this.shopNewBackgroundFeedback =
                'Analisando cenário...';

            this.shopNewBackgroundFeedbackType =
                'info';

            const validation =
                await this.validateShopUploadFile(
                    file,
                    'background'
                );

            if (!validation.ok) {
                this.shopNewBackgroundFeedback =
                    validation.message;

                this.shopNewBackgroundFeedbackType =
                    'error';

                this.lootError =
                    validation.message;

                if (input) {
                    input.value = '';
                }

                return;
            }

            this.revokeShopPreviewUrl(
                this.shopNewBackgroundPreview
            );

            this.shopNewBackgroundFile =
                file;

            this.shopNewBackgroundPreview =
                URL.createObjectURL(file);

            this.shopNewBackgroundFeedback =
                validation.message
                + ' Clique em Criar loja para salvar.';

            this.shopNewBackgroundFeedbackType =
                validation.type;

            this.lootError =
                null;

            if (input) {
                input.value = '';
            }
        },

        clearShopNewBackground() {
            this.revokeShopPreviewUrl(
                this.shopNewBackgroundPreview
            );

            this.shopNewBackgroundFile = null;
            this.shopNewBackgroundPreview = null;

            this.shopNewBackgroundFeedback =
                'Cenário removido da nova loja.';

            this.shopNewBackgroundFeedbackType =
                'info';
        },

        resetShopCreateForm() {
            this.clearShopNewImage();
            this.clearShopNewBackground();
            this.shopNewName = '';
            this.shopNewDescription = '';
            this.shopNewFolderId = null;
            this.shopNewShopkeeperNpcId = '';
            this.shopNewAttendantName = '';
            this.shopNewShopkeeperOpen = false;
            this.shopNewShopkeeperSearch = '';
            this.shopNewHasCounter = true;
            this.shopNewCounterColor = '#6B3A24';
            this.shopNewBackgroundColor = '#6F4A33';
            this.shopNewImageZoom = 100;
            this.shopNewImagePosition = 'center';
            this.shopNewBackgroundZoom = 100;
            this.shopNewBackgroundPosition = 'center';
            this.shopNewImageFeedback = null;
            this.shopNewImageFeedbackType = 'info';
            this.shopNewBackgroundFeedback = null;
            this.shopNewBackgroundFeedbackType = 'info';
        },

        async createShop() {
            if (this.shopCreating) {
                return;
            }

            const name = String(
                this.shopNewName
                ?? ''
            ).trim();

            if (!name) {
                this.lootError =
                    'Informe um nome para a loja.';
                return;
            }

            this.shopCreating = true;
            this.lootError = null;

            try {
                const form = new FormData();

                form.append('name', name);
                form.append(
                    'description',
                    String(
                        this.shopNewDescription
                        ?? ''
                    ).trim()
                );

                if (this.shopNewFolderId) {
                    form.append(
                        'folder_id',
                        String(
                            this.shopNewFolderId
                        )
                    );
                }

                if (this.shopNewShopkeeperNpcId) {
                    form.append(
                        'shopkeeper_npc_id',
                        String(this.shopNewShopkeeperNpcId)
                    );
                }

                form.append(
                    'shop_attendant_name',
                    this.shopNewShopkeeperNpcId
                        ? ''
                        : String(this.shopNewAttendantName ?? '').trim()
                );

                form.append(
                    'shop_has_counter',
                    this.shopNewHasCounter ? '1' : '0'
                );

                form.append(
                    'shop_counter_color',
                    String(this.shopNewCounterColor || '#6B3A24')
                );
                form.append(
                    'shop_background_color',
                    String(this.shopNewBackgroundColor || '#6F4A33')
                );
                form.append(
                    'shop_image_zoom',
                    String(this.shopNewImageZoom || 100)
                );
                form.append(
                    'shop_image_position',
                    String(this.shopNewImagePosition || 'center')
                );
                form.append(
                    'shop_background_zoom',
                    String(this.shopNewBackgroundZoom || 100)
                );
                form.append(
                    'shop_background_position',
                    String(this.shopNewBackgroundPosition || 'center')
                );

                if (this.shopNewImageFile) {
                    form.append(
                        'shop_image',
                        this.shopNewImageFile
                    );
                }

                if (this.shopNewBackgroundFile) {
                    form.append(
                        'shop_background',
                        this.shopNewBackgroundFile
                    );
                }

                const data = await this.lootJson(
                    this.shopStoreUrl,
                    {
                        method: 'POST',
                        headers: this.lootHeaders(),
                        body: form,
                    }
                );

                const shop = this.normalizeLootDrop(
                    data?.shop
                    ?? {}
                );

                const createdShopId =
                    parseInt(shop?.id);

                if (!Number.isFinite(createdShopId)) {
                    throw new Error(
                        'A loja foi criada, mas o servidor não retornou um identificador válido.'
                    );
                }

                /*
                | Atualiza imediatamente a interface para que a loja apareça
                | sem depender do próximo refresh automático.
                */
                this.lootShops = [
                    shop,
                    ...this.lootShops.filter(
                        entry =>
                            parseInt(entry?.id)
                            !== createdShopId
                    ),
                ];

                this.lootWorkspaceMode = 'shop';
                this.lootActiveShopId = createdShopId;
                this.shopActiveFolderId = null;

                this.resetShopCreateForm();
                this.shopCreateOpen = false;

                /*
                | Pode existir um refresh automático iniciado alguns
                | milissegundos antes da criação. Se ele terminar depois
                | do POST, ele pode sobrescrever lootShops com uma resposta
                | antiga e fazer a loja recém-criada desaparecer.
                |
                | Esperamos esse refresh terminar e então buscamos o estado
                | persistido novamente. Assim a criação e a listagem ficam
                | sincronizadas com o banco.
                */
                let refreshWaitAttempts = 0;

                while (
                    this.lootLoading
                    && refreshWaitAttempts < 40
                ) {
                    await new Promise(
                        resolve =>
                            window.setTimeout(resolve, 75)
                    );

                    refreshWaitAttempts++;
                }

                await this.loadLootWorkspace(true);

                this.lootWorkspaceMode = 'shop';
                this.lootActiveShopId = createdShopId;
                this.shopActiveFolderId = null;

                /*
                | Fallback defensivo: se a atualização geral falhar por
                | qualquer motivo transitório, mantemos a loja retornada
                | pelo POST visível na tela.
                */
                if (!this.lootActiveShop) {
                    this.lootShops = [
                        shop,
                        ...this.lootShops.filter(
                            entry =>
                                parseInt(entry?.id)
                                !== createdShopId
                        ),
                    ];

                    this.lootActiveShopId = createdShopId;
                }

                /*
                | Não abrimos mais automaticamente o seletor de produtos.
                | Primeiro mostramos a loja recém-criada ao Mestre; ele pode
                | adicionar estoque pelo botão da própria tela da loja.
                */
            } catch (error) {
                const message =
                    error?.message
                    ?? 'Não foi possível criar a loja.';

                this.lootError =
                    message;

                if (this.shopNewImageFile) {
                    this.shopNewImageFeedback =
                        message;

                    this.shopNewImageFeedbackType =
                        'error';
                }

                if (this.shopNewBackgroundFile) {
                    this.shopNewBackgroundFeedback =
                        message;

                    this.shopNewBackgroundFeedbackType =
                        'error';
                }
            } finally {
                this.shopCreating = false;
            }
        },

        shopPresentationUpdateUrl(dropId) {
            return this.shopPresentationUpdateUrlTemplate
                .replace('__DROP__', dropId);
        },

        openShopPresentationEditor(shop = null) {
            const target = shop ?? this.lootActiveShop;

            if (!target?.id) {
                return;
            }

            this.closeShopPresentationEditor();

            this.shopEditName = String(target?.name ?? '');
            this.shopEditDescription = String(target?.description ?? '');
            this.shopEditShopkeeperNpcId =
                target?.shopkeeper_npc_id
                    ? String(target.shopkeeper_npc_id)
                    : '';
            this.shopEditAttendantName =
                String(target?.shop_attendant_name ?? '');
            this.shopEditShopkeeperOpen = false;
            this.shopEditShopkeeperSearch = '';
            this.shopEditImageFile = null;
            this.shopEditImagePreview = target?.has_custom_shop_image
                ? (target?.presentation_image_url ?? null)
                : null;
            this.shopEditRemoveImage = false;
            this.shopEditBackgroundFile = null;
            this.shopEditBackgroundPreview = target?.background_image_url ?? null;
            this.shopEditRemoveBackground = false;
            this.shopEditHasCounter = target?.shop_has_counter !== false;
            this.shopEditCounterColor =
                target?.shop_counter_color
                ?? '#6B3A24';
            this.shopEditBackgroundColor =
                target?.shop_background_color
                ?? '#6F4A33';
            this.shopEditImageZoom =
                parseInt(target?.shop_image_zoom)
                || 100;
            this.shopEditImagePosition =
                ['top', 'center', 'bottom'].includes(
                    String(target?.shop_image_position ?? '')
                )
                    ? String(target.shop_image_position)
                    : 'center';
            this.shopEditBackgroundZoom =
                parseInt(target?.shop_background_zoom)
                || 100;
            this.shopEditBackgroundPosition =
                ['top', 'center', 'bottom'].includes(
                    String(target?.shop_background_position ?? '')
                )
                    ? String(target.shop_background_position)
                    : 'center';
            this.shopEditImageFeedback = null;
            this.shopEditImageFeedbackType = 'info';
            this.shopEditBackgroundFeedback = null;
            this.shopEditBackgroundFeedbackType = 'info';
            this.shopPresentationOpen = true;
        },

        closeShopPresentationEditor() {
            for (const preview of [
                this.shopEditImagePreview,
                this.shopEditBackgroundPreview,
            ]) {
                if (
                    preview
                    && preview.startsWith('blob:')
                ) {
                    URL.revokeObjectURL(preview);
                }
            }

            this.shopEditAttendantName = '';
            this.shopEditShopkeeperOpen = false;
            this.shopEditShopkeeperSearch = '';
            this.shopEditImageFile = null;
            this.shopEditImagePreview = null;
            this.shopEditRemoveImage = false;
            this.shopEditBackgroundFile = null;
            this.shopEditBackgroundPreview = null;
            this.shopEditRemoveBackground = false;
            this.shopEditHasCounter = true;
            this.shopEditCounterColor = '#6B3A24';
            this.shopEditBackgroundColor = '#6F4A33';
            this.shopEditImageZoom = 100;
            this.shopEditImagePosition = 'center';
            this.shopEditBackgroundZoom = 100;
            this.shopEditBackgroundPosition = 'center';
            this.shopEditImageFeedback = null;
            this.shopEditImageFeedbackType = 'info';
            this.shopEditBackgroundFeedback = null;
            this.shopEditBackgroundFeedbackType = 'info';
            this.shopPresentationOpen = false;
        },

        async handleShopPresentationImage(event) {
            const input =
                event?.target ?? null;

            const file =
                input?.files?.[0] ?? null;

            if (!file) {
                return;
            }

            this.shopEditImageFeedback =
                'Analisando retrato...';

            this.shopEditImageFeedbackType =
                'info';

            const validation =
                await this.validateShopUploadFile(
                    file,
                    'portrait'
                );

            if (!validation.ok) {
                this.shopEditImageFeedback =
                    validation.message;

                this.shopEditImageFeedbackType =
                    'error';

                this.lootError =
                    validation.message;

                if (input) {
                    input.value = '';
                }

                return;
            }

            this.revokeShopPreviewUrl(
                this.shopEditImagePreview
            );

            this.shopEditImageFile =
                file;

            this.shopEditImagePreview =
                URL.createObjectURL(file);

            this.shopEditRemoveImage =
                false;

            this.shopEditImageFeedback =
                validation.message
                + ' Clique em Salvar alterações para aplicar.';

            this.shopEditImageFeedbackType =
                validation.type;

            this.lootError =
                null;

            if (input) {
                input.value = '';
            }
        },

        removeShopCustomImage() {
            this.revokeShopPreviewUrl(
                this.shopEditImagePreview
            );

            this.shopEditImageFile = null;
            this.shopEditImagePreview = null;
            this.shopEditRemoveImage = true;

            this.shopEditImageFeedback =
                'O retrato personalizado será removido ao salvar.';

            this.shopEditImageFeedbackType =
                'warning';
        },

        async handleShopPresentationBackground(event) {
            const input =
                event?.target ?? null;

            const file =
                input?.files?.[0] ?? null;

            if (!file) {
                return;
            }

            this.shopEditBackgroundFeedback =
                'Analisando cenário...';

            this.shopEditBackgroundFeedbackType =
                'info';

            const validation =
                await this.validateShopUploadFile(
                    file,
                    'background'
                );

            if (!validation.ok) {
                this.shopEditBackgroundFeedback =
                    validation.message;

                this.shopEditBackgroundFeedbackType =
                    'error';

                this.lootError =
                    validation.message;

                if (input) {
                    input.value = '';
                }

                return;
            }

            this.revokeShopPreviewUrl(
                this.shopEditBackgroundPreview
            );

            this.shopEditBackgroundFile =
                file;

            this.shopEditBackgroundPreview =
                URL.createObjectURL(file);

            this.shopEditRemoveBackground =
                false;

            this.shopEditBackgroundFeedback =
                validation.message
                + ' Clique em Salvar alterações para aplicar.';

            this.shopEditBackgroundFeedbackType =
                validation.type;

            this.lootError =
                null;

            if (input) {
                input.value = '';
            }
        },

        removeShopBackground() {
            this.revokeShopPreviewUrl(
                this.shopEditBackgroundPreview
            );

            this.shopEditBackgroundFile = null;
            this.shopEditBackgroundPreview = null;
            this.shopEditRemoveBackground = true;

            this.shopEditBackgroundFeedback =
                'O cenário personalizado será removido ao salvar.';

            this.shopEditBackgroundFeedbackType =
                'warning';
        },

        async saveShopPresentation() {
            const shop = this.lootActiveShop;

            if (!shop?.id || this.shopPresentationSaving) {
                return;
            }

            const name = String(this.shopEditName ?? '').trim();

            if (!name) {
                this.lootError = 'Informe o nome da loja.';
                return;
            }

            this.shopPresentationSaving = true;
            this.lootError = null;

            try {
                const form = new FormData();
                form.append('name', name);
                form.append(
                    'description',
                    String(this.shopEditDescription ?? '').trim()
                );
                form.append(
                    'remove_shop_image',
                    this.shopEditRemoveImage ? '1' : '0'
                );
                form.append(
                    'remove_shop_background',
                    this.shopEditRemoveBackground ? '1' : '0'
                );
                form.append(
                    'shop_has_counter',
                    this.shopEditHasCounter ? '1' : '0'
                );

                form.append(
                    'shop_counter_color',
                    String(this.shopEditCounterColor || '#6B3A24')
                );
                form.append(
                    'shop_background_color',
                    String(this.shopEditBackgroundColor || '#6F4A33')
                );
                form.append(
                    'shop_image_zoom',
                    String(this.shopEditImageZoom || 100)
                );
                form.append(
                    'shop_image_position',
                    String(this.shopEditImagePosition || 'center')
                );
                form.append(
                    'shop_background_zoom',
                    String(this.shopEditBackgroundZoom || 100)
                );
                form.append(
                    'shop_background_position',
                    String(this.shopEditBackgroundPosition || 'center')
                );

                if (this.shopEditShopkeeperNpcId) {
                    form.append(
                        'shopkeeper_npc_id',
                        String(this.shopEditShopkeeperNpcId)
                    );
                }

                form.append(
                    'shop_attendant_name',
                    this.shopEditShopkeeperNpcId
                        ? ''
                        : String(this.shopEditAttendantName ?? '').trim()
                );

                if (this.shopEditImageFile) {
                    form.append(
                        'shop_image',
                        this.shopEditImageFile
                    );
                }

                if (this.shopEditBackgroundFile) {
                    form.append(
                        'shop_background',
                        this.shopEditBackgroundFile
                    );
                }

                const data = await this.lootJson(
                    this.shopPresentationUpdateUrl(shop.id),
                    {
                        method: 'POST',
                        headers: this.lootHeaders(),
                        body: form,
                    }
                );

                const updated = this.normalizeLootDrop(
                    data?.shop ?? {}
                );

                const index = this.lootShops.findIndex(
                    entry => parseInt(entry?.id) === parseInt(updated?.id)
                );

                if (index >= 0) {
                    this.lootShops.splice(index, 1, updated);
                }

                this.lootActiveShopId = updated.id;
                this.closeShopPresentationEditor();
            } catch (error) {
                const message =
                    error?.message
                    ?? 'Não foi possível atualizar a apresentação da loja.';

                this.lootError =
                    message;

                if (this.shopEditImageFile) {
                    this.shopEditImageFeedback =
                        message;

                    this.shopEditImageFeedbackType =
                        'error';
                }

                if (this.shopEditBackgroundFile) {
                    this.shopEditBackgroundFeedback =
                        message;

                    this.shopEditBackgroundFeedbackType =
                        'error';
                }
            } finally {
                this.shopPresentationSaving = false;
            }
        },

        shopOpenUrl(dropId) {
            return this.shopOpenUrlTemplate
                .replace('__DROP__', dropId);
        },

        shopCloseUrl(dropId) {
            return this.shopCloseUrlTemplate
                .replace('__DROP__', dropId);
        },

        async openShopSession(shop) {
            if (
                !shop?.id
                || shop?.status === 'released'
            ) {
                return;
            }

            this.lootError = null;

            try {
                await this.lootJson(
                    this.shopOpenUrl(shop.id),
                    {
                        method: 'POST',
                        headers: this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(true);
                this.lootWorkspaceMode = 'shop';
                this.lootActiveShopId = shop.id;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível abrir a loja.';
            }
        },

        shopAddItemUrl(dropId) {
            return this.shopAddItemUrlTemplate
                .replace('__DROP__', dropId);
        },

        shopUpdateItemUrl(dropId, itemId) {
            return this.shopUpdateItemUrlTemplate
                .replace('__DROP__', dropId)
                .replace('__ITEM__', itemId);
        },

        lootCloseDropUrl(dropId) {
            return this.lootCloseDropUrlTemplate
                .replace('__DROP__', dropId);
        },

        async closeLootSession(drop) {
            if (!drop?.id) {
                return;
            }

            const isShop = !!drop?.is_shop;

            if (
                (isShop && drop?.status !== 'released')
                || (!isShop && drop?.status === 'closed')
            ) {
                return;
            }

            const label = isShop
                ? 'esta loja'
                : 'esta sessão de Espólio';

            if (
                !window.confirm(
                    `Fechar ${label}? Ela deixará de aparecer para os Players, mas continuará salva para você abrir novamente depois.`
                )
            ) {
                return;
            }

            this.lootError = null;

            try {
                await this.lootJson(
                    isShop
                        ? this.shopCloseUrl(drop.id)
                        : this.lootCloseDropUrl(drop.id),
                    {
                        method: 'POST',
                        headers: this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(true);

                if (isShop) {
                    this.lootWorkspaceMode = 'shop';
                    this.lootActiveShopId = drop.id;
                } else {
                    this.lootActiveDropId = null;
                    this.lootActiveFolderId = null;
                }
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível fechar a sessão.';
            }
        },

        openShopPicker() {
            const shop = this.lootActiveShop;

            if (!shop?.id) {
                return;
            }

            this.shopVaultSearch = '';
            this.shopVaultNature = 'all';
            this.shopVaultRarity = 'all';
            this.shopSelectedVaultItem = null;
            this.shopAddQuantity = 1;
            this.shopPriceAmounts = {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            };

            this.shopPriceHelpOpen =
                false;

            this.shopItemAddedNoticeVisible =
                false;

            this.shopPickerOpen =
                true;
        },

        closeShopPicker() {
            this.shopPickerOpen = false;
            this.shopPriceHelpOpen = false;
            this.shopItemAddedNoticeVisible = false;
            this.shopVaultSearch = '';
            this.shopVaultNature = 'all';
            this.shopVaultRarity = 'all';
            this.shopSelectedVaultItem = null;
            this.shopAddQuantity = 1;
            this.shopPriceAmounts = {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            };
        },

        openShopPriceHelp() {
            this.shopPriceHelpOpen =
                true;
        },

        closeShopPriceHelp() {
            this.shopPriceHelpOpen =
                false;
        },

        showShopItemAddedNotice(itemName = '') {
            const label =
                String(
                    itemName
                    ?? ''
                ).trim();

            this.shopItemAddedNotice =
                label
                    ? `${label} adicionado à loja.`
                    : 'Item adicionado à loja.';

            this.shopItemAddedNoticeVisible =
                true;

            const token =
                ++this.shopItemAddedNoticeToken;

            window.setTimeout(
                () => {
                    if (
                        token
                        ===
                        this.shopItemAddedNoticeToken
                    ) {
                        this.shopItemAddedNoticeVisible =
                            false;
                    }
                },
                2000
            );
        },

        selectShopVaultItem(item) {
            this.shopSelectedVaultItem = item;
            this.shopAddQuantity = Math.max(
                1,
                parseInt(item?.default_quantity) || 1
            );
            this.shopPriceAmounts = {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            };
        },

        adjustShopAddQuantity(delta) {
            this.shopAddQuantity =
                Math.max(
                    1,
                    Math.min(
                        999999,
                        (
                            parseInt(
                                this.shopAddQuantity
                            )
                            || 1
                        )
                        + (
                            parseInt(
                                delta
                            )
                            || 0
                        )
                    )
                );
        },

        normalizeShopAddQuantity() {
            this.shopAddQuantity =
                Math.max(
                    1,
                    Math.min(
                        999999,
                        parseInt(
                            this.shopAddQuantity
                        )
                        || 1
                    )
                );
        },

        adjustShopPriceCoin(key, delta) {
            if (
                !this.lootCoinDefinitions
                    .some(
                        coin =>
                            coin.key
                            ===
                            key
                    )
            ) {
                return;
            }

            this.shopPriceAmounts[
                key
            ] =
                Math.max(
                    0,
                    (
                        parseInt(
                            this.shopPriceAmounts[
                                key
                            ]
                        )
                        || 0
                    )
                    + (
                        parseInt(
                            delta
                        )
                        || 0
                    )
                );
        },

        normalizeShopPriceCoin(key) {
            if (
                !this.lootCoinDefinitions
                    .some(
                        coin =>
                            coin.key
                            ===
                            key
                    )
            ) {
                return;
            }

            this.shopPriceAmounts[
                key
            ] =
                Math.max(
                    0,
                    parseInt(
                        this.shopPriceAmounts[
                            key
                        ]
                    )
                    || 0
                );
        },

        formatShopCoinNumber(value) {
            const amount =
                Math.max(
                    0,
                    parseInt(
                        value
                    )
                    || 0
                );

            return amount.toLocaleString(
                'en-US',
                {
                    maximumFractionDigits:
                        0,
                    useGrouping:
                        true,
                }
            );
        },

        setShopPriceCoinInput(key, rawValue) {
            if (
                !this.lootCoinDefinitions
                    .some(
                        coin =>
                            coin.key
                            ===
                            key
                    )
            ) {
                return;
            }

            const digits =
                String(
                    rawValue
                    ?? ''
                )
                    .replace(
                        /[^0-9]/g,
                        ''
                    )
                    .replace(
                        /^0+(?=\d)/,
                        ''
                    );

            this.shopPriceAmounts[
                key
            ] =
                digits
                    ? Math.max(
                        0,
                        parseInt(
                            digits,
                            10
                        )
                        || 0
                    )
                    : 0;
        },

        shopPriceDraftInline(source) {
            const parts =
                this.lootCoinDefinitions
                    .map(
                        coin => ({
                            ...coin,
                            amount:
                                Math.max(
                                    0,
                                    parseInt(
                                        source?.[
                                            coin.key
                                        ]
                                    )
                                    || 0
                                ),
                        })
                    )
                    .filter(
                        coin =>
                            coin.amount
                            > 0
                    );

            return parts.length
                ? parts
                    .map(
                        coin =>
                            `${this.formatShopCoinNumber(coin.amount)} ${coin.abbr}`
                    )
                    .join(' · ')
                : 'Defina o preço';
        },

        shopPricePayload(source) {
            const payload = {};

            for (const coin of this.lootCoinDefinitions) {
                payload['price_' + coin.key] = Math.max(
                    0,
                    parseInt(source?.[coin.key]) || 0
                );
            }

            return payload;
        },

        shopPriceHasValue(source) {
            return this.lootCoinDefinitions
                .some(
                    coin =>
                        Math.max(
                            0,
                            parseInt(source?.[coin.key]) || 0
                        ) > 0
                );
        },

        async addSelectedVaultItemToShop() {
            const shop = this.lootActiveShop;
            const item = this.shopSelectedVaultItem;

            if (
                !shop?.id
                || !item?.id
                || this.shopAdding
            ) {
                return;
            }

            if (!this.shopPriceHasValue(this.shopPriceAmounts)) {
                this.lootError = 'Informe um preço para o item da loja.';
                return;
            }

            this.shopAdding = true;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.shopAddItemUrl(shop.id),
                    {
                        method: 'POST',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify({
                            vault_item_id: item.id,
                            quantity: Math.max(
                                1,
                                parseInt(this.shopAddQuantity) || 1
                            ),
                            ...this.shopPricePayload(
                                this.shopPriceAmounts
                            ),
                        }),
                    }
                );

                const activeId =
                    shop.id;

                const addedItemName =
                    String(
                        item?.name
                        ?? ''
                    );

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveShopId =
                    activeId;

                /*
                | O modal permanece aberto para o Mestre inserir vários itens
                | em sequência. Limpamos somente a seleção/preço atual.
                */
                this.shopSelectedVaultItem =
                    null;

                this.shopAddQuantity =
                    1;

                this.shopPriceAmounts = {
                    copper: 0,
                    silver: 0,
                    electrum: 0,
                    gold: 0,
                    platinum: 0,
                };

                this.showShopItemAddedNotice(
                    addedItemName
                );
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível adicionar o item à loja.';
            } finally {
                this.shopAdding = false;
            }
        },

        openShopPriceEditor(item) {
            if (!item?.id) return;

            this.shopEditingItemId = item.id;
            this.shopEditPriceAmounts = {
                copper: Math.max(0, parseInt(item?.price_copper) || 0),
                silver: Math.max(0, parseInt(item?.price_silver) || 0),
                electrum: Math.max(0, parseInt(item?.price_electrum) || 0),
                gold: Math.max(0, parseInt(item?.price_gold) || 0),
                platinum: Math.max(0, parseInt(item?.price_platinum) || 0),
            };
        },

        closeShopPriceEditor() {
            this.shopEditingItemId = null;
        },

        async updateShopItemPrice(item) {
            const shop = this.lootActiveShop;

            if (
                !shop?.id
                || !item?.id
                || this.shopUpdatingItemId !== null
            ) {
                return;
            }

            if (!this.shopPriceHasValue(this.shopEditPriceAmounts)) {
                this.lootError = 'Informe um preço para o item da loja.';
                return;
            }

            this.shopUpdatingItemId = item.id;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.shopUpdateItemUrl(shop.id, item.id),
                    {
                        method: 'PATCH',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify(
                            this.shopPricePayload(
                                this.shopEditPriceAmounts
                            )
                        ),
                    }
                );

                const activeId = shop.id;
                await this.loadLootWorkspace(true);
                this.lootActiveShopId = activeId;
                this.closeShopPriceEditor();
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível atualizar o preço.';
            } finally {
                this.shopUpdatingItemId = null;
            }
        },

        async deleteShopItem(item) {
            const shop = this.lootActiveShop;

            if (
                !shop?.id
                || !item?.id
                || this.lootDeletingItemId !== null
            ) {
                return;
            }

            if (!this.lootCanDeleteItem(item)) {
                this.lootError =
                    'Este item já possui histórico de venda e deve permanecer como registro.';
                return;
            }

            if (
                !window.confirm(
                    `Remover “${item.name ?? 'este item'}” da loja?`
                )
            ) {
                return;
            }

            this.lootDeletingItemId = item.id;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.lootDeleteItemUrl(
                        shop.id,
                        item.id
                    ),
                    {
                        method: 'DELETE',
                        headers: this.lootHeaders(),
                    }
                );

                const activeId = shop.id;
                await this.loadLootWorkspace(true);
                this.lootActiveShopId = activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível remover o item da loja.';
            } finally {
                this.lootDeletingItemId = null;
            }
        },

        lootDeleteDropUrl(dropId) {
            return this.lootDeleteDropUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                );
        },

        openQuickLoot() {
            this.lootQuickOpen =
                true;

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;

            this.lootQuickSearch =
                '';

            this.lootQuickTab =
                'available';

            this.lootQuickComposerOpen =
                false;

            this.lootQuickComposerMode =
                'item';

            this.lootQuickExpandedItemId =
                null;

            this.lootQuickExpandedCurrencyId =
                null;

            this.lootQuickItemMenuId =
                null;

            if (
                !this.lootLoaded
            ) {
                this.loadLootWorkspace();
            }
        },

        closeQuickLoot() {
            this.lootQuickOpen =
                false;

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;

            this.lootQuickSearch =
                '';

            this.lootQuickSelectedItem =
                null;

            this.lootQuickQuantity =
                1;

            this.lootQuickVisibility =
                'party';

            this.lootQuickCharacterIds =
                [];

            this.lootQuickTab =
                'available';

            this.lootQuickComposerOpen =
                false;

            this.lootQuickComposerMode =
                'item';

            this.lootQuickExpandedItemId =
                null;

            this.lootQuickExpandedCurrencyId =
                null;

            this.lootQuickItemMenuId =
                null;

            this.resetQuickCurrencyForm();
        },

        openQuickLootComposer(mode = 'item') {
            this.lootQuickComposerOpen =
                true;

            this.lootQuickComposerMode =
                mode === 'currency'
                    ? 'currency'
                    : 'item';

            this.lootQuickSearch =
                '';

            this.clearQuickVaultItem();

            this.resetQuickCurrencyForm();

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;

            this.lootQuickItemMenuId =
                null;
        },

        closeQuickLootComposer() {
            this.lootQuickComposerOpen =
                false;

            this.lootQuickComposerMode =
                'item';

            this.clearQuickVaultItem();

            this.resetQuickCurrencyForm();

            this.lootQuickSearch =
                '';
        },

        selectQuickVaultItem(item) {
            if (!item?.id) {
                return;
            }

            this.lootQuickSelectedItem =
                item;

            this.lootQuickQuantity =
                Math.max(
                    1,
                    parseInt(
                        item?.default_quantity
                    )
                    || 1
                );

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;
        },

        clearQuickVaultItem() {
            this.lootQuickSelectedItem =
                null;

            this.lootQuickQuantity =
                1;

            this.lootQuickVisibility =
                'party';

            this.lootQuickCharacterIds =
                [];
        },

        resetQuickCurrencyForm() {
            this.lootQuickCurrencyLabel =
                '';

            this.lootQuickCurrencyAmounts =
                {
                    copper: 0,
                    silver: 0,
                    electrum: 0,
                    gold: 0,
                    platinum: 0,
                };

            this.lootQuickCurrencyDistribution =
                'open';

            this.lootQuickCurrencyCharacterIds =
                [];
        },

        quickLootRecipientSelected(characterId) {
            const id =
                parseInt(characterId);

            return this.lootQuickCharacterIds
                .some(
                    current =>
                        parseInt(current)
                        ===
                        id
                );
        },

        toggleQuickLootRecipient(characterId) {
            const id =
                parseInt(characterId);

            if (!Number.isFinite(id)) {
                return;
            }

            if (
                this.quickLootRecipientSelected(
                    id
                )
            ) {
                this.lootQuickCharacterIds =
                    this.lootQuickCharacterIds
                        .filter(
                            current =>
                                parseInt(current)
                                !==
                                id
                        );

                return;
            }

            this.lootQuickCharacterIds =
                [
                    ...this.lootQuickCharacterIds,
                    id,
                ];
        },

        async releaseQuickLootItem() {
            const item =
                this.lootQuickSelectedItem;

            if (
                !item?.id
                ||
                this.lootQuickReleasing
            ) {
                return;
            }

            const quantity =
                Math.max(
                    1,
                    parseInt(
                        this.lootQuickQuantity
                    )
                    || 1
                );

            if (
                this.lootQuickVisibility
                ===
                'restricted'
                &&
                this.lootQuickCharacterIds
                    .length
                ===
                0
            ) {
                this.lootQuickError =
                    'Selecione ao menos um personagem.';

                return;
            }

            this.lootQuickReleasing =
                true;

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;

            try {
                const data =
                    await this.lootJson(
                        this.lootQuickReleaseUrl,
                        {
                            method:
                                'POST',

                            headers:
                                this.lootHeaders(
                                    true
                                ),

                            body:
                                JSON.stringify({
                                    vault_item_id:
                                        item.id,

                                    quantity,

                                    visibility_mode:
                                        this.lootQuickVisibility,

                                    character_ids:
                                        this.lootQuickVisibility
                                        ===
                                        'restricted'
                                            ? this.lootQuickCharacterIds
                                            : [],
                                }),
                        }
                    );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    data?.message
                    ?? 'Item liberado.';

                this.lootQuickTab =
                    'available';

                this.clearQuickVaultItem();
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ?? 'Não foi possível liberar o item.';
            } finally {
                this.lootQuickReleasing =
                    false;
            }
        },

        async releaseQuickLootCurrency() {
            if (
                this.lootQuickCurrencyReleasing
            ) {
                return;
            }

            const payload =
                {
                    label:
                        String(
                            this.lootQuickCurrencyLabel
                            ?? ''
                        ).trim()
                        ||
                        null,

                    distribution_mode:
                        this.lootQuickCurrencyDistribution,

                    character_ids:
                        this.lootQuickCurrencyDistribution
                        ===
                        'open'
                            ? []
                            : [
                                ...this.lootQuickCurrencyCharacterIds,
                            ],
                };

            let hasCoins =
                false;

            for (
                const coin
                of
                this.lootCoinDefinitions
            ) {
                const amount =
                    Math.max(
                        0,
                        parseInt(
                            this.lootQuickCurrencyAmounts?.[
                                coin.key
                            ]
                        )
                        ||
                        0
                    );

                payload[coin.key] =
                    amount;

                if (amount > 0) {
                    hasCoins =
                        true;
                }
            }

            if (!hasCoins) {
                this.lootQuickError =
                    'Informe ao menos uma moeda.';

                return;
            }

            if (
                this.lootQuickCurrencyDistribution
                ===
                'split'
                &&
                this.lootQuickCurrencyCharacterIds.length
                ===
                0
            ) {
                this.lootQuickError =
                    'Selecione ao menos um personagem para dividir as moedas.';

                return;
            }

            if (
                this.lootQuickCurrencyDistribution
                ===
                'first_claim'
                &&
                this.lootQuickCurrencyCharacterIds.length
                !==
                1
            ) {
                this.lootQuickError =
                    'Escolha um personagem para receber as moedas.';

                return;
            }

            this.lootQuickCurrencyReleasing =
                true;

            this.lootQuickError =
                null;

            this.lootQuickNotice =
                null;

            try {
                const data =
                    await this.lootJson(
                        this.lootQuickCurrencyReleaseUrl,
                        {
                            method:
                                'POST',

                            headers:
                                this.lootHeaders(
                                    true
                                ),

                            body:
                                JSON.stringify(
                                    payload
                                ),
                        }
                    );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    data?.message
                    ??
                    'Moedas liberadas.';

                this.lootQuickTab =
                    'available';

                this.resetQuickCurrencyForm();
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ??
                    'Não foi possível liberar as moedas.';
            } finally {
                this.lootQuickCurrencyReleasing =
                    false;
            }
        },

        async withdrawQuickLootCurrency(entry) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !entry?.id
                ||
                this.lootQuickActingCurrencyId
                !==
                null
            ) {
                return;
            }

            this.lootQuickActingCurrencyId =
                entry.id;

            this.lootQuickError =
                null;

            try {
                await this.lootJson(
                    this.lootCurrencyWithdrawUrl(
                        drop.id,
                        entry.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    'Moedas recolhidas. O que já foi recebido permanece nas carteiras.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ??
                    'Não foi possível recolher as moedas.';
            } finally {
                this.lootQuickActingCurrencyId =
                    null;
            }
        },

        async releaseQuickExistingCurrency(entry) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !entry?.id
                ||
                !this.lootQuickCurrencyHasRemaining(
                    entry
                )
                ||
                this.lootQuickActingCurrencyId
                !==
                null
            ) {
                return;
            }

            const mode =
                ['open', 'split', 'first_claim']
                    .includes(
                        entry?.distribution_mode
                    )
                    ? entry.distribution_mode
                    : 'open';

            const recipientIds =
                this.quickCurrencyRecipientIds(
                    entry
                );

            if (
                mode === 'split'
                &&
                recipientIds.length === 0
            ) {
                this.lootQuickError =
                    'Esta divisão não possui personagens vinculados.';

                return;
            }

            if (
                mode === 'first_claim'
                &&
                recipientIds.length !== 1
            ) {
                this.lootQuickError =
                    'Esta entrega não possui um personagem válido.';

                return;
            }

            this.lootQuickActingCurrencyId =
                entry.id;

            this.lootQuickError =
                null;

            try {
                await this.lootJson(
                    this.lootCurrencyReleaseUrl(
                        drop.id,
                        entry.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(
                                true
                            ),

                        body:
                            JSON.stringify({
                                visibility_mode:
                                    mode === 'open'
                                        ? 'party'
                                        : 'restricted',

                                distribution_mode:
                                    mode,

                                character_ids:
                                    mode === 'open'
                                        ? []
                                        : recipientIds,
                            }),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickTab =
                    'available';

                this.lootQuickNotice =
                    'Moedas disponibilizadas novamente.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ??
                    'Não foi possível disponibilizar as moedas novamente.';
            } finally {
                this.lootQuickActingCurrencyId =
                    null;
            }
        },

        async withdrawQuickLootItem(item) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                this.lootActingItemId
                !==
                null
            ) {
                return;
            }

            this.lootActingItemId =
                item.id;

            this.lootQuickError =
                null;

            try {
                await this.lootJson(
                    this.lootWithdrawItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    'Item recolhido da lista de encontrados.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ?? 'Não foi possível recolher o item.';
            } finally {
                this.lootActingItemId =
                    null;
            }
        },

        async releaseQuickExistingItem(item) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                parseInt(
                    item?.quantity_remaining
                    ?? 0
                )
                <=
                0
                ||
                this.lootActingItemId
                !==
                null
            ) {
                return;
            }

            const recipientIds =
                this.quickLootRecipientIds(
                    item
                );

            const visibility =
                item?.visibility_mode
                ===
                'restricted'
                &&
                recipientIds.length > 0
                    ? 'restricted'
                    : 'party';

            this.lootActingItemId =
                item.id;

            this.lootQuickError =
                null;

            this.lootQuickItemMenuId =
                null;

            try {
                await this.lootJson(
                    this.lootReleaseItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(
                                true
                            ),

                        body:
                            JSON.stringify({
                                visibility_mode:
                                    visibility,

                                character_ids:
                                    visibility
                                    ===
                                    'restricted'
                                        ? recipientIds
                                        : [],
                            }),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickTab =
                    'available';

                this.lootQuickNotice =
                    'Item disponibilizado novamente.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ?? 'Não foi possível disponibilizar o item novamente.';
            } finally {
                this.lootActingItemId =
                    null;
            }
        },

        async deleteQuickLootItem(item) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                !this.lootCanDeleteItem(
                    item
                )
                ||
                this.lootDeletingItemId
                !==
                null
            ) {
                return;
            }

            if (
                !window.confirm(
                    `Excluir ${item.name ?? 'este item'} do histórico rápido?`
                )
            ) {
                return;
            }

            this.lootDeletingItemId =
                item.id;

            this.lootQuickError =
                null;

            this.lootQuickItemMenuId =
                null;

            try {
                await this.lootJson(
                    this.lootDeleteItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method:
                            'DELETE',

                        headers:
                            this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    'Item rápido removido.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ?? 'Não foi possível remover o item rápido.';
            } finally {
                this.lootDeletingItemId =
                    null;
            }
        },

        async reverseQuickLootClaim(
            item,
            claim
        ) {
            const drop =
                this.lootQuickDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                !claim?.id
                ||
                claim?.reversed_at
                ||
                this.lootReversingClaimId
                !==
                null
            ) {
                return;
            }

            const destination =
                this.lootClaimDestinationLabel(
                    claim
                );

            if (
                !window.confirm(
                    `Recolher ${claim.quantity ?? 1}× “${item.name ?? 'item'}”?\n\n${destination}\n\nO item será retirado do inventário atual.`
                )
            ) {
                return;
            }

            this.lootReversingClaimId =
                claim.id;

            this.lootQuickError =
                null;

            try {
                await this.lootJson(
                    this.lootReverseClaimUrl(
                        drop.id,
                        item.id,
                        claim.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                await this.loadLootWorkspace(
                    true
                );

                this.lootQuickNotice =
                    'Item recolhido e devolvido ao acesso rápido.';
            } catch (error) {
                this.lootQuickError =
                    error?.message
                    ?? 'Não foi possível recolher o resgate.';
            } finally {
                this.lootReversingClaimId =
                    null;
            }
        },

        async deleteLootDrop(drop) {
            if (
                !drop?.id
                ||
                this.lootDeletingDropId
                !==
                null
            ) {
                return;
            }

            const isShop =
                !!drop?.is_shop;

            const subjectName =
                drop?.name
                ?? (isShop ? 'Loja' : 'Espólio');

            const confirmation =
                isShop
                    ? `Excluir permanentemente a loja “${subjectName}”?\n\nA sessão, o estoque e o histórico de vendas desta loja serão apagados.\n\nOs itens já comprados continuarão nos inventários dos personagens.`
                    : `Excluir permanentemente o Espólio “${subjectName}”?\n\nA sessão e o histórico de resgates deste Espólio serão apagados.\n\nItens e moedas já recebidos continuarão com os personagens.`;

            if (!window.confirm(confirmation)) {
                return;
            }

            this.lootDeletingDropId =
                drop.id;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootDeleteDropUrl(
                        drop.id
                    ),
                    {
                        method:
                            'DELETE',

                        headers:
                            this.lootHeaders(),
                    }
                );

                if (
                    isShop
                    &&
                    parseInt(
                        this.lootActiveShopId
                    )
                    ===
                    parseInt(
                        drop.id
                    )
                ) {
                    this.lootActiveShopId =
                        null;
                }

                if (
                    !isShop
                    &&
                    parseInt(
                        this.lootActiveDropId
                    )
                    ===
                    parseInt(
                        drop.id
                    )
                ) {
                    this.lootActiveDropId =
                        null;
                }

                this.lootDropMenuId =
                    null;

                await this.loadLootWorkspace(
                    true
                );
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? (
                        isShop
                            ? 'Não foi possível excluir a loja.'
                            : 'Não foi possível excluir o Espólio.'
                    );
            } finally {
                this.lootDeletingDropId =
                    null;
            }
        },

        lootAddItemUrl(dropId) {
            return this.lootAddItemUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                );
        },

        lootReleaseItemUrl(
            dropId,
            itemId
        ) {
            return this.lootReleaseItemUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                )
                .replace(
                    '__ITEM__',
                    itemId
                );
        },

        lootWithdrawItemUrl(
            dropId,
            itemId
        ) {
            return this.lootWithdrawItemUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                )
                .replace(
                    '__ITEM__',
                    itemId
                );
        },

        lootReleaseAllItemsUrl(
            dropId
        ) {
            return this.lootReleaseAllItemsUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                );
        },

        lootDeleteItemUrl(
            dropId,
            itemId
        ) {
            return this.lootDeleteItemUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                )
                .replace(
                    '__ITEM__',
                    itemId
                );
        },

        lootReverseClaimUrl(
            dropId,
            itemId,
            claimId
        ) {
            return this.lootReverseClaimUrlTemplate
                .replace(
                    '__DROP__',
                    dropId
                )
                .replace(
                    '__ITEM__',
                    itemId
                )
                .replace(
                    '__CLAIM__',
                    claimId
                );
        },

        lootCurrencyStoreUrl(dropId) {
            return this.lootCurrencyStoreUrlTemplate
                .replace('__DROP__', dropId);
        },

        lootCurrencyReleaseUrl(dropId, currencyId) {
            return this.lootCurrencyReleaseUrlTemplate
                .replace('__DROP__', dropId)
                .replace('__CURRENCY__', currencyId);
        },

        lootCurrencyWithdrawUrl(dropId, currencyId) {
            return this.lootCurrencyWithdrawUrlTemplate
                .replace('__DROP__', dropId)
                .replace('__CURRENCY__', currencyId);
        },

        lootItemImageUrl(
            drop,
            item
        ) {
            if (
                !drop?.id
                ||
                !item?.id
                ||
                !item?.image_path
            ) {
                return null;
            }

            return this.lootItemImageUrlTemplate
                .replace(
                    '__DROP__',
                    drop.id
                )
                .replace(
                    '__ITEM__',
                    item.id
                );
        },

        openLootPicker() {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                drop?.status
                    ===
                    'closed'
            ) {
                return;
            }

            this.lootVaultSearch =
                '';

            this.lootPickerOpen =
                true;
        },

        closeLootPicker() {
            this.lootPickerOpen =
                false;

            this.lootVaultSearch =
                '';
        },

        lootClaims(item) {
            return Array.isArray(
                item?.claims
            )
                ? item.claims
                : [];
        },

        lootActiveClaims(item) {
            return this.lootClaims(item)
                .filter(
                    claim =>
                        !claim?.reversed_at
                );
        },

        lootClaimOriginalCharacter(claim) {
            return claim?.character
                ?? null;
        },

        lootClaimCurrentCharacter(claim) {
            return claim?.character_item?.character
                ?? null;
        },

        lootClaimDestinationLabel(claim) {
            if (claim?.reversed_at) {
                const name =
                    claim?.character?.name
                    ?? 'personagem';

                return `Recolhido de ${name}`;
            }

            const original =
                this.lootClaimOriginalCharacter(
                    claim
                );

            const current =
                this.lootClaimCurrentCharacter(
                    claim
                );

            if (current?.name) {
                if (
                    original?.id
                    &&
                    parseInt(original.id)
                    !==
                    parseInt(current.id)
                ) {
                    return `Agora com ${current.name} · resgatado por ${original.name ?? 'outro personagem'}`;
                }

                return `Com ${current.name}`;
            }

            return original?.name
                ? `Resgatado por ${original.name} · item não localizado`
                : 'Item resgatado · destino não localizado';
        },

        lootClaimDate(value) {
            if (!value) {
                return '';
            }

            const date =
                new Date(value);

            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {
                return '';
            }

            return new Intl.DateTimeFormat(
                'pt-BR',
                {
                    day:
                        '2-digit',

                    month:
                        '2-digit',

                    hour:
                        '2-digit',

                    minute:
                        '2-digit',
                }
            ).format(date);
        },

        lootCanDeleteItem(item) {
            return this.lootClaims(item)
                .length
                ===
                0;
        },

        async addVaultItemToLoot(
            item
        ) {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                drop?.status
                    ===
                    'closed'
            ) {
                return;
            }

            if (
                this.lootAddingVaultId
                !==
                null
            ) {
                return;
            }

            this.lootAddingVaultId =
                item.id;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootAddItemUrl(
                        drop.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(
                                true
                            ),

                        body:
                            JSON.stringify({
                                vault_item_id:
                                    item.id,

                                quantity:
                                    Math.max(
                                        1,
                                        parseInt(
                                            item.default_quantity
                                        )
                                        || 1
                                    ),
                            }),
                    }
                );

                const activeId =
                    drop.id;

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível adicionar o item.';
            } finally {
                this.lootAddingVaultId =
                    null;
            }
        },

        beginLootItemRelease(item) {
            if (!item?.id) {
                return;
            }

            const id = parseInt(item.id);

            if (this.lootReleaseItemId === id) {
                this.cancelLootItemRelease();
                return;
            }

            this.lootReleaseItemId = id;

            this.lootReleaseVisibility =
                item?.visibility_mode === 'restricted'
                    ? 'restricted'
                    : 'party';

            this.lootReleaseCharacterIds =
                (Array.isArray(item?.recipients) ? item.recipients : [])
                    .map(
                        recipient =>
                            parseInt(
                                recipient?.character_id
                                ?? recipient?.character?.id
                            )
                    )
                    .filter(id => Number.isFinite(id));
        },

        cancelLootItemRelease() {
            this.lootReleaseItemId = null;
            this.lootReleaseVisibility = 'party';
            this.lootReleaseCharacterIds = [];
        },

        lootItemRecipientSelected(characterId) {
            const id = parseInt(characterId);

            return this.lootReleaseCharacterIds
                .some(current => parseInt(current) === id);
        },

        toggleLootItemRecipient(characterId) {
            const id = parseInt(characterId);

            if (!Number.isFinite(id)) {
                return;
            }

            const index =
                this.lootReleaseCharacterIds
                    .findIndex(
                        current =>
                            parseInt(current) === id
                    );

            if (index >= 0) {
                this.lootReleaseCharacterIds.splice(index, 1);
                return;
            }

            this.lootReleaseCharacterIds.push(id);
        },

        async releaseLootItem(item) {
            const drop = this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                this.lootActingItemId !== null
            ) {
                return;
            }

            if (
                this.lootReleaseVisibility === 'restricted'
                &&
                this.lootReleaseCharacterIds.length === 0
            ) {
                this.lootError =
                    'Selecione ao menos um personagem para liberar este item.';

                return;
            }

            this.lootActingItemId = item.id;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.lootReleaseItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method: 'POST',
                        headers:
                            this.lootHeaders(true),
                        body:
                            JSON.stringify({
                                visibility_mode:
                                    this.lootReleaseVisibility,
                                character_ids:
                                    this.lootReleaseVisibility === 'restricted'
                                        ? this.lootReleaseCharacterIds
                                        : [],
                            }),
                    }
                );

                const activeId = drop.id;

                this.cancelLootItemRelease();

                await this.loadLootWorkspace(true);

                this.lootActiveDropId = activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível liberar o item.';
            } finally {
                this.lootActingItemId = null;
            }
        },

        async withdrawLootItem(item) {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                this.lootActingItemId
                    !==
                    null
            ) {
                return;
            }

            this.lootActingItemId =
                item.id;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootWithdrawItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                const activeId =
                    drop.id;

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível recolher o item.';
            } finally {
                this.lootActingItemId =
                    null;
            }
        },

        async releaseAllLootRewards() {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                || drop?.status === 'closed'
                || this.lootReleasingAll
            ) {
                return;
            }

            const pendingItems =
                (drop?.items ?? [])
                    .filter(
                        item =>
                            parseInt(
                                item?.quantity_remaining
                            ) > 0
                            && item?.release_status !== 'released'
                    );

            const pendingCurrencies =
                (drop?.currencies ?? [])
                    .filter(
                        currency =>
                            currency?.release_status !== 'released'
                            && this.lootCurrencyParts(
                                currency,
                                true
                            ).length > 0
                    );

            if (
                pendingItems.length === 0
                && pendingCurrencies.length === 0
            ) {
                this.lootError =
                    'Todas as recompensas disponíveis já estão liberadas.';

                return;
            }

            const lines = [];

            if (pendingItems.length > 0) {
                lines.push(
                    `${pendingItems.length} item(ns)`
                );
            }

            if (pendingCurrencies.length > 0) {
                lines.push(
                    `${pendingCurrencies.length} lote(s) de moedas`
                );
            }

            if (
                !window.confirm(
                    `Liberar ${lines.join(' e ')} para toda a Party?`
                    + (
                        pendingCurrencies.length > 0
                            ? '\n\nAs moedas serão liberadas como Caixa da Party.'
                            : ''
                    )
                )
            ) {
                return;
            }

            this.lootReleasingAll =
                true;

            this.lootError =
                null;

            try {
                if (pendingItems.length > 0) {
                    await this.lootJson(
                        this.lootReleaseAllItemsUrl(
                            drop.id
                        ),
                        {
                            method:
                                'POST',

                            headers:
                                this.lootHeaders(),
                        }
                    );
                }

                for (
                    const currency
                    of pendingCurrencies
                ) {
                    await this.lootJson(
                        this.lootCurrencyReleaseUrl(
                            drop.id,
                            currency.id
                        ),
                        {
                            method:
                                'POST',

                            headers:
                                this.lootHeaders(true),

                            body:
                                JSON.stringify({
                                    visibility_mode:
                                        'party',

                                    distribution_mode:
                                        'open',

                                    character_ids:
                                        [],
                                }),
                        }
                    );
                }

                const activeId =
                    drop.id;

                this.cancelLootItemRelease();
                this.cancelLootCurrencyRelease();

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;

                this.lootContentTab =
                    'items';

                this.lootRewardMenuOpen =
                    false;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível liberar todas as recompensas.';
            } finally {
                this.lootReleasingAll =
                    false;
            }
        },

        async releaseAllLootItems() {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                drop?.status
                    ===
                    'closed'
                ||
                this.lootReleasingAll
            ) {
                return;
            }

            const pending =
                (drop?.items ?? [])
                    .filter(
                        item =>
                            parseInt(
                                item?.quantity_remaining
                            ) > 0
                            &&
                            item?.release_status
                            !==
                            'released'
                    )
                    .length;

            if (pending <= 0) {
                this.lootError =
                    'Todos os itens disponíveis já estão liberados.';

                return;
            }

            if (
                !window.confirm(
                    `Liberar ${pending} item(ns) restantes para toda a Party?`
                )
            ) {
                return;
            }

            this.lootReleasingAll =
                true;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootReleaseAllItemsUrl(
                        drop.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                const activeId =
                    drop.id;

                this.cancelLootItemRelease();

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível liberar todos os itens.';
            } finally {
                this.lootReleasingAll =
                    false;
            }
        },

        async deleteLootItem(item) {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                this.lootDeletingItemId
                    !==
                    null
            ) {
                return;
            }

            if (
                !this.lootCanDeleteItem(
                    item
                )
            ) {
                this.lootError =
                    'Este item já possui histórico de resgate e deve permanecer no Espólio.';

                return;
            }

            if (
                !window.confirm(
                    `Excluir “${item.name ?? 'este item'}” do Espólio?`
                )
            ) {
                return;
            }

            this.lootDeletingItemId =
                item.id;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootDeleteItemUrl(
                        drop.id,
                        item.id
                    ),
                    {
                        method:
                            'DELETE',

                        headers:
                            this.lootHeaders(),
                    }
                );

                const activeId =
                    drop.id;

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível excluir o item.';
            } finally {
                this.lootDeletingItemId =
                    null;
            }
        },

        async reverseLootClaim(
            item,
            claim
        ) {
            const drop =
                this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !item?.id
                ||
                !claim?.id
                ||
                claim?.reversed_at
                ||
                this.lootReversingClaimId
                    !==
                    null
            ) {
                return;
            }

            const destination =
                this.lootClaimDestinationLabel(
                    claim
                );

            if (
                !window.confirm(
                    `Recolher ${claim.quantity ?? 1}× “${item.name ?? 'item'}”?\n\n${destination}\n\nO item será retirado do inventário atual e voltará a ficar disponível no Espólio.`
                )
            ) {
                return;
            }

            this.lootReversingClaimId =
                claim.id;

            this.lootError =
                null;

            try {
                await this.lootJson(
                    this.lootReverseClaimUrl(
                        drop.id,
                        item.id,
                        claim.id
                    ),
                    {
                        method:
                            'POST',

                        headers:
                            this.lootHeaders(),
                    }
                );

                const activeId =
                    drop.id;

                await this.loadLootWorkspace(
                    true
                );

                this.lootActiveDropId =
                    activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível recolher o resgate.';
            } finally {
                this.lootReversingClaimId =
                    null;
            }
        },

        resetLootCurrencyForm() {
            this.lootCurrencyLabel = '';

            this.lootCurrencyAmounts = {
                copper: 0,
                silver: 0,
                electrum: 0,
                gold: 0,
                platinum: 0,
            };
        },

        async addLootCurrency() {
            const drop = this.lootActiveDrop;

            if (
                !drop?.id
                ||
                drop?.status === 'closed'
                ||
                this.lootAddingCurrency
            ) {
                return;
            }

            const payload = {
                label:
                    String(
                        this.lootCurrencyLabel
                        ?? ''
                    ).trim()
                    || null,
            };

            let hasCoins = false;

            for (const coin of this.lootCoinDefinitions) {
                const amount =
                    Math.max(
                        0,
                        parseInt(
                            this.lootCurrencyAmounts?.[
                                coin.key
                            ]
                        ) || 0
                    );

                payload[coin.key] = amount;

                if (amount > 0) {
                    hasCoins = true;
                }
            }

            if (!hasCoins) {
                this.lootError =
                    'Informe ao menos uma moeda.';

                return;
            }

            this.lootAddingCurrency = true;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.lootCurrencyStoreUrl(
                        drop.id
                    ),
                    {
                        method: 'POST',
                        headers:
                            this.lootHeaders(true),
                        body:
                            JSON.stringify(payload),
                    }
                );

                const activeId = drop.id;

                this.resetLootCurrencyForm();
                this.lootCurrencyFormOpen = false;

                await this.loadLootWorkspace(true);

                this.lootActiveDropId = activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível adicionar as moedas.';
            } finally {
                this.lootAddingCurrency = false;
            }
        },

        beginLootCurrencyRelease(currency) {
            if (!currency?.id) {
                return;
            }

            const id = parseInt(currency.id);

            if (this.lootReleaseCurrencyId === id) {
                this.cancelLootCurrencyRelease();
                return;
            }

            this.lootReleaseCurrencyId = id;

            this.lootCurrencyReleaseVisibility =
                currency?.visibility_mode === 'restricted'
                    ? 'restricted'
                    : 'party';

            this.lootCurrencyDistribution =
                ['split', 'open', 'first_claim']
                    .includes(currency?.distribution_mode)
                    ? currency.distribution_mode
                    : 'open';

            this.lootCurrencyReleaseCharacterIds =
                (Array.isArray(currency?.recipients) ? currency.recipients : [])
                    .map(recipient =>
                        parseInt(
                            recipient?.character_id
                            ?? recipient?.character?.id
                        )
                    )
                    .filter(id => Number.isFinite(id));
        },

        cancelLootCurrencyRelease() {
            this.lootReleaseCurrencyId = null;
            this.lootCurrencyReleaseVisibility = 'party';
            this.lootCurrencyDistribution = 'open';
            this.lootCurrencyReleaseCharacterIds = [];
        },

        lootCurrencyRecipientSelected(characterId) {
            const id = parseInt(characterId);

            return this.lootCurrencyReleaseCharacterIds
                .some(current => parseInt(current) === id);
        },

        toggleLootCurrencyRecipient(characterId) {
            const id = parseInt(characterId);

            if (!Number.isFinite(id)) {
                return;
            }

            if (
                this.lootCurrencyDistribution
                === 'first_claim'
            ) {
                this.lootCurrencyReleaseCharacterIds = [id];
                this.lootCurrencyReleaseVisibility = 'restricted';
                return;
            }

            const index =
                this.lootCurrencyReleaseCharacterIds
                    .findIndex(current => parseInt(current) === id);

            if (index >= 0) {
                this.lootCurrencyReleaseCharacterIds.splice(index, 1);
                return;
            }

            this.lootCurrencyReleaseCharacterIds.push(id);
        },

        setLootCurrencyReleaseMode(mode) {
            this.lootCurrencyDistribution = mode;

            if (mode === 'open') {
                this.lootCurrencyReleaseVisibility = 'party';
                this.lootCurrencyReleaseCharacterIds = [];
                return;
            }

            if (mode === 'first_claim') {
                this.lootCurrencyReleaseVisibility = 'restricted';

                if (this.lootCurrencyReleaseCharacterIds.length > 1) {
                    this.lootCurrencyReleaseCharacterIds = [
                        this.lootCurrencyReleaseCharacterIds[0],
                    ];
                }
                return;
            }

            this.lootCurrencyReleaseVisibility =
                this.lootCurrencyReleaseCharacterIds.length
                    ? 'restricted'
                    : 'party';
        },

        async releaseLootCurrency(currency) {
            const drop = this.lootActiveDrop;

            if (
                !drop?.id
                || !currency?.id
                || this.lootActingCurrencyId !== null
            ) {
                return;
            }

            if (
                this.lootCurrencyDistribution === 'first_claim'
                && this.lootCurrencyReleaseCharacterIds.length !== 1
            ) {
                this.lootError =
                    'Escolha exatamente um personagem para receber estas moedas.';
                return;
            }

            this.lootActingCurrencyId = currency.id;
            this.lootError = null;

            let visibility = 'party';
            let characterIds = [];

            if (this.lootCurrencyDistribution === 'first_claim') {
                visibility = 'restricted';
                characterIds = [
                    this.lootCurrencyReleaseCharacterIds[0],
                ];
            } else if (this.lootCurrencyDistribution === 'split') {
                characterIds = [
                    ...this.lootCurrencyReleaseCharacterIds,
                ];
                visibility = characterIds.length
                    ? 'restricted'
                    : 'party';
            }

            try {
                await this.lootJson(
                    this.lootCurrencyReleaseUrl(
                        drop.id,
                        currency.id
                    ),
                    {
                        method: 'POST',
                        headers: this.lootHeaders(true),
                        body: JSON.stringify({
                            visibility_mode: visibility,
                            distribution_mode:
                                this.lootCurrencyDistribution,
                            character_ids: characterIds,
                        }),
                    }
                );

                const activeId = drop.id;

                this.cancelLootCurrencyRelease();

                await this.loadLootWorkspace(true);
                this.lootActiveDropId = activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível liberar as moedas.';
            } finally {
                this.lootActingCurrencyId = null;
            }
        },

        async withdrawLootCurrency(currency) {
            const drop = this.lootActiveDrop;

            if (
                !drop?.id
                ||
                !currency?.id
                ||
                this.lootActingCurrencyId !== null
            ) {
                return;
            }

            this.lootActingCurrencyId = currency.id;
            this.lootError = null;

            try {
                await this.lootJson(
                    this.lootCurrencyWithdrawUrl(
                        drop.id,
                        currency.id
                    ),
                    {
                        method: 'POST',
                        headers:
                            this.lootHeaders(),
                    }
                );

                const activeId = drop.id;

                await this.loadLootWorkspace(true);

                this.lootActiveDropId = activeId;
            } catch (error) {
                this.lootError =
                    error?.message
                    ?? 'Não foi possível recolher as moedas.';
            } finally {
                this.lootActingCurrencyId = null;
            }
        },

        openDrawer(name) {
            /* Fecha qualquer menu teleportado antes de trocar de gaveta. */
            this.lootDropMenuId =
                null;

            this.lootFolderMenuId =
                null;

            this.drawer =
                name;

            if (
                name
                ===
                'loot'
            ) {
                /*
                | Clicar em Espólios sempre abre a visão geral.
                | Nenhuma pasta ou Espólio é selecionado automaticamente.
                */
                this.lootWorkspaceMode = 'loot';
                this.lootActiveDropId = null;
                this.lootActiveFolderId = null;
                this.lootContentTab = 'items';
                this.lootDropMenuId = null;
                this.lootFolderMenuId = null;

                this.loadLootWorkspace();
            }
        },

        closeDrawer() {
            /*
            | O menu de ações dos Espólios é teleportado para o <body>.
            | Por isso ele precisa ser fechado explicitamente ao sair da gaveta;
            | caso contrário, continuaria visível fora da tela de Espólios.
            */
            this.lootDropMenuId =
                null;

            this.lootFolderMenuId =
                null;

            this.drawer =
                null;

            this.messageTarget =
                null;

            this.characterTarget =
                null;
        },

        openMessage(player) {
            this.messageTarget =
                player;

            this.drawer =
                'messages';
        },

        partyMemberHidden(userId) {
            const parsedUserId =
                Number(
                    userId
                );

            return this.hiddenMemberUserIds
                .some(
                    id =>
                        Number(id)
                        ===
                        parsedUserId
                );
        },

        syncHiddenMemberUserIds(players) {
            if (!Array.isArray(players)) {
                return;
            }

            this.hiddenMemberUserIds =
                players
                    .filter(
                        player =>
                            !!player
                                ?.hidden_from_party
                    )
                    .map(
                        player =>
                            Number(
                                player?.user_id
                            )
                    )
                    .filter(
                        userId =>
                            Number.isFinite(
                                userId
                            )
                    );
        },

        async setPartyMemberVisibility(
            userId,
            hidden,
            playerName = 'Player'
        ) {
            const parsedUserId =
                Number(
                    userId
                );

            if (
                !Number.isFinite(
                    parsedUserId
                )
                ||
                this.memberVisibilityUserId
                !==
                null
            ) {
                return;
            }

            if (
                hidden
                &&
                !window.confirm(
                    `Esconder ${playerName} da Party?\n\nOs outros jogadores não verão este Player nem a ficha ativa dele até você mostrar novamente.`
                )
            ) {
                return;
            }

            this.memberVisibilityUserId =
                parsedUserId;

            try {
                const url =
                    this.memberVisibilityUrlTemplate
                        .replace(
                            '__USER__',
                            String(
                                parsedUserId
                            )
                        );

                const response =
                    await fetch(
                        url,
                        {
                            method:
                                'PATCH',

                            headers: {
                                'Accept':
                                    'application/json',

                                'Content-Type':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    this.csrf,
                            },

                            credentials:
                                'same-origin',

                            body:
                                JSON.stringify({
                                    hidden:
                                        !!hidden,
                                }),
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
                        ??
                        'Não foi possível alterar a visibilidade do Player.'
                    );
                }

                if (data?.hidden_from_party) {
                    if (
                        !this.partyMemberHidden(
                            parsedUserId
                        )
                    ) {
                        this.hiddenMemberUserIds
                            .push(
                                parsedUserId
                            );
                    }
                } else {
                    this.hiddenMemberUserIds =
                        this.hiddenMemberUserIds
                            .filter(
                                id =>
                                    Number(id)
                                    !==
                                    parsedUserId
                            );
                }

                const player =
                    this.players
                        .find(
                            entry =>
                                Number(
                                    entry?.user_id
                                )
                                ===
                                parsedUserId
                        );

                if (player) {
                    player.hidden_from_party =
                        !!data?.hidden_from_party;
                }

                this.liveError =
                    null;
            } catch (error) {
                console.error(
                    'Visibilidade da Party:',
                    error
                );

                window.alert(
                    error?.message
                    ??
                    'Não foi possível alterar a visibilidade do Player.'
                );
            } finally {
                this.memberVisibilityUserId =
                    null;
            }
        },

        async removePartyMember(player) {
            const userId =
                parseInt(
                    player?.user_id
                );

            if (!Number.isFinite(userId)) {
                return;
            }

            if (
                this.removingMemberUserId
                !==
                null
            ) {
                return;
            }

            const playerName =
                String(
                    player?.player_name
                    ?? 'este Player'
                ).trim()
                ||
                'este Player';

            const confirmed =
                window.confirm(
                    `Remover ${playerName} da Party?\n\nAs fichas deste Player serão desvinculadas desta campanha. A conta e os personagens não serão apagados.`
                );

            if (!confirmed) {
                return;
            }

            this.removingMemberUserId =
                userId;

            try {
                const url =
                    this.removeMemberUrlTemplate
                        .replace(
                            '__USER__',
                            String(userId)
                        );

                const response =
                    await fetch(
                        url,
                        {
                            method:
                                'DELETE',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    this.csrf,
                            },

                            credentials:
                                'same-origin',
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
                        ??
                        'Não foi possível remover o Player da Party.'
                    );
                }

                this.players =
                    this.players.filter(
                        entry =>
                            Number(
                                entry?.user_id
                            )
                            !==
                            userId
                    );

                this.hiddenMemberUserIds =
                    this.hiddenMemberUserIds
                        .filter(
                            id =>
                                Number(id)
                                !==
                                userId
                        );

                if (
                    Number(
                        this.characterTarget?.user_id
                    )
                    ===
                    userId
                ) {
                    this.closeCharacterModal();
                }

                if (
                    Number(
                        this.messageTarget?.user_id
                    )
                    ===
                    userId
                ) {
                    this.messageTarget =
                        null;

                    if (
                        this.drawer
                        ===
                        'messages'
                    ) {
                        this.drawer =
                            null;
                    }
                }

                this.liveError =
                    null;
            } catch (error) {
                console.error(
                    'Remoção da Party:',
                    error
                );

                window.alert(
                    error?.message
                    ??
                    'Não foi possível remover o Player da Party.'
                );
            } finally {
                this.removingMemberUserId =
                    null;
            }
        },

        openCharacterSummary(player) {
            if (!player?.character) {
                return;
            }

            this.drawer =
                null;

            this.characterTarget =
                player;

            this.characterTab =
                'summary';

            this.inventoryItemOpenId =
                null;

            this.characterModalOpen =
                true;
        },

        closeCharacterModal() {
            this.characterModalOpen =
                false;

            this.characterTarget =
                null;

            this.characterTab =
                'summary';

            this.inventoryItemOpenId =
                null;
        },

        toggleInventoryItem(item) {
            this.inventoryItemOpenId =
                Number(
                    this.inventoryItemOpenId
                )
                ===
                Number(
                    item?.id
                )
                    ? null
                    : item?.id;
        },

        equippedItems() {
            return (
                this.characterTarget
                    ?.character
                    ?.inventory
                ?? []
            ).filter(
                item =>
                    !!item.equipped
            );
        },

        storedItems() {
            return (
                this.characterTarget
                    ?.character
                    ?.inventory
                ?? []
            ).filter(
                item =>
                    !item.equipped
            );
        },

        inventoryTypeLabel(type) {
            return {
                mundane:
                    'Comum',

                wonderful:
                    'Maravilhoso',

                technological:
                    'Tecnológico',
            }[
                String(
                    type
                    ?? ''
                )
            ]
            ?? 'Item';
        },

        portraitStyle(character) {
            const hp =
                Math.max(
                    0,
                    Math.min(
                        100,
                        Number(
                            character?.hp_percent
                            ?? 100
                        )
                    )
                );

            const grayscale =
                Math.round(
                    100
                    -
                    hp
                );

            const saturation =
                Math.max(
                    0.22,
                    hp
                    /
                    100
                );

            const brightness =
                hp <= 0
                    ? 0.58
                    : (
                        0.72
                        +
                        (
                            hp
                            /
                            100
                        )
                        *
                        0.28
                    );

            return `grayscale(${grayscale}%) saturate(${saturation}) brightness(${brightness})`;
        },

        signed(value) {
            const number =
                Number(
                    value
                    ?? 0
                );

            return number >= 0
                ? `+${number}`
                : `${number}`;
        },

        formatDeathDate(value) {
            if (!value) {
                return 'Data não registrada';
            }

            const date =
                new Date(value);

            if (Number.isNaN(date.getTime())) {
                return 'Data não registrada';
            }

            return new Intl.DateTimeFormat(
                'pt-BR',
                {
                    dateStyle:
                        'short',

                    timeStyle:
                        'short',
                }
            ).format(
                date
            );
        },

        statusLabel(character) {
            if (character?.death_save_dead) {
                return 'Morto';
            }

            if (character?.death_save_stabilized) {
                return 'Estabilizado';
            }

            if (character?.death_saves_active) {
                return 'Salv. morte';
            }

            switch (character?.health_state) {
                case 'critical':
                    return 'Crítico';

                case 'wounded':
                    return 'Ferido';

                case 'down':
                    return 'Inconsciente';

                case 'dead':
                    return 'Morto';

                default:
                    return 'Estável';
            }
        },

        cardClass(character) {
            if (character?.death_save_dead) {
                return 'border-[#53150f] bg-[#eadbc8] shadow-[0_0_0_1px_rgba(83,21,15,.08)]';
            }

            if (
                character?.death_saves_active
                ||
                character?.health_state === 'down'
            ) {
                return 'border-[#8a2519] bg-[#fff8f4] shadow-[0_0_0_1px_rgba(138,37,25,.08)]';
            }

            if (character?.health_state === 'critical') {
                return 'border-[#a33a2f]/70 bg-[#fff9f5]';
            }

            if (character?.health_state === 'wounded') {
                return 'border-[#c38b48]/65 bg-[#fffaf2]';
            }

            return 'border-[#cdbb9f]/65 bg-[#fffdf8]';
        },

        statusClass(character) {
            if (character?.death_save_dead) {
                return 'bg-[#53150f] text-[#fffaf2]';
            }

            if (
                character?.death_saves_active
                ||
                character?.health_state === 'down'
            ) {
                return 'bg-[#8a2519] text-white';
            }

            if (character?.death_save_stabilized) {
                return 'bg-[#e7ded2] text-[#53150f]';
            }

            if (character?.health_state === 'critical') {
                return 'bg-[#f0d1ca] text-[#7a1f17]';
            }

            if (character?.health_state === 'wounded') {
                return 'bg-[#f0dfbf] text-[#7a4c18]';
            }

            return 'bg-[#e4eee3] text-[#315b34]';
        },

        hpFillClass(character) {
            if (Number(character?.current_hp ?? 0) <= 0) {
                return 'bg-[#8a2519]';
            }

            if (Number(character?.hp_percent ?? 0) <= 25) {
                return 'bg-red-700';
            }

            if (Number(character?.hp_percent ?? 0) <= 50) {
                return 'bg-amber-600';
            }

            return 'bg-emerald-700';
        },
    }"
    x-on:keydown.escape.window="
        if (
            characterModalOpen
        ) {
            closeCharacterModal();
        } else if (
            drawer
        ) {
            closeDrawer();
        }
    "
    x-on:keydown.ctrl.s.window.prevent="
        saveNotes()
    "
    x-on:keydown.meta.s.window.prevent="
        saveNotes()
    "
    class="
        relative
        mt-2
        flex
        min-h-0
        flex-1
        flex-col
        pr-[58px]
    "
>
    {{-- ================================================================
         GATILHOS DAS GAVETAS — LATERAL DIREITA
    ================================================================= --}}
    <nav
        class="
            absolute
            right-1
            top-1/2
            z-40
            flex
            -translate-y-1/2
            flex-col
            gap-2
        "
        aria-label="Ferramentas da campanha"
    >
        <button
            type="button"
            x-on:click="openDrawer('sheets')"
            class="
                group
                relative
                flex
                h-11
                w-11
                items-center
                justify-center
                rounded-xl
                border
                border-[#cdbb9f]/65
                bg-[#fffdf8]/95
                text-[#7d604d]
                shadow-[0_5px_16px_rgba(83,21,15,.07)]
                backdrop-blur
                transition
                hover:-translate-x-0.5
                hover:border-[#6b1d14]/35
                hover:bg-[#eadbc8]
                hover:text-[#53150f]
            "
            :class="
                drawer === 'sheets'
                    ? 'border-[#6b1d14]/45 bg-[#eadbc8] text-[#53150f]'
                    : ''
            "
            aria-label="Abrir Fichas"
        >
            <svg
                class="h-[19px] w-[19px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M7 3.75h7.5L18 7.25v13H7z" />
                <path d="M14.5 3.75v3.5H18" />
                <path d="M9.5 11h6M9.5 14h6M9.5 17h4" />
            </svg>

            <span
                class="
                    pointer-events-none
                    absolute
                    right-[calc(100%+8px)]
                    top-1/2
                    hidden
                    -translate-y-1/2
                    whitespace-nowrap
                    rounded-lg
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-2.5
                    py-1.5
                    text-[9px]
                    font-black
                    uppercase
                    tracking-[0.08em]
                    text-[#6b1d14]
                    shadow-lg
                    group-hover:block
                "
            >
                Fichas
            </span>
        </button>

        <button
            type="button"
            x-on:click="openDrawer('loot')"
            class="
                group
                relative
                flex
                h-11
                w-11
                items-center
                justify-center
                rounded-xl
                border
                border-[#cdbb9f]/65
                bg-[#fffdf8]/95
                text-[#7d604d]
                shadow-[0_5px_16px_rgba(83,21,15,.07)]
                backdrop-blur
                transition
                hover:-translate-x-0.5
                hover:border-[#6b1d14]/35
                hover:bg-[#eadbc8]
                hover:text-[#53150f]
            "
            :class="
                drawer === 'loot'
                    ? 'border-[#6b1d14]/45 bg-[#eadbc8] text-[#53150f]'
                    : ''
            "
            aria-label="Abrir Espólios"
        >
            <svg
                class="h-[20px] w-[20px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M4.5 9.25h15v10h-15z" />
                <path d="M7 9.25V6.5h10v2.75" />
                <path d="M4.5 13h15" />
                <path d="M10 13v2h4v-2" />
            </svg>

            <span
                class="
                    pointer-events-none
                    absolute
                    right-[calc(100%+8px)]
                    top-1/2
                    hidden
                    -translate-y-1/2
                    whitespace-nowrap
                    rounded-lg
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-2.5
                    py-1.5
                    text-[9px]
                    font-black
                    uppercase
                    tracking-[0.08em]
                    text-[#6b1d14]
                    shadow-lg
                    group-hover:block
                "
            >
                Espólios
            </span>
        </button>

        <button
            type="button"
            x-on:click="openDrawer('combats')"
            class="
                group
                relative
                flex
                h-11
                w-11
                items-center
                justify-center
                rounded-xl
                border
                border-[#cdbb9f]/65
                bg-[#fffdf8]/95
                text-[#7d604d]
                shadow-[0_5px_16px_rgba(83,21,15,.07)]
                backdrop-blur
                transition
                hover:-translate-x-0.5
                hover:border-[#6b1d14]/35
                hover:bg-[#eadbc8]
                hover:text-[#53150f]
            "
            :class="
                drawer === 'combats'
                    ? 'border-[#6b1d14]/45 bg-[#eadbc8] text-[#53150f]'
                    : ''
            "
            aria-label="Abrir Combates"
        >
            <svg
                class="h-[20px] w-[20px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M6 5l13 13" />
                <path d="M18 5L5 18" />
                <path d="M15.5 4.5l4 4" />
                <path d="M4.5 15.5l4 4" />
                <path d="M4.5 8.5l4-4" />
                <path d="M15.5 19.5l4-4" />
            </svg>

            <span
                class="
                    pointer-events-none
                    absolute
                    right-[calc(100%+8px)]
                    top-1/2
                    hidden
                    -translate-y-1/2
                    whitespace-nowrap
                    rounded-lg
                    border
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-2.5
                    py-1.5
                    text-[9px]
                    font-black
                    uppercase
                    tracking-[0.08em]
                    text-[#6b1d14]
                    shadow-lg
                    group-hover:block
                "
            >
                Combates
            </span>
        </button>
    </nav>


    {{-- ================================================================
         MESA — PARTY NO TOPO + CADERNO
    ================================================================= --}}
    <div
        class="
            mt-1
            flex
            min-h-0
            flex-1
            flex-col
            gap-3
        "
    >
        {{-- ------------------------------------------------------------
             RETRATOS DA PARTY — TOPO CENTRAL
        ------------------------------------------------------------- --}}
        <section
            class="
                shrink-0
                overflow-hidden
            "
        >
            <div
                class="
                    flex
                    min-h-[168px]
                    min-w-0
                    items-center
                    justify-center
                    gap-3
                    overflow-x-auto
                    overscroll-x-contain
                    px-4
                    py-2
                "
            >
                <template
                    x-for="
                        player
                        in
                        players
                    "
                    :key="
                        player.user_id
                    "
                >
                    <div
                        role="button"
                        tabindex="0"
                        x-on:click="
                            openCharacterSummary(
                                player
                            )
                        "
                        x-on:keydown.enter.prevent="
                            openCharacterSummary(
                                player
                            )
                        "
                        x-on:keydown.space.prevent="
                            openCharacterSummary(
                                player
                            )
                        "
                        class="
                            group
                            relative
                            h-[148px]
                            w-[112px]
                            shrink-0
                            overflow-hidden
                            rounded-[12px]
                            border
                            border-[#b08c62]/65
                            bg-[#2b211d]
                            text-left
                            shadow-[0_4px_12px_rgba(55,35,25,.14)]
                            transition
                            duration-150
                            hover:-translate-y-1
                            hover:border-[#6b1d14]
                            hover:shadow-[0_8px_20px_rgba(55,35,25,.2)]
                            sm:h-[160px]
                            sm:w-[120px]
                        "
                    >
                        <template
                            x-if="
                                player.character
                            "
                        >
                            <div
                                class="
                                    absolute
                                    inset-0
                                "
                            >
                                <template
                                    x-if="
                                        player
                                            .character
                                            .image_url
                                    "
                                >
                                    <img
                                        :src="
                                            player
                                                .character
                                                .image_url
                                        "
                                        :alt="
                                            player
                                                .character
                                                .name
                                        "
                                        class="
                                            absolute
                                            inset-0
                                            h-full
                                            w-full
                                            object-cover
                                            object-top
                                            transition-[filter,transform]
                                            duration-500
                                            group-hover:scale-[1.03]
                                        "
                                        :style="
                                            `filter: ${portraitStyle(player.character)}`
                                        "
                                    >
                                </template>

                                <template
                                    x-if="
                                        !player
                                            .character
                                            .image_url
                                    "
                                >
                                    <div
                                        class="
                                            absolute
                                            inset-0
                                            flex
                                            items-center
                                            justify-center
                                            bg-[#eadbc8]
                                            font-serif
                                            text-2xl
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            player
                                                .character
                                                .name
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                            ?? '?'
                                        "
                                    ></div>
                                </template>


                                <div
                                    class="
                                        absolute
                                        inset-0
                                        bg-gradient-to-t
                                        from-black/90
                                        via-black/10
                                        to-transparent
                                    "
                                ></div>


                                <span
                                    x-show="
                                        Number(
                                            player
                                                .character
                                                .current_hp
                                        )
                                        >
                                        0
                                    "
                                    class="
                                        absolute
                                        right-2
                                        top-2
                                        flex
                                        h-9
                                        w-9
                                        flex-col
                                        items-center
                                        justify-center
                                        rounded-md
                                        border
                                        border-white/45
                                        bg-[#f5ecdc]/90
                                        shadow-sm
                                    "
                                    title="Percepção Passiva"
                                >
                                    <small
                                        class="
                                            text-[6px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                    >
                                        PP
                                    </small>

                                    <strong
                                        class="
                                            -mt-0.5
                                            font-serif
                                            text-[15px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            player
                                                .character
                                                .passive_perception
                                        "
                                    ></strong>
                                </span>


                                <span
                                    x-show="
                                        partyMemberHidden(
                                            player.user_id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        absolute
                                        left-2
                                        top-2
                                        rounded-lg
                                        border
                                        border-white/35
                                        bg-black/65
                                        px-2
                                        py-1
                                        text-[6px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-white
                                        backdrop-blur-sm
                                    "
                                >
                                    Oculto
                                </span>


                                <span
                                    x-show="
                                        Number(
                                            player
                                                .character
                                                .current_hp
                                        )
                                        <=
                                        0
                                    "
                                    x-cloak
                                    class="
                                        absolute
                                        right-2
                                        top-2
                                        rounded-lg
                                        bg-[#6b1d14]/95
                                        px-2
                                        py-1.5
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-white
                                    "
                                    x-text="
                                        statusLabel(
                                            player.character
                                        )
                                    "
                                ></span>


                                <span
                                    x-show="
                                        player
                                            .character
                                            .has_died
                                    "
                                    x-cloak
                                    class="
                                        absolute
                                        left-1
                                        top-7
                                        rounded-md
                                        border
                                        border-white/35
                                        bg-black/70
                                        px-1.5
                                        py-1
                                        text-[5px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-white
                                    "
                                    :title="
                                        `${player.character.death_count ?? 1} morte(s) registrada(s)`
                                    "
                                >
                                    †
                                    <span
                                        x-text="
                                            player
                                                .character
                                                .death_count
                                            ?? 1
                                        "
                                    ></span>
                                </span>


                                <div
                                    class="
                                        absolute
                                        bottom-0
                                        left-0
                                        right-0
                                        px-2.5
                                        pb-2.5
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            font-serif
                                            text-[12px]
                                            font-black
                                            leading-tight
                                            text-white
                                            drop-shadow
                                        "
                                        x-text="
                                            player
                                                .character
                                                .name
                                        "
                                    ></p>

                                    <div
                                        class="
                                            mt-1
                                            flex
                                            items-center
                                            gap-1
                                        "
                                    >
                                        <div
                                            class="
                                                h-1
                                                min-w-0
                                                flex-1
                                                overflow-hidden
                                                rounded-full
                                                bg-white/25
                                            "
                                        >
                                            <div
                                                class="
                                                    h-full
                                                    rounded-full
                                                    transition-all
                                                    duration-300
                                                "
                                                :class="
                                                    hpFillClass(
                                                        player.character
                                                    )
                                                "
                                                :style="
                                                    `width: ${player.character.hp_percent}%`
                                                "
                                            ></div>
                                        </div>

                                        <span
                                            class="
                                                shrink-0
                                                text-[6px]
                                                font-black
                                                text-white
                                                drop-shadow
                                            "
                                        >
                                            <span
                                                x-text="
                                                    player
                                                        .character
                                                        .current_hp
                                                "
                                            ></span>

                                            /
                                            <span
                                                x-text="
                                                    player
                                                        .character
                                                        .max_hp
                                                "
                                            ></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>


                        <template
                            x-if="
                                !player.character
                            "
                        >
                            <div
                                class="
                                    flex
                                    h-full
                                    flex-col
                                    items-center
                                    justify-center
                                    gap-1
                                    bg-[#eadbc8]
                                    px-2
                                    text-center
                                "
                            >
                                <span
                                    class="
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#8c6239]
                                    "
                                    x-text="
                                        player
                                            .player_name
                                            ?.charAt(0)
                                            ?.toUpperCase()
                                        ?? '?'
                                    "
                                ></span>

                                <span
                                    class="
                                        text-[5.5px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#8c6239]
                                    "
                                >
                                    Sem ficha ativa
                                </span>
                            </div>
                        </template>

                    </div>
                </template>


                <div
                    x-show="
                        loadedOnce
                        &&
                        players.length
                        ===
                        0
                    "
                    class="
                        flex
                        h-[96px]
                        min-w-full
                        items-center
                        justify-center
                    "
                >
                    <p
                        class="
                            text-[11px]
                            font-semibold
                            text-[#9a806d]
                        "
                    >
                        Nenhum jogador na mesa.
                    </p>
                </div>
            </div>
        </section>


        {{-- ------------------------------------------------------------
             CADERNO — FOCO PRINCIPAL
        ------------------------------------------------------------- --}}
        <section
            class="
                flex
                min-h-0
                flex-1
                flex-col
                overflow-hidden
                rounded-[22px]
                border
                border-[#cdbb9f]/55
                bg-[#fffdf8]
                shadow-[0_12px_36px_rgba(83,21,15,.06)]
            "
        >
            <header
                class="
                    shrink-0
                    border-b
                    border-[#d8c7ab]/45
                    bg-[#fffdf8]
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-4
                        px-6
                        pb-3
                        pt-5
                    "
                >
                    <div
                        class="
                            flex
                            min-w-0
                            items-center
                            gap-2.5
                        "
                    >
                        <h3
                            class="
                                font-serif
                                text-[22px]
                                font-black
                                tracking-[-0.02em]
                                text-[#53150f]
                            "
                        >
                            Caderno
                        </h3>

                        <span
                            class="
                                inline-flex
                                h-6
                                w-6
                                items-center
                                justify-center
                                rounded-full
                                bg-[#f3eadf]
                                text-[#9a806d]
                            "
                            title="Privado para o Mestre"
                        >
                            <svg
                                class="h-3.5 w-3.5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <rect
                                    x="5"
                                    y="10"
                                    width="14"
                                    height="10"
                                    rx="2"
                                ></rect>
                                <path
                                    d="M8 10V7a4 4 0 018 0v3"
                                ></path>
                            </svg>
                        </span>
                    </div>

                    <div
                        class="
                            flex
                            shrink-0
                            items-center
                            gap-2
                            rounded-full
                            bg-[#f8f3ec]
                            px-3
                            py-1.5
                        "
                        :title="
                            notesError
                            ?? 'Salvamento automático'
                        "
                    >
                        <span
                            class="
                                h-2
                                w-2
                                rounded-full
                                transition-colors
                            "
                            :class="{
                                'bg-emerald-600':
                                    notesStatus === 'saved',

                                'bg-amber-500':
                                    notesStatus === 'dirty'
                                    ||
                                    notesStatus === 'saving',

                                'bg-red-700':
                                    notesStatus === 'error',
                            }"
                        ></span>

                        <span
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#8c6239]
                            "
                            x-text="
                                notesStatusLabel()
                            "
                        ></span>
                    </div>
                </div>

                <div
                    class="
                        flex
                        min-w-0
                        items-center
                        gap-2
                        px-6
                        pb-4
                    "
                >
                    <div
                        class="
                            flex
                            min-w-0
                            flex-1
                            gap-2
                            overflow-x-auto
                            pb-0.5
                        "
                    >
                        <template
                            x-for="
                                (page, index)
                                in
                                notePages
                            "
                            :key="
                                page.id
                            "
                        >
                            <button
                                type="button"
                                x-on:click="
                                    selectNotePage(
                                        page.id
                                    )
                                "
                                class="
                                    max-w-[230px]
                                    shrink-0
                                    truncate
                                    rounded-full
                                    border
                                    px-4
                                    py-2
                                    text-[10px]
                                    font-black
                                    transition
                                "
                                :class="
                                    page.id
                                    ===
                                    activeNoteId
                                        ? 'border-[#6b1d14] bg-[#6b1d14] text-[#fffaf2] shadow-sm'
                                        : 'border-[#d8c7ab]/70 bg-[#faf7f1] text-[#76553f] hover:border-[#b08c62] hover:bg-[#f4ecdf]'
                                "
                                x-text="
                                    noteLabel(
                                        page,
                                        index
                                    )
                                "
                            ></button>
                        </template>
                    </div>

                    <button
                        type="button"
                        x-on:click="
                            addNotePage()
                        "
                        class="
                            flex
                            h-9
                            w-9
                            shrink-0
                            items-center
                            justify-center
                            rounded-full
                            border
                            border-[#cdbb9f]/70
                            bg-[#fffdf8]
                            text-base
                            font-black
                            text-[#6b1d14]
                            transition
                            hover:border-[#6b1d14]/35
                            hover:bg-[#f7f0e6]
                        "
                        title="Nova página"
                    >
                        +
                    </button>
                </div>
            </header>

            <div
                class="
                    flex
                    min-h-0
                    flex-1
                    flex-col
                "
            >
                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        gap-3
                        px-7
                        pb-3
                        pt-5
                    "
                >
                    <input
                        x-ref="
                            noteTitle
                        "
                        type="text"
                        maxlength="120"
                        :value="
                            editorTitle
                        "
                        x-on:input="
                            editorTitle =
                                $event.target.value;

                            editorChanged();
                        "
                        x-on:blur="
                            queueNotesSave(
                                false,
                                300
                            )
                        "
                        placeholder="Título da página"
                        class="
                            min-w-0
                            flex-1
                            border-0
                            bg-transparent
                            p-0
                            font-serif
                            text-[30px]
                            font-black
                            leading-tight
                            tracking-[-0.025em]
                            text-[#53150f]
                            placeholder:text-[#b5a18f]
                            focus:ring-0
                        "
                    >

                    <span
                        class="
                            shrink-0
                            text-[9px]
                            font-black
                            tabular-nums
                            text-[#a28772]
                        "
                    >
                        <span
                            x-text="
                                notePages.findIndex(
                                    page =>
                                        page.id
                                        ===
                                        activeNoteId
                                )
                                +
                                1
                            "
                        ></span>

                        /
                        <span
                            x-text="
                                notePages.length
                            "
                        ></span>
                    </span>

                    <button
                        type="button"
                        x-show="
                            notePages.length
                            >
                            1
                        "
                        x-cloak
                        x-on:click="
                            removeActiveNotePage()
                        "
                        class="
                            flex
                            h-8
                            w-8
                            shrink-0
                            items-center
                            justify-center
                            rounded-full
                            text-base
                            text-[#a28772]
                            transition
                            hover:bg-red-50
                            hover:text-red-700
                        "
                        title="Excluir página"
                    >
                        ×
                    </button>
                </div>

                <div
                    class="
                        relative
                        min-h-0
                        flex-1
                        border-t
                        border-[#eee4d7]
                    "
                >
                    <textarea
                        :value="
                            editorContent
                        "
                        x-on:input="
                            editorContent =
                                $event.target.value;

                            editorChanged();
                        "
                        x-on:blur="
                            queueNotesSave(
                                false,
                                300
                            )
                        "
                        placeholder="Anote nomes, pistas, decisões, lembretes da sessão..."
                        spellcheck="true"
                        class="
                            absolute
                            inset-0
                            h-full
                            w-full
                            resize-none
                            overflow-y-auto
                            border-0
                            bg-[#fffdf8]
                            px-8
                            py-6
                            font-serif
                            text-[18px]
                            leading-9
                            text-[#432c21]
                            placeholder:text-[#b3a08e]/70
                            focus:ring-0
                        "
                    ></textarea>
                </div>
            </div>
        </section>
    </div>


    {{-- ================================================================
         PERSONAGEM — CARTA CENTRAL
    ================================================================= --}}
    <div
        x-show="
            characterModalOpen
            &&
            characterTarget
                ?.character
        "
        x-cloak
        class="
            fixed
            inset-0
            z-[180]
            flex
            items-center
            justify-center
            overflow-hidden
            p-4
        "
    >
        <div
            class="
                absolute
                inset-0
                bg-black/90
                backdrop-blur-[2px]
            "
            style="
                background-color:
                    rgba(8, 6, 5, 0.88);
            "
            x-on:click="
                closeCharacterModal()
            "
        ></div>


        <section
            x-transition:enter="
                transition
                ease-out
                duration-150
            "
            x-transition:enter-start="
                scale-95
                opacity-0
            "
            x-transition:enter-end="
                scale-100
                opacity-100
            "
            x-transition:leave="
                transition
                ease-in
                duration-100
            "
            x-transition:leave-start="
                scale-100
                opacity-100
            "
            x-transition:leave-end="
                scale-95
                opacity-0
            "
            class="
                relative
                z-10
                flex
                max-h-[calc(100dvh-32px)]
                w-full
                max-w-[690px]
                flex-col
                overflow-hidden
                rounded-[22px]
                border
                border-[#b08c62]/55
                bg-[#f4f1e8]
                shadow-[0_28px_80px_rgba(0,0,0,.38)]
            "
            x-on:click.stop
        >
            <template
                x-if="
                    characterTarget
                        ?.character
                "
            >
                <div
                    class="
                        flex
                        min-h-0
                        flex-1
                        flex-col
                    "
                >
                    {{-- CABEÇALHO --}}
                    <header
                        class="
                            relative
                            flex
                            h-[82px]
                            shrink-0
                            items-center
                            gap-3.5
                            overflow-hidden
                            border-b
                            border-[#cdbb9f]/60
                            bg-[#eadbc8]
                            px-4
                        "
                    >
                        <div
                            aria-hidden="true"
                            class="
                                absolute
                                inset-x-0
                                top-0
                                h-[3px]
                                bg-[#6b1d14]
                            "
                        ></div>
                        <div
                            class="
                                flex
                                h-[62px]
                                w-[48px]
                                shrink-0
                                items-center
                                justify-center
                                overflow-hidden
                                rounded-[10px]
                                border
                                border-[#a97d55]/70
                                bg-[#eadbc8]
                                font-serif
                                text-lg
                                font-black
                                text-[#53150f]
                            "
                        >
                            <template
                                x-if="
                                    characterTarget
                                        .character
                                        .image_url
                                "
                            >
                                <img
                                    :src="
                                        characterTarget
                                            .character
                                            .image_url
                                    "
                                    :alt="
                                        characterTarget
                                            .character
                                            .name
                                    "
                                    class="
                                        h-full
                                        w-full
                                        object-cover
                                        object-top
                                    "
                                    :style="
                                        `filter: ${portraitStyle(characterTarget.character)}`
                                    "
                                >
                            </template>

                            <template
                                x-if="
                                    !characterTarget
                                        .character
                                        .image_url
                                "
                            >
                                <span
                                    x-text="
                                        characterTarget
                                            .character
                                            .name
                                            ?.charAt(0)
                                            ?.toUpperCase()
                                        ?? '?'
                                    "
                                ></span>
                            </template>
                        </div>


                        <div
                            class="
                                min-w-0
                                flex-1
                            "
                        >
                            <p
                                class="
                                    truncate
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.13em]
                                    text-[#8c6239]
                                "
                                x-text="
                                    characterTarget
                                        .player_name
                                "
                            ></p>

                            <h2
                                class="
                                    mt-0.5
                                    truncate
                                    font-serif
                                    text-[22px]
                                    font-black
                                    leading-tight
                                    text-[#53150f]
                                "
                                x-text="
                                    characterTarget
                                        .character
                                        .name
                                "
                            ></h2>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-wider
                                    text-[#8c6239]
                                "
                            >
                                <span
                                    x-text="
                                        characterTarget
                                            .character
                                            .class_name
                                    "
                                ></span>

                                <span>
                                    · Nv.
                                </span>

                                <span
                                    x-text="
                                        characterTarget
                                            .character
                                            .level
                                    "
                                ></span>
                            </p>
                        </div>


                        <button
                            type="button"
                            x-on:click="
                                closeCharacterModal()
                            "
                            class="
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                text-xl
                                text-[#8c6239]
                                transition
                                hover:bg-[#dbc7ae]
                                hover:text-[#53150f]
                            "
                        >
                            ×
                        </button>
                    </header>


                    {{-- ABAS --}}
                    <div
                        class="
                            flex
                            h-11
                            shrink-0
                            items-center
                            gap-1.5
                            border-b
                            border-[#cdbb9f]/55
                            bg-[#fffdf8]
                            px-3.5
                        "
                    >
                        <button
                            type="button"
                            x-on:click="
                                characterTab =
                                    'summary'
                            "
                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-wider
                                transition
                            "
                            :class="
                                characterTab
                                ===
                                'summary'
                                    ? 'bg-[#6b1d14] text-[#fffaf2]'
                                    : 'text-[#7d604d] hover:bg-[#f7f0e6]'
                            "
                        >
                            Resumo
                        </button>

                        <button
                            type="button"
                            x-on:click="
                                characterTab =
                                    'inventory'
                            "
                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-wider
                                transition
                            "
                            :class="
                                characterTab
                                ===
                                'inventory'
                                    ? 'bg-[#6b1d14] text-[#fffaf2]'
                                    : 'text-[#7d604d] hover:bg-[#f7f0e6]'
                            "
                        >
                            Inventário

                            <span
                                class="
                                    ml-1
                                    opacity-65
                                "
                                x-text="
                                    characterTarget
                                        .character
                                        .inventory
                                        ?.length
                                    ?? 0
                                "
                            ></span>
                        </button>
                    </div>


                    {{-- =====================================================
                         RESUMO — SEM SCROLL
                    ====================================================== --}}
                    <div
                        x-show="
                            characterTab
                            ===
                            'summary'
                        "
                        class="
                            shrink-0
                            p-3.5
                        "
                    >
                        {{-- PAINEL VITAL --}}
                        <section
                            class="
                                overflow-hidden
                                rounded-[15px]
                                border
                                border-[#cdbb9f]/60
                                bg-[#fffdf8]
                                shadow-[0_3px_10px_rgba(83,21,15,.035)]
                            "
                        >
                            <div
                                class="
                                    grid
                                    grid-cols-[minmax(0,1fr)_96px_104px]
                                    divide-x
                                    divide-[#dfd0ba]/70
                                "
                            >
                                <div
                                    class="
                                        px-4
                                        py-3
                                    "
                                >
                                    <div
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                            gap-3
                                        "
                                    >
                                        <div>
                                            <p
                                                class="
                                                    text-[9px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.10em]
                                                    text-[#8c6239]
                                                "
                                            >
                                                Pontos de Vida
                                            </p>

                                            <p
                                                class="
                                                    mt-1
                                                    font-serif
                                                    text-[26px]
                                                    font-black
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                            >
                                                <span
                                                    x-text="
                                                        characterTarget
                                                            .character
                                                            .current_hp
                                                    "
                                                ></span>

                                                <span
                                                    class="
                                                        mx-0.5
                                                        text-[14px]
                                                        text-[#b09a88]
                                                    "
                                                >
                                                    /
                                                </span>

                                                <span
                                                    x-text="
                                                        characterTarget
                                                            .character
                                                            .max_hp
                                                    "
                                                ></span>
                                            </p>
                                        </div>

                                        <div
                                            class="
                                                flex
                                                flex-col
                                                items-end
                                                gap-1
                                            "
                                        >
                                            <span
                                                class="
                                                    rounded-full
                                                    px-2
                                                    py-1
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.06em]
                                                "
                                                :class="
                                                    statusClass(
                                                        characterTarget.character
                                                    )
                                                "
                                                x-text="
                                                    statusLabel(
                                                        characterTarget.character
                                                    )
                                                "
                                            ></span>

                                            <span
                                                x-show="
                                                    characterTarget
                                                        .character
                                                        .temporary_hp
                                                    >
                                                    0
                                                "
                                                x-cloak
                                                class="
                                                    rounded-md
                                                    bg-[#f1df9f]/75
                                                    px-2
                                                    py-1
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    text-[#765216]
                                                "
                                            >
                                                Temp +
                                                <span
                                                    x-text="
                                                        characterTarget
                                                            .character
                                                            .temporary_hp
                                                    "
                                                ></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            mt-3
                                            h-2.5
                                            overflow-hidden
                                            rounded-full
                                            border
                                            border-[#d5c3aa]/50
                                            bg-[#e6ddd0]
                                        "
                                    >
                                        <div
                                            class="
                                                h-full
                                                rounded-full
                                                transition-all
                                                duration-300
                                            "
                                            :class="
                                                hpFillClass(
                                                    characterTarget.character
                                                )
                                            "
                                            :style="
                                                `width: ${characterTarget.character.hp_percent}%`
                                            "
                                        ></div>
                                    </div>
                                </div>


                                <div
                                    class="
                                        flex
                                        flex-col
                                        items-center
                                        justify-center
                                        bg-[#fbf8f1]
                                        px-2
                                        text-center
                                    "
                                >
                                    <span
                                        class="
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.10em]
                                            text-[#8c6239]
                                        "
                                    >
                                        CA
                                    </span>

                                    <strong
                                        class="
                                            mt-1
                                            font-serif
                                            text-[28px]
                                            font-black
                                            leading-none
                                            text-[#53150f]
                                        "
                                        x-text="
                                            characterTarget
                                                .character
                                                .armor_class
                                        "
                                    ></strong>
                                </div>


                                <div
                                    class="
                                        flex
                                        flex-col
                                        items-center
                                        justify-center
                                        bg-[#fbf8f1]
                                        px-2
                                        text-center
                                    "
                                >
                                    <span
                                        class="
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Percepção
                                    </span>

                                    <strong
                                        class="
                                            mt-1
                                            font-serif
                                            text-[28px]
                                            font-black
                                            leading-none
                                            text-[#53150f]
                                        "
                                        x-text="
                                            characterTarget
                                                .character
                                                .passive_perception
                                        "
                                    ></strong>

                                    <span
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-bold
                                            uppercase
                                            tracking-wider
                                            text-[#a08a78]
                                        "
                                    >
                                        passiva
                                    </span>
                                </div>
                            </div>
                        </section>


                        {{-- ATRIBUTOS + SALVAMENTOS --}}
                        <section
                            class="
                                mt-2.5
                                overflow-hidden
                                rounded-[15px]
                                border
                                border-[#cdbb9f]/60
                                bg-[#fffdf8]
                                shadow-[0_3px_10px_rgba(83,21,15,.03)]
                            "
                        >
                            <header
                                class="
                                    flex
                                    h-9
                                    items-center
                                    justify-between
                                    gap-3
                                    border-b
                                    border-[#dfd0ba]/70
                                    bg-[#f7f0e6]
                                    px-3.5
                                "
                            >
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.11em]
                                        text-[#53150f]
                                    "
                                >
                                    Atributos
                                </p>

                                <div
                                    x-show="
                                        characterTarget
                                            .character
                                            .exhaustion_enabled
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        items-center
                                        gap-1.5
                                    "
                                >
                                    <span
                                        class="
                                            mr-0.5
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                    >
                                        Exaustão
                                    </span>

                                    <template
                                        x-for="
                                            level
                                            in
                                            6
                                        "
                                        :key="
                                            `modal-ex-${level}`
                                        "
                                    >
                                        <span
                                            class="
                                                h-2.5
                                                w-2.5
                                                rounded-full
                                                border
                                            "
                                            :class="
                                                level
                                                <=
                                                characterTarget.character.exhaustion
                                                    ? 'border-[#6b1d14] bg-[#6b1d14]'
                                                    : 'border-[#cdbb9f] bg-[#fffdf8]'
                                            "
                                        ></span>
                                    </template>
                                </div>
                            </header>


                            <div
                                class="
                                    grid
                                    grid-cols-3
                                    divide-x
                                    divide-y
                                    divide-[#eadfce]
                                    sm:grid-cols-6
                                    sm:divide-y-0
                                "
                            >
                                <template
                                    x-for="
                                        (ability, index)
                                        in
                                        characterTarget.character.abilities
                                    "
                                    :key="
                                        ability.key
                                    "
                                >
                                    <div
                                        class="
                                            relative
                                            px-2
                                            py-2.5
                                            text-center
                                            transition
                                            hover:bg-[#fbf8f1]
                                        "
                                    >
                                        <p
                                            class="
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-[0.06em]
                                                text-[#8c6239]
                                            "
                                            x-text="
                                                ability.short
                                            "
                                        ></p>

                                        <div
                                            class="
                                                mt-1
                                                flex
                                                items-baseline
                                                justify-center
                                                gap-1
                                            "
                                        >
                                            <strong
                                                class="
                                                    font-serif
                                                    text-[19px]
                                                    font-black
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    ability.score
                                                "
                                            ></strong>

                                            <span
                                                class="
                                                    text-[10px]
                                                    font-black
                                                    text-[#9a806d]
                                                "
                                                x-text="
                                                    signed(
                                                        ability.modifier
                                                    )
                                                "
                                            ></span>
                                        </div>

                                        <div
                                            class="
                                                mt-2
                                                flex
                                                items-center
                                                justify-center
                                                gap-1.5
                                                border-t
                                                border-[#eadfce]
                                                pt-1.5
                                            "
                                        >
                                            <span
                                                class="
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    text-[#9a806d]
                                                "
                                            >
                                                Salv
                                            </span>

                                            <strong
                                                class="
                                                    font-serif
                                                    text-[12px]
                                                    font-black
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    signed(
                                                        characterTarget
                                                            .character
                                                            .saving_throws[
                                                                index
                                                            ]
                                                            ?.value
                                                        ?? 0
                                                    )
                                                "
                                            ></strong>

                                            <span
                                                x-show="
                                                    characterTarget
                                                        .character
                                                        .saving_throws[
                                                            index
                                                        ]
                                                        ?.proficient
                                                "
                                                x-cloak
                                                class="
                                                    h-2
                                                    w-2
                                                    rounded-full
                                                    bg-[#6b1d14]
                                                "
                                                title="Proficiente"
                                            ></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>


                        {{-- ESTADOS IMPORTANTES --}}
                        <div
                            class="
                                mt-2.5
                                flex
                                min-h-9
                                items-center
                                gap-2
                            "
                        >
                            <div
                                x-show="
                                    Number(
                                        characterTarget
                                            .character
                                            .current_hp
                                    )
                                    <=
                                    0
                                "
                                x-cloak
                                class="
                                    flex
                                    flex-1
                                    items-center
                                    justify-between
                                    gap-3
                                    rounded-xl
                                    border
                                    border-[#8a2519]/35
                                    bg-[#f5e4dd]
                                    px-3
                                    py-2
                                "
                            >
                                <span
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#6b1d14]
                                    "
                                >
                                    Salvamentos contra morte
                                </span>

                                <div
                                    class="
                                        flex
                                        gap-4
                                    "
                                >
                                    <span
                                        class="
                                            text-[10px]
                                            font-black
                                            text-emerald-800
                                        "
                                    >
                                        S
                                        <span
                                            x-text="
                                                characterTarget
                                                    .character
                                                    .death_save_successes
                                            "
                                        ></span>
                                        /
                                        <span
                                            x-text="
                                                characterTarget
                                                    .character
                                                    .death_save_success_limit
                                            "
                                        ></span>
                                    </span>

                                    <span
                                        class="
                                            text-[10px]
                                            font-black
                                            text-[#8a2519]
                                        "
                                    >
                                        F
                                        <span
                                            x-text="
                                                characterTarget
                                                    .character
                                                    .death_save_failures
                                            "
                                        ></span>
                                        /
                                        <span
                                            x-text="
                                                characterTarget
                                                    .character
                                                    .death_save_failure_limit
                                            "
                                        ></span>
                                    </span>
                                </div>
                            </div>


                            <div
                                x-show="
                                    characterTarget
                                        .character
                                        .conditions
                                        ?.length
                                    >
                                    0
                                "
                                x-cloak
                                class="
                                    flex
                                    flex-1
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="
                                        condition
                                        in
                                        characterTarget.character.conditions
                                    "
                                    :key="
                                        condition
                                    "
                                >
                                    <span
                                        class="
                                            rounded-full
                                            border
                                            border-[#d6c1a7]
                                            bg-[#f2e7d7]
                                            px-2.5
                                            py-1
                                            text-[9px]
                                            font-black
                                            text-[#6b1d14]
                                        "
                                        x-text="
                                            condition
                                        "
                                    ></span>
                                </template>
                            </div>
                        </div>


                        <div
                            x-show="
                                characterTarget
                                    .character
                                    .has_died
                            "
                            x-cloak
                            class="
                                mt-2.5
                                flex
                                items-center
                                justify-between
                                gap-3
                                rounded-xl
                                border
                                border-[#53150f]/30
                                bg-[#efe1dc]
                                px-3
                                py-2.5
                            "
                        >
                            <div class="min-w-0">
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.10em]
                                        text-[#6b1d14]
                                    "
                                >
                                    Registro de morte
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[10px]
                                        font-bold
                                        text-[#53150f]
                                    "
                                >
                                    <span
                                        x-text="
                                            characterTarget
                                                .character
                                                .death_count
                                            ?? 1
                                        "
                                    ></span>
                                    <span> ocorrência(s) registrada(s)</span>
                                </p>
                            </div>

                            <div
                                class="
                                    shrink-0
                                    text-right
                                "
                            >
                                <p
                                    class="
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#8c6239]
                                    "
                                >
                                    Última morte
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[9px]
                                        font-black
                                        text-[#53150f]
                                    "
                                    x-text="
                                        formatDeathDate(
                                            characterTarget
                                                .character
                                                .last_death_at
                                        )
                                    "
                                ></p>
                            </div>
                        </div>


                        <div
                            class="
                                mt-2.5
                                grid
                                grid-cols-3
                                gap-2
                            "
                        >
                            <button
                                type="button"
                                x-on:click="
                                    characterModalOpen =
                                        false;

                                    openMessage(
                                        characterTarget
                                    );
                                "
                                class="
                                    rounded-xl
                                    bg-[#6b1d14]
                                    px-3
                                    py-2.5
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.07em]
                                    text-[#fffaf2]
                                    shadow-sm
                                    transition
                                    hover:bg-[#53150f]
                                "
                            >
                                Mensagem
                            </button>

                            <a
                                :href="
                                    characterTarget
                                        .character
                                        .sheet_url
                                "
                                class="
                                    rounded-xl
                                    border
                                    border-[#6b1d14]/25
                                    bg-[#fffdf8]
                                    px-3
                                    py-2.5
                                    text-center
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.07em]
                                    text-[#6b1d14]
                                    transition
                                    hover:bg-[#eadbc8]
                                "
                            >
                                Olhar ficha
                            </a>

                        </div>
                    </div>


                    {{-- =====================================================
                         INVENTÁRIO — ESTILO EQUIPÁVEL
                    ====================================================== --}}
                    <div
                        x-show="
                            characterTab
                            ===
                            'inventory'
                        "
                        x-cloak
                        class="
                            flex
                            min-h-0
                            flex-1
                            flex-col
                        "
                    >
                        <div
                            class="
                                min-h-0
                                flex-1
                                overflow-y-auto
                                [scrollbar-width:none]
                                [&::-webkit-scrollbar]:hidden
                            "
                        >
                            {{-- EQUIPADOS --}}
                            <section
                                class="
                                    border-b
                                    border-[#cdbb9f]/55
                                    bg-[#fffdf8]
                                "
                            >
                                <header
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                        border-b
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]
                                        px-4
                                        py-2.5
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.13em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Em uso
                                        </p>

                                        <div
                                            class="
                                                mt-0.5
                                                flex
                                                items-center
                                                gap-2
                                            "
                                        >
                                            <h3
                                                class="
                                                    font-serif
                                                    text-[21px]
                                                    font-black
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                            >
                                                Itens Equipados
                                            </h3>

                                            <span
                                                class="
                                                    flex
                                                    h-6
                                                    min-w-6
                                                    items-center
                                                    justify-center
                                                    rounded-full
                                                    border
                                                    border-[#cdbb9f]/60
                                                    bg-[#fffdf8]
                                                    px-1.5
                                                    text-[10px]
                                                    font-black
                                                    text-[#6b1d14]
                                                "
                                                x-text="
                                                    equippedItems()
                                                        .length
                                                "
                                            ></span>
                                        </div>
                                    </div>

                                    <span
                                        class="
                                            hidden
                                            text-[8px]
                                            text-[#9a806d]
                                            sm:block
                                        "
                                    >
                                        Clique em um item para detalhes
                                    </span>
                                </header>


                                <div
                                    class="
                                        space-y-0
                                        px-4
                                        py-1.5
                                    "
                                >
                                    <template
                                        x-for="
                                            item
                                            in
                                            equippedItems()
                                        "
                                        :key="
                                            `equipped-${item.id}`
                                        "
                                    >
                                        <article
                                            class="
                                                border-b
                                                border-[#eadfce]
                                                last:border-b-0
                                            "
                                        >
                                            <button
                                                type="button"
                                                x-on:click="
                                                    toggleInventoryItem(
                                                        item
                                                    )
                                                "
                                                class="
                                                    flex
                                                    w-full
                                                    items-center
                                                    gap-3
                                                    py-2.5
                                                    text-left
                                                "
                                            >
                                                <div
                                                    class="
                                                        flex
                                                        h-12
                                                        w-12
                                                        shrink-0
                                                        items-center
                                                        justify-center
                                                        overflow-hidden
                                                        rounded-lg
                                                        border
                                                        border-[#d0b18c]
                                                        bg-[#f4eadb]
                                                        text-[#c7ab89]
                                                    "
                                                >
                                                    <template
                                                        x-if="
                                                            item.image_url
                                                        "
                                                    >
                                                        <img
                                                            :src="
                                                                item.image_url
                                                            "
                                                            :alt="
                                                                item.name
                                                            "
                                                            class="
                                                                h-full
                                                                w-full
                                                                object-cover
                                                            "
                                                        >
                                                    </template>

                                                    <template
                                                        x-if="
                                                            !item.image_url
                                                        "
                                                    >
                                                        <span
                                                            class="
                                                                text-base
                                                            "
                                                        >
                                                            ◆
                                                        </span>
                                                    </template>
                                                </div>


                                                <div
                                                    class="
                                                        min-w-0
                                                        flex-1
                                                    "
                                                >
                                                    <div
                                                        class="
                                                            flex
                                                            min-w-0
                                                            items-center
                                                            gap-2
                                                        "
                                                    >
                                                        <h4
                                                            class="
                                                                min-w-0
                                                                flex-1
                                                                truncate
                                                                font-serif
                                                                text-[14px]
                                                                font-black
                                                                text-[#53150f]
                                                            "
                                                            x-text="
                                                                item.name
                                                            "
                                                        ></h4>

                                                        <span
                                                            x-show="
                                                                item.quantity
                                                                >
                                                                1
                                                            "
                                                            x-cloak
                                                            class="
                                                                shrink-0
                                                                text-[9px]
                                                                font-black
                                                                text-[#8c6239]
                                                            "
                                                        >
                                                            ×
                                                            <span
                                                                x-text="
                                                                    item.quantity
                                                                "
                                                            ></span>
                                                        </span>
                                                    </div>

                                                    <p
                                                        class="
                                                            mt-0.5
                                                            truncate
                                                            text-[9px]
                                                            italic
                                                            text-[#8c6239]
                                                        "
                                                    >
                                                        <span
                                                            x-text="
                                                                item.rarity_label
                                                                ?? item.kind
                                                            "
                                                        ></span>

                                                        <span
                                                            x-show="
                                                                item.attunement_label
                                                            "
                                                            x-cloak
                                                        >
                                                            ·
                                                            <span
                                                                x-text="
                                                                    item.attunement_label
                                                                "
                                                            ></span>
                                                        </span>
                                                    </p>
                                                </div>


                                                <span
                                                    class="
                                                        shrink-0
                                                        text-base
                                                        text-[#c7ab89]
                                                        transition
                                                    "
                                                    :class="
                                                        Number(
                                                            inventoryItemOpenId
                                                        )
                                                        ===
                                                        Number(
                                                            item.id
                                                        )
                                                            ? 'rotate-90'
                                                            : ''
                                                    "
                                                >
                                                    ›
                                                </span>
                                            </button>


                                            <div
                                                x-show="
                                                    Number(
                                                        inventoryItemOpenId
                                                    )
                                                    ===
                                                    Number(
                                                        item.id
                                                    )
                                                "
                                                x-cloak
                                                class="
                                                    mb-2.5
                                                    ml-[60px]
                                                    rounded-lg
                                                    bg-[#f7f0e6]
                                                    px-3
                                                    py-2
                                                "
                                            >
                                                <p
                                                    class="
                                                        text-[9px]
                                                        font-black
                                                        uppercase
                                                        tracking-wider
                                                        text-[#8c6239]
                                                    "
                                                    x-text="
                                                        item.kind
                                                    "
                                                ></p>

                                                <p
                                                    x-show="
                                                        item.description
                                                    "
                                                    x-cloak
                                                    class="
                                                        mt-1
                                                        text-[10px]
                                                        leading-4
                                                        text-[#6f5949]
                                                    "
                                                    x-text="
                                                        item.description
                                                    "
                                                ></p>
                                            </div>
                                        </article>
                                    </template>


                                    <div
                                        x-show="
                                            equippedItems()
                                                .length
                                            ===
                                            0
                                        "
                                        class="
                                            py-5
                                            text-center
                                            text-[10px]
                                            text-[#9a806d]
                                        "
                                    >
                                        Nenhum item equipado.
                                    </div>
                                </div>
                            </section>


                            {{-- GUARDADOS --}}
                            <section
                                class="
                                    bg-[#fffdf8]
                                "
                            >
                                <header
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                        border-b
                                        border-[#cdbb9f]/50
                                        bg-[#f7f0e6]
                                        px-4
                                        py-2.5
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.13em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Guardados
                                        </p>

                                        <div
                                            class="
                                                mt-0.5
                                                flex
                                                items-center
                                                gap-2
                                            "
                                        >
                                            <h3
                                                class="
                                                    font-serif
                                                    text-[16px]
                                                    font-black
                                                    leading-none
                                                    text-[#53150f]
                                                "
                                            >
                                                Mochila
                                            </h3>

                                            <span
                                                class="
                                                    flex
                                                    h-5
                                                    min-w-5
                                                    items-center
                                                    justify-center
                                                    rounded-full
                                                    border
                                                    border-[#cdbb9f]/60
                                                    bg-[#fffdf8]
                                                    px-1.5
                                                    text-[9px]
                                                    font-black
                                                    text-[#6b1d14]
                                                "
                                                x-text="
                                                    storedItems()
                                                        .length
                                                "
                                            ></span>
                                        </div>
                                    </div>
                                </header>


                                <div
                                    class="
                                        px-4
                                        py-1.5
                                    "
                                >
                                    <template
                                        x-for="
                                            item
                                            in
                                            storedItems()
                                        "
                                        :key="
                                            `stored-${item.id}`
                                        "
                                    >
                                        <article
                                            class="
                                                border-b
                                                border-[#eadfce]
                                                last:border-b-0
                                            "
                                        >
                                            <button
                                                type="button"
                                                x-on:click="
                                                    toggleInventoryItem(
                                                        item
                                                    )
                                                "
                                                class="
                                                    flex
                                                    w-full
                                                    items-center
                                                    gap-3
                                                    py-2.5
                                                    text-left
                                                "
                                            >
                                                <div
                                                    class="
                                                        flex
                                                        h-11
                                                        w-11
                                                        shrink-0
                                                        items-center
                                                        justify-center
                                                        overflow-hidden
                                                        rounded-lg
                                                        border
                                                        border-[#d0b18c]
                                                        bg-[#f4eadb]
                                                        text-[#c7ab89]
                                                    "
                                                >
                                                    <template
                                                        x-if="
                                                            item.image_url
                                                        "
                                                    >
                                                        <img
                                                            :src="
                                                                item.image_url
                                                            "
                                                            :alt="
                                                                item.name
                                                            "
                                                            class="
                                                                h-full
                                                                w-full
                                                                object-cover
                                                            "
                                                        >
                                                    </template>

                                                    <template
                                                        x-if="
                                                            !item.image_url
                                                        "
                                                    >
                                                        <span>
                                                            ◆
                                                        </span>
                                                    </template>
                                                </div>

                                                <div
                                                    class="
                                                        min-w-0
                                                        flex-1
                                                    "
                                                >
                                                    <div
                                                        class="
                                                            flex
                                                            items-center
                                                            gap-2
                                                        "
                                                    >
                                                        <h4
                                                            class="
                                                                min-w-0
                                                                flex-1
                                                                truncate
                                                                font-serif
                                                                text-[13px]
                                                                font-black
                                                                text-[#53150f]
                                                            "
                                                            x-text="
                                                                item.name
                                                            "
                                                        ></h4>

                                                        <span
                                                            x-show="
                                                                item.quantity
                                                                >
                                                                1
                                                            "
                                                            x-cloak
                                                            class="
                                                                shrink-0
                                                                text-[9px]
                                                                font-black
                                                                text-[#8c6239]
                                                            "
                                                        >
                                                            ×
                                                            <span
                                                                x-text="
                                                                    item.quantity
                                                                "
                                                            ></span>
                                                        </span>
                                                    </div>

                                                    <p
                                                        class="
                                                            mt-0.5
                                                            truncate
                                                            text-[9px]
                                                            italic
                                                            text-[#8c6239]
                                                        "
                                                    >
                                                        <span
                                                            x-text="
                                                                item.rarity_label
                                                                ?? item.kind
                                                            "
                                                        ></span>

                                                        <span
                                                            x-show="
                                                                item.attunement_label
                                                            "
                                                            x-cloak
                                                        >
                                                            ·
                                                            <span
                                                                x-text="
                                                                    item.attunement_label
                                                                "
                                                            ></span>
                                                        </span>
                                                    </p>
                                                </div>

                                                <span
                                                    class="
                                                        shrink-0
                                                        text-base
                                                        text-[#c7ab89]
                                                    "
                                                >
                                                    ›
                                                </span>
                                            </button>


                                            <div
                                                x-show="
                                                    Number(
                                                        inventoryItemOpenId
                                                    )
                                                    ===
                                                    Number(
                                                        item.id
                                                    )
                                                "
                                                x-cloak
                                                class="
                                                    mb-2.5
                                                    ml-[56px]
                                                    rounded-lg
                                                    bg-[#f7f0e6]
                                                    px-3
                                                    py-2
                                                "
                                            >
                                                <p
                                                    class="
                                                        text-[9px]
                                                        font-black
                                                        uppercase
                                                        tracking-wider
                                                        text-[#8c6239]
                                                    "
                                                    x-text="
                                                        item.kind
                                                    "
                                                ></p>

                                                <p
                                                    x-show="
                                                        item.description
                                                    "
                                                    x-cloak
                                                    class="
                                                        mt-1
                                                        text-[10px]
                                                        leading-4
                                                        text-[#6f5949]
                                                    "
                                                    x-text="
                                                        item.description
                                                    "
                                                ></p>
                                            </div>
                                        </article>
                                    </template>


                                    <div
                                        x-show="
                                            storedItems()
                                                .length
                                            ===
                                            0
                                        "
                                        class="
                                            py-5
                                            text-center
                                            text-[10px]
                                            text-[#9a806d]
                                        "
                                    >
                                        Nenhum item guardado.
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    </div>


    {{-- ================================================================
         GAVETAS — FICHAS / COMBATES / ESPÓLIOS / MENSAGENS
    ================================================================= --}}
    <div
        x-show="
            drawer
        "
        x-cloak
        class="
            fixed
            inset-0
            z-[170]
        "
    >
        <div
            class="
                absolute
                inset-0
                bg-black/90
                backdrop-blur-[2px]
            "
            style="
                background-color:
                    rgba(8, 6, 5, 0.88);
            "
            x-on:click="
                closeDrawer()
            "
        ></div>


        <aside
            x-transition:enter="
                transition
                ease-out
                duration-200
            "
            x-transition:enter-start="
                translate-x-full
            "
            x-transition:enter-end="
                translate-x-0
            "
            x-transition:leave="
                transition
                ease-in
                duration-150
            "
            x-transition:leave-start="
                translate-x-0
            "
            x-transition:leave-end="
                translate-x-full
            "
            :class="
                drawer === 'loot'
                    ? 'max-w-[1180px]'
                    : 'max-w-2xl'
            "
            class="
                absolute
                bottom-0
                right-0
                top-0
                z-10
                flex
                w-full
                flex-col
                border-l
                border-[#cdbb9f]
                bg-[#f4f1e8]
                shadow-2xl
            "
        >
            <header
                x-show="drawer !== 'loot'"
                x-cloak
                class="
                    flex
                    shrink-0
                    items-center
                    justify-between
                    gap-4
                    border-b
                    border-[#cdbb9f]/65
                    bg-[#eadbc8]
                    px-5
                    py-4
                "
            >
                <div
                    class="
                        min-w-0
                    "
                >
                    <h2
                        class="
                            truncate
                            font-serif
                            text-xl
                            font-black
                            text-[#53150f]
                        "
                    >
                        <span
                            x-show="
                                drawer
                                ===
                                'sheets'
                            "
                        >
                            Fichas da Campanha
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'combats'
                            "
                        >
                            Combates
                        </span>

                        <span
                            x-show="
                                drawer
                                ===
                                'messages'
                            "
                        >
                            Mensagens Secretas
                        </span>
                    </h2>
                </div>

                <button
                    type="button"
                    x-on:click="
                        closeDrawer()
                    "
                    class="
                        flex
                        h-9
                        w-9
                        shrink-0
                        items-center
                        justify-center
                        rounded-lg
                        text-xl
                        text-[#8c6239]
                        transition
                        hover:bg-[#dbc7ae]
                        hover:text-[#53150f]
                    "
                >
                    ×
                </button>
            </header>


            <div
                :class="
                    drawer === 'loot'
                        ? 'p-4'
                        : 'p-5'
                "
                class="
                    min-h-0
                    flex-1
                    overflow-y-auto
                "
            >
                @include('campaigns.components.master-shield.drawers.sheets')


                {{-- COMBATES --}}
                <div
                    x-show="
                        drawer
                        ===
                        'combats'
                    "
                >
                    <div
                        class="
                            space-y-2
                        "
                    >
                        @forelse($campaignCombats as $combat)
                            <a
                                href="{{ route('combats.show', $combat) }}"
                                class="
                                    block
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]/60
                                    bg-[#fffdf8]
                                    p-4
                                    transition
                                    hover:border-[#b08c62]
                                    hover:shadow-sm
                                "
                            >
                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <div
                                        class="
                                            min-w-0
                                        "
                                    >
                                        <p
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-[0.14em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Encontro
                                        </p>

                                        <h3
                                            class="
                                                mt-1
                                                truncate
                                                font-serif
                                                text-base
                                                font-black
                                                text-[#53150f]
                                            "
                                        >
                                            {{ $combat->name }}
                                        </h3>
                                    </div>

                                    <span
                                        class="
                                            rounded-full
                                            px-2
                                            py-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            {{
                                                $combat->is_active
                                                    ? 'bg-emerald-100 text-emerald-800'
                                                    : 'bg-[#eee9e1] text-[#7d604d]'
                                            }}
                                        "
                                    >
                                        {{
                                            $combat->is_active
                                                ? 'Em andamento'
                                                : (
                                                    (int) $combat->current_round > 0
                                                        ? 'Pausado'
                                                        : 'Planejado'
                                                )
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="
                                        mt-3
                                        flex
                                        gap-3
                                        text-[8px]
                                        font-black
                                        text-[#8c6239]
                                    "
                                >
                                    <span>
                                        Rodada {{ (int) $combat->current_round }}
                                    </span>

                                    <span>
                                        {{ (int) $combat->players_count }} Players
                                    </span>

                                    <span>
                                        {{ (int) $combat->npcs_count }} NPCs
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-dashed
                                    border-[#cdbb9f]
                                    px-5
                                    py-10
                                    text-center
                                "
                            >
                                <p
                                    class="
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Nenhum combate planejado
                                </p>

                                <a
                                    href="{{ route('combats.index') }}"
                                    class="
                                        mt-3
                                        inline-flex
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-wider
                                        text-[#6b1d14]
                                        hover:underline
                                    "
                                >
                                    Ir para Combates →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>


                @include('campaigns.components.master-shield.drawers.loot')


                {{-- MENSAGENS --}}
                <div
                    x-show="
                        drawer
                        ===
                        'messages'
                    "
                >
                    <div
                        class="
                            rounded-2xl
                            border
                            border-[#cdbb9f]/60
                            bg-[#fffdf8]
                            p-5
                        "
                    >
                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#8c6239]
                            "
                        >
                            Conversa privada
                        </p>

                        <h3
                            class="
                                mt-1
                                font-serif
                                text-lg
                                font-black
                                text-[#53150f]
                            "
                            x-text="
                                messageTarget
                                    ? messageTarget.player_name
                                    : 'Escolha um Player'
                            "
                        ></h3>

                        <div
                            class="
                                mt-4
                                rounded-xl
                                border
                                border-dashed
                                border-[#cdbb9f]
                                px-5
                                py-8
                                text-center
                            "
                        >
                            <p
                                class="
                                    text-[9px]
                                    leading-5
                                    text-[#8c6239]
                                "
                            >
                                O histórico e o envio privado Mestre ↔ Player serão conectados depois.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>