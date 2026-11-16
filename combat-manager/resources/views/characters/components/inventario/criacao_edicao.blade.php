    {{-- EDITOR --}}
    <template x-teleport="body">
        <div
            x-show="editorOpen"
            x-cloak

            x-transition:enter="character-theme-modal-layer-enter"
            x-transition:enter-start="character-theme-modal-layer-enter-start"
            x-transition:enter-end="character-theme-modal-layer-enter-end"
            x-transition:leave="character-theme-modal-layer-leave"
            x-transition:leave-start="character-theme-modal-layer-leave-start"
            x-transition:leave-end="character-theme-modal-layer-leave-end"

            data-theme="{{ $characterTheme }}"

            role="dialog"
            aria-modal="true"
            aria-label="Editor de item"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[240]

                flex
                items-center
                justify-center

                p-4
            "
        >
            <div
                class="
                    character-theme-modal-backdrop
                    absolute
                    inset-0
                "
                @click="closeEditor()"
            ></div>

            <div
                x-transition:enter="character-theme-modal-panel-enter"
                x-transition:enter-start="character-theme-modal-panel-enter-start"
                x-transition:enter-end="character-theme-modal-panel-enter-end"
                x-transition:leave="character-theme-modal-panel-leave"
                x-transition:leave-start="character-theme-modal-panel-leave-start"
                x-transition:leave-end="character-theme-modal-panel-leave-end"

                class="
                    character-theme-modal
                    inventory-v35-modal
                    inventory-v35-editor-modal

                    relative
                    z-10

                    flex
                    max-h-[94dvh]
                    w-full
                    max-w-[960px]
                    flex-col
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
            >
                <div class="flex shrink-0 items-center justify-between border-b border-[#d8c7ab]/60 bg-[#efe9dc]/58 px-5 py-3.5">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.14em] text-[#8c6239]">Item</p>
                        <h3 class="font-serif text-[21px] font-black text-[#53150f]" x-text="editingId ? 'Editar Item' : 'Novo Item'"></h3>
                    </div>

                    <button type="button" @click="closeEditor()" class="inventory-v45-editor-close flex h-9 w-9 items-center justify-center rounded-lg text-xl text-[#8c6239] hover:bg-[#e8dfd1]" title="Fechar editor" aria-label="Fechar editor">×</button>
                </div>

                <div
                    x-show="form"
                    @scroll="hideEditorTooltip()"
                    class="
                        inventory-v32-editor-scroll
                        min-h-0
                        flex-1
                        overflow-y-auto
                        overflow-x-hidden
                        px-5
                        py-5
                        sm:px-6
                    "
                >
                    <section>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-[160px_minmax(0,1fr)]">
                            <div>
                                <label class="inventory-v10-thumb relative flex aspect-square w-full cursor-pointer items-center justify-center rounded-xl border border-dashed border-[#cdbb9f] bg-[#efe9dc]/30">
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" alt="Prévia do item">
                                    </template>
                                    <template x-if="!imagePreview">
                                        <div class="text-center">
                                            <svg class="mx-auto h-7 w-7 text-[#8c6239]/45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01" />
                                            </svg>
                                            <span class="mt-2 block text-[10px] font-black uppercase text-[#8c6239]">Adicionar Foto</span>
                                        </div>
                                    </template>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        @click="$event.target.value = null"
                                        @change="handleImage($event)"
                                        class="hidden"
                                    >
                                </label>

                                <button
                                    x-show="imagePreview"
                                    x-cloak
                                    type="button"
                                    @click="removeImage()"
                                    class="mt-2 w-full text-[9px] font-black uppercase text-red-700 hover:underline"
                                >
                                    Remover Foto
                                </button>
                            </div>

                            <div class="space-y-4">
                                <label class="block">
                                    <span class="text-[12px] font-black text-[#53150f]">Nome</span>
                                    <input type="text" maxlength="120" x-model="form.name" placeholder="Ex.: Machado Lampejo Real" class="inventory-v10-input mt-1.5">
                                </label>

                                <div>
                                    <div class="mb-1.5 flex items-center justify-between gap-3">
                                        <span class="text-[12px] font-black text-[#53150f]">É equipável?</span>
                                        <span class="text-[10px] text-[#8c6239]/70">Permite Equipar / Guardar.</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 sm:max-w-xs">
                                        <button type="button" @click="setEquippable(true)" class="inventory-v10-choice" :class="{ 'active': form.properties.inventory.equippable }">Sim</button>
                                        <button type="button" @click="setEquippable(false)" class="inventory-v10-choice" :class="{ 'active': !form.properties.inventory.equippable }">Não</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[12px] font-black text-[#53150f]">Natureza do item</span>
                                    <div class="mt-1.5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                                        <template x-for="(label, key) in natureLabels" :key="key">
                                            <button
                                                type="button"
                                                @click="setNature(key)"
                                                class="inventory-v10-choice"
                                                :class="{ 'active': form.properties.nature === key }"
                                                x-text="label"
                                            ></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_220px]">
                                    <label>
                                        <span class="text-[12px] font-black text-[#53150f]">Raridade</span>
                                        <select x-model="form.rarity" class="inventory-v10-select mt-1.5">
                                            <template x-for="(label, key) in rarityLabels" :key="key">
                                                <option :value="key" x-text="label"></option>
                                            </template>
                                        </select>
                                    </label>

                                    <div x-show="form.properties.nature === 'wonderful'" x-cloak>
                                        <span class="text-[12px] font-black text-[#53150f]">Sintonização</span>
                                        <button
                                            type="button"
                                            @click="form.requires_attunement = !form.requires_attunement; if (!form.requires_attunement) form.attuned = false"
                                            class="mt-1.5 flex min-h-[44px] w-full items-center justify-between gap-3 rounded-lg border px-3"
                                            :class="form.requires_attunement ? 'border-[#6b1d14]/45 bg-[#6b1d14]/[0.06]' : 'border-[#cdbb9f]/75 bg-white/70'"
                                        >
                                            <span class="text-[11px] font-black text-[#53150f]">Sintonizável</span>
                                            <span class="relative h-5 w-9 rounded-full" :class="form.requires_attunement ? 'bg-[#6b1d14]' : 'bg-[#cdbb9f]'">
                                                <span class="absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-all" :class="form.requires_attunement ? 'left-[18px]' : 'left-0.5'"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mt-6 border-t border-[#d8c7ab]/60 pt-5">
                        <h4 class="font-serif text-[18px] font-black text-[#53150f]">Aparência e Descrição</h4>
                        <p class="mt-0.5 text-[11px] text-[#8c6239]/70">O texto principal que será mostrado na ficha do item.</p>
                        <textarea
                            rows="6"
                            x-model="form.description"
                            placeholder="Descreva a aparência, origem, sensação e funcionamento geral do item..."
                            class="inventory-v10-textarea mt-3"
                        ></textarea>
                    </section>

                    {{-- =====================================================
                         PROPRIEDADES MODULARES
                    ====================================================== --}}
                    <section class="mt-7 border-t border-[#d8c7ab]/60 pt-5">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h4 class="font-serif text-[18px] font-black text-[#53150f]">Propriedades do Item</h4>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/72">
                                    Adicione apenas o que o sistema precisa entender. O restante continua livre na descrição e nas habilidades.
                                </p>
                            </div>

                            <div class="relative">
                                <button
                                    type="button"
                                    @click="modulePickerOpen = !modulePickerOpen"
                                    class="inline-flex min-h-[40px] items-center gap-2 rounded-lg border border-[#6b1d14]/30 bg-[#6b1d14]/[0.05] px-3.5 text-[10px] font-black uppercase tracking-[0.06em] text-[#6b1d14] transition hover:bg-[#6b1d14]/[0.10]"
                                >
                                    <span class="text-[15px] leading-none">+</span>
                                    Adicionar Propriedade
                                </button>

                                <div
                                    x-show="modulePickerOpen"
                                    x-cloak
                                    @click.outside="modulePickerOpen = false"
                                    class="absolute right-0 top-[46px] z-40 w-[min(88vw,330px)] overflow-hidden rounded-xl border border-[#cdbb9f] bg-[#faf8f2] p-2 shadow-xl"
                                >
                                    <button
                                        x-show="!hasModule('weapon')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('weapon')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Arma</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Dano, atributo, propriedades e maestrias.</span>
                                        </span>
                                    </button>

                                    <button
                                        x-show="!hasModule('armor')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('armor')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Armadura</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Categoria, tipo e Classe de Armadura.</span>
                                        </span>
                                    </button>

                                    <button
                                        x-show="!hasModule('shield')"
                                        x-cloak
                                        type="button"
                                        @click="addModule('shield')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">◆</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Escudo</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Bônus de CA e bônus adicional.</span>
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="addModule('custom')"
                                        class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-[#efe9dc]/70"
                                    >
                                        <span class="mt-0.5 font-serif text-[16px] text-[#6b1d14]">+</span>
                                        <span>
                                            <strong class="block text-[12px] text-[#53150f]">Custom</strong>
                                            <span class="mt-0.5 block text-[10px] leading-relaxed text-[#8c6239]">Uma propriedade livre com título, valor e explicação.</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>


                        {{-- ARMA --}}
                        <div
                            x-show="hasModule('weapon')"
                            x-cloak
                            class="inventory-v10-module inventory-v31-weapon-module mt-4"
                        >
                            <div class="flex items-start justify-between gap-4 border-b border-[#d8c7ab]/55 bg-[#efe9dc]/42 px-4 py-3">
                                <div>
                                    <h5 class="font-serif text-[17px] font-black text-[#53150f]">Arma</h5>
                                    <p class="mt-0.5 text-[10px] text-[#8c6239]/72">Configuração mecânica curta para ataques e exibição na ficha.</p>
                                </div>
                                <button type="button" @click="removeModule('weapon')" class="rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                            </div>

                            <div class="space-y-5 p-4">
                                <div>
                                    <span class="text-[11px] font-black text-[#53150f]">Tipo de Arma</span>
                                    <div class="mt-2 grid grid-cols-3 gap-2 sm:max-w-lg">
                                        <button type="button" @click="form.properties.weapon.category = 'simple'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'simple' }">Simples</button>
                                        <button type="button" @click="form.properties.weapon.category = 'martial'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'martial' }">Marcial</button>
                                        <button type="button" @click="form.properties.weapon.category = 'custom'" class="inventory-v10-choice" :class="{ 'active': form.properties.weapon.category === 'custom' }">Custom</button>
                                    </div>

                                    <input
                                        x-show="form.properties.weapon.category === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="120"
                                        x-model="form.properties.weapon.category_custom"
                                        placeholder="Ex.: Arma Exótica"
                                        class="inventory-v10-input mt-2 sm:max-w-lg"
                                    >
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_150px]">
                                    <label>
                                        <span class="text-[11px] font-black text-[#53150f]">Modelo / Tipo</span>
                                        <input type="text" maxlength="120" x-model="form.properties.weapon.weapon_type" placeholder="Ex.: Faca, Machado, Cajado, Foice Grande..." class="inventory-v10-input mt-1.5">
                                    </label>

                                    <label>
                                        <span class="text-[11px] font-black text-[#53150f]">Bônus</span>
                                        <input type="number" min="-99" max="99" x-model.number="form.properties.weapon.magic_bonus" class="inventory-v10-input mt-1.5 text-center">
                                    </label>
                                </div>

                                <div>
                                    <div class="flex items-end justify-between gap-3">
                                        <div>
                                            <span class="text-[11px] font-black text-[#53150f]">Propriedades da Arma</span>
                                            <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Passe o mouse sobre cada propriedade para ler a regra completa.</p>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <template x-for="definition in weaponTraitDefinitions" :key="definition.key">
                                            <button
                                                type="button"
                                                @click="toggleTrait(definition)"
                                                @mouseenter="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @mouseleave="hideEditorTooltip()"
                                                @focus="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @blur="hideEditorTooltip()"
                                                class="inventory-v10-chip"
                                                :class="{ 'active': traitSelected(definition.key) }"
                                            >
                                                <span x-text="definition.name"></span>
                                            </button>
                                        </template>

                                        <button type="button" @click="addCustomTrait()" class="inventory-v10-chip">+ Custom</button>
                                    </div>

                                    <div class="mt-3 space-y-2">
                                        <template x-for="(trait, index) in form.properties.weapon.traits" :key="trait.key">
                                            <div x-show="trait.custom" x-cloak class="grid grid-cols-1 gap-2 rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/24 p-3 sm:grid-cols-[180px_minmax(0,1fr)_auto]">
                                                <input type="text" maxlength="120" x-model="trait.name" placeholder="Nome da propriedade" class="inventory-v10-input">
                                                <input type="text" maxlength="2000" x-model="trait.description" placeholder="O que esta propriedade faz?" class="inventory-v10-input">
                                                <button type="button" @click="removeTrait(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[11px] font-black text-[#53150f]">Dano</span>
                                    <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-[86px_105px_minmax(0,1fr)_minmax(0,1fr)]">
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Dados</span>
                                            <input type="number" min="1" max="99" x-model.number="form.properties.weapon.damage.count" class="inventory-v10-input mt-1 text-center">
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Dado</span>
                                            <input type="text" maxlength="20" x-model="form.properties.weapon.damage.die" placeholder="d4" class="inventory-v10-input mt-1 text-center">
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Atributo</span>
                                            <select x-model="form.properties.weapon.damage.ability" class="inventory-v10-select mt-1">
                                                <template x-for="(label, key) in abilityLabels" :key="key">
                                                    <option :value="key" x-text="label"></option>
                                                </template>
                                            </select>
                                        </label>
                                        <label>
                                            <span class="text-[9px] font-black uppercase text-[#8c6239]">Tipo de Dano</span>
                                            <input type="text" maxlength="80" x-model="form.properties.weapon.damage.type" placeholder="Ex.: Perfurante" class="inventory-v10-input mt-1">
                                        </label>
                                    </div>

                                    <input
                                        x-show="form.properties.weapon.damage.ability === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="80"
                                        x-model="form.properties.weapon.damage.ability_custom"
                                        placeholder="Atributo ou modificador custom"
                                        class="inventory-v10-input mt-2 sm:max-w-md"
                                    >
                                </div>

                                <div>
                                    <div class="flex items-end justify-between gap-3 border-b border-[#d8c7ab]/45 pb-2">
                                        <div>
                                            <span class="text-[11px] font-black text-[#53150f]">Danos Extras</span>
                                            <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Ex.: 2d6 Elétrico além do dano principal.</p>
                                        </div>
                                        <button type="button" @click="addExtraDamage()" class="text-[9px] font-black uppercase text-[#6b1d14] hover:underline">+ Dano Extra</button>
                                    </div>

                                    <div class="mt-2 space-y-2">
                                        <template x-for="(part, index) in form.properties.weapon.extra_damage" :key="'extra-damage-' + index">
                                            <div class="grid grid-cols-[72px_96px_minmax(0,1fr)_auto] gap-2">
                                                <input type="number" min="1" max="99" x-model.number="part.count" class="inventory-v10-input text-center">
                                                <input type="text" maxlength="20" x-model="part.die" placeholder="d6" class="inventory-v10-input text-center">
                                                <input type="text" maxlength="80" x-model="part.type" placeholder="Tipo de dano" class="inventory-v10-input">
                                                <button type="button" @click="removeExtraDamage(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <div>
                                        <span class="text-[11px] font-black text-[#53150f]">Maestrias</span>
                                        <p class="mt-0.5 text-[10px] text-[#8c6239]/70">Pode selecionar mais de uma. Passe o mouse sobre cada maestria para ler a regra completa.</p>
                                    </div>

                                    <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-4">
                                        <template x-for="definition in masteryDefinitions" :key="definition.key">
                                            <button
                                                type="button"
                                                @click="toggleMastery(definition)"
                                                @mouseenter="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @mouseleave="hideEditorTooltip()"
                                                @focus="
                                                    showEditorTooltip(
                                                        $event,
                                                        definition.name,
                                                        definition.description
                                                    )
                                                "
                                                @blur="hideEditorTooltip()"
                                                class="rounded-xl border px-3 py-2.5 text-left transition"
                                                :class="masterySelected(definition.key) ? 'border-[#6b1d14]/45 bg-[#6b1d14]/[0.07]' : 'border-[#d8c7ab]/65 bg-white/55 hover:bg-[#efe9dc]/55'"
                                            >
                                                <strong class="block text-[11px] text-[#53150f]" x-text="definition.name"></strong>
                                                <span class="mt-0.5 block line-clamp-2 text-[9px] leading-relaxed text-[#8c6239]" x-text="definition.description"></span>
                                            </button>
                                        </template>
                                    </div>

                                    <button type="button" @click="addCustomMastery()" class="mt-2 text-[9px] font-black uppercase text-[#6b1d14] hover:underline">+ Maestria Custom</button>

                                    <div class="mt-3 space-y-2">
                                        <template x-for="(mastery, index) in form.properties.weapon.masteries" :key="mastery.key">
                                            <div x-show="mastery.custom" x-cloak class="rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/24 p-3">
                                                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2">
                                                    <input type="text" maxlength="120" x-model="mastery.name" placeholder="Nome da maestria" class="inventory-v10-input">
                                                    <button type="button" @click="removeMastery(index)" class="rounded-md px-2 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                                </div>
                                                <textarea rows="2" maxlength="5000" x-model="mastery.description" placeholder="O que esta maestria faz?" class="inventory-v10-textarea mt-2"></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- ARMADURA --}}
                        <div
                            x-show="hasModule('armor')"
                            x-cloak
                            class="
                                inventory-v10-module
                                inventory-v34-defense-module
                                mt-4
                                overflow-hidden
                            "
                        >
                            {{-- Cabeçalho --}}
                            <div class="inventory-v34-defense-head">
                                <div class="inventory-v34-defense-head-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M8.2 5.2 12 3l3.8 2.2 3.2 1.4-1.3 4.1v8.1H6.3v-8.1L5 6.6l3.2-1.4Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M8.2 5.2 9.4 8h5.2l1.2-2.8"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="font-serif text-[18px] font-black text-[#53150f]">
                                            Armadura
                                        </h5>

                                        <span class="inventory-v34-auto-equip">
                                            Equipável
                                        </span>
                                    </div>

                                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/78">
                                        Defina aqui a fórmula da armadura. Quando o item estiver equipado,
                                        a Classe de Armadura poderá usar estes dados diretamente.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="removeModule('armor')"
                                    class="inventory-v34-remove"
                                >
                                    Remover
                                </button>
                            </div>

                            <div class="space-y-5 p-4 sm:p-5">
                                {{-- Categoria --}}
                                <section>
                                    <div class="inventory-v34-section-label">
                                        <span>01</span>

                                        <div>
                                            <strong>Categoria da Armadura</strong>
                                            <p>
                                                A categoria já configura uma regra defensiva padrão.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2 lg:grid-cols-4">
                                        <button
                                            type="button"
                                            @click="setArmorCategory('light')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'light'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                L
                                            </span>

                                            <strong>Leve</strong>

                                            <small>
                                                Destreza completa
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('medium')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'medium'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                M
                                            </span>

                                            <strong>Média</strong>

                                            <small>
                                                DES até +2
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('heavy')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'heavy'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                P
                                            </span>

                                            <strong>Pesada</strong>

                                            <small>
                                                Não soma DES
                                            </small>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorCategory('custom')"
                                            class="inventory-v34-armor-category"
                                            :class="{
                                                'active':
                                                    form.properties.armor.category === 'custom'
                                            }"
                                        >
                                            <span class="inventory-v34-category-mark">
                                                ◆
                                            </span>

                                            <strong>Especial</strong>

                                            <small>
                                                Fórmula livre
                                            </small>
                                        </button>
                                    </div>

                                    <input
                                        x-show="form.properties.armor.category === 'custom'"
                                        x-cloak
                                        type="text"
                                        maxlength="120"
                                        x-model="form.properties.armor.category_custom"
                                        placeholder="Ex.: Armadura Astral"
                                        class="inventory-v10-input mt-2"
                                    >
                                </section>

                                {{-- Identidade / números --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>02</span>

                                        <div>
                                            <strong>Defesa do Item</strong>
                                            <p>
                                                Base da armadura e bônus mágico próprio do equipamento.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            mt-3
                                            grid
                                            grid-cols-1
                                            gap-3
                                            sm:grid-cols-[minmax(0,1fr)_128px_128px]
                                        "
                                    >
                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Tipo / Modelo
                                            </span>

                                            <input
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.armor.armor_type"
                                                placeholder="Ex.: Meia Armadura"
                                                class="inventory-v10-input mt-1.5"
                                            >
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                CA Base
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="99"
                                                    x-model.number="form.properties.armor.base_ac"
                                                >
                                            </div>
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Mágico
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.armor.magic_bonus"
                                                >
                                            </div>
                                        </label>
                                    </div>
                                </section>

                                {{-- Destreza --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>03</span>

                                        <div>
                                            <strong>Destreza na CA</strong>
                                            <p>
                                                Leve usa tudo, Média normalmente limita e Pesada não usa.
                                                Armaduras especiais podem quebrar essa regra.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('none')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'none'
                                            }"
                                        >
                                            <strong>Sem DES</strong>
                                            <span>Não adiciona Destreza.</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('full')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'full'
                                            }"
                                        >
                                            <strong>DES completa</strong>
                                            <span>Usa o modificador inteiro.</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityMode('capped')"
                                            class="inventory-v34-rule-choice"
                                            :class="{
                                                'active':
                                                    form.properties.armor.dexterity_mode === 'capped'
                                            }"
                                        >
                                            <strong>DES limitada</strong>
                                            <span>Usa somente até um limite.</span>
                                        </button>
                                    </div>

                                    <div
                                        x-show="form.properties.armor.dexterity_mode === 'capped'"
                                        x-cloak
                                        class="inventory-v34-cap-row"
                                    >
                                        <span class="inventory-v34-field-label">
                                            Limite
                                        </span>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityCap(2)"
                                            class="inventory-v34-cap-button"
                                            :class="{
                                                'active':
                                                    parseInt(form.properties.armor.dexterity_cap) === 2
                                            }"
                                        >
                                            +2
                                        </button>

                                        <button
                                            type="button"
                                            @click="setArmorDexterityCap(3)"
                                            class="inventory-v34-cap-button"
                                            :class="{
                                                'active':
                                                    parseInt(form.properties.armor.dexterity_cap) === 3
                                            }"
                                        >
                                            +3
                                        </button>

                                        <label class="inventory-v34-cap-custom">
                                            <span>Outro</span>

                                            <input
                                                type="number"
                                                min="0"
                                                max="99"
                                                x-model.number="form.properties.armor.dexterity_cap"
                                            >
                                        </label>
                                    </div>
                                </section>

                                {{-- Atributos extras --}}
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>04</span>

                                        <div>
                                            <strong>Atributos Adicionais</strong>
                                            <p>
                                                Opcional. Ideal para armaduras mágicas ou especiais que
                                                também somam outro modificador à CA.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid grid-cols-5 gap-2">
                                        <template
                                            x-for="ability in [
                                                'strength',
                                                'constitution',
                                                'intelligence',
                                                'wisdom',
                                                'charisma'
                                            ]"
                                            :key="'armor-extra-ability-' + ability"
                                        >
                                            <button
                                                type="button"
                                                @click="toggleArmorAbilityModifier(ability)"
                                                class="inventory-v34-ability"
                                                :class="{
                                                    'active':
                                                        form.properties.armor.ability_modifiers.includes(
                                                            ability
                                                        )
                                                }"
                                            >
                                                <strong
                                                    x-text="
                                                        {
                                                            strength: 'FOR',
                                                            constitution: 'CON',
                                                            intelligence: 'INT',
                                                            wisdom: 'SAB',
                                                            charisma: 'CAR'
                                                        }[ability]
                                                    "
                                                ></strong>

                                                <span
                                                    x-text="
                                                        {
                                                            strength: 'Força',
                                                            constitution: 'Constituição',
                                                            intelligence: 'Inteligência',
                                                            wisdom: 'Sabedoria',
                                                            charisma: 'Carisma'
                                                        }[ability]
                                                    "
                                                ></span>
                                            </button>
                                        </template>
                                    </div>
                                </section>

                                {{-- Fórmula --}}
                                <section class="inventory-v34-formula-card">
                                    <div class="inventory-v34-formula-icon">
                                        Σ
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <span>
                                            Fórmula da Armadura
                                        </span>

                                        <strong
                                            x-text="
                                                armorFormulaText(
                                                    form.properties.armor
                                                )
                                            "
                                        ></strong>

                                        <small>
                                            Esta fórmula pertence ao item e poderá ser usada
                                            automaticamente quando ele estiver equipado.
                                        </small>
                                    </div>
                                </section>
                            </div>
                        </div>


                        {{-- ESCUDO --}}
                        <div
                            x-show="hasModule('shield')"
                            x-cloak
                            class="
                                inventory-v10-module
                                inventory-v34-defense-module
                                mt-4
                                overflow-hidden
                            "
                        >
                            {{-- Cabeçalho --}}
                            <div class="inventory-v34-defense-head">
                                <div class="inventory-v34-defense-head-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h5 class="font-serif text-[18px] font-black text-[#53150f]">
                                            Escudo
                                        </h5>

                                        <span class="inventory-v34-auto-equip">
                                            Equipável
                                        </span>
                                    </div>

                                    <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/78">
                                        O escudo armazena o bônus base e o bônus mágico separadamente.
                                        Quando equipado, ambos podem entrar na CA.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="removeModule('shield')"
                                    class="inventory-v34-remove"
                                >
                                    Remover
                                </button>
                            </div>

                            <div class="space-y-5 p-4 sm:p-5">
                                <section class="inventory-v34-rule-panel">
                                    <div class="inventory-v34-section-label">
                                        <span>01</span>

                                        <div>
                                            <strong>Identidade do Escudo</strong>
                                            <p>
                                                Nome mecânico do equipamento e seus bônus defensivos.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="
                                            mt-3
                                            grid
                                            grid-cols-1
                                            gap-3
                                            sm:grid-cols-[minmax(0,1fr)_132px_132px]
                                        "
                                    >
                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Nome / Tipo
                                            </span>

                                            <input
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.shield.label"
                                                placeholder="Ex.: Escudo"
                                                class="inventory-v10-input mt-1.5"
                                            >
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Base
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="99"
                                                    x-model.number="form.properties.shield.ac_bonus"
                                                >
                                            </div>
                                        </label>

                                        <label>
                                            <span class="inventory-v34-field-label">
                                                Bônus Mágico
                                            </span>

                                            <div class="inventory-v34-number-field mt-1.5">
                                                <span>+</span>

                                                <input
                                                    type="number"
                                                    min="-99"
                                                    max="99"
                                                    x-model.number="form.properties.shield.magic_bonus"
                                                >
                                            </div>
                                        </label>
                                    </div>
                                </section>

                                <section class="inventory-v34-shield-total">
                                    <div>
                                        <span>
                                            Base
                                        </span>

                                        <strong
                                            x-text="
                                                '+'
                                                + (
                                                    parseInt(
                                                        form.properties.shield.ac_bonus
                                                    ) || 0
                                                )
                                            "
                                        ></strong>
                                    </div>

                                    <b>+</b>

                                    <div>
                                        <span>
                                            Mágico
                                        </span>

                                        <strong
                                            x-text="
                                                (
                                                    parseInt(
                                                        form.properties.shield.magic_bonus
                                                    ) || 0
                                                ) >= 0
                                                    ? '+'
                                                        + (
                                                            parseInt(
                                                                form.properties.shield.magic_bonus
                                                            ) || 0
                                                        )
                                                    : (
                                                        parseInt(
                                                            form.properties.shield.magic_bonus
                                                        ) || 0
                                                    )
                                            "
                                        ></strong>
                                    </div>

                                    <b>=</b>

                                    <div class="is-total">
                                        <span>
                                            Bônus de CA
                                        </span>

                                        <strong
                                            x-text="
                                                (
                                                    shieldTotalBonus(
                                                        form.properties.shield
                                                    ) >= 0
                                                        ? '+'
                                                        : ''
                                                )
                                                + shieldTotalBonus(
                                                    form.properties.shield
                                                )
                                            "
                                        ></strong>
                                    </div>
                                </section>

                                <div class="inventory-v34-equipment-note">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"
                                            stroke-width="1.55"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                    <span>
                                        Este módulo torna o item equipável automaticamente.
                                        A ficha poderá reconhecer o escudo quando ele estiver em uso.
                                    </span>
                                </div>
                            </div>
                        </div>


                        {{-- PROPRIEDADES CUSTOM --}}
                        <div class="mt-4 space-y-3">
                            <template x-for="(property, index) in form.properties.custom_properties" :key="'custom-property-' + index">
                                <div class="inventory-v10-module overflow-hidden">
                                    <div class="flex items-center justify-between border-b border-[#d8c7ab]/50 bg-[#efe9dc]/34 px-4 py-2.5">
                                        <strong class="font-serif text-[15px] text-[#53150f]">Propriedade Custom</strong>
                                        <button type="button" @click="removeCustomProperty(index)" class="rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50">Remover</button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
                                        <label>
                                            <span class="text-[11px] font-black text-[#53150f]">Título</span>
                                            <input type="text" maxlength="150" x-model="property.title" placeholder="Ex.: Defesa" class="inventory-v10-input mt-1.5">
                                        </label>
                                        <label>
                                            <span class="text-[11px] font-black text-[#53150f]">Valor</span>
                                            <input type="text" maxlength="250" x-model="property.value" placeholder="Ex.: +2" class="inventory-v10-input mt-1.5">
                                        </label>
                                        <label class="sm:col-span-2">
                                            <span class="text-[11px] font-black text-[#53150f]">Explicação</span>
                                            <textarea rows="2" maxlength="5000" x-model="property.description" placeholder="Explique esta propriedade, se necessário..." class="inventory-v10-textarea mt-1.5"></textarea>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>


                    {{-- =====================================================
                         HABILIDADES
                    ====================================================== --}}
                    <section class="mt-7 border-t border-[#d8c7ab]/60 pt-5">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h4 class="font-serif text-[19px] font-black text-[#53150f]">Habilidades</h4>
                                <p class="mt-0.5 text-[11px] leading-relaxed text-[#8c6239]/72">
                                    Efeitos passivos e habilidades limitadas ficam aqui. Cada habilidade pode ter seu próprio Rastreador.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addFeature()"
                                class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-[#6b1d14] px-3.5 text-[10px] font-black uppercase tracking-[0.06em] text-[#f4f1e8] hover:bg-[#53150f]"
                            >
                                <span class="text-[15px] leading-none">+</span>
                                Adicionar Habilidade
                            </button>
                        </div>

                        <div class="mt-3 space-y-3">
                            <template x-for="(feature, index) in form.properties.features" :key="'feature-editor-' + index">
                                <article class="inventory-v10-feature-card overflow-hidden">
                                    <div class="flex items-center gap-2 border-b border-[#d8c7ab]/50 bg-[#efe9dc]/32 px-3 py-2">
                                        <button
                                            type="button"
                                            @click="toggleFeatureEditor(feature)"
                                            class="group flex min-w-0 flex-1 items-center gap-3 rounded-lg px-1 py-1 text-left transition hover:bg-[#eadbc8]/35"
                                            :aria-expanded="feature.editor_open"
                                        >
                                            <span
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-[#cdbb9f]/70 bg-[#faf8f2] font-serif text-[12px] font-black text-[#8c6239]"
                                                x-text="String(index + 1).padStart(2, '0')"
                                            ></span>

                                            <span class="min-w-0 flex-1">
                                                <strong
                                                    class="block truncate font-serif text-[15px] font-black text-[#53150f]"
                                                    x-text="String(feature.title ?? '').trim() || 'Habilidade'"
                                                ></strong>

                                                <span
                                                    x-show="!feature.editor_open && String(feature.description ?? '').trim()"
                                                    x-cloak
                                                    class="mt-0.5 block truncate text-[9px] text-[#8c6239]/70"
                                                    x-html="formatItemRichText(feature.description)"
                                                ></span>
                                            </span>

                                            <svg
                                                class="h-4 w-4 shrink-0 text-[#8c6239] transition-transform duration-200"
                                                :class="{ 'rotate-180': feature.editor_open }"
                                                viewBox="0 0 20 20"
                                                fill="none"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M5 7.5 10 12.5 15 7.5"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click.stop="removeFeature(index)"
                                            class="shrink-0 rounded-md px-2 py-1 text-[9px] font-black uppercase text-red-700 hover:bg-red-50"
                                        >
                                            Remover
                                        </button>
                                    </div>

                                    <div
                                        x-show="feature.editor_open"
                                        x-cloak
                                        x-transition.opacity.duration.120ms
                                        class="space-y-4 p-4"
                                    >
                                        <label class="block">
                                            <span class="text-[11px] font-black text-[#53150f]">Nome da Habilidade</span>
                                            <input type="text" maxlength="150" x-model="feature.title" placeholder="Ex.: Luz da Ressurreição" class="inventory-v10-input mt-1.5">
                                        </label>

                                        <label class="block">
                                            <span class="text-[11px] font-black text-[#53150f]">Descrição</span>
                                            <textarea rows="4" maxlength="15000" x-model="feature.description" placeholder="Descreva o efeito da habilidade..." class="inventory-v10-textarea mt-1.5"></textarea>
                                        </label>

                                        <div class="border-t border-[#d8c7ab]/48 pt-4">
                                            <span class="text-[11px] font-black text-[#53150f]">Uso</span>
                                            <div class="mt-2 grid grid-cols-2 gap-2 sm:max-w-md">
                                                <button
                                                    type="button"
                                                    @click="setFeatureUsage(feature, false)"
                                                    class="inventory-v10-choice"
                                                    :class="{ 'active': !feature.usage.enabled }"
                                                >
                                                    Passiva
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="setFeatureUsage(feature, true)"
                                                    class="inventory-v10-choice"
                                                    :class="{ 'active': feature.usage.enabled }"
                                                >
                                                    Limitada
                                                </button>
                                            </div>

                                            <div
                                                x-show="feature.usage.enabled"
                                                x-cloak
                                                class="
                                                    inventory-v31-tracker-editor
                                                    mt-3
                                                    rounded-xl
                                                    border
                                                    border-[#d8c7ab]/60
                                                    bg-[#efe9dc]/24
                                                    p-3.5
                                                "
                                            >
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <div>
                                                        <strong class="block text-[11px] text-[#53150f]">
                                                            Rastreador da Habilidade
                                                        </strong>

                                                        <span class="mt-0.5 block text-[10px] text-[#8c6239]/70">
                                                            Escolha se as cargas são consumidas ou acumuladas.
                                                        </span>
                                                    </div>

                                                    <div class="inventory-v10-tracker">
                                                        <button
                                                            type="button"
                                                            @click="
                                                                feature.usage.current =
                                                                    Math.max(
                                                                        0,
                                                                        feature.usage.current - 1
                                                                    )
                                                            "
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Reduzir carga acumulada'
                                                                    : 'Gastar carga'
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            x-text="
                                                                feature.usage.current
                                                                + '/'
                                                                + feature.usage.max
                                                            "
                                                        ></strong>

                                                        <button
                                                            type="button"
                                                            @click="
                                                                feature.usage.current =
                                                                    Math.min(
                                                                        feature.usage.max,
                                                                        feature.usage.current + 1
                                                                    )
                                                            "
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Acumular carga'
                                                                    : 'Recuperar carga'
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                        Comportamento
                                                    </span>

                                                    <div class="mt-1.5 grid grid-cols-2 gap-2">
                                                        <button
                                                            type="button"
                                                            @click="
                                                                setFeatureCounterMode(
                                                                    feature,
                                                                    'spend'
                                                                )
                                                            "
                                                            :disabled="feature.usage.recovery === 'single_use'"
                                                            class="inventory-v31-counter-choice disabled:cursor-not-allowed disabled:opacity-45"
                                                            :class="{
                                                                'active':
                                                                    feature.usage.counter_mode === 'spend'
                                                            }"
                                                        >
                                                            <strong>Consome cargas</strong>
                                                            <span>Começa no máximo e vai diminuindo.</span>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            @click="
                                                                setFeatureCounterMode(
                                                                    feature,
                                                                    'build'
                                                                )
                                                            "
                                                            :disabled="feature.usage.recovery === 'single_use'"
                                                            class="inventory-v31-counter-choice disabled:cursor-not-allowed disabled:opacity-45"
                                                            :class="{
                                                                'active':
                                                                    feature.usage.counter_mode === 'build'
                                                            }"
                                                        >
                                                            <strong>Acumula cargas</strong>
                                                            <span>Começa em 0 e sobe a cada uso.</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div
                                                    class="
                                                        mt-3
                                                        grid
                                                        grid-cols-2
                                                        gap-3
                                                        md:grid-cols-[120px_120px_minmax(0,1fr)]
                                                    "
                                                >
                                                    {{-- Máximo vem primeiro por ser a regra-base do tracker. --}}
                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Máximo
                                                        </span>

                                                        <input
                                                            type="number"
                                                            min="1"
                                                            x-model.number="feature.usage.max"
                                                            @change="clampFeatureUsage(feature)"
                                                            class="inventory-v10-input mt-1 text-center"
                                                        >
                                                    </label>

                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Atual
                                                        </span>

                                                        <input
                                                            type="number"
                                                            min="0"
                                                            :max="feature.usage.max"
                                                            x-model.number="feature.usage.current"
                                                            @change="clampFeatureUsage(feature)"
                                                            class="inventory-v10-input mt-1 text-center"
                                                        >
                                                    </label>

                                                    <label>
                                                        <span class="text-[9px] font-black uppercase text-[#8c6239]">
                                                            Recupera
                                                        </span>

                                                        <select
                                                            x-model="feature.usage.recovery"
                                                            @change="handleFeatureRecoveryChange(feature)"
                                                            class="inventory-v10-select mt-1"
                                                        >
                                                            <template
                                                                x-for="(label, key) in recoveryLabels"
                                                                :key="key"
                                                            >
                                                                <option
                                                                    :value="key"
                                                                    x-text="label"
                                                                ></option>
                                                            </template>
                                                        </select>
                                                    </label>
                                                </div>

                                                <p
                                                    x-show="feature.usage.recovery === 'single_use'"
                                                    x-cloak
                                                    class="inventory-v43-single-use-note"
                                                >
                                                    Uso Único não recupera cargas. Defina o <strong>Máximo</strong> e o <strong>Atual</strong> normalmente; cada confirmação gasta 1 carga permanentemente. O item só fica <strong>Usado</strong> quando chegar a 0.
                                                </p>

                                                <input
                                                    x-show="feature.usage.recovery === 'custom'"
                                                    x-cloak
                                                    type="text"
                                                    maxlength="120"
                                                    x-model="feature.usage.recovery_custom"
                                                    placeholder="Ex.: Ao terminar uma cena"
                                                    class="inventory-v10-input mt-3"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </template>

                            <div
                                x-show="form.properties.features.length === 0"
                                x-cloak
                                class="rounded-xl border border-dashed border-[#cdbb9f]/75 bg-[#efe9dc]/20 px-4 py-6 text-center"
                            >
                                <p class="font-serif text-[15px] font-black text-[#53150f]">Nenhuma habilidade adicionada</p>
                                <p class="mt-1 text-[10px] text-[#8c6239]/72">Itens simples podem ficar assim. Adicione habilidades apenas quando houver efeitos próprios.</p>
                            </div>
                        </div>
                    </section>


                    <div x-show="editingId && form.is_cursed" x-cloak class="mt-5 border-l-2 border-red-700 bg-red-50/60 px-4 py-3">
                        <p class="text-[9px] font-black uppercase tracking-[0.08em] text-red-700">Maldição Revelada</p>
                        <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-red-900" x-text="form.curse_description"></p>
                    </div>

                    <div x-show="saveError" x-cloak class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[11px] font-bold text-red-700" x-text="saveError"></div>
                </div>

                <div
                    class="
                        inventory-v35-editor-footer

                        flex
                        shrink-0
                        items-center
                        gap-2

                        border-t

                        px-5
                        py-3.5
                    "
                >
                    <button
                        x-show="editingId"
                        x-cloak
                        type="button"
                        @click="deleteConfirmOpen = true"
                        :disabled="saving"
                        class="rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-[0.06em] text-red-700 hover:bg-red-50 disabled:opacity-50"
                    >
                        Excluir
                    </button>

                    <button type="button" @click="closeEditor()" :disabled="saving" class="ml-auto rounded-lg px-3 py-2 text-[9px] font-black uppercase tracking-[0.06em] text-[#8c6239] hover:bg-[#e8dfd1] disabled:opacity-50">Cancelar</button>

                    <button
                        type="button"
                        @click="saveItem()"
                        :disabled="saving"
                        class="rounded-lg bg-[#6b1d14] px-5 py-2.5 text-[9px] font-black uppercase tracking-[0.07em] text-[#f4f1e8] hover:bg-[#53150f] disabled:opacity-50"
                    >
                        <span x-show="!saving">Salvar Item</span>
                        <span x-show="saving" x-cloak>Salvando…</span>
                    </button>
                </div>

                <div
                    x-show="deleteConfirmOpen"
                    x-cloak
                    class="
                        inventory-v35-delete-layer
                        absolute
                        inset-0
                        z-50

                        flex
                        items-center
                        justify-center

                        p-4
                    "
                >
                    <div
                        class="
                            inventory-v35-delete-modal

                            w-full
                            max-w-sm
                            overflow-hidden

                            rounded-2xl
                            border

                            shadow-2xl
                        "
                    >
                        <div class="border-b border-[#d8c7ab]/60 bg-[#efe9dc]/55 px-4 py-3">
                            <h4 class="font-serif text-[18px] font-black text-[#53150f]">Excluir item?</h4>
                        </div>
                        <p class="px-4 py-4 text-[12px] leading-relaxed text-[#6b5548]"><strong x-text="form?.name ?? ''"></strong> será removido permanentemente do inventário.</p>
                        <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/60 px-4 py-3">
                            <button type="button" @click="deleteConfirmOpen = false" class="rounded-lg px-3 py-2 text-[9px] font-black uppercase text-[#8c6239]">Voltar</button>
                            <button type="button" @click="deleteItem()" :disabled="saving" class="rounded-lg bg-red-700 px-3 py-2 text-[9px] font-black uppercase text-white disabled:opacity-50">Excluir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- TOOLTIP FLUTUANTE DO EDITOR --}}
    <template x-teleport="body">
        <div
            x-show="editorTooltip.open"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="
                character-sheet-theme
                inventory-v32-floating-tooltip
                inventory-v35-floating-tooltip
            "
            :style="`
                left: ${editorTooltip.left}px;
                top: ${editorTooltip.top}px;
            `"
            role="tooltip"
        >
            <strong
                x-show="editorTooltip.title"
                x-cloak
                x-text="editorTooltip.title"
            ></strong>

            <p x-text="editorTooltip.text"></p>
        </div>
    </template>

