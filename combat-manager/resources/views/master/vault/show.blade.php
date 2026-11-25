        {{-- =========================================================
             VISUALIZAÇÃO RÁPIDA DO ITEM
        ========================================================== --}}
        <template x-teleport="body">
            <div
                x-show="previewOpen && previewItem"
                x-cloak
                class="master-vault-v9-preview-layer"
                x-transition.opacity.duration.130ms
                role="dialog"
                aria-modal="true"
                aria-label="Visualização do item"
            >
                <div
                    class="master-vault-v9-preview-backdrop"
                    @click="closePreview()"
                ></div>

                <section
                    class="master-vault-v9-preview"
                    :class="{ 'vault-v23-preview-vestige': isVestige(previewItem) }"
                    :data-nature="previewItem?.type || 'mundane'"
                    :data-rarity="previewItem?.rarity || 'common'"
                    x-transition:enter="transition ease-out duration-170"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                >
                    <header class="master-vault-v9-preview-head">
                        <div class="master-vault-v9-preview-head-copy">
                            <span class="master-vault-v9-preview-kicker"
                                  x-text="isVestige(previewItem) ? 'Vestígio · Visão completa do Mestre' : 'Visualização do Mestre'">
                            </span>
                        </div>

                        <div class="master-vault-v9-preview-head-actions">
                            <button
                                type="button"
                                class="master-vault-v9-preview-nav"
                                :disabled="!hasPreviousPreview"
                                @click="previewAdjacent(-1)"
                                title="Item anterior"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="m15 6-6 6 6 6"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-nav"
                                :disabled="!hasNextPreview"
                                @click="previewAdjacent(1)"
                                title="Próximo item"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="m9 6 6 6-6 6"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-close"
                                @click="closePreview()"
                                title="Fechar"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path
                                        d="M6 6l12 12M18 6 6 18"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <div class="master-vault-v9-preview-body">
                        {{-- V66.1.79: reflexo interno; não bloqueia a rolagem. --}}
                        <span x-show="previewItem?.type === 'wonderful' && previewItem?.rarity === 'artifact'"
                              x-cloak class="vault-v79-liquid-gold vault-v79-liquid-gold--preview" aria-hidden="true"></span>
                        <section class="master-vault-v9-preview-hero master-vault-v16-preview-hero">
                            <div class="master-vault-v9-preview-image master-vault-v16-preview-image">
                                {{-- Mesma energia dos cards tecnológicos, atrás da foto e sem alterar o item. --}}
                                <template x-if="previewItem?.type === 'technological' && ['legendary', 'artifact'].includes(previewItem?.rarity)">
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
                                <template x-if="previewItem?.image_url">
                                    <img
                                        :src="previewItem.image_url"
                                        :alt="previewItem.name"
                                    >
                                </template>

                                <template x-if="!previewItem?.image_url">
                                    <svg
                                        class="h-12 w-12"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path d="M5 6h14v12H5z" stroke-width="1.5"/>
                                        <path
                                            d="m7 15 3.2-3.2 2.5 2.5 2-2 2.8 2.7"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </template>
                            </div>

                            <div class="master-vault-v9-preview-summary master-vault-v16-preview-summary">
                                <h1
                                    class="master-vault-v9-preview-name master-vault-v16-preview-name"
                                    x-text="previewItem?.name"
                                ></h1>

                                <div class="master-vault-v16-classification">
                                    <p class="master-vault-v16-nature">
                                        <span x-text="cardNatureLine(previewItem)"></span>
                                    </p>

                                    <p
                                        x-show="cardCategoryLine(previewItem)"
                                        x-cloak
                                        class="master-vault-v16-category"
                                        x-text="cardCategoryLine(previewItem)"
                                    ></p>
                                </div>

                                <div
                                    x-show="(previewItem?.properties?.weapon?.traits ?? []).length > 0"
                                    x-cloak
                                    class="master-vault-v16-inline-rules"
                                >
                                    <strong>Propriedades:</strong>

                                    <div class="master-vault-v16-inline-list">
                                        <template
                                            x-for="(trait, index) in (previewItem?.properties?.weapon?.traits ?? [])"
                                            :key="'preview-inline-trait-' + trait.key"
                                        >
                                            <span class="master-vault-v16-inline-entry">
                                                <button
                                                    type="button"
                                                    class="master-vault-v16-hover-link"
                                                    :data-tooltip="trait.description || 'Sem descrição.'"
                                                    :title="trait.description || trait.name"
                                                >
                                                    <span x-text="trait.name"></span>
                                                    <small
                                                        x-show="weaponRuleCompactValue(trait, previewItem?.properties?.weapon)"
                                                        x-text="' (' + weaponRuleCompactValue(trait, previewItem?.properties?.weapon) + ')'"
                                                    ></small>
                                                </button>
                                                <span
                                                    x-show="index < (previewItem?.properties?.weapon?.traits ?? []).length - 1"
                                                    class="master-vault-v16-separator"
                                                >,</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    x-show="(previewItem?.properties?.weapon?.masteries ?? []).length > 0"
                                    x-cloak
                                    class="master-vault-v16-inline-rules"
                                >
                                    <strong>Maestrias:</strong>

                                    <div class="master-vault-v16-inline-list">
                                        <template
                                            x-for="(mastery, index) in (previewItem?.properties?.weapon?.masteries ?? [])"
                                            :key="'preview-inline-mastery-' + mastery.key"
                                        >
                                            <span class="master-vault-v16-inline-entry">
                                                <button
                                                    type="button"
                                                    class="master-vault-v16-hover-link"
                                                    :data-tooltip="mastery.description || 'Sem descrição.'"
                                                    :title="mastery.description || mastery.name"
                                                >
                                                    <span x-text="'(' + mastery.name + ')'"></span>
                                                </button>
                                                <span
                                                    x-show="index < (previewItem?.properties?.weapon?.masteries ?? []).length - 1"
                                                    class="master-vault-v16-separator"
                                                >,</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    x-show="
                                        (previewItem?.weight !== null && previewItem?.weight !== '')
                                        || (previewItem?.default_quantity ?? 1) !== 1
                                    "
                                    x-cloak
                                    class="master-vault-v16-small-meta"
                                >
                                    <span
                                        x-show="previewItem?.weight !== null && previewItem?.weight !== ''"
                                        x-cloak
                                    >
                                        <strong>Peso:</strong>
                                        <span x-text="previewItem?.weight"></span>
                                    </span>

                                    <span
                                        x-show="(previewItem?.default_quantity ?? 1) !== 1"
                                        x-cloak
                                    >
                                        <strong>Quantidade:</strong>
                                        <span x-text="previewItem?.default_quantity ?? 1"></span>
                                    </span>
                                </div>

                                {{-- A descrição é a última informação do resumo, após arma/propriedades/maestrias. --}}
                                <div
                                    x-show="previewItem?.description"
                                    x-cloak
                                    class="vault-v23-inline-description"
                                >
                                    <div class="vault-v30-rich-text" x-html="formatVaultText(previewItem?.description)"></div>
                                </div>
                            </div>
                        </section>

                        {{-- Armadura --}}
                        <section
                            x-show="previewItem?.properties?.armor"
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Armadura</h3>

                                <span
                                    x-text="
                                        previewItem?.properties?.armor?.armor_type
                                        || armorCategoryLabel(
                                            previewItem?.properties?.armor?.category
                                        )
                                    "
                                ></span>
                            </div>

                            <article class="master-vault-v9-preview-rule">
                                <strong>Fórmula de CA</strong>

                                <p
                                    x-text="
                                        armorFormulaText(
                                            previewItem?.properties?.armor
                                        )
                                    "
                                ></p>
                            </article>
                        </section>

                        {{-- Escudo --}}
                        <section
                            x-show="previewItem?.properties?.shield"
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Escudo</h3>

                                <span
                                    x-text="
                                        previewItem?.properties?.shield?.label
                                        || 'Escudo'
                                    "
                                ></span>
                            </div>

                            <article class="master-vault-v9-preview-rule">
                                <strong>Bônus defensivo</strong>

                                <p
                                    x-text="
                                        '+' + shieldTotalBonus(
                                            previewItem?.properties?.shield
                                        ) + ' CA'
                                    "
                                ></p>
                            </article>
                        </section>

                        {{-- Propriedades custom --}}
                        <section
                            x-show="
                                (previewItem?.properties?.custom_properties ?? []).length > 0
                            "
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v9-preview-section-head">
                                <h3>Propriedades</h3>
                            </div>

                            <div class="master-vault-v9-preview-grid">
                                <template
                                    x-for="
                                        (property, index) in
                                        (previewItem?.properties?.custom_properties ?? [])
                                    "
                                    :key="'preview-property-' + index"
                                >
                                    <article class="master-vault-v9-preview-rule">
                                        <strong x-text="property.title || 'Propriedade'"></strong>

                                        <p
                                            x-text="
                                                [property.value, property.description]
                                                    .filter(Boolean)
                                                    .join(' — ')
                                            "
                                        ></p>
                                    </article>
                                </template>
                            </div>
                        </section>

                        {{-- Habilidades comuns: preservadas sem alteração de comportamento. --}}
                        <section
                            x-show="!isVestige(previewItem) && cardFeatures(previewItem).length > 0"
                            x-cloak
                            class="master-vault-v9-preview-section"
                        >
                            <div class="master-vault-v16-section-title">
                                <span>Habilidades</span>
                                <i></i>
                            </div>

                            <template
                                x-for="(feature, index) in cardFeatures(previewItem)"
                                :key="'preview-feature-' + index"
                            >
                                <article class="master-vault-v9-preview-feature"
                                         :class="{ 'vault-v284-scroll-compact': feature.system_key === 'spell_scroll_cast' && !!previewItem?.properties?.spell_scroll }">
                                    <div class="master-vault-v9-preview-feature-head">
                                        <h4 x-text="feature.title || ('Habilidade ' + (index + 1))"></h4>
                                        <span
                                            x-show="feature?.usage?.enabled"
                                            class="master-vault-v9-preview-usage"
                                            x-text="
                                                (feature.usage.counter_mode === 'build'
                                                    ? ('Acúmulo ' + feature.usage.current + '/' + feature.usage.max)
                                                    : (feature.usage.current + '/' + feature.usage.max + ' usos'))
                                                + (feature.usage.recovery
                                                    ? (' · ' + recoveryLabel(feature.usage.recovery))
                                                    : '')
                                            "
                                        ></span>
                                        <template x-if="feature.system_key === 'spell_scroll_cast' && previewItem?.properties?.spell_scroll">
                                            <button type="button" class="vault-v284-preview-read"
                                                    @click.stop="openScrollReader(previewItem)"
                                                    :aria-label="'Ler feitiço ' + (scrollSpellReaderData(previewItem).name || '')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5h7a3 3 0 0 1 3 3v11a3 3 0 0 0-3-3H4V5.5Zm16 0h-4a3 3 0 0 0-3 3m7-3v11h-4a3 3 0 0 0-3 3"/></svg>
                                                <span>Ler feitiço</span>
                                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7 4 6 6-6 6"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                    <template x-if="feature.system_key !== 'spell_scroll_cast' || !previewItem?.properties?.spell_scroll">
                                        <div class="vault-v30-rich-text" x-html="formatVaultText(feature.description)"></div>
                                    </template>
                                </article>
                            </template>
                        </section>

                        {{-- Vestígios: a prévia do Mestre revela as três etapas completas,
                             sem alterar qual fase é disponibilizada na ficha do jogador. --}}
                        <section
                            x-show="isVestige(previewItem)"
                            x-cloak
                            class="vault-v23-vestige-section"
                            aria-label="Habilidades das três etapas do Vestígio"
                        >
                            <template x-for="stage in [
                                { key: 'dormant', label: 'Dormente', range: 'Proficiência +2 a +3', order: 'I' },
                                { key: 'awakened', label: 'Desperta', range: 'Proficiência +4 a +5', order: 'II' },
                                { key: 'exalted', label: 'Exaltada', range: 'Proficiência +6', order: 'III' }
                            ]" :key="'vault-preview-stage-' + stage.key">
                                <section class="vault-v23-vestige-stage" :data-stage="stage.key">
                                    <div class="vault-v23-stage-header">
                                        <span class="vault-v23-stage-order" x-text="stage.order"></span>
                                        <div class="vault-v23-stage-identity">
                                            <h4 x-text="stage.label"></h4>
                                            <small x-text="stage.range"></small>
                                        </div>
                                        <span class="vault-v23-stage-count"
                                            x-text="(() => {
                                                const count = (previewItem?.properties?.vestige?.stages?.[stage.key]?.features ?? []).length;
                                                return count + (count === 1 ? ' habilidade' : ' habilidades');
                                            })()"
                                        ></span>
                                    </div>

                                    <div
                                        class="vault-v23-stage-lore vault-v30-rich-text"
                                        x-show="previewItem?.properties?.vestige?.stages?.[stage.key]?.lore"
                                        x-html="formatVaultText(previewItem?.properties?.vestige?.stages?.[stage.key]?.lore)"
                                    ></div>

                                    <div class="vault-v23-stage-features">
                                        <template
                                            x-for="(feature, index) in (previewItem?.properties?.vestige?.stages?.[stage.key]?.features ?? [])"
                                            :key="'vault-preview-feature-' + stage.key + '-' + index"
                                        >
                                            <article class="master-vault-v9-preview-feature vault-v23-vestige-feature">
                                                <div class="master-vault-v9-preview-feature-head">
                                                    <h4 x-text="feature.title || ('Habilidade ' + (index + 1))"></h4>
                                                    <span
                                                        x-show="feature?.usage?.enabled"
                                                        class="master-vault-v9-preview-usage"
                                                        x-text="
                                                            (feature.usage.counter_mode === 'build'
                                                                ? ('Acúmulo ' + feature.usage.current + '/' + feature.usage.max)
                                                                : (feature.usage.current + '/' + feature.usage.max + ' usos'))
                                                            + (feature.usage.recovery
                                                                ? (' · ' + recoveryLabel(feature.usage.recovery))
                                                                : '')
                                                        "
                                                    ></span>
                                                </div>
                                                <div class="vault-v30-rich-text" x-html="formatVaultText(feature.description)"></div>
                                            </article>
                                        </template>
                                        <p
                                            class="vault-v23-stage-empty"
                                            x-show="!(previewItem?.properties?.vestige?.stages?.[stage.key]?.features?.length > 0)"
                                        >Nenhuma habilidade cadastrada nesta etapa.</p>
                                    </div>
                                </section>
                            </template>
                        </section>

                        {{-- Segredos do Mestre --}}
                        <section
                            x-show="
                                previewItem?.is_cursed
                                || previewItem?.notes
                            "
                            class="master-vault-v9-preview-secret"
                        >
                            <span class="master-vault-v9-preview-secret-label">
                                Somente o Mestre
                            </span>

                            <h3>Informações privadas</h3>

                            <div x-show="previewItem?.is_cursed">
                                <p>
                                    <strong x-text="previewItem?.curse_name || 'Maldição'"></strong>
                                    <span
                                        x-text="
                                            previewItem?.curse_description
                                            || 'Item marcado como amaldiçoado.'
                                        "
                                    ></span>
                                </p>
                            </div>

                            <div x-show="previewItem?.notes">
                                <p>
                                    <strong>Notas:</strong>
                                    <span x-text="previewItem?.notes"></span>
                                </p>
                            </div>
                        </section>
                    </div>

                    <footer class="master-vault-v9-preview-foot">
                        <span
                            class="master-vault-v9-preview-position"
                            x-text="
                                previewIndex >= 0
                                    ? (
                                        (previewIndex + 1)
                                        + ' de '
                                        + filteredItems.length
                                    )
                                    : ''
                            "
                        ></span>

                        <div class="master-vault-v9-preview-foot-actions">
                            <button
                                type="button"
                                class="master-vault-v9-preview-dismiss"
                                @click="closePreview()"
                            >
                                Fechar
                            </button>

                            <button
                                type="button"
                                class="master-vault-v9-preview-edit"
                                @click="editFromPreview()"
                            >
                                Editar Item
                            </button>
                        </div>
                    </footer>
                </section>
            </div>
        </template>


        {{-- V66.2.81 — Leitura do feitiço em janela própria, sem sobrecarregar editor ou preview. --}}
        <template x-teleport="body">
            <div class="vault-v281-reader-layer" x-show="scrollReaderOpen && scrollReaderItem" x-cloak
                 role="dialog" aria-modal="true" aria-label="Leitura do feitiço vinculado"
                 @keydown.escape.stop.prevent="closeScrollReader()">
                <div class="vault-v281-reader-backdrop" @click="closeScrollReader()"></div>
                <article class="vault-v281-reader-dialog">
                    <header class="vault-v281-reader-head">
                        <div class="vault-v281-reader-brand"><span class="vault-v281-reader-brand-icon">✧</span><span>Grimório · Feitiço vinculado</span></div>
                        <button type="button" class="vault-v281-reader-close" @click="closeScrollReader()" aria-label="Fechar leitura">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 6 18 18M18 6 6 18" stroke-linecap="round"/></svg>
                        </button>
                    </header>
                    <div class="vault-v281-reader-scroll">
                        <div class="vault-v281-reader-article">
                            <div class="vault-v281-reader-title-row">
                                <div>
                                    <h3 x-text="scrollSpellReaderData(scrollReaderItem).name"></h3>
                                    <em x-text="scrollSpellReaderData(scrollReaderItem).subtitle"></em>
                                </div>
                                <small x-text="scrollReaderItem?.properties?.spell_scroll?.spell_source || ''"></small>
                            </div>
                            <dl class="vault-v281-reader-statlines">
                                <div><dt>Conjuração</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).castingTime"></dd></div>
                                <div><dt>Alcance</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).range"></dd></div>
                                <div><dt>Componentes</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).components"></dd></div>
                                <div><dt>Duração</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).duration"></dd></div>
                            </dl>
                            <div class="vault-v281-reader-flags" x-show="scrollSpellReaderData(scrollReaderItem).concentration || scrollSpellReaderData(scrollReaderItem).ritual" x-cloak>
                                <span x-show="scrollSpellReaderData(scrollReaderItem).concentration">Concentração</span>
                                <span x-show="scrollSpellReaderData(scrollReaderItem).ritual">Ritual</span>
                            </div>
                            <div class="vault-v281-reader-prose" x-show="scrollSpellReaderHtml(scrollReaderItem,'description')" x-html="scrollSpellReaderHtml(scrollReaderItem,'description')"></div>
                            <section class="vault-v281-reader-higher" x-show="scrollSpellReaderHtml(scrollReaderItem,'higher')" x-cloak>
                                <h4 x-text="scrollSpellReaderData(scrollReaderItem).higherLabel"></h4>
                                <div class="vault-v281-reader-prose" x-html="scrollSpellReaderHtml(scrollReaderItem,'higher')"></div>
                            </section>
                            <p class="vault-v281-reader-loading" x-show="!scrollSpellReaderHtml(scrollReaderItem,'description') && scrollSpellReaderData(scrollReaderItem).loading" x-cloak>Consultando o Grimório...</p>
                            <div class="vault-v281-reader-unavailable" x-show="!scrollSpellReaderHtml(scrollReaderItem,'description') && !scrollSpellReaderData(scrollReaderItem).loading" x-cloak>
                                <span x-text="scrollSpellReaderData(scrollReaderItem).error ? 'Não foi possível carregar este feitiço.' : 'Descrição não disponível para este feitiço.'"></span>
                                <button type="button" x-show="scrollSpellReaderData(scrollReaderItem).hasLink" @click="requestScrollSpellReader(scrollReaderItem,true)">Tentar novamente</button>
                            </div>
                        </div>
                    </div>
                    <footer class="vault-v281-reader-foot">
                        <span>Dados do Grimório · Valores de CD e ataque definidos pelo pergaminho</span>
                        <div>
                            <button type="button" x-show="scrollReaderItem === form && !!form?.properties?.spell_scroll" x-cloak
                                    @click="closeScrollReader();openScrollLibrary()" class="vault-v281-reader-change">Trocar feitiço</button>
                            <button type="button" @click="closeScrollReader()" class="vault-v281-reader-done">Fechar</button>
                        </div>
                    </footer>
                </article>
            </div>
        </template>
