{{-- Spellbound V66.0.59 — Edição padronizada com o modal de importação. --}}
<div id="npcEditModal" class="npc-edit-modal hidden fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-6" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="npcEditModalTitle">
    <div class="npc-edit-backdrop absolute inset-0" data-close-npc-editor="true"></div>
    <section class="npc-edit-panel relative w-full max-h-[calc(100dvh-24px)] overflow-y-auto rounded-2xl" tabindex="-1">
        <header class="npc-edit-header flex items-center justify-between gap-3 px-5 py-4 sm:px-7">
            <div class="min-w-0">
                <h2 id="npcEditModalTitle" class="font-serif text-[23px] font-bold leading-tight text-[#63251b]">Editar NPC</h2>
                <p id="npcEditNpcName" class="mt-0.5 truncate text-xs text-[#967052]"></p>
            </div>
            <button type="button" data-close-npc-editor="true" aria-label="Fechar edição" class="npc-edit-close shrink-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
            </button>
        </header>

        <form id="npcEditForm" novalidate enctype="multipart/form-data" class="npc-edit-form p-5 sm:p-6 sm:px-7">
            <div class="npc-edit-json-group">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <label for="npcEditJsonInput" class="npc-edit-section-label">Arquivo da ficha</label>
                    <span class="npc-edit-json-caption">Opcional · .json</span>
                </div>
                <div class="npc-edit-file-row flex items-center gap-2 min-w-0">
                    <label for="npcEditJsonInput" class="npc-edit-json-label shrink-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 4H4v16h4M16 4h4v16h-4M10 9l-2 3 2 3m4-6 2 3-2 3"/></svg>
                        Substituir JSON
                    </label>
                    <span id="npcEditJsonFilename" class="npc-edit-selected-name min-w-0 flex-1 truncate" aria-live="polite">Manter JSON atual</span>
                    <button id="npcEditClearJson" type="button" class="npc-edit-clear-json hidden shrink-0" title="Manter o JSON atual" aria-label="Remover JSON selecionado">×</button>
                </div>
                <input id="npcEditJsonInput" name="npc_file" class="sr-only" type="file" accept=".json,application/json,text/plain">
            </div>

            <div class="npc-edit-divider"></div>
            <div class="npc-edit-layout grid gap-5 sm:gap-6 md:grid-cols-[.95fr_1.05fr]">
                <div class="min-w-0">
                    <h3 class="npc-edit-section-label">Retrato</h3>
                    <div class="npc-edit-preview-area mt-3">
                        <div class="npc-edit-preview-ring">
                            <img id="npcEditPreviewImage" alt="Prévia do NPC" class="hidden" draggable="false">
                            <div id="npcEditPreviewFallback" class="npc-edit-preview-fallback" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" class="h-14 w-14 opacity-45"><circle cx="12" cy="7.5" r="3.5"/><path d="M3.5 21v-3.2c0-3.5 3.3-5.6 8.5-5.6s8.5 2.1 8.5 5.6V21"/></svg>
                            </div>
                        </div>
                    </div>
                    <label class="npc-edit-file-label mt-3" for="npcEditImageInput">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h4l2-2h4l2 2h4v12H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
                        Escolher fotografia
                    </label>
                    <input id="npcEditImageInput" name="npc_image" class="sr-only" type="file" accept="image/png,image/jpeg,image/webp,.jpg,.jpeg,.png,.webp">
                    <p class="npc-edit-help mt-2 text-center text-[11px] leading-snug">Mantenha a foto atual ou escolha outra (até 8 MB).</p>
                    <p id="npcEditImageFilename" class="npc-edit-file-name hidden mt-1 text-center text-[11px] break-all" aria-live="polite"></p>
                </div>

                <div class="min-w-0">
                    <h3 class="npc-edit-section-label">Enquadramento</h3>
                    <div class="npc-edit-controls mt-3">
                        <div>
                            <label for="npcEditZoom" class="npc-edit-control-heading">
                                <span>Zoom</span><output id="npcEditZoomValue" for="npcEditZoom" class="npc-edit-value">100%</output>
                            </label>
                            <input type="range" id="npcEditZoom" name="image_zoom" min="1" max="3" step="0.05" value="1" class="npc-edit-range mt-2 w-full" aria-label="Zoom da imagem">
                        </div>
                        <div class="mt-5">
                            <label for="npcEditHorizontal" class="npc-edit-control-heading">
                                <span>Posição horizontal</span><output id="npcEditHorizontalValue" for="npcEditHorizontal" class="npc-edit-value">Centro</output>
                            </label>
                            <input type="range" id="npcEditHorizontal" name="image_horizontal" min="0" max="100" step="1" value="50" class="npc-edit-range mt-2 w-full" aria-label="Deslocamento horizontal da imagem">
                            <div class="npc-edit-range-ends mt-1"><span>Esquerda</span><span>Direita</span></div>
                        </div>
                        <fieldset class="mt-5">
                            <legend class="npc-edit-control-heading">Posição vertical</legend>
                            <div class="npc-edit-position-grid mt-2" role="group" aria-label="Posição vertical do retrato">
                                <label><input type="radio" name="image_position" value="auto" checked><span>Auto</span></label>
                                <label><input type="radio" name="image_position" value="top"><span>Cima</span></label>
                                <label><input type="radio" name="image_position" value="center"><span>Meio</span></label>
                                <label><input type="radio" name="image_position" value="bottom"><span>Baixo</span></label>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div id="npcEditError" class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 p-3 text-xs font-medium text-red-800" role="alert"></div>
            <footer class="npc-edit-footer mt-5 flex items-center justify-end gap-2 border-t border-[#e1d2bc] pt-4">
                <button type="button" data-close-npc-editor="true" class="npc-edit-cancel">Cancelar</button>
                <button id="npcEditSubmit" type="submit" class="npc-edit-save">Salvar alterações</button>
            </footer>
        </form>
    </section>
