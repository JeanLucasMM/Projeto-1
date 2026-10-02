                {{-- ========================================================
                     ESPÓLIOS — GESTÃO
                ========================================================= --}}
                <div
                    x-show="drawer === 'loot'"
                    x-cloak
                >
                    <div class="space-y-3">
                        <div
                            x-show="lootError"
                            x-cloak
                            class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <p
                                    class="text-[12px] font-semibold leading-5 text-red-800"
                                    x-text="lootError"
                                ></p>

                                <button
                                    type="button"
                                    class="shrink-0 text-lg font-black leading-none text-red-700"
                                    @click="lootError = null"
                                >
                                    ×
                                </button>
                            </div>
                        </div>

                        <div
                            x-show="lootLoading && !lootLoaded"
                            x-cloak
                            class="rounded-[22px] border border-[#cdbb9f]/60 bg-[#fffdf8] px-6 py-16 text-center"
                        >
                            <p class="font-serif text-2xl font-black text-[#53150f]">
                                Carregando Espólios...
                            </p>
                            <p class="mt-2 text-[12px] text-[#8c6239]">
                                Organizando tesouros, moedas e histórico da campanha.
                            </p>
                        </div>

                        <template x-if="lootLoaded">
                            <div class="space-y-3">
                                <section
                                    class="border-b border-[#d8c7ab]/70 pb-2"
                                >
                                    <div class="sb-master-loot-heading flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="sb-master-loot-heading__icon">
                                                <svg
                                                    x-show="lootWorkspaceMode === 'shop'"
                                                    x-cloak
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path d="M4 10h16"></path>
                                                    <path d="M5 10 6.5 5h11L19 10"></path>
                                                    <path d="M6 10v8.5h12V10"></path>
                                                    <path d="M9 18.5v-5h6v5"></path>
                                                </svg>

                                                <svg
                                                    x-show="lootWorkspaceMode === 'loot'"
                                                    x-cloak
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    aria-hidden="true"
                                                >
                                                    <path d="M4.5 9.25h15v10h-15z"></path>
                                                    <path d="M7 9.25V6.5h10v2.75"></path>
                                                    <path d="M4.5 13h15"></path>
                                                    <path d="M10 13v2h4v-2"></path>
                                                </svg>
                                            </div>

                                            <div class="min-w-0">
                                                <p
                                                    class="truncate font-serif text-[19px] font-black leading-tight text-[#53150f]"
                                                    x-text="
                                                        lootWorkspaceMode === 'shop'
                                                            ? 'Lojas'
                                                            : 'Espólios'
                                                    "
                                                ></p>

                                                <p
                                                    class="mt-0.5 text-[9px] font-semibold text-[#9a806d]"
                                                    x-text="
                                                        lootWorkspaceMode === 'shop'
                                                            ? 'Gerencie organização, vitrines, estoque e vendas'
                                                            : 'Prepare recompensas e distribua itens para a campanha'
                                                    "
                                                ></p>
                                            </div>
                                        </div>

                                        <div
                                            x-show="lootWorkspaceMode === 'loot'"
                                            x-cloak
                                            class="flex flex-wrap gap-2 xl:justify-end"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex h-9 items-center gap-2 rounded-lg border border-[#d8c7ab] bg-white px-3 text-[9px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:border-[#b08c62] hover:bg-[#faf7f1]"
                                                @click="openQuickLoot()"
                                            >
                                                <span aria-hidden="true">⚡</span>
                                                <span>Itens e moedas rápidas</span>
                                                <span
                                                    x-show="lootQuickActiveOfferCount > 0"
                                                    x-cloak
                                                    class="inline-flex min-w-5 items-center justify-center rounded-full bg-[#6b1d14] px-1.5 py-0.5 text-[7px] text-white"
                                                    x-text="lootQuickActiveOfferCount"
                                                ></span>
                                            </button>

                                            <button
                                                type="button"
                                                class="inline-flex h-9 items-center rounded-lg border border-[#d8c7ab] bg-white px-3 text-[9px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:border-[#b08c62] hover:bg-[#faf7f1]"
                                                @click="openLootFolderEditor()"
                                            >
                                                + Pasta
                                            </button>

                                            <button
                                                type="button"
                                                class="inline-flex h-9 items-center rounded-lg bg-[#6b1d14] px-3.5 text-[9px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f]"
                                                @click="openLootCreate(lootActiveFolderId)"
                                            >
                                                + Espólio
                                            </button>
                                        </div>

                                        <div
                                            x-show="lootWorkspaceMode === 'shop'"
                                            x-cloak
                                            class="flex flex-wrap gap-2 xl:justify-end"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex h-9 items-center rounded-lg bg-[#6b1d14] px-3.5 text-[9px] font-black uppercase tracking-wider text-white transition hover:bg-[#53150f]"
                                                @click="shopNewFolderId = null; shopCreateOpen = !shopCreateOpen; lootCreateOpen = false"
                                            >
                                                + Nova loja
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        x-show="lootCreateOpen"
                                        x-cloak
                                        class="mt-3 grid gap-3 rounded-xl border border-[#d8c7ab]/70 bg-[#f8f3ec] p-3 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,.75fr)_minmax(0,1.35fr)_auto]"
                                    >
                                        <input
                                            type="text"
                                            maxlength="150"
                                            x-model="lootNewDropName"
                                            placeholder="Nome do Espólio, ex.: Caverna do Dragão"
                                            class="h-11 rounded-xl border border-[#cdbb9f] bg-white px-3.5 text-[12px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                        >

                                        <select
                                            x-model="lootNewFolderId"
                                            class="h-11 rounded-xl border border-[#cdbb9f] bg-white px-3 text-[10px] font-bold text-[#76553f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                        >
                                            <option value="">Sem pasta</option>
                                            <template x-for="folder in lootFolders" :key="'new-loot-folder-' + folder.id">
                                                <option :value="folder.id" x-text="folder.name"></option>
                                            </template>
                                        </select>

                                        <input
                                            type="text"
                                            maxlength="10000"
                                            x-model="lootNewDropDescription"
                                            placeholder="Descrição opcional"
                                            class="h-11 rounded-xl border border-[#cdbb9f] bg-white px-3.5 text-[12px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                        >

                                        <button
                                            type="button"
                                            class="rounded-xl bg-[#6b1d14] px-5 py-3 text-[10px] font-black uppercase tracking-wider text-white disabled:opacity-50"
                                            :disabled="lootCreating"
                                            @click="createLootDrop()"
                                        >
                                            Criar Espólio
                                        </button>
                                    </div>

                                    <style>
                                        /*
                                         * Modal de criação da Loja.
                                         * Layout próprio para não depender de novas utilities do Tailwind
                                         * que podem não existir no CSS já compilado pelo Vite.
                                         */
                                        .sb-shop-create-overlay {
                                            position: fixed !important;
                                            inset: 0 !important;
                                            z-index: 99999 !important;
                                            display: flex;
                                            align-items: center !important;
                                            justify-content: center !important;
                                            overflow: hidden !important;
                                            padding: 16px !important;
                                            background: rgba(36, 23, 19, .58) !important;
                                            backdrop-filter: blur(3px);
                                            -webkit-backdrop-filter: blur(3px);
                                        }

                                        .sb-shop-create-shell {
                                            width: min(1280px, calc(100vw - 32px)) !important;
                                            height: calc(100vh - 32px) !important;
                                            height: calc(100dvh - 32px) !important;
                                            max-height: 860px !important;
                                            display: flex !important;
                                            flex-direction: column !important;
                                            overflow: hidden !important;
                                            border: 1px solid #d7c5aa !important;
                                            border-radius: 24px !important;
                                            background: #fbf8f2 !important;
                                            box-shadow: 0 30px 90px rgba(30, 17, 12, .32) !important;
                                        }

                                        .sb-shop-create-shell > header,
                                        .sb-shop-create-shell > footer {
                                            flex: 0 0 auto !important;
                                        }

                                        /*
                                         * O editor usa a prévia em largura total no topo.
                                         * Como a vitrine real do Player é extremamente horizontal
                                         * (1240×200), colocá-la numa coluna lateral deixava a cena
                                         * pequena demais para o Mestre conseguir avaliar o resultado.
                                         *
                                         * A prévia continua 1:1 em proporção, mas agora recebe quase
                                         * toda a largura do modal. Somente os controles rolam abaixo.
                                         */
                                        .sb-shop-create-body {
                                            flex: 1 1 auto !important;
                                            min-height: 0 !important;
                                            display: grid !important;
                                            grid-template-columns: minmax(0, 1fr) !important;
                                            grid-template-rows: auto minmax(0, 1fr) !important;
                                            overflow: hidden !important;
                                        }

                                        .sb-shop-create-controls {
                                            grid-column: 1 !important;
                                            grid-row: 2 !important;
                                            min-width: 0 !important;
                                            min-height: 0 !important;
                                            overflow-y: auto !important;
                                            overscroll-behavior: contain;
                                            scrollbar-gutter: stable;
                                            border-top: 1px solid #e1d5c3 !important;
                                            background: #fbf8f2 !important;
                                            padding: 20px 24px 24px !important;
                                        }

                                        .sb-shop-create-controls {
                                            scrollbar-width: thin;
                                            scrollbar-color: #ad8667 transparent;
                                        }

                                        .sb-shop-create-controls::-webkit-scrollbar {
                                            width: 6px;
                                        }

                                        .sb-shop-create-controls::-webkit-scrollbar-track {
                                            background: transparent;
                                        }

                                        .sb-shop-create-controls::-webkit-scrollbar-thumb {
                                            min-height: 44px;
                                            border-radius: 999px;
                                            background: linear-gradient(180deg, #c7a789 0%, #9e7357 100%);
                                            border: 1px solid rgba(255, 255, 255, .7);
                                        }

                                        .sb-shop-create-controls::-webkit-scrollbar-thumb:hover {
                                            background: linear-gradient(180deg, #b89170 0%, #84543d 100%);
                                        }

                                        .sb-shopkeeper-field {
                                            position: relative;
                                        }

                                        .sb-shopkeeper-trigger {
                                            width: 100%;
                                            min-height: 48px;
                                            display: flex;
                                            align-items: center;
                                            gap: 11px;
                                            padding: 7px 12px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 12px;
                                            background: #fffdfa;
                                            color: #53150f;
                                            text-align: left;
                                            box-shadow: 0 1px 0 rgba(83, 21, 15, .03);
                                            transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
                                        }

                                        .sb-shopkeeper-trigger:hover,
                                        .sb-shopkeeper-trigger.is-open {
                                            border-color: #a96843;
                                            background: #fffaf5;
                                            box-shadow: 0 0 0 3px rgba(138, 75, 32, .08);
                                        }

                                        .sb-shopkeeper-avatar {
                                            width: 34px;
                                            height: 34px;
                                            flex: 0 0 34px;
                                            display: grid;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #ddcdb8;
                                            border-radius: 10px;
                                            background: linear-gradient(145deg, #f4eadf, #fff);
                                            color: #8a5a3a;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 800;
                                        }

                                        .sb-shopkeeper-avatar img {
                                            width: 100%;
                                            height: 100%;
                                            display: block;
                                            object-fit: cover;
                                        }

                                        .sb-shopkeeper-copy {
                                            min-width: 0;
                                            flex: 1 1 auto;
                                        }

                                        .sb-shopkeeper-name {
                                            display: block;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-size: 13px;
                                            font-weight: 800;
                                            line-height: 1.2;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shopkeeper-hint {
                                            display: block;
                                            margin-top: 3px;
                                            overflow: hidden;
                                            color: #9a806d;
                                            font-size: 10px;
                                            font-weight: 600;
                                            line-height: 1.2;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shopkeeper-chevron {
                                            flex: 0 0 auto;
                                            color: #8c6239;
                                            font-size: 15px;
                                            transition: transform .16s ease;
                                        }

                                        .sb-shopkeeper-trigger.is-open .sb-shopkeeper-chevron {
                                            transform: rotate(180deg);
                                        }

                                        .sb-shopkeeper-menu {
                                            position: absolute;
                                            top: calc(100% + 8px);
                                            left: 0;
                                            right: 0;
                                            z-index: 120;
                                            overflow: hidden;
                                            border: 1px solid #d7c5aa;
                                            border-radius: 14px;
                                            background: rgba(255, 253, 250, .985);
                                            box-shadow: 0 18px 46px rgba(54, 29, 20, .20);
                                            backdrop-filter: blur(10px);
                                            -webkit-backdrop-filter: blur(10px);
                                        }

                                        .sb-shopkeeper-search-wrap {
                                            padding: 10px;
                                            border-bottom: 1px solid #eee3d5;
                                            background: #fbf6ef;
                                        }

                                        .sb-shopkeeper-search {
                                            width: 100%;
                                            height: 38px;
                                            padding: 0 12px;
                                            border: 1px solid #ddcdb8;
                                            border-radius: 10px;
                                            outline: none;
                                            background: white;
                                            color: #53150f;
                                            font-size: 12px;
                                            font-weight: 700;
                                        }

                                        .sb-shopkeeper-search:focus {
                                            border-color: #a96843;
                                            box-shadow: 0 0 0 3px rgba(138, 75, 32, .08);
                                        }

                                        .sb-shopkeeper-options {
                                            max-height: 245px;
                                            overflow-y: auto;
                                            padding: 6px;
                                            overscroll-behavior: contain;
                                            scrollbar-width: thin;
                                            scrollbar-color: #b08a6c transparent;
                                        }

                                        .sb-shopkeeper-options::-webkit-scrollbar {
                                            width: 5px;
                                        }

                                        .sb-shopkeeper-options::-webkit-scrollbar-track {
                                            background: transparent;
                                        }

                                        .sb-shopkeeper-options::-webkit-scrollbar-thumb {
                                            border-radius: 999px;
                                            background: #b08a6c;
                                        }

                                        .sb-shopkeeper-option {
                                            width: 100%;
                                            display: flex;
                                            align-items: center;
                                            gap: 10px;
                                            padding: 8px 9px;
                                            border: 0;
                                            border-radius: 10px;
                                            background: transparent;
                                            color: #53150f;
                                            text-align: left;
                                            transition: background .14s ease, color .14s ease;
                                        }

                                        .sb-shopkeeper-option:hover {
                                            background: #f6eee4;
                                        }

                                        .sb-shopkeeper-option.is-selected {
                                            background: #efe1d2;
                                        }

                                        .sb-shopkeeper-option .sb-shopkeeper-avatar {
                                            width: 30px;
                                            height: 30px;
                                            flex-basis: 30px;
                                            border-radius: 9px;
                                        }

                                        .sb-shopkeeper-check {
                                            margin-left: auto;
                                            color: #7b2c1f;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-shopkeeper-empty {
                                            padding: 18px 12px;
                                            color: #9a806d;
                                            font-size: 11px;
                                            font-weight: 700;
                                            text-align: center;
                                        }

                                        .sb-shop-create-preview {
                                            grid-column: 1 !important;
                                            grid-row: 1 !important;
                                            min-width: 0 !important;
                                            min-height: 0 !important;
                                            overflow: hidden !important;
                                            border-left: 0 !important;
                                            border-top: 0 !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f8f2e9 0%,
                                                    #f2e8dc 100%
                                                ) !important;
                                            padding: 14px 24px 18px !important;
                                        }

                                        .sb-shop-create-preview-content {
                                            width: 100% !important;
                                            height: auto !important;
                                            min-height: 0 !important;
                                            display: flex !important;
                                            flex-direction: column !important;
                                        }

                                        .sb-shop-create-preview-content > .shrink-0 {
                                            display: flex !important;
                                            align-items: end !important;
                                            justify-content: space-between !important;
                                            gap: 18px !important;
                                        }

                                        .sb-shop-create-preview-content > .shrink-0 p:last-child {
                                            max-width: 660px !important;
                                            margin-left: auto !important;
                                            text-align: right !important;
                                        }

                                        .sb-shop-create-preview .sb-shop-player-preview-shell {
                                            width: 100% !important;
                                            max-width: none !important;
                                        }

                                        .sb-shop-create-stage {
                                            position: relative !important;
                                            height: clamp(340px, 54vh, 480px) !important;
                                            min-height: 340px !important;
                                            max-height: 480px !important;
                                            overflow: hidden !important;
                                        }

                                        /*
                                         * Prévia da vitrine.
                                         * Toda a geometria crítica fica em CSS próprio para não
                                         * depender de utilities arbitrárias do Tailwind/Vite.
                                         */
                                        .sb-shop-scene-image {
                                            position: absolute !important;
                                            inset: 0 !important;
                                            width: 100% !important;
                                            height: 100% !important;
                                            object-fit: cover !important;
                                            transition: transform .15s ease !important;
                                        }

                                        .sb-shop-portrait-layer {
                                            position: absolute !important;
                                            left: 0 !important;
                                            right: 0 !important;
                                            top: 64px !important;
                                            bottom: 112px !important;
                                            z-index: 10 !important;
                                            overflow: hidden !important;
                                            pointer-events: none !important;
                                        }

                                        .sb-shop-portrait-layer.is-counterless {
                                            bottom: 8px !important;
                                        }

                                        .sb-shop-portrait-image {
                                            position: absolute !important;
                                            inset: 0 !important;
                                            width: 100% !important;
                                            height: 100% !important;
                                            object-fit: contain !important;
                                            filter: drop-shadow(0 18px 22px rgba(0,0,0,.38)) !important;
                                            transition: transform .15s ease !important;
                                        }

                                        .sb-shop-counter {
                                            --counter-color: #6B3A24;
                                            position: absolute !important;
                                            left: 4% !important;
                                            right: 4% !important;
                                            bottom: 0 !important;
                                            height: 116px !important;
                                            z-index: 30 !important;
                                            pointer-events: none !important;
                                            filter: drop-shadow(0 14px 18px rgba(31, 14, 8, .26));
                                        }

                                        .sb-shop-counter__top {
                                            position: absolute !important;
                                            left: -1.5% !important;
                                            right: -1.5% !important;
                                            top: 0 !important;
                                            height: 26px !important;
                                            border: 1px solid color-mix(in srgb, var(--counter-color) 62%, black) !important;
                                            border-radius: 12px 12px 8px 8px !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 70%, white) 0%,
                                                    color-mix(in srgb, var(--counter-color) 88%, white) 20%,
                                                    var(--counter-color) 58%,
                                                    color-mix(in srgb, var(--counter-color) 72%, black) 100%
                                                ) !important;
                                            box-shadow:
                                                inset 0 2px 0 color-mix(in srgb, var(--counter-color) 54%, white),
                                                inset 0 -4px 7px color-mix(in srgb, var(--counter-color) 78%, black),
                                                0 6px 10px rgba(25, 11, 6, .20) !important;
                                        }

                                        .sb-shop-counter__top::after {
                                            content: "";
                                            position: absolute;
                                            left: 5%;
                                            right: 5%;
                                            top: 6px;
                                            height: 3px;
                                            border-radius: 999px;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    transparent,
                                                    color-mix(in srgb, var(--counter-color) 72%, white),
                                                    transparent
                                                );
                                            opacity: .68;
                                        }

                                        .sb-shop-counter__body {
                                            position: absolute !important;
                                            left: 1.5% !important;
                                            right: 1.5% !important;
                                            top: 19px !important;
                                            bottom: 12px !important;
                                            overflow: hidden !important;
                                            border: 1px solid color-mix(in srgb, var(--counter-color) 60%, black) !important;
                                            border-radius: 4px 4px 14px 14px !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 88%, white) 0%,
                                                    var(--counter-color) 22%,
                                                    color-mix(in srgb, var(--counter-color) 88%, black) 100%
                                                ) !important;
                                            box-shadow:
                                                inset 0 3px 5px color-mix(in srgb, var(--counter-color) 68%, white),
                                                inset 0 -10px 18px color-mix(in srgb, var(--counter-color) 78%, black) !important;
                                        }

                                        .sb-shop-counter__frame {
                                            position: absolute !important;
                                            left: 5% !important;
                                            right: 5% !important;
                                            top: 12px !important;
                                            bottom: 13px !important;
                                            border: 2px solid color-mix(in srgb, var(--counter-color) 62%, black) !important;
                                            border-radius: 10px !important;
                                            box-shadow:
                                                inset 0 2px 3px color-mix(in srgb, var(--counter-color) 72%, white),
                                                inset 0 -5px 9px color-mix(in srgb, var(--counter-color) 80%, black) !important;
                                        }

                                        .sb-shop-counter__panel {
                                            position: absolute !important;
                                            top: 18px !important;
                                            bottom: 19px !important;
                                            width: 34% !important;
                                            border: 1px solid color-mix(in srgb, var(--counter-color) 64%, black) !important;
                                            border-radius: 8px !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 78%, white),
                                                    color-mix(in srgb, var(--counter-color) 94%, black)
                                                ) !important;
                                            box-shadow:
                                                inset 0 2px 3px color-mix(in srgb, var(--counter-color) 66%, white),
                                                inset 0 -4px 7px color-mix(in srgb, var(--counter-color) 80%, black) !important;
                                        }

                                        .sb-shop-counter__panel--left {
                                            left: 10% !important;
                                        }

                                        .sb-shop-counter__panel--right {
                                            right: 10% !important;
                                        }

                                        .sb-shop-counter__stile {
                                            position: absolute !important;
                                            left: 50% !important;
                                            top: 14px !important;
                                            bottom: 16px !important;
                                            width: 9px !important;
                                            transform: translateX(-50%) !important;
                                            border-radius: 999px !important;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    color-mix(in srgb, var(--counter-color) 76%, black),
                                                    color-mix(in srgb, var(--counter-color) 74%, white),
                                                    color-mix(in srgb, var(--counter-color) 76%, black)
                                                ) !important;
                                            box-shadow: 0 0 7px color-mix(in srgb, var(--counter-color) 76%, black) !important;
                                        }

                                        .sb-shop-counter__post {
                                            position: absolute !important;
                                            top: 10px !important;
                                            bottom: 10px !important;
                                            width: 10px !important;
                                            border-radius: 999px !important;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    color-mix(in srgb, var(--counter-color) 77%, black),
                                                    color-mix(in srgb, var(--counter-color) 70%, white),
                                                    color-mix(in srgb, var(--counter-color) 77%, black)
                                                ) !important;
                                        }

                                        .sb-shop-counter__post--left {
                                            left: 4.5% !important;
                                        }

                                        .sb-shop-counter__post--right {
                                            right: 4.5% !important;
                                        }

                                        .sb-shop-counter__base {
                                            position: absolute !important;
                                            left: 3% !important;
                                            right: 3% !important;
                                            bottom: 0 !important;
                                            height: 15px !important;
                                            border: 1px solid color-mix(in srgb, var(--counter-color) 60%, black) !important;
                                            border-radius: 5px 5px 8px 8px !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 82%, white),
                                                    var(--counter-color) 35%,
                                                    color-mix(in srgb, var(--counter-color) 74%, black)
                                                ) !important;
                                            box-shadow:
                                                inset 0 2px 0 color-mix(in srgb, var(--counter-color) 68%, white),
                                                0 5px 9px rgba(25,11,6,.18) !important;
                                        }

                                        .sb-shop-counter__foot-shadow {
                                            position: absolute !important;
                                            left: 9% !important;
                                            right: 9% !important;
                                            bottom: -8px !important;
                                            height: 14px !important;
                                            border-radius: 50% !important;
                                            background: rgba(18, 8, 4, .28) !important;
                                            filter: blur(8px) !important;
                                        }



                                        /*
                                         * Prévia 1:1 da cena do Player.
                                         * O shell é um container query; todas as medidas em cqw
                                         * representam a mesma proporção do canvas real 1240×200.
                                         */
                                        .sb-shop-player-preview-shell {
                                            container-type: inline-size;
                                            overflow: hidden;
                                            border-radius: 20px;
                                            background: #2d211d;
                                        }

                                        .sb-shop-create-stage {
                                            position: relative !important;
                                            width: 100% !important;
                                            height: auto !important;
                                            min-height: 0 !important;
                                            max-height: none !important;
                                            aspect-ratio: 1240 / 200 !important;
                                            overflow: hidden !important;
                                            isolation: isolate;
                                        }

                                        .sb-shop-create-stage::after {
                                            content: "";
                                            position: absolute;
                                            right: 0;
                                            bottom: 0;
                                            left: 0;
                                            height: 2.581cqw;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    rgba(40, 19, 10, 0) 0%,
                                                    rgba(40, 19, 10, .38) 55%,
                                                    rgba(26, 12, 7, .74) 100%
                                                );
                                            pointer-events: none;
                                            z-index: 2;
                                        }

                                        .sb-shop-preview-shade {
                                            position: absolute;
                                            inset: 0;
                                            z-index: 1;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    rgba(18,10,7,.48),
                                                    rgba(18,10,7,.08) 42%,
                                                    rgba(18,10,7,.14)
                                                ),
                                                linear-gradient(
                                                    180deg,
                                                    rgba(10,6,4,.12),
                                                    transparent 38%,
                                                    rgba(10,6,4,.28)
                                                );
                                            pointer-events: none;
                                        }

                                        .sb-shop-preview-title {
                                            position: absolute;
                                            top: .968cqw;
                                            left: 1.210cqw;
                                            z-index: 6;
                                            width: 28.226cqw;
                                            min-width: 0;
                                            overflow: hidden;
                                            border: .081cqw solid rgba(255,255,255,.14);
                                            border-radius: 1.129cqw;
                                            background: rgba(20,12,8,.40);
                                            padding: .726cqw .968cqw;
                                            color: #fff8ef;
                                            box-shadow: 0 .968cqw 2.581cqw rgba(0,0,0,.18);
                                            backdrop-filter: blur(.726cqw);
                                        }

                                        .sb-shop-preview-status {
                                            display: flex;
                                            align-items: center;
                                            gap: .484cqw;
                                            color: rgba(255,248,239,.72);
                                            font-size: .645cqw;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-preview-open-dot {
                                            width: .565cqw;
                                            height: .565cqw;
                                            flex: 0 0 auto;
                                            border-radius: 999px;
                                            background: #10b981;
                                            box-shadow: 0 0 0 .323cqw rgba(16,185,129,.10);
                                        }

                                        .sb-shop-preview-title strong {
                                            display: block;
                                            overflow: hidden;
                                            margin-top: .242cqw;
                                            color: #fff;
                                            font-family: Georgia, serif;
                                            font-size: 2.742cqw;
                                            font-weight: 900;
                                            line-height: 1.02;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                            text-shadow: 0 .242cqw .968cqw rgba(0,0,0,.32);
                                        }

                                        .sb-shop-preview-title small {
                                            display: block;
                                            overflow: hidden;
                                            margin-top: .323cqw;
                                            color: rgba(255,248,239,.82);
                                            font-size: .726cqw;
                                            font-weight: 700;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-portrait-layer {
                                            position: absolute !important;
                                            top: .484cqw !important;
                                            right: 34% !important;
                                            bottom: 1.694cqw !important;
                                            left: 34% !important;
                                            z-index: 4 !important;
                                            overflow: hidden !important;
                                            pointer-events: none !important;
                                        }

                                        .sb-shop-portrait-layer.is-counterless {
                                            bottom: 0 !important;
                                        }

                                        .sb-shop-portrait-image {
                                            position: absolute !important;
                                            inset: 0 !important;
                                            width: 100% !important;
                                            height: 100% !important;
                                            object-fit: contain !important;
                                            object-position: center bottom;
                                            filter: drop-shadow(0 .968cqw 1.290cqw rgba(0,0,0,.34)) !important;
                                            transition: transform .15s ease !important;
                                        }

                                        .sb-shop-counter {
                                            position: absolute !important;
                                            inset: 0 !important;
                                            z-index: 6 !important;
                                            height: auto !important;
                                            filter: none !important;
                                            pointer-events: none !important;
                                        }

                                        .sb-shop-counter__top {
                                            position: absolute !important;
                                            top: auto !important;
                                            right: 2.8% !important;
                                            bottom: 1.532cqw !important;
                                            left: 2.8% !important;
                                            height: .806cqw !important;
                                            border: .081cqw solid color-mix(in srgb, var(--counter-color) 60%, black) !important;
                                            border-radius: 1.129cqw !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 68%, white) 0%,
                                                    color-mix(in srgb, var(--counter-color) 88%, white) 20%,
                                                    var(--counter-color) 58%,
                                                    color-mix(in srgb, var(--counter-color) 72%, black) 100%
                                                ) !important;
                                            box-shadow:
                                                0 -.806cqw 1.613cqw rgba(20,9,5,.18),
                                                0 .806cqw 1.290cqw rgba(20,9,5,.18) !important;
                                        }

                                        .sb-shop-counter__top::after {
                                            top: .161cqw !important;
                                            right: 6% !important;
                                            left: 6% !important;
                                            height: .161cqw !important;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    transparent,
                                                    color-mix(in srgb, var(--counter-color) 62%, white),
                                                    transparent
                                                ) !important;
                                            opacity: .75 !important;
                                        }

                                        .sb-shop-counter__body {
                                            position: absolute !important;
                                            top: auto !important;
                                            right: 4.8% !important;
                                            bottom: 0 !important;
                                            left: 4.8% !important;
                                            height: 1.774cqw !important;
                                            overflow: hidden !important;
                                            border: .081cqw solid color-mix(in srgb, var(--counter-color) 58%, black) !important;
                                            border-radius: 1.129cqw 1.129cqw 0 0 !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 82%, white) 0%,
                                                    var(--counter-color) 26%,
                                                    color-mix(in srgb, var(--counter-color) 82%, black) 100%
                                                ) !important;
                                            box-shadow:
                                                0 .968cqw 1.774cqw rgba(20,9,5,.18),
                                                inset 0 .081cqw 0 rgba(255,255,255,.08) !important;
                                        }

                                        .sb-shop-counter__body::before {
                                            content: "";
                                            position: absolute;
                                            top: .403cqw;
                                            right: 4%;
                                            left: 4%;
                                            height: .081cqw;
                                            background: rgba(255,255,255,.08);
                                        }

                                        .sb-shop-counter__body::after {
                                            content: "";
                                            position: absolute;
                                            right: 0;
                                            bottom: 0;
                                            left: 0;
                                            height: .323cqw;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    rgba(0,0,0,0),
                                                    rgba(0,0,0,.18)
                                                );
                                        }

                                        .sb-shop-counter__panel {
                                            position: absolute !important;
                                            top: .323cqw !important;
                                            bottom: .323cqw !important;
                                            width: 30% !important;
                                            border: .081cqw solid color-mix(in srgb, var(--counter-color) 58%, black) !important;
                                            border-radius: .645cqw !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    color-mix(in srgb, var(--counter-color) 88%, white) 0%,
                                                    color-mix(in srgb, var(--counter-color) 88%, black) 100%
                                                ) !important;
                                            opacity: .56 !important;
                                        }

                                        .sb-shop-counter__panel--left {
                                            left: 9% !important;
                                        }

                                        .sb-shop-counter__panel--right {
                                            right: 9% !important;
                                        }

                                        .sb-shop-counter__frame,
                                        .sb-shop-counter__stile,
                                        .sb-shop-counter__post,
                                        .sb-shop-counter__base,
                                        .sb-shop-counter__foot-shadow {
                                            display: none !important;
                                        }

                                        .sb-shop-upload-feedback {
                                            margin-top: 10px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 10px;
                                            padding: 8px 10px;
                                            font-size: 10px;
                                            font-weight: 700;
                                            line-height: 1.45;
                                        }

                                        /* =========================================================
                                           Loja do Mestre — painel e estoque
                                           ========================================================= */
                                        .sb-master-shop-overview {
                                            position: relative;
                                            overflow: hidden;
                                            border: 1px solid #decdb7;
                                            border-radius: 20px;
                                            background:
                                                radial-gradient(circle at 88% -18%, rgba(164, 102, 58, .13), transparent 34%),
                                                linear-gradient(145deg, #fffdfa 0%, #fbf6ee 54%, #f4eadf 100%);
                                            box-shadow: 0 12px 34px rgba(73, 42, 28, .07);
                                        }

                                        .sb-master-shop-overview::before {
                                            content: "";
                                            position: absolute;
                                            inset: 0 auto 0 0;
                                            width: 4px;
                                            background: linear-gradient(180deg, #6b1d14, #a25f37);
                                        }

                                        .sb-master-shop-overview__inner {
                                            position: relative;
                                            z-index: 1;
                                            padding: 20px 20px 18px 22px;
                                        }

                                        .sb-master-shop-overview__top {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 18px;
                                        }

                                        .sb-master-shop-identity {
                                            min-width: 0;
                                            display: flex;
                                            align-items: center;
                                            gap: 14px;
                                        }

                                        .sb-master-shop-avatar {
                                            width: 58px;
                                            height: 58px;
                                            flex: 0 0 58px;
                                            display: grid;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #dbc8ad;
                                            border-radius: 16px;
                                            background: linear-gradient(145deg, #efe2d2, #fffaf4);
                                            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
                                            color: #8f684c;
                                            font-family: Georgia, serif;
                                            font-size: 22px;
                                            font-weight: 900;
                                        }

                                        .sb-master-shop-avatar img {
                                            width: 100%;
                                            height: 100%;
                                            display: block;
                                            object-fit: cover;
                                        }

                                        .sb-master-shop-copy {
                                            min-width: 0;
                                        }

                                        .sb-master-shop-kicker {
                                            margin: 0 0 4px;
                                            color: #9b775d;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .16em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-title-row {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 9px;
                                        }

                                        .sb-master-shop-title {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 27px;
                                            font-weight: 900;
                                            line-height: 1.05;
                                        }

                                        .sb-master-shop-status {
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;
                                            padding: 6px 10px;
                                            border: 1px solid #d5c4ae;
                                            border-radius: 999px;
                                            background: rgba(255,255,255,.72);
                                            color: #725744;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-status::before {
                                            content: "";
                                            width: 7px;
                                            height: 7px;
                                            border-radius: 999px;
                                            background: #a58f7a;
                                        }

                                        .sb-master-shop-status.is-open {
                                            border-color: #a8d8c1;
                                            background: #edf9f3;
                                            color: #17654b;
                                        }

                                        .sb-master-shop-status.is-open::before {
                                            background: #15966b;
                                            box-shadow: 0 0 0 3px rgba(21,150,107,.10);
                                        }

                                        .sb-master-shop-description {
                                            max-width: 690px;
                                            margin: 7px 0 0;
                                            color: #755947;
                                            font-size: 12px;
                                            line-height: 1.55;
                                        }

                                        .sb-master-shop-actions {
                                            flex: 0 0 auto;
                                            display: flex;
                                            flex-wrap: wrap;
                                            justify-content: flex-end;
                                            gap: 8px;
                                        }

                                        .sb-master-shop-action {
                                            min-height: 38px;
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            gap: 7px;
                                            padding: 0 13px;
                                            border: 1px solid #d6c2a8;
                                            border-radius: 11px;
                                            background: rgba(255,253,250,.86);
                                            color: #6b1d14;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .06em;
                                            text-transform: uppercase;
                                            transition: transform .14s ease, border-color .14s ease, background .14s ease, box-shadow .14s ease;
                                        }

                                        .sb-master-shop-action:hover {
                                            transform: translateY(-1px);
                                            border-color: #b98d68;
                                            background: #fff;
                                            box-shadow: 0 6px 16px rgba(94, 49, 30, .08);
                                        }

                                        .sb-master-shop-action.is-primary {
                                            border-color: #6b1d14;
                                            background: #6b1d14;
                                            color: white;
                                        }

                                        .sb-master-shop-action.is-success {
                                            border-color: #16785a;
                                            background: #16785a;
                                            color: white;
                                        }

                                        .sb-master-shop-metrics {
                                            display: grid;
                                            grid-template-columns: repeat(3, minmax(0, 1fr));
                                            gap: 9px;
                                            margin-top: 16px;
                                            padding-top: 14px;
                                            border-top: 1px solid rgba(205, 187, 159, .55);
                                        }

                                        .sb-master-shop-metric {
                                            min-width: 0;
                                            padding: 11px 13px;
                                            border: 1px solid rgba(216, 199, 171, .72);
                                            border-radius: 13px;
                                            background: rgba(255,255,255,.58);
                                        }

                                        .sb-master-shop-metric span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .11em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-metric strong {
                                            display: block;
                                            margin-top: 3px;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 19px;
                                            font-weight: 900;
                                            line-height: 1.15;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-master-shop-tabs {
                                            display: grid;
                                            grid-template-columns: repeat(2, minmax(0, 1fr));
                                            gap: 5px;
                                            padding: 5px;
                                            border: 1px solid #ddcdb8;
                                            border-radius: 14px;
                                            background: #eee5d8;
                                        }

                                        .sb-master-shop-tab {
                                            min-height: 38px;
                                            border: 0;
                                            border-radius: 10px;
                                            background: transparent;
                                            color: #81644f;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                            transition: background .15s ease, color .15s ease, box-shadow .15s ease;
                                        }

                                        .sb-master-shop-tab:hover {
                                            background: rgba(255,255,255,.6);
                                        }

                                        .sb-master-shop-tab.is-active {
                                            background: #6b1d14;
                                            color: white;
                                            box-shadow: 0 4px 12px rgba(83,21,15,.14);
                                        }

                                        .sb-master-shop-stock {
                                            overflow: hidden;
                                            border: 1px solid #ddcdb8;
                                            border-radius: 18px;
                                            background: rgba(255,255,255,.82);
                                            box-shadow: 0 9px 24px rgba(73,42,28,.045);
                                        }

                                        .sb-master-shop-stock-head {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 16px;
                                            padding: 17px 18px 14px;
                                            border-bottom: 1px solid #eee3d5;
                                            background: linear-gradient(180deg, #fffdfa, #fbf6ef);
                                        }

                                        .sb-master-shop-stock-head h5 {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 20px;
                                            font-weight: 900;
                                        }

                                        .sb-master-shop-stock-head p {
                                            margin: 4px 0 0;
                                            color: #8c6b54;
                                            font-size: 10px;
                                            line-height: 1.45;
                                        }

                                        .sb-master-shop-stock-list {
                                            display: grid;
                                            gap: 10px;
                                            padding: 12px;
                                        }

                                        .sb-master-shop-item {
                                            overflow: hidden;
                                            border: 1px solid #e0d2bd;
                                            border-radius: 15px;
                                            background: #fffdfa;
                                            box-shadow: 0 3px 10px rgba(76,44,30,.025);
                                            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
                                        }

                                        .sb-master-shop-item:hover {
                                            border-color: #c9a888;
                                            box-shadow: 0 9px 24px rgba(76,44,30,.07);
                                        }

                                        .sb-master-shop-item.is-open {
                                            border-color: #b98d68;
                                            box-shadow: 0 12px 28px rgba(76,44,30,.085);
                                        }

                                        .sb-master-shop-item-summary {
                                            width: 100%;
                                            display: grid;
                                            grid-template-columns: 76px minmax(0, 1fr) auto;
                                            align-items: center;
                                            gap: 13px;
                                            padding: 12px;
                                            border: 0;
                                            background: transparent;
                                            color: inherit;
                                            text-align: left;
                                            cursor: pointer;
                                        }

                                        .sb-master-shop-item-thumb {
                                            width: 76px;
                                            height: 76px;
                                            display: grid;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #dac8ae;
                                            border-radius: 13px;
                                            background: linear-gradient(145deg, #efe5d8, #fff);
                                        }

                                        .sb-master-shop-item-thumb img {
                                            width: 100%;
                                            height: 100%;
                                            display: block;
                                            object-fit: cover;
                                        }

                                        .sb-master-shop-item-main {
                                            min-width: 0;
                                        }

                                        .sb-master-shop-item-name {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 7px;
                                        }

                                        .sb-master-shop-item-name h6 {
                                            margin: 0;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 18px;
                                            font-weight: 900;
                                            line-height: 1.15;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-master-shop-item-kind {
                                            margin: 4px 0 0;
                                            color: #917056;
                                            font-size: 9px;
                                            font-weight: 700;
                                        }

                                        .sb-master-shop-item-line {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 8px 14px;
                                            margin-top: 9px;
                                        }

                                        .sb-master-shop-item-price {
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 16px;
                                            font-weight: 900;
                                        }

                                        .sb-master-shop-item-meta {
                                            color: #8a745f;
                                            font-size: 9px;
                                            font-weight: 700;
                                        }

                                        .sb-master-shop-item-side {
                                            display: flex;
                                            align-items: center;
                                            gap: 10px;
                                        }

                                        .sb-master-shop-stock-pill {
                                            display: inline-flex;
                                            align-items: center;
                                            padding: 6px 9px;
                                            border-radius: 999px;
                                            background: #f3e7d8;
                                            color: #7b3a2a;
                                            font-size: 8px;
                                            font-weight: 900;
                                            white-space: nowrap;
                                        }

                                        .sb-master-shop-item-chevron {
                                            width: 30px;
                                            height: 30px;
                                            display: grid;
                                            place-items: center;
                                            border: 1px solid #dfcfbb;
                                            border-radius: 9px;
                                            background: white;
                                            color: #815f49;
                                            font-size: 14px;
                                            transition: transform .16s ease, background .16s ease;
                                        }

                                        .sb-master-shop-item.is-open .sb-master-shop-item-chevron {
                                            transform: rotate(180deg);
                                            background: #f4e7d9;
                                        }

                                        .sb-master-shop-item-details {
                                            border-top: 1px solid #eee3d5;
                                            background: linear-gradient(180deg, #fbf7f1 0%, #f7efe6 100%);
                                            padding: 15px;
                                        }

                                        .sb-master-shop-narration {
                                            padding: 14px 15px;
                                            border: 1px solid #dcc9af;
                                            border-radius: 13px;
                                            background: #fffdfa;
                                        }

                                        .sb-master-shop-narration-label {
                                            margin: 0 0 6px;
                                            color: #9a7558;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .13em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-narration p:last-child {
                                            margin: 0;
                                            color: #60483a;
                                            font-size: 12px;
                                            line-height: 1.65;
                                            white-space: pre-line;
                                        }

                                        .sb-master-shop-detail-grid {
                                            display: grid;
                                            grid-template-columns: repeat(4, minmax(0, 1fr));
                                            gap: 8px;
                                            margin-top: 10px;
                                        }

                                        .sb-master-shop-detail-stat {
                                            min-width: 0;
                                            padding: 10px 11px;
                                            border: 1px solid #e0d2bd;
                                            border-radius: 11px;
                                            background: rgba(255,255,255,.74);
                                        }

                                        .sb-master-shop-detail-stat span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 7.5px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-detail-stat strong {
                                            display: block;
                                            margin-top: 3px;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-size: 11px;
                                            font-weight: 900;
                                            line-height: 1.25;
                                            text-overflow: ellipsis;
                                        }

                                        .sb-master-shop-rules {
                                            display: grid;
                                            grid-template-columns: repeat(2, minmax(0, 1fr));
                                            gap: 8px;
                                            margin-top: 10px;
                                        }

                                        .sb-master-shop-rule {
                                            padding: 11px 12px;
                                            border: 1px solid #e0d2bd;
                                            border-radius: 11px;
                                            background: rgba(255,255,255,.76);
                                        }

                                        .sb-master-shop-rule strong {
                                            display: block;
                                            color: #6b1d14;
                                            font-size: 10px;
                                            font-weight: 900;
                                        }

                                        .sb-master-shop-rule p {
                                            margin: 4px 0 0;
                                            color: #725947;
                                            font-size: 10px;
                                            line-height: 1.5;
                                        }

                                        .sb-master-shop-item-actions {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 8px;
                                            padding: 9px 12px 12px;
                                        }

                                        .sb-master-shop-item-actions-left {
                                            color: #927b68;
                                            font-size: 8px;
                                            font-weight: 800;
                                        }

                                        .sb-master-shop-item-actions-right {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 7px;
                                        }

                                        .sb-master-shop-small-action {
                                            min-height: 32px;
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            padding: 0 11px;
                                            border: 1px solid #d6c2a8;
                                            border-radius: 9px;
                                            background: white;
                                            color: #6b1d14;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-small-action:hover {
                                            background: #f6eee4;
                                        }

                                        .sb-master-shop-small-action.is-danger {
                                            border-color: transparent;
                                            background: transparent;
                                            color: #b23d35;
                                        }

                                        @media (max-width: 900px) {
                                            .sb-folder-modal-layer {
                                                padding: 10px;
                                            }

                                            .sb-folder-modal {
                                                max-height: 94vh;
                                            }

                                            .sb-folder-modal__footer {
                                                padding: 11px 12px;
                                            }

                                            .sb-master-shop-overview__top {
                                                flex-direction: column;
                                            }

                                            .sb-master-shop-actions {
                                                justify-content: flex-start;
                                            }

                                            .sb-master-shop-detail-grid {
                                                grid-template-columns: repeat(2, minmax(0, 1fr));
                                            }

                                            .sb-master-shop-rules {
                                                grid-template-columns: 1fr;
                                            }
                                        }

                                        @media (max-width: 620px) {
                                            .sb-master-shop-identity {
                                                align-items: flex-start;
                                            }

                                            .sb-master-shop-avatar {
                                                width: 48px;
                                                height: 48px;
                                                flex-basis: 48px;
                                            }

                                            .sb-master-shop-title {
                                                font-size: 22px;
                                            }

                                            .sb-master-shop-metrics {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-master-shop-item-summary {
                                                grid-template-columns: 58px minmax(0, 1fr);
                                            }

                                            .sb-master-shop-item-thumb {
                                                width: 58px;
                                                height: 58px;
                                            }

                                            .sb-master-shop-item-side {
                                                grid-column: 1 / -1;
                                                justify-content: space-between;
                                            }
                                        }

                                        @media (max-width: 1023px) {
                                            .sb-shop-create-overlay {
                                                align-items: stretch !important;
                                                padding: 8px !important;
                                            }

                                            .sb-shop-create-shell {
                                                width: calc(100vw - 16px) !important;
                                                height: calc(100vh - 16px) !important;
                                                height: calc(100dvh - 16px) !important;
                                                max-height: none !important;
                                                border-radius: 18px !important;
                                            }

                                            .sb-shop-create-body {
                                                display: flex !important;
                                                flex-direction: column !important;
                                                overflow: hidden !important;
                                            }

                                            .sb-shop-create-preview {
                                                order: -1 !important;
                                                flex: 0 0 auto !important;
                                                overflow: visible !important;
                                                border-left: 0 !important;
                                                border-top: 0 !important;
                                                border-bottom: 1px solid #e1d5c3 !important;
                                                padding: 12px 16px 14px !important;
                                            }

                                            .sb-shop-create-preview-content {
                                                height: auto !important;
                                            }

                                            .sb-shop-create-preview-content > .shrink-0 {
                                                display: block !important;
                                            }

                                            .sb-shop-create-preview-content > .shrink-0 p:last-child {
                                                max-width: none !important;
                                                margin-left: 0 !important;
                                                text-align: left !important;
                                            }

                                            .sb-shop-create-controls {
                                                order: 0 !important;
                                                flex: 1 1 auto !important;
                                                min-height: 0 !important;
                                                overflow-y: auto !important;
                                                padding: 16px !important;
                                            }

                                            .sb-shop-create-stage {
                                                width: 100% !important;
                                                height: auto !important;
                                                min-height: 0 !important;
                                                max-height: none !important;
                                                aspect-ratio: 1240 / 200 !important;
                                            }
                                        }


                                        /* =========================================================
                                           LOJA DO MESTRE — CATÁLOGO / FICHA DO PRODUTO
                                           ========================================================= */

                                        .sb-shop-catalog {
                                            overflow: hidden;
                                            border: 1px solid #dbc8ad;
                                            border-radius: 20px;
                                            background: linear-gradient(180deg, rgba(255,253,249,.98), rgba(248,241,231,.98));
                                            box-shadow: 0 14px 40px rgba(76,44,30,.07);
                                        }

                                        .sb-shop-catalog-head {
                                            position: relative;
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 24px;
                                            padding: 22px 24px 20px;
                                            border-bottom: 1px solid #eadcca;
                                            background:
                                                radial-gradient(circle at 90% 0%, rgba(107,29,20,.065), transparent 32%),
                                                linear-gradient(180deg, #fffefa 0%, #fbf6ef 100%);
                                        }

                                        .sb-shop-catalog-head__copy {
                                            min-width: 0;
                                        }

                                        .sb-shop-catalog-eyebrow {
                                            margin: 0 0 4px;
                                            color: #a47d5d;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .16em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-catalog-title {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 23px;
                                            font-weight: 900;
                                            line-height: 1.1;
                                        }

                                        .sb-shop-catalog-description {
                                            max-width: 640px;
                                            margin: 6px 0 0;
                                            color: #8a6d57;
                                            font-size: 10px;
                                            line-height: 1.55;
                                        }

                                        .sb-shop-catalog-toolbar {
                                            display: flex;
                                            align-items: center;
                                            gap: 9px;
                                            flex-shrink: 0;
                                        }

                                        .sb-shop-catalog-search-wrap {
                                            position: relative;
                                            width: 230px;
                                        }

                                        .sb-shop-catalog-search-icon {
                                            position: absolute;
                                            top: 50%;
                                            left: 12px;
                                            display: grid;
                                            place-items: center;
                                            width: 15px;
                                            height: 15px;
                                            transform: translateY(-50%);
                                            color: #9b7a61;
                                            pointer-events: none;
                                        }

                                        .sb-shop-catalog-search-icon svg {
                                            width: 15px;
                                            height: 15px;
                                        }

                                        .sb-shop-catalog-search {
                                            width: 100%;
                                            height: 40px;
                                            padding: 0 12px 0 36px;
                                            border: 1px solid #d8c6ad;
                                            border-radius: 11px;
                                            outline: none;
                                            background: rgba(255,255,255,.88);
                                            color: #53150f;
                                            font-size: 10px;
                                            font-weight: 700;
                                            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
                                        }

                                        .sb-shop-catalog-search::placeholder {
                                            color: #af9987;
                                        }

                                        .sb-shop-catalog-search:focus {
                                            border-color: #a56a48;
                                            background: #fff;
                                            box-shadow: 0 0 0 3px rgba(107,29,20,.07);
                                        }

                                        .sb-shop-catalog-summary {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 8px;
                                            padding: 11px 18px;
                                            border-bottom: 1px solid #eadcca;
                                            background: rgba(247,240,230,.82);
                                        }

                                        .sb-shop-catalog-filter {
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 7px;
                                            min-height: 31px;
                                            padding: 0 11px;
                                            border: 1px solid transparent;
                                            border-radius: 999px;
                                            background: transparent;
                                            color: #806650;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .035em;
                                            text-transform: uppercase;
                                            transition: background .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
                                        }

                                        .sb-shop-catalog-filter:hover {
                                            background: #fff;
                                            color: #53150f;
                                        }

                                        .sb-shop-catalog-filter.is-active {
                                            border-color: #dbc5ab;
                                            background: #fff;
                                            color: #6b1d14;
                                            box-shadow: 0 3px 10px rgba(73,42,28,.05);
                                        }

                                        .sb-shop-catalog-filter span {
                                            display: grid;
                                            place-items: center;
                                            min-width: 20px;
                                            height: 20px;
                                            padding: 0 5px;
                                            border-radius: 999px;
                                            background: #efe1d0;
                                            color: #7c4d36;
                                            font-size: 8px;
                                        }

                                        .sb-shop-catalog-layout {
                                            display: grid;
                                            grid-template-columns: minmax(290px, .82fr) minmax(430px, 1.38fr);
                                            min-height: 510px;
                                        }

                                        .sb-shop-product-list {
                                            min-width: 0;
                                            padding: 15px;
                                            border-right: 1px solid #e5d5c1;
                                            background: linear-gradient(180deg, rgba(247,240,230,.66), rgba(251,247,241,.92));
                                        }

                                        .sb-shop-product-list-head {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 12px;
                                            margin-bottom: 10px;
                                            padding: 0 2px;
                                        }

                                        .sb-shop-product-list-head strong {
                                            color: #684536;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-product-list-head span {
                                            color: #a08773;
                                            font-size: 8px;
                                            font-weight: 800;
                                        }

                                        .sb-shop-product-list-scroll {
                                            display: grid;
                                            gap: 9px;
                                            max-height: 650px;
                                            overflow-y: auto;
                                            padding: 2px 5px 5px 2px;
                                        }

                                        .sb-shop-product-list-scroll::-webkit-scrollbar {
                                            width: 5px;
                                        }

                                        .sb-shop-product-list-scroll::-webkit-scrollbar-track {
                                            background: transparent;
                                        }

                                        .sb-shop-product-list-scroll::-webkit-scrollbar-thumb {
                                            border-radius: 999px;
                                            background: #d4c0a5;
                                        }

                                        .sb-shop-product-card {
                                            position: relative;
                                            width: 100%;
                                            display: grid;
                                            grid-template-columns: 68px minmax(0,1fr);
                                            gap: 12px;
                                            padding: 11px;
                                            overflow: hidden;
                                            border: 1px solid #dfcfbb;
                                            border-radius: 14px;
                                            background: rgba(255,255,255,.8);
                                            color: inherit;
                                            text-align: left;
                                            cursor: pointer;
                                            box-shadow: 0 2px 6px rgba(75,44,30,.025);
                                            transition: transform .16s ease, border-color .16s ease, background .16s ease, box-shadow .16s ease;
                                        }

                                        .sb-shop-product-card::before {
                                            content: "";
                                            position: absolute;
                                            inset: 0 auto 0 0;
                                            width: 3px;
                                            background: transparent;
                                            transition: background .16s ease;
                                        }

                                        .sb-shop-product-card:hover {
                                            transform: translateY(-1px);
                                            border-color: #c7a687;
                                            background: #fffdfa;
                                            box-shadow: 0 8px 18px rgba(76,44,30,.07);
                                        }

                                        .sb-shop-product-card.is-active {
                                            border-color: #a96c4d;
                                            background: linear-gradient(135deg, #fffdfa 0%, #fff7ed 100%);
                                            box-shadow: 0 10px 24px rgba(83,21,15,.09);
                                        }

                                        .sb-shop-product-card.is-active::before {
                                            background: #6b1d14;
                                        }

                                        .sb-shop-product-thumb {
                                            position: relative;
                                            width: 68px;
                                            height: 68px;
                                            display: grid;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #dac7ad;
                                            border-radius: 12px;
                                            background: linear-gradient(145deg, #eee2d3, #fff);
                                        }

                                        .sb-shop-product-thumb img {
                                            width: 100%;
                                            height: 100%;
                                            display: block;
                                            object-fit: cover;
                                        }

                                        .sb-shop-product-thumb-empty {
                                            color: #b09278;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 24px;
                                        }

                                        .sb-shop-product-content {
                                            min-width: 0;
                                        }

                                        .sb-shop-product-row {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 8px;
                                        }

                                        .sb-shop-product-name {
                                            margin: 0;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 16px;
                                            font-weight: 900;
                                            line-height: 1.15;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-product-stock {
                                            flex-shrink: 0;
                                            display: inline-flex;
                                            align-items: center;
                                            min-height: 22px;
                                            padding: 0 7px;
                                            border-radius: 999px;
                                            background: #eee1d1;
                                            color: #7a4e38;
                                            font-size: 7px;
                                            font-weight: 900;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-product-stock.is-empty {
                                            background: #f1dddd;
                                            color: #963d36;
                                        }

                                        .sb-shop-product-kind {
                                            margin: 3px 0 0;
                                            overflow: hidden;
                                            color: #9b7a61;
                                            font-size: 8px;
                                            font-weight: 800;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-product-preview {
                                            display: -webkit-box;
                                            overflow: hidden;
                                            margin: 7px 0 0;
                                            color: #745b49;
                                            font-size: 9px;
                                            line-height: 1.35;
                                            -webkit-box-orient: vertical;
                                            -webkit-line-clamp: 2;
                                        }

                                        .sb-shop-product-footer {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            gap: 5px 10px;
                                            margin-top: 8px;
                                        }

                                        .sb-shop-product-price {
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-product-sale-count {
                                            color: #927762;
                                            font-size: 8px;
                                            font-weight: 700;
                                        }

                                        .sb-shop-product-mini-badge {
                                            display: inline-flex;
                                            align-items: center;
                                            min-height: 19px;
                                            padding: 0 6px;
                                            border: 1px solid #e0cfba;
                                            border-radius: 999px;
                                            background: #f8f1e7;
                                            color: #76553f;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .02em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-product-detail {
                                            min-width: 0;
                                            padding: 19px;
                                            background:
                                                radial-gradient(circle at 100% 0, rgba(107,29,20,.035), transparent 34%),
                                                #fffdfa;
                                        }

                                        .sb-shop-detail-shell {
                                            animation: sbShopDetailIn .18s ease;
                                        }

                                        @keyframes sbShopDetailIn {
                                            from {
                                                opacity: 0;
                                                transform: translateY(3px);
                                            }
                                            to {
                                                opacity: 1;
                                                transform: translateY(0);
                                            }
                                        }

                                        .sb-shop-detail-hero {
                                            display: grid;
                                            grid-template-columns: 138px minmax(0,1fr);
                                            gap: 20px;
                                            align-items: start;
                                        }

                                        .sb-shop-detail-image {
                                            position: relative;
                                            width: 138px;
                                            height: 138px;
                                            display: grid;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #d3bfa4;
                                            border-radius: 18px;
                                            background: linear-gradient(145deg, #eee0ce, #fffdfa);
                                            box-shadow: 0 10px 24px rgba(76,44,30,.09);
                                        }

                                        .sb-shop-detail-image img {
                                            width: 100%;
                                            height: 100%;
                                            display: block;
                                            object-fit: cover;
                                        }

                                        .sb-shop-detail-image-empty {
                                            color: #ae8d72;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 35px;
                                        }

                                        .sb-shop-detail-heading {
                                            min-width: 0;
                                        }

                                        .sb-shop-detail-heading-top {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 12px;
                                        }

                                        .sb-shop-detail-eyebrow {
                                            margin: 2px 0 4px;
                                            color: #a27b5d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .16em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-title {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: clamp(24px,2.3vw,31px);
                                            font-weight: 900;
                                            line-height: 1.04;
                                        }

                                        .sb-shop-detail-close {
                                            width: 32px;
                                            height: 32px;
                                            display: grid;
                                            flex-shrink: 0;
                                            place-items: center;
                                            border: 1px solid #decdb8;
                                            border-radius: 10px;
                                            background: rgba(255,255,255,.75);
                                            color: #80624d;
                                            font-size: 15px;
                                            font-weight: 900;
                                            transition: background .15s ease, color .15s ease;
                                        }

                                        .sb-shop-detail-close:hover {
                                            background: #f2e7da;
                                            color: #53150f;
                                        }

                                        .sb-shop-detail-kind {
                                            margin: 6px 0 0;
                                            color: #92725a;
                                            font-size: 9px;
                                            font-weight: 800;
                                        }

                                        .sb-shop-detail-badges {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 6px;
                                            margin-top: 11px;
                                        }

                                        .sb-shop-detail-badge {
                                            display: inline-flex;
                                            align-items: center;
                                            min-height: 24px;
                                            padding: 0 8px;
                                            border: 1px solid #ddc9ae;
                                            border-radius: 999px;
                                            background: #f7eee2;
                                            color: #79513b;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-badge.is-magical {
                                            border-color: #cbb8d9;
                                            background: #f3ecf8;
                                            color: #69437f;
                                        }

                                        .sb-shop-detail-badge.is-danger {
                                            border-color: #e4b8b3;
                                            background: #fae9e7;
                                            color: #983e37;
                                        }

                                        .sb-shop-detail-commerce {
                                            display: grid;
                                            grid-template-columns: repeat(3,minmax(0,1fr));
                                            gap: 8px;
                                            margin-top: 16px;
                                        }

                                        .sb-shop-detail-commerce-card {
                                            min-width: 0;
                                            padding: 10px 11px;
                                            border: 1px solid #dfcfbb;
                                            border-radius: 11px;
                                            background: rgba(255,255,255,.72);
                                        }

                                        .sb-shop-detail-commerce-card span {
                                            display: block;
                                            color: #a08772;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .1em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-commerce-card strong {
                                            display: block;
                                            margin-top: 3px;
                                            overflow: hidden;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                            line-height: 1.2;
                                            text-overflow: ellipsis;
                                        }

                                        .sb-shop-detail-section {
                                            margin-top: 14px;
                                            overflow: hidden;
                                            border: 1px solid #dfcfbb;
                                            border-radius: 14px;
                                            background: rgba(255,255,255,.68);
                                        }

                                        .sb-shop-detail-section-head {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 10px;
                                            padding: 10px 13px;
                                            border-bottom: 1px solid #eee2d3;
                                            background: linear-gradient(180deg, #fffefa, #faf5ee);
                                        }

                                        .sb-shop-detail-section-head h6 {
                                            margin: 0;
                                            color: #6b1d14;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .12em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-section-body {
                                            padding: 13px;
                                        }

                                        .sb-shop-detail-description {
                                            margin: 0;
                                            color: #60483a;
                                            font-size: 11px;
                                            line-height: 1.7;
                                            white-space: pre-line;
                                        }

                                        .sb-shop-detail-stats {
                                            display: grid;
                                            grid-template-columns: repeat(3,minmax(0,1fr));
                                            gap: 8px;
                                        }

                                        .sb-shop-detail-stat {
                                            min-width: 0;
                                            padding: 10px 11px;
                                            border: 1px solid #eadccb;
                                            border-radius: 10px;
                                            background: #fbf7f1;
                                        }

                                        .sb-shop-detail-stat span {
                                            display: block;
                                            color: #9d816c;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-stat strong {
                                            display: block;
                                            margin-top: 3px;
                                            color: #53150f;
                                            font-size: 10px;
                                            font-weight: 900;
                                            line-height: 1.35;
                                            overflow-wrap: anywhere;
                                        }

                                        .sb-shop-detail-rules {
                                            display: grid;
                                            gap: 8px;
                                        }

                                        .sb-shop-detail-rule {
                                            position: relative;
                                            padding: 11px 12px 11px 15px;
                                            border: 1px solid #e7d8c4;
                                            border-radius: 11px;
                                            background: linear-gradient(135deg, #fffdfa, #faf4eb);
                                        }

                                        .sb-shop-detail-rule::before {
                                            content: "";
                                            position: absolute;
                                            top: 10px;
                                            bottom: 10px;
                                            left: 0;
                                            width: 3px;
                                            border-radius: 0 3px 3px 0;
                                            background: #8b3a28;
                                        }

                                        .sb-shop-detail-rule strong {
                                            display: block;
                                            color: #6b1d14;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 11px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-detail-rule p {
                                            margin: 4px 0 0;
                                            color: #705746;
                                            font-size: 9.5px;
                                            line-height: 1.55;
                                            white-space: pre-line;
                                        }

                                        .sb-shop-detail-actions {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: center;
                                            justify-content: flex-end;
                                            gap: 8px;
                                            margin-top: 14px;
                                            padding-top: 14px;
                                            border-top: 1px solid #eadcca;
                                        }

                                        .sb-shop-detail-action {
                                            min-height: 36px;
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            padding: 0 13px;
                                            border: 1px solid #d5bea4;
                                            border-radius: 10px;
                                            background: #fffdfa;
                                            color: #6b1d14;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            text-transform: uppercase;
                                            transition: border-color .15s ease, background .15s ease, color .15s ease;
                                        }

                                        .sb-shop-detail-action:hover {
                                            border-color: #b88d69;
                                            background: #f7eee3;
                                        }

                                        .sb-shop-detail-action.is-primary {
                                            border-color: #6b1d14;
                                            background: #6b1d14;
                                            color: #fff;
                                        }

                                        .sb-shop-detail-action.is-primary:hover {
                                            background: #53150f;
                                        }

                                        .sb-shop-detail-action.is-danger {
                                            border-color: transparent;
                                            background: transparent;
                                            color: #b23d35;
                                        }

                                        .sb-shop-detail-action.is-danger:hover {
                                            background: #faecea;
                                        }

                                        .sb-shop-detail-price-editor {
                                            margin-top: 12px;
                                            padding: 13px;
                                            border: 1px solid #d9b58f;
                                            border-radius: 13px;
                                            background: linear-gradient(145deg, #fff9f0, #fffdfa);
                                        }

                                        .sb-shop-detail-price-editor-title {
                                            margin: 0;
                                            color: #8c6239;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .1em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-price-grid {
                                            display: grid;
                                            grid-template-columns: repeat(5,minmax(0,1fr));
                                            gap: 7px;
                                            margin-top: 10px;
                                        }

                                        .sb-shop-detail-price-field span {
                                            display: block;
                                            margin-bottom: 4px;
                                            color: #9b7b62;
                                            font-size: 7px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-detail-price-field input {
                                            width: 100%;
                                            height: 38px;
                                            padding: 0 8px;
                                            border: 1px solid #d8c6ad;
                                            border-radius: 9px;
                                            outline: none;
                                            background: white;
                                            color: #53150f;
                                            font-size: 10px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-detail-price-field input:focus {
                                            border-color: #9a6547;
                                            box-shadow: 0 0 0 3px rgba(107,29,20,.06);
                                        }

                                        .sb-shop-detail-price-actions {
                                            display: flex;
                                            justify-content: flex-end;
                                            gap: 7px;
                                            margin-top: 10px;
                                        }

                                        .sb-shop-detail-empty {
                                            min-height: 470px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            padding: 35px;
                                            text-align: center;
                                        }

                                        .sb-shop-detail-empty-icon {
                                            width: 62px;
                                            height: 62px;
                                            display: grid;
                                            place-items: center;
                                            margin: 0 auto 13px;
                                            border: 1px solid #dfcfba;
                                            border-radius: 18px;
                                            background: #f5ecdf;
                                            color: #a17f64;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 26px;
                                        }

                                        .sb-shop-detail-empty h6 {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 19px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-detail-empty p {
                                            max-width: 330px;
                                            margin: 6px auto 0;
                                            color: #927762;
                                            font-size: 9.5px;
                                            line-height: 1.55;
                                        }

                                        .sb-shop-product-empty {
                                            padding: 35px 20px;
                                            border: 1px dashed #d8c5aa;
                                            border-radius: 13px;
                                            background: rgba(255,255,255,.45);
                                            text-align: center;
                                        }

                                        .sb-shop-product-empty strong {
                                            display: block;
                                            color: #6b1d14;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-product-empty p {
                                            margin: 5px 0 0;
                                            color: #997d67;
                                            font-size: 8px;
                                            line-height: 1.5;
                                        }

                                        .sb-master-shop-overview {
                                            box-shadow: 0 12px 32px rgba(76,44,30,.065);
                                        }

                                        .sb-master-shop-tab {
                                            letter-spacing: .055em;
                                        }

                                        .sb-master-shop-tab.is-active {
                                            box-shadow: 0 5px 14px rgba(83,21,15,.17);
                                        }

                                        @media (max-width: 1150px) {
                                            .sb-shop-catalog-layout {
                                                grid-template-columns: minmax(260px,.78fr) minmax(370px,1.22fr);
                                            }

                                            .sb-shop-detail-hero {
                                                grid-template-columns: 110px minmax(0,1fr);
                                            }

                                            .sb-shop-detail-image {
                                                width: 110px;
                                                height: 110px;
                                            }

                                            .sb-shop-detail-stats {
                                                grid-template-columns: repeat(2,minmax(0,1fr));
                                            }
                                        }

                                        @media (max-width: 900px) {
                                            .sb-shop-catalog-head {
                                                flex-direction: column;
                                            }

                                            .sb-shop-catalog-toolbar {
                                                width: 100%;
                                            }

                                            .sb-shop-catalog-search-wrap {
                                                flex: 1;
                                                width: auto;
                                            }

                                            .sb-shop-catalog-layout {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-shop-product-list {
                                                border-right: 0;
                                                border-bottom: 1px solid #e5d5c1;
                                            }

                                            .sb-shop-product-list-scroll {
                                                max-height: 380px;
                                            }

                                            .sb-shop-detail-empty {
                                                min-height: 300px;
                                            }
                                        }

                                        @media (max-width: 620px) {
                                            .sb-shop-catalog-head {
                                                padding: 17px;
                                            }

                                            .sb-shop-catalog-toolbar {
                                                align-items: stretch;
                                                flex-direction: column;
                                            }

                                            .sb-shop-catalog-summary {
                                                padding: 9px 12px;
                                            }

                                            .sb-shop-catalog-filter {
                                                padding: 0 8px;
                                            }

                                            .sb-shop-product-list {
                                                padding: 11px;
                                            }

                                            .sb-shop-product-detail {
                                                padding: 14px;
                                            }

                                            .sb-shop-detail-hero {
                                                grid-template-columns: 80px minmax(0,1fr);
                                                gap: 13px;
                                            }

                                            .sb-shop-detail-image {
                                                width: 80px;
                                                height: 80px;
                                                border-radius: 14px;
                                            }

                                            .sb-shop-detail-title {
                                                font-size: 21px;
                                            }

                                            .sb-shop-detail-commerce {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-shop-detail-stats {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-shop-detail-price-grid {
                                                grid-template-columns: repeat(2,minmax(0,1fr));
                                            }
                                        }

                                        /* ============================================================
                                           MASTER SHOPS V7 — BACKOFFICE + WORKSPACE FOLDERS
                                        ============================================================ */
                                        .sb-shop-folder-create{display:inline-flex;min-height:31px;align-items:center;gap:6px;border:1px solid #d8c7ab;border-radius:9px;background:#fffdf8;padding:0 10px;color:#6b1d14;font-size:8px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;transition:.15s ease}
                                        .sb-shop-folder-create:hover{border-color:#b08c62;background:#f5eee4}
                                        .sb-shop-folder-list{display:grid;gap:8px;margin-top:10px}
                                        .sb-shop-folder{overflow:hidden;border:1px solid #d8c7ab;border-radius:14px;background:rgba(255,253,248,.76);transition:border-color .15s ease,box-shadow .15s ease}
                                        .sb-shop-folder.is-drop-target{border-color:var(--folder-color,#8A4B20);box-shadow:0 0 0 3px color-mix(in srgb,var(--folder-color,#8A4B20) 12%,transparent)}
                                        .sb-shop-folder__head{display:grid;grid-template-columns:35px minmax(0,1fr) auto auto;gap:8px;align-items:center;min-height:52px;padding:7px 8px;background:linear-gradient(90deg,color-mix(in srgb,var(--folder-color,#8A4B20) 10%,#fffdf8),#fffdf8 72%)}
                                        .sb-shop-folder__cover{display:grid;width:35px;height:35px;place-items:center;overflow:hidden;border:1px solid color-mix(in srgb,var(--folder-color,#8A4B20) 35%,#d8c7ab);border-radius:9px;background:color-mix(in srgb,var(--folder-color,#8A4B20) 10%,#f7f2e9);color:var(--folder-color,#8A4B20)}
                                        .sb-shop-folder__cover img{width:100%;height:100%;object-fit:cover}
                                        .sb-shop-folder__copy{min-width:0;text-align:left}.sb-shop-folder__copy strong{display:block;overflow:hidden;color:var(--folder-text-color,#53150f);font-family:Georgia,serif;font-size:13px;line-height:1.05;text-overflow:ellipsis;white-space:nowrap}.sb-shop-folder__copy span{display:block;overflow:hidden;margin-top:3px;color:#8c806f;font-size:8px;line-height:1.25;text-overflow:ellipsis;white-space:nowrap}
                                        .sb-shop-folder__count,.sb-shop-folder__toggle,.sb-shop-folder__menu{display:inline-flex;align-items:center;justify-content:center;border:1px solid #e0d2bd;background:#fffdf8;color:#76553f}.sb-shop-folder__count{min-width:25px;height:25px;border-radius:999px;padding:0 7px;font-size:8px;font-weight:900}.sb-shop-folder__toggle,.sb-shop-folder__menu{width:27px;height:27px;border-radius:8px;font-size:11px;font-weight:900}
                                        .sb-shop-folder__body{display:grid;gap:6px;border-top:1px solid #eadfce;padding:7px}.sb-shop-folder__empty{border:1px dashed #d8c7ab;border-radius:10px;padding:11px 8px;color:#9a806d;font-size:8px;font-weight:800;text-align:center}
                                        .sb-shop-side-card{position:relative;display:grid;grid-template-columns:8px minmax(0,1fr) auto;gap:8px;align-items:center;min-height:48px;border:1px solid #e0d2bd;border-radius:11px;background:#fffdf8;padding:7px 8px;cursor:grab;transition:.15s ease}.sb-shop-side-card:hover{border-color:#b08c62;transform:translateX(1px)}.sb-shop-side-card.is-active{border-color:#8a4b20;box-shadow:inset 3px 0 0 #8a4b20}.sb-shop-side-card.is-dragging{opacity:.45}
                                        .sb-shop-side-card__status{width:8px;height:8px;border-radius:999px;background:#a58f7a}.sb-shop-side-card__status.is-open{background:#168661;box-shadow:0 0 0 3px rgba(22,134,97,.10)}.sb-shop-side-card__copy{min-width:0;text-align:left}.sb-shop-side-card__copy strong{display:block;overflow:hidden;color:#53150f;font-family:Georgia,serif;font-size:12px;line-height:1.05;text-overflow:ellipsis;white-space:nowrap}.sb-shop-side-card__copy span{display:block;margin-top:3px;color:#8c806f;font-size:7.5px;line-height:1.2}.sb-shop-side-card__menu{display:grid;width:27px;height:27px;place-items:center;border-radius:8px;color:#8c6239;font-size:14px;font-weight:900}.sb-shop-side-card__menu:hover{background:#f1e8db;color:#53150f}
                                        .sb-shop-unfiled{border:1px dashed #cdbb9f;border-radius:13px;background:rgba(250,247,241,.64);padding:7px}.sb-shop-unfiled.is-drop-target{border-color:#8a4b20;background:#fff9f2}.sb-shop-unfiled__head{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:3px 3px 7px}.sb-shop-unfiled__head strong{color:#76553f;font-size:8px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
                                        .sb-folder-modal-layer{position:fixed;inset:0;z-index:290;display:grid;place-items:center;padding:12px}.sb-folder-modal-backdrop{position:absolute;inset:0;background:rgba(24,18,14,.72);backdrop-filter:blur(6px)}.sb-folder-modal{position:relative;z-index:1;display:flex;width:min(760px,100%);height:min(680px,calc(100vh - 24px));height:min(680px,calc(100dvh - 24px));max-height:none;flex-direction:column;overflow:hidden;border:1px solid #cdbb9f;border-radius:20px;background:#fffdf8;box-shadow:0 24px 70px rgba(0,0,0,.30)}.sb-folder-modal__head,.sb-folder-modal__footer{display:flex;flex:0 0 auto;align-items:center;justify-content:space-between;gap:14px;background:#f1e4d2;padding:13px 16px}.sb-folder-modal__head{border-bottom:1px solid #d8c7ab}.sb-folder-modal__footer{position:sticky;bottom:0;z-index:3;justify-content:flex-end;border-top:1px solid #d8c7ab;box-shadow:0 -8px 18px rgba(83,21,15,.045)}.sb-folder-modal__body{display:grid;min-height:0;flex:1 1 auto;grid-template-columns:190px minmax(0,1fr);gap:18px;overflow-x:hidden;overflow-y:auto;overscroll-behavior:contain;padding:18px;scrollbar-gutter:stable}.sb-folder-cover-editor{display:grid;align-content:start;gap:9px}.sb-folder-cover-preview{display:grid;width:100%;aspect-ratio:1/1;place-items:center;overflow:hidden;border:1px solid #d8c7ab;border-radius:16px;background:#f5eee4;color:#8c6239}.sb-folder-cover-preview img{width:100%;height:100%;object-fit:cover}.sb-folder-form-grid{display:grid;align-content:start;gap:12px}.sb-folder-field{display:grid;gap:5px}.sb-folder-field>span{color:#8c6239;font-size:8px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.sb-folder-field input,.sb-folder-field textarea{width:100%;border:1px solid #d8c7ab;border-radius:10px;background:#fff;padding:10px 11px;color:#53150f;font-size:11px;outline:none}.sb-folder-field textarea{min-height:82px;resize:vertical}
                                        .sb-master-shop-overview{padding:0!important;overflow:hidden}.sb-master-shop-overview__inner{padding:13px 15px!important}.sb-master-shop-overview__top{align-items:center!important;gap:14px!important}.sb-master-shop-avatar{width:58px!important;height:58px!important;flex-basis:58px!important;border-radius:14px!important}.sb-master-shop-kicker{display:none!important}.sb-master-shop-title{font-size:25px!important}.sb-master-shop-description{max-width:650px!important;margin-top:4px!important;font-size:9px!important;-webkit-line-clamp:1!important}.sb-master-shop-metrics{display:none!important}
                                        .sb-master-shop-tabs{display:inline-flex!important;width:auto!important;gap:4px!important;margin:8px 0 10px!important;border:1px solid #d8c7ab!important;border-radius:12px!important;background:#eee6da!important;padding:4px!important}.sb-master-shop-tab{min-width:116px!important;min-height:35px!important;border:0!important;border-radius:8px!important;background:transparent!important;padding:0 12px!important}.sb-master-shop-tab.is-active{background:#fffdf8!important;box-shadow:0 2px 8px rgba(83,21,15,.08)!important}
                                        .sb-admin-stock{overflow:hidden;border:1px solid #d8c7ab;border-radius:16px;background:#fffdf8}.sb-admin-stock__top{display:flex;align-items:center;justify-content:space-between;gap:14px;border-bottom:1px solid #eadfce;padding:10px 12px}.sb-admin-stock__top-copy strong{display:block;color:#53150f;font-family:Georgia,serif;font-size:17px}.sb-admin-stock__top-copy span{display:block;margin-top:2px;color:#8c806f;font-size:8px}
                                        .sb-admin-category-list{display:grid;gap:7px;padding:9px}.sb-admin-category{overflow:hidden;border:1px solid #e0d2bd;border-radius:13px;background:#faf7f1}.sb-admin-category__head{display:grid;width:100%;grid-template-columns:34px minmax(0,1fr) auto auto;gap:9px;align-items:center;border:0;background:transparent;padding:9px 10px;text-align:left}.sb-admin-category__icon{display:grid;width:34px;height:34px;place-items:center;border:1px solid #d8c7ab;border-radius:9px;background:#fffdf8;color:#8a4b20;font-size:15px}.sb-admin-category__copy strong{display:block;color:#53150f;font-family:Georgia,serif;font-size:15px}.sb-admin-category__copy span{display:block;margin-top:2px;color:#8c806f;font-size:8px}.sb-admin-category__count,.sb-admin-category__chevron{display:grid;height:26px;place-items:center;border:1px solid #e0d2bd;background:#fffdf8;color:#76553f}.sb-admin-category__count{min-width:26px;border-radius:999px;padding:0 7px;font-size:8px;font-weight:900}.sb-admin-category__chevron{width:26px;border-radius:8px;transition:transform .15s ease}.sb-admin-category.is-open .sb-admin-category__chevron{transform:rotate(180deg)}.sb-admin-category__body{border-top:1px solid #eadfce;padding:10px}
                                        .sb-admin-product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(215px,1fr));gap:10px}.sb-admin-product{display:flex;min-width:0;flex-direction:column;overflow:hidden;border:1px solid #d8c7ab;border-radius:13px;background:#fffdf8;box-shadow:0 5px 14px rgba(83,21,15,.045);transition:transform .15s ease,border-color .15s ease,box-shadow .15s ease}.sb-admin-product:hover{transform:translateY(-2px);border-color:#b08c62;box-shadow:0 8px 20px rgba(83,21,15,.08)}.sb-admin-product.is-soldout{opacity:.70;filter:grayscale(.28)}.sb-admin-product__button{display:grid;grid-template-columns:72px minmax(0,1fr);gap:10px;width:100%;border:0;background:transparent;padding:9px;text-align:left}.sb-admin-product__image{position:relative;display:grid;width:72px;height:72px;place-items:center;overflow:hidden;border:1px solid #e0d2bd;border-radius:11px;background:#f1e8db}.sb-admin-product__image img{width:100%;height:100%;object-fit:contain;padding:5px}.sb-admin-product__stock{position:absolute;right:5px;bottom:5px;border:1px solid #d8c7ab;border-radius:999px;background:rgba(255,253,248,.94);padding:3px 6px;color:#76553f;font-size:7px;font-weight:900}.sb-admin-product__copy{min-width:0}.sb-admin-product__kind{color:#9a806d;font-size:7px;font-weight:900;letter-spacing:.07em;text-transform:uppercase}.sb-admin-product__copy h6{overflow:hidden;margin-top:3px;color:#53150f;font-family:Georgia,serif;font-size:15px;font-weight:900;line-height:1.08;text-overflow:ellipsis;white-space:nowrap}.sb-admin-product__copy p{display:-webkit-box;overflow:hidden;margin-top:5px;color:#76553f;font-size:8px;line-height:1.35;-webkit-box-orient:vertical;-webkit-line-clamp:2}.sb-admin-product__footer{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;align-items:center;margin-top:auto;border-top:1px solid #eadfce;background:#f8f2e9;padding:8px 9px}.sb-admin-product__price span{display:block;color:#9a806d;font-size:6.5px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.sb-admin-product__price strong{display:block;margin-top:2px;color:#53150f;font-family:Georgia,serif;font-size:14px}.sb-admin-product__quick{display:flex;gap:5px}.sb-admin-product__quick button{min-height:29px;border:1px solid #d8c7ab;border-radius:8px;background:#fffdf8;padding:0 8px;color:#6b1d14;font-size:7px;font-weight:900;text-transform:uppercase}
                                        .sb-admin-product-modal-layer{position:fixed;inset:0;z-index:292;display:grid;place-items:center;padding:18px}.sb-admin-product-modal-backdrop{position:absolute;inset:0;background:rgba(20,15,12,.76);backdrop-filter:blur(7px)}.sb-admin-product-modal{position:relative;z-index:1;display:flex;width:min(900px,100%);max-height:min(88vh,760px);flex-direction:column;overflow:hidden;border:1px solid #cdbb9f;border-radius:19px;background:#fffdf8;box-shadow:0 25px 70px rgba(0,0,0,.32)}.sb-admin-product-modal__head{display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #d8c7ab;background:#f1e4d2;padding:11px 14px}.sb-admin-product-modal__body{overflow-y:auto;padding:18px 20px}.sb-admin-product-modal__hero{display:grid;grid-template-columns:150px minmax(0,1fr);gap:18px;align-items:start}.sb-admin-product-modal__image{display:grid;width:150px;height:150px;place-items:center;overflow:hidden;border:1px solid #d8c7ab;border-radius:14px;background:#f5eee4}.sb-admin-product-modal__image img{width:100%;height:100%;object-fit:contain;padding:8px}.sb-admin-product-modal__classification{color:#9a806d;font-size:8px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.sb-admin-product-modal__title{margin-top:4px;color:#53150f;font-family:Georgia,serif;font-size:27px;font-weight:900;line-height:1.05}.sb-admin-product-modal__commerce{display:flex;flex-wrap:wrap;gap:7px;margin-top:9px}.sb-admin-product-modal__pill{border:1px solid #d8c7ab;border-radius:999px;background:#faf7f1;padding:5px 9px;color:#76553f;font-size:8px;font-weight:900}.sb-admin-product-section{margin-top:18px;border-top:1px solid #d8c7ab;padding-top:13px}.sb-admin-product-section__title{color:#8c6239;font-size:8px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}.sb-admin-product-section__text{margin-top:8px;color:#5f4638;font-size:11px;line-height:1.65}.sb-admin-product-rule{padding:10px 0}.sb-admin-product-rule+.sb-admin-product-rule{border-top:1px solid #eadfce}.sb-admin-product-rule strong{color:#53150f;font-family:Georgia,serif;font-size:14px}.sb-admin-product-rule p{margin-top:5px;color:#5f4638;font-size:10px;line-height:1.55}.sb-admin-product-modal__footer{display:flex;align-items:center;justify-content:space-between;gap:12px;border-top:1px solid #d8c7ab;background:#f1e4d2;padding:11px 14px}
                                        @media(max-width:900px){.sb-folder-modal-layer{padding:8px}.sb-folder-modal{width:min(680px,100%);height:calc(100vh - 16px);height:calc(100dvh - 16px);border-radius:16px}.sb-folder-modal__body{grid-template-columns:1fr;padding:14px}.sb-folder-cover-editor{grid-template-columns:140px minmax(0,1fr);align-items:start}.sb-folder-cover-preview{width:140px}.sb-folder-modal__footer{padding:10px 12px}.sb-admin-product-modal__hero{grid-template-columns:110px minmax(0,1fr)}.sb-admin-product-modal__image{width:110px;height:110px}}@media(max-height:760px){.sb-folder-modal-layer{padding:8px}.sb-folder-modal{height:calc(100vh - 16px);height:calc(100dvh - 16px);border-radius:16px}.sb-folder-modal__head{padding:9px 13px}.sb-folder-modal__head h4{font-size:18px!important}.sb-folder-modal__body{gap:12px;padding:12px 14px}.sb-folder-cover-editor{gap:6px}.sb-folder-form-grid{gap:8px}.sb-folder-field{gap:3px}.sb-folder-field input,.sb-folder-field textarea{padding:8px 9px}.sb-folder-field textarea{min-height:64px}.sb-folder-modal__footer{padding:9px 12px}}@media(max-height:620px){.sb-folder-modal__body{grid-template-columns:150px minmax(0,1fr);gap:10px;padding:10px 12px}.sb-folder-cover-preview{width:100%;max-height:150px;aspect-ratio:1/1}.sb-folder-field textarea{min-height:52px}.sb-folder-modal__footer button{min-height:36px!important}}

                                    
                                        /* ============================================================
                                           MASTER SHOPS V9 — DENSIDADE, LEITURA E ACESSO RÁPIDO
                                        ============================================================ */
                                        .sb-master-loot-heading{min-height:36px!important;gap:8px!important}
                                        .sb-master-loot-heading__icon{width:32px!important;height:32px!important;flex-basis:32px!important;border-radius:9px!important}
                                        .sb-master-loot-heading__icon svg{width:15px!important;height:15px!important}

                                        .sb-shop-workspace{grid-template-columns:300px minmax(0,1fr)!important;gap:12px!important}
                                        .sb-shop-workspace-sidebar{padding:9px!important}
                                        .sb-loot-mode-switch>button{min-height:32px!important;font-size:8.5px!important}

                                        .sb-shop-organizer-panel{margin-top:8px!important}
                                        .sb-shop-organizer-head{align-items:center!important;padding:1px!important}
                                        .sb-shop-organizer-title h4{margin-top:2px;color:#53150f;font-family:Georgia,serif;font-size:15px;font-weight:900;line-height:1}
                                        .sb-shop-organizer-eyebrow{font-size:6.5px!important}

                                        .sb-shop-folder-create-compact{display:inline-flex;min-height:31px;align-items:center;gap:6px;border:1px solid #8a4b20;border-radius:9px;background:#7a251b;padding:0 10px;color:#fffaf2;font-size:7.5px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;box-shadow:0 4px 10px rgba(83,21,15,.08);transition:.15s ease}
                                        .sb-shop-folder-create-compact:hover{background:#681c14;transform:translateY(-1px)}
                                        .sb-shop-folder-create-compact:disabled{cursor:not-allowed;opacity:.45;transform:none}
                                        .sb-shop-folder-create-compact svg{width:13px;height:13px}

                                        .sb-shop-organizer-search{margin-top:8px!important;border-radius:9px!important;padding:0 9px!important}
                                        .sb-shop-organizer-search input{height:35px!important;font-size:10.5px!important}

                                        .sb-shop-folder-list{gap:6px!important;margin-top:8px!important;max-height:calc(100vh - 285px)!important;max-height:calc(100dvh - 285px)!important}
                                        .sb-shop-folder__head{grid-template-columns:minmax(0,1fr) 27px!important;padding:5px!important}
                                        .sb-shop-folder__main{grid-template-columns:32px minmax(0,1fr) auto 18px!important;gap:7px!important}
                                        .sb-shop-folder__cover{width:32px!important;height:32px!important;border-radius:8px!important}
                                        .sb-shop-folder__copy strong{font-size:13.5px!important;line-height:1.08!important}
                                        .sb-shop-folder__copy small{display:none!important}
                                        .sb-shop-folder__count{min-width:23px!important;height:23px!important;font-size:8px!important}

                                        .sb-shop-folder__body{gap:4px!important;padding:5px!important}
                                        .sb-shop-side-card{min-height:40px!important;border-radius:9px!important;padding:4px 4px 4px 8px!important}
                                        .sb-shop-side-card__copy strong{font-size:13px!important;line-height:1.08!important}
                                        .sb-shop-side-card__copy small{display:none!important}
                                        .sb-shop-side-card__main{gap:9px!important}
                                        .sb-shop-side-card__menu{width:26px!important;height:26px!important}

                                        .sb-shop-unfiled{padding:5px!important;background:#fbf7f1!important}
                                        .sb-shop-unfiled__head{padding:3px 3px 6px!important}
                                        .sb-shop-unfiled__head strong{margin:0!important;color:#76553f!important;font-size:9px!important;font-weight:900!important;letter-spacing:.05em!important;text-transform:uppercase!important}
                                        .sb-shop-folder__empty{padding:7px!important}

                                        .sb-master-shop-overview__inner{padding:9px 11px!important}
                                        .sb-master-shop-overview__top{gap:10px!important}
                                        .sb-master-shop-avatar{width:50px!important;height:50px!important;flex-basis:50px!important;border-radius:12px!important}
                                        .sb-master-shop-title{font-size:23px!important}
                                        .sb-master-shop-actions{gap:6px!important}
                                        .sb-master-shop-action{min-height:34px!important;padding:0 12px!important;font-size:7.5px!important}

                                        .sb-master-shop-tabs{margin:6px 0 10px!important;padding:3px!important}
                                        .sb-master-shop-tab{min-width:104px!important;min-height:32px!important;padding:0 10px!important;font-size:8px!important}

                                        .sb-admin-stock__top{padding:9px 11px!important}
                                        .sb-admin-stock__top-copy span{display:none!important}

                                        @media(max-width:1180px){
                                            .sb-shop-workspace{grid-template-columns:270px minmax(0,1fr)!important}
                                        }
                                        @media(max-width:900px){
                                            .sb-shop-workspace{grid-template-columns:1fr!important}
                                            .sb-shop-folder-list{max-height:360px!important}
                                        }

                                        /* ============================================================
                                           MASTER SHOP — VENDAS E FICHA EDITORIAL
                                        ============================================================ */

                                        .sb-master-sales {
                                            overflow: hidden;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 16px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8 0%,
                                                    #f8f2e8 100%
                                                );
                                            box-shadow: 0 10px 26px rgba(83,21,15,.045);
                                        }

                                        .sb-master-sales__head {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 14px;
                                            border-bottom: 1px solid #e7d9c6;
                                            padding: 12px 14px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffefa 0%,
                                                    #f8f1e8 100%
                                                );
                                        }

                                        .sb-master-sales__head > div > span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .12em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-sales__head h5 {
                                            margin: 2px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 19px;
                                            font-weight: 900;
                                        }

                                        .sb-master-sales__head > strong {
                                            display: inline-flex;
                                            min-height: 29px;
                                            align-items: center;
                                            border: 1px solid #ddcbb3;
                                            border-radius: 999px;
                                            background: #fffdf8;
                                            padding: 0 10px;
                                            color: #76553f;
                                            font-size: 8px;
                                            font-weight: 900;
                                        }

                                        .sb-master-sales__list {
                                            display: grid;
                                            gap: 8px;
                                            padding: 10px;
                                        }

                                        .sb-master-sale-card {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(210px, 1.2fr)
                                                24px
                                                minmax(190px, .95fr)
                                                minmax(120px, .65fr)
                                                minmax(110px, auto);
                                            gap: 12px;
                                            align-items: center;
                                            border: 1px solid #e0d2bd;
                                            border-radius: 13px;
                                            background: #fffdf8;
                                            padding: 9px 11px;
                                            cursor: pointer;
                                            box-shadow: 0 3px 10px rgba(83,21,15,.025);
                                            transition:
                                                transform .14s ease,
                                                border-color .14s ease,
                                                box-shadow .14s ease;
                                        }

                                        .sb-master-sale-card:hover {
                                            transform: translateY(-1px);
                                            border-color: #be9873;
                                            box-shadow: 0 8px 20px rgba(83,21,15,.07);
                                        }

                                        .sb-master-sale-item,
                                        .sb-master-sale-buyer {
                                            display: flex;
                                            min-width: 0;
                                            align-items: center;
                                            gap: 9px;
                                        }

                                        .sb-master-sale-item__image {
                                            display: grid;
                                            width: 54px;
                                            height: 54px;
                                            flex: 0 0 54px;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #ddcbb3;
                                            border-radius: 10px;
                                            background:
                                                linear-gradient(
                                                    145deg,
                                                    #f2e8da,
                                                    #fffdf8
                                                );
                                        }

                                        .sb-master-sale-item__image img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: contain;
                                            padding: 5px;
                                        }

                                        .sb-master-sale-item__copy,
                                        .sb-master-sale-buyer__copy {
                                            min-width: 0;
                                        }

                                        .sb-master-sale-item__copy > span,
                                        .sb-master-sale-buyer__copy > span,
                                        .sb-master-sale-meta span,
                                        .sb-master-sale-price span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-sale-item__copy h6,
                                        .sb-master-sale-buyer__copy h6 {
                                            overflow: hidden;
                                            margin: 2px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                            line-height: 1.12;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-master-sale-item__copy p,
                                        .sb-master-sale-buyer__copy p {
                                            overflow: hidden;
                                            margin: 3px 0 0;
                                            color: #806650;
                                            font-size: 8px;
                                            line-height: 1.25;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-master-sale-buyer__copy p strong {
                                            color: #5e3b2b;
                                            font-weight: 900;
                                        }

                                        .sb-master-sale-arrow {
                                            color: #b08c62;
                                            font-family: Georgia, serif;
                                            font-size: 20px;
                                            font-weight: 900;
                                            text-align: center;
                                        }

                                        .sb-master-sale-buyer__avatar {
                                            display: grid;
                                            width: 43px;
                                            height: 43px;
                                            flex: 0 0 43px;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #d7c3a8;
                                            border-radius: 50%;
                                            background: #efe4d7;
                                            color: #6b1d14;
                                            font-family: Georgia, serif;
                                            font-size: 15px;
                                            font-weight: 900;
                                        }

                                        .sb-master-sale-buyer__avatar img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: cover;
                                            object-position: top center;
                                        }

                                        .sb-master-sale-meta {
                                            display: grid;
                                            grid-template-columns: repeat(2, minmax(0, 1fr));
                                            gap: 7px;
                                        }

                                        .sb-master-sale-meta > div {
                                            min-width: 0;
                                            border-left: 1px solid #eadfce;
                                            padding-left: 9px;
                                        }

                                        .sb-master-sale-meta strong {
                                            display: block;
                                            margin-top: 3px;
                                            color: #5e3b2b;
                                            font-size: 9px;
                                            font-weight: 900;
                                            line-height: 1.25;
                                        }

                                        .sb-master-sale-price {
                                            border-left: 1px solid #eadfce;
                                            padding-left: 12px;
                                            text-align: right;
                                        }

                                        .sb-master-sale-price strong {
                                            display: block;
                                            margin-top: 4px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                            white-space: nowrap;
                                        }

                                        .sb-master-sales__empty {
                                            padding: 46px 20px;
                                            text-align: center;
                                        }

                                        .sb-master-sales__empty > div {
                                            margin: 0 auto;
                                            color: #b08c62;
                                            font-size: 28px;
                                        }

                                        .sb-master-sales__empty h6 {
                                            margin: 7px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 17px;
                                            font-weight: 900;
                                        }

                                        .sb-master-sales__empty p {
                                            max-width: 470px;
                                            margin: 5px auto 0;
                                            color: #8c6b54;
                                            font-size: 9px;
                                            line-height: 1.55;
                                        }

                                        .sb-master-item-detail-layer {
                                            position: fixed;
                                            inset: 0;
                                            z-index: 292;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            padding: 18px;
                                        }

                                        .sb-master-item-detail-backdrop {
                                            position: absolute;
                                            inset: 0;
                                            background: rgba(11,8,6,.76);
                                            backdrop-filter: blur(7px);
                                        }

                                        .sb-master-item-detail {
                                            position: relative;
                                            z-index: 1;
                                            display: flex;
                                            width: min(860px, 100%);
                                            max-height: calc(100dvh - 36px);
                                            flex-direction: column;
                                            overflow: hidden;
                                            border: 1px solid rgba(176,140,98,.58);
                                            border-radius: 16px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fbf8f1 0%,
                                                    #f8f2e8 100%
                                                );
                                            color: #4f3427;
                                            box-shadow:
                                                0 28px 80px rgba(43,29,23,.30),
                                                inset 0 1px 0 rgba(255,255,255,.82);
                                        }

                                        .sb-master-item-detail__topbar {
                                            display: flex;
                                            min-height: 52px;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            border-bottom: 1px solid rgba(160,119,77,.30);
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f1e5d6 0%,
                                                    #eadbc8 100%
                                                );
                                            padding: 9px 13px 9px 16px;
                                        }

                                        .sb-master-item-detail__topbar > span {
                                            color: #7d604d;
                                            font-size: 8.5px;
                                            font-weight: 900;
                                            letter-spacing: .13em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail__topbar > button {
                                            width: 34px;
                                            height: 34px;
                                            border: 0;
                                            border-radius: 9px;
                                            background: rgba(255,253,248,.52);
                                            color: #7d604d;
                                            font-size: 21px;
                                            line-height: 1;
                                        }

                                        .sb-master-item-detail__scroll {
                                            min-height: 0;
                                            flex: 1 1 auto;
                                            overflow-y: auto;
                                            padding: 24px 28px 28px;
                                            scrollbar-color: rgba(140,98,57,.42) transparent;
                                        }

                                        .sb-master-item-detail__hero {
                                            display: grid;
                                            grid-template-columns: 158px minmax(0,1fr);
                                            gap: 20px;
                                            align-items: center;
                                        }

                                        .sb-master-item-detail__image {
                                            display: flex;
                                            width: 158px;
                                            height: 158px;
                                            align-items: center;
                                            justify-content: center;
                                            overflow: hidden;
                                            border: 1px solid rgba(176,140,98,.42);
                                            border-radius: 12px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8 0%,
                                                    #f2eadf 100%
                                                );
                                            padding: 12px;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.76),
                                                0 5px 18px rgba(83,21,15,.05);
                                        }

                                        .sb-master-item-detail__image img {
                                            width: auto;
                                            height: auto;
                                            max-width: 100%;
                                            max-height: 100%;
                                            object-fit: contain;
                                        }

                                        .sb-master-item-detail__identity {
                                            align-self: stretch;
                                        }

                                        .sb-master-item-detail__title-row {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 12px;
                                        }

                                        .sb-master-item-detail__kicker {
                                            display: block;
                                            margin-bottom: 4px;
                                            color: rgba(140,98,57,.78);
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .12em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail__identity h2 {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 30px;
                                            font-weight: 900;
                                            line-height: 1.02;
                                        }

                                        .sb-master-item-detail__stock-status {
                                            display: inline-flex;
                                            min-height: 25px;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            gap: 6px;
                                            border: 1px solid rgba(107,29,20,.16);
                                            border-radius: 999px;
                                            background: rgba(107,29,20,.055);
                                            padding: 0 8px;
                                            color: #6b1d14;
                                            font-size: 7.5px;
                                            font-weight: 900;
                                            letter-spacing: .055em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail__stock-status.is-sold {
                                            border-color: rgba(125,96,77,.22);
                                            background: rgba(125,96,77,.08);
                                            color: #765b4b;
                                        }

                                        .sb-master-item-detail__stock-dot {
                                            width: 6px;
                                            height: 6px;
                                            border-radius: 50%;
                                            background: #6b1d14;
                                        }

                                        .sb-master-item-detail__stock-status.is-sold
                                        .sb-master-item-detail__stock-dot {
                                            background: #8d7868;
                                        }

                                        .sb-master-item-detail__classification {
                                            display: block;
                                            margin-top: 7px;
                                            color: #6b5548;
                                            font-size: 13px;
                                            font-weight: 700;
                                            font-style: italic;
                                            line-height: 1.4;
                                        }

                                        .sb-master-item-editorial {
                                            margin-top: 10px;
                                            color: #5f4636;
                                        }

                                        .sb-master-item-mechanic-line {
                                            margin-top: 4px;
                                            color: #6b5548;
                                            font-size: 10.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-master-item-mechanic-line > strong {
                                            margin-right: 4px;
                                            color: #8c6239;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .065em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-inline-tooltip {
                                            position: relative;
                                            display: inline;
                                        }

                                        .sb-master-item-inline-term {
                                            display: inline;
                                            padding: 0;
                                            border: 0;
                                            background: transparent;
                                            color: inherit;
                                            font: inherit;
                                            text-decoration: underline dotted rgba(140,98,57,.38);
                                            text-underline-offset: 2px;
                                            cursor: help;
                                        }

                                        .sb-master-item-inline-tooltip-panel {
                                            position: absolute;
                                            left: 0;
                                            bottom: calc(100% + 8px);
                                            z-index: 100;
                                            display: block;
                                            width: min(290px, 72vw);
                                            visibility: hidden;
                                            border: 1px solid rgba(176,140,98,.58);
                                            border-radius: 8px;
                                            background: #fbf8f1;
                                            padding: 9px 10px;
                                            opacity: 0;
                                            box-shadow: 0 10px 28px rgba(43,29,23,.18);
                                            pointer-events: none;
                                            transform: translateY(3px);
                                            transition: .12s ease;
                                            white-space: normal;
                                        }

                                        .sb-master-item-inline-tooltip:hover
                                        .sb-master-item-inline-tooltip-panel,
                                        .sb-master-item-inline-tooltip:focus-within
                                        .sb-master-item-inline-tooltip-panel {
                                            visibility: visible;
                                            opacity: 1;
                                            transform: translateY(0);
                                        }

                                        .sb-master-item-inline-tooltip-title {
                                            display: block;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-master-item-inline-tooltip-text {
                                            display: block;
                                            margin-top: 4px;
                                            color: #5f4636;
                                            font-size: 11px;
                                            line-height: 1.55;
                                        }

                                        .sb-master-item-detail-section,
                                        .sb-master-item-price-editor {
                                            margin-top: 24px;
                                        }

                                        .sb-master-item-detail-section__title {
                                            display: flex;
                                            align-items: center;
                                            gap: 10px;
                                            margin-bottom: 12px;
                                            color: #8c6239;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .095em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail-section__title::after {
                                            content: '';
                                            height: 1px;
                                            flex: 1 1 auto;
                                            background: rgba(176,140,98,.34);
                                        }

                                        .sb-master-item-detail-description {
                                            margin: 0;
                                            color: #4f3427;
                                            font-size: 14px;
                                            line-height: 1.78;
                                            white-space: pre-line;
                                        }

                                        .sb-master-item-custom-list,
                                        .sb-master-item-features {
                                            display: grid;
                                            gap: 10px;
                                        }

                                        .sb-master-item-custom-row {
                                            color: #5f3a27;
                                            font-size: 12px;
                                            line-height: 1.65;
                                        }

                                        .sb-master-item-custom-row__head {
                                            display: flex;
                                            flex-wrap: wrap;
                                            align-items: baseline;
                                            gap: 7px;
                                        }

                                        .sb-master-item-custom-row h3 {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 15px;
                                            font-weight: 900;
                                        }

                                        .sb-master-item-custom-row strong {
                                            color: #8c6239;
                                            font-size: 11px;
                                        }

                                        .sb-master-item-custom-row p {
                                            margin: 2px 0 0;
                                        }

                                        .sb-master-item-feature {
                                            overflow: hidden;
                                            border: 1px solid rgba(176,140,98,.34);
                                            border-radius: 11px;
                                            background: #fffdf8;
                                        }

                                        .sb-master-item-feature__head {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 10px;
                                            border-bottom: 1px solid rgba(188,154,111,.28);
                                            background: #f0e4d5;
                                            padding: 9px 11px;
                                        }

                                        .sb-master-item-feature h3 {
                                            margin: 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 17px;
                                            font-weight: 900;
                                        }

                                        .sb-master-item-feature__head span {
                                            color: #8c6239;
                                            font-size: 9px;
                                            font-weight: 900;
                                        }

                                        .sb-master-item-feature p {
                                            margin: 0;
                                            padding: 10px 12px 12px;
                                            color: #4f3427;
                                            font-size: 13px;
                                            line-height: 1.72;
                                            white-space: pre-line;
                                        }

                                        .sb-master-item-price-grid {
                                            display: grid;
                                            grid-template-columns: repeat(5, minmax(0,1fr));
                                            gap: 8px;
                                        }

                                        .sb-master-item-price-grid label {
                                            display: grid;
                                            gap: 4px;
                                        }

                                        .sb-master-item-price-grid label > span {
                                            color: #8c6239;
                                            font-size: 7px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-price-grid input {
                                            width: 100%;
                                            height: 36px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 8px;
                                            background: #fffdf8;
                                            padding: 0 7px;
                                            color: #53150f;
                                            font-size: 10px;
                                            font-weight: 900;
                                            text-align: center;
                                        }

                                        .sb-master-item-detail__footer {
                                            display: flex;
                                            min-height: 62px;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 12px;
                                            border-top: 1px solid rgba(160,119,77,.30);
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f1e5d6 0%,
                                                    #eadbc8 100%
                                                );
                                            padding: 10px 14px;
                                        }

                                        .sb-master-item-detail__footer-price span {
                                            display: block;
                                            color: #8c6239;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail__footer-price strong {
                                            display: block;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 18px;
                                            font-weight: 900;
                                        }

                                        .sb-master-item-detail__footer-actions {
                                            display: flex;
                                            flex-wrap: wrap;
                                            justify-content: flex-end;
                                            gap: 7px;
                                        }

                                        .sb-master-item-detail-action {
                                            min-height: 35px;
                                            border: 1px solid #d1bda2;
                                            border-radius: 9px;
                                            background: #fffdf8;
                                            padding: 0 12px;
                                            color: #6b1d14;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-detail-action.is-primary {
                                            border-color: #6b1d14;
                                            background: #6b1d14;
                                            color: white;
                                        }

                                        .sb-master-item-detail-action.is-danger {
                                            border-color: transparent;
                                            background: transparent;
                                            color: #b23d35;
                                        }

                                        @media (max-width: 1120px) {
                                            .sb-master-sale-card {
                                                grid-template-columns:
                                                    minmax(200px, 1.2fr)
                                                    20px
                                                    minmax(170px, 1fr)
                                                    minmax(105px, .7fr);
                                            }

                                            .sb-master-sale-price {
                                                grid-column: 1 / -1;
                                                border-top: 1px solid #eadfce;
                                                border-left: 0;
                                                padding-top: 8px;
                                                padding-left: 0;
                                                text-align: left;
                                            }
                                        }

                                        @media (max-width: 760px) {
                                            .sb-master-sale-card {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-master-sale-arrow {
                                                display: none;
                                            }

                                            .sb-master-sale-meta {
                                                border-top: 1px solid #eadfce;
                                                padding-top: 8px;
                                            }

                                            .sb-master-sale-price {
                                                grid-column: auto;
                                            }

                                            .sb-master-item-detail__hero {
                                                grid-template-columns: 110px minmax(0,1fr);
                                            }

                                            .sb-master-item-detail__image {
                                                width: 110px;
                                                height: 110px;
                                            }

                                            .sb-master-item-detail__identity h2 {
                                                font-size: 24px;
                                            }

                                            .sb-master-item-price-grid {
                                                grid-template-columns: repeat(3, minmax(0,1fr));
                                            }
                                        }


                                        /* ============================================================
                                           MODAL DO ITEM — ARMA / TOOLTIP GLOBAL
                                        ============================================================ */

                                        .sb-master-item-detail__hero {
                                            overflow: visible;
                                        }

                                        .sb-master-item-detail__identity {
                                            overflow: visible;
                                        }

                                        .sb-master-item-editorial {
                                            margin-top: 8px;
                                            overflow: visible;
                                        }

                                        .sb-master-item-weapon-line {
                                            margin: 0 0 7px;
                                            color: #6b5548;
                                            font-family: Georgia, "Times New Roman", serif;
                                            font-size: 15px;
                                            font-weight: 700;
                                            font-style: italic;
                                            line-height: 1.38;
                                        }

                                        .sb-master-item-mechanic-line {
                                            margin: 3px 0 0;
                                            color: #6b5548;
                                            font-size: 10.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-master-item-mechanic-line > strong {
                                            margin-right: 5px;
                                            color: #8c6239;
                                            font-size: 7.5px;
                                            font-weight: 900;
                                            letter-spacing: .065em;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-item-inline-tooltip {
                                            position: relative;
                                            display: inline;
                                            cursor: help;
                                        }

                                        .sb-master-item-inline-term {
                                            display: inline;
                                            padding: 0;
                                            border: 0;
                                            background: transparent;
                                            color: inherit;
                                            font: inherit;
                                            line-height: inherit;
                                            text-decoration:
                                                underline dotted
                                                rgba(140,98,57,.42);
                                            text-decoration-thickness: 1px;
                                            text-underline-offset: 2px;
                                            outline: none;
                                            cursor: help;
                                        }

                                        .sb-master-item-inline-term:focus-visible {
                                            border-radius: 2px;
                                            outline:
                                                1px solid
                                                rgba(140,98,57,.48);
                                            outline-offset: 2px;
                                        }

                                        /*
                                         * O painel não mora mais dentro da caixa da arma.
                                         * Ele é teleportado para <body> e usa position: fixed.
                                         * Portanto overflow / scroll do modal não consegue cortá-lo.
                                         */
                                        .sb-master-item-global-tooltip {
                                            position: fixed;
                                            z-index: 1000000;
                                            display: block;
                                            max-height: min(260px, 55vh);
                                            overflow-y: auto;
                                            border:
                                                1px solid
                                                rgba(176,140,98,.62);
                                            border-radius: 10px;
                                            background: #fbf8f1;
                                            padding: 10px 11px;
                                            color: #4f3427;
                                            box-shadow:
                                                0 16px 38px
                                                rgba(43,29,23,.25),
                                                inset 0 1px 0
                                                rgba(255,255,255,.78);
                                            pointer-events: none;
                                            transform: none;
                                            scrollbar-width: thin;
                                            scrollbar-color:
                                                rgba(140,98,57,.45)
                                                transparent;
                                        }

                                        .sb-master-item-global-tooltip.is-above {
                                            transform: translateY(-100%);
                                        }

                                        .sb-master-item-global-tooltip > strong {
                                            display: block;
                                            color: #53150f;
                                            font-family:
                                                Georgia,
                                                "Times New Roman",
                                                serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                            line-height: 1.2;
                                        }

                                        .sb-master-item-global-tooltip > span {
                                            display: block;
                                            margin-top: 5px;
                                            color: #5f4636;
                                            font-size: 11px;
                                            line-height: 1.55;
                                            white-space: pre-line;
                                        }

                                        .sb-master-item-nonweapon-stats {
                                            display: grid;
                                            gap: 2px;
                                        }


                                        .sb-master-item-range-inline {
                                            color: #8c6239;
                                            font-weight: 700;
                                            text-decoration: none;
                                        }

                                        .sb-master-item-range-line > span {
                                            color: #5f4636;
                                            font-weight: 800;
                                            letter-spacing: .01em;
                                        }


                                        /* ============================================================
                                           MASTER SHOP V10 — AÇÕES, CARDS E PRECIFICAÇÃO
                                        ============================================================ */

                                        .sb-master-shop-overview {
                                            overflow: visible !important;
                                        }

                                        .sb-master-shop-overview__inner {
                                            position: relative;
                                            overflow: visible;
                                        }

                                        .sb-master-shop-actions {
                                            position: relative;
                                            overflow: visible;
                                        }

                                        .sb-master-shop-more {
                                            position: relative;
                                            flex: 0 0 auto;
                                        }

                                        .sb-master-shop-more__button {
                                            display: grid;
                                            width: 38px;
                                            height: 36px;
                                            place-items: center;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 10px;
                                            background: #fffdf8;
                                            color: #6b1d14;
                                            font-size: 16px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            transition: .14s ease;
                                        }

                                        .sb-master-shop-more__button:hover,
                                        .sb-master-shop-more__button.is-active {
                                            border-color: #a97950;
                                            background: #f2e7d9;
                                        }

                                        .sb-master-shop-more__menu {
                                            position: absolute;
                                            right: 0;
                                            top: calc(100% + 7px);
                                            z-index: 80;
                                            display: grid;
                                            width: 228px;
                                            gap: 3px;
                                            border: 1px solid #d3c0a5;
                                            border-radius: 12px;
                                            background: #fffdf8;
                                            padding: 6px;
                                            box-shadow:
                                                0 18px 44px rgba(52,31,20,.18),
                                                inset 0 1px 0 rgba(255,255,255,.78);
                                        }

                                        .sb-master-shop-more__menu > button {
                                            display: grid;
                                            width: 100%;
                                            grid-template-columns: 30px minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            border: 0;
                                            border-radius: 9px;
                                            background: transparent;
                                            padding: 9px 8px;
                                            color: #5a3024;
                                            text-align: left;
                                        }

                                        .sb-master-shop-more__menu > button:hover {
                                            background: #f4ece1;
                                        }

                                        .sb-master-shop-more__menu > button.is-danger {
                                            color: #a02b24;
                                        }

                                        .sb-master-shop-more__menu > button.is-danger:hover {
                                            background: #fff1ef;
                                        }

                                        .sb-master-shop-more__icon {
                                            display: grid;
                                            width: 30px;
                                            height: 30px;
                                            place-items: center;
                                            border: 1px solid #dfceb8;
                                            border-radius: 8px;
                                            background: #faf5ee;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-master-shop-more__menu strong {
                                            display: block;
                                            font-size: 9px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-master-shop-more__menu small {
                                            display: block;
                                            margin-top: 2px;
                                            color: #90745f;
                                            font-size: 8px;
                                            font-weight: 600;
                                            line-height: 1.3;
                                        }

                                        /* Cards de estoque: maiores e mais fáceis de ler. */
                                        .sb-admin-product-grid {
                                            grid-template-columns:
                                                repeat(
                                                    auto-fill,
                                                    minmax(285px, 1fr)
                                                ) !important;
                                            gap: 13px !important;
                                        }

                                        .sb-admin-product {
                                            border-radius: 15px !important;
                                        }

                                        .sb-admin-product__button {
                                            grid-template-columns:
                                                92px
                                                minmax(0,1fr) !important;
                                            gap: 13px !important;
                                            padding: 12px !important;
                                        }

                                        .sb-admin-product__image {
                                            width: 92px !important;
                                            height: 92px !important;
                                            border-radius: 12px !important;
                                        }

                                        .sb-admin-product__stock {
                                            right: 6px !important;
                                            bottom: 6px !important;
                                            padding: 4px 7px !important;
                                            font-size: 8px !important;
                                        }

                                        .sb-admin-product__kind {
                                            font-size: 8px !important;
                                        }

                                        .sb-admin-product__copy h6 {
                                            margin-top: 4px !important;
                                            font-size: 18px !important;
                                            line-height: 1.08 !important;
                                        }

                                        .sb-admin-product__copy p {
                                            margin-top: 7px !important;
                                            font-size: 10px !important;
                                            line-height: 1.45 !important;
                                            -webkit-line-clamp: 3 !important;
                                        }

                                        .sb-admin-product__footer {
                                            padding: 10px 12px !important;
                                        }

                                        .sb-admin-product__price span {
                                            font-size: 7px !important;
                                        }

                                        .sb-admin-product__price strong {
                                            font-size: 16px !important;
                                        }

                                        .sb-admin-product__quick button {
                                            min-height: 32px !important;
                                            font-size: 7.5px !important;
                                        }

                                        /* Picker */
                                        .sb-shop-picker-head {
                                            display: flex;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 16px;
                                            border-bottom: 1px solid rgba(216,199,171,.75);
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8,
                                                    #fbf6ef
                                                );
                                            padding: 14px 18px;
                                        }

                                        .sb-shop-picker-head__eyebrow {
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .16em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-head h3 {
                                            margin: 2px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 24px;
                                            font-weight: 900;
                                            line-height: 1.05;
                                        }

                                        .sb-shop-picker-head p {
                                            margin: 4px 0 0;
                                            color: #806650;
                                            font-size: 10px;
                                        }

                                        .sb-shop-picker-head__actions {
                                            display: flex;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            gap: 8px;
                                        }

                                        .sb-shop-price-help-trigger {
                                            display: inline-flex;
                                            min-height: 38px;
                                            align-items: center;
                                            gap: 7px;
                                            border: 1px solid #d6c1a4;
                                            border-radius: 10px;
                                            background: #fffaf3;
                                            padding: 0 12px;
                                            color: #6b1d14;
                                            font-size: 8.5px;
                                            font-weight: 900;
                                            letter-spacing: .035em;
                                            text-transform: uppercase;
                                            transition: .14s ease;
                                        }

                                        .sb-shop-price-help-trigger:hover {
                                            border-color: #9b704d;
                                            background: #f3e8db;
                                        }

                                        .sb-shop-price-help-trigger > span {
                                            display: grid;
                                            width: 20px;
                                            height: 20px;
                                            place-items: center;
                                            border-radius: 50%;
                                            background: #6b1d14;
                                            color: white;
                                            font-family: Georgia, serif;
                                            font-size: 12px;
                                        }

                                        .sb-shop-picker-close {
                                            display: grid;
                                            width: 38px;
                                            height: 38px;
                                            place-items: center;
                                            border: 1px solid transparent;
                                            border-radius: 10px;
                                            background: transparent;
                                            color: #8c6239;
                                            font-size: 20px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-close:hover {
                                            border-color: #ddccb5;
                                            background: #f1e8db;
                                            color: #53150f;
                                        }

                                        .sb-shop-added-toast {
                                            position: absolute;
                                            top: 76px;
                                            right: 18px;
                                            z-index: 15;
                                            display: grid;
                                            grid-template-columns: 34px minmax(0,1fr);
                                            gap: 9px;
                                            align-items: center;
                                            width: min(330px, calc(100% - 36px));
                                            border: 1px solid rgba(35,123,84,.35);
                                            border-radius: 12px;
                                            background:
                                                linear-gradient(
                                                    135deg,
                                                    #f3fff9,
                                                    #e9f8f0
                                                );
                                            padding: 9px 11px;
                                            color: #225d43;
                                            box-shadow: 0 14px 35px rgba(30,79,58,.15);
                                        }

                                        .sb-shop-added-toast__icon {
                                            display: grid;
                                            width: 34px;
                                            height: 34px;
                                            place-items: center;
                                            border-radius: 10px;
                                            background: #27875f;
                                            color: white;
                                            font-size: 16px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-added-toast strong {
                                            display: block;
                                            font-size: 9px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-added-toast small {
                                            display: block;
                                            margin-top: 2px;
                                            color: #477762;
                                            font-size: 9px;
                                        }

                                        /* Cards do Vault maiores */
                                        .sb-shop-picker-vault-card {
                                            min-height: 96px;
                                        }

                                        /* Override dos cards inline existentes do picker. */
                                        [x-show="shopPickerOpen && lootActiveShop"]
                                        .space-y-2\.5 > button {
                                            grid-template-columns:
                                                78px
                                                minmax(0,1fr) !important;
                                            min-height: 102px;
                                            gap: 14px !important;
                                            padding: 12px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"]
                                        .space-y-2\.5 > button > div:first-child {
                                            width: 78px !important;
                                            height: 78px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"]
                                        .space-y-2\.5 > button h4 {
                                            font-size: 17px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"]
                                        .space-y-2\.5 > button p {
                                            font-size: 10px !important;
                                            line-height: 1.45 !important;
                                        }

                                        .sb-shop-picker-price-head {
                                            display: flex;
                                            align-items: end;
                                            justify-content: space-between;
                                            gap: 12px;
                                        }

                                        .sb-shop-picker-price-head > div > p {
                                            margin: 0;
                                            color: #8c6239;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .06em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-price-head > div > span {
                                            display: block;
                                            margin-top: 3px;
                                            color: #9a795b;
                                            font-size: 9px;
                                            line-height: 1.35;
                                        }

                                        .sb-shop-picker-price-head > button {
                                            flex: 0 0 auto;
                                            border: 0;
                                            background: transparent;
                                            color: #7b2117;
                                            font-size: 8px;
                                            font-weight: 900;
                                            text-decoration: underline;
                                            text-underline-offset: 3px;
                                        }

                                        /* Guia de preços */
                                        .sb-price-guide-layer {
                                            position: fixed;
                                            inset: 0;
                                            z-index: 310;
                                            display: grid;
                                            place-items: center;
                                            padding: 18px;
                                        }

                                        .sb-price-guide-backdrop {
                                            position: absolute;
                                            inset: 0;
                                            background: rgba(18,13,10,.72);
                                            backdrop-filter: blur(6px);
                                        }

                                        .sb-price-guide {
                                            position: relative;
                                            z-index: 1;
                                            display: flex;
                                            width: min(960px, 100%);
                                            max-height: calc(100dvh - 36px);
                                            flex-direction: column;
                                            overflow: hidden;
                                            border: 1px solid #ceb99c;
                                            border-radius: 18px;
                                            background: #fffdf8;
                                            box-shadow: 0 28px 80px rgba(30,18,12,.30);
                                        }

                                        .sb-price-guide__head {
                                            display: flex;
                                            flex: 0 0 auto;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 18px;
                                            border-bottom: 1px solid #ddccb5;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f2e6d7,
                                                    #eadac6
                                                );
                                            padding: 16px 18px;
                                        }

                                        .sb-price-guide__head > div > span {
                                            color: #8c6239;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .15em;
                                            text-transform: uppercase;
                                        }

                                        .sb-price-guide__head h3 {
                                            margin: 3px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 25px;
                                            font-weight: 900;
                                        }

                                        .sb-price-guide__head p {
                                            max-width: 620px;
                                            margin: 5px 0 0;
                                            color: #76553f;
                                            font-size: 9.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-price-guide__head > button {
                                            display: grid;
                                            width: 36px;
                                            height: 36px;
                                            flex: 0 0 auto;
                                            place-items: center;
                                            border: 1px solid #d4bea1;
                                            border-radius: 10px;
                                            background: #fffaf3;
                                            color: #76553f;
                                            font-size: 20px;
                                        }

                                        .sb-price-guide__body {
                                            display: grid;
                                            min-height: 0;
                                            grid-template-columns:
                                                repeat(2, minmax(0,1fr));
                                            gap: 12px;
                                            overflow-y: auto;
                                            padding: 14px;
                                        }

                                        .sb-price-guide-card {
                                            overflow: hidden;
                                            border: 1px solid #decdb7;
                                            border-radius: 14px;
                                            background: #fffaf4;
                                        }

                                        .sb-price-guide-card.is-mundane {
                                            grid-column: 1 / -1;
                                        }

                                        .sb-price-guide-card__title {
                                            display: flex;
                                            align-items: center;
                                            gap: 10px;
                                            border-bottom: 1px solid #eadfce;
                                            background: #f6eee4;
                                            padding: 11px 12px;
                                        }

                                        .sb-price-guide-card__title > span {
                                            display: grid;
                                            width: 34px;
                                            height: 34px;
                                            flex: 0 0 auto;
                                            place-items: center;
                                            border: 1px solid #d8c3a7;
                                            border-radius: 10px;
                                            background: #fffdf8;
                                            color: #8a4b20;
                                            font-size: 15px;
                                        }

                                        .sb-price-guide-card__title strong {
                                            display: block;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 15px;
                                        }

                                        .sb-price-guide-card__title small {
                                            display: block;
                                            margin-top: 2px;
                                            color: #8c6c55;
                                            font-size: 8px;
                                        }

                                        .sb-price-guide-table {
                                            display: grid;
                                        }

                                        .sb-price-guide-table > div {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                auto;
                                            gap: 12px;
                                            align-items: center;
                                            border-top: 1px solid #eee3d5;
                                            padding: 8px 11px;
                                        }

                                        .sb-price-guide-table > div:first-child {
                                            border-top: 0;
                                        }

                                        .sb-price-guide-table > div.is-head {
                                            background: #fbf6ef;
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                        }

                                        .sb-price-guide-table > div:not(.is-head) > span {
                                            color: #6f5140;
                                            font-size: 9px;
                                            font-weight: 800;
                                        }

                                        .sb-price-guide-table > div > strong {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 11px;
                                        }

                                        .sb-price-guide-table .is-artifact {
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    rgba(107,29,20,.045),
                                                    transparent
                                                );
                                        }

                                        .sb-price-guide-note {
                                            margin: 0;
                                            border-top: 1px solid #eadfce;
                                            background: #fffdf8;
                                            padding: 9px 11px;
                                            color: #876955;
                                            font-size: 8px;
                                            line-height: 1.5;
                                        }

                                        .sb-scroll-price-grid {
                                            display: grid;
                                            grid-template-columns:
                                                repeat(2, minmax(0,1fr));
                                            gap: 0;
                                        }

                                        .sb-scroll-price-grid > div {
                                            display: grid;
                                            grid-template-columns: 38px minmax(0,1fr);
                                            grid-template-areas:
                                                "level rarity"
                                                "level price";
                                            align-items: center;
                                            border-top: 1px solid #eee3d5;
                                            border-right: 1px solid #eee3d5;
                                            padding: 8px 10px;
                                        }

                                        .sb-scroll-price-grid > div:nth-child(2n) {
                                            border-right: 0;
                                        }

                                        .sb-scroll-price-grid > div > span {
                                            grid-area: level;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-scroll-price-grid > div > small {
                                            grid-area: rarity;
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-scroll-price-grid > div > strong {
                                            grid-area: price;
                                            margin-top: 2px;
                                            color: #6b3f2d;
                                            font-size: 9px;
                                        }

                                        .sb-mundane-price-grid {
                                            display: grid;
                                            grid-template-columns:
                                                repeat(3, minmax(0,1fr));
                                        }

                                        .sb-mundane-price-grid > div {
                                            border-left: 1px solid #eadfce;
                                            padding: 11px 12px;
                                        }

                                        .sb-mundane-price-grid > div:first-child {
                                            border-left: 0;
                                        }

                                        .sb-mundane-price-grid span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .06em;
                                            text-transform: uppercase;
                                        }

                                        .sb-mundane-price-grid strong {
                                            display: block;
                                            margin-top: 5px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                        }

                                        .sb-mundane-price-grid small {
                                            display: block;
                                            margin-top: 5px;
                                            color: #806650;
                                            font-size: 8px;
                                            line-height: 1.45;
                                        }

                                        .sb-price-guide__footer {
                                            display: flex;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 14px;
                                            border-top: 1px solid #ddccb5;
                                            background: #f1e4d2;
                                            padding: 10px 14px;
                                        }

                                        .sb-price-guide__footer > span {
                                            color: #806650;
                                            font-size: 8px;
                                            line-height: 1.4;
                                        }

                                        .sb-price-guide__footer > button {
                                            min-height: 35px;
                                            flex: 0 0 auto;
                                            border: 0;
                                            border-radius: 9px;
                                            background: #6b1d14;
                                            padding: 0 16px;
                                            color: white;
                                            font-size: 8px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        @media (max-width: 760px) {
                                            .sb-price-guide__body {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-price-guide-card.is-mundane {
                                                grid-column: auto;
                                            }

                                            .sb-mundane-price-grid {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-mundane-price-grid > div {
                                                border-top: 1px solid #eadfce;
                                                border-left: 0;
                                            }

                                            .sb-mundane-price-grid > div:first-child {
                                                border-top: 0;
                                            }

                                            .sb-shop-picker-head {
                                                align-items: flex-start;
                                            }

                                            .sb-shop-price-help-trigger {
                                                padding: 0 9px;
                                            }
                                        }


                                        /* ============================================================
                                           GUIA DE PREÇOS V2 — 3 ABAS / CONTEÚDO COMPACTO
                                        ============================================================ */

                                        .sb-price-guide-v2 {
                                            width: min(820px, 100%);
                                            max-height: min(
                                                82dvh,
                                                650px
                                            );
                                        }

                                        .sb-price-guide-v2__head {
                                            padding: 12px 15px;
                                        }

                                        .sb-price-guide-v2__head h3 {
                                            font-size: 22px;
                                        }

                                        .sb-price-guide-v2__head p {
                                            max-width: 560px;
                                            font-size: 8.5px;
                                        }

                                        .sb-price-guide-tabs {
                                            display: grid;
                                            flex: 0 0 auto;
                                            grid-template-columns:
                                                repeat(3, minmax(0,1fr));
                                            gap: 6px;
                                            border-bottom:
                                                1px solid #ddccb5;
                                            background: #f8f1e8;
                                            padding: 8px 10px;
                                        }

                                        .sb-price-guide-tabs > button {
                                            display: grid;
                                            grid-template-columns:
                                                32px minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            min-height: 48px;
                                            border:
                                                1px solid transparent;
                                            border-radius: 10px;
                                            background: transparent;
                                            padding: 6px 8px;
                                            color: #76553f;
                                            text-align: left;
                                            transition: .14s ease;
                                        }

                                        .sb-price-guide-tabs > button:hover {
                                            border-color: #dfccb3;
                                            background: rgba(
                                                255,
                                                253,
                                                248,
                                                .68
                                            );
                                        }

                                        .sb-price-guide-tabs > button.is-active {
                                            border-color: #b98a60;
                                            background: #fffdf8;
                                            color: #53150f;
                                            box-shadow:
                                                0 4px 12px
                                                rgba(83,21,15,.06);
                                        }

                                        .sb-price-guide-tabs__icon {
                                            display: grid;
                                            width: 32px;
                                            height: 32px;
                                            place-items: center;
                                            border:
                                                1px solid #d9c3a5;
                                            border-radius: 9px;
                                            background: #f3e8da;
                                            color: #8a4b20;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-price-guide-tabs > button.is-active
                                        .sb-price-guide-tabs__icon {
                                            border-color: #8a4b20;
                                            background: #8a4b20;
                                            color: white;
                                        }

                                        .sb-price-guide-tabs strong {
                                            display: block;
                                            font-family: Georgia, serif;
                                            font-size: 12px;
                                            font-weight: 900;
                                            line-height: 1.05;
                                        }

                                        .sb-price-guide-tabs small {
                                            display: block;
                                            margin-top: 3px;
                                            color: #9a806d;
                                            font-size: 7px;
                                            line-height: 1.2;
                                        }

                                        .sb-price-guide-v2__body {
                                            min-height: 0;
                                            flex: 1 1 auto;
                                            overflow-y: auto;
                                            padding: 10px;
                                            scrollbar-width: thin;
                                            scrollbar-color:
                                                rgba(140,98,57,.4)
                                                transparent;
                                        }

                                        .sb-price-guide-panel {
                                            min-height: 100%;
                                        }

                                        .sb-price-guide-panel__intro {
                                            display: flex;
                                            align-items: end;
                                            justify-content: space-between;
                                            gap: 16px;
                                            margin-bottom: 9px;
                                            padding: 0 2px;
                                        }

                                        .sb-price-guide-panel__intro > div > span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .1em;
                                            text-transform: uppercase;
                                        }

                                        .sb-price-guide-panel__intro h4 {
                                            margin: 2px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 17px;
                                            font-weight: 900;
                                        }

                                        .sb-price-guide-panel__intro > p {
                                            max-width: 390px;
                                            margin: 0;
                                            color: #806650;
                                            font-size: 8px;
                                            line-height: 1.45;
                                            text-align: right;
                                        }

                                        .sb-price-guide-magic-layout {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                minmax(0,1fr);
                                            gap: 10px;
                                        }

                                        .sb-price-guide-table-v2,
                                        .sb-price-guide-examples,
                                        .sb-scroll-price-table-v2,
                                        .sb-mundane-items-table,
                                        .sb-mundane-rarity {
                                            overflow: hidden;
                                            border:
                                                1px solid #decdb7;
                                            border-radius: 12px;
                                            background: #fffdf8;
                                        }

                                        .sb-price-guide-table-v2 > div {
                                            min-height: 35px;
                                            padding: 6px 9px;
                                        }

                                        .sb-price-guide-examples > header,
                                        .sb-mundane-rarity > header {
                                            border-bottom:
                                                1px solid #eadfce;
                                            background: #f6eee4;
                                            padding: 8px 10px;
                                        }

                                        .sb-price-guide-examples > header span,
                                        .sb-mundane-rarity > header span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-price-guide-examples > header strong,
                                        .sb-mundane-rarity > header strong {
                                            display: block;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                        }

                                        .sb-price-guide-example-row {
                                            display: grid;
                                            grid-template-columns:
                                                10px
                                                minmax(0,1fr)
                                                auto;
                                            gap: 8px;
                                            align-items: center;
                                            min-height: 47px;
                                            border-top:
                                                1px solid #eee3d5;
                                            padding: 6px 9px;
                                        }

                                        .sb-price-guide-example-row:first-of-type {
                                            border-top: 0;
                                        }

                                        .sb-price-guide-example-dot {
                                            width: 7px;
                                            height: 7px;
                                            border-radius: 50%;
                                            background: #9a806d;
                                        }

                                        .sb-price-guide-example-dot.is-common {
                                            background: #7e8f82;
                                        }

                                        .sb-price-guide-example-dot.is-uncommon {
                                            background: #4f8b68;
                                        }

                                        .sb-price-guide-example-dot.is-rare {
                                            background: #4d6ea5;
                                        }

                                        .sb-price-guide-example-dot.is-very-rare {
                                            background: #82559a;
                                        }

                                        .sb-price-guide-example-row div strong {
                                            display: block;
                                            color: #5b3527;
                                            font-size: 9px;
                                        }

                                        .sb-price-guide-example-row div small {
                                            display: block;
                                            margin-top: 2px;
                                            color: #9a806d;
                                            font-size: 7px;
                                        }

                                        .sb-price-guide-example-row > b {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 10px;
                                            white-space: nowrap;
                                        }

                                        .sb-price-guide-tip {
                                            display: grid;
                                            grid-template-columns:
                                                28px
                                                minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            margin-top: 9px;
                                            border:
                                                1px dashed #d6c0a1;
                                            border-radius: 10px;
                                            background: #faf4eb;
                                            padding: 7px 9px;
                                        }

                                        .sb-price-guide-tip > span {
                                            display: grid;
                                            width: 28px;
                                            height: 28px;
                                            place-items: center;
                                            border-radius: 8px;
                                            background: #efe2d1;
                                            color: #8a4b20;
                                            font-size: 12px;
                                        }

                                        .sb-price-guide-tip p {
                                            margin: 0;
                                            color: #806650;
                                            font-size: 7.5px;
                                            line-height: 1.45;
                                        }

                                        .sb-scroll-price-table-v2 {
                                            display: grid;
                                        }

                                        .sb-scroll-price-table-v2 > div {
                                            display: grid;
                                            grid-template-columns:
                                                70px
                                                110px
                                                130px
                                                minmax(0,1fr);
                                            gap: 10px;
                                            align-items: center;
                                            min-height: 36px;
                                            border-top:
                                                1px solid #eee3d5;
                                            padding: 5px 9px;
                                        }

                                        .sb-scroll-price-table-v2 > div:first-child {
                                            border-top: 0;
                                        }

                                        .sb-scroll-price-table-v2 > div.is-head {
                                            min-height: 28px;
                                            background: #f6eee4;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                        }

                                        .sb-scroll-price-table-v2 > div > strong {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 10px;
                                        }

                                        .sb-scroll-price-table-v2 > div > span {
                                            color: #76553f;
                                            font-size: 8px;
                                            font-weight: 800;
                                        }

                                        .sb-scroll-price-table-v2 > div > b {
                                            color: #6b1d14;
                                            font-size: 9px;
                                            white-space: nowrap;
                                        }

                                        .sb-scroll-price-table-v2 > div > small {
                                            color: #9a806d;
                                            font-size: 7px;
                                        }

                                        .sb-mundane-reference-layout {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1.15fr)
                                                minmax(0,.85fr);
                                            gap: 10px;
                                        }

                                        .sb-mundane-items-table > div {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                100px;
                                            gap: 10px;
                                            align-items: center;
                                            min-height: 34px;
                                            border-top:
                                                1px solid #eee3d5;
                                            padding: 5px 9px;
                                        }

                                        .sb-mundane-items-table > div:first-child {
                                            border-top: 0;
                                        }

                                        .sb-mundane-items-table > div.is-head {
                                            min-height: 28px;
                                            background: #f6eee4;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .07em;
                                            text-transform: uppercase;
                                        }

                                        .sb-mundane-items-table > div:not(.is-head) span {
                                            color: #76553f;
                                            font-size: 8.5px;
                                        }

                                        .sb-mundane-items-table > div strong {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 10px;
                                            text-align: right;
                                        }

                                        .sb-mundane-rarity > article {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                auto;
                                            gap: 10px;
                                            align-items: center;
                                            border-top:
                                                1px solid #eee3d5;
                                            padding: 8px 10px;
                                        }

                                        .sb-mundane-rarity > article div strong {
                                            display: block;
                                            color: #5b3527;
                                            font-size: 9px;
                                        }

                                        .sb-mundane-rarity > article div small {
                                            display: block;
                                            margin-top: 2px;
                                            color: #9a806d;
                                            font-size: 7px;
                                            line-height: 1.35;
                                        }

                                        .sb-mundane-rarity > article > b {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 12px;
                                        }

                                        .sb-mundane-rarity > p {
                                            margin: 0;
                                            border-top:
                                                1px solid #eadfce;
                                            background: #faf4eb;
                                            padding: 8px 10px;
                                            color: #806650;
                                            font-size: 7.5px;
                                            line-height: 1.45;
                                        }

                                        .sb-price-guide-v2__footer {
                                            min-height: 46px;
                                            padding: 7px 11px;
                                        }

                                        @media (max-width: 760px) {
                                            .sb-price-guide-v2 {
                                                max-height:
                                                    calc(100dvh - 16px);
                                            }

                                            .sb-price-guide-tabs {
                                                gap: 4px;
                                                padding: 6px;
                                            }

                                            .sb-price-guide-tabs > button {
                                                grid-template-columns: 1fr;
                                                justify-items: center;
                                                min-height: 58px;
                                                text-align: center;
                                            }

                                            .sb-price-guide-tabs__icon {
                                                width: 27px;
                                                height: 27px;
                                            }

                                            .sb-price-guide-tabs small {
                                                display: none;
                                            }

                                            .sb-price-guide-panel__intro {
                                                display: block;
                                            }

                                            .sb-price-guide-panel__intro > p {
                                                max-width: none;
                                                margin-top: 5px;
                                                text-align: left;
                                            }

                                            .sb-price-guide-magic-layout,
                                            .sb-mundane-reference-layout {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-scroll-price-table-v2 > div {
                                                grid-template-columns:
                                                    52px
                                                    82px
                                                    100px
                                                    minmax(0,1fr);
                                                gap: 6px;
                                            }
                                        }


                                        /* ============================================================
                                           GUIA DE PREÇOS V3 — TIPOGRAFIA MAIS LEGÍVEL
                                        ============================================================ */

                                        .sb-price-guide-v2 {
                                            width: min(860px, 100%);
                                            max-height: min(84dvh, 690px);
                                        }

                                        .sb-price-guide-v2__head {
                                            padding: 14px 17px;
                                        }

                                        .sb-price-guide-v2__head > div > span {
                                            font-size: 8px;
                                        }

                                        .sb-price-guide-v2__head h3 {
                                            font-size: 25px;
                                        }

                                        .sb-price-guide-v2__head p {
                                            max-width: 600px;
                                            font-size: 10px;
                                            line-height: 1.5;
                                        }

                                        .sb-price-guide-tabs {
                                            gap: 7px;
                                            padding: 9px 11px;
                                        }

                                        .sb-price-guide-tabs > button {
                                            min-height: 54px;
                                            padding: 7px 9px;
                                        }

                                        .sb-price-guide-tabs strong {
                                            font-size: 14px;
                                        }

                                        .sb-price-guide-tabs small {
                                            margin-top: 4px;
                                            font-size: 8.5px;
                                        }

                                        .sb-price-guide-v2__body {
                                            padding: 12px;
                                        }

                                        .sb-price-guide-panel__intro {
                                            margin-bottom: 11px;
                                        }

                                        .sb-price-guide-panel__intro > div > span {
                                            font-size: 7.5px;
                                        }

                                        .sb-price-guide-panel__intro h4 {
                                            font-size: 19px;
                                        }

                                        .sb-price-guide-panel__intro > p {
                                            max-width: 410px;
                                            font-size: 9.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-price-guide-table > div.is-head,
                                        .sb-scroll-price-table-v2 > div.is-head,
                                        .sb-mundane-items-table > div.is-head {
                                            font-size: 7.5px;
                                        }

                                        .sb-price-guide-table > div:not(.is-head) > span {
                                            font-size: 10px;
                                        }

                                        .sb-price-guide-table > div > strong {
                                            font-size: 12.5px;
                                        }

                                        .sb-price-guide-examples > header span,
                                        .sb-mundane-rarity > header span {
                                            font-size: 7.5px;
                                        }

                                        .sb-price-guide-examples > header strong,
                                        .sb-mundane-rarity > header strong {
                                            font-size: 15px;
                                        }

                                        .sb-price-guide-example-row div strong {
                                            font-size: 10.5px;
                                        }

                                        .sb-price-guide-example-row div small {
                                            font-size: 8px;
                                        }

                                        .sb-price-guide-example-row > b {
                                            font-size: 12px;
                                        }

                                        .sb-price-guide-tip p {
                                            font-size: 8.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-scroll-price-table-v2 > div {
                                            min-height: 39px;
                                            padding: 6px 10px;
                                        }

                                        .sb-scroll-price-table-v2 > div > strong {
                                            font-size: 11.5px;
                                        }

                                        .sb-scroll-price-table-v2 > div > span {
                                            font-size: 9px;
                                        }

                                        .sb-scroll-price-table-v2 > div > b {
                                            font-size: 10.5px;
                                        }

                                        .sb-scroll-price-table-v2 > div > small {
                                            font-size: 8px;
                                        }

                                        .sb-mundane-items-table > div:not(.is-head) span {
                                            font-size: 9.5px;
                                        }

                                        .sb-mundane-items-table > div strong {
                                            font-size: 11.5px;
                                        }

                                        .sb-mundane-rarity > article div strong {
                                            font-size: 10.5px;
                                        }

                                        .sb-mundane-rarity > article div small {
                                            font-size: 8px;
                                        }

                                        .sb-mundane-rarity > article > b {
                                            font-size: 13.5px;
                                        }

                                        .sb-mundane-rarity > p {
                                            font-size: 8.5px;
                                            line-height: 1.5;
                                        }

                                        .sb-price-guide-v2__footer > span {
                                            font-size: 8.5px;
                                        }

                                        .sb-price-guide-v2__footer > button {
                                            min-height: 37px;
                                            font-size: 8.5px;
                                        }

                                        @media (max-width: 760px) {
                                            .sb-price-guide-v2__head h3 {
                                                font-size: 22px;
                                            }

                                            .sb-price-guide-tabs strong {
                                                font-size: 12.5px;
                                            }

                                            .sb-price-guide-panel__intro h4 {
                                                font-size: 17px;
                                            }

                                            .sb-scroll-price-table-v2 > div {
                                                grid-template-columns:
                                                    48px
                                                    78px
                                                    96px
                                                    minmax(0,1fr);
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V7 — BUSCA, FILTROS E MOEDAS
                                        ============================================================ */

                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            max-width: 1240px !important;
                                            max-height: 92dvh !important;
                                        }

                                        .sb-shop-picker-layout {
                                            display: grid;
                                            min-height: 0;
                                            flex: 1 1 auto;
                                            grid-template-columns:
                                                minmax(0, 1.12fr)
                                                minmax(470px, .88fr);
                                            overflow: hidden;
                                        }

                                        .sb-shop-picker-catalog {
                                            display: flex;
                                            min-width: 0;
                                            min-height: 0;
                                            flex-direction: column;
                                            border-right: 1px solid rgba(216,199,171,.78);
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f8f2e8 0%,
                                                    #f4ecdf 100%
                                                );
                                        }

                                        .sb-shop-picker-filters {
                                            flex: 0 0 auto;
                                            border-bottom: 1px solid rgba(216,199,171,.72);
                                            background: rgba(255,253,248,.82);
                                            padding: 13px 14px 10px;
                                            backdrop-filter: blur(4px);
                                        }

                                        .sb-shop-picker-search {
                                            display: grid;
                                            grid-template-columns: 22px minmax(0,1fr);
                                            gap: 7px;
                                            align-items: center;
                                            height: 43px;
                                            border: 1px solid #ccb28f;
                                            border-radius: 11px;
                                            background: #fffdf8;
                                            padding: 0 11px;
                                            color: #9a795b;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.78),
                                                0 3px 10px rgba(83,21,15,.025);
                                        }

                                        .sb-shop-picker-search:focus-within {
                                            border-color: #8a4b20;
                                            box-shadow:
                                                0 0 0 3px rgba(138,75,32,.08);
                                        }

                                        .sb-shop-picker-search svg {
                                            width: 17px;
                                            height: 17px;
                                        }

                                        .sb-shop-picker-search input {
                                            width: 100%;
                                            border: 0;
                                            background: transparent;
                                            color: #53150f;
                                            font-size: 11px;
                                            font-weight: 750;
                                            outline: none;
                                        }

                                        .sb-shop-picker-search input::placeholder {
                                            color: #a48c79;
                                        }

                                        .sb-shop-picker-filter-row {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                190px;
                                            gap: 8px;
                                            margin-top: 8px;
                                        }

                                        .sb-shop-picker-nature {
                                            display: grid;
                                            grid-template-columns:
                                                repeat(3, minmax(0,1fr));
                                            gap: 4px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 10px;
                                            background: #efe4d5;
                                            padding: 3px;
                                        }

                                        .sb-shop-picker-nature button {
                                            min-height: 32px;
                                            border: 1px solid transparent;
                                            border-radius: 8px;
                                            background: transparent;
                                            color: #76553f;
                                            font-size: 8px;
                                            font-weight: 900;
                                            letter-spacing: .02em;
                                            text-transform: uppercase;
                                            transition: .14s ease;
                                        }

                                        .sb-shop-picker-nature button:hover {
                                            background: rgba(255,253,248,.62);
                                        }

                                        .sb-shop-picker-nature button.is-active {
                                            border-color: rgba(176,140,98,.28);
                                            background: #fffdf8;
                                            color: #6b1d14;
                                            box-shadow: 0 3px 9px rgba(83,21,15,.055);
                                        }

                                        .sb-shop-picker-rarity {
                                            position: relative;
                                            display: grid;
                                            grid-template-columns:
                                                auto minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            min-height: 38px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 10px;
                                            background: #fffdf8;
                                            padding: 0 9px;
                                        }

                                        .sb-shop-picker-rarity > span {
                                            color: #9a795b;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .06em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-rarity select {
                                            width: 100%;
                                            min-width: 0;
                                            border: 0;
                                            background: transparent;
                                            color: #53150f;
                                            font-size: 9px;
                                            font-weight: 900;
                                            outline: none;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            display: flex;
                                            min-height: 23px;
                                            align-items: end;
                                            justify-content: space-between;
                                            gap: 10px;
                                            margin-top: 6px;
                                            padding: 0 2px;
                                            color: #8b715d;
                                            font-size: 7.5px;
                                        }

                                        .sb-shop-picker-filter-summary strong {
                                            color: #53150f;
                                            font-size: 9px;
                                        }

                                        .sb-shop-picker-filter-summary button {
                                            border: 0;
                                            background: transparent;
                                            color: #8a2d22;
                                            font-size: 7.5px;
                                            font-weight: 900;
                                            text-decoration: underline;
                                            text-underline-offset: 3px;
                                        }

                                        .sb-shop-picker-items {
                                            display: grid;
                                            min-height: 0;
                                            flex: 1 1 auto;
                                            align-content: start;
                                            gap: 8px;
                                            overflow-y: auto;
                                            padding: 10px 12px 14px;
                                            scrollbar-width: thin;
                                            scrollbar-color: rgba(140,98,57,.44) transparent;
                                        }

                                        .sb-shop-picker-item {
                                            display: grid;
                                            grid-template-columns:
                                                78px minmax(0,1fr) 28px;
                                            gap: 12px;
                                            align-items: center;
                                            width: 100%;
                                            min-height: 102px;
                                            border: 1px solid #d9c6ab;
                                            border-radius: 14px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    rgba(255,253,248,.96),
                                                    rgba(251,247,240,.96)
                                                );
                                            padding: 9px;
                                            color: #53150f;
                                            text-align: left;
                                            box-shadow:
                                                0 3px 10px rgba(83,21,15,.025);
                                            transition:
                                                transform .13s ease,
                                                border-color .13s ease,
                                                box-shadow .13s ease;
                                        }

                                        .sb-shop-picker-item:hover {
                                            transform: translateY(-1px);
                                            border-color: #b88e67;
                                            box-shadow: 0 7px 17px rgba(83,21,15,.07);
                                        }

                                        .sb-shop-picker-item.is-selected {
                                            border-color: #8a4b20;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffaf3,
                                                    #f6e9d9
                                                );
                                            box-shadow:
                                                0 0 0 2px rgba(138,75,32,.08),
                                                0 8px 18px rgba(83,21,15,.08);
                                        }

                                        .sb-shop-picker-item__image {
                                            display: grid;
                                            width: 78px;
                                            height: 78px;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 12px;
                                            background: #f0e7da;
                                            color: #a28670;
                                            font-family: Georgia, serif;
                                            font-size: 18px;
                                        }

                                        .sb-shop-picker-item__image img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: contain;
                                            padding: 5px;
                                        }

                                        .sb-shop-picker-item__copy {
                                            min-width: 0;
                                        }

                                        .sb-shop-picker-item__meta {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 5px;
                                            align-items: center;
                                        }

                                        .sb-shop-picker-item__nature,
                                        .sb-shop-picker-item__rarity {
                                            display: inline-flex;
                                            min-height: 20px;
                                            align-items: center;
                                            border: 1px solid #dfcfba;
                                            border-radius: 999px;
                                            background: #faf5ed;
                                            padding: 0 7px;
                                            color: #806650;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .045em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-item__nature.is-magical {
                                            border-color: rgba(138,75,32,.26);
                                            background: #f5e7d7;
                                            color: #8a4b20;
                                        }

                                        .sb-shop-picker-item__rarity {
                                            background: #fffdf8;
                                            color: #9a806d;
                                        }

                                        .sb-shop-picker-item h4 {
                                            overflow: hidden;
                                            margin: 5px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 16px;
                                            font-weight: 900;
                                            line-height: 1.08;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-picker-item p {
                                            display: -webkit-box;
                                            overflow: hidden;
                                            margin: 5px 0 0;
                                            color: #6f5745;
                                            font-size: 9px;
                                            line-height: 1.45;
                                            -webkit-box-orient: vertical;
                                            -webkit-line-clamp: 2;
                                        }

                                        .sb-shop-picker-item__select {
                                            display: grid;
                                            width: 27px;
                                            height: 27px;
                                            place-items: center;
                                            border: 1px solid #ddccb5;
                                            border-radius: 50%;
                                            background: #fffdf8;
                                            color: #b69a83;
                                            opacity: .62;
                                        }

                                        .sb-shop-picker-item__select svg {
                                            width: 14px;
                                            height: 14px;
                                        }

                                        .sb-shop-picker-item.is-selected
                                        .sb-shop-picker-item__select {
                                            border-color: #8a4b20;
                                            background: #8a4b20;
                                            color: white;
                                            opacity: 1;
                                        }

                                        .sb-shop-picker-empty {
                                            padding: 48px 20px;
                                            text-align: center;
                                        }

                                        .sb-shop-picker-empty > div {
                                            color: #b08c62;
                                            font-size: 28px;
                                        }

                                        .sb-shop-picker-empty h4 {
                                            margin: 7px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 17px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-empty p {
                                            margin: 4px 0 0;
                                            color: #8c6b54;
                                            font-size: 9px;
                                        }

                                        .sb-shop-picker-empty button {
                                            margin-top: 10px;
                                            border: 1px solid #d5c1a4;
                                            border-radius: 9px;
                                            background: #fffdf8;
                                            padding: 7px 10px;
                                            color: #6b1d14;
                                            font-size: 8px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-config {
                                            position: relative;
                                            display: flex;
                                            min-width: 0;
                                            min-height: 0;
                                            flex-direction: column;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8 0%,
                                                    #faf5ed 100%
                                                );
                                        }

                                        .sb-shop-picker-config__content {
                                            display: grid;
                                            min-height: 0;
                                            flex: 1 1 auto;
                                            align-content: start;
                                            gap: 10px;
                                            overflow-y: auto;
                                            padding: 14px;
                                            scrollbar-width: thin;
                                            scrollbar-color: rgba(140,98,57,.4) transparent;
                                        }

                                        .sb-shop-picker-selected {
                                            display: grid;
                                            grid-template-columns:
                                                86px minmax(0,1fr);
                                            gap: 12px;
                                            align-items: center;
                                            border: 1px solid #decbb1;
                                            border-radius: 14px;
                                            background:
                                                linear-gradient(
                                                    135deg,
                                                    #f5eadc,
                                                    #fffdf8 72%
                                                );
                                            padding: 10px;
                                        }

                                        .sb-shop-picker-selected__image {
                                            display: grid;
                                            width: 86px;
                                            height: 86px;
                                            place-items: center;
                                            overflow: hidden;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 12px;
                                            background: #fffdf8;
                                            color: #b79b7f;
                                            font-family: Georgia, serif;
                                            font-size: 20px;
                                        }

                                        .sb-shop-picker-selected__image img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: contain;
                                            padding: 6px;
                                        }

                                        .sb-shop-picker-selected__copy > span {
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .1em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            margin: 3px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 21px;
                                            font-weight: 900;
                                            line-height: 1.05;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            margin: 4px 0 0;
                                            color: #8a5f45;
                                            font-size: 9px;
                                            font-weight: 800;
                                        }

                                        .sb-shop-picker-selected__copy small {
                                            display: -webkit-box;
                                            overflow: hidden;
                                            margin-top: 5px;
                                            color: #765f4f;
                                            font-size: 8.5px;
                                            line-height: 1.4;
                                            -webkit-box-orient: vertical;
                                            -webkit-line-clamp: 2;
                                        }

                                        .sb-shop-picker-config-card {
                                            border: 1px solid #decbb1;
                                            border-radius: 14px;
                                            background: rgba(255,253,248,.88);
                                            padding: 11px;
                                        }

                                        .sb-shop-picker-config-card__head {
                                            display: flex;
                                            align-items: flex-end;
                                            justify-content: space-between;
                                            gap: 10px;
                                            margin-bottom: 9px;
                                        }

                                        .sb-shop-picker-config-card__head > div > span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .1em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-config-card__head > div > strong {
                                            display: block;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-config-card__head > small {
                                            max-width: 190px;
                                            color: #9a806d;
                                            font-size: 7.5px;
                                            line-height: 1.35;
                                            text-align: right;
                                        }

                                        .sb-shop-picker-config-card__head > button {
                                            border: 0;
                                            background: transparent;
                                            color: #7b2117;
                                            font-size: 7.5px;
                                            font-weight: 900;
                                            text-decoration: underline;
                                            text-underline-offset: 3px;
                                        }

                                        .sb-shop-quantity-stepper {
                                            display: grid;
                                            grid-template-columns:
                                                42px minmax(0,1fr) 42px;
                                            gap: 6px;
                                        }

                                        .sb-shop-quantity-stepper button,
                                        .sb-shop-quantity-stepper input {
                                            height: 39px;
                                            border: 1px solid #d2bda0;
                                            background: #fffdf8;
                                            color: #53150f;
                                        }

                                        .sb-shop-quantity-stepper button {
                                            border-radius: 10px;
                                            font-size: 17px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-quantity-stepper button:disabled {
                                            opacity: .35;
                                        }

                                        .sb-shop-quantity-stepper input {
                                            min-width: 0;
                                            border-radius: 10px;
                                            font-family: Georgia, serif;
                                            font-size: 16px;
                                            font-weight: 900;
                                            text-align: center;
                                            outline: none;
                                        }

                                        .sb-shop-price-coins {
                                            display: grid;
                                            grid-template-columns:
                                                repeat(5, minmax(0,1fr));
                                            gap: 6px;
                                        }

                                        .sb-shop-price-coin {
                                            min-width: 0;
                                            border: 1px solid rgba(176,140,98,.34);
                                            border-radius: 12px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffefb 0%,
                                                    #f7efe2 100%
                                                );
                                            padding: 7px;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.78);
                                            transition:
                                                border-color .14s ease,
                                                box-shadow .14s ease,
                                                transform .14s ease;
                                        }

                                        .sb-shop-price-coin.is-active {
                                            border-color: rgba(107,29,20,.42);
                                            box-shadow:
                                                0 7px 16px rgba(107,29,20,.07),
                                                inset 0 1px 0 rgba(255,255,255,.82);
                                            transform: translateY(-1px);
                                        }

                                        .sb-shop-price-coin__head {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                        }

                                        .sb-shop-price-coin__identity {
                                            display: flex;
                                            min-width: 0;
                                            align-items: center;
                                            gap: 5px;
                                        }

                                        .sb-shop-price-coin__icon {
                                            display: grid;
                                            width: 27px;
                                            height: 27px;
                                            flex: 0 0 27px;
                                            place-items: center;
                                            border: 1px solid currentColor;
                                            border-radius: 50%;
                                        }

                                        .sb-shop-price-coin__icon svg {
                                            width: 18px;
                                            height: 18px;
                                        }

                                        .sb-shop-price-coin__icon circle,
                                        .sb-shop-price-coin__icon path {
                                            stroke: currentColor;
                                            stroke-width: 1.25;
                                            stroke-linecap: round;
                                            stroke-linejoin: round;
                                        }

                                        .sb-shop-price-coin.coin-copper
                                        .sb-shop-price-coin__icon {
                                            background: #f3ddcf;
                                            color: #a4512d;
                                        }

                                        .sb-shop-price-coin.coin-silver
                                        .sb-shop-price-coin__icon {
                                            background: #ececea;
                                            color: #777a7d;
                                        }

                                        .sb-shop-price-coin.coin-electrum
                                        .sb-shop-price-coin__icon {
                                            background: #eee7c8;
                                            color: #8f7e38;
                                        }

                                        .sb-shop-price-coin.coin-gold
                                        .sb-shop-price-coin__icon {
                                            background: #f5e3ad;
                                            color: #b17812;
                                        }

                                        .sb-shop-price-coin.coin-platinum
                                        .sb-shop-price-coin__icon {
                                            background: #edf0ed;
                                            color: #7b8582;
                                        }

                                        .sb-shop-price-coin__meta {
                                            display: flex;
                                            min-width: 0;
                                            flex-direction: column;
                                            gap: 1px;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            color: #53150f;
                                            font-size: 9px;
                                            font-weight: 900;
                                            line-height: 1;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            overflow: hidden;
                                            color: #9a795b;
                                            font-size: 6px;
                                            font-weight: 800;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-price-coin__stepper {
                                            display: grid;
                                            grid-template-columns:
                                                24px minmax(0,1fr) 24px;
                                            gap: 3px;
                                            margin-top: 7px;
                                        }

                                        .sb-shop-price-coin__stepper button,
                                        .sb-shop-price-coin__stepper input {
                                            height: 31px;
                                            border: 1px solid rgba(176,140,98,.40);
                                            background: #fffdf8;
                                            color: #53150f;
                                        }

                                        .sb-shop-price-coin__stepper button {
                                            border-radius: 8px;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-price-coin__stepper button:disabled {
                                            opacity: .28;
                                        }

                                        .sb-shop-price-coin__stepper input {
                                            width: 100%;
                                            min-width: 0;
                                            border-radius: 8px;
                                            padding: 0 2px;
                                            font-family: Georgia, serif;
                                            font-size: 12px;
                                            font-weight: 900;
                                            text-align: center;
                                            outline: none;
                                        }

                                        .sb-shop-price-summary {
                                            display: flex;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 10px;
                                            margin-top: 8px;
                                            border-top: 1px solid #eadfce;
                                            padding-top: 8px;
                                        }

                                        .sb-shop-price-summary span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-price-summary strong {
                                            display: block;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-price-summary small {
                                            max-width: 170px;
                                            color: #8c6f58;
                                            font-size: 7px;
                                            line-height: 1.35;
                                            text-align: right;
                                        }

                                        .sb-shop-picker-config-empty {
                                            display: flex;
                                            min-height: 100%;
                                            flex: 1 1 auto;
                                            flex-direction: column;
                                            align-items: center;
                                            justify-content: center;
                                            padding: 30px 20px;
                                            text-align: center;
                                        }

                                        .sb-shop-picker-config-empty__icon {
                                            display: grid;
                                            width: 58px;
                                            height: 58px;
                                            place-items: center;
                                            border: 1px solid #d3bea1;
                                            border-radius: 50%;
                                            background: #f3e9dc;
                                            color: #8c6239;
                                            font-family: Georgia, serif;
                                            font-size: 24px;
                                        }

                                        .sb-shop-picker-config-empty h4 {
                                            margin: 13px 0 0;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 20px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-config-empty p {
                                            max-width: 290px;
                                            margin: 5px 0 0;
                                            color: #8c6b54;
                                            font-size: 9px;
                                            line-height: 1.5;
                                        }

                                        .sb-shop-picker-submit {
                                            display: flex;
                                            min-height: 70px;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 12px;
                                            border-top: 1px solid #ddccb5;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f1e5d6,
                                                    #eadbc8
                                                );
                                            padding: 10px 13px;
                                        }

                                        .sb-shop-picker-submit > div > span {
                                            display: block;
                                            color: #8c6239;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            display: block;
                                            max-width: 260px;
                                            overflow: hidden;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                            font-weight: 900;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-picker-submit > button {
                                            min-width: 166px;
                                            min-height: 40px;
                                            flex: 0 0 auto;
                                            border: 0;
                                            border-radius: 10px;
                                            background: #6b1d14;
                                            padding: 0 15px;
                                            color: white;
                                            font-size: 8.5px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            text-transform: uppercase;
                                            transition: .14s ease;
                                        }

                                        .sb-shop-picker-submit > button:hover:not(:disabled) {
                                            background: #53150f;
                                            transform: translateY(-1px);
                                        }

                                        .sb-shop-picker-submit > button:disabled {
                                            cursor: not-allowed;
                                            opacity: .42;
                                        }

                                        @media (max-width: 1080px) {
                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(390px,.9fr);
                                            }

                                            .sb-shop-price-coins {
                                                grid-template-columns:
                                                    repeat(3, minmax(0,1fr));
                                            }
                                        }

                                        @media (max-width: 820px) {
                                            .sb-shop-picker-layout {
                                                grid-template-columns: 1fr;
                                                overflow-y: auto;
                                            }

                                            .sb-shop-picker-catalog {
                                                min-height: 500px;
                                                border-right: 0;
                                                border-bottom: 1px solid #ddccb5;
                                            }

                                            .sb-shop-picker-config {
                                                min-height: 560px;
                                            }

                                            .sb-shop-picker-filter-row {
                                                grid-template-columns: 1fr;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V8 — FIT SCREEN / MENOS SCROLL / MAIS RESPIRO
                                           Objetivo:
                                           - aproveitar melhor largura e altura do viewport;
                                           - reduzir scroll interno desnecessário;
                                           - manter cards legíveis sem "esmagar" a coluna direita.
                                        ============================================================ */

                                        [x-show="shopPickerOpen && lootActiveShop"] {
                                            padding: 12px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            width: min(
                                                1320px,
                                                calc(100vw - 24px)
                                            ) !important;
                                            max-width: none !important;
                                            height: min(
                                                760px,
                                                calc(100dvh - 24px)
                                            ) !important;
                                            max-height: none !important;
                                            border-radius: 16px !important;
                                        }

                                        /* Cabeçalho mais baixo sem perder hierarquia. */
                                        .sb-shop-picker-head {
                                            min-height: 70px;
                                            padding: 9px 14px !important;
                                        }

                                        .sb-shop-picker-head__eyebrow {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-head h3 {
                                            margin-top: 1px !important;
                                            font-size: 22px !important;
                                        }

                                        .sb-shop-picker-head p {
                                            margin-top: 3px !important;
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-price-help-trigger {
                                            min-height: 34px !important;
                                            padding: 0 10px !important;
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-price-help-trigger > span {
                                            width: 18px !important;
                                            height: 18px !important;
                                            font-size: 10px !important;
                                        }

                                        .sb-shop-picker-close {
                                            width: 34px !important;
                                            height: 34px !important;
                                        }

                                        /* Mais largura para ambos os lados. */
                                        .sb-shop-picker-layout {
                                            grid-template-columns:
                                                minmax(0, 1.18fr)
                                                minmax(500px, .82fr) !important;
                                        }

                                        /* ============================================================
                                           CATÁLOGO — filtros em uma faixa mais baixa
                                        ============================================================ */

                                        .sb-shop-picker-filters {
                                            display: grid;
                                            grid-template-columns:
                                                minmax(270px, 1.05fr)
                                                minmax(390px, .95fr);
                                            gap: 6px 8px;
                                            padding: 8px 10px 6px !important;
                                        }

                                        .sb-shop-picker-search {
                                            height: 38px !important;
                                            border-radius: 10px !important;
                                            padding: 0 9px !important;
                                        }

                                        .sb-shop-picker-search svg {
                                            width: 15px !important;
                                            height: 15px !important;
                                        }

                                        .sb-shop-picker-search input {
                                            font-size: 10px !important;
                                        }

                                        .sb-shop-picker-filter-row {
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                165px !important;
                                            gap: 6px !important;
                                            margin-top: 0 !important;
                                        }

                                        .sb-shop-picker-nature {
                                            gap: 3px !important;
                                            border-radius: 9px !important;
                                            padding: 3px !important;
                                        }

                                        .sb-shop-picker-nature button {
                                            min-height: 30px !important;
                                            border-radius: 7px !important;
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-picker-rarity {
                                            min-height: 36px !important;
                                            border-radius: 9px !important;
                                            padding: 0 8px !important;
                                        }

                                        .sb-shop-picker-rarity > span {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-picker-rarity select {
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            grid-column: 1 / -1;
                                            min-height: 17px !important;
                                            align-items: center !important;
                                            margin-top: 0 !important;
                                            padding: 0 2px !important;
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-picker-filter-summary strong {
                                            font-size: 8px !important;
                                        }

                                        /* Mais itens visíveis simultaneamente. */
                                        .sb-shop-picker-items {
                                            gap: 6px !important;
                                            padding: 7px 9px 10px !important;
                                        }

                                        .sb-shop-picker-item {
                                            grid-template-columns:
                                                66px
                                                minmax(0,1fr)
                                                26px !important;
                                            gap: 10px !important;
                                            min-height: 84px !important;
                                            border-radius: 12px !important;
                                            padding: 7px !important;
                                        }

                                        .sb-shop-picker-item__image {
                                            width: 66px !important;
                                            height: 66px !important;
                                            border-radius: 10px !important;
                                        }

                                        .sb-shop-picker-item__meta {
                                            gap: 4px !important;
                                        }

                                        .sb-shop-picker-item__nature,
                                        .sb-shop-picker-item__rarity {
                                            min-height: 18px !important;
                                            padding: 0 6px !important;
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-picker-item h4 {
                                            margin-top: 4px !important;
                                            font-size: 15px !important;
                                        }

                                        .sb-shop-picker-item p {
                                            margin-top: 4px !important;
                                            font-size: 8.5px !important;
                                            line-height: 1.35 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        .sb-shop-picker-item__select {
                                            width: 25px !important;
                                            height: 25px !important;
                                        }

                                        /* ============================================================
                                           CONFIGURAÇÃO — menos altura, mais leitura
                                        ============================================================ */

                                        .sb-shop-picker-config__content {
                                            gap: 8px !important;
                                            padding: 9px 10px !important;
                                        }

                                        .sb-shop-picker-selected {
                                            grid-template-columns:
                                                68px
                                                minmax(0,1fr) !important;
                                            gap: 10px !important;
                                            border-radius: 12px !important;
                                            padding: 8px !important;
                                        }

                                        .sb-shop-picker-selected__image {
                                            width: 68px !important;
                                            height: 68px !important;
                                            border-radius: 10px !important;
                                        }

                                        .sb-shop-picker-selected__copy > span {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            margin-top: 2px !important;
                                            font-size: 18px !important;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            margin-top: 3px !important;
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-picker-selected__copy small {
                                            margin-top: 3px !important;
                                            font-size: 7.5px !important;
                                            line-height: 1.3 !important;
                                            -webkit-line-clamp: 1 !important;
                                        }

                                        .sb-shop-picker-config-card {
                                            border-radius: 12px !important;
                                            padding: 8px !important;
                                        }

                                        .sb-shop-picker-config-card__head {
                                            align-items: center !important;
                                            margin-bottom: 6px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > span {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > strong {
                                            margin-top: 1px !important;
                                            font-size: 13px !important;
                                        }

                                        .sb-shop-picker-config-card__head > small {
                                            max-width: 170px !important;
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-config-card__head > button {
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-quantity-stepper {
                                            grid-template-columns:
                                                38px
                                                minmax(0,1fr)
                                                38px !important;
                                            gap: 5px !important;
                                        }

                                        .sb-shop-quantity-stepper button,
                                        .sb-shop-quantity-stepper input {
                                            height: 34px !important;
                                        }

                                        .sb-shop-quantity-stepper input {
                                            font-size: 14px !important;
                                        }

                                        /* Moedas: continuam 5 lado a lado, mas com menos altura. */
                                        .sb-shop-price-coins {
                                            gap: 5px !important;
                                        }

                                        .sb-shop-price-coin {
                                            border-radius: 10px !important;
                                            padding: 5px !important;
                                        }

                                        .sb-shop-price-coin__icon {
                                            width: 23px !important;
                                            height: 23px !important;
                                            flex-basis: 23px !important;
                                        }

                                        .sb-shop-price-coin__icon svg {
                                            width: 15px !important;
                                            height: 15px !important;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            font-size: 5.5px !important;
                                        }

                                        .sb-shop-price-coin__stepper {
                                            grid-template-columns:
                                                22px
                                                minmax(0,1fr)
                                                22px !important;
                                            gap: 2px !important;
                                            margin-top: 5px !important;
                                        }

                                        .sb-shop-price-coin__stepper button,
                                        .sb-shop-price-coin__stepper input {
                                            height: 27px !important;
                                        }

                                        .sb-shop-price-coin__stepper input {
                                            font-size: 10px !important;
                                        }

                                        .sb-shop-price-summary {
                                            margin-top: 6px !important;
                                            padding-top: 6px !important;
                                        }

                                        .sb-shop-price-summary span {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-price-summary strong {
                                            margin-top: 1px !important;
                                            font-size: 12px !important;
                                        }

                                        .sb-shop-price-summary small {
                                            max-width: 145px !important;
                                            font-size: 6.5px !important;
                                        }

                                        /* Footer mais fino para devolver área útil ao formulário. */
                                        .sb-shop-picker-submit {
                                            min-height: 56px !important;
                                            padding: 7px 10px !important;
                                        }

                                        .sb-shop-picker-submit > div > span {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            margin-top: 1px !important;
                                            font-size: 12px !important;
                                        }

                                        .sb-shop-picker-submit > button {
                                            min-width: 155px !important;
                                            min-height: 36px !important;
                                            border-radius: 9px !important;
                                            font-size: 7.5px !important;
                                        }

                                        /* ============================================================
                                           VIEWPORT BAIXO — caso exato de telas ~600–700px de altura
                                        ============================================================ */

                                        @media (max-height: 720px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] {
                                                padding: 8px !important;
                                            }

                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: calc(100vw - 16px) !important;
                                                height: calc(100dvh - 16px) !important;
                                                border-radius: 14px !important;
                                            }

                                            .sb-shop-picker-head {
                                                min-height: 62px !important;
                                                padding: 7px 12px !important;
                                            }

                                            .sb-shop-picker-head h3 {
                                                font-size: 20px !important;
                                            }

                                            .sb-shop-picker-head p {
                                                font-size: 8px !important;
                                            }

                                            .sb-shop-picker-filters {
                                                padding: 6px 8px 4px !important;
                                            }

                                            .sb-shop-picker-search {
                                                height: 34px !important;
                                            }

                                            .sb-shop-picker-nature button {
                                                min-height: 27px !important;
                                            }

                                            .sb-shop-picker-rarity {
                                                min-height: 33px !important;
                                            }

                                            .sb-shop-picker-filter-summary {
                                                min-height: 14px !important;
                                            }

                                            .sb-shop-picker-items {
                                                gap: 5px !important;
                                                padding: 6px 8px 8px !important;
                                            }

                                            .sb-shop-picker-item {
                                                grid-template-columns:
                                                    58px
                                                    minmax(0,1fr)
                                                    24px !important;
                                                min-height: 72px !important;
                                                gap: 8px !important;
                                                padding: 6px !important;
                                            }

                                            .sb-shop-picker-item__image {
                                                width: 58px !important;
                                                height: 58px !important;
                                            }

                                            .sb-shop-picker-item p {
                                                -webkit-line-clamp: 1 !important;
                                            }

                                            .sb-shop-picker-config__content {
                                                gap: 6px !important;
                                                padding: 7px 8px !important;
                                            }

                                            .sb-shop-picker-selected {
                                                grid-template-columns:
                                                    60px
                                                    minmax(0,1fr) !important;
                                                padding: 6px !important;
                                            }

                                            .sb-shop-picker-selected__image {
                                                width: 60px !important;
                                                height: 60px !important;
                                            }

                                            .sb-shop-picker-selected__copy h4 {
                                                font-size: 17px !important;
                                            }

                                            .sb-shop-picker-selected__copy small {
                                                display: none !important;
                                            }

                                            .sb-shop-picker-config-card {
                                                padding: 7px !important;
                                            }

                                            .sb-shop-picker-config-card__head {
                                                margin-bottom: 5px !important;
                                            }

                                            .sb-shop-quantity-stepper button,
                                            .sb-shop-quantity-stepper input {
                                                height: 31px !important;
                                            }

                                            .sb-shop-price-coin {
                                                padding: 4px !important;
                                            }

                                            .sb-shop-price-coin__stepper {
                                                margin-top: 4px !important;
                                            }

                                            .sb-shop-price-coin__stepper button,
                                            .sb-shop-price-coin__stepper input {
                                                height: 25px !important;
                                            }

                                            .sb-shop-price-summary {
                                                margin-top: 4px !important;
                                                padding-top: 4px !important;
                                            }

                                            .sb-shop-picker-submit {
                                                min-height: 50px !important;
                                                padding: 6px 9px !important;
                                            }

                                            .sb-shop-picker-submit > button {
                                                min-height: 34px !important;
                                            }
                                        }

                                        /* Largura intermediária: evita apertar moedas. */
                                        @media (max-width: 1180px) and (min-width: 821px) {
                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(455px,.9fr) !important;
                                            }

                                            .sb-shop-picker-filters {
                                                grid-template-columns: 1fr !important;
                                            }

                                            .sb-shop-picker-filter-row {
                                                margin-top: 0 !important;
                                            }

                                            .sb-shop-picker-filter-summary {
                                                grid-column: auto;
                                            }

                                            .sb-shop-price-coins {
                                                grid-template-columns:
                                                    repeat(5,minmax(0,1fr)) !important;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V9 — PONTO DE EQUILÍBRIO
                                           Menor que o V8, tipografia maior e coluna direita preenchida.
                                        ============================================================ */

                                        [x-show="shopPickerOpen && lootActiveShop"] {
                                            padding: 20px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            width: min(
                                                1180px,
                                                calc(100vw - 40px)
                                            ) !important;
                                            height: min(
                                                600px,
                                                calc(100dvh - 40px)
                                            ) !important;
                                            max-width: none !important;
                                            max-height: none !important;
                                            border-radius: 17px !important;
                                        }

                                        /* Header: menor que o V7, mas legível. */
                                        .sb-shop-picker-head {
                                            min-height: 66px !important;
                                            padding: 9px 15px !important;
                                        }

                                        .sb-shop-picker-head__eyebrow {
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-picker-head h3 {
                                            margin-top: 1px !important;
                                            font-size: 23px !important;
                                        }

                                        .sb-shop-picker-head p {
                                            margin-top: 3px !important;
                                            font-size: 9px !important;
                                        }

                                        .sb-shop-price-help-trigger {
                                            min-height: 35px !important;
                                            padding: 0 11px !important;
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-picker-close {
                                            width: 35px !important;
                                            height: 35px !important;
                                        }

                                        /* Proporção equilibrada: catálogo amplo sem esmagar preço. */
                                        .sb-shop-picker-layout {
                                            grid-template-columns:
                                                minmax(0, 1.08fr)
                                                minmax(475px, .92fr) !important;
                                        }

                                        /* ============================================================
                                           FILTROS
                                        ============================================================ */

                                        .sb-shop-picker-filters {
                                            grid-template-columns:
                                                minmax(260px, 1fr)
                                                minmax(360px, .96fr) !important;
                                            gap: 7px 8px !important;
                                            padding: 8px 10px 6px !important;
                                        }

                                        .sb-shop-picker-search {
                                            height: 39px !important;
                                        }

                                        .sb-shop-picker-search input {
                                            font-size: 10.5px !important;
                                        }

                                        .sb-shop-picker-filter-row {
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                168px !important;
                                            gap: 6px !important;
                                        }

                                        .sb-shop-picker-nature button {
                                            min-height: 31px !important;
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-picker-rarity {
                                            min-height: 37px !important;
                                        }

                                        .sb-shop-picker-rarity > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-rarity select {
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            min-height: 18px !important;
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-picker-filter-summary strong {
                                            font-size: 8.5px !important;
                                        }

                                        /* ============================================================
                                           CATÁLOGO
                                        ============================================================ */

                                        .sb-shop-picker-items {
                                            gap: 6px !important;
                                            padding: 7px 9px 10px !important;
                                        }

                                        .sb-shop-picker-item {
                                            grid-template-columns:
                                                64px
                                                minmax(0,1fr)
                                                27px !important;
                                            gap: 10px !important;
                                            min-height: 86px !important;
                                            padding: 7px !important;
                                        }

                                        .sb-shop-picker-item__image {
                                            width: 64px !important;
                                            height: 64px !important;
                                        }

                                        .sb-shop-picker-item__nature,
                                        .sb-shop-picker-item__rarity {
                                            min-height: 19px !important;
                                            padding: 0 6px !important;
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-item h4 {
                                            margin-top: 4px !important;
                                            font-size: 16px !important;
                                        }

                                        .sb-shop-picker-item p {
                                            margin-top: 4px !important;
                                            font-size: 9px !important;
                                            line-height: 1.38 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        /* ============================================================
                                           PAINEL DIREITO
                                        ============================================================ */

                                        .sb-shop-picker-config__content {
                                            gap: 8px !important;
                                            padding: 9px 10px !important;
                                        }

                                        .sb-shop-picker-selected {
                                            grid-template-columns:
                                                72px
                                                minmax(0,1fr) !important;
                                            min-height: 92px !important;
                                            gap: 10px !important;
                                            padding: 8px !important;
                                        }

                                        .sb-shop-picker-selected__image {
                                            width: 72px !important;
                                            height: 72px !important;
                                        }

                                        .sb-shop-picker-selected__copy > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            font-size: 20px !important;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-picker-selected__copy small {
                                            margin-top: 4px !important;
                                            font-size: 8.5px !important;
                                            line-height: 1.35 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        .sb-shop-picker-config-card {
                                            padding: 9px !important;
                                        }

                                        .sb-shop-picker-config-card__head {
                                            margin-bottom: 7px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > strong {
                                            font-size: 14px !important;
                                        }

                                        .sb-shop-picker-config-card__head > small {
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-picker-config-card__head > button {
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-quantity-stepper {
                                            grid-template-columns:
                                                40px
                                                minmax(0,1fr)
                                                40px !important;
                                        }

                                        .sb-shop-quantity-stepper button,
                                        .sb-shop-quantity-stepper input {
                                            height: 36px !important;
                                        }

                                        .sb-shop-quantity-stepper input {
                                            font-size: 15px !important;
                                        }

                                        /*
                                         * O preço cresce um pouco para ocupar a área útil da direita,
                                         * eliminando aquele "buraco" visual entre moedas e footer.
                                         */
                                        .sb-shop-picker-config-card.is-price {
                                            min-height: 205px !important;
                                            display: flex;
                                            flex-direction: column;
                                            justify-content: flex-start;
                                        }

                                        .sb-shop-price-coins {
                                            gap: 6px !important;
                                        }

                                        .sb-shop-price-coin {
                                            min-height: 86px !important;
                                            padding: 6px !important;
                                        }

                                        .sb-shop-price-coin__icon {
                                            width: 26px !important;
                                            height: 26px !important;
                                            flex-basis: 26px !important;
                                        }

                                        .sb-shop-price-coin__icon svg {
                                            width: 17px !important;
                                            height: 17px !important;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-price-coin__stepper {
                                            grid-template-columns:
                                                23px
                                                minmax(0,1fr)
                                                23px !important;
                                            gap: 3px !important;
                                            margin-top: 6px !important;
                                        }

                                        .sb-shop-price-coin__stepper button,
                                        .sb-shop-price-coin__stepper input {
                                            height: 29px !important;
                                        }

                                        .sb-shop-price-coin__stepper input {
                                            font-size: 11px !important;
                                        }

                                        .sb-shop-price-summary {
                                            margin-top: auto !important;
                                            padding-top: 8px !important;
                                        }

                                        .sb-shop-price-summary span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-price-summary strong {
                                            font-size: 13px !important;
                                        }

                                        .sb-shop-price-summary small {
                                            font-size: 7px !important;
                                        }

                                        /* Footer: firme, mas não enorme. */
                                        .sb-shop-picker-submit {
                                            min-height: 57px !important;
                                            padding: 7px 10px !important;
                                        }

                                        .sb-shop-picker-submit > div > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            font-size: 12.5px !important;
                                        }

                                        .sb-shop-picker-submit > button {
                                            min-width: 158px !important;
                                            min-height: 37px !important;
                                            font-size: 8px !important;
                                        }

                                        /* ============================================================
                                           TELA BAIXA: mantém tudo visível sem estourar viewport.
                                        ============================================================ */

                                        @media (max-height: 680px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] {
                                                padding: 14px !important;
                                            }

                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: min(
                                                    1180px,
                                                    calc(100vw - 28px)
                                                ) !important;
                                                height: calc(100dvh - 28px) !important;
                                            }

                                            .sb-shop-picker-head {
                                                min-height: 61px !important;
                                                padding: 7px 13px !important;
                                            }

                                            .sb-shop-picker-head h3 {
                                                font-size: 21px !important;
                                            }

                                            .sb-shop-picker-filters {
                                                padding: 6px 8px 4px !important;
                                            }

                                            .sb-shop-picker-search {
                                                height: 35px !important;
                                            }

                                            .sb-shop-picker-nature button {
                                                min-height: 28px !important;
                                            }

                                            .sb-shop-picker-rarity {
                                                min-height: 34px !important;
                                            }

                                            .sb-shop-picker-filter-summary {
                                                min-height: 15px !important;
                                            }

                                            .sb-shop-picker-item {
                                                grid-template-columns:
                                                    58px
                                                    minmax(0,1fr)
                                                    24px !important;
                                                min-height: 74px !important;
                                                gap: 8px !important;
                                                padding: 6px !important;
                                            }

                                            .sb-shop-picker-item__image {
                                                width: 58px !important;
                                                height: 58px !important;
                                            }

                                            .sb-shop-picker-item p {
                                                font-size: 8.5px !important;
                                                -webkit-line-clamp: 1 !important;
                                            }

                                            .sb-shop-picker-config__content {
                                                gap: 6px !important;
                                                padding: 7px 8px !important;
                                            }

                                            .sb-shop-picker-selected {
                                                grid-template-columns:
                                                    62px
                                                    minmax(0,1fr) !important;
                                                min-height: 76px !important;
                                                padding: 6px !important;
                                            }

                                            .sb-shop-picker-selected__image {
                                                width: 62px !important;
                                                height: 62px !important;
                                            }

                                            .sb-shop-picker-selected__copy h4 {
                                                font-size: 18px !important;
                                            }

                                            .sb-shop-picker-selected__copy small {
                                                -webkit-line-clamp: 1 !important;
                                            }

                                            .sb-shop-picker-config-card {
                                                padding: 7px !important;
                                            }

                                            .sb-shop-quantity-stepper button,
                                            .sb-shop-quantity-stepper input {
                                                height: 32px !important;
                                            }

                                            .sb-shop-picker-config-card.is-price {
                                                min-height: 176px !important;
                                            }

                                            .sb-shop-price-coin {
                                                min-height: 72px !important;
                                                padding: 4px !important;
                                            }

                                            .sb-shop-price-coin__icon {
                                                width: 23px !important;
                                                height: 23px !important;
                                                flex-basis: 23px !important;
                                            }

                                            .sb-shop-price-coin__stepper {
                                                margin-top: 4px !important;
                                            }

                                            .sb-shop-price-coin__stepper button,
                                            .sb-shop-price-coin__stepper input {
                                                height: 25px !important;
                                            }

                                            .sb-shop-price-summary {
                                                padding-top: 5px !important;
                                            }

                                            .sb-shop-picker-submit {
                                                min-height: 50px !important;
                                                padding: 6px 9px !important;
                                            }

                                            .sb-shop-picker-submit > button {
                                                min-height: 34px !important;
                                            }
                                        }

                                        @media (max-width: 1180px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: calc(100vw - 32px) !important;
                                            }

                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(450px,.92fr) !important;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V10 — NÚMEROS GRANDES NAS MOEDAS
                                        ============================================================ */

                                        /*
                                         * O input de number nativo reserva espaço para os spinners do
                                         * navegador. Em cards estreitos isso fazia 1900, 10000 etc.
                                         * invadirem os botões laterais.
                                         */
                                        .sb-shop-price-coin__stepper {
                                            grid-template-columns:
                                                21px
                                                minmax(0,1fr)
                                                21px !important;
                                            gap: 3px !important;
                                        }

                                        .sb-shop-price-coin__stepper input {
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            max-width: 100% !important;
                                            padding: 0 2px !important;
                                            overflow: hidden;
                                            appearance: textfield;
                                            -moz-appearance: textfield;
                                            font-size: 10px !important;
                                            font-variant-numeric: tabular-nums;
                                            letter-spacing: -.02em;
                                            text-overflow: clip;
                                        }

                                        .sb-shop-price-coin__stepper input::-webkit-outer-spin-button,
                                        .sb-shop-price-coin__stepper input::-webkit-inner-spin-button {
                                            margin: 0;
                                            -webkit-appearance: none;
                                        }

                                        .sb-shop-price-coin__stepper input.is-long {
                                            font-size: 8.5px !important;
                                            letter-spacing: -.045em;
                                        }

                                        .sb-shop-price-coin__stepper input.is-very-long {
                                            font-size: 7px !important;
                                            letter-spacing: -.065em;
                                        }

                                        .sb-shop-price-coin__stepper button {
                                            width: 21px !important;
                                            min-width: 21px !important;
                                            padding: 0 !important;
                                        }

                                        /*
                                         * No resumo podemos ter valores muito maiores; ele quebra
                                         * apenas entre moedas, nunca no meio de um número.
                                         */
                                        .sb-shop-price-summary {
                                            min-width: 0;
                                        }

                                        .sb-shop-price-summary > div {
                                            min-width: 0;
                                        }

                                        .sb-shop-price-summary strong {
                                            overflow-wrap: normal;
                                            word-break: keep-all;
                                            white-space: normal;
                                            line-height: 1.25;
                                        }

                                        @media (max-height: 680px) and (min-width: 821px) {
                                            .sb-shop-price-coin__stepper {
                                                grid-template-columns:
                                                    19px
                                                    minmax(0,1fr)
                                                    19px !important;
                                                gap: 2px !important;
                                            }

                                            .sb-shop-price-coin__stepper button {
                                                width: 19px !important;
                                                min-width: 19px !important;
                                            }

                                            .sb-shop-price-coin__stepper input {
                                                font-size: 9px !important;
                                            }

                                            .sb-shop-price-coin__stepper input.is-long {
                                                font-size: 7.75px !important;
                                            }

                                            .sb-shop-price-coin__stepper input.is-very-long {
                                                font-size: 6.5px !important;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V11 — UNIFORMIZAÇÃO / MOEDAS MAIS IMPORTANTES
                                        ============================================================ */

                                        /*
                                         * Mantém o modal no ponto de equilíbrio do V9,
                                         * mas entrega um pouco mais da largura à configuração/preço.
                                         */
                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            width: min(
                                                1210px,
                                                calc(100vw - 40px)
                                            ) !important;
                                        }

                                        .sb-shop-picker-layout {
                                            grid-template-columns:
                                                minmax(0, 1fr)
                                                minmax(520px, .98fr) !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Catálogo: tipografia coerente e descrição realmente legível.
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-item {
                                            grid-template-columns:
                                                66px
                                                minmax(0,1fr)
                                                27px !important;
                                            min-height: 90px !important;
                                        }

                                        .sb-shop-picker-item__image {
                                            width: 66px !important;
                                            height: 66px !important;
                                        }

                                        .sb-shop-picker-item__nature,
                                        .sb-shop-picker-item__rarity {
                                            font-size: 6.75px !important;
                                            letter-spacing: .035em !important;
                                        }

                                        .sb-shop-picker-item h4 {
                                            font-size: 16.5px !important;
                                            line-height: 1.08 !important;
                                        }

                                        .sb-shop-picker-item p {
                                            margin-top: 5px !important;
                                            color: #684f3e !important;
                                            font-size: 10px !important;
                                            font-weight: 600 !important;
                                            line-height: 1.45 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-picker-filter-summary strong {
                                            font-size: 9px !important;
                                        }

                                        /* Item selecionado: mesma escala tipográfica do catálogo. */
                                        .sb-shop-picker-selected__copy > span {
                                            font-size: 6.75px !important;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            font-size: 20px !important;
                                            line-height: 1.05 !important;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            font-size: 8.75px !important;
                                        }

                                        .sb-shop-picker-selected__copy small {
                                            color: #684f3e !important;
                                            font-size: 9.25px !important;
                                            font-weight: 600 !important;
                                            line-height: 1.4 !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Bloco de preço ganha maior relevância.
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-config-card.is-price {
                                            border-color: #cbaa82 !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf9 0%,
                                                    #f9f1e5 100%
                                                ) !important;
                                            box-shadow:
                                                0 7px 18px rgba(83,21,15,.045),
                                                inset 0 1px 0 rgba(255,255,255,.86);
                                        }

                                        .sb-shop-picker-config-card.is-price
                                        .sb-shop-picker-config-card__head > div > span {
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-picker-config-card.is-price
                                        .sb-shop-picker-config-card__head > div > strong {
                                            font-size: 15px !important;
                                        }

                                        .sb-shop-price-coins {
                                            grid-template-columns:
                                                repeat(5, minmax(0,1fr)) !important;
                                            gap: 7px !important;
                                        }

                                        .sb-shop-price-coin {
                                            min-width: 0;
                                            min-height: 92px !important;
                                            border-radius: 11px !important;
                                            padding: 7px !important;
                                        }

                                        .sb-shop-price-coin__identity {
                                            gap: 6px !important;
                                        }

                                        .sb-shop-price-coin__icon {
                                            width: 28px !important;
                                            height: 28px !important;
                                            flex-basis: 28px !important;
                                        }

                                        .sb-shop-price-coin__icon svg {
                                            width: 18px !important;
                                            height: 18px !important;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            font-size: 9px !important;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            font-size: 6.25px !important;
                                        }

                                        /*
                                         * Mais largura útil no centro. O input é TEXT apenas para
                                         * exibir "1,000 / 10,000 / 100,000"; o estado continua numérico.
                                         */
                                        .sb-shop-price-coin__stepper {
                                            grid-template-columns:
                                                20px
                                                minmax(0,1fr)
                                                20px !important;
                                            gap: 3px !important;
                                            margin-top: 7px !important;
                                        }

                                        .sb-shop-price-coin__stepper button {
                                            width: 20px !important;
                                            min-width: 20px !important;
                                            height: 31px !important;
                                            padding: 0 !important;
                                            font-size: 13px !important;
                                        }

                                        .sb-shop-price-coin__stepper input {
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            max-width: 100% !important;
                                            height: 31px !important;
                                            padding: 0 2px !important;
                                            overflow: hidden;
                                            appearance: none;
                                            -moz-appearance: textfield;
                                            color: #53150f !important;
                                            font-family: Georgia, serif !important;
                                            font-size: 10.5px !important;
                                            font-weight: 900 !important;
                                            font-variant-numeric: tabular-nums;
                                            letter-spacing: -.025em;
                                            text-align: center;
                                            text-overflow: clip;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-price-coin__stepper input.is-long {
                                            font-size: 9px !important;
                                            letter-spacing: -.04em !important;
                                        }

                                        .sb-shop-price-coin__stepper input.is-very-long {
                                            font-size: 7.6px !important;
                                            letter-spacing: -.055em !important;
                                        }

                                        .sb-shop-price-summary {
                                            gap: 14px !important;
                                            border-top-color: #ddc7aa !important;
                                        }

                                        .sb-shop-price-summary span {
                                            font-size: 6.75px !important;
                                        }

                                        .sb-shop-price-summary strong {
                                            color: #53150f !important;
                                            font-size: 14px !important;
                                            font-variant-numeric: tabular-nums;
                                            line-height: 1.2 !important;
                                            word-break: normal !important;
                                            overflow-wrap: normal !important;
                                        }

                                        .sb-shop-price-summary small {
                                            max-width: 190px !important;
                                            font-size: 7.5px !important;
                                            line-height: 1.4 !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Uniformização das microtipografias do painel direito.
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-config-card__head > div > span,
                                        .sb-shop-picker-submit > div > span {
                                            letter-spacing: .08em !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > strong {
                                            font-size: 14px !important;
                                        }

                                        .sb-shop-picker-config-card__head > small {
                                            color: #806650 !important;
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            font-size: 13px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Viewport baixo: preserva leitura e reduz apenas o necessário.
                                        ------------------------------------------------------------ */

                                        @media (max-height: 680px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: min(
                                                    1210px,
                                                    calc(100vw - 28px)
                                                ) !important;
                                            }

                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(505px,.98fr) !important;
                                            }

                                            .sb-shop-picker-item {
                                                grid-template-columns:
                                                    59px
                                                    minmax(0,1fr)
                                                    24px !important;
                                                min-height: 76px !important;
                                            }

                                            .sb-shop-picker-item__image {
                                                width: 59px !important;
                                                height: 59px !important;
                                            }

                                            .sb-shop-picker-item h4 {
                                                font-size: 15.5px !important;
                                            }

                                            .sb-shop-picker-item p {
                                                font-size: 9.25px !important;
                                                -webkit-line-clamp: 1 !important;
                                            }

                                            .sb-shop-price-coin {
                                                min-height: 78px !important;
                                                padding: 5px !important;
                                            }

                                            .sb-shop-price-coin__icon {
                                                width: 24px !important;
                                                height: 24px !important;
                                                flex-basis: 24px !important;
                                            }

                                            .sb-shop-price-coin__stepper {
                                                grid-template-columns:
                                                    19px
                                                    minmax(0,1fr)
                                                    19px !important;
                                            }

                                            .sb-shop-price-coin__stepper button {
                                                width: 19px !important;
                                                min-width: 19px !important;
                                                height: 27px !important;
                                            }

                                            .sb-shop-price-coin__stepper input {
                                                height: 27px !important;
                                                font-size: 9.5px !important;
                                            }

                                            .sb-shop-price-coin__stepper input.is-long {
                                                font-size: 8.2px !important;
                                            }

                                            .sb-shop-price-coin__stepper input.is-very-long {
                                                font-size: 7px !important;
                                            }
                                        }

                                        @media (max-width: 1180px) and (min-width: 821px) {
                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(490px,.98fr) !important;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V12 — REDESIGN
                                           Menos caixas, mais hierarquia, contraste e ritmo visual.
                                        ============================================================ */

                                        [x-show="shopPickerOpen && lootActiveShop"] {
                                            padding: 24px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            width: min(
                                                1160px,
                                                calc(100vw - 48px)
                                            ) !important;
                                            height: min(
                                                560px,
                                                calc(100dvh - 48px)
                                            ) !important;
                                            max-width: none !important;
                                            max-height: none !important;
                                            overflow: hidden !important;
                                            border:
                                                1px solid
                                                rgba(176,140,98,.52) !important;
                                            border-radius: 18px !important;
                                            background: #f7f1e8 !important;
                                            box-shadow:
                                                0 34px 90px rgba(25,15,10,.34),
                                                inset 0 1px 0 rgba(255,255,255,.82) !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Header
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-head {
                                            min-height: 70px !important;
                                            border-bottom:
                                                1px solid
                                                rgba(176,140,98,.28) !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdfa 0%,
                                                    #fbf5ed 100%
                                                ) !important;
                                            padding: 10px 15px !important;
                                        }

                                        .sb-shop-picker-head__brand {
                                            display: flex;
                                            min-width: 0;
                                            align-items: center;
                                            gap: 11px;
                                        }

                                        .sb-shop-picker-head__brand-icon {
                                            display: grid;
                                            width: 40px;
                                            height: 40px;
                                            flex: 0 0 40px;
                                            place-items: center;
                                            border:
                                                1px solid
                                                rgba(138,75,32,.30);
                                            border-radius: 11px;
                                            background:
                                                linear-gradient(
                                                    145deg,
                                                    #f2e1cf,
                                                    #fffaf2
                                                );
                                            color: #8a4b20;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.8);
                                        }

                                        .sb-shop-picker-head__brand-icon svg {
                                            width: 20px;
                                            height: 20px;
                                        }

                                        .sb-shop-picker-head__eyebrow {
                                            font-size: 7px !important;
                                            letter-spacing: .13em !important;
                                        }

                                        .sb-shop-picker-head h3 {
                                            margin-top: 1px !important;
                                            font-size: 23px !important;
                                            line-height: 1 !important;
                                        }

                                        .sb-shop-picker-head p {
                                            margin-top: 4px !important;
                                            color: #806650 !important;
                                            font-size: 9.25px !important;
                                        }

                                        .sb-shop-price-help-trigger {
                                            min-height: 36px !important;
                                            border-radius: 10px !important;
                                            background: #fffaf3 !important;
                                            font-size: 8px !important;
                                        }

                                        .sb-shop-picker-close {
                                            width: 36px !important;
                                            height: 36px !important;
                                            border-radius: 10px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Painéis
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-layout {
                                            grid-template-columns:
                                                minmax(0, 1.06fr)
                                                minmax(485px, .94fr) !important;
                                            background: #f5eee4;
                                        }

                                        .sb-shop-picker-catalog,
                                        .sb-shop-picker-config {
                                            min-height: 0;
                                        }

                                        .sb-shop-picker-catalog {
                                            border-right:
                                                1px solid
                                                rgba(176,140,98,.25) !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #f6efe5 0%,
                                                    #f2eadf 100%
                                                ) !important;
                                        }

                                        .sb-shop-picker-config {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fbf8f2 0%,
                                                    #f7f0e6 100%
                                                ) !important;
                                        }

                                        .sb-shop-picker-pane-head {
                                            display: flex;
                                            min-height: 45px;
                                            flex: 0 0 auto;
                                            align-items: center;
                                            justify-content: space-between;
                                            gap: 12px;
                                            border-bottom:
                                                1px solid
                                                rgba(176,140,98,.22);
                                            padding: 8px 11px;
                                        }

                                        .sb-shop-picker-pane-head.is-config {
                                            background:
                                                rgba(255,253,248,.55);
                                        }

                                        .sb-shop-picker-pane-head > div > span {
                                            display: block;
                                            color: #9a806d;
                                            font-size: 6.5px;
                                            font-weight: 900;
                                            letter-spacing: .11em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-picker-pane-head > div > strong {
                                            display: block;
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-pane-count,
                                        .sb-shop-picker-pane-step {
                                            display: inline-flex;
                                            min-height: 26px;
                                            align-items: center;
                                            gap: 4px;
                                            border:
                                                1px solid
                                                rgba(176,140,98,.28);
                                            border-radius: 999px;
                                            background: rgba(255,253,248,.82);
                                            padding: 0 9px;
                                            color: #8b715d;
                                            font-size: 7px;
                                            font-weight: 800;
                                        }

                                        .sb-shop-picker-pane-count strong {
                                            color: #53150f;
                                            font-size: 8px;
                                        }

                                        /* ------------------------------------------------------------
                                           Filtros: uma toolbar coesa
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-filters {
                                            display: grid !important;
                                            grid-template-columns:
                                                minmax(230px, 1fr)
                                                minmax(335px, 1.02fr) !important;
                                            gap: 7px 8px !important;
                                            border-bottom:
                                                1px solid
                                                rgba(176,140,98,.20) !important;
                                            background:
                                                rgba(255,253,248,.48) !important;
                                            padding: 8px 10px 6px !important;
                                        }

                                        .sb-shop-picker-search {
                                            height: 38px !important;
                                            border-color:
                                                rgba(176,140,98,.38) !important;
                                            background:
                                                rgba(255,253,248,.92) !important;
                                            box-shadow: none !important;
                                        }

                                        .sb-shop-picker-search input {
                                            font-size: 10.5px !important;
                                        }

                                        .sb-shop-picker-filter-row {
                                            grid-template-columns:
                                                minmax(0,1fr)
                                                160px !important;
                                            gap: 6px !important;
                                            margin-top: 0 !important;
                                        }

                                        .sb-shop-picker-nature {
                                            border-color:
                                                rgba(176,140,98,.30) !important;
                                            background: #e9ddcf !important;
                                        }

                                        .sb-shop-picker-nature button {
                                            min-height: 30px !important;
                                            font-size: 7.25px !important;
                                        }

                                        .sb-shop-picker-nature button.is-active {
                                            background: #fffdf8 !important;
                                            color: #6b1d14 !important;
                                            box-shadow:
                                                0 3px 9px rgba(83,21,15,.05) !important;
                                        }

                                        .sb-shop-picker-rarity {
                                            min-height: 36px !important;
                                            border-color:
                                                rgba(176,140,98,.34) !important;
                                            background:
                                                rgba(255,253,248,.92) !important;
                                        }

                                        .sb-shop-picker-rarity > span {
                                            font-size: 6.25px !important;
                                        }

                                        .sb-shop-picker-rarity select {
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            grid-column: 1 / -1;
                                            min-height: 17px !important;
                                            margin-top: 0 !important;
                                            padding: 0 2px !important;
                                            color: #927864 !important;
                                            font-size: 7.5px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Lista: menos "cartão", mais catálogo
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-items {
                                            gap: 5px !important;
                                            padding: 7px 8px 10px !important;
                                        }

                                        .sb-shop-picker-item {
                                            position: relative;
                                            grid-template-columns:
                                                62px
                                                minmax(0,1fr)
                                                26px !important;
                                            gap: 10px !important;
                                            min-height: 80px !important;
                                            border-color:
                                                rgba(176,140,98,.26) !important;
                                            border-radius: 11px !important;
                                            background:
                                                rgba(255,253,248,.70) !important;
                                            padding: 7px !important;
                                            box-shadow: none !important;
                                        }

                                        .sb-shop-picker-item::before {
                                            content: "";
                                            position: absolute;
                                            left: 0;
                                            top: 9px;
                                            bottom: 9px;
                                            width: 3px;
                                            border-radius: 0 4px 4px 0;
                                            background: transparent;
                                        }

                                        .sb-shop-picker-item:hover {
                                            transform: none !important;
                                            border-color:
                                                rgba(138,75,32,.38) !important;
                                            background: #fffdf8 !important;
                                            box-shadow:
                                                0 5px 14px rgba(83,21,15,.05) !important;
                                        }

                                        .sb-shop-picker-item.is-selected {
                                            border-color:
                                                rgba(138,75,32,.58) !important;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    #fffaf3,
                                                    #fffdf8 74%
                                                ) !important;
                                            box-shadow:
                                                0 7px 17px rgba(83,21,15,.07) !important;
                                        }

                                        .sb-shop-picker-item.is-selected::before {
                                            background: #8a4b20;
                                        }

                                        .sb-shop-picker-item__image {
                                            width: 62px !important;
                                            height: 62px !important;
                                            border-radius: 10px !important;
                                            background:
                                                linear-gradient(
                                                    145deg,
                                                    #eee1d1,
                                                    #fffaf3
                                                ) !important;
                                        }

                                        .sb-shop-picker-item__nature,
                                        .sb-shop-picker-item__rarity {
                                            min-height: 18px !important;
                                            padding: 0 6px !important;
                                            font-size: 6.25px !important;
                                        }

                                        .sb-shop-picker-item h4 {
                                            margin-top: 4px !important;
                                            font-size: 16px !important;
                                            line-height: 1.05 !important;
                                        }

                                        .sb-shop-picker-item p {
                                            margin-top: 5px !important;
                                            color: #62493a !important;
                                            font-size: 9.75px !important;
                                            font-weight: 600 !important;
                                            line-height: 1.4 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        .sb-shop-picker-item__select {
                                            width: 25px !important;
                                            height: 25px !important;
                                            background:
                                                rgba(255,253,248,.80) !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Configuração: 3 etapas visuais
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-config__content {
                                            position: relative;
                                            display: grid !important;
                                            gap: 7px !important;
                                            align-content: start !important;
                                            padding: 8px 10px 9px 20px !important;
                                        }

                                        .sb-shop-picker-config__content::before {
                                            content: "";
                                            position: absolute;
                                            left: 8px;
                                            top: 20px;
                                            bottom: 20px;
                                            width: 1px;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    rgba(138,75,32,.35),
                                                    rgba(138,75,32,.08)
                                                );
                                        }

                                        .sb-shop-picker-selected,
                                        .sb-shop-picker-config-card {
                                            position: relative;
                                            overflow: visible !important;
                                        }

                                        .sb-shop-picker-step-badge {
                                            position: absolute;
                                            left: -19px;
                                            top: 13px;
                                            z-index: 3;
                                            display: grid;
                                            width: 20px;
                                            height: 20px;
                                            place-items: center;
                                            border: 2px solid #f8f2e9;
                                            border-radius: 50%;
                                            background: #8a4b20;
                                            color: white;
                                            font-family: Georgia, serif;
                                            font-size: 8px;
                                            font-weight: 900;
                                            box-shadow:
                                                0 2px 7px rgba(83,21,15,.16);
                                        }

                                        .sb-shop-picker-selected {
                                            grid-template-columns:
                                                70px
                                                minmax(0,1fr) !important;
                                            gap: 10px !important;
                                            min-height: 88px !important;
                                            border:
                                                1px solid
                                                rgba(176,140,98,.34) !important;
                                            border-radius: 12px !important;
                                            background:
                                                linear-gradient(
                                                    135deg,
                                                    #f2e4d3 0%,
                                                    #fffdf8 64%
                                                ) !important;
                                            padding: 8px !important;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.76);
                                        }

                                        .sb-shop-picker-selected__image {
                                            width: 70px !important;
                                            height: 70px !important;
                                            border-radius: 10px !important;
                                        }

                                        .sb-shop-picker-selected__copy > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            margin-top: 2px !important;
                                            font-size: 19px !important;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            margin-top: 3px !important;
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-picker-selected__copy small {
                                            margin-top: 4px !important;
                                            color: #62493a !important;
                                            font-size: 8.75px !important;
                                            font-weight: 600 !important;
                                            line-height: 1.35 !important;
                                            -webkit-line-clamp: 2 !important;
                                        }

                                        .sb-shop-picker-config-card {
                                            border:
                                                1px solid
                                                rgba(176,140,98,.30) !important;
                                            border-radius: 12px !important;
                                            background:
                                                rgba(255,253,248,.80) !important;
                                            padding: 8px !important;
                                            box-shadow: none !important;
                                        }

                                        .sb-shop-picker-config-card.is-price {
                                            min-height: 190px !important;
                                            border-color:
                                                rgba(138,75,32,.38) !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8 0%,
                                                    #f8eddf 100%
                                                ) !important;
                                            box-shadow:
                                                0 6px 16px rgba(83,21,15,.045) !important;
                                        }

                                        .sb-shop-picker-config-card__head {
                                            align-items: center !important;
                                            margin-bottom: 6px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-picker-config-card__head > div > strong {
                                            margin-top: 1px !important;
                                            font-size: 13.5px !important;
                                        }

                                        .sb-shop-picker-config-card__head > small,
                                        .sb-shop-picker-config-card__head > button {
                                            font-size: 7px !important;
                                        }

                                        .sb-shop-quantity-stepper {
                                            grid-template-columns:
                                                38px
                                                minmax(0,1fr)
                                                38px !important;
                                            gap: 5px !important;
                                        }

                                        .sb-shop-quantity-stepper button,
                                        .sb-shop-quantity-stepper input {
                                            height: 34px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Moedas: mais identidade, menos monotonia
                                        ------------------------------------------------------------ */

                                        .sb-shop-price-coins {
                                            grid-template-columns:
                                                repeat(5, minmax(0,1fr)) !important;
                                            gap: 6px !important;
                                        }

                                        .sb-shop-price-coin {
                                            min-height: 84px !important;
                                            border-radius: 10px !important;
                                            padding: 6px !important;
                                            background:
                                                rgba(255,253,248,.85) !important;
                                        }

                                        .sb-shop-price-coin.coin-copper {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fff9f4,
                                                    #f5e5db
                                                ) !important;
                                        }

                                        .sb-shop-price-coin.coin-silver {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fff,
                                                    #ececea
                                                ) !important;
                                        }

                                        .sb-shop-price-coin.coin-electrum {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf4,
                                                    #eee7c8
                                                ) !important;
                                        }

                                        .sb-shop-price-coin.coin-gold {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffaf0,
                                                    #f5e3ad
                                                ) !important;
                                        }

                                        .sb-shop-price-coin.coin-platinum {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fbfdfc,
                                                    #e7ece9
                                                ) !important;
                                        }

                                        .sb-shop-price-coin.is-active {
                                            transform: none !important;
                                            box-shadow:
                                                0 0 0 2px rgba(107,29,20,.07),
                                                0 6px 14px rgba(83,21,15,.055) !important;
                                        }

                                        .sb-shop-price-coin__icon {
                                            width: 26px !important;
                                            height: 26px !important;
                                            flex-basis: 26px !important;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            font-size: 5.8px !important;
                                        }

                                        .sb-shop-price-coin__stepper {
                                            grid-template-columns:
                                                20px
                                                minmax(0,1fr)
                                                20px !important;
                                            gap: 3px !important;
                                            margin-top: 6px !important;
                                        }

                                        .sb-shop-price-coin__stepper button,
                                        .sb-shop-price-coin__stepper input {
                                            height: 28px !important;
                                        }

                                        .sb-shop-price-summary {
                                            margin-top: auto !important;
                                            padding-top: 7px !important;
                                        }

                                        .sb-shop-price-summary span {
                                            font-size: 6.5px !important;
                                        }

                                        .sb-shop-price-summary strong {
                                            font-size: 13.5px !important;
                                        }

                                        .sb-shop-price-summary small {
                                            max-width: 155px !important;
                                            font-size: 6.75px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Footer
                                        ------------------------------------------------------------ */

                                        .sb-shop-picker-submit {
                                            min-height: 55px !important;
                                            border-top:
                                                1px solid
                                                rgba(176,140,98,.30) !important;
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #eee0ce,
                                                    #e8d5bd
                                                ) !important;
                                            padding: 7px 10px !important;
                                        }

                                        .sb-shop-picker-submit > div > span {
                                            font-size: 6.25px !important;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            font-size: 12.5px !important;
                                        }

                                        .sb-shop-picker-submit > button {
                                            min-width: 160px !important;
                                            min-height: 36px !important;
                                            border-radius: 9px !important;
                                            background: #6b1d14 !important;
                                            font-size: 8px !important;
                                        }

                                        /* ------------------------------------------------------------
                                           Viewports mais baixos
                                        ------------------------------------------------------------ */

                                        @media (max-height: 660px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] {
                                                padding: 14px !important;
                                            }

                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: min(
                                                    1160px,
                                                    calc(100vw - 28px)
                                                ) !important;
                                                height: calc(100dvh - 28px) !important;
                                            }

                                            .sb-shop-picker-head {
                                                min-height: 62px !important;
                                                padding: 7px 13px !important;
                                            }

                                            .sb-shop-picker-head__brand-icon {
                                                width: 34px;
                                                height: 34px;
                                                flex-basis: 34px;
                                            }

                                            .sb-shop-picker-head h3 {
                                                font-size: 21px !important;
                                            }

                                            .sb-shop-picker-pane-head {
                                                min-height: 40px;
                                                padding: 6px 9px;
                                            }

                                            .sb-shop-picker-filters {
                                                padding: 6px 8px 4px !important;
                                            }

                                            .sb-shop-picker-item {
                                                grid-template-columns:
                                                    56px
                                                    minmax(0,1fr)
                                                    23px !important;
                                                min-height: 70px !important;
                                                gap: 8px !important;
                                                padding: 5px !important;
                                            }

                                            .sb-shop-picker-item__image {
                                                width: 56px !important;
                                                height: 56px !important;
                                            }

                                            .sb-shop-picker-item p {
                                                font-size: 9px !important;
                                                -webkit-line-clamp: 1 !important;
                                            }

                                            .sb-shop-picker-config__content {
                                                gap: 5px !important;
                                                padding:
                                                    6px 8px 7px 18px !important;
                                            }

                                            .sb-shop-picker-selected {
                                                grid-template-columns:
                                                    58px
                                                    minmax(0,1fr) !important;
                                                min-height: 70px !important;
                                                padding: 5px !important;
                                            }

                                            .sb-shop-picker-selected__image {
                                                width: 58px !important;
                                                height: 58px !important;
                                            }

                                            .sb-shop-picker-selected__copy small {
                                                display: none !important;
                                            }

                                            .sb-shop-picker-config-card {
                                                padding: 6px !important;
                                            }

                                            .sb-shop-picker-config-card.is-price {
                                                min-height: 160px !important;
                                            }

                                            .sb-shop-price-coin {
                                                min-height: 70px !important;
                                                padding: 4px !important;
                                            }

                                            .sb-shop-price-coin__icon {
                                                width: 22px !important;
                                                height: 22px !important;
                                                flex-basis: 22px !important;
                                            }

                                            .sb-shop-price-coin__stepper button,
                                            .sb-shop-price-coin__stepper input {
                                                height: 24px !important;
                                            }

                                            .sb-shop-picker-submit {
                                                min-height: 48px !important;
                                            }
                                        }

                                        @media (max-width: 1100px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: calc(100vw - 32px) !important;
                                            }

                                            .sb-shop-picker-layout {
                                                grid-template-columns:
                                                    minmax(0,1fr)
                                                    minmax(455px,.95fr) !important;
                                            }
                                        }


                                        /* ============================================================
                                           SHOP PICKER V13 — CLEAN
                                        ============================================================ */

                                        [x-show="shopPickerOpen && lootActiveShop"] {
                                            padding: 30px !important;
                                        }

                                        [x-show="shopPickerOpen && lootActiveShop"] > section {
                                            width: min(1120px, calc(100vw - 60px)) !important;
                                            height: min(520px, calc(100dvh - 60px)) !important;
                                            border-radius: 18px !important;
                                        }

                                        .sb-shop-picker-head {
                                            min-height: 60px !important;
                                            padding: 8px 14px !important;
                                        }

                                        .sb-shop-picker-head__brand-icon {
                                            width: 36px !important;
                                            height: 36px !important;
                                            flex-basis: 36px !important;
                                        }

                                        .sb-shop-picker-head__brand-icon svg {
                                            width: 18px !important;
                                            height: 18px !important;
                                        }

                                        .sb-shop-picker-head h3 {
                                            font-size: 22px !important;
                                        }

                                        .sb-shop-picker-head p {
                                            display: none !important;
                                        }

                                        .sb-shop-picker-layout {
                                            grid-template-columns:
                                                minmax(0, 1.05fr)
                                                minmax(500px, .95fr) !important;
                                        }

                                        .sb-shop-picker-pane-head,
                                        .sb-shop-picker-pane-count,
                                        .sb-shop-picker-pane-step,
                                        .sb-shop-picker-step-badge {
                                            display: none !important;
                                        }

                                        /* filtros */
                                        .sb-shop-picker-filters {
                                            grid-template-columns:
                                                minmax(220px, 1fr)
                                                minmax(330px, 1fr) !important;
                                            gap: 7px !important;
                                            padding: 9px 10px 7px !important;
                                        }

                                        .sb-shop-picker-filter-summary {
                                            grid-column: 1 / -1;
                                            min-height: 13px !important;
                                            justify-content: flex-end !important;
                                            margin: 0 !important;
                                            padding: 0 2px !important;
                                        }

                                        /* catálogo */
                                        .sb-shop-picker-items {
                                            gap: 5px !important;
                                            padding: 7px 8px 9px !important;
                                        }

                                        .sb-shop-picker-item {
                                            grid-template-columns:
                                                60px
                                                minmax(0,1fr)
                                                24px !important;
                                            min-height: 76px !important;
                                            gap: 9px !important;
                                            padding: 6px !important;
                                        }

                                        .sb-shop-picker-item__image {
                                            width: 60px !important;
                                            height: 60px !important;
                                        }

                                        .sb-shop-picker-item h4 {
                                            font-size: 16px !important;
                                        }

                                        .sb-shop-picker-item p {
                                            margin-top: 4px !important;
                                            font-size: 9.5px !important;
                                            line-height: 1.4 !important;
                                            -webkit-line-clamp: 1 !important;
                                        }

                                        /* configuração */
                                        .sb-shop-picker-config__content {
                                            gap: 7px !important;
                                            padding: 9px 10px !important;
                                        }

                                        .sb-shop-picker-config__content::before {
                                            display: none !important;
                                        }

                                        .sb-shop-picker-selected {
                                            grid-template-columns:
                                                64px
                                                minmax(0,1fr) !important;
                                            min-height: 78px !important;
                                            gap: 9px !important;
                                            padding: 7px !important;
                                        }

                                        .sb-shop-picker-selected__image {
                                            width: 64px !important;
                                            height: 64px !important;
                                        }

                                        .sb-shop-picker-selected__copy > span,
                                        .sb-shop-picker-selected__copy small {
                                            display: none !important;
                                        }

                                        .sb-shop-picker-selected__copy h4 {
                                            margin-top: 0 !important;
                                            font-size: 19px !important;
                                        }

                                        .sb-shop-picker-selected__copy p {
                                            margin-top: 4px !important;
                                            font-size: 8.5px !important;
                                        }

                                        .sb-shop-picker-config-card {
                                            padding: 8px !important;
                                        }

                                        .sb-shop-picker-config-card__head {
                                            min-height: 22px;
                                            align-items: center !important;
                                            margin-bottom: 6px !important;
                                        }

                                        .sb-shop-picker-config-card__head > strong {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 14px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-picker-config-card__head > div,
                                        .sb-shop-picker-config-card__head > small {
                                            display: none !important;
                                        }

                                        .sb-shop-picker-config-card__head > button {
                                            font-size: 7.5px !important;
                                        }

                                        /* preço e moedas */
                                        .sb-shop-picker-config-card.is-price {
                                            min-height: 164px !important;
                                            padding: 9px !important;
                                        }

                                        .sb-shop-price-coins {
                                            grid-template-columns:
                                                repeat(5, minmax(0,1fr)) !important;
                                            gap: 7px !important;
                                        }

                                        .sb-shop-price-coin {
                                            min-height: 92px !important;
                                            padding: 7px !important;
                                        }

                                        .sb-shop-price-coin__icon {
                                            width: 27px !important;
                                            height: 27px !important;
                                            flex-basis: 27px !important;
                                        }

                                        .sb-shop-price-coin__meta strong {
                                            font-size: 9px !important;
                                        }

                                        .sb-shop-price-coin__meta small {
                                            font-size: 6px !important;
                                        }

                                        .sb-shop-price-coin__input {
                                            display: block;
                                            width: 100%;
                                            margin-top: 8px;
                                        }

                                        .sb-shop-price-coin__input input {
                                            width: 100% !important;
                                            min-width: 0 !important;
                                            height: 38px !important;
                                            border: 1px solid rgba(176,140,98,.45);
                                            border-radius: 9px;
                                            background: #fffdf8;
                                            padding: 0 6px !important;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 16px !important;
                                            font-weight: 900;
                                            font-variant-numeric: tabular-nums;
                                            letter-spacing: -.02em;
                                            text-align: center;
                                            outline: none;
                                        }

                                        .sb-shop-price-coin__input input:focus {
                                            border-color: #8a4b20;
                                            box-shadow: 0 0 0 3px rgba(138,75,32,.08);
                                        }

                                        .sb-shop-price-coin__input input.is-long {
                                            font-size: 14px !important;
                                        }

                                        .sb-shop-price-coin__input input.is-very-long {
                                            font-size: 12px !important;
                                        }

                                        .sb-shop-price-coin__stepper {
                                            display: none !important;
                                        }

                                        .sb-shop-price-summary {
                                            margin-top: 8px !important;
                                            padding-top: 7px !important;
                                        }

                                        .sb-shop-price-summary > div {
                                            width: 100%;
                                        }

                                        .sb-shop-price-summary strong {
                                            margin-top: 2px !important;
                                            font-size: 14px !important;
                                        }

                                        .sb-shop-price-summary small {
                                            display: none !important;
                                        }

                                        /* footer */
                                        .sb-shop-picker-submit {
                                            min-height: 52px !important;
                                            padding: 7px 10px !important;
                                        }

                                        .sb-shop-picker-submit > div > span {
                                            display: none !important;
                                        }

                                        .sb-shop-picker-submit > div > strong {
                                            margin: 0 !important;
                                            font-size: 13px !important;
                                        }

                                        @media (max-height: 660px) and (min-width: 821px) {
                                            [x-show="shopPickerOpen && lootActiveShop"] {
                                                padding: 18px !important;
                                            }

                                            [x-show="shopPickerOpen && lootActiveShop"] > section {
                                                width: min(1120px, calc(100vw - 36px)) !important;
                                                height: min(520px, calc(100dvh - 36px)) !important;
                                            }

                                            .sb-shop-price-coin__input input {
                                                height: 34px !important;
                                                font-size: 14px !important;
                                            }

                                            .sb-shop-price-coin__input input.is-long {
                                                font-size: 12.5px !important;
                                            }

                                            .sb-shop-price-coin__input input.is-very-long {
                                                font-size: 11px !important;
                                            }
                                        }

</style>


                                    <style>
                                        /* ============================================================
                                           MASTER SHOPS V8 — ORGANIZAÇÃO E HIERARQUIA VISUAL
                                        ============================================================ */

                                        .sb-master-loot-heading {
                                            min-height: 48px;
                                        }

                                        .sb-master-loot-heading__icon {
                                            display: grid;
                                            width: 38px;
                                            height: 38px;
                                            flex: 0 0 38px;
                                            place-items: center;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 11px;
                                            background: #fffdf8;
                                            color: #8a4b20;
                                            box-shadow:
                                                inset 0 1px 0 rgba(255,255,255,.8),
                                                0 4px 12px rgba(83,21,15,.045);
                                        }

                                        .sb-master-loot-heading__icon svg {
                                            width: 18px;
                                            height: 18px;
                                        }

                                        .sb-shop-workspace {
                                            gap: 14px !important;
                                            align-items: start;
                                        }

                                        .sb-shop-workspace-sidebar {
                                            position: sticky;
                                            top: 0;
                                            border-radius: 17px !important;
                                            background: #f5efe6 !important;
                                            padding: 10px !important;
                                            box-shadow: none !important;
                                        }

                                        .sb-loot-mode-switch {
                                            border-radius: 11px !important;
                                            background: #e9dfd2 !important;
                                            padding: 3px !important;
                                        }

                                        .sb-loot-mode-switch > button {
                                            min-height: 34px;
                                            border-radius: 8px !important;
                                        }

                                        .sb-shop-organizer-panel {
                                            margin-top: 10px;
                                        }

                                        .sb-shop-organizer-head {
                                            display: flex;
                                            align-items: flex-start;
                                            justify-content: space-between;
                                            gap: 10px;
                                            padding: 3px 2px 0;
                                        }

                                        .sb-shop-organizer-head h4 {
                                            margin-top: 2px;
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 17px;
                                            font-weight: 900;
                                            line-height: 1.05;
                                        }

                                        .sb-shop-organizer-head > div > span {
                                            display: block;
                                            margin-top: 4px;
                                            color: #8c806f;
                                            font-size: 8.5px;
                                            line-height: 1.35;
                                        }

                                        .sb-shop-organizer-eyebrow {
                                            color: #9a806d;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .11em;
                                            line-height: 1;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-organizer-total {
                                            display: grid;
                                            min-width: 29px;
                                            height: 29px;
                                            place-items: center;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 999px;
                                            background: #fffdf8;
                                            color: #6b1d14;
                                            font-size: 9px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-folder-create-primary {
                                            display: grid;
                                            width: 100%;
                                            grid-template-columns: 34px minmax(0,1fr) 22px;
                                            gap: 9px;
                                            align-items: center;
                                            margin-top: 11px;
                                            border: 1px solid #8a4b20;
                                            border-radius: 12px;
                                            background:
                                                linear-gradient(
                                                    135deg,
                                                    #7a251b 0%,
                                                    #5f1711 100%
                                                );
                                            padding: 8px 10px;
                                            color: #fffaf2;
                                            text-align: left;
                                            box-shadow: 0 6px 14px rgba(83,21,15,.11);
                                            transition:
                                                transform .15s ease,
                                                box-shadow .15s ease,
                                                filter .15s ease;
                                        }

                                        .sb-shop-folder-create-primary:hover {
                                            transform: translateY(-1px);
                                            filter: brightness(1.04);
                                            box-shadow: 0 9px 18px rgba(83,21,15,.15);
                                        }

                                        .sb-shop-folder-create-primary:disabled {
                                            cursor: not-allowed;
                                            opacity: .45;
                                            transform: none;
                                        }

                                        .sb-shop-folder-create-primary__icon {
                                            display: grid;
                                            width: 34px;
                                            height: 34px;
                                            place-items: center;
                                            border: 1px solid rgba(255,255,255,.18);
                                            border-radius: 9px;
                                            background: rgba(255,255,255,.08);
                                        }

                                        .sb-shop-folder-create-primary__icon svg {
                                            width: 17px;
                                            height: 17px;
                                        }

                                        .sb-shop-folder-create-primary__copy {
                                            min-width: 0;
                                        }

                                        .sb-shop-folder-create-primary__copy strong {
                                            display: block;
                                            font-size: 9px;
                                            font-weight: 900;
                                            letter-spacing: .04em;
                                            line-height: 1.1;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-folder-create-primary__copy small {
                                            display: block;
                                            margin-top: 3px;
                                            color: rgba(255,250,242,.70);
                                            font-size: 7.5px;
                                            line-height: 1.2;
                                        }

                                        .sb-shop-folder-create-primary__arrow {
                                            font-size: 17px;
                                            font-weight: 500;
                                            text-align: center;
                                        }

                                        .sb-shop-organizer-search {
                                            display: grid;
                                            grid-template-columns: 17px minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            margin-top: 9px;
                                            border: 1px solid #d8c7ab;
                                            border-radius: 10px;
                                            background: #fffdf8;
                                            padding: 0 10px;
                                            color: #9a806d;
                                        }

                                        .sb-shop-organizer-search:focus-within {
                                            border-color: #a9784d;
                                            box-shadow: 0 0 0 3px rgba(138,75,32,.08);
                                        }

                                        .sb-shop-organizer-search svg {
                                            width: 15px;
                                            height: 15px;
                                        }

                                        .sb-shop-organizer-search input {
                                            width: 100%;
                                            height: 38px;
                                            border: 0;
                                            background: transparent;
                                            color: #53150f;
                                            font-size: 10px;
                                            font-weight: 700;
                                            outline: none;
                                        }

                                        .sb-shop-organizer-search input::placeholder {
                                            color: #a3978b;
                                        }

                                        .sb-shop-organizer-hint {
                                            display: flex;
                                            align-items: center;
                                            gap: 6px;
                                            margin-top: 7px;
                                            padding: 0 2px;
                                            color: #9a806d;
                                            font-size: 7.5px;
                                            line-height: 1.3;
                                        }

                                        .sb-shop-organizer-hint > span {
                                            color: #8a4b20;
                                            font-size: 11px;
                                            font-weight: 900;
                                        }

                                        .sb-shop-folder-list {
                                            display: grid !important;
                                            gap: 7px !important;
                                            margin-top: 10px !important;
                                            max-height: 51vh;
                                            overflow-y: auto;
                                            padding-right: 2px;
                                            scrollbar-width: thin;
                                            scrollbar-color: #c7b69e transparent;
                                        }

                                        .sb-shop-folder {
                                            position: relative;
                                            overflow: visible !important;
                                            border: 1px solid #ddd0bd !important;
                                            border-left:
                                                3px solid
                                                var(--folder-color, #8a4b20) !important;
                                            border-radius: 12px !important;
                                            background: #fffdf8 !important;
                                            box-shadow: none !important;
                                            transition:
                                                border-color .15s ease,
                                                box-shadow .15s ease;
                                        }

                                        .sb-shop-folder.is-open {
                                            border-color:
                                                color-mix(
                                                    in srgb,
                                                    var(--folder-color, #8a4b20) 42%,
                                                    #ddd0bd
                                                ) !important;
                                            box-shadow: 0 5px 14px rgba(83,21,15,.045) !important;
                                        }

                                        .sb-shop-folder.is-drop-target {
                                            border-color:
                                                var(--folder-color, #8a4b20) !important;
                                            box-shadow:
                                                0 0 0 3px
                                                color-mix(
                                                    in srgb,
                                                    var(--folder-color, #8a4b20) 12%,
                                                    transparent
                                                ) !important;
                                        }

                                        .sb-shop-folder__head {
                                            display: grid !important;
                                            grid-template-columns: minmax(0,1fr) 28px !important;
                                            gap: 5px !important;
                                            align-items: center !important;
                                            min-height: 0 !important;
                                            padding: 5px 5px 5px 7px !important;
                                            background:
                                                linear-gradient(
                                                    90deg,
                                                    color-mix(
                                                        in srgb,
                                                        var(--folder-color, #8a4b20) 8%,
                                                        #fffdf8
                                                    ),
                                                    #fffdf8 68%
                                                ) !important;
                                        }

                                        .sb-shop-folder__main {
                                            display: grid;
                                            min-width: 0;
                                            grid-template-columns: 34px minmax(0,1fr) auto 20px;
                                            gap: 8px;
                                            align-items: center;
                                            border: 0;
                                            background: transparent;
                                            padding: 0;
                                            text-align: left;
                                        }

                                        .sb-shop-folder__cover {
                                            display: grid !important;
                                            width: 34px !important;
                                            height: 34px !important;
                                            place-items: center !important;
                                            overflow: hidden !important;
                                            border:
                                                1px solid
                                                color-mix(
                                                    in srgb,
                                                    var(--folder-color, #8a4b20) 32%,
                                                    #ddd0bd
                                                ) !important;
                                            border-radius: 9px !important;
                                            background:
                                                color-mix(
                                                    in srgb,
                                                    var(--folder-color, #8a4b20) 9%,
                                                    #f7f2e9
                                                ) !important;
                                            color: var(--folder-color, #8a4b20) !important;
                                        }

                                        .sb-shop-folder__cover img {
                                            width: 100% !important;
                                            height: 100% !important;
                                            object-fit: cover !important;
                                        }

                                        .sb-shop-folder__cover svg {
                                            width: 16px;
                                            height: 16px;
                                        }

                                        .sb-shop-folder__copy {
                                            display: block !important;
                                            min-width: 0 !important;
                                            text-align: left !important;
                                        }

                                        .sb-shop-folder__copy strong {
                                            display: block !important;
                                            overflow: hidden !important;
                                            color:
                                                var(
                                                    --folder-text-color,
                                                    #53150f
                                                ) !important;
                                            font-family: Georgia, serif !important;
                                            font-size: 12.5px !important;
                                            font-weight: 900 !important;
                                            line-height: 1.05 !important;
                                            text-overflow: ellipsis !important;
                                            white-space: nowrap !important;
                                        }

                                        .sb-shop-folder__copy small {
                                            display: block;
                                            overflow: hidden;
                                            margin-top: 3px;
                                            color: #8c806f;
                                            font-size: 7.3px;
                                            line-height: 1.15;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                        }

                                        .sb-shop-folder__count {
                                            min-width: 24px !important;
                                            height: 24px !important;
                                            padding: 0 6px !important;
                                            font-size: 7.5px !important;
                                        }

                                        .sb-shop-folder__chevron {
                                            color: #8c6239;
                                            font-size: 13px;
                                            font-weight: 900;
                                            text-align: center;
                                            transition: transform .15s ease;
                                        }

                                        .sb-shop-folder.is-open .sb-shop-folder__chevron {
                                            transform: rotate(180deg);
                                        }

                                        .sb-shop-folder__menu {
                                            display: grid !important;
                                            width: 28px !important;
                                            height: 28px !important;
                                            place-items: center !important;
                                            border: 0 !important;
                                            border-radius: 8px !important;
                                            background: transparent !important;
                                            color: #8c6239 !important;
                                            font-size: 13px !important;
                                            font-weight: 900 !important;
                                        }

                                        .sb-shop-folder__menu:hover,
                                        .sb-shop-side-card__menu:hover {
                                            background: #f1e8db !important;
                                            color: #53150f !important;
                                        }

                                        .sb-shop-folder-menu,
                                        .sb-shop-side-menu {
                                            position: absolute;
                                            right: 0;
                                            top: 31px;
                                            z-index: 70;
                                            width: 170px;
                                            overflow: hidden;
                                            border: 1px solid #cdbb9f;
                                            border-radius: 11px;
                                            background: #fffdf8;
                                            padding: 5px;
                                            box-shadow: 0 12px 30px rgba(55,30,18,.18);
                                        }

                                        .sb-shop-folder-menu button,
                                        .sb-shop-side-menu button {
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

                                        .sb-shop-folder-menu button:hover,
                                        .sb-shop-side-menu button:hover {
                                            background: #f1e8db;
                                        }

                                        .sb-shop-folder-menu button.is-danger,
                                        .sb-shop-side-menu button.is-danger {
                                            color: #b42318;
                                        }

                                        .sb-shop-folder__body {
                                            display: grid;
                                            gap: 5px !important;
                                            border-top: 1px solid #eee2d1 !important;
                                            padding: 6px !important;
                                        }

                                        .sb-shop-side-card {
                                            position: relative !important;
                                            display: grid !important;
                                            grid-template-columns: minmax(0,1fr) 27px !important;
                                            gap: 4px !important;
                                            align-items: center !important;
                                            min-height: 46px !important;
                                            border: 1px solid #e3d7c6 !important;
                                            border-radius: 10px !important;
                                            background: #fffdf8 !important;
                                            padding: 5px 5px 5px 7px !important;
                                            cursor: grab !important;
                                            box-shadow: none !important;
                                            transition:
                                                border-color .15s ease,
                                                background .15s ease,
                                                transform .15s ease !important;
                                        }

                                        .sb-shop-side-card:hover {
                                            border-color: #c9ab85 !important;
                                            transform: translateX(1px) !important;
                                        }

                                        .sb-shop-side-card.is-active {
                                            border-color: #8a4b20 !important;
                                            background: #fffaf4 !important;
                                            box-shadow: inset 3px 0 0 #8a4b20 !important;
                                        }

                                        .sb-shop-side-card.is-dragging {
                                            opacity: .42 !important;
                                        }

                                        .sb-shop-side-card__main {
                                            display: grid;
                                            min-width: 0;
                                            grid-template-columns: 8px minmax(0,1fr);
                                            gap: 8px;
                                            align-items: center;
                                            border: 0;
                                            background: transparent;
                                            padding: 0;
                                            text-align: left;
                                        }

                                        .sb-shop-side-card__status {
                                            width: 8px !important;
                                            height: 8px !important;
                                            border-radius: 999px !important;
                                            background: #ad9d89 !important;
                                        }

                                        .sb-shop-side-card__status.is-open {
                                            background: #168661 !important;
                                            box-shadow: 0 0 0 3px rgba(22,134,97,.10) !important;
                                        }

                                        .sb-shop-side-card__copy {
                                            min-width: 0 !important;
                                        }

                                        .sb-shop-side-card__copy strong {
                                            display: block !important;
                                            overflow: hidden !important;
                                            color: #53150f !important;
                                            font-family: Georgia, serif !important;
                                            font-size: 12px !important;
                                            font-weight: 900 !important;
                                            line-height: 1.05 !important;
                                            text-overflow: ellipsis !important;
                                            white-space: nowrap !important;
                                        }

                                        .sb-shop-side-card__copy small {
                                            display: block;
                                            margin-top: 3px;
                                            color: #8c806f;
                                            font-size: 7.2px;
                                            line-height: 1.2;
                                        }

                                        .sb-shop-side-card__menu {
                                            display: grid !important;
                                            width: 27px !important;
                                            height: 27px !important;
                                            place-items: center !important;
                                            border: 0 !important;
                                            border-radius: 8px !important;
                                            background: transparent !important;
                                            color: #8c6239 !important;
                                            font-size: 13px !important;
                                            font-weight: 900 !important;
                                        }

                                        .sb-shop-folder__empty {
                                            display: grid;
                                            gap: 2px !important;
                                            border: 1px dashed #d8c7ab !important;
                                            border-radius: 9px !important;
                                            background: #faf7f1 !important;
                                            padding: 10px 8px !important;
                                            color: #8c806f !important;
                                            text-align: center !important;
                                        }

                                        .sb-shop-folder__empty strong {
                                            color: #76553f;
                                            font-size: 8px;
                                            font-weight: 900;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-folder__empty span {
                                            font-size: 7.5px;
                                        }

                                        .sb-shop-folder__empty.is-success {
                                            border-color: #bfd8cc !important;
                                            background: #f4faf6 !important;
                                        }

                                        .sb-shop-unfiled {
                                            overflow: visible !important;
                                            border: 1px solid #ddd0bd !important;
                                            border-radius: 12px !important;
                                            background: #f8f3ec !important;
                                            padding: 6px !important;
                                        }

                                        .sb-shop-unfiled.is-drop-target {
                                            border-color: #8a4b20 !important;
                                            background: #fff9f2 !important;
                                            box-shadow: 0 0 0 3px rgba(138,75,32,.08);
                                        }

                                        .sb-shop-unfiled__head {
                                            display: flex !important;
                                            align-items: center !important;
                                            justify-content: space-between !important;
                                            gap: 8px !important;
                                            padding: 2px 3px 7px !important;
                                        }

                                        .sb-shop-unfiled__head > div {
                                            min-width: 0;
                                        }

                                        .sb-shop-unfiled__head span:first-child {
                                            display: block;
                                            color: #8a4b20;
                                            font-size: 7px;
                                            font-weight: 900;
                                            letter-spacing: .08em;
                                            text-transform: uppercase;
                                        }

                                        .sb-shop-unfiled__head strong {
                                            display: block !important;
                                            margin-top: 2px;
                                            color: #76553f !important;
                                            font-size: 8px !important;
                                            font-weight: 800 !important;
                                            letter-spacing: 0 !important;
                                            text-transform: none !important;
                                        }

                                        .sb-shop-unfiled__list {
                                            display: grid;
                                            gap: 5px;
                                        }

                                        .sb-shop-organizer-empty {
                                            display: grid;
                                            gap: 3px;
                                            border: 1px dashed #cdbb9f;
                                            border-radius: 12px;
                                            padding: 18px 10px;
                                            text-align: center;
                                        }

                                        .sb-shop-organizer-empty strong {
                                            color: #53150f;
                                            font-family: Georgia, serif;
                                            font-size: 13px;
                                        }

                                        .sb-shop-organizer-empty span {
                                            color: #8c806f;
                                            font-size: 8px;
                                        }

                                        /* Estado ativo das abas — evita texto branco sobre fundo claro. */
                                        .sb-master-shop-tab {
                                            color: #7d604d !important;
                                        }

                                        .sb-master-shop-tab.is-active {
                                            color: #6b1d14 !important;
                                            background: #fffdf8 !important;
                                        }

                                        .sb-master-shop-tabs {
                                            margin: 8px 0 12px !important;
                                        }

                                        .sb-admin-stock {
                                            border-color: #d8c7ab !important;
                                            border-radius: 15px !important;
                                            box-shadow: 0 6px 18px rgba(83,21,15,.035);
                                        }

                                        .sb-admin-stock__top {
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    #fffdf8,
                                                    #fcf8f2
                                                );
                                            padding: 11px 13px !important;
                                        }

                                        .sb-admin-stock__top-copy strong {
                                            font-size: 18px !important;
                                        }

                                        .sb-admin-stock__top-copy span {
                                            margin-top: 3px !important;
                                            font-size: 8.5px !important;
                                        }

                                        .sb-admin-category-list {
                                            gap: 8px !important;
                                            padding: 10px !important;
                                        }

                                        .sb-admin-category {
                                            border-radius: 12px !important;
                                            background: #fbf8f3 !important;
                                        }

                                        .sb-admin-category__head {
                                            padding: 10px 11px !important;
                                            transition: background .15s ease;
                                        }

                                        .sb-admin-category__head:hover {
                                            background: #f7f0e7 !important;
                                        }

                                        .sb-admin-category__copy strong {
                                            font-size: 16px !important;
                                        }

                                        .sb-admin-category__copy span {
                                            margin-top: 3px !important;
                                            font-size: 9px !important;
                                            line-height: 1.35 !important;
                                        }

                                        .sb-admin-product-grid {
                                            grid-template-columns:
                                                repeat(
                                                    auto-fill,
                                                    minmax(235px, 1fr)
                                                ) !important;
                                            gap: 10px !important;
                                        }

                                        .sb-admin-product__copy h6 {
                                            font-size: 16px !important;
                                        }

                                        .sb-admin-product__copy p {
                                            font-size: 8.5px !important;
                                        }

                                        @media (max-width: 1180px) {
                                            .sb-shop-workspace {
                                                grid-template-columns:
                                                    250px
                                                    minmax(0,1fr) !important;
                                            }
                                        }

                                        @media (max-width: 900px) {
                                            .sb-shop-workspace {
                                                grid-template-columns:
                                                    1fr !important;
                                            }

                                            .sb-shop-workspace-sidebar {
                                                position: static !important;
                                            }

                                            .sb-shop-folder-list {
                                                max-height: 360px;
                                            }
                                        }
                                    

                                        /* ============================================================
                                           PASTAS DE LOJAS — MODAL COMPACTO E ORGANIZADO
                                           Mantém formulário e capa em colunas reais, sem sobreposição.
                                        ============================================================ */
                                        .sb-folder-modal {
                                            width: min(760px, calc(100vw - 32px)) !important;
                                            height: auto !important;
                                            max-height: min(560px, calc(100dvh - 32px)) !important;
                                        }

                                        .sb-folder-modal__head {
                                            padding: 12px 15px !important;
                                        }

                                        .sb-folder-modal__body {
                                            display: block !important;
                                            min-height: 0 !important;
                                            flex: 0 1 auto !important;
                                            overflow-x: hidden !important;
                                            overflow-y: auto !important;
                                            padding: 16px !important;
                                            scrollbar-gutter: auto !important;
                                        }

                                        .sb-shop-folder-modal__content {
                                            display: grid;
                                            grid-template-columns: minmax(0, 1fr) 220px;
                                            gap: 18px;
                                            align-items: start;
                                            width: 100%;
                                            max-width: 720px;
                                            margin: 0 auto;
                                        }

                                        .sb-shop-folder-modal__form {
                                            min-width: 0;
                                            display: grid;
                                            gap: 12px;
                                        }

                                        .sb-shop-folder-modal__cover {
                                            min-width: 0;
                                            display: grid;
                                            gap: 8px;
                                            align-content: start;
                                        }

                                        .sb-shop-folder-modal__cover .sb-folder-cover-preview {
                                            width: 100% !important;
                                            height: 168px !important;
                                            max-height: none !important;
                                            aspect-ratio: auto !important;
                                            border-radius: 14px !important;
                                            background: #f3eadf !important;
                                        }

                                        .sb-shop-folder-modal__cover .sb-folder-cover-preview img {
                                            width: 100% !important;
                                            height: 100% !important;
                                            object-fit: cover !important;
                                        }

                                        .sb-shop-folder-modal__cover-actions {
                                            display: grid;
                                            grid-template-columns: 1fr;
                                            gap: 6px;
                                        }

                                        .sb-folder-field {
                                            gap: 5px !important;
                                        }

                                        .sb-folder-field input,
                                        .sb-folder-field textarea {
                                            width: 100% !important;
                                            box-sizing: border-box !important;
                                        }

                                        .sb-folder-field input {
                                            min-height: 42px !important;
                                            padding: 9px 11px !important;
                                        }

                                        .sb-folder-field textarea {
                                            min-height: 118px !important;
                                            max-height: 170px !important;
                                            padding: 10px 11px !important;
                                            resize: vertical !important;
                                        }

                                        .sb-folder-modal__footer {
                                            position: static !important;
                                            padding: 10px 12px !important;
                                        }

                                        @media (max-width: 720px) {
                                            .sb-folder-modal {
                                                width: min(680px, calc(100vw - 16px)) !important;
                                                max-height: calc(100dvh - 16px) !important;
                                            }

                                            .sb-folder-modal__body {
                                                padding: 13px !important;
                                            }

                                            .sb-shop-folder-modal__content {
                                                grid-template-columns: 1fr;
                                                gap: 13px;
                                            }

                                            .sb-shop-folder-modal__cover {
                                                grid-template-columns: 150px minmax(0,1fr);
                                                gap: 10px;
                                                align-items: start;
                                            }

                                            .sb-shop-folder-modal__cover .sb-folder-cover-preview {
                                                width: 150px !important;
                                                height: 120px !important;
                                            }

                                            .sb-shop-folder-modal__cover-actions {
                                                align-self: stretch;
                                                align-content: start;
                                            }
                                        }

                                        @media (max-width: 520px) {
                                            .sb-shop-folder-modal__cover {
                                                grid-template-columns: 1fr;
                                            }

                                            .sb-shop-folder-modal__cover .sb-folder-cover-preview {
                                                width: 100% !important;
                                                height: 150px !important;
                                            }
                                        }
</style>

                                    <template x-teleport="body">
                                    <div
                                        x-show="shopCreateOpen"
                                        x-cloak
                                        role="dialog"
                                        aria-modal="true"
                                        aria-label="Criar nova sessão de loja"
                                        class="sb-shop-create-overlay"
                                        @click.self="shopCreateOpen = false"
                                        @keydown.escape.window="shopNewShopkeeperOpen ? (shopNewShopkeeperOpen = false) : (shopCreateOpen = false)"
                                    >
                                        <div class="sb-shop-create-shell">
                                            <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#e1d5c3] bg-white px-5 py-2.5 sm:px-6">
                                                <h4 class="min-w-0 truncate font-serif text-[21px] font-black text-[#53150f]">Nova loja</h4>

                                                <button
                                                    type="button"
                                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#ddcfbb] bg-[#fffdfa] text-[18px] leading-none text-[#6b1d14] transition hover:bg-[#f5eee4]"
                                                    @click="shopCreateOpen = false"
                                                    aria-label="Fechar"
                                                >×</button>
                                            </header>

                                            <div class="sb-shop-create-body">
                                                <div class="sb-shop-create-controls">
                                                    <div class="space-y-4">
                                                        <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                            <div class="mb-4">
                                                                <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Identidade</p>
                                                                <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">Defina nome, atendente e uma descrição curta.</p>
                                                            </div>

                                                            <div class="grid gap-4 md:grid-cols-2">
                                                                <label>
                                                                    <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Nome da loja</span>
                                                                    <input
                                                                        type="text"
                                                                        maxlength="150"
                                                                        x-model="shopNewName"
                                                                        placeholder="Ex.: Empório do Porto"
                                                                        class="h-12 w-full rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[14px] font-semibold text-[#53150f] outline-none transition placeholder:text-[#a3978b] focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                    >
                                                                </label>

                                                                <div class="sb-shopkeeper-field" @click.outside="shopNewShopkeeperOpen = false">
                                                                    <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Atendente</span>

                                                                    <button
                                                                        type="button"
                                                                        class="sb-shopkeeper-trigger"
                                                                        :class="shopNewShopkeeperOpen ? 'is-open' : ''"
                                                                        :aria-expanded="shopNewShopkeeperOpen ? 'true' : 'false'"
                                                                        @click="shopNewShopkeeperOpen = !shopNewShopkeeperOpen; if (shopNewShopkeeperOpen) shopNewShopkeeperSearch = ''"
                                                                    >
                                                                        <span class="sb-shopkeeper-avatar">
                                                                            <template x-if="shopNewSelectedShopkeeper()?.image_url">
                                                                                <img
                                                                                    :src="shopNewSelectedShopkeeper().image_url"
                                                                                    :alt="shopNewSelectedShopkeeper().name"
                                                                                >
                                                                            </template>
                                                                            <template x-if="!shopNewSelectedShopkeeper()?.image_url">
                                                                                <span x-text="shopNewSelectedShopkeeper() ? 'NPC' : '—'"></span>
                                                                            </template>
                                                                        </span>

                                                                        <span class="sb-shopkeeper-copy">
                                                                            <strong
                                                                                class="sb-shopkeeper-name"
                                                                                x-text="shopNewSelectedShopkeeper()?.name || 'Sem NPC vinculado'"
                                                                            ></strong>
                                                                            <small
                                                                                class="sb-shopkeeper-hint"
                                                                                x-text="shopNewSelectedShopkeeper() ? 'Retrato do Cofre como padrão' : 'Opcional · escolha um NPC do Cofre'"
                                                                            ></small>
                                                                        </span>

                                                                        <span class="sb-shopkeeper-chevron">⌄</span>
                                                                    </button>

                                                                    <div
                                                                        x-show="shopNewShopkeeperOpen"
                                                                        x-cloak
                                                                        x-transition.opacity.duration.120ms
                                                                        class="sb-shopkeeper-menu"
                                                                        @click.stop
                                                                    >
                                                                        <div class="sb-shopkeeper-search-wrap">
                                                                            <input
                                                                                type="search"
                                                                                x-model.debounce.120ms="shopNewShopkeeperSearch"
                                                                                placeholder="Buscar NPC..."
                                                                                class="sb-shopkeeper-search"
                                                                            >
                                                                        </div>

                                                                        <div class="sb-shopkeeper-options">
                                                                            <button
                                                                                type="button"
                                                                                class="sb-shopkeeper-option"
                                                                                :class="!shopNewShopkeeperNpcId ? 'is-selected' : ''"
                                                                                @click="shopNewShopkeeperNpcId = ''; shopNewShopkeeperOpen = false"
                                                                            >
                                                                                <span class="sb-shopkeeper-avatar">—</span>
                                                                                <span class="sb-shopkeeper-copy">
                                                                                    <strong class="sb-shopkeeper-name">Sem NPC vinculado</strong>
                                                                                    <small class="sb-shopkeeper-hint">A loja usa somente a imagem enviada</small>
                                                                                </span>
                                                                                <span x-show="!shopNewShopkeeperNpcId" class="sb-shopkeeper-check">✓</span>
                                                                            </button>

                                                                            <template x-for="npc in shopNewFilteredShopkeepers()" :key="'new-shopkeeper-menu-' + npc.id">
                                                                                <button
                                                                                    type="button"
                                                                                    class="sb-shopkeeper-option"
                                                                                    :class="parseInt(shopNewShopkeeperNpcId) === parseInt(npc.id) ? 'is-selected' : ''"
                                                                                    @click="shopNewShopkeeperNpcId = String(npc.id); shopNewShopkeeperOpen = false"
                                                                                >
                                                                                    <span class="sb-shopkeeper-avatar">
                                                                                        <template x-if="npc.image_url">
                                                                                            <img :src="npc.image_url" :alt="npc.name">
                                                                                        </template>
                                                                                        <template x-if="!npc.image_url">
                                                                                            <span>NPC</span>
                                                                                        </template>
                                                                                    </span>
                                                                                    <span class="sb-shopkeeper-copy">
                                                                                        <strong class="sb-shopkeeper-name" x-text="npc.name"></strong>
                                                                                        <small class="sb-shopkeeper-hint" x-text="npc.image_url ? 'Com retrato disponível' : 'Sem retrato cadastrado'"></small>
                                                                                    </span>
                                                                                    <span
                                                                                        x-show="parseInt(shopNewShopkeeperNpcId) === parseInt(npc.id)"
                                                                                        class="sb-shopkeeper-check"
                                                                                    >✓</span>
                                                                                </button>
                                                                            </template>

                                                                            <div
                                                                                x-show="shopNewFilteredShopkeepers().length === 0"
                                                                                x-cloak
                                                                                class="sb-shopkeeper-empty"
                                                                            >
                                                                                Nenhum NPC encontrado.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <label
                                                                x-show="!shopNewShopkeeperNpcId"
                                                                x-cloak
                                                                class="mt-4 block"
                                                            >
                                                                <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Nome do atendente</span>
                                                                <input
                                                                    type="text"
                                                                    maxlength="150"
                                                                    x-model="shopNewAttendantName"
                                                                    placeholder="Ex.: Balric, o balconista"
                                                                    class="h-12 w-full rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[14px] font-semibold text-[#53150f] outline-none transition placeholder:text-[#a3978b] focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                >
                                                                <span class="mt-1.5 block text-[11px] leading-5 text-[#9a806d]">Use este campo quando o atendente não for um NPC cadastrado.</span>
                                                            </label>

                                                            <label class="mt-4 block">
                                                                <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Descrição</span>
                                                                <textarea
                                                                    rows="3"
                                                                    maxlength="10000"
                                                                    x-model="shopNewDescription"
                                                                    placeholder="Apresente a loja, o comerciante ou o tipo de mercadoria..."
                                                                    class="w-full resize-y rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 py-3 text-[14px] font-semibold leading-6 text-[#53150f] outline-none transition placeholder:text-[#a3978b] focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                ></textarea>
                                                            </label>

                                                            <label class="mt-4 block">
                                                                <span class="mb-1.5 block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Pasta / Local</span>
                                                                <select
                                                                    x-model="shopNewFolderId"
                                                                    class="h-12 w-full rounded-xl border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[14px] font-semibold text-[#53150f] outline-none transition focus:border-[#8a4b20] focus:ring-2 focus:ring-[#8a4b20]/10"
                                                                >
                                                                    <option :value="null">Sem pasta</option>
                                                                    <template x-for="folder in shopFolders" :key="'new-shop-folder-' + folder.id">
                                                                        <option :value="folder.id" x-text="folder.name"></option>
                                                                    </template>
                                                                </select>
                                                                <span class="mt-1.5 block text-[11px] leading-5 text-[#9a806d]">Opcional. Você também poderá arrastar a loja entre pastas depois.</span>
                                                            </label>
                                                        </section>

                                                        <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                            <div class="mb-4">
                                                                <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Imagens</p>
                                                                <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">Escolha o retrato do atendente e o cenário da vitrine.</p>
                                                            </div>

                                                            <div class="grid gap-3 sm:grid-cols-2">
                                                                <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                    <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Retrato</p>
                                                                    <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">Sem upload, usamos a imagem do NPC.</p>
                                                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                                                        <label class="inline-flex cursor-pointer items-center rounded-lg border border-[#c59263] bg-white px-3.5 py-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#f7eadb]">
                                                                            Escolher imagem
                                                                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleShopNewImage($event)">
                                                                        </label>
                                                                        <button x-show="shopNewImageFile" x-cloak type="button" class="rounded-lg px-3 py-2.5 text-[10px] font-black uppercase text-red-700 hover:bg-red-50" @click="clearShopNewImage()">Remover</button>
                                                                    </div>
                                                                    <p class="mt-2 text-[9px] font-semibold text-[#9a806d]" x-text="'JPG, PNG ou WEBP · limite atual: ' + formatShopFileSize(shopPortraitUploadLimitBytes)"></p>
                                                                    <p
                                                                        x-show="shopNewImageFeedback"
                                                                        x-cloak
                                                                        class="sb-shop-upload-feedback"
                                                                        :style="shopUploadFeedbackStyle(shopNewImageFeedbackType)"
                                                                        x-text="shopNewImageFeedback"
                                                                    ></p>
                                                                </div>

                                                                <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                    <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cenário</p>
                                                                    <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">Interior, rua, forja ou qualquer imagem de fundo.</p>
                                                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                                                        <label class="inline-flex cursor-pointer items-center rounded-lg border border-[#c59263] bg-white px-3.5 py-2.5 text-[10px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-[#f7eadb]">
                                                                            Escolher fundo
                                                                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleShopNewBackground($event)">
                                                                        </label>
                                                                        <button x-show="shopNewBackgroundFile" x-cloak type="button" class="rounded-lg px-3 py-2.5 text-[10px] font-black uppercase text-red-700 hover:bg-red-50" @click="clearShopNewBackground()">Remover</button>
                                                                    </div>
                                                                    <p class="mt-2 text-[9px] font-semibold text-[#9a806d]" x-text="'JPG, PNG ou WEBP · limite atual: ' + formatShopFileSize(shopBackgroundUploadLimitBytes)"></p>
                                                                    <p
                                                                        x-show="shopNewBackgroundFeedback"
                                                                        x-cloak
                                                                        class="sb-shop-upload-feedback"
                                                                        :style="shopUploadFeedbackStyle(shopNewBackgroundFeedbackType)"
                                                                        x-text="shopNewBackgroundFeedback"
                                                                    ></p>
                                                                </div>
                                                            </div>
                                                        </section>

                                                        <section class="rounded-2xl border border-[#e1d5c3] bg-white p-4 sm:p-5">
                                                            <div class="mb-4">
                                                                <p class="text-[12px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Aparência</p>
                                                                <p class="mt-1 text-[12px] leading-5 text-[#9a806d]">A prévia fica sempre visível ao lado enquanto você ajusta.</p>
                                                            </div>

                                                            <div class="mb-3 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                                    <div>
                                                                        <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Balcão da loja</p>
                                                                        <p class="mt-1 text-[11px] leading-5 text-[#9a806d]">Escolha se a cena deve ter um balcão na frente do atendente.</p>
                                                                    </div>
                                                                    <div class="grid shrink-0 grid-cols-2 gap-1 rounded-xl border border-[#ded0bd] bg-white p-1">
                                                                        <button type="button" class="rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewHasCounter ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-[#f5eee4]'" @click="shopNewHasCounter = true">Com balcão</button>
                                                                        <button type="button" class="rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="!shopNewHasCounter ? 'bg-[#6b1d14] text-white shadow-sm' : 'text-[#76553f] hover:bg-[#f5eee4]'" @click="shopNewHasCounter = false">Sem balcão</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="grid gap-3 sm:grid-cols-2">
                                                                <label class="flex items-center justify-between gap-4 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] px-4 py-3.5">
                                                                    <div>
                                                                        <span class="block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cor do fundo</span>
                                                                        <span class="mt-1 block text-[11px] font-semibold text-[#9a806d]" x-text="shopNewBackgroundColor"></span>
                                                                    </div>
                                                                    <input type="color" x-model="shopNewBackgroundColor" class="h-10 w-14 cursor-pointer rounded-lg border border-[#d8c7ab] bg-white p-1">
                                                                </label>

                                                                <label x-show="shopNewHasCounter" x-cloak class="flex items-center justify-between gap-4 rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] px-4 py-3.5">
                                                                    <div>
                                                                        <span class="block text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cor do balcão</span>
                                                                        <span class="mt-1 block text-[11px] font-semibold text-[#9a806d]" x-text="shopNewCounterColor"></span>
                                                                    </div>
                                                                    <input type="color" x-model="shopNewCounterColor" class="h-10 w-14 cursor-pointer rounded-lg border border-[#d8c7ab] bg-white p-1">
                                                                </label>
                                                            </div>

                                                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                                <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                    <div class="flex items-center justify-between gap-3">
                                                                        <div>
                                                                            <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Retrato</p>
                                                                            <p class="mt-1 text-[11px] text-[#9a806d]">Tamanho e área prioritária.</p>
                                                                        </div>
                                                                        <span class="rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-black text-[#6b1d14]" x-text="shopNewImageZoom + '%' "></span>
                                                                    </div>
                                                                    <input type="range" min="70" max="180" step="5" x-model.number="shopNewImageZoom" class="mt-3 w-full accent-[#6b1d14]">

                                                                    <div class="mt-3">
                                                                        <span class="mb-2 block text-[10px] font-black uppercase tracking-wider text-[#8c6239]">Enquadramento do atendente</span>
                                                                        <div class="grid grid-cols-3 gap-1 rounded-lg border border-[#ded0bd] bg-white p-1">
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewImagePosition === 'top' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewImagePosition = 'top'">Cima</button>
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewImagePosition === 'center' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewImagePosition = 'center'">Meio</button>
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewImagePosition === 'bottom' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewImagePosition = 'bottom'">Baixo</button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="rounded-xl border border-[#e3d7c5] bg-[#fdfaf5] p-4">
                                                                    <div class="flex items-center justify-between gap-3">
                                                                        <div>
                                                                            <p class="text-[11px] font-black uppercase tracking-wider text-[#76553f]">Cenário</p>
                                                                            <p class="mt-1 text-[11px] text-[#9a806d]">Zoom e área que deve permanecer visível.</p>
                                                                        </div>
                                                                        <span class="rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-black text-[#6b1d14]" x-text="shopNewBackgroundZoom + '%' "></span>
                                                                    </div>
                                                                    <input type="range" min="100" max="180" step="5" x-model.number="shopNewBackgroundZoom" class="mt-3 w-full accent-[#6b1d14]">

                                                                    <div class="mt-3">
                                                                        <span class="mb-2 block text-[10px] font-black uppercase tracking-wider text-[#8c6239]">Enquadramento do cenário</span>
                                                                        <div class="grid grid-cols-3 gap-1 rounded-lg border border-[#ded0bd] bg-white p-1">
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewBackgroundPosition === 'top' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewBackgroundPosition = 'top'">Cima</button>
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewBackgroundPosition === 'center' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewBackgroundPosition = 'center'">Meio</button>
                                                                            <button type="button" class="rounded-md px-2 py-2.5 text-[10px] font-black uppercase tracking-wider transition" :class="shopNewBackgroundPosition === 'bottom' ? 'bg-[#6b1d14] text-white' : 'text-[#76553f] hover:bg-[#f5efe6]'" @click="shopNewBackgroundPosition = 'bottom'">Baixo</button>
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
                                                                <div class="sb-shop-create-stage" :style="{ backgroundColor: shopNewBackgroundColor }">
                                                                    <template x-if="shopNewBackgroundPreview">
                                                                        <img
                                                                            :src="shopNewBackgroundPreview"
                                                                            alt="Fundo da loja"
                                                                            class="sb-shop-scene-image"
                                                                            :style="{
                                                                                transform: 'scale(' + (shopNewBackgroundZoom / 100) + ')',
                                                                                transformOrigin: shopNewBackgroundPosition === 'top' ? 'center top' : (shopNewBackgroundPosition === 'bottom' ? 'center bottom' : 'center center'),
                                                                                objectPosition: shopNewBackgroundPosition === 'top' ? 'center top' : (shopNewBackgroundPosition === 'bottom' ? 'center bottom' : 'center center')
                                                                            }"
                                                                            x-on:error="shopNewBackgroundFeedback = 'Não foi possível exibir a prévia do cenário.'; shopNewBackgroundFeedbackType = 'error'"
                                                                        >
                                                                    </template>

                                                                    <div
                                                                        x-show="!shopNewBackgroundPreview"
                                                                        class="absolute inset-0"
                                                                        style="background: linear-gradient(180deg, #6f4a33 0%, #523221 100%);"
                                                                    ></div>

                                                                    <div class="sb-shop-preview-shade"></div>

                                                                    <div class="sb-shop-preview-title">
                                                                        <div class="sb-shop-preview-status">
                                                                            <span class="sb-shop-preview-open-dot"></span>
                                                                            <span>Loja aberta · prévia</span>
                                                                        </div>
                                                                        <strong x-text="shopNewName || 'Sua Loja'"></strong>
                                                                        <small x-text="'Atendido por ' + shopNewAttendantDisplayName()"></small>
                                                                    </div>

                                                                    <div class="sb-shop-portrait-layer" :class="{ 'is-counterless': !shopNewHasCounter }">
                                                                        <template x-if="shopNewPortraitPreviewUrl()">
                                                                            <img
                                                                                :src="shopNewPortraitPreviewUrl()"
                                                                                alt="Atendente"
                                                                                class="sb-shop-portrait-image"
                                                                                :style="{
                                                                                    transform: 'scale(' + (shopNewImageZoom / 100) + ')',
                                                                                    transformOrigin: shopNewImagePosition === 'top' ? 'center top' : (shopNewImagePosition === 'bottom' ? 'center bottom' : 'center center'),
                                                                                    objectPosition: shopNewImagePosition === 'top' ? 'center top' : (shopNewImagePosition === 'bottom' ? 'center bottom' : 'center center')
                                                                                }"
                                                                                x-on:error="shopNewImageFeedback = shopNewImagePreview ? 'Não foi possível exibir a prévia do retrato.' : 'A imagem deste NPC não pôde ser carregada.'; shopNewImageFeedbackType = 'error'"
                                                                            >
                                                                        </template>
                                                                    </div>

                                                                    <div
                                                                        x-show="shopNewHasCounter"
                                                                        x-cloak
                                                                        class="sb-shop-counter"
                                                                        :style="{ '--counter-color': shopNewCounterColor }"
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

                                            <footer class="flex shrink-0 flex-col gap-3 border-t border-[#e1d5c3] bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                                <p class="text-[11px] font-semibold leading-5 text-[#8c6239]">A loja é criada fechada. Você poderá preparar estoque e preços antes de abrir para a Party.</p>
                                                <div class="flex shrink-0 justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        class="h-9 rounded-lg border border-[#d8c7ab] bg-[#fffdfa] px-4 text-[9px] font-black uppercase tracking-wider text-[#76553f] transition hover:bg-[#f5eee4]"
                                                        @click="shopCreateOpen = false"
                                                    >Cancelar</button>
                                                    <button
                                                        type="button"
                                                        class="h-9 rounded-lg bg-[#6b1d14] px-5 text-[9px] font-black uppercase tracking-wider text-white shadow-sm transition hover:bg-[#53150f] disabled:opacity-50"
                                                        :disabled="shopCreating"
                                                        @click="createShop()"
                                                    >
                                                        <span x-text="shopCreating ? 'Criando...' : 'Criar loja'"></span>
                                                    </button>
                                                </div>
                                            </footer>
                                        </div>
                                    </div>
                                    </template>
                                </section>

                                <template x-teleport="body">
                                    <div
                                        x-show="shopFolderModalOpen"
                                        x-cloak
                                        class="sb-folder-modal-layer"
                                        @keydown.escape.window="closeShopFolderEditor()"
                                    >
                                        <div class="sb-folder-modal-backdrop" @click="closeShopFolderEditor()"></div>

                                        <section class="sb-folder-modal">
                                            <header class="sb-folder-modal__head">
                                                <div>
                                                    <p class="text-[8px] font-black uppercase tracking-[0.12em] text-[#9a806d]">Organização</p>
                                                    <h4 class="mt-0.5 font-serif text-[22px] font-black text-[#53150f]" x-text="shopFolderEditingId ? 'Editar pasta' : 'Criar pasta'"></h4>
                                                </div>
                                                <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-[#d8c7ab] bg-[#fffdf8] text-[#76553f]" @click="closeShopFolderEditor()">×</button>
                                            </header>

                                            <div class="sb-folder-modal__body">
                                                <div class="sb-shop-folder-modal__content">
                                                    <div class="sb-shop-folder-modal__form">
                                                        <label class="sb-folder-field">
                                                            <span>Nome</span>
                                                            <input type="text" maxlength="120" x-model="shopFolderName" placeholder="Ex.: Distrito do Mercado">
                                                        </label>

                                                        <label class="sb-folder-field">
                                                            <span>Descrição</span>
                                                            <textarea rows="4" maxlength="10000" x-model="shopFolderDescription" placeholder="Resumo curto para reconhecer este grupo de lojas."></textarea>
                                                        </label>
                                                    </div>

                                                    <aside class="sb-shop-folder-modal__cover">
                                                        <div class="sb-folder-cover-preview">
                                                            <template x-if="shopFolderImagePreview">
                                                                <img :src="shopFolderImagePreview" alt="Prévia da pasta">
                                                            </template>
                                                            <template x-if="!shopFolderImagePreview">
                                                                <div class="text-center">
                                                                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#efe2d1] text-[#8a4b20]">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-7 w-7">
                                                                            <path d="M3.5 7.5h6l1.5 2h9.5v8.5A2.5 2.5 0 0 1 18 20.5H6A2.5 2.5 0 0 1 3.5 18V7.5Z"></path>
                                                                            <path d="M3.5 7.5V6A2.5 2.5 0 0 1 6 3.5h3.5l2 2H18A2.5 2.5 0 0 1 20.5 8"></path>
                                                                        </svg>
                                                                    </div>
                                                                    <p class="mt-3 text-[9px] font-bold text-[#8c6239]">Capa opcional</p>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="sb-shop-folder-modal__cover-actions">
                                                            <label class="cursor-pointer rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-3 py-2.5 text-center text-[8px] font-black uppercase tracking-wider text-[#6b1d14] transition hover:bg-white">
                                                                Escolher imagem
                                                                <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="handleShopFolderImage($event)">
                                                            </label>
                                                            <button x-show="shopFolderImagePreview" x-cloak type="button" class="rounded-xl border border-transparent px-3 py-2 text-[8px] font-black uppercase text-red-700 transition hover:border-red-100 hover:bg-red-50" @click="removeShopFolderImage()">Remover imagem</button>
                                                        </div>
                                                    </aside>
                                                </div>
                                            </div>

                                            <footer class="sb-folder-modal__footer">
                                                <button
                                                    type="button"
                                                    class="min-h-10 rounded-xl border border-[#d8c7ab] bg-[#fffdf8] px-5 text-[9px] font-black uppercase tracking-[0.05em] text-[#76553f] transition hover:bg-white"
                                                    @click="closeShopFolderEditor()"
                                                >
                                                    Cancelar
                                                </button>

                                                <button
                                                    type="button"
                                                    class="inline-flex min-h-10 min-w-[145px] items-center justify-center gap-2 rounded-xl bg-[#6b1d14] px-6 text-[9px] font-black uppercase tracking-[0.05em] text-white shadow-sm transition hover:bg-[#7b2117] disabled:cursor-not-allowed disabled:opacity-50"
                                                    :disabled="shopFolderSaving || !String(shopFolderName ?? '').trim()"
                                                    @click="saveShopFolder()"
                                                >
                                                    <svg
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.9"
                                                        class="h-3.5 w-3.5"
                                                        aria-hidden="true"
                                                    >
                                                        <path d="M12 5v14"></path>
                                                        <path d="M5 12h14"></path>
                                                    </svg>

                                                    <span
                                                        x-text="
                                                            shopFolderSaving
                                                                ? 'Salvando...'
                                                                : (
                                                                    shopFolderEditingId
                                                                        ? 'Salvar pasta'
                                                                        : 'Criar pasta'
                                                                )
                                                        "
                                                    ></span>
                                                </button>
                                            </footer>
                                        </section>
                                    </div>
                                </template>


                                {{--
                                    Workspaces separados por domínio.
                                    Mantemos os modais compartilhados neste loader por enquanto.
                                --}}
                                @include('campaigns.components.master-shield.drawers.loot.espolios')
                                @include('campaigns.components.master-shield.drawers.loot.lojas')
                            </div>
                        </template>
                    </div>

                    {{-- VAULT EM MODAL --}}
                    <div
                        x-show="lootPickerOpen && lootActiveDrop"
                        x-cloak
                        class="fixed inset-0 z-[260] flex items-center justify-center p-4 sm:p-6"
                    >
                        <div
                            class="absolute inset-0 bg-black/55 backdrop-blur-[2px]"
                            @click="closeLootPicker()"
                        ></div>

                        <section
                            x-transition
                            class="relative z-10 flex max-h-[86vh] w-full max-w-5xl flex-col overflow-hidden rounded-[22px] border border-[#cdbb9f] bg-[#f7f2e9] shadow-2xl"
                        >
                            <header
                                class="flex shrink-0 flex-col gap-3 border-b border-[#d8c7ab]/70 bg-[#fffdf8] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p class="text-[8px] font-black uppercase tracking-[0.16em] text-[#8c6239]">
                                        Vault do Mestre
                                    </p>
                                    <h3 class="mt-0.5 font-serif text-xl font-black text-[#53150f]">
                                        Adicionar itens ao Espólio
                                    </h3>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input
                                        type="search"
                                        x-model.debounce.180ms="lootVaultSearch"
                                        placeholder="Buscar no Vault..."
                                        class="h-10 w-[240px] max-w-full rounded-xl border border-[#cdbb9f] bg-white px-3 text-[10px] font-semibold text-[#53150f] focus:border-[#6b1d14] focus:ring-[#6b1d14]/10"
                                    >

                                    <button
                                        type="button"
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg font-black text-[#8c6239] hover:bg-[#f1e8db] hover:text-[#53150f]"
                                        @click="closeLootPicker()"
                                    >
                                        ×
                                    </button>
                                </div>
                            </header>

                            <div class="min-h-0 flex-1 overflow-y-auto p-5">
                                <div
                                    x-show="lootFilteredVaultItems.length > 0"
                                    class="grid gap-3 md:grid-cols-2"
                                >
                                    <template x-for="item in lootFilteredVaultItems" :key="'vault-modal-' + item.id">
                                        <article
                                            class="grid grid-cols-[64px_minmax(0,1fr)_auto] gap-3 rounded-2xl border border-[#d8c7ab]/70 bg-[#fffdf8] p-3 transition hover:border-[#b08c62] hover:shadow-sm"
                                        >
                                            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-[#cdbb9f]/60 bg-[#efe9dc]">
                                                <template x-if="item.image_url">
                                                    <img
                                                        :src="item.image_url"
                                                        :alt="item.name"
                                                        class="h-full w-full object-cover"
                                                    >
                                                </template>
                                                <template x-if="!item.image_url">
                                                    <span class="font-serif text-xl font-black text-[#a28670]">?</span>
                                                </template>
                                            </div>

                                            <div class="min-w-0">
                                                <h4 class="truncate font-serif text-[13px] font-black text-[#53150f]" x-text="item.name"></h4>
                                                <p
                                                    class="mt-0.5 truncate text-[8px] font-bold text-[#8c6239]"
                                                    x-text="lootVaultClassification(item)"
                                                ></p>
                                                <p
                                                    x-show="item.description"
                                                    class="mt-1 line-clamp-2 text-[8.5px] leading-4 text-[#6f5745]"
                                                    x-text="item.description"
                                                ></p>
                                            </div>

                                            <button
                                                type="button"
                                                class="self-center rounded-xl bg-[#6b1d14] px-3 py-2 text-[7px] font-black uppercase tracking-wider text-white disabled:opacity-50"
                                                :disabled="lootAddingVaultId !== null"
                                                @click="addVaultItemToLoot(item)"
                                            >
                                                <span x-show="parseInt(lootAddingVaultId) !== parseInt(item.id)">Adicionar</span>
                                                <span x-show="parseInt(lootAddingVaultId) === parseInt(item.id)" x-cloak>...</span>
                                            </button>
                                        </article>
                                    </template>
                                </div>

                                <div
                                    x-show="lootFilteredVaultItems.length === 0"
                                    x-cloak
                                    class="rounded-2xl border border-dashed border-[#cdbb9f] bg-[#fffdf8] px-6 py-12 text-center"
                                >
                                    <p
                                        class="font-serif text-base font-black text-[#53150f]"
                                        x-text="vaultItems.length ? 'Nenhum item encontrado' : 'Seu Vault está vazio'"
                                    ></p>

                                    <a
                                        href="{{ route('master.vault.items.index') }}"
                                        class="mt-3 inline-flex text-[8px] font-black uppercase tracking-wider text-[#6b1d14] hover:underline"
                                    >
                                        Abrir Vault →
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>



                {{-- SESSÃO DE LOJA — ADICIONAR PRODUTO --}}
                <div
                    x-show="shopPickerOpen && lootActiveShop"
                    x-cloak
                    class="fixed inset-0 z-[272] flex items-center justify-center p-3 sm:p-6"
                >
                    <div
                        class="absolute inset-0 bg-black/60 backdrop-blur-[2px]"
                        @click="closeShopPicker()"
                    ></div>

                    <section
                        x-transition
                        class="relative z-10 flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-[24px] border border-[#cdbb9f] bg-[#f7f2e9] shadow-2xl"
                    >
                        <header class="sb-shop-picker-head">
                            <div class="sb-shop-picker-head__brand">
                                <span class="sb-shop-picker-head__brand-icon" aria-hidden="true">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M4 9.5h16v10H4z"></path>
                                        <path d="m5 9.5 1.2-5h11.6l1.2 5"></path>
                                        <path d="M8 19.5v-5h4v5"></path>
                                        <path d="M4 9.5c0 1.4 1 2.5 2.25 2.5S8.5 10.9 8.5 9.5"></path>
                                        <path d="M8.5 9.5c0 1.4 1 2.5 2.25 2.5S13 10.9 13 9.5"></path>
                                        <path d="M13 9.5c0 1.4 1 2.5 2.25 2.5s2.25-1.1 2.25-2.5"></path>
                                        <path d="M17.5 9.5c0 1.4 1 2.5 2.25 2.5"></path>
                                    </svg>
                                </span>

                                <div>
                                    <p class="sb-shop-picker-head__eyebrow">Sessão de Loja</p>
                                    <h3>Adicionar produto</h3>
                                    
                                </div>
                            </div>

                            <div class="sb-shop-picker-head__actions">
                                <button
                                    type="button"
                                    class="sb-shop-price-help-trigger"
                                    @click="openShopPriceHelp()"
                                >
                                    <span>?</span>
                                    Ajuda com preço?
                                </button>

                                <button
                                    type="button"
                                    class="sb-shop-picker-close"
                                    @click="closeShopPicker()"
                                    aria-label="Fechar"
                                >×</button>
                            </div>
                        </header>

                        <div
                            x-show="shopItemAddedNoticeVisible"
                            x-cloak
                            x-transition.opacity.scale.95
                            class="sb-shop-added-toast"
                            role="status"
                            aria-live="polite"
                        >
                            <span class="sb-shop-added-toast__icon">✓</span>

                            <span>
                                <strong>Item adicionado à loja</strong>
                                <small x-text="shopItemAddedNotice"></small>
                            </span>
                        </div>

                        <div class="sb-shop-picker-layout">
                            <section class="sb-shop-picker-catalog">
                                <div class="sb-shop-picker-filters">
                                    <label class="sb-shop-picker-search">
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            aria-hidden="true"
                                        >
                                            <circle cx="11" cy="11" r="6.5"></circle>
                                            <path d="m16 16 4 4"></path>
                                        </svg>

                                        <input
                                            type="search"
                                            x-model.debounce.180ms="shopVaultSearch"
                                            placeholder="Buscar por nome, descrição ou raridade..."
                                        >
                                    </label>

                                    <div class="sb-shop-picker-filter-row">
                                        <div
                                            class="sb-shop-picker-nature"
                                            role="group"
                                            aria-label="Natureza do item"
                                        >
                                            <button
                                                type="button"
                                                :class="{ 'is-active': shopVaultNature === 'all' }"
                                                @click="shopVaultNature = 'all'"
                                            >
                                                Todos
                                            </button>

                                            <button
                                                type="button"
                                                :class="{ 'is-active': shopVaultNature === 'mundane' }"
                                                @click="shopVaultNature = 'mundane'"
                                            >
                                                Mundanos
                                            </button>

                                            <button
                                                type="button"
                                                :class="{ 'is-active': shopVaultNature === 'magical' }"
                                                @click="shopVaultNature = 'magical'"
                                            >
                                                Mágicos
                                            </button>
                                        </div>

                                        <label class="sb-shop-picker-rarity">
                                            <span>Raridade</span>

                                            <select
                                                x-model="shopVaultRarity"
                                            >
                                                <option value="all">
                                                    Todas
                                                </option>

                                                <template
                                                    x-for="rarity in shopVaultRarityOptions()"
                                                    :key="'shop-rarity-' + rarity.value"
                                                >
                                                    <option
                                                        :value="rarity.value"
                                                        x-text="rarity.label"
                                                    ></option>
                                                </template>
                                            </select>
                                        </label>
                                    </div>

                                    <div
                                        x-show="shopVaultActiveFilterCount() > 0"
                                        x-cloak
                                        class="sb-shop-picker-filter-summary"
                                    >
                                        <button
                                            type="button"
                                            @click="clearShopVaultFilters()"
                                        >
                                            Limpar filtros
                                            <span
                                                x-text="'(' + shopVaultActiveFilterCount() + ')'"
                                            ></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="sb-shop-picker-items">
                                    <template
                                        x-for="item in shopFilteredVaultItems()"
                                        :key="'shop-vault-' + item.id"
                                    >
                                        <button
                                            type="button"
                                            class="sb-shop-picker-item"
                                            :class="{
                                                'is-selected':
                                                    parseInt(shopSelectedVaultItem?.id)
                                                    ===
                                                    parseInt(item.id)
                                            }"
                                            @click="selectShopVaultItem(item)"
                                        >
                                            <div class="sb-shop-picker-item__image">
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

                                            <div class="sb-shop-picker-item__copy">
                                                <div class="sb-shop-picker-item__meta">
                                                    <span
                                                        class="sb-shop-picker-item__nature"
                                                        :class="{
                                                            'is-magical':
                                                                shopVaultItemNature(item)
                                                                ===
                                                                'magical'
                                                        }"
                                                        x-text="
                                                            shopVaultItemNature(item) === 'magical'
                                                                ? 'Item Mágico'
                                                                : 'Item Mundano'
                                                        "
                                                    ></span>

                                                    <span
                                                        x-show="item?.rarity"
                                                        x-cloak
                                                        class="sb-shop-picker-item__rarity"
                                                        x-text="shopVaultRarityLabel(item?.rarity)"
                                                    ></span>
                                                </div>

                                                <h4 x-text="item.name"></h4>

                                                <p
                                                    x-show="item.description"
                                                    x-cloak
                                                    x-text="item.description"
                                                ></p>
                                            </div>

                                            <span class="sb-shop-picker-item__select">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    aria-hidden="true"
                                                >
                                                    <path d="m8 12 2.5 2.5L16 9"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </template>

                                    <div
                                        x-show="shopFilteredVaultItems().length === 0"
                                        x-cloak
                                        class="sb-shop-picker-empty"
                                    >
                                        <div>◇</div>

                                        <h4>Nenhum item encontrado</h4>

                                        <p>
                                            Ajuste o nome, a raridade ou o tipo do item.
                                        </p>

                                        <button
                                            type="button"
                                            @click="clearShopVaultFilters()"
                                        >
                                            Limpar filtros
                                        </button>
                                    </div>
                                </div>
                            </section>

                            <aside class="sb-shop-picker-config">
                                <template x-if="shopSelectedVaultItem">
                                    <div class="sb-shop-picker-config__content">
                                        <section class="sb-shop-picker-selected">
                                            

                                            <div class="sb-shop-picker-selected__image">
                                                <template x-if="shopSelectedVaultItem?.image_url">
                                                    <img
                                                        :src="shopSelectedVaultItem.image_url"
                                                        :alt="shopSelectedVaultItem.name"
                                                    >
                                                </template>

                                                <template x-if="!shopSelectedVaultItem?.image_url">
                                                    <span>◆</span>
                                                </template>
                                            </div>

                                            <div class="sb-shop-picker-selected__copy">
                                                

                                                <h4
                                                    x-text="shopSelectedVaultItem?.name"
                                                ></h4>

                                                <p
                                                    x-text="lootVaultClassification(shopSelectedVaultItem)"
                                                ></p>

                                            </div>
                                        </section>

                                        <section class="sb-shop-picker-config-card is-stock">
                                            

                                            <div class="sb-shop-picker-config-card__head">
                                                <strong>Quantidade</strong>
                                            </div>

                                            <div class="sb-shop-quantity-stepper">
                                                <button
                                                    type="button"
                                                    @click="adjustShopAddQuantity(-1)"
                                                    :disabled="shopAddQuantity <= 1"
                                                    aria-label="Diminuir quantidade"
                                                >−</button>

                                                <input
                                                    type="number"
                                                    min="1"
                                                    max="999999"
                                                    x-model.number="shopAddQuantity"
                                                    @input="normalizeShopAddQuantity()"
                                                >

                                                <button
                                                    type="button"
                                                    @click="adjustShopAddQuantity(1)"
                                                    aria-label="Aumentar quantidade"
                                                >+</button>
                                            </div>
                                        </section>

                                        <section class="sb-shop-picker-config-card is-price">
                                            

                                            <div class="sb-shop-picker-config-card__head">
                                                <strong>Preço por unidade</strong>

                                                <button
                                                    type="button"
                                                    @click="openShopPriceHelp()"
                                                >
                                                    Ajuda com preço?
                                                </button>
                                            </div>

                                            <div class="sb-shop-price-coins">
                                                <template
                                                    x-for="coin in lootCoinDefinitions"
                                                    :key="'shop-add-price-' + coin.key"
                                                >
                                                    <article
                                                        class="sb-shop-price-coin"
                                                        :class="[
                                                            'coin-' + coin.key,
                                                            {
                                                                'is-active':
                                                                    (parseInt(shopPriceAmounts[coin.key]) || 0) > 0
                                                            }
                                                        ]"
                                                    >
                                                        <div class="sb-shop-price-coin__head">
                                                            <span class="sb-shop-price-coin__identity">
                                                                <span
                                                                    class="sb-shop-price-coin__icon"
                                                                    aria-hidden="true"
                                                                >
                                                                    <template x-if="coin.key === 'copper'">
                                                                        <svg viewBox="0 0 24 24" fill="none">
                                                                            <circle cx="12" cy="12" r="8.25"></circle>
                                                                            <path d="M12 7.3v9.4M8.8 9.5h6.4M8.8 14.5h6.4"></path>
                                                                        </svg>
                                                                    </template>

                                                                    <template x-if="coin.key === 'silver'">
                                                                        <svg viewBox="0 0 24 24" fill="none">
                                                                            <circle cx="12" cy="12" r="8.25"></circle>
                                                                            <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"></path>
                                                                        </svg>
                                                                    </template>

                                                                    <template x-if="coin.key === 'electrum'">
                                                                        <svg viewBox="0 0 24 24" fill="none">
                                                                            <circle cx="12" cy="12" r="8.25"></circle>
                                                                            <path d="M8.3 12h7.4M12 8.3v7.4"></path>
                                                                        </svg>
                                                                    </template>

                                                                    <template x-if="coin.key === 'gold'">
                                                                        <svg viewBox="0 0 24 24" fill="none">
                                                                            <circle cx="12" cy="12" r="8.25"></circle>
                                                                            <path d="m12 7 1.4 3 3.3.4-2.4 2.3.7 3.3-3-1.6-3 1.6.7-3.3-2.4-2.3 3.3-.4L12 7Z"></path>
                                                                        </svg>
                                                                    </template>

                                                                    <template x-if="coin.key === 'platinum'">
                                                                        <svg viewBox="0 0 24 24" fill="none">
                                                                            <circle cx="12" cy="12" r="8.25"></circle>
                                                                            <path d="M8.5 8.5h7v7h-7zM10.5 6.5h3M10.5 17.5h3"></path>
                                                                        </svg>
                                                                    </template>
                                                                </span>

                                                                <span class="sb-shop-price-coin__meta">
                                                                    <strong x-text="coin.abbr"></strong>
                                                                    <small x-text="coin.label"></small>
                                                                </span>
                                                            </span>
                                                        </div>

                                                        <div class="sb-shop-price-coin__input">
                                                            <input
                                                                type="text"
                                                                inputmode="numeric"
                                                                autocomplete="off"
                                                                :value="
                                                                    formatShopCoinNumber(
                                                                        shopPriceAmounts[coin.key]
                                                                    )
                                                                "
                                                                :class="{
                                                                    'is-long':
                                                                        String(
                                                                            Math.max(
                                                                                0,
                                                                                parseInt(shopPriceAmounts[coin.key]) || 0
                                                                            )
                                                                        ).length >= 4,
                                                                    'is-very-long':
                                                                        String(
                                                                            Math.max(
                                                                                0,
                                                                                parseInt(shopPriceAmounts[coin.key]) || 0
                                                                            )
                                                                        ).length >= 7
                                                                }"
                                                                @focus="$event.target.select()"
                                                                @input="
                                                                    setShopPriceCoinInput(
                                                                        coin.key,
                                                                        $event.target.value
                                                                    );
                                                                    $event.target.value =
                                                                        formatShopCoinNumber(
                                                                            shopPriceAmounts[coin.key]
                                                                        );
                                                                "
                                                            >
                                                        </div>
                                                    </article>
                                                </template>
                                            </div>

                                            <div class="sb-shop-price-summary">
                                                <div>
                                                    <span>Preço definido</span>

                                                    <strong
                                                        x-text="shopPriceDraftInline(shopPriceAmounts)"
                                                    ></strong>
                                                </div>

                                            </div>
                                        </section>
                                    </div>
                                </template>

                                <div
                                    x-show="!shopSelectedVaultItem"
                                    x-cloak
                                    class="sb-shop-picker-config-empty"
                                >
                                    <div class="sb-shop-picker-config-empty__icon">
                                        ◇
                                    </div>

                                    <h4>Escolha um produto</h4>

                                    <p>
                                        Selecione um item à esquerda para definir estoque e preço.
                                    </p>
                                </div>

                                <footer
                                    x-show="shopSelectedVaultItem"
                                    x-cloak
                                    class="sb-shop-picker-submit"
                                >
                                    <div>
                                        

                                        <strong
                                            x-text="
                                                shopAddQuantity
                                                + '× '
                                                + (shopSelectedVaultItem?.name || '')
                                            "
                                        ></strong>
                                    </div>

                                    <button
                                        type="button"
                                        :disabled="
                                            shopAdding
                                            || !shopPriceHasValue(shopPriceAmounts)
                                        "
                                        @click="addSelectedVaultItemToShop()"
                                    >
                                        <span
                                            x-text="
                                                shopAdding
                                                    ? 'Adicionando...'
                                                    : 'Adicionar à loja'
                                            "
                                        ></span>
                                    </button>
                                </footer>
                            </aside>
                        </div>
                    </section>
                </div>

                {{-- AJUDA RÁPIDA DE PREÇOS --}}
                <div
                    x-show="shopPriceHelpOpen"
                    x-cloak
                    class="sb-price-guide-layer"
                    @keydown.escape.window="shopPriceHelpOpen && closeShopPriceHelp()"
                >
                    <div
                        class="sb-price-guide-backdrop"
                        @click="closeShopPriceHelp()"
                    ></div>

                    <section
                        x-data="{
                            guideTab: 'magic',

                            setGuideTab(tab) {
                                this.guideTab = tab;
                            },
                        }"
                        x-transition.opacity.scale.95
                        class="sb-price-guide sb-price-guide-v2"
                        role="dialog"
                        aria-modal="true"
                        aria-label="Ajuda com preço"
                    >
                        <header class="sb-price-guide__head sb-price-guide-v2__head">
                            <div>
                                <span>Referência rápida</span>
                                <h3>Ajuda com preço</h3>
                                <p>
                                    Escolha uma categoria. Os valores servem como referência rápida para a economia da campanha.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="closeShopPriceHelp()"
                                aria-label="Fechar ajuda"
                            >×</button>
                        </header>

                        <nav
                            class="sb-price-guide-tabs"
                            role="tablist"
                            aria-label="Categorias de preço"
                        >
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="guideTab === 'magic'"
                                :class="{ 'is-active': guideTab === 'magic' }"
                                @click="setGuideTab('magic')"
                            >
                                <span class="sb-price-guide-tabs__icon">✦</span>

                                <span>
                                    <strong>Itens Mágicos</strong>
                                    <small>Raridade e poções</small>
                                </span>
                            </button>

                            <button
                                type="button"
                                role="tab"
                                :aria-selected="guideTab === 'scrolls'"
                                :class="{ 'is-active': guideTab === 'scrolls' }"
                                @click="setGuideTab('scrolls')"
                            >
                                <span class="sb-price-guide-tabs__icon">⌁</span>

                                <span>
                                    <strong>Pergaminhos</strong>
                                    <small>Nível e preço</small>
                                </span>
                            </button>

                            <button
                                type="button"
                                role="tab"
                                :aria-selected="guideTab === 'mundane'"
                                :class="{ 'is-active': guideTab === 'mundane' }"
                                @click="setGuideTab('mundane')"
                            >
                                <span class="sb-price-guide-tabs__icon">◇</span>

                                <span>
                                    <strong>Mundanos</strong>
                                    <small>Equipamentos e escassez</small>
                                </span>
                            </button>
                        </nav>

                        <div class="sb-price-guide-v2__body">
                            {{-- ITENS MÁGICOS --}}
                            <section
                                x-show="guideTab === 'magic'"
                                x-cloak
                                class="sb-price-guide-panel"
                                role="tabpanel"
                            >
                                <div class="sb-price-guide-panel__intro">
                                    <div>
                                        <span>Itens mágicos</span>
                                        <h4>Faixa por raridade</h4>
                                    </div>

                                    <p>
                                        Use a raridade como ponto de partida e ajuste por utilidade, demanda e disponibilidade local.
                                    </p>
                                </div>

                                <div class="sb-price-guide-magic-layout">
                                    <div class="sb-price-guide-table sb-price-guide-table-v2">
                                        <div class="is-head">
                                            <span>Raridade</span>
                                            <span>Faixa sugerida</span>
                                        </div>

                                        <div>
                                            <span>Comum</span>
                                            <strong>50–100 PO</strong>
                                        </div>

                                        <div>
                                            <span>Incomum</span>
                                            <strong>101–500 PO</strong>
                                        </div>

                                        <div>
                                            <span>Raro</span>
                                            <strong>501–5.000 PO</strong>
                                        </div>

                                        <div>
                                            <span>Muito Raro</span>
                                            <strong>5.001–50.000 PO</strong>
                                        </div>

                                        <div>
                                            <span>Lendário</span>
                                            <strong>50.001+ PO</strong>
                                        </div>

                                        <div class="is-artifact">
                                            <span>Artefato</span>
                                            <strong>Sem preço fixo</strong>
                                        </div>
                                    </div>

                                    <div class="sb-price-guide-examples">
                                        <header>
                                            <span>Exemplos rápidos</span>
                                            <strong>Poções</strong>
                                        </header>

                                        <div class="sb-price-guide-example-row">
                                            <span class="sb-price-guide-example-dot is-common"></span>

                                            <div>
                                                <strong>Poção de Cura</strong>
                                                <small>Comum</small>
                                            </div>

                                            <b>50 PO</b>
                                        </div>

                                        <div class="sb-price-guide-example-row">
                                            <span class="sb-price-guide-example-dot is-uncommon"></span>

                                            <div>
                                                <strong>Poção de Cura Maior</strong>
                                                <small>Incomum</small>
                                            </div>

                                            <b>150 PO</b>
                                        </div>

                                        <div class="sb-price-guide-example-row">
                                            <span class="sb-price-guide-example-dot is-rare"></span>

                                            <div>
                                                <strong>Poção de Cura Superior</strong>
                                                <small>Raro</small>
                                            </div>

                                            <b>1.500 PO</b>
                                        </div>

                                        <div class="sb-price-guide-example-row">
                                            <span class="sb-price-guide-example-dot is-very-rare"></span>

                                            <div>
                                                <strong>Poção de Cura Suprema</strong>
                                                <small>Muito Raro</small>
                                            </div>

                                            <b>15.000 PO</b>
                                        </div>
                                    </div>
                                </div>

                                <div class="sb-price-guide-tip">
                                    <span>✦</span>

                                    <p>
                                        Consumíveis normalmente ficam na parte baixa ou média da faixa; itens permanentes e muito desejados podem ficar mais próximos do topo.
                                    </p>
                                </div>
                            </section>

                            {{-- PERGAMINHOS --}}
                            <section
                                x-show="guideTab === 'scrolls'"
                                x-cloak
                                class="sb-price-guide-panel"
                                role="tabpanel"
                            >
                                <div class="sb-price-guide-panel__intro">
                                    <div>
                                        <span>Pergaminhos</span>
                                        <h4>Referência por nível da magia</h4>
                                    </div>

                                    <p>
                                        Valores sugeridos para compra em loja. Magias raras, proibidas ou muito procuradas podem custar mais.
                                    </p>
                                </div>

                                <div class="sb-scroll-price-table-v2">
                                    <div class="is-head">
                                        <span>Nível</span>
                                        <span>Raridade</span>
                                        <span>Preço sugerido</span>
                                        <span>Referência</span>
                                    </div>

                                    <div>
                                        <strong>Truque</strong>
                                        <span>Comum</span>
                                        <b>25 PO</b>
                                        <small>Utilidade simples</small>
                                    </div>

                                    <div>
                                        <strong>1º</strong>
                                        <span>Comum</span>
                                        <b>75 PO</b>
                                        <small>Magia básica</small>
                                    </div>

                                    <div>
                                        <strong>2º</strong>
                                        <span>Incomum</span>
                                        <b>250 PO</b>
                                        <small>Recurso especializado</small>
                                    </div>

                                    <div>
                                        <strong>3º</strong>
                                        <span>Incomum</span>
                                        <b>500 PO</b>
                                        <small>Magia relevante</small>
                                    </div>

                                    <div>
                                        <strong>4º</strong>
                                        <span>Raro</span>
                                        <b>2.500 PO</b>
                                        <small>Poder alto</small>
                                    </div>

                                    <div>
                                        <strong>5º</strong>
                                        <span>Raro</span>
                                        <b>5.000 PO</b>
                                        <small>Recurso raro</small>
                                    </div>

                                    <div>
                                        <strong>6º</strong>
                                        <span>Muito Raro</span>
                                        <b>15.000 PO</b>
                                        <small>Magia excepcional</small>
                                    </div>

                                    <div>
                                        <strong>7º</strong>
                                        <span>Muito Raro</span>
                                        <b>25.000 PO</b>
                                        <small>Mercado restrito</small>
                                    </div>

                                    <div>
                                        <strong>8º</strong>
                                        <span>Muito Raro</span>
                                        <b>50.000 PO</b>
                                        <small>Extremamente escasso</small>
                                    </div>

                                    <div>
                                        <strong>9º</strong>
                                        <span>Lendário</span>
                                        <b>100.000+ PO</b>
                                        <small>Quase impossível de encontrar</small>
                                    </div>
                                </div>

                                <div class="sb-price-guide-tip">
                                    <span>⌁</span>

                                    <p>
                                        Pergaminhos são consumíveis: o preço pode variar bastante conforme acesso a magos, bibliotecas, guildas e restrições da região.
                                    </p>
                                </div>
                            </section>

                            {{-- MUNDANOS --}}
                            <section
                                x-show="guideTab === 'mundane'"
                                x-cloak
                                class="sb-price-guide-panel"
                                role="tabpanel"
                            >
                                <div class="sb-price-guide-panel__intro">
                                    <div>
                                        <span>Itens mundanos</span>
                                        <h4>Equipamentos comuns</h4>
                                    </div>

                                    <p>
                                        Use o preço base do equipamento e aplique escassez apenas quando fizer sentido para a região.
                                    </p>
                                </div>

                                <div class="sb-mundane-reference-layout">
                                    <div class="sb-mundane-items-table">
                                        <div class="is-head">
                                            <span>Item</span>
                                            <span>Preço base</span>
                                        </div>

                                        <div>
                                            <span>Tocha</span>
                                            <strong>1 PC</strong>
                                        </div>

                                        <div>
                                            <span>Rações — 1 dia</span>
                                            <strong>5 PP</strong>
                                        </div>

                                        <div>
                                            <span>Corda de cânhamo</span>
                                            <strong>1 PO</strong>
                                        </div>

                                        <div>
                                            <span>Mochila</span>
                                            <strong>2 PO</strong>
                                        </div>

                                        <div>
                                            <span>Algemas</span>
                                            <strong>2 PO</strong>
                                        </div>

                                        <div>
                                            <span>Kit de Curandeiro</span>
                                            <strong>5 PO</strong>
                                        </div>

                                        <div>
                                            <span>Fechadura</span>
                                            <strong>10 PO</strong>
                                        </div>

                                        <div>
                                            <span>Lupa</span>
                                            <strong>100 PO</strong>
                                        </div>

                                        <div>
                                            <span>Luneta</span>
                                            <strong>1.000 PO</strong>
                                        </div>
                                    </div>

                                    <div class="sb-mundane-rarity">
                                        <header>
                                            <span>Exemplo de disponibilidade</span>
                                            <strong>Escassez local</strong>
                                        </header>

                                        <article class="is-common">
                                            <div>
                                                <strong>Comum</strong>
                                                <small>Encontrado normalmente.</small>
                                            </div>

                                            <b>×1</b>
                                        </article>

                                        <article class="is-scarce">
                                            <div>
                                                <strong>Escasso</strong>
                                                <small>Especializado ou pouco disponível.</small>
                                            </div>

                                            <b>×2–5</b>
                                        </article>

                                        <article class="is-exotic">
                                            <div>
                                                <strong>Exótico / Luxo</strong>
                                                <small>Importado, ilegal ou raro naquela região.</small>
                                            </div>

                                            <b>×5–20</b>
                                        </article>

                                        <p>
                                            Ex.: uma fechadura de 10 PO pode custar 20–50 PO em uma região onde esse tipo de item é escasso.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <footer class="sb-price-guide__footer sb-price-guide-v2__footer">
                            <span>
                                Referência de mesa: adapte os valores à economia e à disponibilidade da sua campanha.
                            </span>

                            <button
                                type="button"
                                @click="closeShopPriceHelp()"
                            >
                                Fechar
                            </button>
                        </footer>
                    </section>
                </div>

                @include('campaigns.components.master-shield.drawers.loot.quick-items')