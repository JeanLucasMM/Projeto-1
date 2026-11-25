    {{-- VISUALIZAÇÃO COMPLETA --}}
    <template x-teleport="body">
        <div
            x-show="detailOpen"
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
            aria-label="Detalhes do item"

            class="
                character-sheet-theme
                character-theme-modal-layer
                inventory-v35-modal-layer

                fixed
                inset-0
                z-[230]

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
                @click="closeDetail()"
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
                    inventory-v35-detail-modal
                    inventory-v10-detail-paper
                    inventory-v17-detail-modal
                    inventory-v50-detail-modal

                    relative
                    z-10

                    flex
                    max-h-[92dvh]
                    w-full
                    max-w-[940px]
                    flex-col
                    overflow-hidden

                    rounded-2xl
                    border

                    shadow-2xl
                "
                :class="{ 'inventory-v50-detail-artifact': isArtifact(detailItem), 'inventory-v50-detail-vestige': isVestige(detailItem), 'inventory-v51-ascending-detail': !!vestigeGlowingItemIds[String(detailItem?.id)] }"
                :data-nature="detailItem?.type || 'mundane'"
                :data-rarity="detailItem?.rarity || 'common'"
            >
                <div class="inventory-v17-detail-topbar flex shrink-0 items-center gap-2 border-b border-[#d8c7ab]/55 bg-[#efe9dc]/28 px-4 py-2.5">
                    <span class="mr-auto text-[9px] font-black uppercase tracking-[0.11em] text-[#8c6239]/75">
                        <span x-text="isVestige(detailItem) ? '✦ Vestígio' : (isArtifact(detailItem) ? '✦ Artefato' : 'Detalhes do Item')"></span>
                    </span>

                    <span
                        x-show="isVestige(detailItem)"
                        x-cloak
                        class="inventory-v51-topbar-stage"
                        x-text="vestigeStageLabel(vestigeEffectiveStage(detailItem))"
                    ></span>

                    <button
                        x-show="detailItem?.properties?.inventory?.equippable"
                        x-cloak
                        type="button"
                        @click="
                            !detailItem.equipped && ['simple','martial','custom'].includes(detailItem?.properties?.weapon?.category)
                            ? window.dispatchEvent(new CustomEvent('spellbound-weapon-equip-request', {
                                detail: {item:detailItem}
                              }))
                            : toggleEquipped(detailItem)
                        "
                        :disabled="busyItemId !== null"
                        class="inventory-v15-detail-equip"
                        :class="{
                            'is-equipped':
                                detailItem?.equipped,

                            'is-stowed':
                                !detailItem?.equipped
                        }"
                        x-text="detailItem?.equipped ? 'Guardar' : 'Equipar'"
                    ></button>


                    <button
                        x-show="detailItem?.requires_attunement"
                        x-cloak
                        type="button"
                        @click="
                            detailItem?.attuned
                                ? applyAttunement(
                                    detailItem,
                                    false
                                )
                                : requestAttunement(
                                    detailItem
                                )
                        "
                        :disabled="busyItemId !== null"
                        class="inventory-v18-attunement-toggle"
                        :class="{
                            'is-attuned':
                                detailItem?.attuned,

                            'is-unattuned':
                                !detailItem?.attuned
                        }"
                        :title="
                            detailItem?.attuned
                                ? 'Remover sintonização'
                                : 'Sintonizar item'
                        "
                    >
                        <span
                            class="inventory-v18-attunement-mark"
                            x-text="
                                detailItem?.attuned
                                    ? '◆'
                                    : '◇'
                            "
                        ></span>

                        <span
                            x-text="
                                detailItem?.attuned
                                    ? 'Dessintonizar'
                                    : 'Sintonizar'
                            "
                        ></span>
                    </button>

                    <button
                        type="button"
                        @click="openEdit(detailItem)"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase tracking-[0.04em] text-[#f4f1e8] transition hover:bg-[#53150f]"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.2 5.2l3.6 3.6M4 20l4.4-1 10.4-10.4a2.55 2.55 0 00-3.6-3.6L4.8 15.4 4 20z" />
                        </svg>
                        Editar
                    </button>

                    <button
                        type="button"
                        @click="closeDetail()"
                        class="inventory-v45-detail-close flex h-8 w-8 items-center justify-center rounded-lg text-xl text-[#8c6239] transition hover:bg-[#efe9dc] hover:text-[#53150f]"
                        title="Fechar"
                    >
                        ×
                    </button>
                </div>

                <div class="inventory-v17-detail-scroll min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7 sm:py-6">
                    <template x-if="detailItem">
                        <article>
                            {{-- Cabeçalho editorial --}}
                            <div class="inventory-v82-detail-hero grid grid-cols-1 gap-5 sm:grid-cols-[158px_minmax(0,1fr)]">
                                <div
                                    class="inventory-v10-thumb inventory-v94-detail-art flex aspect-square w-full items-center justify-center rounded-2xl border border-[#d8c7ab]/65 shadow-[0_5px_18px_rgba(83,21,15,.06)]"
                                >
                                    <template x-if="detailItem?.type === 'technological' && ['legendary', 'artifact'].includes(detailItem?.rarity)">
                                    <span class="vault-v54-preview-energy" aria-hidden="true">
                                        <svg class="vault-v54-preview-circuits" viewBox="0 0 160 230" preserveAspectRatio="none">
                                            <path class="vault-v54-preview-trace" d="M-8 48 H45 L65 68 H108 L122 51 H170" />
                                            <path class="vault-v54-preview-trace" d="M169 177 H114 L98 156 H49 L33 180 H-8" />
                                            <path class="vault-v54-preview-trace" d="M-8 110 H27 L44 94 H97 L112 111 H170" />
                                            <path class="vault-v54-preview-trace" d="M160 20 H129 L108 39 H86 M1 202 H38 L58 188 H91" />
                                        </svg>
                                        <i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i>
                                        <i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i>
                                        <i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i><i class="vault-v54-preview-spark"></i>
                                    </span>
                                </template>
                                    <template x-if="detailItem.image_url">
                                        <img :src="detailItem.image_url" :alt="detailItem.name">
                                    </template>

                                    <template x-if="!detailItem.image_url">
                                        <span class="font-serif text-[40px] text-[#8c6239]/28">◆</span>
                                    </template>
                                </div>

                                <div class="inventory-v17-detail-identity min-w-0 self-center">
                                    <div class="inventory-v17-title-row">
                                        <div class="min-w-0 flex-1">
                                            {{-- Identificação rara preservada; sem etiqueta genérica "Item do Inventário". --}}
                                            <span x-show="isArtifact(detailItem)" x-cloak
                                                  class="inventory-v17-item-kicker"
                                                  x-text="isVestige(detailItem) ? '✦ Vestígio' : 'Artefato'"></span>

                                            <h2
                                                class="inventory-v17-item-title"
                                                x-text="detailItem.name"
                                            ></h2>
                                        </div>

                                        <span
                                            x-show="detailItem.equipped"
                                            x-cloak
                                            class="inventory-v17-status-badge is-equipped"
                                        >
                                            <span class="inventory-v17-status-dot"></span>
                                            Equipado
                                        </span>
                                    </div>

                                    <em
                                        class="inventory-v17-classification"
                                        x-text="classificationLine(detailItem)"
                                    ></em>

                                    <div
                                        x-show="
                                            detailItem.properties?.weapon
                                            || nonWeaponMechanicalLines(detailItem).length > 0
                                        "
                                        x-cloak
                                        class="inventory-v20-editorial-mechanics inventory-v82-editorial-mechanics"
                                        :class="{ 'inventory-v82-weapon-spacing': !!detailItem.properties?.weapon }"
                                    >
                                        {{-- ARMA --}}
                                        <template x-if="detailItem.properties?.weapon">
                                            <div class="inventory-v20-weapon-text">
                                                <p
                                                    class="inventory-v20-weapon-line"
                                                    x-text="weaponDetailLine(detailItem)"
                                                ></p>

                                                <p
                                                    x-show="
                                                        detailItem.properties?.weapon?.traits?.length
                                                    "
                                                    x-cloak
                                                    class="inventory-v20-detail-line"
                                                >
                                                    <strong>Propriedades:</strong>

                                                    <template
                                                        x-for="
                                                            (
                                                                trait,
                                                                traitIndex
                                                            ) in (
                                                                detailItem.properties
                                                                    ?.weapon
                                                                    ?.traits
                                                                ?? []
                                                            )
                                                        "
                                                        :key="
                                                            'detail-property-'
                                                            + detailItem.id
                                                            + '-'
                                                            + (
                                                                trait.key
                                                                ?? trait.name
                                                                ?? traitIndex
                                                            )
                                                        "
                                                    >
                                                        <span class="inventory-v21-inline-tooltip">
                                                            <span
                                                                class="inventory-v21-inline-term"
                                                                tabindex="0"
                                                                x-text="trait.name || 'Propriedade'"
                                                            ></span>

                                                            <span
                                                                x-show="
                                                                    traitIndex
                                                                    < (
                                                                        detailItem.properties
                                                                            ?.weapon
                                                                            ?.traits
                                                                            ?.length
                                                                        ?? 0
                                                                    ) - 1
                                                                "
                                                                aria-hidden="true"
                                                            >, </span>

                                                            <span
                                                                class="inventory-v21-tooltip-panel"
                                                                role="tooltip"
                                                            >
                                                                <span
                                                                    class="inventory-v21-tooltip-title"
                                                                    x-text="trait.name || 'Propriedade'"
                                                                ></span>

                                                                <span
                                                                    class="inventory-v21-tooltip-text"
                                                                    x-text="
                                                                        trait.description
                                                                        || 'Sem descrição adicional.'
                                                                    "
                                                                ></span>
                                                            </span>
                                                        </span>
                                                    </template>
                                                </p>

                                                <p
                                                    x-show="
                                                        detailItem.properties?.weapon?.masteries?.length
                                                    "
                                                    x-cloak
                                                    class="inventory-v20-detail-line"
                                                >
                                                    <strong>Maestrias:</strong>

                                                    <template
                                                        x-for="
                                                            (
                                                                mastery,
                                                                masteryIndex
                                                            ) in (
                                                                detailItem.properties
                                                                    ?.weapon
                                                                    ?.masteries
                                                                ?? []
                                                            )
                                                        "
                                                        :key="
                                                            'detail-mastery-'
                                                            + detailItem.id
                                                            + '-'
                                                            + (
                                                                mastery.key
                                                                ?? mastery.name
                                                                ?? masteryIndex
                                                            )
                                                        "
                                                    >
                                                        <span class="inventory-v21-inline-tooltip">
                                                            <span
                                                                class="inventory-v21-inline-term"
                                                                tabindex="0"
                                                            >
                                                                (<span
                                                                    x-text="
                                                                        mastery.name
                                                                        || 'Maestria'
                                                                    "
                                                                ></span>)
                                                            </span>

                                                            <span
                                                                x-show="
                                                                    masteryIndex
                                                                    < (
                                                                        detailItem.properties
                                                                            ?.weapon
                                                                            ?.masteries
                                                                            ?.length
                                                                        ?? 0
                                                                    ) - 1
                                                                "
                                                                aria-hidden="true"
                                                            > </span>

                                                            <span
                                                                class="inventory-v21-tooltip-panel"
                                                                role="tooltip"
                                                            >
                                                                <span
                                                                    class="inventory-v21-tooltip-title"
                                                                    x-text="
                                                                        mastery.name
                                                                        || 'Maestria'
                                                                    "
                                                                ></span>

                                                                <span
                                                                    class="inventory-v21-tooltip-text"
                                                                    x-text="
                                                                        mastery.description
                                                                        || 'Sem descrição adicional.'
                                                                    "
                                                                ></span>
                                                            </span>
                                                        </span>
                                                    </template>
                                                </p>
                                            </div>
                                        </template>

                                        {{-- ARMADURA / ESCUDO / CUSTOM --}}
                                        <template
                                            x-for="(line, lineIndex) in nonWeaponMechanicalLines(detailItem)"
                                            :key="'detail-other-line-' + lineIndex"
                                        >
                                            <p
                                                class="inventory-v20-detail-line"
                                                :class="{
                                                    'mt-1':
                                                        lineIndex > 0
                                                }"
                                                x-text="line"
                                            ></p>
                                        </template>
                                    </div>
                                    {{-- V66.1.82: a descrição de qualquer item segue as maestrias,
                                         dentro da coluna que acompanha a imagem. --}}
                                    <div
                                        x-show="detailItem.description"
                                        x-cloak
                                        class="inventory-v82-inline-description inventory-v238-rich-text"
                                        x-html="formatItemRichText(detailItem.description)"
                                    ></div>

                                </div>
                            </div>

                            {{-- Propriedades custom --}}
                            <section
                                x-show="detailItem.properties?.custom_properties?.length"
                                x-cloak
                                class="inventory-v17-detail-section mt-7"
                            >
                                <div class="inventory-v11-detail-section-title">
                                    Propriedades
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <template
                                        x-for="(entry, index) in detailItem.properties.custom_properties"
                                        :key="'detail-custom-' + index"
                                    >
                                        <div class="rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/22 px-3.5 py-3">
                                            <div class="flex flex-wrap items-baseline gap-2">
                                                <h3
                                                    class="font-serif text-[16px] font-black text-[#53150f]"
                                                    x-text="entry.title"
                                                ></h3>

                                                <strong
                                                    x-show="entry.value"
                                                    class="text-[12px] text-[#8c6239]"
                                                    x-text="entry.value"
                                                ></strong>
                                            </div>

                                            <div
                                                x-show="entry.description"
                                                class="inventory-v238-rich-text mt-1.5 text-[12px] leading-[1.65] text-[#5f3a27]"
                                                x-html="formatItemRichText(entry.description)"
                                            ></div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            {{-- V66.1.28: Poderes de um Vestígio liberados para este personagem. --}}
                            <section
                                x-show="isVestige(detailItem)"
                                x-cloak
                                class="inventory-v50-powers mt-6"
                            >
                                <template x-for="stage in vestigeUnlockedStages(detailItem)" :key="'player-vestige-' + stage.key">
                                    <div class="inventory-v50-stage-panel">
                                        <header class="inventory-v50-stage-header">
                                            <div>
                                                <span class="inventory-v50-stage-eyebrow">Poder do Vestígio</span>
                                                <h3 x-text="stage.label"></h3>
                                            </div>
                                            <span class="inventory-v50-stage-status" :class="{ 'is-current': stage.active }"
                                                  x-text="stage.active ? 'Etapa atual' : 'Etapa adquirida'"></span>
                                        </header>
                                        <div x-show="stage.lore" x-cloak class="inventory-v50-stage-lore inventory-v238-rich-text" x-html="formatItemRichText(stage.lore)"></div>
                                        <div class="inventory-v50-stage-features">
                                            <template x-for="(feature, featureIndex) in stage.features"
                                                      :key="stage.key + '-' + featureIndex">
                                                <div class="inventory-v50-stage-feature">
                                                    <div class="inventory-v50-stage-feature-head">
                                                        <strong x-text="feature.title || 'Habilidade'"></strong>
                                                        <span x-text="vestigeFeatureUsageLabel(feature)"></span>
                                                    </div>
                                                    <div x-show="feature.description" x-cloak class="inventory-v238-rich-text" x-html="formatItemRichText(feature.description)"></div>
                                                </div>
                                            </template>
                                            <p x-show="stage.features.length === 0" x-cloak class="inventory-v50-stage-lore">
                                                Nenhuma habilidade cadastrada nesta etapa.
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </section>

                            {{-- Habilidades comuns (controladores de usos existentes preservados). --}}
                            <section
                                x-show="!isVestige(detailItem) && detailItem.properties?.features?.length"
                                x-cloak
                                class="inventory-v17-detail-section mt-7"
                            >
                                <div class="inventory-v11-detail-section-title">
                                    Habilidades
                                </div>

                                <div class="space-y-3">
                                    <template
                                        x-for="(feature, featureIndex) in detailItem.properties.features"
                                        :key="'detail-feature-' + featureIndex"
                                    >
                                        <div class="inventory-v11-ability-card">
                                            <div class="inventory-v11-ability-header">
                                                <h3
                                                    class="min-w-0 flex-1 font-serif text-[17px] font-black leading-tight text-[#53150f]"
                                                    x-text="feature.title || 'Habilidade'"
                                                ></h3>

                                                <template x-if="window.spellboundIsScrollRules(detailItem, feature)">
                                                    <button type="button"
                                                        class="inventory-v103-detail-cast"
                                                        :disabled="busyFeatureKey !== null || !!detailItem?.properties?.spell_scroll?.consumed || (feature?.usage?.enabled && Number(feature.usage.current) < 1)"
                                                        @click.stop="window.dispatchEvent(new CustomEvent('spellbound-scroll-request', {detail:{item:detailItem,index:featureIndex}}))">
                                                        <span x-text="!!detailItem?.properties?.spell_scroll?.consumed || (feature?.usage?.enabled && Number(feature.usage.current) < 1) ? 'Conjurado' : 'Conjurar'"></span>
                                                        <span x-show="feature?.usage?.enabled" x-text="'· ' + (feature.usage.current ?? 0) + '/' + (feature.usage.max ?? 1)"></span>
                                                    </button>
                                                </template>

                                                {{-- V66.2.104: o feitiço vinculado tem leitura própria;
                                                     nenhuma descrição automática é repetida nesta gaveta. --}}
                                                <template x-if="window.spellboundIsScrollCast(detailItem, feature)">
                                                    <button type="button"
                                                        class="inventory-v103-read-button inventory-v104-feature-read"
                                                        :aria-label="'Ler feitiço ' + (detailItem?.properties?.spell_scroll?.spell_name || '')"
                                                        @click.stop="window.dispatchEvent(new CustomEvent('spellbound-scroll-read', {detail:{item:detailItem}}))">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5h7a3 3 0 0 1 3 3v11a3 3 0 0 0-3-3H4V5Zm16 0h-4a3 3 0 0 0-3 3"/></svg>
                                                        Ler feitiço
                                                    </button>
                                                </template>

                                                <div
                                                    x-show="feature.usage?.enabled && !window.spellboundIsScrollRules(detailItem, feature) && !window.spellboundIsScrollCast(detailItem, feature)"
                                                    x-cloak
                                                    class="flex shrink-0 flex-wrap items-center justify-end gap-2"
                                                >
                                                    <button
                                                        x-show="
                                                            feature.usage.recovery === 'single_use'
                                                            && !singleUseFeatureUsed(feature)
                                                        "
                                                        x-cloak
                                                        type="button"
                                                        class="inventory-v43-single-use-action"
                                                        @click="requestSingleUse(detailItem, featureIndex)"
                                                        :disabled="busyFeatureKey !== null"
                                                    >
                                                        <span>Usar item</span>
                                                        <span
                                                            class="ml-1 opacity-75"
                                                            x-text="
                                                                '· '
                                                                + feature.usage.current
                                                                + '/'
                                                                + feature.usage.max
                                                            "
                                                        ></span>
                                                    </button>

                                                    <span
                                                        x-show="
                                                            feature.usage.recovery === 'single_use'
                                                            && singleUseFeatureUsed(feature)
                                                        "
                                                        x-cloak
                                                        class="inventory-v43-used-badge !static"
                                                    >
                                                        Usado
                                                    </span>

                                                    <div
                                                        x-show="feature.usage.recovery !== 'single_use'"
                                                        x-cloak
                                                        class="inventory-v10-tracker"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click="changeFeatureUses(detailItem, featureIndex, -1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Reduzir carga acumulada'
                                                                    : 'Gastar carga'
                                                            "
                                                        >
                                                            −
                                                        </button>

                                                        <strong
                                                            x-text="feature.usage.current + '/' + feature.usage.max"
                                                        ></strong>

                                                        <button
                                                            type="button"
                                                            @click="changeFeatureUses(detailItem, featureIndex, 1)"
                                                            :disabled="busyFeatureKey !== null"
                                                            :title="
                                                                feature.usage.counter_mode === 'build'
                                                                    ? 'Acumular carga'
                                                                    : 'Recuperar carga'
                                                            "
                                                        >
                                                            +
                                                        </button>
                                                    </div>

                                                    <span
                                                        class="inventory-v11-recovery-pill"
                                                        x-text="
                                                            feature.usage.recovery === 'single_use'
                                                                ? 'Uso Único'
                                                                : '/ ' + recoveryLabel(feature.usage)
                                                        "
                                                    ></span>

                                                    {{-- Descarte junto da habilidade consumida; sem aviso no cabeçalho. --}}
                                                    <button
                                                        x-show="
                                                            singleUseItemUsed(detailItem)
                                                            && feature.usage?.recovery === 'single_use'
                                                            && featureIndex === singleUseFeatureEntries(detailItem)[0]?.index
                                                        "
                                                        x-cloak
                                                        type="button"
                                                        class="inventory-v43-discard-action"
                                                        @click="requestDiscardUsedItem(detailItem)"
                                                        :disabled="busyItemId !== null"
                                                        :aria-label="'Jogar fora ' + detailItem.name"
                                                        title="Descartar item consumido"
                                                    >
                                                        Jogar fora
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Pergaminhos possuem ações próprias; a descrição extensa
                                                 é consultada no botão Ler feitiço, sem ocupar a gaveta. --}}
                                            <div class="inventory-v11-ability-body"
                                                 x-show="!window.spellboundIsScrollRules(detailItem, feature) && !window.spellboundIsScrollCast(detailItem, feature)" x-cloak>
                                                <div
                                                    x-show="feature.description"
                                                    class="inventory-v238-rich-text text-[13px] leading-[1.72] text-[#4f3427]"
                                                    x-html="formatItemRichText(feature.description)"
                                                ></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="detailItem.is_cursed"
                                x-cloak
                                class="mt-7 rounded-r-xl border-l-2 border-red-700 bg-red-50/60 px-3.5 py-3"
                            >
                                <p class="text-[10px] font-black uppercase tracking-[0.08em] text-red-700">
                                    Maldição Revelada
                                </p>

                                <p
                                    class="mt-1 whitespace-pre-line text-[13px] leading-relaxed text-red-900"
                                    x-text="detailItem.curse_description"
                                ></p>
                            </section>
                        </article>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- =========================================================
         CONFIRMAÇÃO — USO ÚNICO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="singleUseConfirmOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[270] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Confirmar uso do item"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="cancelSingleUse()"
            ></div>

            <div
                class="character-theme-modal relative z-10 w-full max-w-[430px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="border-b border-[#d8c7ab]/55 bg-[#efe9dc]/38 px-5 py-4">
                    <span class="text-[9px] font-black uppercase tracking-[0.11em] text-[#8c6239]">
                        Uso permanente
                    </span>
                    <h4 class="mt-1 font-serif text-[20px] font-black text-[#53150f]">
                        Gastar 1 carga?
                    </h4>
                </div>

                <div class="px-5 py-5">
                    <p class="text-[13px] leading-[1.7] text-[#5f4638]">
                        Você está prestes a gastar 1 carga de
                        <strong x-text="pendingSingleUse?.itemName ?? 'este item'"></strong>.
                    </p>

                    <div
                        class="mt-3 rounded-xl border border-[#d8c7ab]/55 bg-[#efe9dc]/30 px-4 py-3"
                    >
                        <span class="text-[9px] font-black uppercase tracking-[0.08em] text-[#8c6239]">
                            Cargas
                        </span>

                        <strong
                            class="mt-1 block font-serif text-[18px] font-black text-[#53150f]"
                            x-text="
                                (pendingSingleUse?.current ?? 0)
                                + '/'
                                + (pendingSingleUse?.max ?? 1)
                                + ' → '
                                + (pendingSingleUse?.remainingAfterUse ?? 0)
                                + '/'
                                + (pendingSingleUse?.max ?? 1)
                            "
                        ></strong>
                    </div>

                    <p
                        x-show="!pendingSingleUse?.isLastCharge"
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Essa carga é consumida permanentemente e não poderá ser recuperada pelo contador.
                    </p>

                    <p
                        x-show="
                            pendingSingleUse?.isLastCharge
                            && pendingSingleUse?.marksWholeItem
                        "
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Esta é a última carga. Depois da confirmação, o item será marcado como <strong>Usado</strong> e poderá ser descartado.
                    </p>

                    <p
                        x-show="
                            pendingSingleUse?.isLastCharge
                            && !pendingSingleUse?.marksWholeItem
                        "
                        x-cloak
                        class="mt-3 text-[12px] leading-[1.65] text-[#7c6657]"
                    >
                        Esta é a última carga desta habilidade. Depois da confirmação, ela ficará esgotada permanentemente.
                    </p>
                </div>

                <div class="flex justify-end gap-2 border-t border-[#d8c7ab]/55 bg-[#efe9dc]/20 px-5 py-3.5">
                    <button
                        type="button"
                        @click="cancelSingleUse()"
                        :disabled="busyFeatureKey !== null"
                        class="rounded-lg px-3.5 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-[#8c6239] disabled:opacity-50"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        @click="confirmSingleUse()"
                        :disabled="busyFeatureKey !== null"
                        class="rounded-lg bg-[#6b1d14] px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] text-[#faf8f2] hover:bg-[#53150f] disabled:opacity-50"
                    >
                        <span x-text="busyFeatureKey !== null ? 'Gastando...' : 'Gastar 1 carga'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>


    {{-- =========================================================
         CONFIRMAÇÃO — DESCARTAR ITEM USADO
    ========================================================== --}}
    <template x-teleport="body">
        <div
            x-show="discardUsedConfirmOpen"
            x-cloak
            data-theme="{{ $characterTheme }}"
            class="character-sheet-theme character-theme-modal-layer fixed inset-0 z-[271] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Descartar item usado"
        >
            <div
                class="character-theme-modal-backdrop absolute inset-0"
                @click="cancelDiscardUsedItem()"
            ></div>

            <div
                class="character-theme-modal inventory-v89-discard-modal relative z-10 w-full max-w-[410px] overflow-hidden rounded-2xl border shadow-2xl"
            >
                <div class="inventory-v89-discard-head px-5 py-4">
                    <span class="inventory-v89-discard-eyebrow text-[9px] font-black uppercase tracking-[0.11em]">
                        Item usado
                    </span>
                    <h4 class="inventory-v89-discard-title mt-1 font-serif text-[20px] font-black">
                        Jogar fora?
                    </h4>
                </div>

                <div class="inventory-v89-discard-body px-5 py-5">
                    <p class="text-[13px] leading-[1.7]">
                        <strong x-text="pendingDiscardItem?.name ?? 'Este item'"></strong>
                        já foi consumido. Ao jogar fora, ele será removido permanentemente do inventário.
                    </p>
                </div>

                <div class="inventory-v89-discard-footer flex justify-end gap-2 px-5 py-3.5">
                    <button
                        type="button"
                        @click="cancelDiscardUsedItem()"
                        :disabled="busyItemId !== null"
                        class="inventory-v89-discard-keep rounded-lg px-3.5 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] disabled:opacity-50"
                    >
                        Manter
                    </button>

                    <button
                        type="button"
                        @click="confirmDiscardUsedItem()"
                        :disabled="busyItemId !== null"
                        class="inventory-v89-discard-confirm rounded-lg px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.04em] disabled:opacity-50"
                    >
                        <span x-text="busyItemId !== null ? 'Descartando...' : 'Jogar fora'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>


{{-- V66.2.103: os controles do pergaminho herdam a paleta da ficha. --}}
@push('styles')
<style>
.character-sheet-theme[data-theme] .inventory-v103-read-button,
.character-sheet-theme[data-theme] .inventory-v103-detail-cast {
    display:inline-flex;align-items:center;justify-content:center;gap:7px;
    border:1px solid var(--theme-border-strong,var(--theme-border));
    border-radius:9px;padding:8px 11px;white-space:nowrap;
    background:var(--theme-button-background,var(--theme-surface-card));
    color:var(--theme-button-text,var(--theme-text-strong));
    font:800 10px system-ui,sans-serif;cursor:pointer;
    transition:background .15s,border-color .15s;
}
.character-sheet-theme[data-theme] .inventory-v103-read-button {
    background:var(--theme-card-alt-background,var(--theme-paper-alt));
    color:var(--theme-text-strong);
}
.character-sheet-theme[data-theme] .inventory-v103-read-button svg{width:14px;height:14px;flex-shrink:0;}
.character-sheet-theme[data-theme] .inventory-v103-read-button:hover,
.character-sheet-theme[data-theme] .inventory-v103-detail-cast:hover:not(:disabled){
    background:var(--theme-button-hover-background,var(--theme-hover));
    border-color:var(--theme-accent);
}
.character-sheet-theme[data-theme] .inventory-v103-detail-cast:disabled{opacity:.52;cursor:not-allowed;}
@media(max-width:560px){.character-sheet-theme[data-theme] .inventory-v103-read-button{font-size:9px;padding:7px 8px;}}
/* V66.2.104 — ações pontuais, sem interferir nos outros itens. */
.character-sheet-theme[data-theme] .inventory-v104-feature-read{
    margin-left:auto;flex:0 0 auto;padding:7px 10px;
    background:var(--theme-card-alt-background,var(--theme-paper-alt));
    color:var(--theme-text-primary);font-size:10px;
}
.character-sheet-theme[data-theme] .inventory-v104-feature-read:hover{
    color:var(--theme-text-strong);background:var(--theme-hover);
}
@media(max-width:540px){
    .character-sheet-theme[data-theme] .inventory-v104-feature-read{padding:7px 8px;font-size:9px;}
    .character-sheet-theme[data-theme] .inventory-v11-ability-header{flex-wrap:wrap;row-gap:9px;}
}
</style>
@endpush