</div>
<style>
    .npc-edit-modal.hidden{display:none}
    .npc-edit-backdrop{background:rgba(38,27,22,.62);backdrop-filter:blur(5px)}
    .npc-edit-panel{width:min(760px,calc(100vw - 24px))!important;max-width:760px!important;max-height:calc(100dvh - 24px);background:#fbf8f2;border:1px solid #d1bea3;box-shadow:0 27px 75px rgba(39,22,14,.31);animation:npc-editor-enter .2s ease both;scrollbar-width:thin;scrollbar-color:#aa8b66 #fbf8f2}
    .npc-edit-header{background:linear-gradient(180deg,#fffdf8,#f6efe4);border-bottom:1px solid #e0d0b8}
    .npc-edit-close{display:flex;align-items:center;justify-content:center;width:33px;height:33px;border-radius:10px;border:1px solid #dac6ab;background:#fffaf3;color:#6b1d14;transition:background .16s,border-color .16s}
    .npc-edit-close:hover{background:#f2e5d6;border-color:#c7a57c}
    .npc-edit-section-label{font:700 16px Georgia,serif;color:#63251b}
    .npc-edit-preview-area{display:flex;align-items:center;justify-content:center;min-height:229px;padding:18px 12px;border-radius:15px;border:1px solid #e1d0b9;background:radial-gradient(circle at 50% 32%,#fffdf9 0%,#f1e5d4 100%)}
    .npc-edit-preview-ring{position:relative;width:184px;height:184px;overflow:hidden;border-radius:50%;border:5px solid #fffaf2;box-shadow:0 0 0 1px #bc956f,0 9px 25px rgba(80,48,29,.15);background:#e7d9c8}
    #npcEditPreviewImage{width:100%;height:100%;display:block;object-fit:cover;user-select:none;pointer-events:none;transition:transform .16s ease,object-position .16s ease}
    #npcEditPreviewImage.hidden{display:none}
    .npc-edit-preview-fallback{display:flex;align-items:center;justify-content:center;height:100%;color:#986a4d}
    .npc-edit-preview-fallback.hidden{display:none}
    .npc-edit-file-label,.npc-edit-json-label{display:flex;align-items:center;justify-content:center;gap:8px;min-height:39px;padding:9px 12px;border-radius:10px;border:1px solid #cbb391;background:#fffdf8;color:#712c1f;font:700 12px Georgia,serif;cursor:pointer;transition:border-color .16s,background .16s,transform .16s}
    .npc-edit-file-label:hover,.npc-edit-json-label:hover{border-color:#9b663f;background:#f3e8d8;transform:translateY(-1px)}
    .npc-edit-file-label:focus-within,.npc-edit-json-label:focus-within{outline:2px solid #b28b5e;outline-offset:2px}
    .npc-edit-help{color:#967c64}
    .npc-edit-file-name{color:#805d42}
    .npc-edit-control-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;font-weight:700;color:#6b281d}
    .npc-edit-value{padding:3px 8px;border:1px solid #e3d3bc;border-radius:7px;background:#f4ebdd;color:#875a39;font:700 11px ui-monospace,SFMono-Regular,monospace}
    .npc-edit-range{--npc-range-fill:50%;display:block;height:6px;margin-inline:0;appearance:none;-webkit-appearance:none;border-radius:99px;cursor:pointer;background:linear-gradient(90deg,#852b21 0 var(--npc-range-fill),#ddceba var(--npc-range-fill) 100%)}
    .npc-edit-range::-webkit-slider-thumb{-webkit-appearance:none;width:17px;height:17px;border-radius:50%;border:3px solid #fffaf2;background:#852b21;box-shadow:0 0 0 1px #994e39,0 2px 6px rgba(58,28,17,.17)}
    .npc-edit-range::-moz-range-thumb{width:12px;height:12px;border-radius:50%;border:3px solid #fffaf2;background:#852b21;box-shadow:0 0 0 1px #994e39}
    .npc-edit-range:focus-visible{outline:3px solid rgba(173,123,75,.4);outline-offset:6px}
    .npc-edit-range-ends{display:flex;justify-content:space-between;color:#a58c74;font-size:10px}
    .npc-edit-position-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:5px}
    .npc-edit-position-grid label{position:relative;min-width:0;cursor:pointer}
    .npc-edit-position-grid input{position:absolute;opacity:0;width:1px;height:1px}
    .npc-edit-position-grid span{display:flex;align-items:center;justify-content:center;min-height:35px;padding:5px 3px;border-radius:9px;border:1px solid #ddc8ab;background:#fffcf7;color:#876950;font-size:11px;font-weight:700;transition:background .15s,color .15s,border-color .15s}
    .npc-edit-position-grid input:checked+span{background:#77251b;border-color:#77251b;color:#fffaf1;box-shadow:0 3px 8px rgba(113,37,27,.15)}
    .npc-edit-position-grid input:focus-visible+span{outline:2px solid #ac8660;outline-offset:2px}
    .npc-edit-json-caption{color:#9c795c;font-size:11px;font-weight:600}
    .npc-edit-file-row{padding:5px 6px;background:#f6efe3;border-radius:11px;border:1px solid #dfcbb0}
    .npc-edit-json-label{min-height:36px;padding:8px 13px}
    .npc-edit-selected-name{color:#90765d;font-size:11px}
    .npc-edit-clear-json{width:25px;height:25px;display:flex;align-items:center;justify-content:center;border-radius:8px;color:#984131;font-size:18px}
    .npc-edit-clear-json:hover{background:#ebdccc}
    .npc-edit-divider{height:1px;margin-top:15px;margin-bottom:16px;background:linear-gradient(90deg,transparent,#ddc7aa,transparent)}
    .npc-edit-cancel{padding:10px 14px;border:1px solid #d9c6ae;border-radius:10px;background:#fffaf2;color:#6b1d14;font-size:12px;font-weight:700}
    .npc-edit-cancel:hover{background:#f1e7d9}
    .npc-edit-save{padding:11px 16px;border-radius:10px;background:linear-gradient(110deg,#6b1d14,#962e21);color:white;font:700 12px Georgia,serif;box-shadow:0 3px 10px rgba(107,29,20,.16)}
    .npc-edit-save:hover:not(:disabled){filter:brightness(1.06)}
    .npc-edit-save:disabled{opacity:.55;cursor:wait}
    /* Compartilha as proporções do editor de importação V66.0.59. */
    .npc-edit-header{padding:13px 23px}
    .npc-edit-form{padding:17px 23px 16px}
    .npc-edit-layout{grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);gap:20px}
    .npc-edit-preview-area{min-height:192px;padding:12px}
    .npc-edit-preview-ring{width:154px;height:154px}
    .npc-edit-position-grid span{min-height:33px}
    .npc-edit-footer{margin-top:18px;padding-top:13px}
    @media(max-width:640px){
        .npc-edit-layout{grid-template-columns:minmax(0,1fr);gap:18px}
        .npc-edit-header{padding:12px 16px}
        .npc-edit-form{padding:16px}
        .npc-edit-preview-area{min-height:174px}
        .npc-edit-preview-ring{width:142px;height:142px}
    }
    @keyframes npc-editor-enter{from{opacity:0;transform:translateY(10px) scale(.985)}to{opacity:1;transform:none}}
    @media (prefers-reduced-motion:reduce){.npc-edit-panel{animation:none}#npcEditPreviewImage,.npc-edit-file-label,.npc-edit-json-label{transition:none}}
</style>
<script>
(function () {
    'use strict';
    const byId = id => document.getElementById(id);
    const modal = byId('npcEditModal');
    const form = byId('npcEditForm');
    const preview = byId('npcEditPreviewImage');
    const fallback = byId('npcEditPreviewFallback');
    const zoomInput = byId('npcEditZoom');
    const horizontalInput = byId('npcEditHorizontal');
    const imageInput = byId('npcEditImageInput');
    const jsonInput = byId('npcEditJsonInput');
    let npcId = null;
    let temporaryUrl = null;
    let originalUrl = '';
    let previousBodyOverflow = '';
    let returnFocus = null;
    let saving = false;
    let initialZoom = 1;
    let initialHorizontal = 50;
    let initialPosition = 'auto';

    const clamp = (value, min, max) => Math.max(min, Math.min(max, Number(value) || min));
    const activePosition = () => form.querySelector('input[name="image_position"]:checked')?.value || 'auto';
    function displayError(message) {
        const box = byId('npcEditError');
        box.textContent = message;
        box.classList.remove('hidden');
    }
    function updatePreview() {
        const zoom = clamp(zoomInput.value, 1, 3);
        const horizontal = clamp(horizontalInput.value, 0, 100);
        byId('npcEditZoomValue').textContent = `${Math.round(zoom * 100)}%`;
        byId('npcEditHorizontalValue').textContent = horizontal === 50 ? 'Centro' : `${Math.round(horizontal)}%`;
        zoomInput.style.setProperty('--npc-range-fill', `${(zoom - 1) * 50}%`);
        horizontalInput.style.setProperty('--npc-range-fill', `${horizontal}%`);
        const pos = activePosition();
        const tall = preview.naturalWidth > 0 && preview.naturalHeight / preview.naturalWidth > 1.28;
        const verticalPosition = pos === 'auto' ? (tall ? '12%' : '50%') : ({top:'0%',center:'50%',bottom:'100%'})[pos] || '50%';
        const verticalOrigin = pos === 'auto' && tall ? '0%' : verticalPosition;
        preview.style.objectPosition = `${horizontal}% ${verticalPosition}`;
        preview.style.transformOrigin = `${horizontal}% ${verticalOrigin}`;
        preview.style.transform = `scale(${zoom})`;
    }
    function changeImageSrc(url) {
        if (url) {
            preview.onload = updatePreview;
            preview.onerror = function () {
                preview.classList.add('hidden');
                fallback.classList.remove('hidden');
            };
            preview.src = url;
            preview.classList.remove('hidden');
            fallback.classList.add('hidden');
        } else {
            preview.removeAttribute('src');
            preview.classList.add('hidden');
            fallback.classList.remove('hidden');
        }
        updatePreview();
    }
    function revokeTemporaryImage() {
        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);
        temporaryUrl = null;
    }
    function openEditor(button) {
        if (saving) return;
        npcId = button.dataset.npcId;
        if (!npcId) return;
        const name = button.dataset.npcName || 'NPC';
        originalUrl = button.dataset.npcImage || '';
        returnFocus = button;
        previousBodyOverflow = document.body.style.overflow;
        form.reset();
        revokeTemporaryImage();
        byId('npcEditError').classList.add('hidden');
        byId('npcEditNpcName').textContent = name;
        // Mantém o ícone neutro igual ao da importação, sem substituir o SVG por texto.
        zoomInput.value = button.dataset.imageZoom || '1';
        horizontalInput.value = button.dataset.imageHorizontal ?? '50';
        const position = button.dataset.imagePosition || 'auto';
        const radio = form.querySelector(`input[name="image_position"][value="${position}"]`);
        if (radio) radio.checked = true;
        initialZoom = clamp(zoomInput.value, 1, 3);
        initialHorizontal = clamp(horizontalInput.value, 0, 100);
        initialPosition = activePosition();
        byId('npcEditImageFilename').textContent = '';
        byId('npcEditImageFilename').classList.add('hidden');
        byId('npcEditJsonFilename').textContent = 'Manter JSON atual';
        byId('npcEditClearJson').classList.add('hidden');
        changeImageSrc(originalUrl);
        document.querySelectorAll('.npc-dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        zoomInput.focus();
    }
    function closeEditor() {
        if (saving || modal.classList.contains('hidden')) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = previousBodyOverflow;
        revokeTemporaryImage();
        if (returnFocus?.isConnected) returnFocus.focus();
    }
    imageInput.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        revokeTemporaryImage();
        if (file) {
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                displayError('Escolha uma imagem JPG, PNG ou WebP.');
                imageInput.value = '';
                changeImageSrc(originalUrl);
                return;
            }
            if (file.size > 8 * 1024 * 1024) {
                displayError('A imagem não pode ultrapassar 8 MB.');
                imageInput.value = '';
                changeImageSrc(originalUrl);
                return;
            }
            temporaryUrl = URL.createObjectURL(file);
            changeImageSrc(temporaryUrl);
        } else changeImageSrc(originalUrl);
        const filename = byId('npcEditImageFilename');
        filename.textContent = file?.name || '';
        filename.classList.toggle('hidden', !file);
    });
    jsonInput.addEventListener('change', () => {
        const file = jsonInput.files?.[0];
        byId('npcEditJsonFilename').textContent = file?.name || 'Manter JSON atual';
        byId('npcEditClearJson').classList.toggle('hidden', !file);
    });
    byId('npcEditClearJson').addEventListener('click', () => {
        jsonInput.value = '';
        byId('npcEditJsonFilename').textContent = 'Manter JSON atual';
        byId('npcEditClearJson').classList.add('hidden');
    });
    zoomInput.addEventListener('input', updatePreview);
    horizontalInput.addEventListener('input', updatePreview);
    form.querySelectorAll('input[name="image_position"]').forEach(input => input.addEventListener('change', updatePreview));
    document.addEventListener('click', event => {
        const edit = event.target.closest('.npc-edit-btn');
        if (edit) { event.preventDefault(); event.stopPropagation(); openEditor(edit); return; }
        if (event.target.closest('[data-close-npc-editor]')) { event.preventDefault(); closeEditor(); }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) { event.stopPropagation(); closeEditor(); }
        if (event.key === 'Tab' && !modal.classList.contains('hidden')) {
            const focusable = [...modal.querySelectorAll('button:not(:disabled),input:not(:disabled),[tabindex="0"]')].filter(el => !el.closest('.hidden'));
            const first = focusable[0], last = focusable[focusable.length - 1];
            if (first && event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (last && !event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    // V66.0.69: o enquadramento passa a ser gravado no arquivo real da foto,
    // não apenas em atributos CSS. Usa a mesma projeção 900x900 da importação.
    async function generateFramedPhoto() {
        if (!preview.getAttribute('src')) throw new Error('O NPC não possui uma fotografia para enquadrar.');
        if (!preview.complete || !preview.naturalWidth) await preview.decode();
        const size = 900;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        if (!ctx) throw new Error('Não foi possível preparar o editor de imagem.');
        const zoom = clamp(zoomInput.value, 1, 3);
        const horizontal = clamp(horizontalInput.value, 0, 100) / 100;
        const position = activePosition();
        const tall = preview.naturalHeight / preview.naturalWidth > 1.28;
        const vertical = position === 'auto' ? (tall ? .12 : .5) : ({ top:0, center:.5, bottom:1 })[position] ?? .5;
        const originY = position === 'auto' && tall ? 0 : vertical;
        const cover = Math.max(size / preview.naturalWidth, size / preview.naturalHeight);
        const width = preview.naturalWidth * cover;
        const height = preview.naturalHeight * cover;
        const x = (size - width) * horizontal;
        const y = (size - height) * vertical;
        const pivotX = size * horizontal;
        const pivotY = size * originY;
        ctx.translate(pivotX, pivotY);
        ctx.scale(zoom, zoom);
        ctx.translate(-pivotX, -pivotY);
        try {
            ctx.drawImage(preview, x, y, width, height);
        } catch (e) {
            throw new Error('Não foi possível ler a fotografia atual. Abra a página pelo mesmo endereço do site e tente novamente.');
        }
        const blob = await new Promise((resolve, reject) => {
            try {
                canvas.toBlob(result => result ? resolve(result) : reject(new Error('Falha ao gerar o retrato.')), 'image/png');
            } catch (e) {
                reject(new Error('A fotografia não permite gerar um recorte neste endereço. Abra a página pelo endereço principal do site.'));
            }
        });
        if (blob.size > 8 * 1024 * 1024) throw new Error('O retrato ajustado ultrapassou 8 MB. Tente outra fotografia.');
        return new File([blob], 'npc-retrato-ajustado.png', { type:'image/png', lastModified:Date.now() });
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (saving || !npcId) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) { displayError('Token de segurança ausente. Atualize a página.'); return; }
        saving = true;
        const submit = byId('npcEditSubmit');
        submit.disabled = true;
        submit.textContent = 'Salvando...';
        byId('npcEditError').classList.add('hidden');
        const data = new FormData(form);
        data.set('_method', 'PATCH');
        try {
            // Não reprocessa a foto quando só o JSON mudou ou quando os ajustes
            // não foram alterados. Uma foto nova sempre é enquadrada.
            const photoWasReplaced = !!imageInput.files?.length;
            // Fotos editadas nas versões anteriores tinham o enquadramento só
            // como metadados. Ao salvar novamente, aplica esse ajuste no PNG.
            const oldFramingWasCssOnly = (
                Math.abs(initialZoom - 1) > .005 ||
                Math.abs(initialHorizontal - 50) >= .5 ||
                initialPosition === 'top' || initialPosition === 'bottom'
            );
            const controlsChanged = (
                Math.abs(clamp(zoomInput.value, 1, 3) - initialZoom) > .005 ||
                Math.abs(clamp(horizontalInput.value, 0, 100) - initialHorizontal) >= .5 ||
                activePosition() !== initialPosition
            );
            if (preview.getAttribute('src') && (photoWasReplaced || controlsChanged || oldFramingWasCssOnly)) {
                const framed = await generateFramedPhoto();
                data.set('npc_image', framed, framed.name);
                // O recorte já contém zoom e deslocamento: aplicar CSS de novo
                // ampliaria a imagem duas vezes e distorceria o resultado no card.
                data.set('image_zoom', '1');
                data.set('image_horizontal', '50');
                data.set('image_position', 'center');
            }
            const response = await fetch('/npcs/' + encodeURIComponent(npcId) + '/edit', {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: data,
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok || !result.success) {
                const fieldErrors = result.errors ? Object.values(result.errors).flat().join(' ') : '';
                throw new Error(fieldErrors || result.message || 'Não foi possível salvar as alterações.');
            }
            submit.textContent = 'Salvo!';
            window.location.reload();
        } catch (error) {
            displayError(error?.message || 'Erro ao salvar NPC.');
            submit.disabled = false;
            submit.textContent = 'Salvar alterações';
            saving = false;
        }
    });
})();
</script>
