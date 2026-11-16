{{-- Seletor compartilhado pelo Cofre e pela visualização de uma pasta. --}}
<div
    id="npcFolderPicker"
    class="npc-folder-picker hidden fixed inset-0 z-[100] flex items-center justify-center px-4 py-8"
    role="dialog"
    aria-modal="true"
    aria-labelledby="npcFolderPickerTitle"
    aria-hidden="true"
>
    <div class="npc-folder-picker__backdrop absolute inset-0" data-close-npc-picker="true"></div>
    <div class="npc-folder-picker__panel relative w-full max-w-md overflow-hidden rounded-2xl" tabindex="-1">
        <div class="npc-folder-picker__header px-6 pt-5 pb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <span class="npc-folder-picker__eyebrow">ORGANIZAÇÃO DO COFRE</span>
                    <h2 id="npcFolderPickerTitle" class="mt-1 font-serif text-xl font-bold text-[#5b2017]">Enviar para pasta</h2>
                    <p id="npcFolderPickerNpcName" class="mt-1 text-xs text-[#8c6239] break-words"></p>
                </div>
                <button type="button" class="npc-folder-picker__close" data-close-npc-picker="true" aria-label="Fechar seletor de pastas">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <label for="npcFolderSearch" class="sr-only">Buscar pasta</label>
            <div class="npc-folder-picker__search mt-4 flex items-center gap-2 rounded-xl px-3">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 shrink-0" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.4 4.4"/></svg>
                <input id="npcFolderSearch" type="search" autocomplete="off" placeholder="Encontrar uma pasta..." class="w-full h-10 border-0 bg-transparent text-sm text-[#38261d] placeholder-[#9c866e] focus:outline-none focus:ring-0">
            </div>
        </div>
        <div id="npcFolderPickerList" class="npc-folder-picker__list px-3 pb-3" role="group" aria-label="Pastas disponíveis">
            @forelse($availableFolders as $destinationFolder)
                <button
                    type="button"
                    class="npc-folder-destination"
                    data-folder-id="{{ $destinationFolder->id }}"
                    data-folder-name="{{ $destinationFolder->name }}"
                >
                    <span class="npc-folder-destination__emblem" style="--npc-folder-color: {{ $destinationFolder->color ?? '#8c6239' }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-5 w-5"><path stroke-linejoin="round" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7Z"/></svg>
                    </span>
                    <span class="min-w-0 flex-1 text-left">
                        <strong class="block truncate text-[13px] font-semibold">{{ $destinationFolder->name }}</strong>
                        <small class="npc-folder-destination__caption">Guardar NPC nesta pasta</small>
                    </span>
                    <svg class="npc-folder-destination__arrow h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                </button>
            @empty
                <div class="px-4 py-8 text-center text-sm text-[#8c6239]">Nenhuma pasta disponível. Crie uma pasta no Cofre para começar.</div>
            @endforelse
            <p id="npcFolderNoSearchResults" class="hidden px-4 py-6 text-center text-xs text-[#8c6239]">Nenhuma pasta corresponde à pesquisa.</p>
        </div>
        <div class="npc-folder-picker__footer flex items-center justify-between gap-3 px-6 py-3">
            <span id="npcFolderPickerStatus" aria-live="polite" class="text-[11px] text-[#8c6239]">Escolha uma pasta para guardar o NPC.</span>
            <button type="button" data-close-npc-picker="true" class="text-[11px] font-bold text-[#6b1d14] hover:underline shrink-0">Cancelar</button>
        </div>
    </div>
</div>

