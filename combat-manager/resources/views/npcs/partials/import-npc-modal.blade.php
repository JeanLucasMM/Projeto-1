{{-- Spellbound V66.0.59 — Importação compacta, padronizada com o editor de NPCs. --}}
<div id="importModal" class="npc-import-modal hidden fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-5" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="npcImportTitle">
    <div class="npc-import-backdrop absolute inset-0" data-close-npc-import="true"></div>
    <section class="npc-import-panel relative w-full max-h-[calc(100dvh-24px)] overflow-y-auto rounded-2xl" tabindex="-1">
        <header class="npc-import-header flex items-center justify-between gap-3 px-5 py-4 sm:px-7">
            <h2 id="npcImportTitle" class="font-serif text-[23px] font-bold leading-tight text-[#63251b]">Importar NPC</h2>
            <button type="button" data-close-npc-import="true" aria-label="Fechar importação" class="npc-import-close shrink-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
            </button>
        </header>

        <form id="importForm" action="{{ route('npc.import') }}" method="POST" enctype="multipart/form-data" class="npc-import-form px-5 py-5 sm:px-7 sm:py-6">
            @csrf
            <div class="npc-import-json-group">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <label for="npc_file" class="npc-import-section-label">Arquivo da ficha <span class="text-[#9b3426]" aria-label="obrigatório">*</span></label>
                    <span class="npc-import-json-caption">.json</span>
                </div>
                <div class="npc-import-file-row flex items-center gap-2 min-w-0">
                    <label for="npc_file" class="npc-import-file-btn shrink-0">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M8 4H4v16h4M16 4h4v16h-4M10 9l-2 3 2 3m4-6 2 3-2 3"/></svg>
                        Escolher JSON
                    </label>
                    <span id="npcImportJsonFilename" class="npc-import-selected-name flex-1 min-w-0 truncate">Nenhum arquivo escolhido</span>
                    <button id="npcImportClearJson" type="button" class="npc-import-clear hidden shrink-0" title="Remover JSON" aria-label="Remover JSON selecionado">×</button>
                </div>
                <input id="npc_file" name="npc_file" type="file" accept=".json,application/json" required class="npc-import-visually-hidden">
            </div>

            <div class="npc-import-divider mt-5 mb-5"></div>
            <div class="npc-import-layout grid gap-5 sm:gap-6 md:grid-cols-[.95fr_1.05fr]">
                <div class="min-w-0">
                    <h3 class="npc-import-section-label">Retrato</h3>
                    <div class="npc-import-preview-area mt-3">
                        <div class="npc-import-preview-ring">
                            <img id="npcImportPreviewImage" alt="Prévia do enquadramento" class="hidden" draggable="false">
                            <div id="npcImportPreviewFallback" class="npc-import-preview-fallback" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" class="h-14 w-14 opacity-45"><circle cx="12" cy="7.5" r="3.5"/><path d="M3.5 21v-3.2c0-3.5 3.3-5.6 8.5-5.6s8.5 2.1 8.5 5.6V21"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <label for="npc_image" class="npc-import-file-btn npc-import-photo-btn flex-1">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 7h4l2-2h4l2 2h4v12H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
                            Escolher fotografia
                        </label>
                        <button id="npcImportClearImage" type="button" class="npc-import-clear-image hidden shrink-0" aria-label="Remover fotografia" title="Remover fotografia">×</button>
                    </div>
                    <input id="npc_image" name="npc_image" type="file" accept="image/png,image/jpeg,image/webp,.jpg,.jpeg,.png,.webp" class="npc-import-visually-hidden">
                    <p class="npc-import-help mt-2 text-center">Foto opcional (até 8 MB).</p>
                    <p id="npcImportImageFilename" class="npc-import-selected-name mt-1 truncate text-center hidden" aria-live="polite"></p>
                </div>

                <div class="min-w-0">
                    <h3 class="npc-import-section-label">Enquadramento</h3>
                    <div id="npcImportControls" class="mt-4">
                        <div>
                            <label for="npcImportZoom" class="npc-import-control-heading"><span>Zoom</span><output id="npcImportZoomValue" for="npcImportZoom" class="npc-import-value">100%</output></label>
                            <input id="npcImportZoom" type="range" min="1" max="3" step="0.05" value="1" class="npc-import-range mt-3 w-full" disabled aria-label="Zoom do retrato">
                        </div>
                        <div class="mt-6">
                            <label for="npcImportHorizontal" class="npc-import-control-heading"><span>Posição horizontal</span><output id="npcImportHorizontalValue" for="npcImportHorizontal" class="npc-import-value">Centro</output></label>
                            <input id="npcImportHorizontal" type="range" min="0" max="100" step="1" value="50" class="npc-import-range mt-3 w-full" disabled aria-label="Posição horizontal do retrato">
                            <div class="npc-import-range-ends mt-1"><span>Esquerda</span><span>Direita</span></div>
                        </div>
                        <fieldset class="mt-6" id="npcImportVertical">
                            <legend class="npc-import-control-heading">Posição vertical</legend>
                            <div class="npc-import-position-grid mt-2" role="group" aria-label="Posição vertical do retrato">
                                <label><input type="radio" name="import_image_position" value="auto" checked disabled><span>Auto</span></label>
                                <label><input type="radio" name="import_image_position" value="top" disabled><span>Cima</span></label>
                                <label><input type="radio" name="import_image_position" value="center" disabled><span>Meio</span></label>
                                <label><input type="radio" name="import_image_position" value="bottom" disabled><span>Baixo</span></label>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <p id="npcImportError" class="hidden mt-4 npc-import-error" role="alert"></p>
            <footer class="npc-import-footer mt-5 flex items-center justify-end gap-2 border-t pt-4">
                <button type="button" data-close-npc-import="true" class="npc-import-cancel">Cancelar</button>
                <button type="submit" id="npcImportSubmit" class="npc-import-submit">Importar NPC</button>
            </footer>
        </form>
    </section>
