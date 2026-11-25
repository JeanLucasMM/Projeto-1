{{-- Spellbound V66.2.77 — Leitor e conjuração de pergaminhos no Inventário.
     O texto é carregado individualmente do Grimório e renderizado via x-text
     para impedir HTML arbitrário em fontes adicionais/homebrew. --}}
<style>
    .character-sheet-theme[data-theme] .inventory-v277-modal {
        background: var(--theme-surface-raised, #fcf8f1);
        color: var(--theme-text-primary, #4e3028);
        border: 1px solid var(--theme-border-strong, #d9bd99);
        box-shadow: var(--theme-shadow-strong, 0 25px 75px rgba(0,0,0,.35));
    }
    .character-sheet-theme[data-theme] .inventory-v277-heading {
        font-family: Georgia, 'Times New Roman', serif;
        color: var(--theme-text-strong, #5c291d);
        font-weight: 700;
    }
    .character-sheet-theme[data-theme] .inventory-v277-rule {
        border-color: var(--theme-border-strong, #d9bd99);
    }
    .character-sheet-theme[data-theme] .inventory-v277-muted {
        color: var(--theme-text-secondary, #8d6a52);
    }
    .character-sheet-theme[data-theme] .inventory-v277-stat {
        background: var(--theme-surface-secondary, #f4ebdf);
        border: 1px solid var(--theme-border, #e3cdb1);
        border-radius: 10px;
        padding: 9px 12px;
        min-width: 0;
    }
    .character-sheet-theme[data-theme] .inventory-v277-stat span {
        display: block;
        font-size: 10px;
        color: var(--theme-text-secondary, #8d6a52);
        margin-bottom: 3px;
    }
    .character-sheet-theme[data-theme] .inventory-v277-stat strong {
        display: block;
        color: var(--theme-text-primary, #4e3028);
        font-size: 13px;
        overflow-wrap: anywhere;
    }
    .character-sheet-theme[data-theme] .inventory-v277-btn {
        background: var(--theme-accent-primary, #713224);
        color: var(--theme-text-on-accent, #fff9f3);
        border-radius: 9px;
        font-weight: 700;
        font-size: 12px;
        padding: 11px 17px;
        transition: filter .15s ease;
    }
    .character-sheet-theme[data-theme] .inventory-v277-btn:hover:not(:disabled) { filter: brightness(1.08); }
    .character-sheet-theme[data-theme] .inventory-v277-btn:disabled { opacity: .45; cursor: not-allowed; }
    .character-sheet-theme[data-theme] .inventory-v277-outline {
        border: 1px solid var(--theme-border-strong, #d9bd99);
        color: var(--theme-text-primary, #4e3028);
        background: transparent;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        padding: 10px 14px;
    }
    .character-sheet-theme[data-theme] .inventory-v277-scrolltext {
        white-space: pre-line;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 15px;
        line-height: 1.76;
        overflow-wrap: anywhere;
    }
    .character-sheet-theme[data-theme] .inventory-v277-error {
        border: 1px solid var(--theme-state-danger, #ba6054);
        border-radius: 9px;
        padding: 12px 14px;
        color: var(--theme-state-danger, #ba6054);
        font-size: 12px;
    }
</style>

{{-- Leitura completa do feitiço no padrão editorial do Grimório. --}}
<template x-teleport="body">
    <div x-show="scrollSpellReaderOpen" x-cloak
         data-theme="{{ $characterTheme }}"
         class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[315] flex items-center justify-center p-3 sm:p-6"
         role="dialog" aria-modal="true" aria-label="Ler o feitiço do pergaminho"
         @keydown.escape.stop.prevent="closeScrollReader()">
        <div class="character-theme-modal-backdrop absolute inset-0" @click="closeScrollReader()"></div>
        <article class="inventory-v277-modal relative z-10 flex w-full max-w-[860px] flex-col overflow-hidden rounded-2xl"
                 style="max-height: min(90dvh, 940px)">
            <header class="flex shrink-0 items-start justify-between gap-4 border-b px-5 py-4 sm:px-7 inventory-v277-rule">
                <div class="min-w-0">
                    <small class="inventory-v277-muted text-[10px] font-black uppercase tracking-[.12em]">Grimório · Pergaminho de feitiço</small>
                    <h2 class="inventory-v277-heading mt-1 text-[26px] leading-tight"
                        x-text="scrollSpellView(scrollReaderItem).name || 'Feitiço'"></h2>
                    <em class="inventory-v277-muted text-[13px]"
                        x-text="scrollSpellView(scrollReaderItem).subtitle"></em>
                </div>
                <button type="button" class="inventory-v277-outline shrink-0" @click="closeScrollReader()">Fechar</button>
            </header>
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7">
                <p x-show="scrollReaderBusy" class="inventory-v277-muted text-[13px]">Consultando os dados do Grimório...</p>
                <p x-show="scrollReaderError" x-cloak class="inventory-v277-error" x-text="scrollReaderError"></p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <template x-for="entry in scrollSpellStats(scrollReaderItem)" :key="entry.key">
                        <div class="inventory-v277-stat">
                            <span x-text="entry.name"></span>
                            <strong x-text="entry.value"></strong>
                        </div>
                    </template>
                </div>
                <div class="mt-6 inventory-v277-scrolltext" x-text="scrollSpellView(scrollReaderItem).description || (scrollReaderBusy ? '' : 'A descrição não está disponível neste pergaminho.')"></div>
                <section x-show="scrollSpellView(scrollReaderItem).higher"
                         x-cloak class="mt-6 border-t pt-5 inventory-v277-rule">
                    <h3 class="inventory-v277-heading text-lg">Em Níveis Superiores</h3>
                    <div class="inventory-v277-scrolltext mt-2"
                         x-text="scrollSpellView(scrollReaderItem).higher"></div>
                </section>
            </div>
            <footer class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t px-5 py-4 inventory-v277-rule sm:px-7">
                <small class="inventory-v277-muted text-[11px]">
                    A consulta não aprende nem desbloqueia este feitiço.
                </small>
                <button type="button" class="inventory-v277-btn"
                        x-show="scrollReaderItem && scrollCanCast(scrollReaderItem)"
                        @click="openScrollCast(scrollReaderItem)">Conjurar pelo pergaminho</button>
            </footer>
        </article>
    </div>
</template>

{{-- A confirmação usa a CD do item e a concentração compartilhada. --}}
<template x-teleport="body">
    <div x-show="scrollCastOpen" x-cloak
         data-theme="{{ $characterTheme }}"
         class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[316] flex items-center justify-center p-4"
         role="dialog" aria-modal="true" aria-label="Confirmar conjuração do pergaminho"
         @keydown.escape.stop.prevent="closeScrollCast()">
        <div class="character-theme-modal-backdrop absolute inset-0" @click="closeScrollCast()"></div>
        <section class="inventory-v277-modal relative z-10 w-full max-w-[540px] overflow-hidden rounded-2xl">
            <header class="border-b px-5 py-5 inventory-v277-rule">
                <small class="inventory-v277-muted text-[10px] font-black uppercase tracking-[.12em]">Usar pergaminho</small>
                <h2 class="inventory-v277-heading mt-1 text-[23px]"
                    x-text="scrollSpellView(scrollCastItem).name || 'Conjurar feitiço'"></h2>
                <p class="inventory-v277-muted mt-1 text-[12px]"
                   x-text="scrollSpellView(scrollCastItem).subtitle"></p>
            </header>
            <div class="space-y-4 px-5 py-5">
                <div class="grid grid-cols-3 gap-2">
                    <div class="inventory-v277-stat">
                        <span>CD do pergaminho</span><strong x-text="scrollSpellDc(scrollCastItem)"></strong>
                    </div>
                    <div class="inventory-v277-stat">
                        <span>Ataque</span><strong x-text="scrollSpellAttack(scrollCastItem)"></strong>
                    </div>
                    <div class="inventory-v277-stat">
                        <span>Usos</span><strong x-text="scrollUsesLabel(scrollCastItem)"></strong>
                    </div>
                </div>
                <p class="inventory-v277-muted text-[12px] leading-relaxed">
                    Não utiliza espaços de magia e não exige que este feitiço esteja preparado ou liberado no Grimório. Somente o uso do pergaminho será gasto.
                </p>
                <p x-show="scrollCastLoading" x-cloak class="inventory-v277-muted text-[12px]">Verificando a concentração atual...</p>
                <div x-show="scrollSpellView(scrollCastItem).concentration"
                     x-cloak class="inventory-v277-stat">
                    <strong>Requer concentração</strong>
                    <p class="inventory-v277-muted mt-2 text-[12px] leading-relaxed">
                        A concentração será controlada normalmente pela ficha.
                    </p>
                    <label x-show="scrollActiveConcentration?.active" class="mt-3 flex cursor-pointer items-start gap-2 text-[12px] leading-relaxed">
                        <input type="checkbox" x-model="scrollReplaceConcentration" class="mt-1">
                        <span>Encerrar a concentração em <strong x-text="scrollActiveConcentration?.effect_name || scrollActiveConcentration?.spell_name || 'outro efeito'"></strong> e iniciar esta.</span>
                    </label>
                </div>
                <p x-show="scrollCastError" x-cloak class="inventory-v277-error" x-text="scrollCastError"></p>
            </div>
            <footer class="flex items-center justify-end gap-2 border-t px-5 py-4 inventory-v277-rule">
                <button type="button" class="inventory-v277-outline" @click="closeScrollCast()" :disabled="scrollCastBusy">Cancelar</button>
                <button type="button" class="inventory-v277-btn"
                        :disabled="scrollCastBusy || scrollCastLoading || !scrollCanCast(scrollCastItem) || (scrollSpellView(scrollCastItem).concentration && scrollActiveConcentration?.active && !scrollReplaceConcentration)"
                        @click="confirmScrollCast()"
                        x-text="scrollCastBusy ? 'Conjurando...' : 'Confirmar conjuração'"></button>
            </footer>
        </section>
    </div>
</template>
