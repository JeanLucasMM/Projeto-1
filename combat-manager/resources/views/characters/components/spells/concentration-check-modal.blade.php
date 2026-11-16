{{-- V62: modal de concentração do Grimório alinhado ao das Habilidades, com confirmação e rerrolagem. --}}
@include('components.ui.poly-die-styles')
<style>
    .sb-v62-concentration-die {
        --theme-accent: var(--spellbook-state-concentration, #7b5548);
        --theme-accent-strong: var(--spellbook-state-concentration, #7b5548);
    }
    .sb-v62-concentration-die .sb-poly-die .sb-poly-number {
        font-size: 21px !important;
        font-weight: 760;
    }
    .sb-v62-concentration-manual {
        width: 100%;
        border-radius: 14px;
        border: 1px solid color-mix(in srgb, var(--spellbook-input-border) 88%, transparent 12%);
        background: var(--spellbook-input-bg);
        color: var(--spellbook-input-text);
        padding: 0.9rem 1rem;
        font-size: 0.95rem;
        font-weight: 700;
        outline: none;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
    }
    .sb-v62-concentration-manual::placeholder {
        color: var(--spellbook-input-placeholder);
    }
</style>
<template x-teleport="body">
    <div
        x-show="concentrationCheckOpen"
        x-cloak
        class="character-spellbook-v4 sb-modal-backdrop fixed inset-0 z-[165] flex items-center justify-center p-4 backdrop-blur-sm"
        :style="spellbookThemeStyle"
        @click.self="closeSpellConcentrationCheck()"
    >
        <section
            class="sb-modal-shell w-full max-w-md overflow-hidden rounded-[22px] border"
            role="alertdialog"
            aria-live="assertive"
            aria-label="Salvamento de concentração"
        >
            <header class="sb-modal-header border-t-[4px] px-5 py-4" style="border-top-color: var(--spellbook-state-concentration);">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                            style="
                                background: color-mix(in srgb, var(--spellbook-state-concentration) 14%, var(--spellbook-paper-strong) 86%);
                                color: var(--spellbook-state-concentration);
                            "
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M12 2v4M12 18v4M2 12h4M18 12h4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M19.1 4.9l-2.8 2.8M7.7 16.3l-2.8 2.8"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="text-[7px] font-black uppercase tracking-[0.14em]" style="color: var(--spellbook-state-concentration);">
                                Concentração
                            </p>
                            <h3 class="sb-title mt-0.5 font-serif text-[22px] font-black leading-tight">
                                Salvamento de Constituição
                            </h3>
                            <p class="sb-muted mt-1 text-[9px] leading-4">
                                <span x-text="concentration?.spell_name || 'Feitiço'" class="font-bold" style="color: var(--spellbook-title);"></span>
                                precisa manter a concentração após receber dano.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="closeSpellConcentrationCheck()"
                        :disabled="concentrationRolling"
                        class="sb-button-soft flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border text-base disabled:opacity-40"
                        aria-label="Fechar"
                    >×</button>
                </div>
            </header>

            <div class="px-5 py-4">
                <div class="sb-panel grid grid-cols-3 overflow-hidden rounded-xl border sb-border-soft sb-shadow">
                    <div class="px-3 py-3 text-center">
                        <span class="sb-label block text-[6px] font-black uppercase tracking-[0.12em]">CD</span>
                        <strong class="sb-title mt-1 block font-serif text-[22px] font-black leading-none" x-text="concentrationCheck?.dc ?? 10"></strong>
                    </div>
                    <div class="border-l sb-border-soft px-3 py-3 text-center">
                        <span class="sb-label block text-[6px] font-black uppercase tracking-[0.12em]">Dano</span>
                        <strong class="sb-title mt-1 block font-serif text-[22px] font-black leading-none" x-text="concentrationCheck?.damage ?? 0"></strong>
                    </div>
                    <div class="border-l sb-border-soft px-3 py-3 text-center">
                        <span class="sb-label block text-[6px] font-black uppercase tracking-[0.12em]">Mod.</span>
                        <strong class="sb-title mt-1 block font-serif text-[22px] font-black leading-none"
                            x-text="Number(concentrationCheck?.modifier ?? 0) >= 0 ? `+${Number(concentrationCheck?.modifier ?? 0)}` : `${Number(concentrationCheck?.modifier ?? 0)}`"></strong>
                    </div>
                </div>

                <p
                    x-show="Number(concentrationCheck?.exhaustion_penalty ?? 0) > 0"
                    class="mt-2 text-center text-[8px] font-semibold"
                    style="color: var(--spellbook-state-warning);"
                    x-text="`A exaustão está aplicando -${concentrationCheck?.exhaustion_penalty ?? 0} neste salvamento.`"
                ></p>

                <button
                    x-show="!concentrationRoll && !concentrationRolling && !concentrationManualMode"
                    x-cloak
                    type="button"
                    @click="rollSpellConcentrationCheck()"
                    class="sb-button-primary mt-4 w-full rounded-xl px-4 py-3 text-[9px] font-black uppercase tracking-[0.1em] shadow-[var(--spellbook-shadow)] transition"
                >
                    Rolar Constituição
                </button>

                <div x-show="!concentrationManualMode && (concentrationRolling || concentrationRoll)" x-cloak
                    class="sb-panel sb-border-soft mt-4 flex items-center gap-4 rounded-xl border px-4 py-3">
                    <div class="sb-v62-concentration-die grid shrink-0 place-items-center">
                        <x-ui.poly-die sides="20" size="70"
                            x-bind:class="concentrationRolling && !concentrationRoll ? 'is-rolling' : (concentrationRoll ? 'is-landed' : '')">
                            <span x-text="concentrationDieDisplay"></span>
                        </x-ui.poly-die>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="sb-label block text-[8px] font-black uppercase tracking-[0.08em]"
                            x-text="concentrationRolling && !concentrationRoll ? 'Rolando d20...' : 'Resultado total'"></span>
                        <strong class="sb-title mt-1 block font-serif text-[24px] font-black leading-none"
                            x-text="concentrationRolling && !concentrationRoll ? '···' : (concentrationRoll?.total ?? '—')"></strong>
                        <p x-show="concentrationRoll && !concentrationRolling" x-cloak
                            class="sb-muted mt-1 text-[10px] font-bold"
                            x-text="concentrationRoll?.passed ? 'Sucesso · aguardando confirmação' : 'Falha · aguardando confirmação'"></p>
                    </div>
                </div>

                <div x-show="concentrationManualMode" x-cloak class="mt-4">
                    <label class="sb-label mb-2 block text-[9px] font-black" for="spell-concentration-manual-total">
                        Total rolado na mesa (com modificador)
                    </label>
                    <input id="spell-concentration-manual-total" type="number" step="1"
                        class="sb-v62-concentration-manual"
                        x-model="concentrationManualTotal"
                        placeholder="Informe o resultado total">
                </div>

                <p x-show="concentrationError" x-cloak class="mt-3 text-[9px] font-bold" style="color: var(--spellbook-state-damage);" x-text="concentrationError"></p>
            </div>

            <footer class="sb-paper-alt flex justify-end border-t sb-border-soft px-5 py-3">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button
                        type="button"
                        x-show="!concentrationManualMode"
                        @click="concentrationManualMode = true; concentrationError = ''"
                        :disabled="concentrationRolling"
                        class="sb-button-soft rounded-lg border px-4 py-2 text-[8px] font-black uppercase tracking-[0.08em] disabled:opacity-40"
                    >
                        Rolar na mesa
                    </button>
                    <button
                        type="button"
                        x-show="concentrationManualMode"
                        x-cloak
                        @click="concentrationManualMode = false; concentrationError = ''"
                        :disabled="concentrationRolling"
                        class="sb-button-soft rounded-lg border px-4 py-2 text-[8px] font-black uppercase tracking-[0.08em] disabled:opacity-40"
                    >
                        Voltar aos dados
                    </button>
                    <button
                        type="button"
                        x-show="!concentrationManualMode && concentrationRoll"
                        x-cloak
                        @click="rollSpellConcentrationCheck()"
                        :disabled="concentrationRolling"
                        class="sb-button-soft rounded-lg border px-4 py-2 text-[8px] font-black uppercase tracking-[0.08em] disabled:opacity-40"
                    >
                        Rolar novamente
                    </button>
                    <button
                        type="button"
                        x-show="concentrationManualMode || concentrationRoll"
                        x-cloak
                        @click="confirmSpellConcentrationCheck()"
                        :disabled="concentrationRolling || (!concentrationManualMode && !concentrationRoll)"
                        class="sb-button-primary rounded-lg px-4 py-2 text-[8px] font-black uppercase tracking-[0.08em] disabled:opacity-40"
                    >
                        Usar resultado
                    </button>
                </div>
            </footer>
        </section>
    </div>
</template>