<style>
    /* ============================================================
       LOJAS V2 — MENU FLUTUANTE + HEADER REFINADO
       ============================================================ */

    .sb-shop-side-menu.sb-shop-side-menu--portal {
        position: fixed !important;
        right: auto !important;
        top: auto !important;
        z-index: 99990 !important;
        width: 174px !important;
        overflow: visible !important;
        border: 1px solid #cdbb9f !important;
        border-radius: 12px !important;
        background: #fffdf8 !important;
        padding: 5px !important;
        box-shadow:
            0 18px 42px rgba(55,30,18,.22),
            inset 0 1px 0 rgba(255,255,255,.82) !important;
    }

    .sb-master-shop-overview.sb-master-shop-overview--refined {
        overflow: visible !important;
        border-radius: 18px !important;
        border-color: rgba(205,184,155,.88) !important;
        background:
            radial-gradient(circle at 84% -30%, rgba(138,75,32,.13), transparent 36%),
            linear-gradient(135deg, #fffdf9 0%, #faf4eb 57%, #f1e4d6 100%) !important;
        box-shadow: 0 10px 28px rgba(73,42,28,.055) !important;
    }

    .sb-master-shop-overview.sb-master-shop-overview--refined::before {
        width: 4px !important;
        border-radius: 18px 0 0 18px;
        background: linear-gradient(180deg, #6b1d14 0%, #a2643d 100%) !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-overview__inner {
        padding: 13px 14px 13px 17px !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-overview__top {
        align-items: center !important;
        gap: 18px !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-identity {
        gap: 13px !important;
        align-items: center !important;
    }

    .sb-master-shop-avatar-frame {
        position: relative;
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
    }

    .sb-master-shop-overview--refined .sb-master-shop-avatar {
        width: 72px !important;
        height: 72px !important;
        flex-basis: 72px !important;
        border: 1px solid #cfb18c !important;
        border-radius: 17px !important;
        background:
            radial-gradient(circle at 50% 28%, #fffaf2 0%, #eadac7 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.9),
            0 7px 18px rgba(83,21,15,.08) !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-avatar img {
        object-fit: contain !important;
        object-position: center bottom !important;
        padding: 3px !important;
    }

    .sb-master-shop-avatar-state {
        position: absolute;
        right: -3px;
        bottom: -3px;
        display: grid;
        width: 18px;
        height: 18px;
        place-items: center;
        border: 3px solid #fbf5ec;
        border-radius: 999px;
        background: #aa9a87;
        box-shadow: 0 2px 7px rgba(63,35,22,.15);
    }

    .sb-master-shop-avatar-state::after {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #fff;
        opacity: .9;
    }

    .sb-master-shop-avatar-state.is-open { background: #168661; }

    .sb-master-shop-overview--refined .sb-master-shop-copy {
        min-width: 0 !important;
        padding-top: 1px;
    }

    .sb-master-shop-overview--refined .sb-master-shop-kicker {
        display: block !important;
        margin: 0 0 3px !important;
        color: #a17b5e !important;
        font-size: 7px !important;
        letter-spacing: .13em !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-title-row { gap: 8px !important; }

    .sb-master-shop-overview--refined .sb-master-shop-title {
        font-size: clamp(22px, 2.1vw, 29px) !important;
        line-height: .98 !important;
        letter-spacing: -.015em;
    }

    .sb-master-shop-overview--refined .sb-master-shop-status {
        min-height: 27px;
        padding: 0 9px !important;
        font-size: 7.5px !important;
        letter-spacing: .055em !important;
    }

    .sb-master-shop-shopkeeper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        margin-top: 6px;
        color: #886a55;
        font-size: 8.5px;
        line-height: 1.25;
    }

    .sb-master-shop-shopkeeper svg {
        width: 13px;
        height: 13px;
        flex: 0 0 13px;
        color: #9a6746;
    }

    .sb-master-shop-shopkeeper > span { color: #9b816d; }

    .sb-master-shop-shopkeeper strong {
        color: #6b1d14;
        font-size: 9px;
        font-weight: 900;
    }

    .sb-master-shop-overview--refined .sb-master-shop-description {
        display: -webkit-box !important;
        max-width: 650px !important;
        margin-top: 5px !important;
        overflow: hidden !important;
        color: #765b49 !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1 !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-actions {
        align-items: center !important;
        gap: 7px !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-action {
        min-height: 38px !important;
        border-radius: 10px !important;
        padding: 0 14px !important;
    }

    .sb-master-shop-overview--refined .sb-master-shop-more__button {
        width: 40px !important;
        height: 38px !important;
        border-radius: 10px !important;
    }

    @media (max-width: 980px) {
        .sb-master-shop-overview--refined .sb-master-shop-overview__top {
            align-items: flex-start !important;
        }
        .sb-master-shop-avatar-frame,
        .sb-master-shop-overview--refined .sb-master-shop-avatar {
            width: 62px !important;
            height: 62px !important;
            flex-basis: 62px !important;
        }
    }
</style>

<style>
    /* ============================================================
       LOJAS — DIRETÓRIO DE PASTAS + SIDEBAR EM ALTURA COMPLETA
       ============================================================ */
    .sb-shop-workspace.sb-shop-workspace--full {
        height: calc(100dvh - 148px);
        min-height: 520px;
        align-items: stretch !important;
    }

    .sb-shop-workspace--full .sb-shop-workspace-sidebar {
        display: flex;
        height: 100%;
        min-height: 0;
        flex-direction: column;
    }

    .sb-shop-workspace--full .sb-shop-organizer-panel {
        display: flex;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    .sb-shop-workspace--full .sb-shop-folder-list {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow-y: auto !important;
        padding-right: 3px;
    }

    .sb-shop-folder-menu-floating {
        width: 180px;
        overflow: hidden;
        border: 1px solid #cdbb9f;
        border-radius: 11px;
        background: #fffdf8;
        padding: 5px;
        box-shadow: 0 14px 34px rgba(55,30,18,.20);
    }

    .sb-shop-folder-menu-floating button {
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

    .sb-shop-folder-menu-floating button:hover { background: #f1e8db; }
    .sb-shop-folder-menu-floating button.is-danger { color: #b42318; }

    .sb-shop-directory-hero,
    .sb-shop-folder-hero,
    .sb-shop-directory-section {
        border: 1px solid #d7c5aa;
        border-radius: 17px;
        background: #fffdf8;
        box-shadow: none;
    }

    .sb-shop-directory-hero,
    .sb-shop-folder-hero {
        border-left: 3px solid #8a4b20;
        background: linear-gradient(100deg,#fffdf8 0%,#fbf5ed 58%,#f2e4d5 100%);
    }

    .sb-shop-directory-card {
        overflow: hidden;
        border: 1px solid #ddcfba;
        border-radius: 12px;
        background: #fbf7f1;
        text-align: left;
        transition: border-color .15s ease, background .15s ease, transform .15s ease;
    }

    .sb-shop-directory-card:hover {
        border-color: #b98f69;
        background: #fffdf8;
        transform: translateY(-1px);
        box-shadow: inset 3px 0 0 #8a4b20;
    }

    .sb-shop-directory-card__cover {
        display: grid;
        min-height: 118px;
        place-items: center;
        overflow: hidden;
        border-right: 1px solid #eadcc8;
        background: #f1e6d8;
    }

    .sb-shop-directory-card__cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sb-shop-directory-shop {
        display: grid;
        grid-template-columns: 56px minmax(0,1fr) auto;
        gap: 10px;
        align-items: center;
        border: 1px solid #ddcfba;
        border-radius: 12px;
        background: #fbf7f1;
        padding: 9px 10px;
        text-align: left;
        transition: .15s ease;
    }

    .sb-shop-directory-shop:hover {
        border-color: #b98f69;
        background: #fffdf8;
        box-shadow: inset 3px 0 0 #8a4b20;
    }

    .sb-shop-directory-shop__image {
        display: grid;
        width: 56px;
        height: 56px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #d8c7ab;
        border-radius: 11px;
        background: #f1e6d8;
        color: #9d7659;
    }

    .sb-shop-directory-shop__image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 3px;
    }

    .sb-shop-folder__head {
        overflow: visible !important;
    }

    @media (max-width: 1180px) {
        .sb-shop-workspace.sb-shop-workspace--full { height: auto; min-height: auto; }
        .sb-shop-workspace--full .sb-shop-workspace-sidebar { height: auto; }
        .sb-shop-workspace--full .sb-shop-folder-list { max-height: 360px !important; }
    }

    .sb-shop-workspace--full .sb-shop-folder__head {
        grid-template-columns: minmax(0,1fr) 27px 27px !important;
    }

    .sb-shop-workspace--full .sb-shop-folder__main {
        grid-template-columns: 34px minmax(0,1fr) auto !important;
    }
</style>


{{-- ================================================================
     LOJAS — WORKSPACE
    
     Este partial contém apenas a interface principal de Lojas.
     A infraestrutura Alpine, modais compartilhados e estilos continuam
     no loader loot.blade.php nesta primeira etapa da separação.
================================================================ --}}
<div
    x-show="lootWorkspaceMode === 'shop'"
    x-cloak
    class="sb-shop-workspace sb-shop-workspace--full grid gap-3 xl:grid-cols-[300px_minmax(0,1fr)]"
>
    <aside class="sb-shop-workspace-sidebar min-w-0 rounded-2xl border border-[#d8c7ab]/70 bg-[#f7f2e9]/80 p-3">
                                        <div class="sb-loot-mode-switch grid grid-cols-2 gap-1 rounded-xl border border-[#d8c7ab]/60 bg-[#eee6da]/80 p-1">
                                            <button
                                                type="button"
                                                class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-wider transition"
                                                :class="lootWorkspaceMode === 'loot'
                                                    ? 'bg-[#fffdf8] text-[#53150f] shadow-sm'
                                                    : 'text-[#8c6239] hover:bg-white/50'"
                                                @click="lootWorkspaceMode = 'loot'; lootActiveDropId = null; lootActiveFolderId = null; lootDropMenuId = null; lootFolderMenuId = null; shopFolderMenuId = null"
                                            >
                                                Espólios
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-wider transition"
                                                :class="lootWorkspaceMode === 'shop'
                                                    ? 'bg-[#fffdf8] text-[#53150f] shadow-sm'
                                                    : 'text-[#8c6239] hover:bg-white/50'"
                                                @click="lootWorkspaceMode = 'shop'; lootActiveShopId = null; shopActiveFolderId = null; lootDropMenuId = null; shopFolderMenuId = null"
                                            >
                                                Lojas
                                            </button>
                                        </div>
                                        <div x-show="lootWorkspaceMode === 'shop'" x-cloak class="sb-shop-organizer-panel">
                                            <div class="sb-shop-organizer-head">
                                                <div class="sb-shop-organizer-title">
                                                    
                                                    <h4>Lojas</h4>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="sb-shop-folder-create-compact"
                                                    :disabled="!shopFolderSystemReady"
                                                    @click="openShopFolderEditor()"
                                                    title="Criar pasta"
                                                >
                                                    <svg
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        aria-hidden="true"
                                                    >
                                                        <path d="M3.5 7.5h6l1.5 2h9.5v8.5A2.5 2.5 0 0 1 18 20.5H6A2.5 2.5 0 0 1 3.5 18V7.5Z"></path>
                                                        <path d="M12 12v5M9.5 14.5h5"></path>
                                                    </svg>
                                                    <span>+ Pasta</span>
                                                </button>
                                            </div>

                                            <div
                                                x-show="!shopFolderSystemReady"
                                                x-cloak
                                                class="mt-2 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5 text-[9px] font-bold leading-relaxed text-amber-900"
                                            >
                                                <span
                                                    x-text="
                                                        shopFolderSystemMessage
                                                        || 'O sistema de pastas ainda não está pronto.'
                                                    "
                                                ></span>
                                            </div>

                                            <label class="sb-shop-organizer-search">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <circle cx="11" cy="11" r="6.5"></circle>
                                                    <path d="m16 16 4 4"></path>
                                                </svg>

                                                <input
                                                    type="search"
                                                    x-model.debounce.180ms="shopSearch"
                                                    placeholder="Buscar loja..."
                                                >
                                            </label>

                                            <div class="sb-shop-folder-list">
                                                <template
                                                    x-for="folder in shopFolders"
                                                    :key="'shop-folder-' + folder.id"
                                                >
                                                    <section
                                                        class="sb-shop-folder"
                                                        :class="{
                                                            'is-open':
                                                                shopFolderIsOpen(folder.id),
                                                            'is-drop-target':
                                                                parseInt(shopFolderDropTarget) === parseInt(folder.id)
                                                        }"
                                                        @dragover.prevent="
                                                            shopFolderDropTarget = folder.id
                                                        "
                                                        @dragleave="
                                                            parseInt(shopFolderDropTarget) === parseInt(folder.id)
                                                                ? (shopFolderDropTarget = null)
                                                                : null
                                                        "
                                                        @drop="
                                                            dropShopIntoFolder(folder.id, $event)
                                                        "
                                                    >
                                                        <div class="sb-shop-folder__head">
                                                            <button
                                                                type="button"
                                                                class="sb-shop-folder__main"
                                                                @click="selectShopFolder(folder)"
                                                                :title="folder.description || folder.name"
                                                            >
                                                                <span class="sb-shop-folder__cover">
                                                                    <template x-if="folder.image_url">
                                                                        <img
                                                                            :src="folder.image_url"
                                                                            :alt="folder.name"
                                                                        >
                                                                    </template>

                                                                    <template x-if="!folder.image_url">
                                                                        <svg
                                                                            viewBox="0 0 24 24"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            stroke-width="1.7"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            aria-hidden="true"
                                                                        >
                                                                            <path d="M3.5 7.5h6l1.5 2h9.5v8.5A2.5 2.5 0 0 1 18 20.5H6A2.5 2.5 0 0 1 3.5 18V7.5Z"></path>
                                                                            <path d="M3.5 7.5V6A2.5 2.5 0 0 1 6 3.5h3.5l2 2H18A2.5 2.5 0 0 1 20.5 8"></path>
                                                                        </svg>
                                                                    </template>
                                                                </span>

                                                                <span class="sb-shop-folder__copy">
                                                                    <strong x-text="folder.name"></strong>
                                                                </span>

                                                                <span
                                                                    class="sb-shop-folder__count"
                                                                    x-text="shopFolderShopCount(folder.id)"
                                                                ></span>

                                                            </button>

                                                            <button
                                                                type="button"
                                                                class="sb-shop-folder__menu"
                                                                @click.stop="toggleShopFolder(folder.id)"
                                                                :title="shopFolderIsOpen(folder.id) ? 'Recolher pasta' : 'Expandir pasta'"
                                                            >
                                                                <span x-text="shopFolderIsOpen(folder.id) ? '⌃' : '⌄'"></span>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                class="sb-shop-folder__menu"
                                                                @click.stop="toggleShopFolderMenu(folder, $event)"
                                                                title="Opções da pasta"
                                                            >···</button>
                                                        </div>

                                                        <div
                                                            x-show="shopFolderIsOpen(folder.id)"
                                                            x-cloak
                                                            class="sb-shop-folder__body"
                                                        >
                                                            <template
                                                                x-for="shop in shopFolderShops(folder.id)"
                                                                :key="'folder-shop-' + shop.id"
                                                            >
                                                                <article
                                                                    x-data="{ shopMenuX: 0, shopMenuY: 0 }"
                                                                    class="sb-shop-side-card"
                                                                    :class="{
                                                                        'is-active':
                                                                            parseInt(lootActiveShopId) === parseInt(shop.id),
                                                                        'is-dragging':
                                                                            parseInt(shopFolderDraggingShopId) === parseInt(shop.id)
                                                                    }"
                                                                    draggable="true"
                                                                    @dragstart="startShopDrag(shop, $event)"
                                                                    @dragend="endShopDrag()"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="sb-shop-side-card__main"
                                                                        @click="selectShop(shop)"
                                                                    >
                                                                        <span
                                                                            class="sb-shop-side-card__status"
                                                                            :class="{
                                                                                'is-open':
                                                                                    shop.status === 'released'
                                                                            }"
                                                                        ></span>

                                                                        <span class="sb-shop-side-card__copy">
                                                                            <strong x-text="shop.name"></strong>
                                                                        </span>
                                                                    </button>

                                                                    <button
                                                                        type="button"
                                                                        class="sb-shop-side-card__menu"
                                                                        @click.stop="
                                                                            if (parseInt(lootDropMenuId) === parseInt(shop.id)) {
                                                                                lootDropMenuId = null;
                                                                            } else {
                                                                                const rect = $el.getBoundingClientRect();
                                                                                const menuWidth = 174;
                                                                                const menuHeight = 90;
                                                                                shopMenuX = Math.max(10, Math.min(window.innerWidth - menuWidth - 10, rect.right - menuWidth));
                                                                                shopMenuY = rect.bottom + 6;
                                                                                if (shopMenuY + menuHeight > window.innerHeight - 10) {
                                                                                    shopMenuY = Math.max(10, rect.top - menuHeight - 6);
                                                                                }
                                                                                lootDropMenuId = shop.id;
                                                                            }
                                                                        "
                                                                        title="Opções da loja"
                                                                    >···</button>

                                                                    <template x-teleport="body">

                                                                        <div

                                                                            x-show="parseInt(lootDropMenuId) === parseInt(shop.id)"

                                                                            x-cloak

                                                                            x-transition.opacity.scale.95

                                                                            @click.outside="lootDropMenuId = null"

                                                                            class="sb-shop-side-menu sb-shop-side-menu--portal"

                                                                            :style="`left:${shopMenuX}px;top:${shopMenuY}px`"

                                                                        >
<button
                                                                            x-show="shop.status !== 'released'"
                                                                            x-cloak
                                                                            type="button"
                                                                            @click="openShopSession(shop); lootDropMenuId = null"
                                                                        >Abrir loja</button>

                                                                        <button
                                                                            x-show="shop.status === 'released'"
                                                                            x-cloak
                                                                            type="button"
                                                                            @click="closeLootSession(shop); lootDropMenuId = null"
                                                                        >Fechar loja</button>

                                                                        <button
                                                                            type="button"
                                                                            class="is-danger"
                                                                            @click="deleteLootDrop(shop)"
                                                                        >Excluir loja</button>
                                                                        </div>

                                                                    </template>
                                                                </article>
                                                            </template>

                                                            <div
                                                                x-show="
                                                                    shopFolderDraggingShopId
                                                                    && shopFolderShops(folder.id).length === 0
                                                                "
                                                                x-cloak
                                                                class="sb-shop-folder__empty"
                                                            >
                                                                <strong>Solte aqui</strong>
                                                                <span>Mover loja para esta pasta.</span>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </template>

                                                <section
                                                    x-show="
                                                        shopUnfiledShops().length > 0
                                                        || shopFolderDraggingShopId
                                                    "
                                                    x-cloak
                                                    class="sb-shop-unfiled"
                                                    :class="{
                                                        'is-drop-target':
                                                            shopFolderDropTarget === 'unfiled'
                                                    }"
                                                    @dragover.prevent="
                                                        shopFolderDropTarget = 'unfiled'
                                                    "
                                                    @dragleave="
                                                        shopFolderDropTarget === 'unfiled'
                                                            ? (shopFolderDropTarget = null)
                                                            : null
                                                    "
                                                    @drop="
                                                        dropShopIntoFolder(null, $event)
                                                    "
                                                >
                                                    <header class="sb-shop-unfiled__head">
                                                        <strong>Sem pasta</strong>

                                                        <span
                                                            class="sb-shop-folder__count"
                                                            x-text="shopUnfiledShops().length"
                                                        ></span>
                                                    </header>

                                                    <div class="sb-shop-unfiled__list">
                                                        <template
                                                            x-for="shop in shopUnfiledShops()"
                                                            :key="'unfiled-shop-' + shop.id"
                                                        >
                                                            <article
                                                                x-data="{ shopMenuX: 0, shopMenuY: 0 }"
                                                                    class="sb-shop-side-card"
                                                                :class="{
                                                                    'is-active':
                                                                        parseInt(lootActiveShopId) === parseInt(shop.id),
                                                                    'is-dragging':
                                                                        parseInt(shopFolderDraggingShopId) === parseInt(shop.id)
                                                                }"
                                                                draggable="true"
                                                                @dragstart="startShopDrag(shop, $event)"
                                                                @dragend="endShopDrag()"
                                                            >
                                                                <button
                                                                    type="button"
                                                                    class="sb-shop-side-card__main"
                                                                    @click="selectShop(shop)"
                                                                >
                                                                    <span
                                                                        class="sb-shop-side-card__status"
                                                                        :class="{
                                                                            'is-open':
                                                                                shop.status === 'released'
                                                                        }"
                                                                    ></span>

                                                                    <span class="sb-shop-side-card__copy">
                                                                        <strong x-text="shop.name"></strong>
                                                                        </span>
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="sb-shop-side-card__menu"
                                                                    @click.stop="
                                                                            if (parseInt(lootDropMenuId) === parseInt(shop.id)) {
                                                                                lootDropMenuId = null;
                                                                            } else {
                                                                                const rect = $el.getBoundingClientRect();
                                                                                const menuWidth = 174;
                                                                                const menuHeight = 90;
                                                                                shopMenuX = Math.max(10, Math.min(window.innerWidth - menuWidth - 10, rect.right - menuWidth));
                                                                                shopMenuY = rect.bottom + 6;
                                                                                if (shopMenuY + menuHeight > window.innerHeight - 10) {
                                                                                    shopMenuY = Math.max(10, rect.top - menuHeight - 6);
                                                                                }
                                                                                lootDropMenuId = shop.id;
                                                                            }
                                                                        "
                                                                >···</button>

                                                                <template x-teleport="body">

                                                                    <div

                                                                        x-show="parseInt(lootDropMenuId) === parseInt(shop.id)"

                                                                        x-cloak

                                                                        x-transition.opacity.scale.95

                                                                        @click.outside="lootDropMenuId = null"

                                                                        class="sb-shop-side-menu sb-shop-side-menu--portal"

                                                                        :style="`left:${shopMenuX}px;top:${shopMenuY}px`"

                                                                    >
<button
                                                                        x-show="shop.status !== 'released'"
                                                                        x-cloak
                                                                        type="button"
                                                                        @click="openShopSession(shop); lootDropMenuId = null"
                                                                    >Abrir loja</button>

                                                                    <button
                                                                        x-show="shop.status === 'released'"
                                                                        x-cloak
                                                                        type="button"
                                                                        @click="closeLootSession(shop); lootDropMenuId = null"
                                                                    >Fechar loja</button>

                                                                    <button
                                                                        type="button"
                                                                        class="is-danger"
                                                                        @click="deleteLootDrop(shop)"
                                                                    >Excluir loja</button>
                                                                    </div>

                                                                </template>
                                                            </article>
                                                        </template>

                                                        <div
                                                            x-show="
                                                                shopFolderDraggingShopId
                                                                && shopUnfiledShops().length === 0
                                                            "
                                                            x-cloak
                                                            class="sb-shop-folder__empty"
                                                        >
                                                            <strong>Solte aqui</strong>
                                                            <span>Remover a loja da pasta atual.</span>
                                                        </div>
                                                    </div>
                                                </section>

                                                <div
                                                    x-show="lootVisibleShops().length === 0"
                                                    x-cloak
                                                    class="sb-shop-organizer-empty"
                                                >
                                                    <strong>Nenhuma loja encontrada</strong>
                                                    <span>Crie uma nova loja ou limpe a busca.</span>
                                                </div>
                                            </div>
                                        </div>

    </aside>

    <main class="min-w-0">
                                        <div x-show="lootWorkspaceMode === 'shop'" x-cloak>
                                            <template x-if="lootActiveShop">
                                                <div class="space-y-3">
                                                    <section class="sb-master-shop-overview sb-master-shop-overview--refined">
                                                        <div class="sb-master-shop-overview__inner">
                                                            <div class="sb-master-shop-overview__top">
                                                                <div class="sb-master-shop-identity">
                                                                    <div class="sb-master-shop-avatar-frame">
                                                                        <div class="sb-master-shop-avatar">
                                                                            <template x-if="lootActiveShop?.presentation_image_url">
                                                                                <img
                                                                                    :src="lootActiveShop.presentation_image_url"
                                                                                    :alt="lootActiveShop?.shopkeeper_name || lootActiveShop?.name"
                                                                                >
                                                                            </template>
                                                                            <template x-if="!lootActiveShop?.presentation_image_url">
                                                                                <span>◇</span>
                                                                            </template>
                                                                        </div>

                                                                        <span
                                                                            class="sb-master-shop-avatar-state"
                                                                            :class="{ 'is-open': lootActiveShop?.status === 'released' }"
                                                                            aria-hidden="true"
                                                                        ></span>
                                                                    </div>

                                                                    <div class="sb-master-shop-copy">
                                                                        <p class="sb-master-shop-kicker">Loja da campanha</p>

                                                                        <div class="sb-master-shop-title-row">
                                                                            <h4
                                                                                class="sb-master-shop-title"
                                                                                x-text="lootActiveShop?.name"
                                                                            ></h4>

                                                                            <span
                                                                                class="sb-master-shop-status"
                                                                                :class="{ 'is-open': lootActiveShop?.status === 'released' }"
                                                                                x-text="lootActiveShop?.status === 'released' ? 'Loja aberta' : 'Loja fechada'"
                                                                            ></span>
                                                                        </div>

                                                                        <div class="sb-master-shop-shopkeeper">
                                                                            <svg
                                                                                viewBox="0 0 24 24"
                                                                                fill="none"
                                                                                stroke="currentColor"
                                                                                stroke-width="1.8"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                aria-hidden="true"
                                                                            >
                                                                                <circle cx="12" cy="8" r="3.2"></circle>
                                                                                <path d="M5.5 19c.7-3.6 3-5.5 6.5-5.5s5.8 1.9 6.5 5.5"></path>
                                                                            </svg>
                                                                            <span>Atendido por</span>
                                                                            <strong x-text="lootActiveShop?.shopkeeper_name || 'Sem atendente definido'"></strong>
                                                                        </div>

                                                                        <p
                                                                            x-show="lootActiveShop?.description"
                                                                            class="sb-master-shop-description"
                                                                            x-text="lootActiveShop?.description"
                                                                        ></p>
                                                                    </div>
                                                                </div>

                                                                <div class="sb-master-shop-actions">
                                                                    <button
                                                                        x-show="lootActiveShop?.status !== 'released'"
                                                                        x-cloak
                                                                        type="button"
                                                                        class="sb-master-shop-action is-success"
                                                                        @click="openShopSession(lootActiveShop)"
                                                                    >
                                                                        Abrir Loja
                                                                    </button>

                                                                    <button
                                                                        x-show="lootActiveShop?.status === 'released'"
                                                                        x-cloak
                                                                        type="button"
                                                                        class="sb-master-shop-action"
                                                                        @click="closeLootSession(lootActiveShop)"
                                                                    >
                                                                        Fechar Loja
                                                                    </button>

                                                                    <div
                                                                        class="sb-master-shop-more"
                                                                        @click.outside="shopHeaderMenuOpen = false"
                                                                    >
                                                                        <button
                                                                            type="button"
                                                                            class="sb-master-shop-more__button"
                                                                            :class="{ 'is-active': shopHeaderMenuOpen }"
                                                                            @click="
                                                                                shopHeaderMenuOpen =
                                                                                    !shopHeaderMenuOpen
                                                                            "
                                                                            aria-label="Mais ações da loja"
                                                                            title="Mais ações"
                                                                        >
                                                                            ···
                                                                        </button>

                                                                        <div
                                                                            x-show="shopHeaderMenuOpen"
                                                                            x-cloak
                                                                            x-transition.opacity.scale.95
                                                                            class="sb-master-shop-more__menu"
                                                                        >
                                                                            <button
                                                                                type="button"
                                                                                @click="
                                                                                    shopHeaderMenuOpen = false;
                                                                                    openShopPresentationEditor(lootActiveShop);
                                                                                "
                                                                            >
                                                                                <span class="sb-master-shop-more__icon">✎</span>

                                                                                <span>
                                                                                    <strong>Editar Loja</strong>
                                                                                    <small>Aparência, atendente e dados.</small>
                                                                                </span>
                                                                            </button>

                                                                            <button
                                                                                type="button"
                                                                                class="is-danger"
                                                                                @click="
                                                                                    shopHeaderMenuOpen = false;
                                                                                    deleteLootDrop(lootActiveShop);
                                                                                "
                                                                            >
                                                                                <span class="sb-master-shop-more__icon">×</span>

                                                                                <span>
                                                                                    <strong>Excluir Loja</strong>
                                                                                    <small>Remove a loja e o histórico desta sessão.</small>
                                                                                </span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        <template x-teleport="body">
                                                        <div
                                                            x-show="shopPresentationOpen"
                                                            x-cloak
                                                            role="dialog"
                                                            aria-modal="true"
                                                            aria-label="Personalizar loja"
                                                            class="sb-shop-create-overlay"
                                                            @click.self="closeShopPresentationEditor()"
                                                            @keydown.escape.window="shopEditShopkeeperOpen ? (shopEditShopkeeperOpen = false) : closeShopPresentationEditor()"
                                                        >
                                                            <div class="sb-shop-create-shell">
                                                                <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#e1d5c3] bg-white px-5 py-2.5 sm:px-6">
                                                                    <h4
                                                                        class="min-w-0 truncate font-serif text-[21px] font-black text-[#53150f]"
                                                                        x-text="shopEditName || 'Editar loja'"
                                                                    ></h4>

                                                                    <button
                                                                        type="button"
                                                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#ddcfbb] bg-[#fffdfa] text-[18px] leading-none text-[#6b1d14] transition hover:bg-[#f5eee4]"
                                                                        @click="closeShopPresentationEditor()"
                                                                        aria-label="Fechar"
                                                                    >×</button>
                                                                </header>

                                                                <div class="sb-shop-create-body">
                                                                    <div class="sb-shop-create-controls">
                                                                        <div class="space-y-4">
                                                                            <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                                                <div class="mb-4">
                                                                                    <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Identidade</p>
                                                                                    <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">Os valores salvos da loja aparecem aqui para você ajustar.</p>
                                                                                </div>

                                                                                <div class="grid gap-4 md:grid-cols-2">
                                                                                    <label>
                                                                                        <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Nome da loja</span>
                                                                                        <input
                                                                                            type="text"
                                                                                            maxlength="150"
                                                                                            x-model="shopEditName"
                                                                                            class="h-12 w-full rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[14px] font-semibold text-[#53150f] outline-none transition focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                                        >
                                                                                    </label>

                                                                                    <div class="sb-shopkeeper-field" @click.outside="shopEditShopkeeperOpen = false">
                                                                                        <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Atendente</span>

                                                                                        <button
                                                                                            type="button"
                                                                                            class="sb-shopkeeper-trigger"
                                                                                            :class="shopEditShopkeeperOpen ? 'is-open' : ''"
                                                                                            :aria-expanded="shopEditShopkeeperOpen ? 'true' : 'false'"
                                                                                            @click="shopEditShopkeeperOpen = !shopEditShopkeeperOpen; if (shopEditShopkeeperOpen) shopEditShopkeeperSearch = ''"
                                                                                        >
                                                                                            <span class="sb-shopkeeper-avatar">
                                                                                                <template x-if="shopEditSelectedShopkeeper()?.image_url">
                                                                                                    <img :src="shopEditSelectedShopkeeper().image_url" :alt="shopEditSelectedShopkeeper().name">
                                                                                                </template>
                                                                                                <template x-if="!shopEditSelectedShopkeeper()?.image_url">
                                                                                                    <span x-text="shopEditSelectedShopkeeper() ? 'NPC' : '—'"></span>
                                                                                                </template>
                                                                                            </span>

                                                                                            <span class="sb-shopkeeper-copy">
                                                                                                <strong class="sb-shopkeeper-name" x-text="shopEditSelectedShopkeeper()?.name || 'Sem NPC vinculado'"></strong>
                                                                                                <small class="sb-shopkeeper-hint" x-text="shopEditSelectedShopkeeper() ? 'Retrato do Cofre como padrão' : 'Opcional · use um NPC ou um atendente genérico'"></small>
                                                                                            </span>
                                                                                            <span class="sb-shopkeeper-chevron">⌄</span>
                                                                                        </button>

                                                                                        <div
                                                                                            x-show="shopEditShopkeeperOpen"
                                                                                            x-cloak
                                                                                            x-transition.opacity.duration.120ms
                                                                                            class="sb-shopkeeper-menu"
                                                                                            @click.stop
                                                                                        >
                                                                                            <div class="sb-shopkeeper-search-wrap">
                                                                                                <input type="search" x-model.debounce.120ms="shopEditShopkeeperSearch" placeholder="Buscar NPC..." class="sb-shopkeeper-search">
                                                                                            </div>

                                                                                            <div class="sb-shopkeeper-options">
                                                                                                <button
                                                                                                    type="button"
                                                                                                    class="sb-shopkeeper-option"
                                                                                                    :class="!shopEditShopkeeperNpcId ? 'is-selected' : ''"
                                                                                                    @click="shopEditShopkeeperNpcId = ''; shopEditShopkeeperOpen = false"
                                                                                                >
                                                                                                    <span class="sb-shopkeeper-avatar">—</span>
                                                                                                    <span class="sb-shopkeeper-copy">
                                                                                                        <strong class="sb-shopkeeper-name">Sem NPC vinculado</strong>
                                                                                                        <small class="sb-shopkeeper-hint">Use um nome livre e uma imagem enviada</small>
                                                                                                    </span>
                                                                                                    <span x-show="!shopEditShopkeeperNpcId" class="sb-shopkeeper-check">✓</span>
                                                                                                </button>

                                                                                                <template x-for="npc in shopEditFilteredShopkeepers()" :key="'edit-shopkeeper-menu-' + npc.id">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="sb-shopkeeper-option"
                                                                                                        :class="parseInt(shopEditShopkeeperNpcId) === parseInt(npc.id) ? 'is-selected' : ''"
                                                                                                        @click="shopEditShopkeeperNpcId = String(npc.id); shopEditShopkeeperOpen = false"
                                                                                                    >
                                                                                                        <span class="sb-shopkeeper-avatar">
                                                                                                            <template x-if="npc.image_url"><img :src="npc.image_url" :alt="npc.name"></template>
                                                                                                            <template x-if="!npc.image_url"><span>NPC</span></template>
                                                                                                        </span>
                                                                                                        <span class="sb-shopkeeper-copy">
                                                                                                            <strong class="sb-shopkeeper-name" x-text="npc.name"></strong>
                                                                                                            <small class="sb-shopkeeper-hint" x-text="npc.image_url ? 'Com retrato disponível' : 'Sem retrato cadastrado'"></small>
                                                                                                        </span>
                                                                                                        <span x-show="parseInt(shopEditShopkeeperNpcId) === parseInt(npc.id)" class="sb-shopkeeper-check">✓</span>
                                                                                                    </button>
                                                                                                </template>

                                                                                                <div x-show="shopEditFilteredShopkeepers().length === 0" x-cloak class="sb-shopkeeper-empty">Nenhum NPC encontrado.</div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <label x-show="!shopEditShopkeeperNpcId" x-cloak class="mt-4 block">
                                                                                    <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Nome do atendente</span>
                                                                                    <input
                                                                                        type="text"
                                                                                        maxlength="150"
                                                                                        x-model="shopEditAttendantName"
                                                                                        placeholder="Ex.: Balric, o balconista"
                                                                                        class="h-12 w-full rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[14px] font-semibold text-[#53150f] outline-none transition placeholder:text-[#a3978b] focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                                    >
                                                                                    <span class="mt-1.5 block text-[11px] leading-5 text-[#9a806d]">Este nome aparece para o Mestre e para os Players quando não houver NPC vinculado.</span>
                                                                                </label>

                                                                                <label class="mt-4 block">
                                                                                    <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Descrição</span>
                                                                                    <textarea
                                                                                        rows="3"
                                                                                        maxlength="10000"
                                                                                        x-model="shopEditDescription"
                                                                                        class="w-full resize-y rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 py-3 text-[14px] font-semibold leading-6 text-[#53150f] outline-none transition focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                                    ></textarea>
                                                                                </label>
                                                                            </section>

                                                                            <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                                                <div class="mb-4">
                                                                                    <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Imagens</p>
                                                                                    <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">Troque o retrato ou o cenário. A prévia à direita continua visível.</p>
                                                                                </div>
                                                                                <div class="grid gap-3 sm:grid-cols-2">
                                                                                    <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                                        <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Retrato</p>
                                                                                        <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">Sem imagem personalizada, usamos o retrato do NPC.</p>
                                                                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                                                                            <label class="inline-flex cursor-pointer items-center rounded-lg border border-[#c59263] bg-white px-3.5 py-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#f7eadb]">
                                                                                                Escolher imagem
                                                                                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleShopPresentationImage($event)">
                                                                                            </label>
                                                                                            <button
                                                                                                x-show="shopEditImagePreview || (!shopEditRemoveImage && lootActiveShop?.has_custom_shop_image)"
                                                                                                x-cloak
                                                                                                type="button"
                                                                                                class="rounded-lg px-3 py-2.5 text-[10px] font-black uppercase text-red-700 hover:bg-red-50"
                                                                                                @click="removeShopCustomImage()"
                                                                                            >Remover</button>
                                                                                        </div>
                                                                                        <p class="mt-2 text-[9px] font-semibold text-[#9a806d]" x-text="'JPG, PNG ou WEBP · limite atual: ' + formatShopFileSize(shopPortraitUploadLimitBytes)"></p>
                                                                                        <p
                                                                                            x-show="shopEditImageFeedback"
                                                                                            x-cloak
                                                                                            class="sb-shop-upload-feedback"
                                                                                            :style="shopUploadFeedbackStyle(shopEditImageFeedbackType)"
                                                                                            x-text="shopEditImageFeedback"
                                                                                        ></p>
                                                                                    </div>

                                                                                    <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                                        <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cenário</p>
                                                                                        <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">Interior, rua, forja ou qualquer imagem de fundo.</p>
                                                                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                                                                            <label class="inline-flex cursor-pointer items-center rounded-lg border border-[#c59263] bg-white px-3.5 py-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#f7eadb]">
                                                                                                Escolher fundo
                                                                                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleShopPresentationBackground($event)">
                                                                                            </label>
                                                                                            <button
                                                                                                x-show="shopEditBackgroundPreview || (!shopEditRemoveBackground && lootActiveShop?.has_custom_shop_background)"
                                                                                                x-cloak
                                                                                                type="button"
                                                                                                class="rounded-lg px-3 py-2.5 text-[10px] font-black uppercase text-red-700 hover:bg-red-50"
                                                                                                @click="removeShopBackground()"
                                                                                            >Remover</button>
                                                                                        </div>
                                                                                        <p class="mt-2 text-[9px] font-semibold text-[#9a806d]" x-text="'JPG, PNG ou WEBP · limite atual: ' + formatShopFileSize(shopBackgroundUploadLimitBytes)"></p>
                                                                                        <p
                                                                                            x-show="shopEditBackgroundFeedback"
                                                                                            x-cloak
                                                                                            class="sb-shop-upload-feedback"
                                                                                            :style="shopUploadFeedbackStyle(shopEditBackgroundFeedbackType)"
                                                                                            x-text="shopEditBackgroundFeedback"
                                                                                        ></p>
                                                                                    </div>
                                                                                </div>
                                                                            </section>

                                                                            <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                                                <div class="mb-4">
                                                                                    <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Aparência</p>
                                                                                    <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">As opções abaixo carregam os valores atualmente salvos na loja.</p>
                                                                                </div>

                                                                                <div class="mb-3 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                                                        <div>
                                                                                            <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Balcão da loja</p>
                                                                                            <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">A alteração aparece imediatamente na prévia e também será vista pelo Player.</p>
                                                                                        </div>
                                                                                        <div class="grid shrink-0 grid-cols-2 gap-1 rounded-xl border border-[#ded0bd] bg-white p-1">
                                                                                            <button type="button" class="rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditHasCounter ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-[#f5eee4]'" @click="shopEditHasCounter = true">Com balcão</button>
                                                                                            <button type="button" class="rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="!shopEditHasCounter ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-[#f5eee4]'" @click="shopEditHasCounter = false">Sem balcão</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="grid gap-3 sm:grid-cols-2">
                                                                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] px-4 py-3.5">
                                                                                        <div><span class="block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cor do fundo</span><span class="mt-1 block text-[11px] font-semibold text-[#9a806d]" x-text="shopEditBackgroundColor"></span></div>
                                                                                        <input type="color" x-model="shopEditBackgroundColor" class="h-10 w-14 cursor-pointer rounded-lg border border-[#d8c7ab] bg-white p-1">
                                                                                    </label>
                                                                                    <label x-show="shopEditHasCounter" x-cloak class="flex items-center justify-between gap-4 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] px-4 py-3.5">
                                                                                        <div><span class="block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cor do balcão</span><span class="mt-1 block text-[11px] font-semibold text-[#9a806d]" x-text="shopEditCounterColor"></span></div>
                                                                                        <input type="color" x-model="shopEditCounterColor" class="h-10 w-14 cursor-pointer rounded-lg border border-[#d8c7ab] bg-white p-1">
                                                                                    </label>
                                                                                </div>

                                                                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                                                    <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                                        <div class="flex items-center justify-between gap-3">
                                                                                            <div><p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Retrato</p><p class="mt-1 text-[11px] text-[#9a806d]">Tamanho e área prioritária.</p></div>
                                                                                            <span class="rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-black text-[#6b1d14]" x-text="shopEditImageZoom + '%' "></span>
                                                                                        </div>
                                                                                        <input type="range" min="70" max="180" step="5" x-model.number="shopEditImageZoom" class="mt-3 w-full accent-[#6b1d14]">
                                                                                        <div class="mt-3">
                                                                                            <span class="mb-2 block text-[10px] font-black uppercase tracking-wider text-[#8c6239]">Enquadramento do atendente</span>
                                                                                            <div class="grid grid-cols-3 gap-1 rounded-lg border border-[#ded0bd] bg-white p-1">
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditImagePosition === 'top' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditImagePosition = 'top'">Cima</button>
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditImagePosition === 'center' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditImagePosition = 'center'">Meio</button>
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditImagePosition === 'bottom' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditImagePosition = 'bottom'">Baixo</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                                        <div class="flex items-center justify-between gap-3">
                                                                                            <div><p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cenário</p><p class="mt-1 text-[11px] text-[#9a806d]">Zoom e área que deve permanecer visível.</p></div>
                                                                                            <span class="rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-black text-[#6b1d14]" x-text="shopEditBackgroundZoom + '%' "></span>
                                                                                        </div>
                                                                                        <input type="range" min="100" max="180" step="5" x-model.number="shopEditBackgroundZoom" class="mt-3 w-full accent-[#6b1d14]">
                                                                                        <div class="mt-3">
                                                                                            <span class="mb-2 block text-[10px] font-black uppercase tracking-wider text-[#8c6239]">Enquadramento do cenário</span>
                                                                                            <div class="grid grid-cols-3 gap-1 rounded-lg border border-[#ded0bd] bg-white p-1">
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditBackgroundPosition === 'top' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditBackgroundPosition = 'top'">Cima</button>
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditBackgroundPosition === 'center' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditBackgroundPosition = 'center'">Meio</button>
                                                                                                <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopEditBackgroundPosition === 'bottom' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopEditBackgroundPosition = 'bottom'">Baixo</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </section>
                                                                        </div>
                                                                    </div>

                                                                    <aside class="sb-shop-create-preview">
                                                                        <div class="sb-shop-create-preview-content">
                                                                            <div class="shrink-0">
                                                                                <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#8c6239]">Prévia da vitrine</p>
                                                                            </div>
                                                                            <div class="mt-2.5 min-h-0 flex-1">
                                                                                <div class="sb-shop-player-preview-shell border border-[#cdbb9f]/90 shadow-[0_18px_44px_rgba(52,29,21,.18)]">
                                                                                    <div class="sb-shop-create-stage" :style="{ backgroundColor: shopEditBackgroundColor }">
                                                                                        <template x-if="shopEditBackgroundPreview">
                                                                                            <img
                                                                                                :src="shopEditBackgroundPreview"
                                                                                                alt="Fundo da loja"
                                                                                                class="sb-shop-scene-image"
                                                                                                :style="{
                                                                                                    transform: 'scale(' + (shopEditBackgroundZoom / 100) + ')',
                                                                                                    transformOrigin: shopEditBackgroundPosition === 'top' ? 'center top' : (shopEditBackgroundPosition === 'bottom' ? 'center bottom' : 'center center'),
                                                                                                    objectPosition: shopEditBackgroundPosition === 'top' ? 'center top' : (shopEditBackgroundPosition === 'bottom' ? 'center bottom' : 'center center')
                                                                                                }"
                                                                                                x-on:error="shopEditBackgroundFeedback = 'Não foi possível exibir a prévia do cenário salvo.'; shopEditBackgroundFeedbackType = 'error'"
                                                                                            >
                                                                                        </template>

                                                                                        <div
                                                                                            x-show="!shopEditBackgroundPreview"
                                                                                            class="absolute inset-0"
                                                                                            style="background: linear-gradient(180deg, #6f4a33 0%, #523221 100%);"
                                                                                        ></div>

                                                                                        <div class="sb-shop-preview-shade"></div>

                                                                                        <div class="sb-shop-preview-title">
                                                                                            <div class="sb-shop-preview-status">
                                                                                                <span class="sb-shop-preview-open-dot"></span>
                                                                                                <span>Loja aberta · prévia</span>
                                                                                            </div>
                                                                                            <strong x-text="shopEditName || 'Sua Loja'"></strong>
                                                                                            <small x-text="'Atendido por ' + shopEditAttendantDisplayName()"></small>
                                                                                        </div>

                                                                                        <div class="sb-shop-portrait-layer" :class="{ 'is-counterless': !shopEditHasCounter }">
                                                                                            <template x-if="shopEditPortraitPreviewUrl()">
                                                                                                <img
                                                                                                    :src="shopEditPortraitPreviewUrl()"
                                                                                                    alt="Atendente"
                                                                                                    class="sb-shop-portrait-image"
                                                                                                    :style="{
                                                                                                        transform: 'scale(' + (shopEditImageZoom / 100) + ')',
                                                                                                        transformOrigin: shopEditImagePosition === 'top' ? 'center top' : (shopEditImagePosition === 'bottom' ? 'center bottom' : 'center center'),
                                                                                                        objectPosition: shopEditImagePosition === 'top' ? 'center top' : (shopEditImagePosition === 'bottom' ? 'center bottom' : 'center center')
                                                                                                    }"
                                                                                                    x-on:error="shopEditImageFeedback = 'Não foi possível exibir o retrato salvo do atendente.'; shopEditImageFeedbackType = 'error'"
                                                                                                >
                                                                                            </template>
                                                                                        </div>

                                                                                        <div
                                                                                            x-show="shopEditHasCounter"
                                                                                            x-cloak
                                                                                            class="sb-shop-counter"
                                                                                            :style="{ '--counter-color': shopEditCounterColor }"
                                                                                        >
                                                                                            <div class="sb-shop-counter__foot-shadow"></div>
                                                                                            <div class="sb-shop-counter__top"></div>
                                                                                            <div class="sb-shop-counter__body">
                                                                                                <div class="sb-shop-counter__frame"></div>
                                                                                                <div class="sb-shop-counter__panel sb-shop-counter__panel--left"></div>
                                                                                                <div class="sb-shop-counter__panel sb-shop-counter__panel--right"></div>
                                                                                                <div class="sb-shop-counter__stile"></div>
                                                                                                <div class="sb-shop-counter__post sb-shop-counter__post--left"></div>
                                                                                                <div class="sb-shop-counter__post sb-shop-counter__post--right"></div>
                                                                                            </div>
                                                                                            <div class="sb-shop-counter__base"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </aside>
                                                                </div>

                                                                <footer class="flex shrink-0 items-center justify-end gap-2 border-t border-[#e1d5c3] bg-[#fbf8f2] px-5 py-2 sm:px-6">
                                                                    <button type="button" class="h-9 rounded-lg border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[9px] font-black uppercase tracking-wider text-[#76553f] hover:bg-[#f5eee4]" @click="closeShopPresentationEditor()">Cancelar</button>
                                                                    <button type="button" class="h-9 rounded-lg bg-[#6b1d14] px-5 text-[9px] font-black uppercase tracking-wider text-white disabled:opacity-50" :disabled="shopPresentationSaving" @click="saveShopPresentation()">
                                                                        <span x-text="shopPresentationSaving ? 'Salvando...' : 'Salvar alterações'"></span>
                                                                    </button>
                                                                </footer>
                                                            </div>
                                                        </div>
                                                        </template>
                                                            <div class="sb-master-shop-metrics">
                                                                <div class="sb-master-shop-metric">
                                                                    <span>Produtos</span>
                                                                    <strong x-text="lootDropItemCount(lootActiveShop)"></strong>
                                                                </div>

                                                                <div class="sb-master-shop-metric">
                                                                    <span>Vendas</span>
                                                                    <strong x-text="shopSalesCount(lootActiveShop)"></strong>
                                                                </div>

                                                                <div class="sb-master-shop-metric">
                                                                    <span>Atendente</span>
                                                                    <strong
                                                                        x-text="lootActiveShop?.shopkeeper_name || 'Sem atendente'"
                                                                    ></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>

                                                    <nav class="sb-master-shop-tabs">
                                                        <button
                                                            type="button"
                                                            class="sb-master-shop-tab"
                                                            :class="{ 'is-active': shopContentTab === 'stock' }"
                                                            @click="shopContentTab = 'stock'"
                                                        >
                                                            Estoque
                                                            <span class="ml-1 opacity-60" x-text="'· ' + lootDropItemCount(lootActiveShop)"></span>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="sb-master-shop-tab"
                                                            :class="{ 'is-active': shopContentTab === 'sales' }"
                                                            @click="shopContentTab = 'sales'"
                                                        >
                                                            Vendas
                                                            <span class="ml-1 opacity-60" x-text="'· ' + shopSalesCount(lootActiveShop)"></span>
                                                        </button>
                                                    </nav>

                                                    <section
                                                        x-show="shopContentTab === 'stock'"
                                                        x-cloak
                                                        class="sb-admin-stock"
                                                    >
                                                        <header class="sb-admin-stock__top">
                                                            <div class="sb-admin-stock__top-copy">
                                                                <strong>Estoque da loja</strong>
                                                                <span>Organizado por natureza do item. Clique em um card para abrir a ficha completa.</span>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                class="sb-master-shop-action is-primary"
                                                                @click="openShopPicker()"
                                                            >
                                                                + Adicionar produto
                                                            </button>
                                                        </header>

                                                        <div class="sb-admin-category-list">
                                                            <template
                                                                x-for="section in shopStockSections()"
                                                                :key="'master-stock-section-' + section.key"
                                                            >
                                                                <section
                                                                    x-show="
                                                                        section.items.filter(
                                                                            item =>
                                                                                (parseInt(item?.quantity_remaining) || 0) > 0
                                                                        ).length > 0
                                                                    "
                                                                    x-cloak
                                                                    class="sb-admin-category"
                                                                    :class="{ 'is-open': shopStockCategoryIsOpen(section.key) }"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="sb-admin-category__head"
                                                                        @click="toggleShopStockCategory(section.key)"
                                                                    >
                                                                        <span class="sb-admin-category__icon">
                                                                            <span
                                                                                x-text="
                                                                                    section.key === 'consumables'
                                                                                        ? '◒'
                                                                                        : (section.key === 'magical' ? '✦' : '◇')
                                                                                "
                                                                            ></span>
                                                                        </span>

                                                                        <span class="sb-admin-category__copy">
                                                                            <strong x-text="section.title"></strong>
                                                                            <span x-text="section.description"></span>
                                                                        </span>

                                                                        <span
                                                                            class="sb-admin-category__count"
                                                                            x-text="
                                                                                section.items.filter(
                                                                                    item =>
                                                                                        (parseInt(item?.quantity_remaining) || 0) > 0
                                                                                ).length
                                                                            "
                                                                        ></span>
                                                                        <span class="sb-admin-category__chevron">⌄</span>
                                                                    </button>

                                                                    <div
                                                                        x-show="shopStockCategoryIsOpen(section.key)"
                                                                        x-cloak
                                                                        class="sb-admin-category__body"
                                                                    >
                                                                        <div class="sb-admin-product-grid">
                                                                            <template
                                                                                x-for="
                                                                                    item in section.items.filter(
                                                                                        item =>
                                                                                            (parseInt(item?.quantity_remaining) || 0) > 0
                                                                                    )
                                                                                "
                                                                                :key="'admin-product-' + item.id"
                                                                            >
                                                                                <article
                                                                                    class="sb-admin-product"
                                                                                    :class="{
                                                                                        'is-soldout':
                                                                                            (parseInt(item?.quantity_remaining) || 0) <= 0
                                                                                    }"
                                                                                >
                                                                                    <button
                                                                                        type="button"
                                                                                        class="sb-admin-product__button"
                                                                                        @click="selectShopItem(item)"
                                                                                    >
                                                                                        <div class="sb-admin-product__image">
                                                                                            <template x-if="lootItemImageUrl(lootActiveShop, item)">
                                                                                                <img :src="lootItemImageUrl(lootActiveShop, item)" :alt="item.name">
                                                                                            </template>
                                                                                            <template x-if="!lootItemImageUrl(lootActiveShop, item)">
                                                                                                <span>◆</span>
                                                                                            </template>
                                                                                            <span
                                                                                                class="sb-admin-product__stock"
                                                                                                x-text="
                                                                                                    (parseInt(item?.quantity_remaining) || 0)
                                                                                                    + '/'
                                                                                                    + (parseInt(item?.quantity_total) || 0)
                                                                                                "
                                                                                            ></span>
                                                                                        </div>

                                                                                        <div class="sb-admin-product__copy">
                                                                                            <span class="sb-admin-product__kind" x-text="lootVaultClassification(item)"></span>
                                                                                            <h6 x-text="item.name"></h6>
                                                                                            <p x-text="item?.description || 'Sem descrição cadastrada.'"></p>
                                                                                        </div>
                                                                                    </button>

                                                                                    <footer class="sb-admin-product__footer">
                                                                                        <div class="sb-admin-product__price">
                                                                                            <span>Preço</span>
                                                                                            <strong x-text="shopPriceInline(item)"></strong>
                                                                                        </div>

                                                                                        <div class="sb-admin-product__quick">
                                                                                            <button type="button" @click.stop="selectShopItem(item); openShopPriceEditor(item)">Preço</button>
                                                                                            <button
                                                                                                x-show="lootCanDeleteItem(item)"
                                                                                                x-cloak
                                                                                                type="button"
                                                                                                class="!text-red-700"
                                                                                                @click.stop="deleteShopItem(item)"
                                                                                            >Remover</button>
                                                                                        </div>
                                                                                    </footer>
                                                                                </article>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </section>
                                                            </template>

                                                            <div
                                                                x-show="
                                                                    !(lootActiveShop?.items ?? []).some(
                                                                        item =>
                                                                            (parseInt(item?.quantity_remaining) || 0) > 0
                                                                    )
                                                                "
                                                                x-cloak
                                                                class="rounded-xl border border-dashed border-[#cdbb9f] bg-[#faf7f1] px-6 py-12 text-center"
                                                            >
                                                                <p class="font-serif text-lg font-black text-[#53150f]">
                                                                    Nenhum item no estoque
                                                                </p>
                                                                <p class="mt-1 text-[9px] text-[#8c6239]">
                                                                    Produtos vendidos ficam no histórico de Vendas. Adicione novos itens para reabastecer a loja.
                                                                </p>
                                                                <button
                                                                    type="button"
                                                                    class="sb-master-shop-action is-primary mt-4"
                                                                    @click="openShopPicker()"
                                                                >
                                                                    Adicionar produto
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <template x-teleport="body">
                                                            <div
                                                                x-show="shopProductModalOpen && shopActiveItem"
                                                                x-cloak
                                                                x-transition.opacity
                                                                x-data="{
                                                                    itemTipOpen: false,
                                                                    itemTipTitle: '',
                                                                    itemTipText: '',
                                                                    itemTipX: 0,
                                                                    itemTipY: 0,
                                                                    itemTipWidth: 300,
                                                                    itemTipAbove: false,

                                                                    masterItemWeapon(item) {
                                                                        const weapon =
                                                                            item?.properties?.weapon;

                                                                        return (
                                                                            weapon
                                                                            && typeof weapon === 'object'
                                                                            && !Array.isArray(weapon)
                                                                        )
                                                                            ? weapon
                                                                            : null;
                                                                    },

                                                                    masterItemIsWeapon(item) {
                                                                        return !!this.masterItemWeapon(item);
                                                                    },

                                                                    masterItemWeaponLine(item) {
                                                                        const weapon =
                                                                            this.masterItemWeapon(item);

                                                                        if (!weapon) {
                                                                            return '';
                                                                        }

                                                                        const category =
                                                                            {
                                                                                simple: 'Arma Simples',
                                                                                martial: 'Arma Marcial',
                                                                                custom: 'Arma',
                                                                            }[
                                                                                String(
                                                                                    weapon?.category
                                                                                    ?? ''
                                                                                ).toLowerCase()
                                                                            ]
                                                                            ?? 'Arma';

                                                                        const weaponType =
                                                                            String(
                                                                                weapon?.weapon_type
                                                                                ?? weapon?.category_custom
                                                                                ?? ''
                                                                            ).trim();

                                                                        const damage =
                                                                            weapon?.damage;

                                                                        let damageText = '';

                                                                        if (
                                                                            damage
                                                                            && typeof damage === 'object'
                                                                            && damage?.die
                                                                        ) {
                                                                            const dice =
                                                                                `${Math.max(
                                                                                    1,
                                                                                    parseInt(damage?.count) || 1
                                                                                )}${damage.die}`;

                                                                            damageText =
                                                                                [
                                                                                    dice,
                                                                                    damage?.type,
                                                                                ]
                                                                                    .filter(Boolean)
                                                                                    .join(' ');
                                                                        }

                                                                        const main =
                                                                            [
                                                                                category,
                                                                                weaponType,
                                                                            ]
                                                                                .filter(Boolean)
                                                                                .join(' - ');

                                                                        return damageText
                                                                            ? `${main} (${damageText})`
                                                                            : main;
                                                                    },

                                                                    masterItemRangeText(item) {
                                                                        const weapon =
                                                                            this.masterItemWeapon(item);

                                                                        if (!weapon) {
                                                                            return '';
                                                                        }

                                                                        const traits =
                                                                            Array.isArray(weapon?.traits)
                                                                                ? weapon.traits
                                                                                : [];

                                                                        const hasRange =
                                                                            traits.some(
                                                                                trait =>
                                                                                    String(
                                                                                        trait?.key
                                                                                        ?? ''
                                                                                    )
                                                                                        .trim()
                                                                                        .toLowerCase()
                                                                                    ===
                                                                                    'range'
                                                                            );

                                                                        if (!hasRange) {
                                                                            return '';
                                                                        }

                                                                        const rawNormal =
                                                                            weapon?.range_normal_ft
                                                                            ?? weapon?.range?.normal
                                                                            ?? null;

                                                                        const rawLong =
                                                                            weapon?.range_long_ft
                                                                            ?? weapon?.range?.max
                                                                            ?? null;

                                                                        const normal =
                                                                            (
                                                                                rawNormal === null
                                                                                || rawNormal === ''
                                                                                || Number.isNaN(
                                                                                    Number(rawNormal)
                                                                                )
                                                                            )
                                                                                ? null
                                                                                : Number(rawNormal);

                                                                        const long =
                                                                            (
                                                                                rawLong === null
                                                                                || rawLong === ''
                                                                                || Number.isNaN(
                                                                                    Number(rawLong)
                                                                                )
                                                                            )
                                                                                ? null
                                                                                : Number(rawLong);

                                                                        if (
                                                                            normal === null
                                                                            && long === null
                                                                        ) {
                                                                            return '';
                                                                        }

                                                                        if (
                                                                            normal !== null
                                                                            && long !== null
                                                                        ) {
                                                                            return `${normal}/${long} Ft`;
                                                                        }

                                                                        if (normal !== null) {
                                                                            return `${normal} Ft`;
                                                                        }

                                                                        return `máx. ${long} Ft`;
                                                                    },

                                                                    masterItemTooltipText(entry) {
                                                                        if (
                                                                            !entry
                                                                            || typeof entry !== 'object'
                                                                        ) {
                                                                            return '';
                                                                        }

                                                                        return String(
                                                                            entry?.description
                                                                            ?? entry?.text
                                                                            ?? entry?.value
                                                                            ?? ''
                                                                        ).trim();
                                                                    },

                                                                    openMasterItemTip(event, entry, fallback) {
                                                                        const text =
                                                                            this.masterItemTooltipText(entry);

                                                                        if (!text) {
                                                                            this.closeMasterItemTip();
                                                                            return;
                                                                        }

                                                                        const rect =
                                                                            event.currentTarget
                                                                                .getBoundingClientRect();

                                                                        const width =
                                                                            Math.min(
                                                                                320,
                                                                                Math.max(
                                                                                    220,
                                                                                    window.innerWidth - 24
                                                                                )
                                                                            );

                                                                        this.itemTipWidth =
                                                                            width;

                                                                        this.itemTipX =
                                                                            Math.max(
                                                                                12,
                                                                                Math.min(
                                                                                    rect.left,
                                                                                    window.innerWidth - width - 12
                                                                                )
                                                                            );

                                                                        this.itemTipAbove =
                                                                            rect.bottom
                                                                            >
                                                                            window.innerHeight * 0.64;

                                                                        this.itemTipY =
                                                                            this.itemTipAbove
                                                                                ? rect.top - 8
                                                                                : rect.bottom + 8;

                                                                        this.itemTipTitle =
                                                                            String(
                                                                                entry?.name
                                                                                ?? entry?.label
                                                                                ?? entry?.key
                                                                                ?? fallback
                                                                                ?? 'Detalhes'
                                                                            );

                                                                        this.itemTipText =
                                                                            text;

                                                                        this.itemTipOpen =
                                                                            true;
                                                                    },

                                                                    closeMasterItemTip() {
                                                                        this.itemTipOpen = false;
                                                                    },
                                                                }"
                                                                class="sb-master-item-detail-layer"
                                                                role="dialog"
                                                                aria-modal="true"
                                                                aria-label="Detalhes do produto"
                                                                @keydown.escape.window="
                                                                    closeMasterItemTip();
                                                                    closeShopProductModal();
                                                                "
                                                            >
                                                                <div
                                                                    class="sb-master-item-detail-backdrop"
                                                                    @click="closeShopProductModal()"
                                                                ></div>

                                                                <section
                                                                    x-show="shopActiveItem"
                                                                    x-transition
                                                                    class="sb-master-item-detail"
                                                                >
                                                                    <header class="sb-master-item-detail__topbar">
                                                                        <span>Detalhes do item</span>

                                                                        <button
                                                                            type="button"
                                                                            @click="closeShopProductModal()"
                                                                            aria-label="Fechar detalhes"
                                                                        >×</button>
                                                                    </header>

                                                                    <div
                                                                        class="sb-master-item-detail__scroll"
                                                                        @scroll.capture="closeMasterItemTip()"
                                                                    >
                                                                        <div class="sb-master-item-detail__hero">
                                                                            <div class="sb-master-item-detail__image">
                                                                                <template x-if="lootItemImageUrl(lootActiveShop, shopActiveItem)">
                                                                                    <img
                                                                                        :src="lootItemImageUrl(lootActiveShop, shopActiveItem)"
                                                                                        :alt="shopActiveItem?.name"
                                                                                    >
                                                                                </template>

                                                                                <template x-if="!lootItemImageUrl(lootActiveShop, shopActiveItem)">
                                                                                    <span>◆</span>
                                                                                </template>
                                                                            </div>

                                                                            <div class="sb-master-item-detail__identity">
                                                                                <div class="sb-master-item-detail__title-row">
                                                                                    <div class="min-w-0">
                                                                                        <span class="sb-master-item-detail__kicker">
                                                                                            Item da loja
                                                                                        </span>

                                                                                        <h2 x-text="shopActiveItem?.name"></h2>
                                                                                    </div>

                                                                                    <span
                                                                                        class="sb-master-item-detail__stock-status"
                                                                                        :class="{
                                                                                            'is-sold':
                                                                                                (parseInt(shopActiveItem?.quantity_remaining) || 0) <= 0
                                                                                        }"
                                                                                    >
                                                                                        <span class="sb-master-item-detail__stock-dot"></span>
                                                                                        <span
                                                                                            x-text="
                                                                                                (parseInt(shopActiveItem?.quantity_remaining) || 0) > 0
                                                                                                    ? shopActiveItem.quantity_remaining + ' em estoque'
                                                                                                    : 'Vendido'
                                                                                            "
                                                                                        ></span>
                                                                                    </span>
                                                                                </div>

                                                                                <em
                                                                                    class="sb-master-item-detail__classification"
                                                                                    x-text="lootVaultClassification(shopActiveItem)"
                                                                                ></em>

                                                                                <div class="sb-master-item-editorial">
                                                                                    <p
                                                                                        x-show="masterItemIsWeapon(shopActiveItem)"
                                                                                        x-cloak
                                                                                        class="sb-master-item-weapon-line"
                                                                                        x-text="masterItemWeaponLine(shopActiveItem)"
                                                                                    ></p>

                                                                                    <p
                                                                                        x-show="
                                                                                            masterItemIsWeapon(shopActiveItem)
                                                                                            && Array.isArray(shopActiveItem?.properties?.weapon?.traits)
                                                                                            && shopActiveItem.properties.weapon.traits.length > 0
                                                                                        "
                                                                                        x-cloak
                                                                                        class="sb-master-item-mechanic-line"
                                                                                    >
                                                                                        <strong>Propriedades:</strong>

                                                                                        <template
                                                                                            x-for="(trait, index) in (shopActiveItem?.properties?.weapon?.traits ?? [])"
                                                                                            :key="'master-item-trait-' + index"
                                                                                        >
                                                                                            <span>
                                                                                                <span
                                                                                                    class="sb-master-item-inline-tooltip"
                                                                                                    @mouseenter="openMasterItemTip($event, trait, 'Propriedade')"
                                                                                                    @mouseleave="closeMasterItemTip()"
                                                                                                    @focusin="openMasterItemTip($event, trait, 'Propriedade')"
                                                                                                    @focusout="closeMasterItemTip()"
                                                                                                >
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="sb-master-item-inline-term"
                                                                                                    >
                                                                                                        <span
                                                                                                            x-text="
                                                                                                                trait?.name
                                                                                                                || trait?.label
                                                                                                                || trait?.key
                                                                                                                || 'Propriedade'
                                                                                                            "
                                                                                                        ></span>

                                                                                                        <span
                                                                                                            x-show="
                                                                                                                String(
                                                                                                                    trait?.key
                                                                                                                    ?? ''
                                                                                                                )
                                                                                                                    .trim()
                                                                                                                    .toLowerCase()
                                                                                                                === 'range'
                                                                                                                && masterItemRangeText(shopActiveItem)
                                                                                                            "
                                                                                                            x-cloak
                                                                                                            class="sb-master-item-range-inline"
                                                                                                            x-text="
                                                                                                                ' ('
                                                                                                                + masterItemRangeText(shopActiveItem)
                                                                                                                + ')'
                                                                                                            "
                                                                                                        ></span>
                                                                                                    </button>
                                                                                                </span><span
                                                                                                    x-show="
                                                                                                        index
                                                                                                        <
                                                                                                        (shopActiveItem?.properties?.weapon?.traits ?? []).length - 1
                                                                                                    "
                                                                                                    aria-hidden="true"
                                                                                                >, </span>
                                                                                            </span>
                                                                                        </template>
                                                                                    </p>

                                                                                    <p
                                                                                        x-show="
                                                                                            masterItemIsWeapon(shopActiveItem)
                                                                                            && masterItemRangeText(shopActiveItem)
                                                                                        "
                                                                                        x-cloak
                                                                                        class="sb-master-item-mechanic-line sb-master-item-range-line"
                                                                                    >
                                                                                    
                                                                                    </p>

                                                                                    <p
                                                                                        x-show="
                                                                                            masterItemIsWeapon(shopActiveItem)
                                                                                            && Array.isArray(shopActiveItem?.properties?.weapon?.masteries)
                                                                                            && shopActiveItem.properties.weapon.masteries.length > 0
                                                                                        "
                                                                                        x-cloak
                                                                                        class="sb-master-item-mechanic-line"
                                                                                    >
                                                                                        <strong>Maestrias:</strong>

                                                                                        <template
                                                                                            x-for="(mastery, index) in (shopActiveItem?.properties?.weapon?.masteries ?? [])"
                                                                                            :key="'master-item-mastery-' + index"
                                                                                        >
                                                                                            <span>
                                                                                                <span
                                                                                                    class="sb-master-item-inline-tooltip"
                                                                                                    @mouseenter="openMasterItemTip($event, mastery, 'Maestria')"
                                                                                                    @mouseleave="closeMasterItemTip()"
                                                                                                    @focusin="openMasterItemTip($event, mastery, 'Maestria')"
                                                                                                    @focusout="closeMasterItemTip()"
                                                                                                >
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="sb-master-item-inline-term"
                                                                                                        x-text="mastery?.name || mastery?.label || mastery?.key || 'Maestria'"
                                                                                                    ></button>
                                                                                                </span><span
                                                                                                    x-show="
                                                                                                        index
                                                                                                        <
                                                                                                        (shopActiveItem?.properties?.weapon?.masteries ?? []).length - 1
                                                                                                    "
                                                                                                    aria-hidden="true"
                                                                                                >, </span>
                                                                                            </span>
                                                                                        </template>
                                                                                    </p>

                                                                                    <template
                                                                                        x-if="!masterItemIsWeapon(shopActiveItem)"
                                                                                    >
                                                                                        <div class="sb-master-item-nonweapon-stats">
                                                                                            <template
                                                                                                x-for="(stat, index) in shopItemDetailStats(shopActiveItem)"
                                                                                                :key="'master-item-stat-' + index"
                                                                                            >
                                                                                                <p class="sb-master-item-mechanic-line">
                                                                                                    <strong x-text="stat.label + ':'"></strong>
                                                                                                    <span x-text="stat.value"></span>
                                                                                                </p>
                                                                                            </template>
                                                                                        </div>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <section
                                                                            x-show="shopActiveItem?.description"
                                                                            x-cloak
                                                                            class="sb-master-item-detail-section"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Descrição
                                                                            </div>

                                                                            <p
                                                                                class="sb-master-item-detail-description"
                                                                                x-text="shopActiveItem?.description"
                                                                            ></p>
                                                                        </section>

                                                                        <section
                                                                            x-show="
                                                                                Array.isArray(shopActiveItem?.properties?.custom_properties)
                                                                                && shopActiveItem.properties.custom_properties.length > 0
                                                                            "
                                                                            x-cloak
                                                                            class="sb-master-item-detail-section"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Propriedades
                                                                            </div>

                                                                            <div class="sb-master-item-custom-list">
                                                                                <template
                                                                                    x-for="(entry, index) in (shopActiveItem?.properties?.custom_properties ?? [])"
                                                                                    :key="'master-custom-property-' + index"
                                                                                >
                                                                                    <div class="sb-master-item-custom-row">
                                                                                        <div class="sb-master-item-custom-row__head">
                                                                                            <h3 x-text="entry?.title || 'Propriedade'"></h3>
                                                                                            <strong
                                                                                                x-show="entry?.value"
                                                                                                x-text="entry?.value"
                                                                                            ></strong>
                                                                                        </div>

                                                                                        <p
                                                                                            x-show="entry?.description"
                                                                                            x-text="entry?.description"
                                                                                        ></p>
                                                                                    </div>
                                                                                </template>
                                                                            </div>
                                                                        </section>

                                                                        <section
                                                                            x-show="
                                                                                Array.isArray(shopActiveItem?.properties?.features)
                                                                                && shopActiveItem.properties.features.length > 0
                                                                            "
                                                                            x-cloak
                                                                            class="sb-master-item-detail-section"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Habilidades
                                                                            </div>

                                                                            <div class="sb-master-item-features">
                                                                                <template
                                                                                    x-for="(feature, index) in (shopActiveItem?.properties?.features ?? [])"
                                                                                    :key="'master-item-feature-' + index"
                                                                                >
                                                                                    <article class="sb-master-item-feature">
                                                                                        <div class="sb-master-item-feature__head">
                                                                                            <h3 x-text="feature?.title || 'Habilidade'"></h3>

                                                                                            <span
                                                                                                x-text="
                                                                                                    feature?.usage?.enabled
                                                                                                        ? (
                                                                                                            (parseInt(feature?.usage?.max) || 0) > 0
                                                                                                                ? (
                                                                                                                    (feature?.usage?.counter_mode === 'build' ? 'Acúmulo: ' : 'Usos: ')
                                                                                                                    + (parseInt(feature?.usage?.max) || 0)
                                                                                                                )
                                                                                                                : 'Uso limitado'
                                                                                                        )
                                                                                                        : 'Passiva'
                                                                                                "
                                                                                            ></span>
                                                                                        </div>

                                                                                        <p
                                                                                            x-show="feature?.description"
                                                                                            x-text="feature?.description"
                                                                                        ></p>
                                                                                    </article>
                                                                                </template>
                                                                            </div>
                                                                        </section>

                                                                        <section
                                                                            x-show="shopActiveItem?.is_cursed && shopActiveItem?.curse_description"
                                                                            x-cloak
                                                                            class="sb-master-item-detail-section"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Maldição
                                                                            </div>

                                                                            <p
                                                                                class="sb-master-item-detail-description"
                                                                                x-text="shopActiveItem?.curse_description"
                                                                            ></p>
                                                                        </section>

                                                                        <section
                                                                            x-show="shopActiveItem?.notes"
                                                                            x-cloak
                                                                            class="sb-master-item-detail-section"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Observações
                                                                            </div>

                                                                            <p
                                                                                class="sb-master-item-detail-description"
                                                                                x-text="shopActiveItem?.notes"
                                                                            ></p>
                                                                        </section>

                                                                        <section
                                                                            x-show="
                                                                                parseInt(shopEditingItemId)
                                                                                ===
                                                                                parseInt(shopActiveItem?.id)
                                                                            "
                                                                            x-cloak
                                                                            class="sb-master-item-price-editor"
                                                                        >
                                                                            <div class="sb-master-item-detail-section__title">
                                                                                Editar preço
                                                                            </div>

                                                                            <div class="sb-master-item-price-grid">
                                                                                <template
                                                                                    x-for="coin in lootCoinDefinitions"
                                                                                    :key="'master-modal-price-' + coin.key"
                                                                                >
                                                                                    <label>
                                                                                        <span x-text="coin.abbr"></span>
                                                                                        <input
                                                                                            type="number"
                                                                                            min="0"
                                                                                            step="1"
                                                                                            x-model.number="shopPriceDraft[coin.key]"
                                                                                        >
                                                                                    </label>
                                                                                </template>
                                                                            </div>
                                                                        </section>
                                                                    </div>

                                                                    <footer class="sb-master-item-detail__footer">
                                                                        <div class="sb-master-item-detail__footer-price">
                                                                            <span>Preço unitário</span>
                                                                            <strong x-text="shopPriceInline(shopActiveItem)"></strong>
                                                                        </div>

                                                                        <div class="sb-master-item-detail__footer-actions">
                                                                            <button
                                                                                x-show="
                                                                                    parseInt(shopEditingItemId)
                                                                                    !==
                                                                                    parseInt(shopActiveItem?.id)
                                                                                "
                                                                                x-cloak
                                                                                type="button"
                                                                                class="sb-master-item-detail-action"
                                                                                @click="openShopPriceEditor(shopActiveItem)"
                                                                            >
                                                                                Editar preço
                                                                            </button>

                                                                            <button
                                                                                x-show="
                                                                                    parseInt(shopEditingItemId)
                                                                                    ===
                                                                                    parseInt(shopActiveItem?.id)
                                                                                "
                                                                                x-cloak
                                                                                type="button"
                                                                                class="sb-master-item-detail-action is-primary"
                                                                                :disabled="shopUpdatingItemId !== null"
                                                                                @click="updateShopItemPrice(shopActiveItem)"
                                                                            >
                                                                                Salvar preço
                                                                            </button>

                                                                            <button
                                                                                x-show="lootCanDeleteItem(shopActiveItem)"
                                                                                x-cloak
                                                                                type="button"
                                                                                class="sb-master-item-detail-action is-danger"
                                                                                @click="deleteShopItem(shopActiveItem)"
                                                                            >
                                                                                Remover produto
                                                                            </button>
                                                                        </div>
                                                                    </footer>
                                                                </section>

                                                                <template x-teleport="body">
                                                                    <div
                                                                        x-show="itemTipOpen"
                                                                        x-cloak
                                                                        class="sb-master-item-global-tooltip"
                                                                        :class="{
                                                                            'is-above':
                                                                                itemTipAbove
                                                                        }"
                                                                        :style="
                                                                            `left:${itemTipX}px;`
                                                                            + `top:${itemTipY}px;`
                                                                            + `width:${itemTipWidth}px;`
                                                                        "
                                                                        role="tooltip"
                                                                    >
                                                                        <strong x-text="itemTipTitle"></strong>
                                                                        <span x-text="itemTipText"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </section>

                                                    <section
                                                        x-show="shopContentTab === 'sales'"
                                                        x-cloak
                                                        class="sb-master-sales"
                                                    >
                                                        <header class="sb-master-sales__head">
                                                            <div>
                                                                <span>Histórico comercial</span>
                                                                <h5>Vendas realizadas</h5>
                                                            </div>

                                                            <strong>
                                                                <span x-text="shopSalesCount(lootActiveShop)"></span>
                                                                venda(s)
                                                            </strong>
                                                        </header>

                                                        <div
                                                            x-show="shopSalesCount(lootActiveShop) > 0"
                                                            x-cloak
                                                            class="sb-master-sales__list"
                                                        >
                                                            <template
                                                                x-for="item in (lootActiveShop?.items ?? [])"
                                                                :key="'shop-sales-item-' + item.id"
                                                            >
                                                                <template
                                                                    x-for="claim in shopItemSales(item)"
                                                                    :key="'shop-sale-' + claim.id"
                                                                >
                                                                    <article
                                                                        class="sb-master-sale-card"
                                                                        @click="selectShopItem(item)"
                                                                    >
                                                                        <div class="sb-master-sale-item">
                                                                            <div class="sb-master-sale-item__image">
                                                                                <template x-if="lootItemImageUrl(lootActiveShop, item)">
                                                                                    <img
                                                                                        :src="lootItemImageUrl(lootActiveShop, item)"
                                                                                        :alt="item.name"
                                                                                    >
                                                                                </template>

                                                                                <template x-if="!lootItemImageUrl(lootActiveShop, item)">
                                                                                    <span>◆</span>
                                                                                </template>
                                                                            </div>

                                                                            <div class="sb-master-sale-item__copy">
                                                                                <span>Item vendido</span>
                                                                                <h6 x-text="item.name"></h6>
                                                                                <p x-text="lootVaultClassification(item)"></p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="sb-master-sale-arrow" aria-hidden="true">
                                                                            →
                                                                        </div>

                                                                        <div class="sb-master-sale-buyer">
                                                                            <div class="sb-master-sale-buyer__avatar">
                                                                                <template x-if="claim?.character?.image_path">
                                                                                    <img
                                                                                        :src="'/storage/' + claim.character.image_path"
                                                                                        :alt="claim?.character?.name || 'Personagem'"
                                                                                    >
                                                                                </template>

                                                                                <template x-if="!claim?.character?.image_path">
                                                                                    <span
                                                                                        x-text="
                                                                                            String(
                                                                                                claim?.character?.name
                                                                                                || claim?.user?.name
                                                                                                || '?'
                                                                                            )
                                                                                                .slice(0, 1)
                                                                                                .toUpperCase()
                                                                                        "
                                                                                    ></span>
                                                                                </template>
                                                                            </div>

                                                                            <div class="sb-master-sale-buyer__copy">
                                                                                <span>Vendido para</span>
                                                                                <h6
                                                                                    x-text="
                                                                                        claim?.character?.name
                                                                                        || 'Personagem'
                                                                                    "
                                                                                ></h6>

                                                                                <p>
                                                                                    Player:
                                                                                    <strong
                                                                                        x-text="
                                                                                            claim?.user?.name
                                                                                            || 'Conta não identificada'
                                                                                        "
                                                                                    ></strong>
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="sb-master-sale-meta">
                                                                            <div>
                                                                                <span>Quando</span>
                                                                                <strong x-text="lootClaimDate(claim.claimed_at)"></strong>
                                                                            </div>

                                                                            <div>
                                                                                <span>Quantidade</span>
                                                                                <strong x-text="(parseInt(claim.quantity) || 1) + '×'"></strong>
                                                                            </div>
                                                                        </div>

                                                                        <div class="sb-master-sale-price">
                                                                            <span>Valor pago</span>
                                                                            <strong
                                                                                x-text="
                                                                                    shopCopperValueInline(
                                                                                        claim.price_paid_copper_value
                                                                                    )
                                                                                "
                                                                            ></strong>
                                                                        </div>
                                                                    </article>
                                                                </template>
                                                            </template>
                                                        </div>

                                                        <div
                                                            x-show="shopSalesCount(lootActiveShop) === 0"
                                                            x-cloak
                                                            class="sb-master-sales__empty"
                                                        >
                                                            <div>◇</div>
                                                            <h6>Nenhuma venda ainda</h6>
                                                            <p>
                                                                Quando um Player comprar algo, a negociação aparecerá aqui com item, comprador, horário e valor pago.
                                                            </p>
                                                        </div>
                                                    </section>
                                                </div>
                                            </template>

                                            <div x-show="!lootActiveShop" x-cloak class="space-y-3">
                                                {{-- VISÃO GERAL DAS PASTAS DE LOJAS --}}
                                                <template x-if="!shopActiveFolder">
                                                    <div class="space-y-3">
                                                        <section class="sb-shop-directory-hero p-5">
                                                            <div class="flex flex-wrap items-start justify-between gap-4">
                                                                <div>
                                                                    <p class="text-[8px] font-black uppercase tracking-[0.16em] text-[#a47c5e]">Visão geral</p>
                                                                    <h4 class="mt-1 font-serif text-[26px] font-black text-[#53150f]">Lojas da campanha</h4>
                                                                    <p class="mt-2 max-w-2xl text-[10px] leading-5 text-[#76553f]">Organize suas lojas por cidades, regiões, distritos ou qualquer agrupamento útil para a campanha.</p>
                                                                </div>

                                                                <div class="flex flex-wrap gap-2">
                                                                    <button
                                                                        type="button"
                                                                        class="rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]"
                                                                        @click="shopNewFolderId = null; shopCreateOpen = true; lootCreateOpen = false"
                                                                    >+ Loja sem pasta</button>
                                                                    <button
                                                                        type="button"
                                                                        class="inline-flex items-center gap-2 rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white"
                                                                        @click="openShopFolderEditor()"
                                                                    >
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5" aria-hidden="true"><path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path><path d="M3.5 7.5V5h6l1.5 2.5"></path></svg>
                                                                        <span>+ Nova pasta</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </section>

                                                        <section class="sb-shop-directory-section p-4">
                                                            <div class="flex items-center justify-between gap-3 border-b border-[#eadcc8] pb-3">
                                                                <div>
                                                                    <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#a47c5e]">Organização</p>
                                                                    <h5 class="mt-1 font-serif text-[20px] font-black text-[#53150f]">Pastas de lojas</h5>
                                                                </div>
                                                                <span class="rounded-full border border-[#d8c7ab] bg-[#f8f1e8] px-2.5 py-1 text-[9px] font-black text-[#76553f]" x-text="shopFolders.length"></span>
                                                            </div>

                                                            <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                                <template x-for="folder in shopFolders" :key="'shop-overview-folder-' + folder.id">
                                                                    <button type="button" class="sb-shop-directory-card" @click="selectShopFolder(folder)">
                                                                        <div class="grid grid-cols-[120px_minmax(0,1fr)]">
                                                                            <div class="sb-shop-directory-card__cover">
                                                                                <template x-if="folder?.image_url"><img :src="folder.image_url" :alt="folder.name"></template>
                                                                                <template x-if="!folder?.image_url">
                                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-7 w-7 text-[#9d7659]" aria-hidden="true"><path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path><path d="M3.5 7.5V5h6l1.5 2.5"></path></svg>
                                                                                </template>
                                                                            </div>
                                                                            <div class="p-4">
                                                                                <div class="flex items-start justify-between gap-3">
                                                                                    <div class="min-w-0">
                                                                                        <p class="truncate font-serif text-[16px] font-black text-[#53150f]" x-text="folder.name"></p>
                                                                                        <p x-show="folder?.description" class="mt-1 line-clamp-2 text-[10px] leading-4 text-[#76553f]" x-text="folder.description"></p>
                                                                                    </div>
                                                                                    <span class="rounded-full border border-[#d8c7ab] bg-white px-2 py-1 text-[8px] font-black text-[#76553f]" x-text="shopFolderShopCount(folder.id)"></span>
                                                                                </div>
                                                                                <span class="mt-3 inline-block text-[8px] font-black uppercase tracking-wider text-[#8a4b20]">Abrir pasta →</span>
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                </template>

                                                                <button type="button" class="min-h-[118px] rounded-xl border border-dashed border-[#cdbb9f] bg-[#fbf7f1] p-5 text-center transition hover:border-[#8a4b20] hover:bg-white" @click="openShopFolderEditor()">
                                                                    <span class="mx-auto grid h-9 w-9 place-items-center rounded-xl bg-[#efe2d1] text-lg font-black text-[#8a4b20]">+</span>
                                                                    <strong class="mt-2 block font-serif text-[14px] text-[#53150f]">Criar nova pasta</strong>
                                                                    <span class="mt-1 block text-[9px] text-[#9a806d]">Agrupe lojas relacionadas.</span>
                                                                </button>
                                                            </div>
                                                        </section>

                                                        <section x-show="shopUnfiledShops().length > 0" x-cloak class="sb-shop-directory-section p-4">
                                                            <div class="flex items-center justify-between gap-3 border-b border-[#eadcc8] pb-3">
                                                                <h5 class="font-serif text-[18px] font-black text-[#53150f]">Lojas sem pasta</h5>
                                                                <span class="rounded-full border border-[#d8c7ab] bg-[#f8f1e8] px-2.5 py-1 text-[9px] font-black text-[#76553f]" x-text="shopUnfiledShops().length"></span>
                                                            </div>
                                                            <div class="mt-3 grid gap-2 md:grid-cols-2">
                                                                <template x-for="shop in shopUnfiledShops()" :key="'shop-overview-unfiled-' + shop.id">
                                                                    <button type="button" class="sb-shop-directory-shop" @click="selectShop(shop)">
                                                                        <span class="sb-shop-directory-shop__image">
                                                                            <template x-if="shop?.presentation_image_url"><img :src="shop.presentation_image_url" :alt="shop.name"></template>
                                                                            <template x-if="!shop?.presentation_image_url"><span class="font-serif text-lg">◇</span></template>
                                                                        </span>
                                                                        <span class="min-w-0">
                                                                            <strong class="block truncate font-serif text-[14px] text-[#53150f]" x-text="shop.name"></strong>
                                                                            <small class="mt-1 block truncate text-[9px] text-[#8c725e]" x-text="shop.description || 'Sem descrição.'"></small>
                                                                        </span>
                                                                        <span class="text-lg text-[#9a6f50]">›</span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </template>

                                                {{-- PASTA DE LOJAS ABERTA --}}
                                                <template x-if="shopActiveFolder">
                                                    <div class="space-y-3">
                                                        <section class="sb-shop-folder-hero p-4">
                                                            <div class="grid gap-4 md:grid-cols-[150px_minmax(0,1fr)_auto] md:items-center">
                                                                <div class="h-[105px] overflow-hidden rounded-2xl border border-[#d8c7ab] bg-[#f1e6d8]">
                                                                    <template x-if="shopActiveFolder?.image_url"><img :src="shopActiveFolder.image_url" :alt="shopActiveFolder.name" class="h-full w-full object-cover"></template>
                                                                    <template x-if="!shopActiveFolder?.image_url">
                                                                        <div class="grid h-full place-items-center text-[#9d7659]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-8 w-8" aria-hidden="true"><path d="M3.5 7.5h6l1.5 2h9.5v9.5h-17z"></path><path d="M3.5 7.5V5h6l1.5 2.5"></path></svg></div>
                                                                    </template>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <div class="flex flex-wrap items-center gap-2">
                                                                        <h4 class="font-serif text-[26px] font-black text-[#53150f]" x-text="shopActiveFolder.name"></h4>
                                                                        <span class="rounded-full border border-[#d8c7ab] bg-[#fffdf8] px-2.5 py-1 text-[8px] font-black text-[#76553f]" x-text="shopFolderShopCount(shopActiveFolder.id) + ' lojas'"></span>
                                                                    </div>
                                                                    <p x-show="shopActiveFolder?.description" class="mt-2 max-w-2xl text-[10px] leading-5 text-[#76553f]" x-text="shopActiveFolder.description"></p>
                                                                </div>
                                                                <div class="grid gap-2">
                                                                    <button type="button" class="rounded-xl bg-[#6b1d14] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-white" @click="shopNewFolderId = shopActiveFolder.id; shopCreateOpen = true; lootCreateOpen = false">+ Nova loja</button>
                                                                    <button type="button" class="rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-4 py-2.5 text-[8px] font-black uppercase tracking-wider text-[#6b1d14]" @click="openShopFolderEditor(shopActiveFolder)">Editar pasta</button>
                                                                </div>
                                                            </div>
                                                        </section>

                                                        <section class="sb-shop-directory-section p-4">
                                                            <div class="flex items-center justify-between gap-3 border-b border-[#eadcc8] pb-3">
                                                                <div>
                                                                    <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#a47c5e]">Lojas desta pasta</p>
                                                                    <h5 class="mt-1 font-serif text-[20px] font-black text-[#53150f]">Estabelecimentos</h5>
                                                                </div>
                                                                <button type="button" class="rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-3 py-2 text-[8px] font-black uppercase text-[#76553f]" @click="shopActiveFolderId = null">Voltar</button>
                                                            </div>

                                                            <div class="mt-3 grid gap-2">
                                                                <template x-for="shop in shopFolderShops(shopActiveFolder.id)" :key="'shop-folder-main-' + shop.id">
                                                                    <button type="button" class="sb-shop-directory-shop" @click="selectShop(shop)">
                                                                        <span class="sb-shop-directory-shop__image">
                                                                            <template x-if="shop?.presentation_image_url"><img :src="shop.presentation_image_url" :alt="shop.name"></template>
                                                                            <template x-if="!shop?.presentation_image_url"><span class="font-serif text-lg">◇</span></template>
                                                                        </span>
                                                                        <span class="min-w-0">
                                                                            <span class="flex flex-wrap items-center gap-2">
                                                                                <strong class="truncate font-serif text-[15px] text-[#53150f]" x-text="shop.name"></strong>
                                                                                <span class="rounded-full px-2 py-1 text-[7px] font-black uppercase" :class="shop.status === 'released' ? 'bg-emerald-100 text-emerald-800' : 'bg-[#eee5d8] text-[#76553f]'" x-text="shop.status === 'released' ? 'Aberta' : 'Fechada'"></span>
                                                                            </span>
                                                                            <small class="mt-1 block truncate text-[9px] text-[#8c725e]" x-text="shop.description || 'Sem descrição.'"></small>
                                                                        </span>
                                                                        <span class="text-lg text-[#9a6f50]">›</span>
                                                                    </button>
                                                                </template>

                                                                <div x-show="shopFolderShops(shopActiveFolder.id).length === 0" x-cloak class="rounded-xl border border-dashed border-[#d8c7ab] bg-[#fbf7f1] px-5 py-10 text-center">
                                                                    <p class="font-serif text-lg font-black text-[#53150f]">Nenhuma loja nesta pasta</p>
                                                                    <p class="mt-1 text-[10px] text-[#8c6239]">Crie uma loja ou mova uma existente para cá.</p>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
    </main>

    <template x-if="drawer === 'loot' && lootWorkspaceMode === 'shop' && shopMenuFolder">
        <div>
            <button type="button" class="fixed inset-0 z-[99980] cursor-default bg-transparent" @click="shopFolderMenuId = null" aria-label="Fechar menu da pasta"></button>
            <div
                class="sb-shop-folder-menu-floating fixed z-[99989]"
                :style="`left:${shopFolderMenuX}px;top:${shopFolderMenuY}px`"
                @keydown.escape.window="shopFolderMenuId = null"
            >
                <button type="button" @click.stop="openShopFolderEditor(shopMenuFolder); shopFolderMenuId = null">Editar pasta</button>
                <button type="button" @click.stop="moveShopFolder(shopMenuFolder, -1); shopFolderMenuId = null">Mover para cima</button>
                <button type="button" @click.stop="moveShopFolder(shopMenuFolder, 1); shopFolderMenuId = null">Mover para baixo</button>
                <button type="button" class="is-danger" @click.stop="deleteShopFolder(shopMenuFolder); shopFolderMenuId = null">Excluir pasta</button>
            </div>
        </div>
    </template>
</div>