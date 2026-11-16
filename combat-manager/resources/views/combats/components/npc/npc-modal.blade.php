{{-- V66.0.41 — Seleção de múltiplos NPCs, aliados e inimigos, em uma única operação. --}}
<div
    x-show="openNpcModal"
    x-cloak
    x-transition.opacity.duration.180ms
    x-data="{
        search: '',
        selectedIds: [],
        sides: {},
        quantities: {},
        allowDuplicates: false,
        get chosen() {
            return this.selectedIds.map(id => ({
                npc_id: Number(id),
                side: this.sides[id] || 'enemy',
                quantity: Number(this.quantities[id] || 1)
            }));
        },
        get alliedCount() { return this.chosen.filter(item => item.side === 'ally').reduce((n, item) => n + item.quantity, 0); },
        get enemyCount() { return this.chosen.filter(item => item.side === 'enemy').reduce((n, item) => n + item.quantity, 0); },
        get totalCount() { return this.alliedCount + this.enemyCount; },
        isSelected(id) { return this.selectedIds.includes(String(id)); },
        add(id) {
            id = String(id);
            if (!this.selectedIds.includes(id)) this.selectedIds = [...this.selectedIds, id];
        },
        toggle(id) {
            id = String(id);
            this.selectedIds = this.isSelected(id)
                ? this.selectedIds.filter(value => value !== id)
                : [...this.selectedIds, id];
        },
        selectSide(id, side) {
            id = String(id);
            this.add(id);
            this.sides = {...this.sides, [id]: side};
        },
        getSide(id) { return this.sides[String(id)] || 'enemy'; },
        getQuantity(id) { return Number(this.quantities[String(id)] || 1); },
        changeQuantity(id, step) {
            id = String(id);
            this.quantities = {...this.quantities, [id]: Math.max(1, Math.min(8, this.getQuantity(id) + step))};
        },
        isVisible(searchable) {
            return !this.search.trim() || String(searchable || '').toLocaleLowerCase().includes(this.search.trim().toLocaleLowerCase());
        },
        selectVisible() {
            if (!this.$refs.npcGrid) return;
            this.$refs.npcGrid.querySelectorAll('[data-npc-id][data-search]').forEach(card => {
                if (this.isVisible(card.dataset.search)) this.add(card.dataset.npcId);
            });
        },
        setAllSide(side) {
            const next = {...this.sides};
            this.selectedIds.forEach(id => { next[id] = side; });
            this.sides = next;
        },
        reset() {
            this.search = '';
            this.selectedIds = [];
            this.sides = {};
            this.quantities = {};
            this.allowDuplicates = false;
        }
    }"
    x-init="if ({{ old('npcs') !== null ? 'true' : 'false' }}) openNpcModal = true"
    x-on:keydown.escape.window="if (openNpcModal) openNpcModal = false"
    x-on:click.self="openNpcModal = false"
    class="fixed inset-0 z-[70] flex items-start justify-center overflow-y-auto bg-stone-950/65 px-3 py-[4vh] backdrop-blur-[3px] sm:px-6"
    aria-label="Adicionar criaturas ao combate"