<style>
    /* Estilos compartilhados: Cofre / visualização de pastas, sem efeitos globais. */
    /* Carta ilustrada — opção A, compartilhada entre Cofre e interior de pastas. */
    .npc-vault-card {
        display: flex;
        isolation: isolate;
        min-width: 0;
        min-height: 232px;
        height: 232px;
        align-items: stretch;
        padding: 0;
        border: 1px solid #dfcfba;
        border-radius: 16px;
        background: #fffaf2;
        box-shadow: 0 4px 12px rgba(63,39,27,.055);
        transition: box-shadow .22s ease, border-color .22s ease, opacity .2s ease;
    }
    .npc-vault-card:hover, .npc-vault-card:focus-within {
        /* Permanecer abaixo do cabeçalho sticky; sem deslocamento no hover. */
        z-index: 2;
        transform: none;
        border-color: #ba9575;
        box-shadow: 0 14px 28px rgba(67,37,24,.14);
    }
    .npc-card-top-ornament {
        position: absolute;
        z-index: 1;
        inset: 7px;
        height: auto;
        border: 1px solid rgba(104,62,43,.14);
        border-radius: 10px;
        background: none;
        pointer-events: none;
    }
    .npc-card-art {
        position: relative;
        flex: 0 0 42%;
        min-width: 0;
        overflow: hidden;
        border-radius: 15px 0 0 15px;
        border-right: 1px solid rgba(147,109,78,.26);
        background: radial-gradient(circle at 45% 42%, #f0e3d1 0%, #ddcbb4 65%, #bca184 125%);
    }
    .npc-card-art::before {
        content: '';
        position: absolute;
        z-index: 2;
        pointer-events: none;
        inset: 10px;
        border: 1px solid rgba(255,250,240,.4);
        border-radius: 9px;
    }
    .npc-card-art::after {
        content: '';
        position: absolute;
        z-index: 2;
        pointer-events: none;
        inset: 46% 0 0;
        background: linear-gradient(to top, rgba(57,29,21,.48), rgba(57,29,21,0));
    }
    .npc-portrait-frame {
        position: absolute;
        inset: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
    }
    .npc-portrait-image {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(var(--npc-portrait-zoom, 1));
        transition: transform .32s ease, object-position .25s ease;
    }
    .npc-vault-card:hover .npc-portrait-image {
        transform: scale(calc(var(--npc-portrait-zoom, 1) * 1.025));
    }
    .npc-card-art__initials {
        color: #734935;
        font: 800 clamp(28px, 4vw, 48px)/1 Georgia, 'Times New Roman', serif;
        letter-spacing: -.04em;
        text-shadow: 0 1px rgba(255,255,255,.35);
    }
    .npc-card-art__caption {
        position: absolute;
        z-index: 3;
        bottom: 15px;
        left: 17px;
        font: 800 9px/1.1 ui-sans-serif, system-ui, sans-serif;
        letter-spacing: .18em;
        color: #fff8ed;
        text-shadow: 0 2px 8px rgba(41,21,17,.65);
        pointer-events: none;
    }
    .npc-card-detail {
        flex: 1 1 58%;
        min-width: 0;
        display: flex;
        flex-direction: column;
        padding: 14px 15px 11px;
    }
    .npc-card-detail__eyebrow {
        color: #ae855c;
        font: 700 16px/20px Georgia, serif;
        height: 20px;
    }
    .npc-card-detail__name {
        margin: 7px 0 6px;
        min-height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        color: #5c2119;
        font: 800 18px/1.14 Georgia, 'Times New Roman', serif;
        overflow-wrap: anywhere;
    }
    .npc-card-creature {
        margin: 0 0 8px;
        color: #926b4b;
        font: 750 10px/1.4 ui-sans-serif, system-ui, sans-serif;
        text-transform: uppercase;
        letter-spacing: .07em;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
    }
    .npc-card-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0,1fr));
        flex-shrink: 0;
        min-height: 51px;
        margin-top: auto;
        padding: 0;
        border: 1px solid #e6d8c6;
        border-radius: 9px;
        background: #f5f0e8;
        overflow: hidden;
    }
    .npc-card-stat {
        position: relative;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    .npc-card-stat + .npc-card-stat::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 0;
        width: 1px;
        background: #ded1bd;
    }
    .npc-card-stat__label {
        color: #987252;
        font: 800 9px/1 ui-sans-serif, system-ui, sans-serif;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .npc-card-stat__value {
        color: #7a271d;
        font: 800 14px/1.2 Georgia, serif;
        font-variant-numeric: tabular-nums;
    }
    .npc-card-stat__value--ac { color: #315576; }
    .npc-view-button {
        margin-top: 3px;
        padding: 9px 2px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: #722419;
        font: 800 11px/1.2 Georgia, serif;
        letter-spacing: .055em;
        text-transform: uppercase;
        text-decoration: none;
        transition: color .18s ease;
    }
    .npc-view-button svg { width: 17px; height: 17px; flex-shrink: 0; transition: transform .18s ease; }
    .npc-view-button:hover, .npc-view-button:focus-visible { color: #a4492f; }
    .npc-view-button:hover svg, .npc-view-button:focus-visible svg { transform: translateX(3px); }
    .npc-vault-card .npc-deceased-overlay { border-radius: 15px; }
    .npc-vault-card .npc-card-actions { z-index: 60; top: 10px; right: 10px; }
    .npc-vault-card .npc-dropdown-menu { z-index: 60; box-shadow: 0 15px 38px rgba(64,30,22,.19); }
    .npc-vault-card .npc-menu-btn { width: 26px; height: 26px; }
    .npc-vault-card.is-dragging { opacity: .45; transform: scale(.98) rotate(-1deg); }
    .npc-vault-card.is-sending { pointer-events: none; animation: npc-vault-send .65s ease-in forwards; }
    @keyframes npc-vault-send {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        50% { transform: translateY(-12px) scale(.98); opacity: .85; }
        100% { transform: translateY(-19px) scale(.92); opacity: 0; }
    }
    @media (max-width: 400px) {
        .npc-card-art { flex-basis: 39%; }
        .npc-card-detail { padding: 12px 11px 10px; }
        .npc-card-detail__name { font-size: 16px; }
        .npc-card-stats { min-height: 48px; }
    }
    .npc-vault-folder { transition:transform .23s cubic-bezier(.2,.8,.2,1), box-shadow .23s ease, border-color .23s ease; }
    .npc-vault-folder.is-drag-over { transform:translateY(-6px) scale(1.013); border-color:#8c6239; box-shadow:0 16px 35px rgba(107,29,20,.18), 0 0 0 3px rgba(140,98,57,.22); }
    .npc-folder-drop-hint { position:absolute; inset:0; z-index:35; opacity:0; pointer-events:none; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:7px; background:rgba(244,238,225,.96); color:#62251b; border-radius:15px; transition:opacity .19s ease; }
    .npc-folder-drop-hint__icon { font-family:Georgia,serif; font-size:36px; line-height:1; color:#8c6239; }
    .npc-folder-drop-hint strong { font:700 17px Georgia,serif; }
    .npc-folder-drop-hint small { font-size:11px; color:#967a5f; }
    .npc-vault-folder.is-drag-over .npc-folder-drop-hint { opacity:1; }
    .npc-vault-folder.is-placed { animation:npc-vault-placed .7s ease; }
    @keyframes npc-vault-placed { 0%,100%{box-shadow:0 4px 12px rgba(62,36,22,.07)} 45%{box-shadow:0 0 0 5px rgba(143,121,76,.31),0 17px 35px rgba(63,40,22,.18)} }
    body.npc-is-dragging .npc-vault-folder:not(.is-drag-over) { border-style:dashed; }
    .npc-folder-picker { position:fixed; inset:0; z-index:100; }
    .npc-folder-picker.hidden { display:none; }
    .npc-folder-picker__backdrop { background:rgba(34,25,22,.55); backdrop-filter:blur(4px); }
    .npc-folder-picker__panel { background:#f9f5ed; border:1px solid #d1bfa5; box-shadow:0 28px 75px rgba(40,25,19,.36); animation:npc-picker-in .19s ease both; }
    @keyframes npc-picker-in { from{opacity:0; transform:translateY(9px) scale(.985)} to{opacity:1; transform:translateY(0) scale(1)} }
    .npc-folder-picker__header { background:linear-gradient(180deg, #fefbf6, #f6efe3); border-bottom:1px solid rgba(178,148,113,.35); }
    .npc-folder-picker__eyebrow { font-size:9px; font-weight:800; letter-spacing:.16em; color:#99724c; }
    .npc-folder-picker__close { display:flex; justify-content:center; align-items:center; width:30px; height:30px; border:1px solid #d8c5ac; border-radius:9px; background:#fffaf2; color:#6b1d14; }
    .npc-folder-picker__search { background:#fffcf7; border:1px solid #dccbb5; color:#8c6239; }
    .npc-folder-picker__search:focus-within { border-color:#8c6239; box-shadow:0 0 0 3px rgba(140,98,57,.10); }
    .npc-folder-picker__list { max-height:min(44vh,360px); overflow:auto; scrollbar-width:thin; scrollbar-color:#b49a77 #f9f5ed; }
    .npc-folder-picker__list::-webkit-scrollbar { width:9px; }
    .npc-folder-picker__list::-webkit-scrollbar-thumb { background:#b49a77; border:2px solid #f9f5ed; border-radius:12px; }
    .npc-folder-destination[hidden] { display:none; }
    .npc-folder-destination { width:100%; display:flex; align-items:center; gap:11px; padding:10px 12px; border:1px solid transparent; border-radius:11px; color:#4c3427; transition:background .15s ease,border-color .15s ease, transform .15s ease; }
    .npc-folder-destination:hover:not(:disabled),.npc-folder-destination:focus-visible:not(:disabled) { background:#f1e6d5; border-color:#dbc4a6; transform:translateX(2px); outline:none; }
    .npc-folder-destination:disabled { opacity:.62; cursor:default; }
    .npc-folder-destination__emblem { flex-shrink:0; display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:11px; color:var(--npc-folder-color); background:color-mix(in srgb,var(--npc-folder-color) 11%,white); border:1px solid color-mix(in srgb,var(--npc-folder-color) 28%,white); }
    .npc-folder-destination__caption { display:block; margin-top:2px; font-size:10px; color:#9b816b; }
    .npc-folder-destination__arrow { color:#a98b70; }
    .npc-folder-picker__footer { border-top:1px solid #ddcbb4; background:#f3ebdf; }
    .npc-vault-toast { position:fixed; z-index:110; bottom:24px; right:24px; max-width:min(365px,calc(100vw - 32px)); padding:13px 17px; border-radius:12px; border:1px solid #c5ac8a; box-shadow:0 13px 38px rgba(50,29,22,.18); background:#fffaf2; color:#5b281d; font-size:13px; font-weight:600; animation:npc-picker-in .2s ease; }
    .npc-vault-toast.is-error { border-color:#bd887e; color:#8c2118; }
    @media (prefers-reduced-motion:reduce) {
        .npc-vault-card,.npc-vault-folder,.npc-folder-drop-hint,.npc-portrait-image { transition:none!important; }
        .npc-vault-card.is-sending,.npc-vault-folder.is-placed,.npc-folder-picker__panel,.npc-vault-toast { animation:none!important; }
    }
</style>

<script>
(function () {
    'use strict';
    let selectedNpcId = null;
    let selectedNpcName = '';
    let selectedFolderId = '';
    let lastFocus = null;
    let draggingNpcId = null;
    let sending = false;
    let toastTimer = null;
    let previousBodyOverflow = null;

    const byId = (id) => document.getElementById(id);
    const picker = () => byId('npcFolderPicker');
    const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    function closeMenus(exceptId = null) {
        document.querySelectorAll('.npc-dropdown-menu').forEach(menu => {
            const open = menu.id === exceptId && menu.classList.contains('hidden');
            if (exceptId && menu.id === exceptId && open) {
                menu.classList.remove('hidden');
            } else if (!exceptId || menu.id !== exceptId || !open) {
                menu.classList.add('hidden');
            }
            const btn = document.querySelector('.npc-menu-btn[data-target="' + menu.id + '"]');
            if (btn) btn.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
        });
    }

    function announce(message, error = false) {
        const old = document.querySelector('.npc-vault-toast');
        if (old) old.remove();
        if (toastTimer) clearTimeout(toastTimer);
        const item = document.createElement('div');
        item.className = 'npc-vault-toast' + (error ? ' is-error' : '');
        item.role = error ? 'alert' : 'status';
        item.textContent = message;
        document.body.appendChild(item);
        toastTimer = setTimeout(() => item.remove(), 4200);
    }

    function updateDestinationButtons() {
        picker()?.querySelectorAll('.npc-folder-destination').forEach(button => {
            const isCurrent = String(button.dataset.folderId) === selectedFolderId;
            button.disabled = isCurrent || sending;
            button.querySelector('.npc-folder-destination__caption').textContent = isCurrent
                ? 'Pasta atual' : 'Guardar NPC nesta pasta';
        });
    }

    function openPicker(trigger) {
        const modal = picker();
        if (!modal || sending) return;
        selectedNpcId = String(trigger.dataset.npcId || '');
        selectedNpcName = String(trigger.dataset.npcName || 'NPC');
        selectedFolderId = String(trigger.dataset.currentFolder || '');
        if (!selectedNpcId) return;
        lastFocus = document.activeElement;
        closeMenus();
        byId('npcFolderPickerNpcName').textContent = selectedNpcName;
        byId('npcFolderPickerStatus').textContent = 'Escolha uma pasta para guardar o NPC.';
        byId('npcFolderSearch').value = '';
        modal.querySelectorAll('.npc-folder-destination').forEach(button => button.hidden = false);
        byId('npcFolderNoSearchResults').classList.add('hidden');
        updateDestinationButtons();
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden','false');
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        byId('npcFolderSearch').focus();
    }

    function closePicker() {
        if (sending) return;
        const modal = picker();
        if (!modal) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden','true');
        document.body.style.overflow = previousBodyOverflow ?? '';
        previousBodyOverflow = null;
        if (lastFocus && lastFocus.isConnected) lastFocus.focus();
    }

    async function moveNpcToFolder(npcId, folderId, folderName = '', dropTarget = null) {
        if (sending || !npcId || !folderId) return;
        const card = Array.from(document.querySelectorAll('.npc-card')).find(item => item.dataset.npc === String(npcId));
        if (card && String(card.dataset.currentFolder || '') === String(folderId)) {
            announce('Este NPC já está na pasta escolhida.');
            closePicker();
            return;
        }
        sending = true;
        updateDestinationButtons();
        const status = byId('npcFolderPickerStatus');
        if (status) status.textContent = 'Guardando NPC...';
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrf) throw new Error('Token de segurança não encontrado. Atualize a página.');
            const response = await fetch(`/npcs/${encodeURIComponent(npcId)}/move-folder`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ folder_id: folderId }),
            });
            if (!response.ok) {
                const data = await response.json().catch(() => null);
                throw new Error(data?.message || 'Não foi possível mover o NPC.');
            }
            // O retorno do servidor confirma a mudança; somente então animamos.
            if (card) card.classList.add('is-sending');
            if (dropTarget) dropTarget.classList.add('is-placed');
            if (status) status.textContent = 'NPC guardado com sucesso!';
            const destinationName = folderName || dropTarget?.dataset.name || 'a pasta selecionada';
            announce(`NPC enviado para ${destinationName}.`);
            const modal = picker();
            if (modal && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = previousBodyOverflow ?? '';
                previousBodyOverflow = null;
            }
            // Recarregamento garante contadores, avatares e resultados de filtros coerentes.
            window.setTimeout(() => window.location.reload(), 820);
        } catch (error) {
            if (status) status.textContent = 'Erro ao guardar. Você pode tentar novamente.';
            announce(error?.message || 'Não foi possível mover o NPC.', true);
        } finally {
            sending = false;
            updateDestinationButtons();
        }
    }

    function clearDragHighlights() {
        document.body.classList.remove('npc-is-dragging');
        document.querySelectorAll('.npc-card.is-dragging').forEach(card => card.classList.remove('is-dragging'));
        document.querySelectorAll('.folder-dropzone.is-drag-over').forEach(folder => folder.classList.remove('is-drag-over'));
    }

    function setup() {
        document.addEventListener('click', function (event) {
            const menuButton = event.target.closest('.npc-menu-btn');
            if (menuButton) {
                event.preventDefault();
                event.stopPropagation();
                closeMenus(menuButton.dataset.target);
                return;
            }
            const moveButton = event.target.closest('.npc-move-folder-btn');
            if (moveButton) {
                event.preventDefault();
                event.stopPropagation();
                openPicker(moveButton);
                return;
            }
            const destination = event.target.closest('.npc-folder-destination');
            if (destination && !destination.disabled && !sending) {
                event.preventDefault();
                moveNpcToFolder(selectedNpcId, destination.dataset.folderId, destination.dataset.folderName);
                return;
            }
            if (event.target.closest('[data-close-npc-picker]')) {
                event.preventDefault();
                closePicker();
                return;
            }
            if (!event.target.closest('.npc-dropdown-menu')) closeMenus();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { closeMenus(); closePicker(); }
            if (event.key === 'Tab' && picker() && !picker().classList.contains('hidden')) {
                const focusable = Array.from(picker().querySelectorAll('button:not(:disabled):not([hidden]),input:not(:disabled)')).filter(x => x.offsetParent !== null);
                if (!focusable.length) return;
                const first = focusable[0], last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });

        byId('npcFolderSearch')?.addEventListener('input', function () {
            const q = normalize(this.value);
            let visible = 0;
            picker().querySelectorAll('.npc-folder-destination').forEach(button => {
                button.hidden = !normalize(button.dataset.folderName).includes(q);
                if (!button.hidden) visible++;
            });
            byId('npcFolderNoSearchResults').classList.toggle('hidden', visible > 0);
        });

        document.querySelectorAll('img[data-npc-portrait]').forEach(img => {
            const adjust = () => {
                if (!img.naturalWidth || !img.naturalHeight) return;
                const isTall = img.naturalHeight / img.naturalWidth > 1.28;
                if (img.dataset.imagePosition && img.dataset.imagePosition !== 'auto') return;
                const horizontal = Math.max(0, Math.min(100, Number(img.dataset.imageHorizontal ?? '50')));
                img.style.objectPosition = `${horizontal}% ${isTall ? '12%' : '50%'}`;
                img.style.transformOrigin = `${horizontal}% ${isTall ? '0%' : '50%'}`;
            };
            if (img.complete) adjust();
            img.addEventListener('load', adjust, { once: true });
        });

        document.querySelectorAll('.npc-card').forEach(card => {
            card.addEventListener('dragstart', event => {
                if (event.target.closest('button,a,form,input,.npc-dropdown-menu')) { event.preventDefault(); return; }
                if (sending) { event.preventDefault(); return; }
                draggingNpcId = card.dataset.npc;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', draggingNpcId);
                event.dataTransfer.setData('application/x-spellbound-npc', draggingNpcId);
                card.classList.add('is-dragging');
                document.body.classList.add('npc-is-dragging');
                closeMenus();
            });
            card.addEventListener('dragend', () => { draggingNpcId = null; clearDragHighlights(); });
        });

        document.querySelectorAll('.folder-dropzone').forEach(folder => {
            folder.addEventListener('dragover', event => {
                if (!draggingNpcId || sending) return;
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                folder.classList.add('is-drag-over');
            });
            folder.addEventListener('dragleave', event => {
                if (event.relatedTarget && folder.contains(event.relatedTarget)) return;
                folder.classList.remove('is-drag-over');
            });
            folder.addEventListener('drop', event => {
                if (!draggingNpcId || sending) return;
                event.preventDefault();
                event.stopPropagation();
                const id = event.dataTransfer.getData('application/x-spellbound-npc') || draggingNpcId;
                const folderId = folder.dataset.folder;
                draggingNpcId = null;
                clearDragHighlights();
                moveNpcToFolder(id, folderId, folder.dataset.name, folder);
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup, { once: true });
    else setup();
})();
</script>