</div>

<style>
    .npc-import-modal.hidden{display:none}
    .npc-import-backdrop{background:rgba(38,27,22,.62);backdrop-filter:blur(5px)}
    .npc-import-panel{width:min(760px,calc(100vw - 24px))!important;max-width:760px!important;max-height:calc(100dvh - 24px);background:#fbf8f2;border:1px solid #d1bea3;box-shadow:0 27px 75px rgba(39,22,14,.31);scrollbar-width:thin;scrollbar-color:#aa8b66 #fbf8f2;animation:npc-import-enter .2s ease both}
    .npc-import-header{background:linear-gradient(180deg,#fffdf8,#f6efe4);border-bottom:1px solid #e0d0b8}
    .npc-import-close{display:flex;align-items:center;justify-content:center;width:33px;height:33px;border-radius:10px;border:1px solid #dac6ab;background:#fffaf3;color:#6b1d14;transition:background .16s,border-color .16s}
    .npc-import-close:hover{background:#f2e5d6;border-color:#c7a57c}
    .npc-import-section-label{font:700 16px Georgia,serif;color:#63251b}
    .npc-import-json-caption{color:#9c795c;font-size:11px;font-weight:600}
    .npc-import-file-row{padding:5px 6px;background:#f6efe3;border-radius:11px;border:1px solid #dfcbb0}
    .npc-import-file-btn{display:flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:9px 14px;border-radius:10px;border:1px solid #cbb391;background:#fffdf8;color:#712c1f;font:700 12px Georgia,serif;cursor:pointer;transition:border-color .16s,background .16s,transform .16s}
    .npc-import-file-btn:hover{background:#f3e8d8;border-color:#9b663f;transform:translateY(-1px)}
    .npc-import-file-btn:focus-within{outline:2px solid #b28b5e;outline-offset:2px}
    .npc-import-photo-btn{width:100%}
    .npc-import-selected-name{color:#90765d;font-size:11px}
    .npc-import-clear{width:25px;height:25px;display:flex;align-items:center;justify-content:center;border-radius:8px;color:#984131;font-size:18px}
    .npc-import-clear:hover{background:#ebdccc}
    .npc-import-clear-image{width:38px;height:38px;border:1px solid #cbb391;border-radius:10px;background:#fffaf2;color:#8c3427;font-size:20px}
    .npc-import-clear-image:hover{background:#f5e6df}
    .npc-import-divider{height:1px;background:linear-gradient(90deg,transparent,#ddc7aa,transparent)}
    .npc-import-preview-area{display:flex;align-items:center;justify-content:center;min-height:226px;padding:18px 12px;border-radius:15px;border:1px solid #e1d0b9;background:radial-gradient(circle at 50% 32%,#fffdf9 0%,#f1e5d4 100%)}
    .npc-import-preview-ring{position:relative;width:184px;height:184px;overflow:hidden;border-radius:50%;border:5px solid #fffaf2;box-shadow:0 0 0 1px #bc956f,0 9px 25px rgba(80,48,29,.15);background:#e7d9c8}
    #npcImportPreviewImage{width:100%;height:100%;display:block;object-fit:cover;user-select:none;pointer-events:none;transition:transform .16s ease,object-position .16s ease}
    #npcImportPreviewImage.hidden{display:none}
    .npc-import-preview-fallback{display:flex;align-items:center;justify-content:center;height:100%;color:#986a4d}
    .npc-import-preview-fallback.hidden{display:none}
    .npc-import-help{color:#967c64;font-size:11px}
    .npc-import-control-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;font-weight:700;color:#6b281d}
    .npc-import-value{padding:3px 8px;border:1px solid #e3d3bc;border-radius:7px;background:#f4ebdd;color:#875a39;font:700 11px ui-monospace,SFMono-Regular,monospace}
    .npc-import-range{--npc-range-fill:50%;height:6px;margin-inline:0;appearance:none;-webkit-appearance:none;border-radius:99px;cursor:pointer;background:linear-gradient(90deg,#852b21 0 var(--npc-range-fill),#ddceba var(--npc-range-fill) 100%)}
    .npc-import-range::-webkit-slider-thumb{-webkit-appearance:none;width:17px;height:17px;border-radius:50%;border:3px solid #fffaf2;background:#852b21;box-shadow:0 0 0 1px #994e39,0 2px 6px rgba(58,28,17,.17)}
    .npc-import-range::-moz-range-thumb{width:12px;height:12px;border-radius:50%;border:3px solid #fffaf2;background:#852b21;box-shadow:0 0 0 1px #994e39}
    .npc-import-range:disabled{cursor:default;opacity:.55}
    .npc-import-range:focus-visible{outline:3px solid rgba(173,123,75,.4);outline-offset:6px}
    .npc-import-range-ends{display:flex;justify-content:space-between;color:#a58c74;font-size:10px}
    .npc-import-position-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:5px}
    .npc-import-position-grid label{position:relative;min-width:0;cursor:pointer}
    .npc-import-position-grid input{position:absolute;opacity:0;width:1px;height:1px}
    .npc-import-position-grid span{display:flex;align-items:center;justify-content:center;min-height:35px;padding:5px 3px;border-radius:9px;border:1px solid #ddc8ab;background:#fffcf7;color:#876950;font-size:11px;font-weight:700;transition:background .15s,color .15s,border-color .15s}
    .npc-import-position-grid input:checked+span{background:#77251b;border-color:#77251b;color:#fffaf1;box-shadow:0 3px 8px rgba(113,37,27,.15)}
    .npc-import-position-grid input:disabled+span{opacity:.65;cursor:default}
    .npc-import-position-grid input:focus-visible+span{outline:2px solid #ac8660;outline-offset:2px}
    .npc-import-error{border-radius:10px;border:1px solid #e7b7ae;background:#fff0ed;color:#842b23;padding:11px 12px;font-size:12px;font-weight:600}
    .npc-import-footer{border-color:#e1d2bc}
    .npc-import-cancel{padding:10px 14px;border:1px solid #d9c6ae;border-radius:10px;background:#fffaf2;color:#6b1d14;font-size:12px;font-weight:700}
    .npc-import-cancel:hover{background:#f1e7d9}
    .npc-import-submit{padding:11px 17px;border-radius:10px;background:linear-gradient(110deg,#6b1d14,#962e21);color:white;font:700 12px Georgia,serif;box-shadow:0 3px 10px rgba(107,29,20,.16)}
    .npc-import-submit:hover:not(:disabled){filter:brightness(1.06)}
    .npc-import-submit:disabled{opacity:.55;cursor:wait}
    .npc-import-visually-hidden{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
    /* Dimensões explícitas: não dependem do gerador de classes arbitrárias do Vite. */
    .npc-import-header{padding:13px 23px}
    .npc-import-form{padding:17px 23px 16px}
    .npc-import-layout{grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);gap:20px}
    .npc-import-divider{margin-top:15px;margin-bottom:16px}
    .npc-import-preview-area{min-height:192px;padding:12px}
    .npc-import-preview-ring{width:154px;height:154px}
    .npc-import-position-grid span{min-height:33px}
    .npc-import-footer{margin-top:18px;padding-top:13px}
    @media(max-width:640px){
        .npc-import-layout{grid-template-columns:minmax(0,1fr);gap:18px}
        .npc-import-header{padding:12px 16px}
        .npc-import-form{padding:16px}
        .npc-import-preview-area{min-height:174px}
        .npc-import-preview-ring{width:142px;height:142px}
    }
    @keyframes npc-import-enter{from{opacity:0;transform:translateY(10px) scale(.985)}to{opacity:1;transform:none}}
    @media (prefers-reduced-motion:reduce){.npc-import-panel{animation:none}#npcImportPreviewImage,.npc-import-file-btn{transition:none}}
</style>

<script>
(function () {
    'use strict';
    const byId = id => document.getElementById(id);
    const modal = byId('importModal');
    const form = byId('importForm');
    const jsonInput = byId('npc_file');
    const imageInput = byId('npc_image');
    const preview = byId('npcImportPreviewImage');
    const fallback = byId('npcImportPreviewFallback');
    const zoomInput = byId('npcImportZoom');
    const horizontalInput = byId('npcImportHorizontal');
    const verticalInputs = [...form.querySelectorAll('input[name="import_image_position"]')];
    const submit = byId('npcImportSubmit');
    let temporaryUrl = null;
    let previousBodyOverflow = '';
    let returnFocus = null;
    let submitting = false;

    const clamp = (n, min, max) => Math.max(min, Math.min(max, Number(n) || min));
    const activePosition = () => verticalInputs.find(input => input.checked)?.value || 'auto';
    function reportError(message) {
        const el = byId('npcImportError');
        el.textContent = message;
        el.classList.remove('hidden');
    }
    function clearError() {
        const el = byId('npcImportError');
        el.classList.add('hidden');
        el.textContent = '';
    }
    function revokePreviewUrl() {
        if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);
        temporaryUrl = null;
    }
    function imageSettings() {
        const zoom = clamp(zoomInput.value, 1, 3);
        const horizontal = clamp(horizontalInput.value, 0, 100) / 100;
        const pos = activePosition();
        const tall = preview.naturalWidth > 0 && preview.naturalHeight / preview.naturalWidth > 1.28;
        const vertical = pos === 'auto' ? (tall ? 0.12 : 0.5) : ({top:0,center:0.5,bottom:1})[pos] ?? 0.5;
        const originVertical = pos === 'auto' && tall ? 0 : vertical;
        return {zoom, horizontal, vertical, originVertical};
    }
    function updatePreview() {
        const {zoom, horizontal, vertical, originVertical} = imageSettings();
        byId('npcImportZoomValue').textContent = `${Math.round(zoom * 100)}%`;
        byId('npcImportHorizontalValue').textContent = horizontal === 0.5 ? 'Centro' : `${Math.round(horizontal * 100)}%`;
        zoomInput.style.setProperty('--npc-range-fill', `${(zoom - 1) * 50}%`);
        horizontalInput.style.setProperty('--npc-range-fill', `${horizontal * 100}%`);
        preview.style.objectPosition = `${horizontal * 100}% ${vertical * 100}%`;
        preview.style.transformOrigin = `${horizontal * 100}% ${originVertical * 100}%`;
        preview.style.transform = `scale(${zoom})`;
    }
    function setImageEnabled(enabled) {
        zoomInput.disabled = !enabled;
        horizontalInput.disabled = !enabled;
        verticalInputs.forEach(input => {input.disabled = !enabled;});
    }
    function clearImage() {
        imageInput.value = '';
        revokePreviewUrl();
        preview.removeAttribute('src');
        preview.classList.add('hidden');
        fallback.classList.remove('hidden');
        zoomInput.value = '1';
        horizontalInput.value = '50';
        verticalInputs[0].checked = true;
        setImageEnabled(false);
        byId('npcImportClearImage').classList.add('hidden');
        byId('npcImportImageFilename').classList.add('hidden');
        byId('npcImportImageFilename').textContent = '';
        updatePreview();
    }
    function resetForm() {
        form.reset();
        byId('npcImportJsonFilename').textContent = 'Nenhum arquivo escolhido';
        byId('npcImportClearJson').classList.add('hidden');
        clearImage();
        clearError();
        submitting = false;
        submit.disabled = false;
        submit.textContent = 'Importar NPC';
    }
    function open() {
        if (submitting) return;
        returnFocus = document.activeElement;
        previousBodyOverflow = document.body.style.overflow;
        resetForm();
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        jsonInput.focus();
    }
    function close() {
        if (submitting || modal.classList.contains('hidden')) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = previousBodyOverflow;
        resetForm();
        if (returnFocus?.isConnected) returnFocus.focus();
    }
    window.openImportModal = open;
    window.closeImportModal = close;

    jsonInput.addEventListener('change', () => {
        const file = jsonInput.files?.[0];
        byId('npcImportJsonFilename').textContent = file?.name || 'Nenhum arquivo escolhido';
        byId('npcImportClearJson').classList.toggle('hidden', !file);
        clearError();
    });
    byId('npcImportClearJson').addEventListener('click', () => {
        jsonInput.value = '';
        byId('npcImportJsonFilename').textContent = 'Nenhum arquivo escolhido';
        byId('npcImportClearJson').classList.add('hidden');
    });
    imageInput.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        clearError();
        if (!file) {clearImage();return;}
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            clearImage();reportError('Escolha uma imagem JPG, PNG ou WebP.');return;
        }
        if (file.size > 8 * 1024 * 1024) {
            clearImage();reportError('A imagem não pode ultrapassar 8 MB.');return;
        }
        revokePreviewUrl();
        temporaryUrl = URL.createObjectURL(file);
        preview.onload = () => {setImageEnabled(true);updatePreview();};
        preview.onerror = () => {clearImage();reportError('Não foi possível abrir essa imagem.');};
        preview.src = temporaryUrl;
        preview.classList.remove('hidden');
        fallback.classList.add('hidden');
        byId('npcImportImageFilename').textContent = file.name;
        byId('npcImportImageFilename').classList.remove('hidden');
        byId('npcImportClearImage').classList.remove('hidden');
        setImageEnabled(false);
        updatePreview();
    });
    byId('npcImportClearImage').addEventListener('click', clearImage);
    zoomInput.addEventListener('input', updatePreview);
    horizontalInput.addEventListener('input', updatePreview);
    verticalInputs.forEach(input => input.addEventListener('change', updatePreview));
    modal.addEventListener('click', event => {
        if (event.target.closest('[data-close-npc-import]')) { event.preventDefault(); close(); }
    });
    document.addEventListener('keydown', event => {
        if (modal.classList.contains('hidden')) return;
        if (event.key === 'Escape') {event.preventDefault();close();return;}
        if (event.key !== 'Tab') return;
        const focusable = [...modal.querySelectorAll('button:not([disabled]),input:not([disabled]),label[for]')]
            .filter(el => !el.classList.contains('hidden') && !!el.getClientRects().length);
        if (!focusable.length) return;
        const first = focusable[0], last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {event.preventDefault();last.focus();}
        else if (!event.shiftKey && document.activeElement === last) {event.preventDefault();first.focus();}
    });

    // Faz o recorte no navegador; o importador original recebe a imagem já enquadrada.
    // Dessa forma, NpcController::import e os dois formatos JSON permanecem intocados.
    function cropImage() {
        return new Promise(async (resolve, reject) => {
            try {
                if (!preview.complete || !preview.naturalWidth) await preview.decode();
                const size = 900;
                const canvas = document.createElement('canvas');
                canvas.width = size;
                canvas.height = size;
                const ctx = canvas.getContext('2d');
                if (!ctx) throw new Error('Canvas indisponível');
                const {zoom, horizontal, vertical, originVertical} = imageSettings();
                const width = preview.naturalWidth, height = preview.naturalHeight;
                const cover = Math.max(size / width, size / height);
                const scaledW = width * cover, scaledH = height * cover;
                const x = (size - scaledW) * horizontal;
                const y = (size - scaledH) * vertical;
                const originX = size * horizontal, originY = size * originVertical;
                ctx.translate(originX, originY);
                ctx.scale(zoom, zoom);
                ctx.translate(-originX, -originY);
                ctx.drawImage(preview, x, y, scaledW, scaledH);

                const source = imageInput.files[0];
                const mime = ['image/png','image/webp','image/jpeg'].includes(source.type) ? source.type : 'image/png';
                canvas.toBlob(blob => {
                    if (!blob) {reject(new Error('Falha ao criar a imagem enquadrada'));return;}
                    const extension = blob.type === 'image/webp' ? '.webp' : blob.type === 'image/jpeg' ? '.jpg' : '.png';
                    const stem = source.name.replace(/\.[^.]+$/, '');
                    resolve(new File([blob], `${stem}-retrato${extension}`, {type:blob.type, lastModified:Date.now()}));
                }, mime, 0.94);
            } catch (error) {reject(error);}
        });
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (submitting) return;
        clearError();
        const json = jsonInput.files?.[0];
        if (!json) {reportError('Escolha o arquivo JSON da ficha.');jsonInput.focus();return;}
        if (!/\.json$/i.test(json.name)) {reportError('Selecione um arquivo .json válido.');return;}
        submitting = true;
        submit.disabled = true;
        submit.textContent = 'Importando...';
        try {
            if (imageInput.files?.[0]) {
                const cropped = await cropImage();
                if (cropped.size > 8 * 1024 * 1024) throw new Error('O retrato final excede 8 MB.');
                const files = new DataTransfer();
                files.items.add(cropped);
                imageInput.files = files.files;
            }
            HTMLFormElement.prototype.submit.call(form);
        } catch (error) {
            submitting = false;
            submit.disabled = false;
            submit.textContent = 'Importar NPC';
            reportError('Não foi possível preparar o retrato. Tente outra imagem ou ajuste o zoom.');
            console.error('Importação de NPC — erro no enquadramento:', error);
        }
    });
})();
</script>