>
    <section
        class="flex w-full max-w-[1100px] max-h-[92vh] flex-col overflow-hidden rounded-[19px] border border-[#dac8ad] bg-[#f9f6f0] shadow-[0_28px_100px_rgba(25,13,10,.37)] ring-1 ring-white/60"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sb-batch-npc-title"
    >
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-[#e2d5c2] bg-gradient-to-r from-[#f2ece2] to-[#fcfaf6] px-5 py-4 sm:px-7">
            <div class="min-w-0">
                <p class="text-[9px] font-extrabold uppercase tracking-[.22em] text-[#97724e]">Gerenciador de combate</p>
                <h2 id="sb-batch-npc-title" class="mt-1 font-serif text-[23px] font-bold leading-tight text-[#6b1d14]">Convocar criaturas</h2>
                <p class="mt-1 text-[12px] text-[#8c735b]">Selecione várias criaturas e defina quem luta ao lado dos jogadores.</p>
            </div>
            <button type="button" x-on:click="openNpcModal = false" aria-label="Fechar seleção de criaturas"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#e1d1bc] bg-white/75 text-[#8d685a] transition hover:border-[#bca08b] hover:bg-white hover:text-[#6b1d14] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#6b1d14]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </header>

        @if(old('npcs') !== null && $errors->any())
            <div class="border-b border-red-200 bg-red-50 px-5 py-2.5 text-[12px] font-semibold text-red-800" role="alert">
                {{ $errors->first() }} Selecione novamente as criaturas para tentar outra vez.
            </div>
        @endif

        <div class="shrink-0 border-b border-[#e5d9ca] bg-[#fbf9f5] px-5 py-4 sm:px-7">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <label class="relative block flex-1">
                    <span class="sr-only">Pesquisar criatura por nome ou tipo</span>
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[#aa9076]" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input type="search" x-model="search" placeholder="Buscar por nome ou tipo de criatura..." autocomplete="off"
                        class="w-full rounded-xl border border-[#dac8b2] bg-white py-2.5 pl-10 pr-4 text-[13px] text-[#5c2c21] placeholder-[#a69689] shadow-sm outline-none transition focus:border-[#a87656] focus:ring-2 focus:ring-[#a87656]/15">
                </label>
                <div class="flex items-center justify-between gap-2 md:justify-end">
                    <button type="button" x-on:click="selectVisible()" class="rounded-lg border border-[#d8c6ad] bg-white px-3 py-2 text-[10px] font-bold text-[#7a4c31] transition hover:bg-[#f2e6d6]">Selecionar visíveis</button>
                    <button type="button" x-on:click="reset()" x-bind:disabled="selectedIds.length === 0" class="rounded-lg px-2.5 py-2 text-[10px] font-bold text-[#a37960] hover:text-[#6b1d14] disabled:opacity-40">Limpar</button>
                    <span class="rounded-full border border-[#d8c6ad] bg-[#f3eadd] px-3 py-1.5 text-[10px] font-extrabold text-[#6b1d14]" x-text="selectedIds.length + ' selecionados'"></span>
                </div>
            </div>
        </div>

        <form action="{{ route('master.combats.npcs.batch', ['combat' => $combat->id]) }}" method="POST" class="flex min-h-0 flex-1 flex-col" x-on:submit="if (selectedIds.length === 0) $event.preventDefault()">
            @csrf
            <input type="hidden" name="allow_duplicates" x-bind:value="allowDuplicates ? 1 : 0">
            <template x-for="(item, index) in chosen" :key="item.npc_id">
                <span style="display:none">
                    <input type="hidden" x-bind:name="'npcs[' + index + '][npc_id]'" x-bind:value="item.npc_id">
                    <input type="hidden" x-bind:name="'npcs[' + index + '][side]'" x-bind:value="item.side">
                    <input type="hidden" x-bind:name="'npcs[' + index + '][quantity]'" x-bind:value="item.quantity">
                </span>
            </template>

            <div class="custom-scrollbar min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
                @if($npcs->isEmpty())
                    <div class="flex min-h-[260px] flex-col items-center justify-center gap-3 text-[#a18067]">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/><path d="M8 7h8M8 11h5"/></svg>
                        <p class="font-serif text-[17px] font-semibold">Nenhum NPC disponível no bestiário.</p>
                    </div>
                @else
                    <div x-ref="npcGrid" class="grid grid-cols-1 items-stretch gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($npcs as $npc)
                            <article
                                data-npc-id="{{ (int) $npc->id }}"
                                data-search="{{ mb_strtolower(($npc->name ?? '') . ' ' . ($npc->creature_type ?? ''), 'UTF-8') }}"
                                x-show="isVisible($el.dataset.search)"
                                x-bind:class="isSelected({{ (int) $npc->id }}) ? 'border-[#bb8752] bg-[#fffbf3] shadow-[0_6px_20px_rgba(120,64,21,.13)] ring-1 ring-[#c69561]/40' : 'border-[#e0d3c2] bg-white hover:border-[#bc9774] hover:shadow-[0_5px_18px_rgba(77,39,17,.08)]'"
                                class="group relative flex min-w-0 flex-col overflow-hidden rounded-[15px] border transition-all duration-200"
                            >
                                <button type="button" x-on:click="toggle({{ (int) $npc->id }})" class="flex min-w-0 gap-3 p-3 text-left" x-bind:aria-pressed="isSelected({{ (int) $npc->id }})">
                                    <div class="relative h-[106px] w-[84px] shrink-0 overflow-hidden rounded-xl border border-[#dfd3c2] bg-[#f1ebe2] shadow-inner">
                                        @if(!empty($npc->image_path))
                                            <img src="{{ asset('storage/' . ltrim($npc->image_path, '/')) }}" alt="Retrato de {{ $npc->name }}" loading="lazy"
                                                class="h-full w-full object-contain object-bottom"
                                                x-on:error="$el.style.display = 'none'; $el.nextElementSibling.style.display = 'flex'">
                                            <div class="hidden h-full w-full items-center justify-center text-[#b79c80]" aria-hidden="true">
                                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                                            </div>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[#b79c80]" aria-hidden="true">
                                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                                        <div class="flex min-w-0 items-start justify-between gap-2">
                                            <h3 class="min-w-0 break-words font-serif text-[14px] font-bold leading-[1.2] text-[#6b1d14]" title="{{ $npc->name }}">{{ $npc->name }}</h3>
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border text-[11px] font-bold transition"
                                                x-bind:class="isSelected({{ (int) $npc->id }}) ? 'border-[#ab783e] bg-[#89572b] text-white' : 'border-[#cdbda9] bg-[#fcfaf7] text-transparent'">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><path d="m4 12 5 5L20 6"/></svg>
                                            </span>
                                        </div>
                                        <p class="truncate text-[10px] text-[#977e68]">{{ $npc->creature_type ?: 'Criatura' }}</p>
                                        <div class="mt-auto flex flex-wrap gap-1.5">
                                            <span class="rounded-md border border-[#e4d2b5] bg-[#f8f1e6] px-2 py-1 text-[10px] font-bold text-[#79502c]">ND {{ $npc->challenge_rating ?? '—' }}</span>
                                            <span class="rounded-md border border-[#d2dfd4] bg-[#edf6ef] px-2 py-1 text-[10px] font-bold text-[#277253]">PV {{ $npc->max_hp ?? '—' }}</span>
                                            <span class="rounded-md border border-[#dfd8d0] bg-[#f5f2ee] px-2 py-1 text-[10px] font-bold text-[#836b59]">CA {{ $npc->armor_class ?? '—' }}</span>
                                        </div>
                                    </div>
                                </button>
                                <div class="mt-auto border-t border-[#ede1d2] bg-[#faf7f2] px-3 py-2.5">
                                    <div class="grid grid-cols-2 gap-1.5" role="group" aria-label="Lado do NPC {{ $npc->name }}">
                                        <button type="button" x-on:click="selectSide({{ (int) $npc->id }}, 'enemy')"
                                            x-bind:aria-pressed="isSelected({{ (int) $npc->id }}) && getSide({{ (int) $npc->id }}) === 'enemy'"
                                            x-bind:class="isSelected({{ (int) $npc->id }}) && getSide({{ (int) $npc->id }}) === 'enemy' ? 'border-[#b87567] bg-[#f5e6e1] text-[#933f31]' : 'border-[#e4d9cd] bg-white text-[#998473] hover:bg-[#f7efeb]'"
                                            class="rounded-lg border py-1.5 text-[10px] font-extrabold transition">Inimigo</button>
                                        <button type="button" x-on:click="selectSide({{ (int) $npc->id }}, 'ally')"
                                            x-bind:aria-pressed="isSelected({{ (int) $npc->id }}) && getSide({{ (int) $npc->id }}) === 'ally'"
                                            x-bind:class="isSelected({{ (int) $npc->id }}) && getSide({{ (int) $npc->id }}) === 'ally' ? 'border-[#7fb9a2] bg-[#e4f3e9] text-[#176c57]' : 'border-[#e4d9cd] bg-white text-[#998473] hover:bg-[#edf7f1]'"
                                            class="rounded-lg border py-1.5 text-[10px] font-extrabold transition">Aliado</button>
                                    </div>
                                    <div x-show="isSelected({{ (int) $npc->id }})" x-cloak class="mt-2 flex items-center justify-between border-t border-[#eee4d7] pt-2">
                                        <span class="text-[10px] font-semibold text-[#937a64]">Quantidade</span>
                                        <div class="inline-flex items-center overflow-hidden rounded-lg border border-[#dfd1c0] bg-white">
                                            <button type="button" x-on:click="changeQuantity({{ (int) $npc->id }}, -1)" class="flex h-7 w-7 items-center justify-center text-[#7a4d3f] hover:bg-[#f4e8dc]" aria-label="Diminuir quantidade de {{ $npc->name }}">−</button>
                                            <span class="min-w-[24px] text-center text-[11px] font-extrabold text-[#6b1d14]" x-text="getQuantity({{ (int) $npc->id }})"></span>
                                            <button type="button" x-on:click="changeQuantity({{ (int) $npc->id }}, 1)" class="flex h-7 w-7 items-center justify-center text-[#7a4d3f] hover:bg-[#f4e8dc]" aria-label="Aumentar quantidade de {{ $npc->name }}">+</button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <p x-show="search.trim() && $refs.npcGrid && !Array.from($refs.npcGrid.querySelectorAll('[data-search]')).some(el => isVisible(el.dataset.search))" x-cloak class="py-14 text-center font-serif italic text-[#907765]">Nenhuma criatura corresponde à pesquisa.</p>
                @endif
            </div>

            <footer class="shrink-0 border-t border-[#e3d6c5] bg-gradient-to-b from-[#fcfaf6] to-[#f3eadf] px-5 py-4 sm:px-7">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-2">
                        <span class="text-[10px] font-bold text-[#826247]" x-text="totalCount + ' criatura(s) a adicionar'"></span>
                        <span class="rounded-full border border-[#e3bdb4] bg-[#f7e6e1] px-2 py-1 text-[9px] font-bold text-[#9a4b3f]" x-text="enemyCount + ' inimigos'"></span>
                        <span class="rounded-full border border-[#b9d9c7] bg-[#e9f6ed] px-2 py-1 text-[9px] font-bold text-[#236e56]" x-text="alliedCount + ' aliados'"></span>
                        <div x-show="selectedIds.length > 0" class="flex items-center gap-1.5">
                            <button type="button" x-on:click="setAllSide('enemy')" class="text-[10px] font-semibold text-[#a14f42] underline-offset-2 hover:underline">Todos inimigos</button>
                            <span class="text-[#ccb69f]">·</span>
                            <button type="button" x-on:click="setAllSide('ally')" class="text-[10px] font-semibold text-[#217258] underline-offset-2 hover:underline">Todos aliados</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click="openNpcModal = false" class="rounded-xl border border-[#d9c7b1] bg-white px-4 py-2.5 text-[11px] font-bold text-[#79543d] transition hover:bg-[#f5ede4]">Cancelar</button>
                        <button type="submit" x-bind:disabled="!selectedIds.length"
                            class="min-w-[190px] rounded-xl border border-[#6b1d14] bg-[#6b1d14] px-5 py-2.5 text-[11px] font-extrabold tracking-wide text-white shadow-[0_4px_12px_rgba(107,29,20,.18)] transition hover:bg-[#54150f] disabled:cursor-not-allowed disabled:opacity-40"
                            x-text="'Adicionar ' + totalCount + ' ao combate'">Adicionar ao combate</button>
                    </div>
                </div>
                <label class="mt-3 inline-flex cursor-pointer items-center gap-2 text-[10px] text-[#8d735d]">
                    <input type="checkbox" x-model="allowDuplicates" class="h-3.5 w-3.5 rounded border-[#c9b59e] text-[#6b1d14] focus:ring-[#6b1d14]/20">
                    Permitir adicionar cópias de NPCs que já estão neste combate
                </label>
                <p class="mt-1 text-[9px] text-[#aa8a72]">Sem essa opção, criaturas já presentes são ignoradas. Quantidade maior que 1 cria cópias novas da criatura selecionada.</p>
            </footer>
        </form>
    </section>
</div>

{{-- Compatibilidade: confirmação de duplicidade do endpoint antigo de adição individual. --}}
@if(session('duplicateNpc'))
    <div class="fixed inset-0 z-[80] flex items-center justify-center bg-stone-950/65 px-4 backdrop-blur-sm">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-[#dac8b0] bg-[#f9f6f0] shadow-2xl">
            <div class="px-6 py-6 text-center">
                <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full border border-amber-200 bg-amber-50 text-amber-700">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v5m0 4h.01M10 3 2.7 16a2 2 0 0 0 1.75 3h15.1a2 2 0 0 0 1.75-3L14 3a2.3 2.3 0 0 0-4 0Z"/></svg>
                </div>
                <h2 class="font-serif text-lg font-bold text-[#6b1d14]">Criatura já presente</h2>
                <p class="mt-2 text-[13px] text-[#826a55]">
                    <strong>{{ session('duplicateNpc.name') }}</strong> já está no combate. Deseja adicionar outra cópia?
                </p>
            </div>
            <div class="flex gap-2 border-t border-[#e6d8c7] bg-[#f3ede4] px-4 py-3">
                <a href="{{ route('combats.show', $combat) }}" class="flex-1 rounded-lg border border-[#dac8b0] bg-white px-3 py-2.5 text-center text-[11px] font-bold text-[#765541]">Cancelar</a>
                <form action="{{ route('combats.npcs.store', $combat) }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="npc_id" value="{{ session('duplicateNpc.npc') }}">
                    <input type="hidden" name="force" value="1">
                    <button type="submit" class="w-full rounded-lg bg-[#6b1d14] px-3 py-2.5 text-[11px] font-bold text-white hover:bg-[#53150f]">Adicionar cópia</button>
                </form>
            </div>
        </div>
    </div>
@endif
