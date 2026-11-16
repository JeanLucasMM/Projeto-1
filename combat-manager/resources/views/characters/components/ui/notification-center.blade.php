{{--
    SPELLBOUND — CENTRO DE AVISOS (V1)

    Um único contrato para todas as telas:
    window.dispatchEvent(new CustomEvent('spellbound-notify', {
        detail: {
            title: 'Item enviado!',
            message: '2× Poção de Cura para Horácio.',
            variant: 'success', // success, info, warning, error
            duration: 4500,
        },
    }));

    Reutilize este partial uma única vez no layout principal de cada tela.
    O template é teleportado para o body e copia os tokens do tema ativo.
--}}
@once
<style>
    .sb-notices-host {
        position: fixed !important;
        bottom: clamp(76px, 11vh, 104px) !important;
        left: 50% !important;
        transform: translateX(-50%);
        width: min(92vw, 410px);
        z-index: 10050 !important;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }
    .sb-notice {
        position: relative;
        isolation: isolate;
        pointer-events: auto;
        opacity: 1 !important;
        display: flex;
        align-items: flex-start;
        gap: 11px;
        border-radius: 13px;
        border: 1px solid var(--theme-toast-border, var(--theme-border, #cbbca8)) !important;
        background: var(--theme-toast-surface, var(--theme-surface-raised, var(--theme-paper-strong, #fffdf8))) !important;
        background-image: none !important;
        color: var(--theme-text-primary, #3b2a21) !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, .25), 0 3px 12px rgba(0, 0, 0, .10) !important;
        -webkit-backdrop-filter: none !important;
        backdrop-filter: none !important;
        padding: 12px 13px 12px 16px;
        overflow: hidden;
    }
    .sb-notice::before {
        content: '';
        position: absolute;
        left: 0;
        top: 8px;
        bottom: 8px;
        width: 3px;
        border-radius: 0 8px 8px 0;
        background: var(--theme-accent-strong, #89572c);
    }
    .sb-notice--error::before { background: var(--theme-state-damage, #b42332); }
    .sb-notice--warning::before { background: var(--theme-state-warning, #bc842b); }
    .sb-notice__icon {
        width: 33px;
        height: 33px;
        flex: 0 0 33px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid var(--theme-border, #cbbca8);
        background: var(--theme-accent-soft, var(--theme-surface-inset, #f5ebdf));
        color: var(--theme-accent-strong, #89572c);
    }
    .sb-notice--error .sb-notice__icon {
        color: var(--theme-state-damage, #b42332);
        background: var(--theme-surface-inset, var(--theme-paper-strong, #fffdf8));
    }
    .sb-notice--warning .sb-notice__icon { color: var(--theme-state-warning, #bc842b); }
    .sb-notice__title {
        margin: 0;
        color: var(--theme-text-strong, #53150f) !important;
        font-family: Georgia, serif;
        font-weight: 800;
        font-size: 13px;
        line-height: 1.3;
    }
    .sb-notice__message {
        margin: 3px 0 0;
        color: var(--theme-text-primary, #563d30) !important;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }
    .sb-notice__close {
        flex: 0 0 27px;
        width: 27px;
        height: 27px;
        border-radius: 8px;
        border: 1px solid var(--theme-border, #cbbca8);
        background: var(--theme-surface-inset, var(--theme-paper-strong, #fffdf8));
        color: var(--theme-text-secondary, #755e4f);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 17px;
        cursor: pointer;
    }
    .sb-notice__close:hover { background: var(--theme-card-hover, var(--theme-paper-strong, #fffdf8)); }
    .character-sheet-theme[data-theme="midnight"].sb-notices-host .sb-notice {
        background: #293139 !important;
        border-color: rgba(151,162,173,.22) !important;
    }
    .character-sheet-theme[data-theme="obsidian"].sb-notices-host .sb-notice {
        background: var(--theme-surface-raised, #27262b) !important;
    }
    @media (prefers-reduced-motion: reduce) {
        .sb-notice { transition: none !important; }
    }
</style>
@endonce
<div
    x-data="{
        notices: [],
        serial: 0,
        currentTheme: 'spellbound',
        themeInline: '',
        themeObserver: null,

        init() {
            this.$nextTick(() => {
                this.syncTheme();
                const source = this.$el.closest('.character-sheet-theme[data-theme]');
                if (!source) return;
                this.themeObserver = new MutationObserver(() => this.syncTheme());
                this.themeObserver.observe(source, {
                    attributes: true,
                    attributeFilter: ['data-theme', 'style', 'class'],
                });
            });
        },

        destroy() {
            this.themeObserver?.disconnect();
            this.themeObserver = null;
        },

        syncTheme() {
            const source = this.$el.closest('.character-sheet-theme[data-theme]')
                || document.querySelector('.character-sheet-theme[data-theme]');
            if (!source) return;

            this.currentTheme = source.dataset.theme || 'spellbound';
            const computed = window.getComputedStyle(source);
            const tokens = [
                '--theme-paper-strong', '--theme-surface-raised',
                '--theme-surface-inset', '--theme-accent-soft',
                '--theme-accent-strong', '--theme-accent',
                '--theme-text-primary', '--theme-text-strong',
                '--theme-text-secondary', '--theme-border',
                '--theme-card-hover', '--theme-state-damage',
                '--theme-state-warning', '--theme-toast-border',
                '--theme-toast-surface',
            ];
            this.themeInline = tokens.map((token) => {
                const value = computed.getPropertyValue(token).trim();
                return value ? `${token}: ${value};` : '';
            }).filter(Boolean).join(' ');
        },

        push(data) {
            if (!data || typeof data !== 'object') return;
            const message = String(data.message ?? '').trim().slice(0, 320);
            if (!message) return;

            this.syncTheme();
            const variant = ['success', 'info', 'warning', 'error'].includes(data.variant)
                ? data.variant
                : 'info';
            const notice = {
                id: ++this.serial,
                title: String(data.title ?? 'Spellbound').trim().slice(0, 100),
                message,
                variant,
            };
            this.notices = [...this.notices, notice].slice(-3);
            const requested = Number(data.duration);
            const duration = Number.isFinite(requested) && requested > 0
                ? Math.max(2000, Math.min(12000, requested))
                : 4800;
            window.setTimeout(() => this.dismiss(notice.id), duration);
        },

        dismiss(id) {
            this.notices = this.notices.filter(notice => notice.id !== id);
        },
    }"
    x-on:spellbound-notify.window="push($event.detail)"
>
    <template x-teleport="body">
        <div
            class="character-sheet-theme sb-notices-host"
            :data-theme="currentTheme"
            :style="themeInline"
            role="region"
            aria-label="Avisos do Spellbound"
            aria-live="polite"
            aria-relevant="additions"
        >
            <template x-for="notice in notices" :key="notice.id">
                <article
                    class="sb-notice"
                    :class="'sb-notice--' + notice.variant"
                    :role="notice.variant === 'error' ? 'alert' : 'status'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-1"
                >
                    <div class="sb-notice__icon" aria-hidden="true">
                        <svg x-show="notice.variant !== 'error' && notice.variant !== 'warning'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m5 12 4 4L19 6" />
                        </svg>
                        <svg x-show="notice.variant === 'error' || notice.variant === 'warning'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4m0 4h.01" /><path d="m10.3 3.9-8.3 14.4a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                        </svg>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p class="sb-notice__title" x-text="notice.title"></p>
                        <p class="sb-notice__message" x-text="notice.message"></p>
                    </div>
                    <button type="button" class="sb-notice__close" @click="dismiss(notice.id)" aria-label="Fechar aviso">×</button>
                </article>
            </template>
        </div>
    </template>
</div>
