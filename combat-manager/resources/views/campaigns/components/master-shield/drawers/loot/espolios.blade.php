{{-- ================================================================
     ESPÓLIOS — WORKSPACE
     Pastas de Espólios representam locais, dungeons, regiões ou arcos.
================================================================ --}}
<div
    x-show="lootWorkspaceMode === 'loot'"
    x-cloak
    class="sb-shop-workspace sb-loot-workspace sb-workspace-fill grid gap-3 xl:grid-cols-[306px_minmax(0,1fr)]"
>
    <style>
        /* ============================================================
           ESPÓLIOS — MESMA LINGUAGEM VISUAL DAS LOJAS
           ============================================================ */
        .sb-loot-workspace {
            height: calc(100dvh - 148px);
            min-height: 520px;
            gap: 14px !important;
            align-items: stretch;
        }

        .sb-loot-workspace .sb-loot-sidebar {
            height: 100%;
            min-height: 0;
        }

        .sb-loot-workspace .sb-loot-scroll {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none !important;
        }

        .sb-loot-sidebar {
            border-radius: 17px !important;
            border-color: #ddd0bd !important;
            background: #f5efe6 !important;
            box-shadow: none !important;
        }

        .sb-loot-workspace .sb-loot-mode-switch {
            border-radius: 11px !important;
            border-color: #d8c7ab !important;
            background: #e9dfd2 !important;
            padding: 3px !important;
        }

        .sb-loot-workspace .sb-loot-mode-switch > button {
            min-height: 34px;
            border-radius: 8px !important;
        }

        .sb-loot-folder-card {
            position: relative;
            overflow: visible !important;
            border: 1px solid #ddd0bd !important;
            border-left: 3px solid #8a4b20 !important;
            border-radius: 12px !important;
            background: #fffdf8 !important;
            box-shadow: none !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .sb-loot-folder-card.is-active {
            border-color: #b68d68 !important;
            box-shadow: 0 5px 14px rgba(83,21,15,.045) !important;
        }

        .sb-loot-folder-head {
            min-height: 48px;
            padding: 6px 6px 6px 7px !important;
            background: linear-gradient(90deg,#fbf6ef 0%,#fffdf8 74%);
            border-radius: 10px;
        }

        .sb-loot-folder-body {
            border-top: 1px solid #eee2d1 !important;
            background: #fbf7f0 !important;
            padding: 6px !important;
        }

        .sb-loot-drop-row {
            min-height: 48px;
            border: 1px solid #e3d7c6 !important;
            border-radius: 10px !important;
            background: #fffdf8 !important;
            box-shadow: none !important;
            transition: border-color .15s ease, background .15s ease, transform .15s ease !important;
        }

        .sb-loot-drop-row:hover {
            border-color: #c9ab85 !important;
            transform: translateX(1px);
        }

        .sb-loot-drop-row.is-active {
            border-color: #8a4b20 !important;
            background: #fffaf4 !important;
            box-shadow: inset 3px 0 0 #8a4b20 !important;
        }

        .sb-loot-drop-menu {
            width: 174px;
            overflow: hidden;
            border: 1px solid #cdbb9f;
            border-radius: 11px;
            background: #fffdf8;
            padding: 5px;
            box-shadow: 0 12px 30px rgba(55,30,18,.18);
        }

        .sb-loot-drop-menu button {
            display: flex;
            width: 100%;
            border: 0;
            border-radius: 7px;
            background: transparent;
            padding: 8px 9px;
            color: #6b1d14;
            font-size: 7.5px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
        }

        .sb-loot-drop-menu button:hover {
            background: #f1e8db;
        }

        .sb-loot-drop-menu button.is-danger {
            color: #b42318;
        }

        .sb-loot-folder-menu-floating {
            width: 180px;
            overflow: hidden;
            border: 1px solid #cdbb9f;
            border-radius: 11px;
            background: #fffdf8;
            padding: 5px;
            box-shadow: 0 14px 34px rgba(55,30,18,.20);
        }

        .sb-loot-folder-menu-floating button {
            display: flex;
            width: 100%;
            border: 0;
            border-radius: 7px;
            background: transparent;
            padding: 8px 9px;
            color: #6b1d14;
            font-size: 7.5px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
        }

        .sb-loot-folder-menu-floating button:hover {
            background: #f1e8db;
        }

        .sb-loot-folder-menu-floating button.is-danger {
            color: #b42318;
        }

        .sb-loot-main-card {
            border-color: #d7c5aa !important;
            border-radius: 17px !important;
            background: #fffdf8 !important;
            box-shadow: none !important;
        }

        /* Cabeçalhos e cartões seguem a mesma paleta das Lojas. */
        .sb-loot-location-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid #d7c5aa !important;
            border-left: 3px solid #8a4b20 !important;
            border-radius: 17px !important;
            background:
                linear-gradient(
                    100deg,
                    #fffdf8 0%,
                    #fbf5ed 50%,
                    #f0dfcf 100%
                ) !important;
            box-shadow: none !important;
        }

        .sb-loot-location-cover {
            border-color: #d6b88e !important;
            border-radius: 14px !important;
            background: #f3e8da !important;
        }

        .sb-loot-section-card {
            border: 1px solid #d7c5aa !important;
            border-radius: 17px !important;
            background: #fffdf8 !important;
            box-shadow: none !important;
        }

        .sb-loot-section-head {
            border-color: #eadcc8 !important;
        }

        .sb-loot-reward-card {
            border-color: #ddcfba !important;
            border-radius: 12px !important;
            background: #fbf7f1 !important;
            box-shadow: none !important;
        }

        .sb-loot-reward-card:hover {
            border-color: #b98f69 !important;
            background: #fffdf8 !important;
            box-shadow: inset 3px 0 0 #8a4b20 !important;
        }

        .sb-loot-overview-hero {
            border: 1px solid #d7c5aa !important;
            border-left: 3px solid #8a4b20 !important;
            border-radius: 17px !important;
            background:
                linear-gradient(
                    100deg,
                    #fffdf8 0%,
                    #fbf5ed 58%,
                    #f2e4d5 100%
                ) !important;
            box-shadow: none !important;
        }

        .sb-loot-overview-card {
            border-color: #ddcfba !important;
            background: #fbf7f1 !important;
            box-shadow: none !important;
        }

        .sb-loot-overview-card:hover {
            border-color: #b98f69 !important;
            background: #fffdf8 !important;
            box-shadow: inset 3px 0 0 #8a4b20 !important;
        }

        .sb-loot-folder-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .sb-loot-folder-action svg {
            width: 13px;
            height: 13px;
            flex: 0 0 auto;
        }

        .sb-loot-workspace,
        .sb-loot-workspace * {
            scrollbar-width: thin;
            scrollbar-color: #bda98f transparent;
        }

        .sb-loot-workspace ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .sb-loot-workspace ::-webkit-scrollbar-track {
            background: transparent;
        }

        .sb-loot-workspace ::-webkit-scrollbar-thumb {
            border: 2px solid transparent;
            border-radius: 999px;
            background: #bda98f;
            background-clip: padding-box;
        }

        .sb-loot-workspace ::-webkit-scrollbar-thumb:hover {
            background: #9d7d61;
            background-clip: padding-box;
        }

        .sb-loot-workspace ::-webkit-scrollbar-corner {
            background: transparent;
        }
    </style>

    <aside class="sb-shop-workspace-sidebar sb-loot-sidebar flex min-w-0 flex-col rounded-2xl border border-[#d8c7ab]/70 bg-[#f7f2e9]/90 p-3 shadow-[0_8px_24px_rgba(83,21,15,.035)]">
        <div class="sb-loot-mode-switch grid grid-cols-2 gap-1 rounded-xl border border-[#d8c7ab]/60 bg-[#eee6da]/80 p-1">
            <button
                type="button"
                class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-wider transition"
                :class="lootWorkspaceMode === 'loot' ? 'bg-[#fffdf8] text-[#53150f] shadow-sm' : 'text-[#8c6239] hover:bg-white/50'"
                @click="lootWorkspaceMode = 'loot'; lootActiveDropId = null; lootActiveFolderId = null; lootContentTab = 'items'; lootDropMenuId = null; lootFolderMenuId = null"
            >Espólios</button>
            <button
                type="button"
                class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-wider transition"
                :class="lootWorkspaceMode === 'shop' ? 'bg-[#fffdf8] text-[#53150f] shadow-sm' : 'text-[#8c6239] hover:bg-white/50'"
                @click="lootWorkspaceMode = 'shop'; lootActiveShopId = null; shopActiveFolderId = null; lootDropMenuId = null; lootFolderMenuId = null; shopFolderMenuId = null"
            >Lojas</button>
        </div>

        <div class="mt-3 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[7px] font-black uppercase tracking-[0.14em] text-[#a47c5e]">Organização</p>
                <h4 class="font-serif text-[17px] font-black text-[#53150f]">Locais da campanha</h4>
            </div>

            <button
                type="button"
                class="sb-loot-folder-action shrink-0 rounded-lg bg-[#6b1d14] px-3 py-2 text-[8px] font-black uppercase tracking-wider text-white hover:bg-[#53150f]"
                @click="openLootFolderEditor()"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path>
                    <path d="M3.5 7.5V5h6l1.5 2.5"></path>
                </svg>
                <span>+ Pasta</span>
            </button>
        </div>

        <div class="mt-3 grid gap-2">
            <div class="relative">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#a47c5e]" aria-hidden="true">
                    <circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path>
                </svg>
                <input
                    type="search"
                    x-model.debounce.180ms="lootDropSearch"
                    placeholder="Buscar Espólio..."
                    class="h-10 w-full rounded-[10px] border border-[#d8c7ab] bg-[#fffdf8] pl-9 pr-3 text-[11px] font-semibold text-[#53150f] focus:border-[#a9784d] focus:ring-[#8a4b20]/10"
                >
            </div>

        </div>

        <div class="sb-loot-scroll mt-3 min-h-0 flex-1 space-y-2 overflow-y-auto pr-1" @scroll="lootDropMenuId = null; lootFolderMenuId = null">
            <template x-for="folder in lootFolders" :key="'loot-folder-' + folder.id">
                <section
                    class="sb-loot-folder-card overflow-visible rounded-2xl border border-[#d8c7ab]/70 bg-[#fffdf8]"
                    :class="{
                        'is-active': parseInt(lootActiveFolderId) === parseInt(folder.id),
                        'border-[#8a4b20] bg-[#fffaf4]': lootFolderDropTarget === 'folder:' + folder.id
                    }"
                    @dragover.prevent="lootFolderDropTarget = 'folder:' + folder.id"
                    @dragleave="lootFolderDropTarget === 'folder:' + folder.id ? (lootFolderDropTarget = null) : null"
                    @drop="dropLootIntoFolder(folder.id, $event)"
                >
                    <header class="sb-loot-folder-head relative flex items-center gap-2.5 p-2.5">
                        <button
                            type="button"
                            class="flex min-w-0 flex-1 items-center gap-2.5 text-left"
                            @click="selectLootFolder(folder)"
                        >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab]/70 bg-[#efe4d5]">
                                <template x-if="folder?.image_url"><img :src="folder.image_url" :alt="folder.name" class="h-full w-full object-cover"></template>
                                <template x-if="!folder?.image_url">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4 text-[#8a4b20]" aria-hidden="true"><path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path><path d="M3.5 7.5V5h6l1.5 2.5"></path></svg>
                                </template>
                            </span>
                            <span class="min-w-0 flex-1">
                                <strong class="block truncate font-serif text-[13px] font-black text-[#53150f]" x-text="folder.name"></strong>
                                <span x-show="folder?.description" class="mt-0.5 block truncate text-[9px] leading-4 text-[#8f725c]" x-text="folder.description"></span>
                            </span>
                        </button>

                        <span class="rounded-full border border-[#d8c7ab] bg-[#faf7f1] px-2 py-1 text-[8px] font-black text-[#76553f]" x-text="lootFolderDropCount(folder.id)"></span>

                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-lg text-[14px] font-black text-[#8c6239] hover:bg-[#efe4d5]" @click.stop="toggleLootFolder(folder.id)" :title="lootFolderIsOpen(folder.id) ? 'Recolher' : 'Abrir'" x-text="lootFolderIsOpen(folder.id) ? '⌃' : '⌄'"></button>
                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-lg text-[14px] font-black text-[#8c6239] hover:bg-[#efe4d5]" @click.stop="toggleLootFolderMenu(folder, $event)">···</button>

                    </header>

                    <div x-show="lootFolderIsOpen(folder.id)" x-collapse class="sb-loot-folder-body border-t border-[#e7dbc9] bg-[#faf7f1]/70 p-2">
                        <div class="space-y-1.5">
                            <template x-for="drop in lootFolderDrops(folder.id)" :key="'loot-folder-drop-' + drop.id">
                                <article
                                    class="sb-loot-drop-row group relative flex items-center gap-2 rounded-xl border px-2 py-2 transition"
                                    :class="parseInt(lootActiveDropId) === parseInt(drop.id) ? 'is-active' : ''"
                                    draggable="true"
                                    @dragstart="startLootDropDrag(drop, $event)"
                                    @dragend="endLootDropDrag()"
                                >
                                    <button type="button" class="flex min-w-0 flex-1 items-center gap-2.5 text-left" @click="selectLootDrop(drop); lootContentTab = 'items'">
                                        <span class="relative flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab] bg-[#efe4d5]">
                                            <template x-if="lootDropCoverUrl(drop)">
                                                <img :src="lootDropCoverUrl(drop)" :alt="drop.name" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!lootDropCoverUrl(drop)">
                                                <span class="font-serif text-sm font-black text-[#a28670]">◇</span>
                                            </template>
                                            <span class="absolute bottom-1 right-1 h-2 w-2 rounded-full border border-white" :class="drop.status === 'released' ? 'bg-emerald-600' : (drop.status === 'closed' ? 'bg-[#a58f7a]' : 'bg-amber-500')"></span>
                                        </span>

                                        <span class="min-w-0 flex-1">
                                            <strong class="block truncate font-serif text-[12px] text-[#53150f]" x-text="drop.name"></strong>
                                            <span x-show="drop?.description" class="mt-0.5 block truncate text-[9px] leading-4 text-[#8f725c]" x-text="drop.description"></span>
                                        </span>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-[14px] font-black text-[#9a806d] transition hover:bg-[#efe4d5] hover:text-[#53150f]"
                                        @click.stop="toggleLootDropMenu(drop, $event)"
                                        :aria-expanded="parseInt(lootDropMenuId) === parseInt(drop.id)"
                                        aria-label="Ações do Espólio"
                                        title="Ações do Espólio"
                                    >···</button>
                                </article>
                            </template>

                            <button
                                x-show="lootFolderDrops(folder.id).length === 0"
                                x-cloak
                                type="button"
                                class="w-full rounded-xl border border-dashed border-[#d8c7ab] px-3 py-5 text-center text-[8px] font-bold text-[#9a806d]"
                                @click="openLootCreate(folder.id)"
                            >+ Criar Espólio nesta pasta</button>
                        </div>
                    </div>
                </section>
            </template>

            <section
                x-show="lootUnfiledDrops().length > 0 || lootFolderDraggingDropId"
                x-cloak
                class="rounded-2xl border border-dashed border-[#d8c7ab] bg-[#faf7f1]/80 p-2.5"
                :class="{ 'border-[#8a4b20] bg-[#fffaf4]': lootFolderDropTarget === 'unfiled' }"
                @dragover.prevent="lootFolderDropTarget = 'unfiled'"
                @dragleave="lootFolderDropTarget === 'unfiled' ? (lootFolderDropTarget = null) : null"
                @drop="dropLootIntoFolder(null, $event)"
            >
                <header class="flex items-center justify-between gap-3 px-1 pb-2">
                    <strong class="text-[8px] font-black uppercase tracking-[0.08em] text-[#9a806d]">Sem pasta</strong>
                    <span class="rounded-full border border-[#d8c7ab] bg-white px-2 py-1 text-[8px] font-black text-[#76553f]" x-text="lootUnfiledDrops().length"></span>
                </header>

                <div class="space-y-1.5">
                    <template x-for="drop in lootUnfiledDrops()" :key="'loot-unfiled-' + drop.id">
                        <article
                            class="sb-loot-drop-row relative flex items-center gap-2 rounded-xl border px-2.5 py-2.5 transition"
                            :class="parseInt(lootActiveDropId) === parseInt(drop.id) ? 'is-active' : ''"
                            draggable="true"
                            @dragstart="startLootDropDrag(drop, $event)"
                            @dragend="endLootDropDrag()"
                        >
                            <button type="button" class="flex min-w-0 flex-1 items-center gap-2.5 text-left" @click="selectLootDrop(drop); lootContentTab = 'items'">
                                <span class="relative flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab] bg-[#efe4d5]">
                                    <template x-if="lootDropCoverUrl(drop)">
                                        <img :src="lootDropCoverUrl(drop)" :alt="drop.name" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!lootDropCoverUrl(drop)">
                                        <span class="font-serif text-sm font-black text-[#a28670]">◇</span>
                                    </template>
                                    <span class="absolute bottom-1 right-1 h-2 w-2 rounded-full border border-white" :class="drop.status === 'released' ? 'bg-emerald-600' : (drop.status === 'closed' ? 'bg-[#a58f7a]' : 'bg-amber-500')"></span>
                                </span>

                                <span class="min-w-0 flex-1">
                                    <strong class="block truncate font-serif text-[12px] text-[#53150f]" x-text="drop.name"></strong>
                                    <span x-show="drop?.description" class="mt-0.5 block truncate text-[9px] leading-4 text-[#8f725c]" x-text="drop.description"></span>
                                </span>
                            </button>
                            <button
                                type="button"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-[14px] font-black text-[#9a806d] transition hover:bg-[#efe4d5] hover:text-[#53150f]"
                                @click.stop="toggleLootDropMenu(drop, $event)"
                                :aria-expanded="parseInt(lootDropMenuId) === parseInt(drop.id)"
                                aria-label="Ações do Espólio"
                                title="Ações do Espólio"
                            >···</button>
                        </article>
                    </template>

                </div>
            </section>
        </div>
    </aside>

    <main class="min-w-0">
                                        <template x-if="lootWorkspaceMode === 'loot' && lootActiveDrop">
                                            <div class="space-y-3">
                                                {{-- ====================================================
                                                     CABEÇALHO DO ESPÓLIO
                                                     Foto + contexto + ações principais.
                                                ===================================================== --}}
                                                <section class="sb-loot-main-card overflow-visible rounded-[22px] border border-[#d8c7ab]/75 bg-[#fffdf8] p-3.5 shadow-[0_12px_32px_rgba(83,21,15,.045)]">
                                                    <div class="grid gap-4 xl:grid-cols-[285px_minmax(0,1fr)_220px]">
                                                        <div class="relative min-h-[190px] overflow-hidden rounded-[17px] border border-[#d6c2a7] bg-[#efe5d8]">
                                                            <template x-if="lootDropCoverUrl(lootActiveDrop)">
                                                                <img
                                                                    :src="lootDropCoverUrl(lootActiveDrop)"
                                                                    :alt="lootActiveDrop?.name"
                                                                    class="absolute inset-0 h-full w-full object-cover"
                                                                >
                                                            </template>

                                                            <template x-if="!lootDropCoverUrl(lootActiveDrop)">
                                                                <div class="absolute inset-0 grid place-items-center bg-[linear-gradient(135deg,#efe5d8,#e4d5c2)]">
                                                                    <div class="text-center text-[#8c725e]">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto h-10 w-10 text-[#9a806d]" aria-hidden="true">
                                                                            <path d="M3 19.5 8.25 13l3.25 3.5 2.5-3 7 6"></path>
                                                                            <path d="M4 5h16v14H4z"></path>
                                                                            <circle cx="15.5" cy="9" r="1.5"></circle>
                                                                        </svg>
                                                                        <span class="mt-2 block text-[8px] font-black uppercase tracking-[0.16em] text-[#8c725e]">Sem foto</span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="min-w-0 px-1 py-1">
                                                            <div class="flex flex-wrap items-center gap-2 text-[8px] font-black uppercase tracking-[0.14em] text-[#9a806d]">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-3.5 w-3.5" aria-hidden="true">
                                                                    <path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path>
                                                                    <path d="M3.5 7.5V5h6l1.5 2.5"></path>
                                                                </svg>
                                                                <span x-text="lootDropFolderName(lootActiveDrop)"></span>
                                                            </div>

                                                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                                                <h4
                                                                    class="font-serif text-[30px] font-black leading-none text-[#53150f]"
                                                                    x-text="lootActiveDrop?.name"
                                                                ></h4>

                                                                <span
                                                                    class="rounded-full border px-3 py-1.5 text-[8px] font-black uppercase tracking-wider"
                                                                    :class="lootStatusClass(lootActiveDrop?.status)"
                                                                    x-text="lootDropStatusLabel(lootActiveDrop?.status, lootActiveDrop?.closed_reason)"
                                                                ></span>
                                                            </div>

                                                            <p
                                                                x-show="lootActiveDrop?.description"
                                                                class="mt-3 max-w-3xl text-[12px] leading-[1.6] text-[#6f5240]"
                                                                x-text="lootActiveDrop?.description"
                                                            ></p>

                                                            <p
                                                                x-show="!lootActiveDrop?.description"
                                                                x-cloak
                                                                class="mt-3 text-[10px] italic text-[#a18a76]"
                                                            >Sem descrição. Use “Editar local” para adicionar contexto ao Mestre.</p>

                                                            <div class="mt-5 flex flex-wrap gap-x-5 gap-y-3 border-t border-[#e5d8c6] pt-4 text-[#76553f]">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#f1e8db] text-[#8a4b20]">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4" aria-hidden="true">
                                                                            <path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <div>
                                                                        <p class="text-[7px] font-black uppercase tracking-wider text-[#a18a76]">Pasta</p>
                                                                        <strong class="block text-[9px] text-[#53150f]" x-text="lootDropFolderName(lootActiveDrop)"></strong>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2">
                                                                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#f1e8db] text-[#8a4b20]">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4" aria-hidden="true">
                                                                            <path d="M5 8.5h14v10H5z"></path>
                                                                            <path d="M8 8.5V6h8v2.5"></path>
                                                                            <path d="M5 12h14"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <div>
                                                                        <p class="text-[7px] font-black uppercase tracking-wider text-[#a18a76]">Recompensas</p>
                                                                        <strong class="block text-[9px] text-[#53150f]">
                                                                            <span x-text="lootDropRewardCount(lootActiveDrop)"></span>
                                                                            <span x-text="lootDropRewardCount(lootActiveDrop) === 1 ? ' recompensa' : ' recompensas'"></span>
                                                                        </strong>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2">
                                                                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#f1e8db] text-[#8a4b20]">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4" aria-hidden="true">
                                                                            <circle cx="12" cy="12" r="8"></circle>
                                                                            <path d="M12 7.5V12l3 2"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <div>
                                                                        <p class="text-[7px] font-black uppercase tracking-wider text-[#a18a76]">Atualizado</p>
                                                                        <strong class="block text-[9px] text-[#53150f]" x-text="lootClaimDate(lootActiveDrop?.updated_at) || 'Agora'"></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="relative flex flex-col gap-2 xl:items-stretch">
                                                            <div
                                                                x-show="lootActiveDrop?.status !== 'closed'"
                                                                x-cloak
                                                                class="relative"
                                                                @click.outside="lootRewardMenuOpen = false"
                                                            >
                                                                <button
                                                                    type="button"
                                                                    class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#6b1d14] px-4 text-[9px] font-black uppercase tracking-wider text-white shadow-sm transition hover:bg-[#53150f]"
                                                                    @click="lootRewardMenuOpen = !lootRewardMenuOpen"
                                                                >
                                                                    <span class="text-base leading-none">+</span>
                                                                    Adicionar recompensa
                                                                </button>

                                                                <div
                                                                    x-show="lootRewardMenuOpen"
                                                                    x-cloak
                                                                    x-transition.origin.top.right
                                                                    class="absolute right-0 top-[calc(100%+6px)] z-50 w-full min-w-[220px] overflow-hidden rounded-xl border border-[#cdbb9f] bg-[#fffdf8] p-1.5 shadow-xl"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-[9px] font-black text-[#53150f] hover:bg-[#f1e8db]"
                                                                        @click="lootRewardMenuOpen = false; lootContentTab = 'items'; openLootPicker()"
                                                                    >
                                                                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-[#efe4d5] text-[#8a4b20]">◇</span>
                                                                        <span>
                                                                            <strong class="block">Item do Vault</strong>
                                                                            <small class="mt-0.5 block text-[7px] font-semibold text-[#9a806d]">Arma, poção, tesouro ou equipamento.</small>
                                                                        </span>
                                                                    </button>

                                                                    <button
                                                                        type="button"
                                                                        class="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-[9px] font-black text-[#53150f] hover:bg-[#f1e8db]"
                                                                        @click="lootRewardMenuOpen = false; lootContentTab = 'items'; lootCurrencyFormOpen = true"
                                                                    >
                                                                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-[#f5e7c4] text-[#9a671d]">¤</span>
                                                                        <span>
                                                                            <strong class="block">Punhado de moedas</strong>
                                                                            <small class="mt-0.5 block text-[7px] font-semibold text-[#9a806d]">PC, PP, PE, PO e PL no mesmo inventário.</small>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                x-show="lootActiveDrop?.status !== 'closed'"
                                                                x-cloak
                                                                class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cdbb9f] bg-white px-4 text-[9px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#faf7f1] disabled:opacity-50"
                                                                :disabled="lootReleasingAll"
                                                                @click="releaseAllLootRewards()"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4" aria-hidden="true">
                                                                    <circle cx="8" cy="8" r="2.5"></circle>
                                                                    <circle cx="16.5" cy="9" r="2"></circle>
                                                                    <path d="M3.5 18c.6-3.1 2.2-4.7 4.5-4.7s3.9 1.6 4.5 4.7"></path>
                                                                    <path d="M13.5 17.5c.4-2.3 1.5-3.5 3.2-3.5 1.6 0 2.8 1.2 3.3 3.5"></path>
                                                                </svg>
                                                                <span x-text="lootReleasingAll ? 'Liberando...' : 'Liberar para Party'"></span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                class="flex min-h-10 w-full items-center justify-center gap-2 rounded-xl px-4 text-[8px] font-black uppercase tracking-wider text-[#76553f] transition hover:bg-[#f1e8db]"
                                                                @click="openLootDropEditor(lootActiveDrop)"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5" aria-hidden="true">
                                                                    <path d="m4 16.5-.5 4 4-.5L18 9.5 14.5 6z"></path>
                                                                    <path d="m13.5 7 3.5 3.5"></path>
                                                                </svg>
                                                                Editar local
                                                            </button>
                                                        </div>
                                                    </div>
                                                </section>

                                                <nav class="grid grid-cols-2 gap-1 rounded-xl border border-[#d8c7ab]/70 bg-[#eee6da]/70 p-1">
                                                    <button
                                                        type="button"
                                                        class="rounded-lg px-3 py-2.5 text-[9px] font-black uppercase tracking-wider transition"
                                                        :class="lootContentTab !== 'history' ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-white'"
                                                        @click="lootContentTab = 'items'"
                                                    >Itens</button>

                                                    <button
                                                        type="button"
                                                        class="rounded-lg px-3 py-2.5 text-[9px] font-black uppercase tracking-wider transition"
                                                        :class="lootContentTab === 'history' ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-white'"
                                                        @click="lootContentTab = 'history'"
                                                    >Histórico</button>
                                                </nav>

                                                {{-- ====================================================
                                                     ITENS + MOEDAS
                                                     Moedas deixam de ser uma aba e passam a ser
                                                     recompensas no mesmo fluxo visual dos itens.
                                                ===================================================== --}}
                                                <section
                                                    x-show="lootContentTab !== 'history'"
                                                    x-cloak
                                                    class="sb-loot-main-card rounded-[22px] border border-[#d8c7ab]/70 bg-white p-4"
                                                >
                                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                                                        <div>
                                                            <h5 class="font-serif text-[21px] font-black text-[#53150f]">Itens do Espólio</h5>
                                                            <p class="mt-1 text-[10px] text-[#8c6239]">Itens, tesouros e moedas encontrados neste local ficam juntos.</p>
                                                        </div>
                                                    </div>

                                                    <div
                                                        x-show="lootDropRewardCount(lootActiveDrop) > 0"
                                                        class="mt-4 grid gap-2 md:grid-cols-[minmax(0,1fr)_190px]"
                                                    >
                                                        <div class="relative">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#a47c5e]" aria-hidden="true">
                                                                <circle cx="11" cy="11" r="6.5"></circle>
                                                                <path d="m16 16 4 4"></path>
                                                            </svg>
                                                            <input
                                                                type="search"
                                                                x-model.debounce.180ms="lootItemSearch"
                                                                placeholder="Buscar item neste Espólio..."
                                                                class="h-11 w-full rounded-xl border border-[#cdbb9f] bg-[#faf7f1] pl-9 pr-3 text-[10px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                                            >
                                                        </div>

                                                        <select
                                                            x-model="lootRewardTypeFilter"
                                                            class="h-11 rounded-xl border border-[#cdbb9f] bg-[#faf7f1] px-3 text-[9px] font-bold text-[#76553f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                                        >
                                                            <option value="all">Todos os tipos</option>
                                                            <option value="items">Itens</option>
                                                            <option value="coins">Moedas</option>
                                                        </select>
                                                    </div>

                                                    {{-- FORMULÁRIO DE MOEDAS — agora dentro da aba de itens --}}
                                                    <div
                                                        x-show="lootCurrencyFormOpen"
                                                        x-cloak
                                                        class="mt-4 rounded-2xl border border-[#d8c7ab]/70 bg-[#f8f3ec] p-4"
                                                    >
                                                        <div class="flex items-center justify-between gap-3">
                                                            <div>
                                                                <p class="font-serif text-[16px] font-black text-[#53150f]">Punhado de moedas</p>
                                                                <p class="mt-1 text-[9px] text-[#8c6239]">Adicione as moedas como uma recompensa comum. A regra de distribuição é escolhida ao liberar.</p>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                class="grid h-8 w-8 place-items-center rounded-lg text-lg font-black text-[#8c6239] hover:bg-white"
                                                                @click="lootCurrencyFormOpen = false"
                                                            >×</button>
                                                        </div>

                                                        <input
                                                            type="text"
                                                            maxlength="120"
                                                            x-model="lootCurrencyLabel"
                                                            placeholder="Nome opcional, ex.: Bolsa do capitão"
                                                            class="mt-3 h-11 w-full rounded-xl border border-[#cdbb9f] bg-white px-3.5 text-[10px] font-semibold text-[#53150f]"
                                                        >

                                                        <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                                            <template x-for="coin in lootCoinDefinitions" :key="'coin-form-' + coin.key">
                                                                <label>
                                                                    <span class="mb-1 block text-[8px] font-black uppercase tracking-wider text-[#8c6239]" x-text="coin.abbr"></span>
                                                                    <input
                                                                        type="number"
                                                                        min="0"
                                                                        max="999999999"
                                                                        x-model.number="lootCurrencyAmounts[coin.key]"
                                                                        class="h-11 w-full rounded-xl border border-[#cdbb9f] bg-white px-3 text-[11px] font-black text-[#53150f]"
                                                                    >
                                                                </label>
                                                            </template>
                                                        </div>

                                                        <div class="mt-4 flex justify-end gap-2">
                                                            <button
                                                                type="button"
                                                                class="rounded-xl px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#8c6239]"
                                                                @click="lootCurrencyFormOpen = false; resetLootCurrencyForm()"
                                                            >Cancelar</button>

                                                            <button
                                                                type="button"
                                                                class="rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white disabled:opacity-50"
                                                                :disabled="lootAddingCurrency"
                                                                @click="addLootCurrency()"
                                                            >
                                                                <span x-show="!lootAddingCurrency">Adicionar recompensa</span>
                                                                <span x-show="lootAddingCurrency" x-cloak>Adicionando...</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div
                                                        x-show="lootVisibleRewardCount(lootActiveDrop) > 0"
                                                        class="mt-4 space-y-3"
                                                    >
                                                        {{-- ITENS DO VAULT --}}
                                                        <template x-for="item in lootVisibleItems(lootActiveDrop)" :key="'loot-item-' + item.id">
                                                            <article class="rounded-2xl border border-[#e0d2bd]/80 bg-[#fffdfa] p-3.5 transition hover:border-[#c7ad87] hover:shadow-[0_6px_18px_rgba(83,21,15,.04)]">
                                                                <div class="grid gap-3 lg:grid-cols-[72px_minmax(0,1fr)_auto] lg:items-start">
                                                                    <div class="flex h-[72px] w-[72px] items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab] bg-[#f5efe5]">
                                                                        <template x-if="lootItemImageUrl(lootActiveDrop, item)">
                                                                            <img :src="lootItemImageUrl(lootActiveDrop, item)" :alt="item.name" class="h-full w-full object-cover">
                                                                        </template>
                                                                        <template x-if="!lootItemImageUrl(lootActiveDrop, item)">
                                                                            <span class="font-serif text-2xl font-black text-[#a28670]">◇</span>
                                                                        </template>
                                                                    </div>

                                                                    <div class="min-w-0">
                                                                        <div class="flex flex-wrap items-center gap-2">
                                                                            <h6 class="font-serif text-[17px] font-black text-[#53150f]" x-text="item.name"></h6>

                                                                            <span
                                                                                class="rounded-full border px-2.5 py-1 text-[7px] font-black uppercase tracking-wide"
                                                                                :class="lootStatusClass(item.release_status)"
                                                                                x-text="lootItemStatusLabel(item.release_status)"
                                                                            ></span>
                                                                        </div>

                                                                        <p class="mt-1 text-[9px] font-bold text-[#8c6239]">
                                                                            <span x-text="lootVaultClassification(item)"></span>
                                                                            <span> · </span>
                                                                            <strong x-text="Math.max(0, parseInt(item.quantity_remaining) || 0)"></strong>/<span x-text="Math.max(0, parseInt(item.quantity_total) || 0)"></span> disponível
                                                                        </p>

                                                                        <p
                                                                            x-show="item.description"
                                                                            class="mt-2 line-clamp-2 text-[10px] leading-5 text-[#76553f]"
                                                                            x-text="item.description"
                                                                        ></p>

                                                                        <div class="mt-2.5 flex flex-wrap gap-1.5 text-[8px] font-semibold text-[#8c725e]">
                                                                            <span x-show="item?.weight" class="rounded-full bg-[#f3ece2] px-2 py-1"><span x-text="item.weight"></span> kg</span>
                                                                            <span x-show="item?.type" class="rounded-full bg-[#f3ece2] px-2 py-1" x-text="item.type"></span>
                                                                            <span x-show="item?.rarity" class="rounded-full bg-[#f3ece2] px-2 py-1" x-text="item.rarity"></span>
                                                                            <span
                                                                                x-show="item.release_status === 'released'"
                                                                                class="rounded-full bg-[#f3ece2] px-2 py-1"
                                                                            >Visível para <span x-text="lootAudienceLabel(item)"></span></span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex flex-wrap gap-2 lg:max-w-[240px] lg:justify-end">
                                                                        <button
                                                                            type="button"
                                                                            x-show="lootActiveDrop?.status !== 'closed' && item.release_status !== 'released' && parseInt(item.quantity_remaining) > 0"
                                                                            class="rounded-xl bg-[#6b1d14] px-3.5 py-2.5 text-[8px] font-black uppercase tracking-wider text-white"
                                                                            @click="beginLootItemRelease(item)"
                                                                        >Liberar</button>

                                                                        <button
                                                                            type="button"
                                                                            x-show="lootActiveDrop?.status !== 'closed' && item.release_status === 'released' && parseInt(item.quantity_remaining) > 0"
                                                                            class="rounded-xl border border-[#cdbb9f] bg-white px-3.5 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]"
                                                                            @click="withdrawLootItem(item)"
                                                                        >Recolher</button>

                                                                        <button
                                                                            type="button"
                                                                            x-show="lootActiveDrop?.status !== 'closed' && lootCanDeleteItem(item)"
                                                                            class="rounded-xl px-3 py-2.5 text-[8px] font-black uppercase tracking-wider text-red-700 hover:bg-red-50"
                                                                            @click="deleteLootItem(item)"
                                                                        >Excluir</button>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="parseInt(lootReleaseItemId) === parseInt(item.id)"
                                                                    x-cloak
                                                                    class="mt-4 rounded-2xl border border-[#d8c7ab]/70 bg-[#f8f3ec] p-4"
                                                                >
                                                                    <p class="font-serif text-[15px] font-black text-[#53150f]">Quem pode ver este item?</p>

                                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                                        <button
                                                                            type="button"
                                                                            class="rounded-xl border px-3 py-2 text-[9px] font-black"
                                                                            :class="lootReleaseVisibility === 'party' ? 'border-[#6b1d14] bg-[#6b1d14] text-white' : 'border-[#cdbb9f] bg-white text-[#76553f]'"
                                                                            @click="lootReleaseVisibility = 'party'; lootReleaseCharacterIds = []"
                                                                        >Party inteira</button>

                                                                        <button
                                                                            type="button"
                                                                            class="rounded-xl border px-3 py-2 text-[9px] font-black"
                                                                            :class="lootReleaseVisibility === 'restricted' ? 'border-[#6b1d14] bg-[#6b1d14] text-white' : 'border-[#cdbb9f] bg-white text-[#76553f]'"
                                                                            @click="lootReleaseVisibility = 'restricted'"
                                                                        >Personagens específicos</button>
                                                                    </div>

                                                                    <div x-show="lootReleaseVisibility === 'restricted'" x-cloak class="mt-3 flex flex-wrap gap-2">
                                                                        <template x-for="character in lootCharacters" :key="'item-recipient-' + character.id">
                                                                            <button
                                                                                type="button"
                                                                                class="rounded-full border px-3 py-2 text-[9px] font-black"
                                                                                :class="lootItemRecipientSelected(character.id) ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-[#cdbb9f] bg-white text-[#76553f]'"
                                                                                @click="toggleLootItemRecipient(character.id)"
                                                                                x-text="character.name"
                                                                            ></button>
                                                                        </template>
                                                                    </div>

                                                                    <div class="mt-4 flex justify-end gap-2">
                                                                        <button type="button" class="rounded-lg px-3 py-2 text-[8px] font-black uppercase text-[#8c6239]" @click="cancelLootItemRelease()">Cancelar</button>
                                                                        <button type="button" class="rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase text-white" @click="releaseLootItem(item)">Confirmar liberação</button>
                                                                    </div>
                                                                </div>
                                                            </article>
                                                        </template>

                                                        {{-- MOEDAS COMO RECOMPENSA --}}
                                                        <template x-for="currency in lootVisibleCurrencies(lootActiveDrop)" :key="'loot-currency-item-' + currency.id">
                                                            <article class="rounded-2xl border border-[#e0d2bd]/80 bg-[#fffdfa] p-3.5 transition hover:border-[#c7ad87] hover:shadow-[0_6px_18px_rgba(83,21,15,.04)]">
                                                                <div class="grid gap-3 lg:grid-cols-[72px_minmax(0,1fr)_auto] lg:items-start">
                                                                    <div class="grid h-[72px] w-[72px] place-items-center overflow-hidden rounded-xl border border-[#d8c7ab] bg-[linear-gradient(145deg,#f8edce,#efe0b2)]">
                                                                        <svg viewBox="0 0 64 64" fill="none" class="h-12 w-12" aria-hidden="true">
                                                                            <ellipse cx="31" cy="44" rx="20" ry="8" fill="#B87825" opacity=".26"></ellipse>
                                                                            <circle cx="24" cy="37" r="13" fill="#DCA63C" stroke="#9B651E" stroke-width="2"></circle>
                                                                            <circle cx="40" cy="34" r="11" fill="#E7B64A" stroke="#9B651E" stroke-width="2"></circle>
                                                                            <circle cx="35" cy="45" r="9" fill="#C98D2F" stroke="#8B571B" stroke-width="2"></circle>
                                                                            <path d="M24 30v14M19 37h10" stroke="#FFF0B8" stroke-width="2" stroke-linecap="round" opacity=".85"></path>
                                                                        </svg>
                                                                    </div>

                                                                    <div class="min-w-0">
                                                                        <div class="flex flex-wrap items-center gap-2">
                                                                            <h6
                                                                                class="font-serif text-[17px] font-black text-[#53150f]"
                                                                                x-text="currency.label || 'Punhado de moedas'"
                                                                            ></h6>

                                                                            <span
                                                                                class="rounded-full border px-2.5 py-1 text-[7px] font-black uppercase tracking-wide"
                                                                                :class="lootStatusClass(currency.release_status)"
                                                                                x-text="lootItemStatusLabel(currency.release_status)"
                                                                            ></span>
                                                                        </div>

                                                                        <p class="mt-1 text-[9px] font-bold text-[#8c6239]">
                                                                            Tesouro · moedas
                                                                        </p>

                                                                        <div class="mt-2.5 flex flex-wrap gap-2">
                                                                            <template x-for="coin in lootCurrencyParts(currency, false)" :key="'currency-chip-' + currency.id + '-' + coin.key">
                                                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-[#dfcfb8] bg-[#f7f0e4] px-2.5 py-1.5 text-[9px] font-black text-[#6b4b35]">
                                                                                    <span class="h-2.5 w-2.5 rounded-full border border-[#b88a3f] bg-[#e3b85a]"></span>
                                                                                    <span x-text="coin.amount + ' ' + coin.abbr"></span>
                                                                                </span>
                                                                            </template>
                                                                        </div>

                                                                        <div class="mt-2.5 flex flex-wrap gap-1.5 text-[8px] font-semibold text-[#8c725e]">
                                                                            <span class="rounded-full bg-[#f3ece2] px-2 py-1">Tesouro</span>
                                                                            <span class="rounded-full bg-[#f3ece2] px-2 py-1">Moedas</span>
                                                                            <span
                                                                                x-show="currency.release_status === 'released'"
                                                                                class="rounded-full bg-[#f3ece2] px-2 py-1"
                                                                                x-text="lootDistributionLabel(currency.distribution_mode)"
                                                                            ></span>
                                                                            <span
                                                                                x-show="currency.release_status === 'released'"
                                                                                class="rounded-full bg-[#f3ece2] px-2 py-1"
                                                                            >Restante: <strong x-text="lootCurrencyInline(currency, true)"></strong></span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex flex-wrap gap-2 lg:max-w-[240px] lg:justify-end">
                                                                        <button
                                                                            type="button"
                                                                            x-show="lootActiveDrop?.status !== 'closed' && currency.release_status !== 'released'"
                                                                            class="rounded-xl bg-[#6b1d14] px-3.5 py-2.5 text-[8px] font-black uppercase tracking-wider text-white"
                                                                            @click="beginLootCurrencyRelease(currency)"
                                                                        >Liberar</button>

                                                                        <button
                                                                            type="button"
                                                                            x-show="lootActiveDrop?.status !== 'closed' && currency.release_status === 'released'"
                                                                            class="rounded-xl border border-[#cdbb9f] bg-white px-3.5 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]"
                                                                            @click="withdrawLootCurrency(currency)"
                                                                        >Recolher</button>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="parseInt(lootReleaseCurrencyId) === parseInt(currency.id)"
                                                                    x-cloak
                                                                    class="mt-4 rounded-2xl border border-[#d8c7ab]/70 bg-[#f8f3ec] p-4"
                                                                >
                                                                    <div>
                                                                        <p class="font-serif text-[16px] font-black text-[#53150f]">Como estas moedas serão divididas?</p>
                                                                        <p class="mt-1 text-[9px] leading-5 text-[#8c6239]">Escolha a regra antes de disponibilizar esta recompensa.</p>
                                                                    </div>

                                                                    <div class="mt-4 grid gap-3 lg:grid-cols-3">
                                                                        <button
                                                                            type="button"
                                                                            class="rounded-2xl border p-4 text-left transition"
                                                                            :class="lootCurrencyDistribution === 'first_claim' ? 'border-[#6b1d14] bg-[#fff7ef] shadow-sm' : 'border-[#cdbb9f] bg-white hover:border-[#b08c62]'"
                                                                            @click="setLootCurrencyReleaseMode('first_claim')"
                                                                        >
                                                                            <strong class="block font-serif text-[14px] text-[#53150f]">Um personagem</strong>
                                                                            <span class="mt-1 block text-[9px] leading-4 text-[#76553f]">Uma ficha recebe todo o valor restante.</span>
                                                                        </button>

                                                                        <button
                                                                            type="button"
                                                                            class="rounded-2xl border p-4 text-left transition"
                                                                            :class="lootCurrencyDistribution === 'split' ? 'border-[#6b1d14] bg-[#fff7ef] shadow-sm' : 'border-[#cdbb9f] bg-white hover:border-[#b08c62]'"
                                                                            @click="setLootCurrencyReleaseMode('split')"
                                                                        >
                                                                            <strong class="block font-serif text-[14px] text-[#53150f]">Dividir igualmente</strong>
                                                                            <span class="mt-1 block text-[9px] leading-4 text-[#76553f]">O sistema calcula a parte dos participantes.</span>
                                                                        </button>

                                                                        <button
                                                                            type="button"
                                                                            class="rounded-2xl border p-4 text-left transition"
                                                                            :class="lootCurrencyDistribution === 'open' ? 'border-[#6b1d14] bg-[#fff7ef] shadow-sm' : 'border-[#cdbb9f] bg-white hover:border-[#b08c62]'"
                                                                            @click="setLootCurrencyReleaseMode('open')"
                                                                        >
                                                                            <strong class="block font-serif text-[14px] text-[#53150f]">Caixa da Party</strong>
                                                                            <span class="mt-1 block text-[9px] leading-4 text-[#76553f]">Todos veem o saldo e escolhem quanto retirar.</span>
                                                                        </button>
                                                                    </div>

                                                                    <div
                                                                        x-show="lootCurrencyDistribution === 'first_claim' || lootCurrencyDistribution === 'split'"
                                                                        x-cloak
                                                                        class="mt-4"
                                                                    >
                                                                        <div class="flex items-center justify-between gap-3">
                                                                            <p
                                                                                class="text-[8px] font-black uppercase tracking-wider text-[#8c6239]"
                                                                                x-text="lootCurrencyDistribution === 'first_claim' ? 'Escolha o personagem' : 'Participantes da divisão'"
                                                                            ></p>
                                                                            <p x-show="lootCurrencyDistribution === 'split'" class="text-[8px] text-[#9a806d]">Nenhuma seleção = todos os ativos</p>
                                                                        </div>

                                                                        <div class="mt-2 flex flex-wrap gap-2">
                                                                            <template x-for="character in lootCharacters" :key="'currency-recipient-' + character.id">
                                                                                <button
                                                                                    type="button"
                                                                                    class="rounded-full border px-3 py-2 text-[9px] font-black"
                                                                                    :class="lootCurrencyRecipientSelected(character.id) ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-[#cdbb9f] bg-white text-[#76553f]'"
                                                                                    @click="toggleLootCurrencyRecipient(character.id)"
                                                                                    x-text="character.name"
                                                                                ></button>
                                                                            </template>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-4 flex justify-end gap-2">
                                                                        <button type="button" class="rounded-lg px-3 py-2 text-[8px] font-black uppercase text-[#8c6239]" @click="cancelLootCurrencyRelease()">Cancelar</button>
                                                                        <button type="button" class="rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase text-white" @click="releaseLootCurrency(currency)">Confirmar regra</button>
                                                                    </div>
                                                                </div>
                                                            </article>
                                                        </template>
                                                    </div>

                                                    <div
                                                        x-show="lootDropRewardCount(lootActiveDrop) > 0 && lootVisibleRewardCount(lootActiveDrop) === 0"
                                                        x-cloak
                                                        class="mt-4 rounded-2xl border border-dashed border-[#cdbb9f] bg-[#faf7f1] px-6 py-10 text-center"
                                                    >
                                                        <p class="font-serif text-base font-black text-[#53150f]">Nenhuma recompensa corresponde ao filtro</p>
                                                        <button
                                                            type="button"
                                                            class="mt-2 text-[8px] font-black uppercase tracking-wider text-[#6b1d14] hover:underline"
                                                            @click="lootItemSearch = ''; lootRewardTypeFilter = 'all'; lootItemStatusFilter = 'all'"
                                                        >Limpar filtros</button>
                                                    </div>

                                                    <div
                                                        x-show="lootDropRewardCount(lootActiveDrop) === 0 && !lootCurrencyFormOpen"
                                                        x-cloak
                                                        class="mt-4 rounded-2xl border border-dashed border-[#cdbb9f] bg-[#faf7f1] px-6 py-12 text-center"
                                                    >
                                                        <p class="font-serif text-lg font-black text-[#53150f]">Este Espólio ainda está vazio</p>
                                                        <p class="mt-1 text-[9px] text-[#8c6239]">Adicione um item do Vault ou um punhado de moedas.</p>

                                                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                                                            <button
                                                                type="button"
                                                                class="rounded-xl bg-[#6b1d14] px-4 py-3 text-[8px] font-black uppercase tracking-wider text-white"
                                                                @click="openLootPicker()"
                                                            >Adicionar do Vault</button>

                                                            <button
                                                                type="button"
                                                                class="rounded-xl border border-[#cdbb9f] bg-white px-4 py-3 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]"
                                                                @click="lootCurrencyFormOpen = true"
                                                            >Adicionar moedas</button>
                                                        </div>
                                                    </div>
                                                </section>

                                                {{-- HISTÓRICO --}}
                                                <section
                                                    x-show="lootContentTab === 'history'"
                                                    x-cloak
                                                    class="rounded-2xl border border-[#d8c7ab]/70 bg-white p-4"
                                                >
                                                    <div>
                                                        <h5 class="font-serif text-[18px] font-black text-[#53150f]">Histórico do Espólio</h5>
                                                        <p class="mt-1 text-[11px] text-[#8c6239]">Aqui fica o que já saiu do Espólio. A administração dos itens permanece limpa nas outras abas.</p>
                                                    </div>

                                                    <div x-show="lootDropClaimCount(lootActiveDrop) > 0" class="mt-4 space-y-4">
                                                        <template x-for="item in (lootActiveDrop?.items ?? [])" :key="'history-item-' + item.id">
                                                            <div x-show="lootClaims(item).length > 0" class="rounded-2xl border border-[#d8c7ab]/70 bg-[#faf7f1] p-4">
                                                                <h6 class="font-serif text-[15px] font-black text-[#53150f]" x-text="item.name"></h6>
                                                                <div class="mt-3 space-y-2">
                                                                    <template x-for="claim in lootClaims(item)" :key="'history-claim-' + claim.id">
                                                                        <div class="flex flex-col gap-2 rounded-xl border border-[#e0d2bd] bg-white px-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                                                                            <div>
                                                                                <p class="text-[11px] font-black text-[#53150f]" x-text="lootClaimDestinationLabel(claim)"></p>
                                                                                <p class="mt-0.5 text-[9px] text-[#8c6239]">
                                                                                    <span x-text="(claim.quantity ?? 1) + '×'"></span>
                                                                                    <span> · </span>
                                                                                    <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                                                    <template x-if="claim.reversed_at"><span> · recolhido <span x-text="lootClaimDate(claim.reversed_at)"></span></span></template>
                                                                                </p>
                                                                            </div>

                                                                            <button
                                                                                type="button"
                                                                                x-show="!claim.reversed_at && claim.character_item"
                                                                                x-cloak
                                                                                class="shrink-0 rounded-xl border border-[#cdbb9f] bg-white px-3 py-2 text-[9px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#f7f0e6] disabled:opacity-50"
                                                                                :disabled="lootReversingClaimId !== null"
                                                                                @click="reverseLootClaim(item, claim)"
                                                                            >Recolher do inventário</button>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <template x-for="currency in (lootActiveDrop?.currencies ?? [])" :key="'history-currency-' + currency.id">
                                                            <div x-show="lootCurrencyClaims(currency).length > 0" class="rounded-2xl border border-[#d8c7ab]/70 bg-[#faf7f1] p-4">
                                                                <h6 class="font-serif text-[15px] font-black text-[#53150f]" x-text="currency.label || 'Moedas'"></h6>
                                                                <div class="mt-3 space-y-2">
                                                                    <template x-for="claim in lootCurrencyClaims(currency)" :key="'history-currency-claim-' + claim.id">
                                                                        <div class="rounded-xl border border-[#e0d2bd] bg-white px-3 py-3">
                                                                            <p class="text-[11px] font-black text-[#53150f]" x-text="claim?.character?.name || 'Personagem'"></p>
                                                                            <p class="mt-0.5 text-[9px] text-[#8c6239]">
                                                                                <span x-text="lootCurrencyClaimInline(claim)"></span>
                                                                                <span> · </span>
                                                                                <span x-text="lootClaimDate(claim.claimed_at)"></span>
                                                                            </p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <div x-show="lootDropClaimCount(lootActiveDrop) === 0" x-cloak class="mt-4 rounded-2xl border border-dashed border-[#cdbb9f] bg-[#faf7f1] px-6 py-12 text-center">
                                                        <p class="font-serif text-lg font-black text-[#53150f]">Nenhum movimento ainda</p>
                                                        <p class="mt-1 text-[10px] text-[#8c6239]">Quando alguém retirar um item ou moedas, o registro aparecerá aqui.</p>
                                                    </div>
                                                </section>

                                            </div>
                                        </template>

                                        <section
                                            x-show="lootWorkspaceMode === 'loot' && !lootActiveDrop && lootActiveFolder"
                                            x-cloak
                                            class="space-y-3"
                                        >
                                            {{-- ====================================================
                                                 VISÃO DO LOCAL
                                                 Cabeçalho compacto + lista clara de Espólios.
                                            ===================================================== --}}
                                            <section class="sb-loot-location-hero p-4">
                                                <div class="grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)_auto] lg:items-center">
                                                    <div class="sb-loot-location-cover relative h-[145px] overflow-hidden border">
                                                        <template x-if="lootActiveFolder?.image_url">
                                                            <img
                                                                :src="lootActiveFolder.image_url"
                                                                :alt="lootActiveFolder?.name"
                                                                class="absolute inset-0 h-full w-full object-cover"
                                                            >
                                                        </template>
                                                        <template x-if="!lootActiveFolder?.image_url">
                                                            <div class="absolute inset-0 grid place-items-center bg-[linear-gradient(135deg,#6b1d14,#2b1712)]">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-10 w-10 text-white/70" aria-hidden="true">
                                                                    <path d="M3 19.5 8.25 13l3.25 3.5 2.5-3 7 6"></path>
                                                                    <path d="M4 5h16v14H4z"></path>
                                                                    <circle cx="15.5" cy="9" r="1.5"></circle>
                                                                </svg>
                                                            </div>
                                                        </template>
                                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                                    </div>

                                                    <div class="min-w-0">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <h4 class="font-serif text-[28px] font-black leading-tight text-[#53150f]" x-text="lootActiveFolder?.name"></h4>
                                                            <span class="rounded-full border border-[#d8c7ab] bg-[#f8f1e8] px-2.5 py-1 text-[8px] font-black text-[#76553f]">
                                                                <span x-text="lootFolderDropCount(lootActiveFolder?.id)"></span>
                                                                <span x-text="lootFolderDropCount(lootActiveFolder?.id) === 1 ? ' Espólio' : ' Espólios'"></span>
                                                            </span>
                                                        </div>

                                                        <p
                                                            x-show="lootActiveFolder?.description"
                                                            class="mt-2 max-w-2xl text-[11px] leading-[1.55] text-[#6f5240]"
                                                            x-text="lootActiveFolder?.description"
                                                        ></p>
                                                        <p
                                                            x-show="!lootActiveFolder?.description"
                                                            x-cloak
                                                            class="mt-2 text-[10px] italic text-[#a18a76]"
                                                        >Sem descrição para este local.</p>
                                                    </div>

                                                    <div class="flex flex-wrap gap-2 lg:w-[150px] lg:flex-col">
                                                        <button
                                                            type="button"
                                                            class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#6b1d14] px-4 text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f]"
                                                            @click="openLootCreate(lootActiveFolder?.id)"
                                                        >+ Novo Espólio</button>
                                                        <button
                                                            type="button"
                                                            class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#d8c7ab] bg-white px-4 text-[8px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#faf7f1]"
                                                            @click="openLootFolderEditor(lootActiveFolder)"
                                                        >Editar pasta</button>
                                                    </div>
                                                </div>
                                            </section>

                                            <section class="sb-loot-section-card p-4">
                                                <div class="sb-loot-section-head flex flex-wrap items-center justify-between gap-3 border-b pb-3">
                                                    <div>
                                                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#a47c5e]">Espólios deste local</p>
                                                        <h5 class="mt-1 font-serif text-[19px] font-black text-[#53150f]">Tesouros preparados</h5>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        class="rounded-xl border border-[#d8c7ab] bg-[#faf7f1] px-3 py-2 text-[8px] font-black uppercase tracking-wider text-[#76553f] hover:bg-white"
                                                        @click="openLootCreate(lootActiveFolder?.id)"
                                                    >Adicionar</button>
                                                </div>

                                                <div class="mt-3 space-y-2">
                                                    <template x-for="drop in lootFolderDrops(lootActiveFolder?.id)" :key="'folder-overview-drop-' + drop.id">
                                                        <button
                                                            type="button"
                                                            class="sb-loot-reward-card group flex w-full items-center gap-3 border p-3 text-left transition"
                                                            @click="selectLootDrop(drop); lootContentTab = 'items'"
                                                        >
                                                            <span class="relative flex h-[66px] w-[82px] shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#d8c7ab] bg-[#efe4d5]">
                                                                <template x-if="lootDropCoverUrl(drop)">
                                                                    <img :src="lootDropCoverUrl(drop)" :alt="drop.name" class="h-full w-full object-cover">
                                                                </template>
                                                                <template x-if="!lootDropCoverUrl(drop)">
                                                                    <span class="font-serif text-xl font-black text-[#a28670]">◇</span>
                                                                </template>
                                                            </span>

                                                            <span class="min-w-0 flex-1">
                                                                <span class="flex flex-wrap items-center gap-2">
                                                                    <strong class="truncate font-serif text-[15px] font-black text-[#53150f]" x-text="drop.name"></strong>
                                                                    <span
                                                                        class="rounded-full px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                                                                        :class="drop.status === 'released' ? 'bg-emerald-100 text-emerald-800' : (drop.status === 'closed' ? 'bg-stone-200 text-stone-700' : 'bg-amber-100 text-amber-800')"
                                                                        x-text="drop.status === 'released' ? 'Liberado' : (drop.status === 'closed' ? 'Encerrado' : 'Preparado')"
                                                                    ></span>
                                                                </span>
                                                                <span
                                                                    x-show="drop?.description"
                                                                    class="mt-1.5 block line-clamp-2 text-[10px] leading-[1.5] text-[#6f5240]"
                                                                    x-text="drop.description"
                                                                ></span>
                                                                <span class="mt-2 flex flex-wrap items-center gap-3 text-[8px] font-bold text-[#9a806d]">
                                                                    <span><strong class="text-[#53150f]" x-text="lootDropRewardCount(drop)"></strong> recompensas</span>
                                                                    <span x-show="drop?.updated_at">Atualizado <span x-text="lootClaimDate(drop.updated_at)"></span></span>
                                                                </span>
                                                            </span>

                                                            <span class="shrink-0 text-[18px] text-[#b08c62] transition group-hover:translate-x-0.5">›</span>
                                                        </button>
                                                    </template>

                                                    <div
                                                        x-show="lootFolderDrops(lootActiveFolder?.id).length === 0"
                                                        x-cloak
                                                        class="rounded-2xl border border-dashed border-[#d8c7ab] bg-[#faf7f1] px-5 py-10 text-center"
                                                    >
                                                        <span class="mx-auto grid h-11 w-11 place-items-center rounded-xl bg-[#efe2d1] text-xl font-black text-[#8a4b20]">+</span>
                                                        <p class="mt-3 font-serif text-lg font-black text-[#53150f]">Nenhum Espólio neste local</p>
                                                        <p class="mt-1 text-[10px] text-[#8c6239]">Crie o primeiro conjunto de recompensas da pasta.</p>
                                                        <button
                                                            type="button"
                                                            class="mt-4 rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white"
                                                            @click="openLootCreate(lootActiveFolder?.id)"
                                                        >Criar Espólio</button>
                                                    </div>
                                                </div>
                                            </section>
                                        </section>

                                        <section
                                            x-show="lootWorkspaceMode === 'loot' && !lootActiveDrop && !lootActiveFolder"
                                            x-cloak
                                            class="space-y-3"
                                        >
                                            <section class="sb-loot-overview-hero p-5">
                                                <div class="flex flex-wrap items-start justify-between gap-4">
                                                    <div class="max-w-2xl">
                                                        <p class="text-[8px] font-black uppercase tracking-[0.16em] text-[#a47c5e]">Visão geral</p>
                                                        <h4 class="mt-1 font-serif text-[25px] font-black text-[#53150f]">Espólios da campanha</h4>
                                                        <p class="mt-1.5 text-[10px] leading-5 text-[#8c6239]">Selecione um local para organizar seus tesouros ou abra um Espólio diretamente pela lista à esquerda.</p>
                                                    </div>

                                                    <div class="flex flex-wrap gap-2">
                                                        <button
                                                            type="button"
                                                            class="rounded-xl border border-[#d8c7ab] bg-white px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14] hover:bg-[#faf7f1]"
                                                            @click="openLootCreate(null)"
                                                        >+ Espólio sem pasta</button>
                                                        <button
                                                            type="button"
                                                            class="sb-loot-folder-action rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white hover:bg-[#53150f]"
                                                            @click="openLootFolderEditor()"
                                                        >
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path>
                                                                <path d="M3.5 7.5V5h6l1.5 2.5"></path>
                                                            </svg>
                                                            <span>+ Nova pasta</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </section>

                                            <section class="sb-loot-section-card p-4">
                                                <div class="sb-loot-section-head flex items-center justify-between gap-3 border-b pb-3">
                                                    <div>
                                                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#a47c5e]">Organização</p>
                                                        <h5 class="mt-1 font-serif text-[19px] font-black text-[#53150f]">Locais da campanha</h5>
                                                    </div>
                                                    <span class="rounded-full border border-[#d8c7ab] bg-[#f8f1e8] px-2.5 py-1 text-[8px] font-black text-[#76553f]" x-text="lootFolders.length"></span>
                                                </div>

                                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                    <template x-for="folder in lootFolders" :key="'loot-overview-folder-' + folder.id">
                                                        <button
                                                            type="button"
                                                            class="sb-loot-overview-card group overflow-hidden rounded-xl border text-left transition hover:-translate-y-0.5"
                                                            @click="selectLootFolder(folder)"
                                                        >
                                                            <div class="grid grid-cols-[128px_minmax(0,1fr)]">
                                                                <div class="relative min-h-[112px] overflow-hidden border-r border-[#e3d5c2] bg-[#f3ece2]">
                                                                    <template x-if="folder?.image_url">
                                                                        <img :src="folder.image_url" :alt="folder.name" class="absolute inset-0 h-full w-full object-cover">
                                                                    </template>
                                                                    <template x-if="!folder?.image_url">
                                                                        <div class="absolute inset-0 grid place-items-center bg-[linear-gradient(135deg,#f1e8dc,#e4d5c2)]">
                                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-8 w-8 text-[#9a806d]" aria-hidden="true"><path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path><path d="M3.5 7.5V5h6l1.5 2.5"></path></svg>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <div class="flex min-w-0 flex-col justify-center p-4">
                                                                    <div class="flex items-start justify-between gap-3">
                                                                        <div class="min-w-0">
                                                                            <p class="truncate font-serif text-[16px] font-black text-[#53150f]" x-text="folder.name"></p>
                                                                            <p
                                                                                x-show="folder?.description"
                                                                                class="mt-1.5 line-clamp-2 text-[10px] leading-[1.5] text-[#6f5240]"
                                                                                x-text="folder.description"
                                                                            ></p>
                                                                        </div>
                                                                        <span class="rounded-full border border-[#d8c7ab] bg-white px-2 py-1 text-[8px] font-black text-[#76553f]" x-text="lootFolderDropCount(folder.id)"></span>
                                                                    </div>
                                                                    <span class="mt-3 text-[9px] font-black uppercase tracking-wider text-[#8a4b20]">Abrir local →</span>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </template>

                                                    <button
                                                        type="button"
                                                        class="min-h-[112px] rounded-2xl border border-dashed border-[#cdbb9f] bg-[#faf7f1] px-5 py-7 text-center transition hover:border-[#8a4b20] hover:bg-white"
                                                        @click="openLootFolderEditor()"
                                                    >
                                                        <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-[#efe2d1] text-lg font-black text-[#8a4b20]">+</span>
                                                        <strong class="mt-2 block font-serif text-[14px] text-[#53150f]">Criar novo local</strong>
                                                        <span class="mt-1 block text-[9px] leading-4 text-[#8f725c]">Organize Espólios em uma nova pasta.</span>
                                                    </button>
                                                </div>

                                                <div
                                                    x-show="lootFolders.length === 0"
                                                    x-cloak
                                                    class="mt-3 rounded-2xl border border-dashed border-[#d8c7ab] bg-[#faf7f1] px-6 py-10 text-center"
                                                >
                                                    <p class="font-serif text-lg font-black text-[#53150f]">Nenhum local criado ainda</p>
                                                    <p class="mt-1 text-[10px] text-[#8c6239]">Crie uma pasta para começar a organizar os Espólios da campanha.</p>
                                                </div>
                                            </section>
                                        </section>
    </main>

    {{-- MENU GLOBAL DE AÇÕES DO ESPÓLIO
         Fica dentro do workspace, fora das áreas com scroll.
         Não usa x-teleport: assim ele nunca permanece órfão no <body>. --}}
    <template x-if="drawer === 'loot' && lootWorkspaceMode === 'loot' && lootMenuFolder">
        <div>
            <button
                type="button"
                class="fixed inset-0 z-[99980] cursor-default bg-transparent"
                @click="lootFolderMenuId = null"
                aria-label="Fechar menu da pasta"
            ></button>

            <div
                class="sb-loot-folder-menu-floating fixed z-[99989]"
                :style="`left:${lootFolderMenuX}px;top:${lootFolderMenuY}px`"
                @keydown.escape.window="lootFolderMenuId = null"
            >
                <button
                    type="button"
                    @click.stop="openLootFolderEditor(lootMenuFolder); lootFolderMenuId = null"
                >Editar pasta</button>
                <button
                    type="button"
                    class="is-danger"
                    @click.stop="deleteLootFolder(lootMenuFolder); lootFolderMenuId = null"
                >Excluir pasta</button>
            </div>
        </div>
    </template>

    <template x-if="drawer === 'loot' && lootWorkspaceMode === 'loot' && lootMenuDrop">
        <div>
            <button
                type="button"
                x-show="lootDropMenuId !== null"
                x-cloak
                class="fixed inset-0 z-[99990] cursor-default bg-transparent"
                aria-label="Fechar menu do Espólio"
                @click="lootDropMenuId = null"
                @contextmenu.prevent="lootDropMenuId = null"
            ></button>

            <div
                x-show="lootDropMenuId !== null"
                x-cloak
                x-transition.opacity.scale.95
                @keydown.escape.window="lootDropMenuId = null"
                class="sb-loot-drop-menu fixed z-[99999]"
                :style="lootDropMenuStyle()"
                role="menu"
                aria-label="Ações do Espólio"
            >
                <button
                    type="button"
                    role="menuitem"
                    @click.stop="selectLootDrop(lootMenuDrop); lootContentTab = 'items'; lootDropMenuId = null"
                >Abrir Espólio</button>

                <button
                    type="button"
                    role="menuitem"
                    @click.stop="openLootDropEditor(lootMenuDrop); lootDropMenuId = null"
                >Editar Espólio</button>

                <button
                    type="button"
                    role="menuitem"
                    class="is-danger disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="parseInt(lootDeletingDropId) === parseInt(lootMenuDrop?.id)"
                    @click.stop="deleteLootDrop(lootMenuDrop); lootDropMenuId = null"
                    x-text="parseInt(lootDeletingDropId) === parseInt(lootMenuDrop?.id) ? 'Excluindo...' : 'Excluir Espólio'"
                ></button>
            </div>
        </div>
    </template>

    {{-- MODAL — EDITAR ESPÓLIO / FOTO DO LOCAL --}}
    <div
        x-show="lootDropEditorOpen"
        x-cloak
        class="fixed inset-0 z-[295] flex items-center justify-center p-4 sm:p-6"
        @keydown.escape.window="lootDropEditorOpen && closeLootDropEditor()"
    >
        <div
            class="absolute inset-0 bg-[#160d09]/65 backdrop-blur-[3px]"
            @click="closeLootDropEditor()"
        ></div>

        <section
            x-transition.opacity.scale.95
            class="relative z-10 flex max-h-[88vh] w-full max-w-[860px] flex-col overflow-hidden rounded-[22px] border border-[#cdbb9f] bg-[#fffdf8] shadow-[0_28px_90px_rgba(20,12,8,.34)]"
            role="dialog"
            aria-modal="true"
            aria-label="Editar Espólio"
        >
            <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#d8c7ab]/70 bg-[#f1e4d2] px-5 py-4">
                <div>
                    <p class="text-[8px] font-black uppercase tracking-[0.16em] text-[#9a806d]">Apresentação do Espólio</p>
                    <h3 class="mt-0.5 font-serif text-[22px] font-black text-[#53150f]">Editar local</h3>
                </div>

                <button
                    type="button"
                    class="grid h-9 w-9 place-items-center rounded-xl border border-[#d8c7ab] bg-[#fffdf8] text-lg font-black text-[#8c6239]"
                    @click="closeLootDropEditor()"
                    aria-label="Fechar"
                >×</button>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto p-5">
                <div
                    x-show="lootDropFeedback"
                    x-cloak
                    class="mb-4 flex items-start gap-2.5 rounded-xl border px-3.5 py-3 text-[9px] font-bold leading-4"
                    :class="{
                        'border-emerald-200 bg-emerald-50 text-emerald-800': lootDropFeedbackType === 'success',
                        'border-red-200 bg-red-50 text-red-800': lootDropFeedbackType === 'error',
                        'border-amber-200 bg-amber-50 text-amber-900': lootDropFeedbackType === 'info',
                    }"
                >
                    <span
                        class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full text-[10px]"
                        :class="lootDropFeedbackType === 'success' ? 'bg-emerald-100' : (lootDropFeedbackType === 'error' ? 'bg-red-100' : 'bg-amber-100')"
                        x-text="lootDropFeedbackType === 'success' ? '✓' : (lootDropFeedbackType === 'error' ? '!' : 'i')"
                    ></span>
                    <span x-text="lootDropFeedback"></span>
                </div>

                <div class="grid gap-5 md:grid-cols-[280px_minmax(0,1fr)]">
                    <div>
                        <label class="group relative flex aspect-[4/3] cursor-pointer items-center justify-center overflow-hidden rounded-2xl border border-[#b08c62] bg-[#f3eadf]">
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="handleLootDropImage($event)"
                            >

                            <template x-if="lootDropImagePreview">
                                <img
                                    :src="lootDropImagePreview"
                                    alt="Foto do local"
                                    class="absolute inset-0 h-full w-full object-cover"
                                >
                            </template>

                            <div
                                x-show="!lootDropImagePreview"
                                class="relative z-10 px-5 text-center text-[#8a4b20]"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="mx-auto h-10 w-10" aria-hidden="true">
                                    <path d="M4 8.5h3l1.2-2h7.6l1.2 2h3v9.5H4z"></path>
                                    <circle cx="12" cy="13" r="3"></circle>
                                </svg>
                                <span class="mt-2 block text-[8px] font-black uppercase tracking-wider">Foto deste Espólio</span>
                                <span class="mt-1 block text-[8px] leading-4 text-[#9a806d]">JPG, PNG ou WebP · até 8 MB</span>
                            </div>
                        </label>

                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <label class="cursor-pointer rounded-xl border border-[#d8c7ab] bg-white px-3 py-2.5 text-center text-[8px] font-black uppercase text-[#76553f] hover:bg-[#faf7f1]">
                                Escolher foto
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleLootDropImage($event)"
                                >
                            </label>

                            <button
                                x-show="lootDropImagePreview"
                                x-cloak
                                type="button"
                                class="rounded-xl border border-[#d8c7ab] bg-white px-3 py-2.5 text-[8px] font-black uppercase text-red-700"
                                @click="removeLootDropImage()"
                            >Remover</button>
                        </div>

                        <p
                            x-show="!lootDropImagePreview && lootDropCoverUrl(lootActiveDrop)"
                            x-cloak
                            class="mt-2 rounded-xl bg-[#f7f0e6] px-3 py-2 text-[8px] leading-4 text-[#8c725e]"
                        >Sem foto própria. O cabeçalho usa a capa da pasta como fallback.</p>
                    </div>

                    <div class="space-y-4">
                        <label class="block">
                            <span class="mb-1.5 block text-[8px] font-black uppercase tracking-[0.12em] text-[#9a806d]">Nome</span>
                            <input
                                type="text"
                                maxlength="150"
                                x-model="lootDropEditName"
                                placeholder="Ex.: Entrada"
                                class="h-11 w-full rounded-xl border border-[#cdbb9f] bg-white px-3.5 text-[11px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            >
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[8px] font-black uppercase tracking-[0.12em] text-[#9a806d]">Descrição</span>
                            <textarea
                                rows="6"
                                maxlength="10000"
                                x-model="lootDropEditDescription"
                                placeholder="Descreva rapidamente o local, a cena ou onde as recompensas foram encontradas."
                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-3.5 py-3 text-[10px] leading-5 text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            ></textarea>
                        </label>

                        <div class="rounded-2xl border border-[#e0d2bd] bg-[#faf7f1] p-4">
                            <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#a17e62]">Organização</p>
                            <p class="mt-1 font-serif text-[15px] font-black text-[#53150f]" x-text="lootDropFolderName(lootActiveDrop)"></p>
                            <p class="mt-1 text-[9px] leading-4 text-[#8c725e]">A pasta continua sendo alterada pela organização lateral. Este editor cuida apenas da apresentação do Espólio.</p>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="flex shrink-0 justify-end gap-2 border-t border-[#d8c7ab]/70 bg-[#f1e4d2] px-5 py-3.5">
                <button
                    type="button"
                    class="min-h-10 rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-5 text-[8px] font-black uppercase tracking-wider text-[#76553f]"
                    @click="closeLootDropEditor()"
                >Cancelar</button>

                <button
                    type="button"
                    class="min-h-10 rounded-xl bg-[#6b1d14] px-6 text-[8px] font-black uppercase tracking-wider text-white disabled:opacity-50"
                    :disabled="lootDropSaving || !String(lootDropEditName ?? '').trim()"
                    @click="saveLootDropPresentation()"
                >
                    <span x-show="!lootDropSaving">Salvar alterações</span>
                    <span x-show="lootDropSaving" x-cloak>Salvando...</span>
                </button>
            </footer>
        </section>
    </div>

    {{-- MODAL — PASTA DE ESPÓLIOS --}}
    <div
        x-show="lootFolderModalOpen"
        x-cloak
        class="fixed inset-0 z-[290] flex items-center justify-center p-3 sm:p-5"
        @keydown.escape.window="lootFolderModalOpen && closeLootFolderEditor()"
    >
        <div
            class="absolute inset-0 bg-[#160d09]/65 backdrop-blur-[3px]"
            @click="closeLootFolderEditor()"
        ></div>

        <section
            x-transition.opacity.scale.95
            class="relative z-10 flex max-h-[calc(100dvh-30px)] w-full max-w-[760px] flex-col overflow-hidden rounded-[22px] border border-[#cdbb9f] bg-[#fffdf8] shadow-[0_28px_90px_rgba(20,12,8,.34)]"
            role="dialog"
            aria-modal="true"
            aria-label="Editar pasta de Espólios"
        >
            <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#d8c7ab]/70 bg-[#f1e4d2] px-5 py-3.5">
                <div class="min-w-0">
                    <p class="text-[7px] font-black uppercase tracking-[0.16em] text-[#9a806d]">Organização de Espólios</p>
                    <h3
                        class="mt-0.5 truncate font-serif text-[21px] font-black leading-tight text-[#53150f]"
                        x-text="lootFolderEditingId ? 'Editar pasta' : 'Criar pasta'"
                    ></h3>
                </div>

                <button
                    type="button"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#d8c7ab] bg-[#fffdf8] text-lg font-black text-[#8c6239] transition hover:bg-white hover:text-[#53150f]"
                    @click="closeLootFolderEditor()"
                    aria-label="Fechar"
                >×</button>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                <div
                    x-show="lootFolderFeedback"
                    x-cloak
                    class="mb-3.5 flex items-start gap-2.5 rounded-xl border px-3.5 py-3 text-[9px] font-bold leading-4"
                    :class="{
                        'border-emerald-200 bg-emerald-50 text-emerald-800': lootFolderFeedbackType === 'success',
                        'border-red-200 bg-red-50 text-red-800': lootFolderFeedbackType === 'error',
                        'border-amber-200 bg-amber-50 text-amber-900': lootFolderFeedbackType === 'info',
                    }"
                >
                    <span
                        class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full text-[10px]"
                        :class="lootFolderFeedbackType === 'success' ? 'bg-emerald-100' : (lootFolderFeedbackType === 'error' ? 'bg-red-100' : 'bg-amber-100')"
                        x-text="lootFolderFeedbackType === 'success' ? '✓' : (lootFolderFeedbackType === 'error' ? '!' : 'i')"
                    ></span>
                    <span x-text="lootFolderFeedback"></span>
                </div>

                <div class="space-y-3.5">
                    <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)]">
                        <label class="block">
                            <span class="mb-1.5 block text-[7px] font-black uppercase tracking-[0.1em] text-[#9a806d]">Nome</span>
                            <input
                                type="text"
                                maxlength="120"
                                x-model="lootFolderName"
                                placeholder="Ex.: Ruínas de Rexxentrum"
                                class="h-10 w-full rounded-xl border border-[#cdbb9f] bg-white px-3.5 text-[11px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            >
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[7px] font-black uppercase tracking-[0.1em] text-[#9a806d]">Descrição do local</span>
                            <textarea
                                rows="3"
                                maxlength="10000"
                                x-model="lootFolderDescription"
                                placeholder="Resumo curto para reconhecer rapidamente este local."
                                class="min-h-[88px] w-full resize-y rounded-xl border border-[#cdbb9f] bg-white px-3.5 py-2.5 text-[10px] leading-5 text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                            ></textarea>
                        </label>
                    </div>

                    <section class="rounded-2xl border border-[#ddcdb7] bg-[#f8f3ec] p-3.5">
                        <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="text-[7px] font-black uppercase tracking-[0.12em] text-[#9a806d]">Imagem do local</p>
                                <p class="mt-1 text-[8px] leading-4 text-[#8c725e]">A imagem aparece como capa da pasta. O cabeçalho aplica contraste automaticamente.</p>
                            </div>

                            <span
                                x-show="lootFolderImagePreview"
                                x-cloak
                                class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-1 text-[7px] font-black uppercase tracking-wider text-emerald-700"
                            >Imagem definida</span>
                        </div>

                        <div class="grid gap-3 md:grid-cols-[230px_minmax(0,1fr)] md:items-stretch">
                            <label class="group relative flex h-[145px] cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-[#c7aa82] bg-[#eee3d4] transition hover:border-[#8a4b20]">
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleLootFolderImage($event)"
                                >

                                <template x-if="lootFolderImagePreview">
                                    <img
                                        :src="lootFolderImagePreview"
                                        alt="Imagem do local"
                                        class="absolute inset-0 h-full w-full object-cover"
                                    >
                                </template>

                                <div
                                    x-show="!lootFolderImagePreview"
                                    class="relative z-10 px-4 text-center text-[#8a4b20]"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="mx-auto h-8 w-8" aria-hidden="true">
                                        <path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path>
                                        <path d="M3.5 7.5V5h6l1.5 2.5"></path>
                                    </svg>
                                    <strong class="mt-2 block text-[8px] font-black uppercase tracking-wider">Selecionar imagem</strong>
                                    <span class="mt-1 block text-[7px] text-[#9a806d]">JPG, PNG ou WebP</span>
                                </div>

                                <div
                                    x-show="lootFolderImagePreview"
                                    x-cloak
                                    class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/65 to-transparent px-3 pb-2.5 pt-7 text-[7px] font-black uppercase tracking-wider text-white opacity-0 transition group-hover:opacity-100"
                                >Clique para trocar a imagem</div>
                            </label>

                            <div class="flex min-h-[145px] flex-col justify-between rounded-xl border border-[#e2d5c3] bg-[#fffdf8] p-3.5">
                                <div>
                                    <p class="font-serif text-[14px] font-black text-[#53150f]">Capa da pasta</p>
                                    <p class="mt-1.5 text-[9px] leading-4 text-[#846b58]">Use uma imagem horizontal. Ela será recortada automaticamente para preencher a capa, sem precisar configurar cor ou cor de texto.</p>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <label class="cursor-pointer rounded-lg bg-[#6b1d14] px-3.5 py-2 text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f]">
                                        <span x-text="lootFolderImagePreview ? 'Trocar imagem' : 'Escolher imagem'"></span>
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                            @change="handleLootFolderImage($event)"
                                        >
                                    </label>

                                    <button
                                        x-show="lootFolderImagePreview"
                                        x-cloak
                                        type="button"
                                        class="rounded-lg border border-[#d8c7ab] bg-white px-3.5 py-2 text-[8px] font-black uppercase tracking-wider text-red-700 transition hover:bg-red-50"
                                        @click="removeLootFolderImage()"
                                    >Remover</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <footer class="flex shrink-0 items-center justify-end gap-2 border-t border-[#d8c7ab]/70 bg-[#f1e4d2] px-5 py-3">
                <button
                    type="button"
                    class="min-h-10 rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-5 text-[8px] font-black uppercase tracking-wider text-[#76553f] transition hover:bg-white"
                    @click="closeLootFolderEditor()"
                >Cancelar</button>

                <button
                    type="button"
                    class="min-h-10 rounded-xl bg-[#6b1d14] px-6 text-[8px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f] disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="lootFolderSaving || !String(lootFolderName ?? '').trim()"
                    @click="saveLootFolder()"
                    x-text="lootFolderSaving ? 'Salvando...' : (lootFolderEditingId ? 'Salvar pasta' : 'Criar pasta')"
                ></button>
            </footer>
        </section>
    </div>

</div>