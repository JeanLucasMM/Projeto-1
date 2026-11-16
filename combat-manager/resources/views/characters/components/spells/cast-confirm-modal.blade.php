{{-- Confirmação enxuta: origem da magia, recurso e concentração em uma única decisão. --}}
<template x-teleport="body">
    <div x-show="castConfirmOpen" x-cloak
        class="character-spellbook-v4 sb-modal-backdrop fixed inset-0 z-[150] flex items-center justify-center p-3 backdrop-blur-sm sm:p-5"
        :style="spellbookThemeStyle" @click.self="closeCastConfirm()" @keydown.escape.window="if (castConfirmOpen && !busy) closeCastConfirm()">

        <section role="dialog" aria-modal="true" aria-labelledby="sb-v14-cast-title"
            class="sb-modal-shell flex max-h-[92vh] w-full max-w-[570px] flex-col overflow-hidden rounded-[23px] border shadow-[0_22px_90px_rgba(41,25,15,.22)]">

            <header class="sb-modal-header relative shrink-0 overflow-hidden border-b px-5 py-4 sm:px-6"
                style="border-top:3px solid var(--spellbook-accent)">
                <div class="flex items-center gap-3.5">
                    {{-- Mesmo selo compacto da versão anterior; imagem personalizada é opcional. --}}
                    <span class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl border font-serif text-[17px] font-bold shadow-sm"
                        style="border-color:var(--spellbook-border);background:var(--spellbook-paper-alt);color:var(--spellbook-accent)">
                        <img x-show="castConfirmSpell?.personal_media_url && !castConfirmSpell?._mediaError" x-cloak
                            :src="castConfirmSpell?.personal_media_url || null"
                            x-on:error="if (castConfirmSpell) castConfirmSpell._mediaError = true"
                            x-on:load="if (castConfirmSpell) castConfirmSpell._mediaError = false"
                            :alt="castConfirmSpell?.name || 'Imagem do feitiço'"
                            class="absolute inset-0 h-full w-full object-contain" />
                        <span x-show="!castConfirmSpell?.personal_media_url || castConfirmSpell?._mediaError"
                            x-text="Number(castConfirmSpell?.level ?? 0) === 0 ? 'T' : castConfirmSpell?.level"></span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <span class="sb-label block text-[8px] font-bold uppercase tracking-[.16em]">Conjuração</span>
                        <h3 id="sb-v14-cast-title" class="sb-title mt-0.5 truncate font-serif text-[23px] font-black leading-tight"
                            x-text="castConfirmSpell?.name || 'Feitiço'"></h3>
                        <p class="sb-muted mt-0.5 truncate text-[10px]" x-text="castConfirmSpell?.subtitle_pt || castConfirmSpell?.subtitle || ''"></p>
                    </div>
                    <button type="button" @click="closeCastConfirm()" :disabled="busy"
                        class="sb-button-soft flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border text-[18px] transition hover:scale-105 disabled:opacity-40"
                        aria-label="Fechar">×</button>
                </div>
            </header>

            <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-5 py-5 sm:px-6">

                {{-- Origem só precisa de uma escolha se houver mais de um perfil. --}}
                <section x-show="availableCastProfiles(castConfirmSpell).length > 0" x-cloak>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <h4 class="sb-label text-[9px] font-black uppercase tracking-[.12em]">Conjurar como</h4>
                        <span x-show="availableCastProfiles(castConfirmSpell).length > 1" class="sb-muted text-[9px]">Escolha o atributo</span>
                    </div>
                    <div class="grid gap-2" :class="availableCastProfiles(castConfirmSpell).length > 1 ? 'grid-cols-2' : 'grid-cols-1'">
                        <template x-for="profile in availableCastProfiles(castConfirmSpell)" :key="profile.id">
                            <button type="button" @click="castProfileId = String(profile.id)"
                                :aria-pressed="String(castConfirmProfile()?.id) === String(profile.id)"
                                class="group flex min-w-0 items-center gap-3 rounded-2xl border p-3 text-left transition-all duration-150 hover:-translate-y-[1px]"
                                :style="String(castConfirmProfile()?.id) === String(profile.id)
                                    ? 'border-color:var(--spellbook-accent);background:var(--spellbook-accent-soft);box-shadow:inset 0 0 0 1px var(--spellbook-accent)'
                                    : 'border-color:var(--spellbook-border);background:var(--spellbook-paper-strong)'">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border text-[12px]"
                                    :style="String(castConfirmProfile()?.id) === String(profile.id)
                                        ? 'color:var(--spellbook-accent);border-color:var(--spellbook-border-strong)'
                                        : 'color:var(--spellbook-muted);border-color:var(--spellbook-border-soft)'">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 2.4 6.5L21 12l-6.6 2.5L12 21l-2.4-6.5L3 12l6.6-2.5L12 3Z"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <strong class="sb-title block truncate text-[12px]" x-text="profile.class"></strong>
                                    <span class="sb-muted mt-0.5 block text-[9px]" x-text="`Ataque ${formatBonus(profile.spell_attack_bonus)}`"></span>
                                </span>
                                <span class="shrink-0 text-right">
                                    <span class="sb-label block text-[8px] uppercase tracking-wider">CD</span>
                                    <strong class="sb-title block font-serif text-[21px] leading-none" x-text="profile.spell_save_dc"></strong>
                                </span>
                            </button>
                        </template>
                    </div>
                    <p x-show="castConfirmProfile() && !canCastSelectedProfile()" x-cloak
                        class="mt-2 rounded-lg border border-amber-300/60 bg-amber-50 px-3 py-2 text-[10px] text-amber-900">
                        Esta magia não está preparada neste perfil.
                    </p>
                </section>

                <p x-show="availableCastProfiles(castConfirmSpell).length === 0 && Number(castConfirmSpell?.level ?? 0) > 0 && !castConfirmSpell?.cast_without_slot && Number(castConfirmSpell?.free_casts_current ?? 0) === 0" x-cloak
                    class="rounded-xl border border-amber-300/60 bg-amber-50 px-3 py-2 text-[10px] text-amber-900">
                    Vincule e prepare esta magia na aba Origem para usar um perfil de conjuração.
                </p>

                {{-- Slot convencional e slot de pacto seguem a mesma seleção compacta. --}}
                <section x-show="Number(castConfirmSpell?.level ?? 0) > 0" x-cloak>
                    <div class="mb-2 flex items-center justify-between">
                        <h4 class="sb-label text-[9px] font-black uppercase tracking-[.12em]">Recurso</h4>
                        <span class="sb-muted text-[9px]" x-show="castResource === 'slot' && castSlotKey" x-text="`Círculo ${castLevel}`"></span>
                    </div>

                    <div class="space-y-3">
                        <div x-show="availableSlotsFor(castConfirmSpell).some(s => s.slot_type === 'spell')" x-cloak>
                            <p class="sb-muted mb-1.5 text-[9px] font-semibold">Espaços de magia</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="slot in availableSlotsFor(castConfirmSpell).filter(s => s.slot_type === 'spell')" :key="makeSlotKey(slot)">
                                    <button type="button" @click="castResource = 'slot'; castSlotKey = makeSlotKey(slot); changeCastSlot()"
                                        :aria-pressed="castResource === 'slot' && castSlotKey === makeSlotKey(slot)"
                                        class="inline-flex min-w-[89px] items-center justify-between gap-2 rounded-xl border px-3 py-2.5 text-left transition hover:-translate-y-[1px]"
                                        :style="castResource === 'slot' && castSlotKey === makeSlotKey(slot)
                                            ? 'border-color:var(--spellbook-accent);background:var(--spellbook-accent-soft);box-shadow:inset 0 0 0 1px var(--spellbook-accent)'
                                            : 'border-color:var(--spellbook-border);background:var(--spellbook-paper-strong)'">
                                        <strong class="sb-title font-serif text-[14px]" x-text="`${slot.slot_level}º`"></strong>
                                        <span class="sb-muted text-[9px] font-bold tabular-nums" x-text="`${slot.current}/${slot.maximum}`"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div x-show="availableSlotsFor(castConfirmSpell).some(s => String(s.slot_type).startsWith('pact:'))" x-cloak>
                            <p class="sb-muted mb-1.5 text-[9px] font-semibold">Pacto</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="slot in availableSlotsFor(castConfirmSpell).filter(s => String(s.slot_type).startsWith('pact:'))" :key="makeSlotKey(slot)">
                                    <button type="button" @click="castResource = 'slot'; castSlotKey = makeSlotKey(slot); changeCastSlot()"
                                        :aria-pressed="castResource === 'slot' && castSlotKey === makeSlotKey(slot)"
                                        class="inline-flex min-w-[116px] items-center justify-between gap-2 rounded-xl border px-3 py-2.5 text-left transition hover:-translate-y-[1px]"
                                        :style="castResource === 'slot' && castSlotKey === makeSlotKey(slot)
                                            ? 'border-color:var(--spellbook-accent);background:var(--spellbook-accent-soft);box-shadow:inset 0 0 0 1px var(--spellbook-accent)'
                                            : 'border-color:var(--spellbook-border);background:var(--spellbook-paper-alt)'">
                                        <strong class="sb-title text-[10px] font-black" x-text="slotLabel(slot)"></strong>
                                        <span class="sb-muted text-[9px] font-bold tabular-nums" x-text="`${slot.current}/${slot.maximum}`"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2" x-show="Number(castConfirmSpell?.free_casts_current ?? 0) > 0 || castConfirmSpell?.cast_without_slot" x-cloak>
                            <button type="button" x-show="Number(castConfirmSpell?.free_casts_current ?? 0) > 0"
                                @click="castResource = 'free'; castLevel = Number(castConfirmSpell.level)"
                                class="rounded-xl border px-3 py-2.5 text-[10px] font-bold transition"
                                :style="castResource === 'free' ? 'border-color:var(--spellbook-accent);background:var(--spellbook-accent-soft)' : 'border-color:var(--spellbook-border);background:var(--spellbook-paper-strong)'">
                                Uso gratuito · <span x-text="castConfirmSpell?.free_casts_current ?? 0"></span>
                            </button>
                            <button type="button" x-show="castConfirmSpell?.cast_without_slot"
                                @click="castResource = 'none'; castLevel = Number(castConfirmSpell.level)"
                                class="rounded-xl border px-3 py-2.5 text-[10px] font-bold transition"
                                :style="castResource === 'none' ? 'border-color:var(--spellbook-accent);background:var(--spellbook-accent-soft)' : 'border-color:var(--spellbook-border);background:var(--spellbook-paper-strong)'">
                                Sem gastar espaço
                            </button>
                        </div>
                    </div>
                    <p x-show="availableSlotsFor(castConfirmSpell).length === 0 && !castConfirmSpell?.cast_without_slot && Number(castConfirmSpell?.free_casts_current ?? 0) === 0" x-cloak
                        class="sb-muted mt-2 text-[10px]">Sem espaços disponíveis.</p>
                </section>

                <div x-show="Number(castConfirmSpell?.level ?? 0) === 0" x-cloak
                    class="flex items-center gap-2 rounded-xl border px-3 py-3"
                    style="background:var(--spellbook-paper-alt);border-color:var(--spellbook-border)">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m12 3 3 7 6 2-6 2-3 7-3-7-6-2 6-2 3-7Z"/></svg>
                    <span class="sb-title text-[11px] font-semibold">Truque · nenhum espaço consumido</span>
                </div>

                {{-- V21 — Resumo de dano: uma linha por efeito/condição.
                     Nada é somado quando o alvo/efeito exige escolha. --}}
                <div x-show="castDamageOptions(castConfirmSpell).length > 0 || castUpcastPreview(castConfirmSpell).active" x-cloak
                    class="rounded-xl border px-3.5 py-2.5"
                    style="border-color:var(--spellbook-border);background:var(--spellbook-paper-alt)">
                    <div class="space-y-2.5">
                        <div x-show="castDamageOptions(castConfirmSpell).length > 0" class="space-y-2">
                            <template x-for="(hit, i) in castDamageOptions(castConfirmSpell)" :key="`${i}:${hit.dice}:${hit.type}`">
                                <div class="flex flex-wrap items-baseline gap-x-2.5 gap-y-0.5"
                                    :class="i > 0 ? 'border-t pt-2' : ''"
                                    style="border-color:var(--spellbook-border-soft)">
                                    <span class="sb-label shrink-0 text-[8px] font-black uppercase tracking-[.10em]">Dano</span>
                                    <strong class="sb-title font-serif text-[18px] font-black tabular-nums" x-text="hit.dice"></strong>
                                    <span x-show="hit.type" class="sb-title text-[10px] font-bold" x-text="hit.type"></span>
                                    <span x-show="hit.condition || hit.qualifier" class="sb-muted text-[9px]"
                                        x-text="hit.condition || hit.qualifier"></span>
                                </div>
                            </template>
                        </div>
                        {{-- Efeitos complexos de círculo superior: texto integral, sem corte. --}}
                        <div x-show="castUpcastPreview(castConfirmSpell).active"
                            class="flex min-w-0 items-start gap-2"
                            :class="castDamageOptions(castConfirmSpell).length ? 'border-t pt-2' : ''"
                            style="border-color:var(--spellbook-border-soft)">
                            <span class="inline-flex shrink-0 items-center rounded-lg border px-2 py-1 text-[9px] font-bold"
                                style="border-color:var(--spellbook-border-strong);background:var(--spellbook-accent-soft);color:var(--spellbook-accent)"
                                x-text="`${castConfirmSpell?.level}º → ${castLevel}º`"></span>
                            <span class="sb-muted block min-w-0 flex-1 whitespace-pre-line break-words text-[10px] leading-[1.6]"
                                style="overflow-wrap:anywhere"
                                x-text="castUpcastPreview(castConfirmSpell).increment
                                    ? castUpcastPreview(castConfirmSpell).shortText
                                    : castUpcastPreview(castConfirmSpell).description"></span>
                        </div>
                    </div>
                </div>

                {{-- Aviso contextual: só quando existe concentração ativa a ser substituída. --}}
                <div x-show="castWillReplaceConcentration()" x-cloak
                    class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-[10px] text-amber-900">
                    <svg viewBox="0 0 24 24" class="mt-px h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 9v4m0 4h.01M10 3 2 19h20L14 3h-4Z"/></svg>
                    <span>Substituirá a concentração em <strong x-text="concentrationConflictLabel()"></strong>.</span>
                </div>
            </div>

            <footer class="flex shrink-0 items-center justify-end gap-2 border-t px-5 py-3.5 sm:px-6"
                style="border-color:var(--spellbook-border);background:var(--spellbook-paper-alt)">
                <button type="button" @click="closeCastConfirm()" :disabled="busy"
                    class="sb-button-soft rounded-xl border px-4 py-2.5 text-[10px] font-bold disabled:opacity-40">Cancelar</button>
                <button type="button" @click="confirmCast()"
                    :disabled="busy || (availableCastProfiles(castConfirmSpell).length > 1 && !castConfirmProfile()) || !canCastSelectedProfile() || (Number(castConfirmSpell?.level ?? 0) > 0 && castResource === 'slot' && !castSlotKey) || (Number(castConfirmSpell?.level ?? 0) > 0 && castResource === 'none' && !castConfirmSpell?.cast_without_slot)"
                    class="sb-button-primary inline-flex min-w-[118px] items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-[10px] font-black shadow-[0_5px_14px_rgba(88,38,23,.12)] transition hover:-translate-y-[1px] disabled:cursor-not-allowed disabled:opacity-45">
                    <span x-text="busy ? 'Conjurando…' : 'Conjurar'"></span>
                    <svg x-show="!busy" viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 18 12-12M9 6h9v9"/></svg>
                </button>
            </footer>
        </section>
    </div>
</template>
