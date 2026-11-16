        <template x-teleport="body">
            <div
                x-show="editorOpen"
                x-cloak
                class="master-vault-v4-layer"
                x-transition.opacity.duration.140ms
                role="dialog"
                aria-modal="true"
                aria-label="Editor de item do Vault"
            >
                <div
                    class="master-vault-v4-backdrop"
                    @click="closeEditor()"
                ></div>

                <section
                    class="master-vault-v4-modal"
                    x-transition:enter="transition ease-out duration-180"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-[.985]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-130"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-[.99]"
                >
                    {{-- Editor compacto: sem cabeçalho redundante; a ação está no rodapé. --}}
                    <header class="master-vault-v4-head vault-v82-editor-head">
                        <span class="vault-v82-editor-head-rule" aria-hidden="true"></span>
                        <button
                            type="button"
                            class="master-vault-v4-close"
                            @click="closeEditor()"
                            :disabled="saving"
                            aria-label="Fechar editor"
                            title="Fechar"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                        </button>
                    </header>

                    <template x-if="form">
                        <div class="master-vault-v4-scroll">
                            {{-- V66.1.18 — Dados gerais organizados por etapas.
                                 Todos os x-model / eventos seguem a V66.1.17. --}}
                            <div class="vault-v18-workspace">
                                <aside class="vault-v18-sidebar" aria-label="Imagem do item">
                                    <div class="vault-v18-sidebar-card">

                                        <div class="master-vault-v4-photo vault-v18-photo">
                                            <label class="master-vault-v4-photo-frame" title="Adicionar ou substituir a imagem do item">
                                                <template x-if="imagePreview || form.image_url">
                                                    <img :src="imagePreview || form.image_url" alt="Prévia do item">
                                                </template>
                                                <template x-if="!(imagePreview || form.image_url)">
                                                    <div class="master-vault-v4-photo-empty">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                            <path d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <span>Adicionar foto</span>
                                                    </div>
                                                </template>
                                                <input
                                                    type="file"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="hidden"
                                                    x-ref="vaultItemImageInput"
                                                    @click="$event.target.value = null"
                                                    @change="handleImage($event)"
                                                >
                                            </label>
                                            <button
                                                x-show="imagePreview || form.image_url"
                                                x-cloak
                                                type="button"
                                                class="vault-v38-photo-edit"
                                                @click="openCropEditor()"
                                                title="Ajustar enquadramento, zoom ou substituir a foto"
                                                aria-label="Editar foto do item"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 20h9M16 4a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4L16 4z"/>
                                                </svg>
                                                <span>Ajustar foto</span>
                                            </button>
                                            <button
                                                x-show="imagePreview || form.image_url"
                                                x-cloak
                                                type="button"
                                                class="master-vault-v4-photo-remove"
                                                @click="removeImage()"
                                            >Remover foto</button>
                                        </div>
                                    </div>
                                </aside>

                                <div class="vault-v18-form">
                                    <section class="vault-v18-card" x-ref="vaultEditorIdentity">
                                        <header class="vault-v18-card-heading">
                                            <div>
                                                <h3>Identidade e classificação</h3>
                                                <p>Defina o item e como ele será utilizado.</p>
                                            </div>
                                        </header>
                                        <div class="vault-v18-fields vault-v18-fields--identity">
                                            <label class="master-vault-v4-field vault-v18-field--name">
                                                <span>Nome do Item</span>
                                                <input
                                                    type="text"
                                                    maxlength="120"
                                                    x-model="form.name"
                                                    placeholder="Ex.: Machado Lampejo Real"
                                                    class="master-vault-v4-input"
                                                >
                                            </label>
                                        </div>

                                        <div class="vault-v82-classification" x-ref="vaultEditorClassification">
                                            <div class="vault-v82-classification-main">
                                                <div class="vault-v18-group">
                                            <span class="master-vault-v4-label">Natureza do Item</span>
                                            <div class="vault-v18-nature-options">
                                                <button type="button" class="vault-v18-nature-option"
                                                    :class="{ 'is-active': form.type === 'wonderful' }"
                                                    :aria-pressed="form.type === 'wonderful'"
                                                    @click="setNature('wonderful')">
                                                    <span class="vault-v39-nature-selected" aria-hidden="true">✓</span>
                                                    <span class="vault-v18-nature-mark vault-v38-nature-mark vault-v38-nature-mark--magical" aria-hidden="true"><span class="vault-v36-nature-icon vault-v36-nature-icon--magical">✦</span></span>
                                                    <span><strong>Maravilhoso</strong></span>
                                                </button>
                                                <button type="button" class="vault-v18-nature-option"
                                                    :class="{ 'is-active': form.type === 'technological' }"
                                                    :aria-pressed="form.type === 'technological'"
                                                    @click="setNature('technological')">
                                                    <span class="vault-v39-nature-selected" aria-hidden="true">✓</span>
                                                    <span class="vault-v18-nature-mark vault-v38-nature-mark vault-v38-nature-mark--technology" aria-hidden="true"><span class="vault-v40-tech-symbol"></span></span>
                                                    <span><strong>Tecnológico</strong></span>
                                                </button>
                                                <button type="button" class="vault-v18-nature-option"
                                                    :class="{ 'is-active': form.type === 'mundane' }"
                                                    :aria-pressed="form.type === 'mundane'"
                                                    @click="setNature('mundane')">
                                                    <span class="vault-v39-nature-selected" aria-hidden="true">✓</span>
                                                    <span class="vault-v18-nature-mark vault-v38-nature-mark vault-v38-nature-mark--mundane" aria-hidden="true"><img class="vault-v39-nature-image vault-v39-nature-image--mundane" src="{{ asset('images/vault/icons/mundane.png') }}" alt="" loading="lazy"></span>
                                                    <span><strong>Mundano</strong></span>
                                                </button>
                                            </div>
                                                </div>
                                            <div class="vault-v18-field-group vault-v82-equippable">
                                                <span class="master-vault-v4-label">É equipável?</span>
                                                <button type="button" class="vault-v46-equip-toggle"
                                                    :class="{ 'is-active': form.properties.inventory.equippable }"
                                                    :aria-pressed="form.properties.inventory.equippable"
                                                    :aria-label="form.properties.inventory.equippable ? 'Equipável. Clique para desativar' : 'Não equipável. Clique para ativar'"
                                                    @click="setEquippable(!form.properties.inventory.equippable)">
                                                    <span class="vault-v45-hand-icon" :class="form.properties.inventory.equippable ? 'is-equipped' : 'is-unequipped'" aria-hidden="true"></span>
                                                    <span x-text="form.properties.inventory.equippable ? 'Equipável' : 'Não equipável'"></span>
                                                    <span class="vault-v46-equip-indicator" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            </div>

                                        <div class="vault-v18-fields vault-v18-classification-fields">
                                            <div class="vault-v38-rarity-field vault-v82-rarity-field master-vault-v4-field"
                                                 :data-nature="form.type" :data-rarity="form.rarity">
                                                <span class="master-vault-v4-label" id="vault-editor-rarity-label">Raridade</span>
                                                <details class="vault-v45-rarity-picker" @click.outside="$el.open = false">
                                                    <summary class="vault-v45-rarity-trigger" :aria-label="'Selecionar raridade. Atual: ' + ({common:'Comum',uncommon:'Incomum',rare:'Raro',very_rare:'Muito Raro',legendary:'Lendário',artifact:'Artefato'}[form.rarity] || 'Comum')">
                                                        <span class="vault-v45-rarity-current"><i class="vault-v82-rarity-dot" aria-hidden="true"></i><strong x-text="({common:'Comum',uncommon:'Incomum',rare:'Raro',very_rare:'Muito Raro',legendary:'Lendário',artifact:'Artefato'}[form.rarity] || 'Comum')"></strong></span>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                                                    </summary>
                                                    <div class="vault-v82-rarity-list vault-v45-rarity-options" role="group" aria-labelledby="vault-editor-rarity-label">
                                                    @foreach($rarityLabels as $key => $label)
                                                        <button type="button" class="vault-v82-rarity-choice"
                                                            data-rarity="{{ $key }}"
                                                            :class="{ 'is-selected': form.rarity === '{{ $key }}' }"
                                                            :aria-pressed="form.rarity === '{{ $key }}'"
                                                            @click="form.rarity = '{{ $key }}'; if (form.rarity !== 'artifact' && form.properties?.vestige) form.properties.vestige.enabled = false; $el.closest('details').open = false">
                                                            <span class="vault-v82-rarity-dot" aria-hidden="true"></span>
                                                            <span>{{ $label }}</span>
                                                        </button>
                                                    @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                            <div x-show="form.type === 'wonderful'" x-cloak class="vault-v18-field-group vault-v46-attunement-field">
                                                <span class="master-vault-v4-label">Sintonização</span>
                                                <button type="button" class="vault-v46-attunement-toggle" role="switch"
                                                    :aria-checked="form.requires_attunement"
                                                    :aria-label="form.requires_attunement ? 'Item sintonizável. Clique para desativar' : 'Item sem sintonia. Clique para ativar'"
                                                    :class="{ 'is-on': form.requires_attunement }"
                                                    @click="form.requires_attunement = !form.requires_attunement">
                                                    <img class="vault-v247-crystal" src="{{ asset('images/vault/icons/attunement-crystal.png') }}" alt="" aria-hidden="true">
                                                    <span x-text="form.requires_attunement ? 'Sintonizável' : 'Sem sintonia'"></span>
                                                    <i class="vault-v46-attunement-mark" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- O modo Vestígio continua usando as mesmas regras e campos. --}}
                                        <div x-show="form.type === 'wonderful' && form.rarity === 'artifact'" x-cloak
                                            class="vault-v16-mode-switch vault-v18-vestige">
                                            <div class="vault-v16-mode-switch__text">
                                                <span class="vault-v16-mode-switch__eyebrow">Artefato especial</span>
                                                <strong>Vestígio</strong>
                                                <p>Substitui habilidades convencionais por três etapas de poder.</p>
                                            </div>
                                            <button type="button" role="switch" :aria-checked="editingVestige"
                                                class="master-vault-v4-switch" :class="{ 'is-on': editingVestige }"
                                                @click="toggleVestige()" aria-label="Alternar modo Vestígio"></button>
                                        </div>
                                        </div>
                                    </section>

                                    <details class="vault-v45-inventory-details" x-ref="vaultEditorInventory">
                                        <summary class="vault-v45-inventory-trigger">
                                            <span class="vault-v45-inventory-summary-title">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 7a3 3 0 1 1 6 0"/><path d="M7.3 8h9.4l3.15 12H4.15L7.3 8Z"/><path d="M10 12h4M12 10v4"/></svg>
                                                <strong>Quantidade e peso</strong>
                                            </span>
                                            <span class="vault-v45-inventory-summary-value" x-text="'Qtd. ' + (form.default_quantity || 1) + (form.weight !== null && form.weight !== '' ? ' · ' + formatWeightKg(form.weight) : ' · Peso opcional')"></span>
                                            <svg class="vault-v45-inventory-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
                                        </summary>
                                        <div class="vault-v18-fields vault-v18-inventory-fields vault-v45-inventory-fields">
                                            <label class="master-vault-v4-field">
                                                <span>Quantidade padrão</span>
                                                <input type="number" min="1" max="999999"
                                                    x-model.number="form.default_quantity" class="master-vault-v4-input">
                                            </label>
                                            <label class="master-vault-v4-field">
                                                <span class="vault-v45-weight-label">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 7a3 3 0 1 1 6 0"/><path d="M7.5 8h9L20 20H4L7.5 8Z"/></svg>
                                                    Peso
                                                </span>
                                                <div class="vault-v46-weight-control">
                                                    <input type="text" inputmode="numeric" autocomplete="off" maxlength="24"
                                                        :value="weightGramsText"
                                                        @input="setWeightFromGrams($event)"
                                                        placeholder="0,000" aria-label="Peso em kg. Digite o valor em gramas; a vírgula é inserida automaticamente"
                                                        title="Digite números em gramas: 100 equivale a 0,100 kg; 1000 equivale a 1,000 kg"
                                                        class="master-vault-v4-input vault-v248-weight-input">
                                                    <span class="vault-v46-weight-unit" aria-hidden="true">kg</span>
                                                </div>
                                            </label>
                                        </div>
                                    </details>


                                </div>
                                    <details open class="vault-v18-card vault-v18-card--description vault-v45-description vault-v247-description-details" x-ref="vaultEditorDescription">
                                        <summary class="vault-v18-card-heading vault-v247-description-summary">
                                            <h3>Descrição do Item</h3>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                        </summary>
                                        <div class="master-vault-v4-field vault-v31-rich-field" x-data="vaultTiptapField(() => form, 'description', 30000, 'Descrição pública do item')">
        <div class="vault-v31-rich-toolbar" role="toolbar" aria-label="Formatar descrição pública">
            <div class="vault-v31-rich-actions">
                <button type="button" title="Negrito (Ctrl+B)" aria-label="Negrito" @mousedown.prevent @click="action('bold')" :class="{ 'is-active': active('bold') }"><b>B</b></button>
                <button type="button" title="Itálico (Ctrl+I)" aria-label="Itálico" @mousedown.prevent @click="action('italic')" :class="{ 'is-active': active('italic') }"><i>I</i></button>
                <button type="button" title="Riscado" aria-label="Riscado" @mousedown.prevent @click="action('strike')" :class="{ 'is-active': active('strike') }"><s>S</s></button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Subtítulo" aria-label="Subtítulo" @mousedown.prevent @click="action('heading')" :class="{ 'is-active': active('heading') }">T</button>
                <button type="button" title="Lista com marcadores" aria-label="Lista com marcadores" @mousedown.prevent @click="action('bulletList')" :class="{ 'is-active': active('bulletList') }">• ≡</button>
                <button type="button" title="Lista numerada" aria-label="Lista numerada" @mousedown.prevent @click="action('orderedList')" :class="{ 'is-active': active('orderedList') }">1. ≡</button>
                <button type="button" title="Citação" aria-label="Citação" @mousedown.prevent @click="action('blockquote')" :class="{ 'is-active': active('blockquote') }">❝</button>
                <button type="button" title="Linha divisória (ou digite --- e Enter)" aria-label="Inserir linha divisória" @mousedown.prevent @click="action('horizontalRule')">―</button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Desfazer (Ctrl+Z)" aria-label="Desfazer" @mousedown.prevent @click="action('undo')">↶</button>
                <button type="button" title="Refazer" aria-label="Refazer" @mousedown.prevent @click="action('redo')">↷</button>
                <button type="button" title="Limpar formatação" aria-label="Limpar formatação" @mousedown.prevent @click="action('clear')">Tx</button>
            </div>
        </div>
        <div class="vault-v31-rich-canvas">
            <div x-ref="fallbackHost" x-show="!mounted" x-cloak
                 @input="syncFallback()" @paste="pasteFallback($event)"
                 @keydown="fallbackHotkey($event)">
                <div x-ignore data-vault-fallback contenteditable="true"
                     role="textbox" aria-multiline="true" spellcheck="true"
                     class="vault-v31-fallback-input" aria-label="Campo de texto do item"></div>
            </div>
            <div x-show="mounted" x-cloak>
                <div x-ref="editorSurface" class="vault-v31-rich-surface" x-on:click.self="richEditor?.focus()">
                    <div x-ignore data-vault-tiptap-mount></div>
                </div>
            </div>
        </div>
        <div class="vault-v31-rich-foot">
            <span>Escreva normalmente e formate somente o que desejar.</span>
            <span :class="{ 'is-over': contentLength > maxLength }" x-text="contentLength + ' / ' + maxLength"></span>
        </div>
    </div>
                                    </details>
                            </div>

                            {{-- =====================================================
                                 CATEGORIA DO ITEM
                            ====================================================== --}}
                            <section x-ref="vaultEditorMechanics" class="vault-v18-mechanics">
                                <div class="master-vault-v5-category-shell">
                                    <header class="master-vault-v5-category-head">
                                        <strong>Categoria do Item</strong>
                                        <p>Escolha o tipo para configurar suas propriedades.</p>
                                    </header>

                                    <div class="master-vault-v5-category-grid vault-v19-category-grid">
                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'weapon' }"
                                            @click="setItemCategory('weapon')"
                                        >
                                            <span class="master-vault-v5-category-icon vault-v249-weapon-icon"><img class="vault-v247-category-image vault-v249-weapon-image" src="{{ asset('images/vault/icons/editor-weapon.png') }}" alt="" aria-hidden="true"></span>

                                            <span>
                                                <strong>Arma</strong>
                                                <span>Dano, bônus e maestrias.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'armor' }"
                                            @click="setItemCategory('armor')"
                                        >
                                            <span class="master-vault-v5-category-icon"><img class="vault-v247-category-image" src="{{ asset('images/vault/icons/editor-armor.png') }}" alt="" aria-hidden="true"></span>

                                            <span>
                                                <strong>Armadura</strong>
                                                <span>Proteção e Classe de Armadura.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'shield' }"
                                            @click="setItemCategory('shield')"
                                        >
                                            <span class="master-vault-v5-category-icon"><img class="vault-v247-category-image" src="{{ asset('images/vault/icons/editor-shield.png') }}" alt="" aria-hidden="true"></span>

                                            <span>
                                                <strong>Escudo</strong>
                                                <span>Defesa e bônus mágico.</span>
                                            </span>
                                        </button>

                                        <button
                                            type="button"
                                            class="master-vault-v5-category"
                                            :class="{ 'is-active': itemCategory() === 'other' }"
                                            @click="setItemCategory('other')"
                                        >
                                            <span class="master-vault-v5-category-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 8 4.6v9L12 21l-8-4.4v-9L12 3Z"/><path d="M4 7.6 12 12l8-4.4M12 12v9"/></svg></span>

                                            <span>
                                                <strong>Outro Item</strong>
                                                <span>Ferramenta, consumível ou acessório.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="master-vault-v5-category vault-v247-scroll-category"
                                            :class="{ 'is-active': itemCategory() === 'spell_scroll' }"
                                            :aria-pressed="itemCategory() === 'spell_scroll'"
                                            @click="setItemCategory('spell_scroll')">
                                            <span class="master-vault-v5-category-icon"><img class="vault-v247-category-image" src="{{ asset('images/vault/icons/editor-spell-scroll.png') }}" alt="" aria-hidden="true"></span>
                                            <span><strong>Pergaminho de Feitiço</strong><span>Vincule um feitiço do grimório.</span></span>
                                        </button>
                                    </div>
                                </div>


                                {{-- V66.2.67 — busca rápida e biblioteca completa em modal independente. --}}
                                <template x-if="itemCategory() === 'spell_scroll'">
                                <article class="master-vault-v4-module vault-v266-scroll-editor vault-v267-scroll-editor">
                                    <header class="master-vault-v4-module-head vault-v266-scroll-head">
                                        <div>
                                            <span class="vault-v260-eyebrow">Biblioteca arcana · Pergaminhos</span>
                                            <h3>Pergaminho de Feitiço</h3>
                                        </div>
                                        <button type="button" class="master-vault-v4-remove" @click="setItemCategory('other')">Limpar Categoria</button>
                                    </header>
                                    <div class="master-vault-v4-module-body vault-v266-scroll-body vault-v267-scroll-body">
                                        <section class="vault-v267-quick-card">
                                            <div class="vault-v267-quick-title">
                                                <span class="vault-v267-quick-emblem" aria-hidden="true">
                                                    <img src="{{ asset('images/vault/icons/editor-spell-scroll.png') }}" alt="">
                                                </span>
                                                <div><strong>Feitiço do pergaminho</strong><small>Pesquise pelo nome ou abra a biblioteca para explorar o catálogo.</small></div>
                                                <span class="vault-v267-base-tag">Edição 2024</span>
                                            </div>
                                            <div class="vault-v267-quick-controls">
                                                <div class="vault-v267-quick-search-wrap" @click.outside="scrollQuickFocused=false">
                                                    <label class="vault-v267-quick-search">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.7" cy="10.7" r="6.2"/><path d="m15.5 15.5 5 5" stroke-linecap="round"/></svg>
                                                        <input type="search" x-model="scrollQuickSearch"
                                                               @focus="scrollQuickFocused=true"
                                                               @keydown.escape.stop="scrollQuickFocused=false"
                                                               @keydown.enter.prevent="scrollQuickMatches.length && selectScrollSpell(scrollQuickMatches[0])"
                                                               placeholder="Digite o nome do feitiço..." aria-label="Pesquisar feitiços para o pergaminho" autocomplete="off">
                                                    </label>
                                                    <div class="vault-v267-suggestions" x-show="scrollQuickFocused && scrollQuickSearch.trim().length > 0" x-cloak>
                                                        <template x-for="spell in scrollQuickMatches" :key="'quick-scroll-'+spell.id">
                                                            <button type="button" class="vault-v267-suggestion-row" @click="selectScrollSpell(spell)">
                                                                <span><strong x-text="spell.name"></strong><small x-text="scrollSchoolLabel(spell.school)"></small></span>
                                                                <span class="vault-v267-suggestion-meta"><b x-text="spell.level_label"></b><small x-text="spell.source"></small></span>
                                                            </button>
                                                        </template>
                                                        <div class="vault-v267-suggestion-empty" x-show="scrollQuickMatches.length===0">Nenhum feitiço encontrado. Abra a biblioteca para ativar fontes extras.</div>
                                                    </div>
                                                </div>
                                                <button type="button" class="vault-v267-open-book" @click="openScrollLibrary()"
                                                        title="Abrir biblioteca completa de feitiços" aria-label="Abrir biblioteca de feitiços">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3.8 5.5h7a3.3 3.3 0 0 1 3.2 3.2v10.8a3.3 3.3 0 0 0-3.2-3.2h-7V5.5ZM20.2 5.5h-4a3.3 3.3 0 0 0-3.2 3.2M20.2 5.5v10.8h-4a3.3 3.3 0 0 0-3.2 3.2" stroke-linejoin="round"/></svg>
                                                    <span>Abrir biblioteca</span>
                                                </button>
                                            </div>
                                            <div class="vault-v267-selection" x-show="form.properties.spell_scroll?.spell_name" x-cloak>
                                                <span class="vault-v267-selection-mark" aria-hidden="true">✓</span>
                                                <div class="vault-v267-selection-text">
                                                    <small>Feitiço selecionado</small>
                                                    <strong x-text="form.properties.spell_scroll?.spell_name"></strong>
                                                </div>
                                                <span class="vault-v267-selection-level" x-text="form.properties.spell_scroll?.spell_level === 0 ? 'Truque' : (form.properties.spell_scroll?.spell_level ? form.properties.spell_scroll.spell_level + 'º nível' : '—')"></span>
                                                <button type="button" class="vault-v267-change-link" @click="openScrollLibrary()">Trocar</button>
                                            </div>
                                            <div class="vault-v275-spell-defaults" x-show="form.properties.spell_scroll?.spell_name && scrollSpellDefaultRarity(form.properties.spell_scroll?.spell_level)" x-cloak>
                                                <span><strong>Raridade:</strong> <span x-text="scrollSpellRarityLabel(scrollSpellDefaultRarity(form.properties.spell_scroll?.spell_level))"></span></span>
                                                <span x-text="scrollCastDefaultTitle(form.properties.spell_scroll).replace(form.properties.spell_scroll?.spell_name || '', '').replace(/[()]/g, '').trim()"></span>
                                                <button type="button" x-show="form.rarity !== scrollSpellDefaultRarity(form.properties.spell_scroll?.spell_level)" @click="applyScrollRecommendedRarity(null, true)">Aplicar raridade sugerida</button>
                                            </div>
                                            <div class="vault-v273-casting-note vault-v274-scroll-abilities" x-show="form.properties.spell_scroll?.spell_name" x-cloak>
                                                <span class="vault-v273-casting-icon" aria-hidden="true">✦</span>
                                                <div><strong>Uso e Conjuração de Pergaminhos</strong>
                                                    <small x-text="scrollCastUsageCaption()"></small>
                                                    <span class="vault-v274-spell-line" x-text="scrollCastDefaultTitle(form.properties.spell_scroll)"></span>
                                                </div>
                                                <div class="vault-v274-scroll-actions">
                                                    <button type="button" @click="openScrollCastFeature('rules')">Editar regras e usos</button>
                                                    <button type="button" @click="openScrollCastFeature('spell')">Editar feitiço</button>
                                                </div>
                                            </div>
                                            <details class="vault-v274-scroll-stats" x-show="form.properties.spell_scroll?.spell_name" x-cloak>
                                                <summary>Personalizar CD e bônus de ataque <span x-text="'· ' + scrollCastDefaultTitle(form.properties.spell_scroll)"></span></summary>
                                                <div class="vault-v274-scroll-stats-grid">
                                                    <label><span>CD do pergaminho</span>
                                                        <input type="number" min="1" max="99" step="1"
                                                               x-model.number="form.properties.spell_scroll.spell_dc" @change="syncScrollCastFeature()"
                                                               :placeholder="scrollSpellDefaultStats(form.properties.spell_scroll.spell_level).dc ?? '—'">
                                                    </label>
                                                    <label><span>Bônus de ataque</span>
                                                        <input type="number" min="-30" max="99" step="1"
                                                               x-model.number="form.properties.spell_scroll.spell_attack_bonus" @change="syncScrollCastFeature()"
                                                               :placeholder="scrollSpellDefaultStats(form.properties.spell_scroll.spell_level).attack ?? '—'">
                                                    </label>
                                                    <button type="button" @click="form.properties.spell_scroll.spell_dc=null;form.properties.spell_scroll.spell_attack_bonus=null;syncScrollCastFeature()">Restaurar padrão</button>
                                                </div>
                                            </details>
                                            <p class="vault-v267-quick-empty" x-show="!form.properties.spell_scroll?.spell_name" x-cloak>
                                                Nenhum feitiço vinculado. <span x-text="scrollBaseCount ? (scrollBaseCount + ' feitiços oficiais disponíveis.') : 'Não há feitiços oficiais importados neste Grimório.'"></span>
                                            </p>
                                        </section>
                                        <details class="vault-v266-manual vault-v267-manual">
                                            <summary>Feitiço personalizado / preenchimento manual <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true"><path d="m5 7.5 5 5 5-5" stroke-width="1.6" stroke-linecap="round"/></svg></summary>
                                            <div class="vault-v247-spell-manual">
                                                <label class="master-vault-v4-field"><span>Nome do feitiço</span>
                                                    <input type="text" class="master-vault-v4-input" maxlength="160"
                                                           x-model="form.properties.spell_scroll.spell_name" placeholder="Nome do feitiço"
                                                           @input="clearScrollLinkForManualInput()" @change="syncScrollCastFeature()"></label>
                                                <label class="master-vault-v4-field"><span>Nível</span>
                                                    <select class="master-vault-v4-select" x-model.number="form.properties.spell_scroll.spell_level" @change="syncScrollLevelAndRarity()">
                                                        <option value="">Não definido</option>
                                                        <template x-for="level in [0,1,2,3,4,5,6,7,8,9]" :key="'scroll-level-'+level">
                                                            <option :value="level" x-text="level === 0 ? 'Truque' : level + 'º nível'"></option>
                                                        </template>
                                                    </select>
                                                </label>
                                            </div>
                                        </details>
                                    </div>

                                    {{-- V66.2.70 — Biblioteca em duas abas, sem painéis empilhados. --}}
                                    <template x-teleport="body">
                                        <div class="vault-v267-library-layer vault-v270-library-layer" x-show="scrollBookModalOpen" x-cloak
                                             @keydown.escape.stop.prevent="closeScrollLibrary()" role="presentation">
                                            <div class="vault-v267-library-backdrop" @click="closeScrollLibrary()"></div>
                                            <section class="vault-v267-library-dialog vault-v270-library-dialog" role="dialog" aria-modal="true" aria-label="Biblioteca de feitiços" tabindex="-1">
                                                <header class="vault-v267-library-head vault-v270-head">
                                                    <div class="vault-v267-library-head-title">
                                                        <span class="vault-v267-library-emblem" aria-hidden="true">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5.5h7a3.1 3.1 0 0 1 3 3v11a3 3 0 0 0-3-3H4V5.5Zm16 0h-4a3 3 0 0 0-3 3m7-3v11h-4a3 3 0 0 0-3 3"/></svg>
                                                        </span>
                                                        <div><small>OFICINA DO MESTRE · BIBLIOTECA ARCANA</small><h3>Escolher feitiço</h3><p>Pesquise no catálogo ou explore os livros da campanha.</p></div>
                                                    </div>
                                                    <button type="button" class="vault-v267-library-close" @click="closeScrollLibrary()" aria-label="Fechar biblioteca">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6 18 18M18 6 6 18" stroke-linecap="round"/></svg>
                                                    </button>
                                                </header>
                                                <nav class="vault-v270-tabs" aria-label="Seções da biblioteca">
                                                    <button type="button" :class="{'is-current':scrollLibraryTab==='spells'}" :aria-current="scrollLibraryTab==='spells'?'page':null" @click="scrollLibraryTab='spells'">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 5h7a3 3 0 0 1 3 3v11a3 3 0 0 0-3-3H4V5Zm16 0h-4a3 3 0 0 0-3 3m7-3v11h-4a3 3 0 0 0-3 3"/></svg>
                                                        Feitiços <small x-text="scrollBaseCount + ' oficiais'"></small>
                                                    </button>
                                                    <button type="button" :class="{'is-current':scrollLibraryTab==='books'}" :aria-current="scrollLibraryTab==='books'?'page':null" @click="scrollLibraryTab='books'">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h16v16H4zM8 4v16M12 8h5M12 12h5"/></svg>
                                                        Livros e campanhas <small x-text="scrollExtraBooks.length ? scrollExtraBooks.length + ' ativo(s)' : 'Opcionais'"></small>
                                                    </button>
                                                    <span class="vault-v270-tab-note" x-text="scrollActiveCampaign ? 'Campanha: '+scrollActiveCampaign.name : 'Catálogo oficial ativo'"></span>
                                                </nav>

                                                <div class="vault-v270-spell-tab" x-show="scrollLibraryTab==='spells'" x-cloak>
                                                    <div class="vault-v267-library-filters vault-v270-filters">
                                                        <div class="vault-v269-filter-search-wrap">
                                                            <label class="vault-v267-library-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6"/><path d="m15 15 5 5"/></svg>
                                                                <input x-ref="scrollLibrarySearch" type="search" x-model.debounce.100ms="spellSearch" placeholder="Nome do feitiço..." aria-label="Buscar feitiço na biblioteca" autocomplete="off">
                                                            </label>
                                                            <button type="button" class="vault-v269-clear-spell-search" x-show="spellSearch.length" x-cloak @click="spellSearch='';scrollListLimit=60" aria-label="Limpar pesquisa">×</button>
                                                        </div>
                                                        <select class="vault-v267-filter" x-model="scrollLevelFilter" aria-label="Filtrar por nível"><option value="all">Todos os níveis</option><option value="0">Truques</option><template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'library-level-'+n"><option :value="String(n)" x-text="n+'º nível'"></option></template></select>
                                                        <select class="vault-v267-filter" x-model="scrollSchoolFilter" aria-label="Filtrar por escola"><option value="all">Todas as escolas</option><template x-for="school in scrollSchoolOptions" :key="'library-school-'+school"><option :value="school" x-text="scrollSchoolLabel(school)"></option></template></select>
                                                    </div>
                                                    <div class="vault-v267-library-content vault-v270-spell-content">
                                                        <div class="vault-v267-library-list-pane vault-v270-list-pane">
                                                            <div class="vault-v267-list-title"><strong>Feitiços disponíveis</strong><span x-text="scrollVisibleCount + ' resultado(s)'"></span></div>
                                                            <div class="vault-v267-library-list">
                                                                <template x-for="spell in displayedScrollSpells" :key="'library-spell-'+spell.id">
                                                                    <button type="button" class="vault-v267-library-spell vault-v270-spell-row" @click="previewScrollSpell(spell)"
                                                                            :class="{'is-active':String(scrollPreviewId)===String(spell.id), 'is-linked':String(form.properties.spell_scroll?.spell_id)===String(spell.id)}">
                                                                        <span class="vault-v267-spell-name"><strong x-text="spell.name"></strong><small x-text="scrollSchoolLabel(spell.school)"></small></span>
                                                                        <span class="vault-v267-spell-meta"><b x-text="spell.level_label"></b><small x-text="spell.source"></small></span>
                                                                    </button>
                                                                </template>
                                                                <p class="vault-v267-library-empty" x-show="!scrollVisibleCount">Nenhum feitiço encontrado. Ajuste a busca ou escolha um livro adicional.</p>
                                                            </div>
                                                            <button type="button" class="vault-v267-load-more" x-show="scrollVisibleCount>scrollListLimit" x-cloak
                                                                    @click="scrollListLimit+=60" x-text="'Mostrar mais ('+Math.min(60,scrollVisibleCount-scrollListLimit)+')'"></button>
                                                        </div>
                                                        <div class="vault-v267-library-preview vault-v270-preview-pane">
                                                            <template x-if="scrollPreviewSpell">
                                                                <div class="vault-v270-spell-detail">
                                                                    <div class="vault-v270-spell-paper" x-ref="scrollSpellDetail"
                                                                         @mouseover="positionScrollRuleTooltip($event)" @focusin="positionScrollRuleTooltip($event)">
                                                                        <div class="vault-v272-reader">
                                                                            {{-- Leitor do Grimório do Player: título, subtítulo, dados e texto rico. --}}
                                                                            <div class="vault-v272-identity">
                                                                                <div class="vault-v272-title-line">
                                                                                    <h4 x-text="scrollPreviewSpell.name"></h4>
                                                                                    <span class="vault-v272-source" x-text="scrollPreviewSpell.source_label || scrollPreviewSpell.source"></span>
                                                                                </div>
                                                                                <p class="vault-v272-subtitle">
                                                                                    <span x-text="scrollPreviewSpell.subtitle_pt || (scrollPreviewSpell.level === 0 ? 'Truque (' + scrollSchoolLabel(scrollPreviewSpell.school) + ')' : scrollPreviewSpell.level + 'º círculo (' + scrollSchoolLabel(scrollPreviewSpell.school) + ')')"></span>
                                                                                    <span class="vault-v272-ritual" x-show="scrollPreviewSpell.ritual && scrollPreviewSpell._detail_loaded" x-cloak>Ritual</span>
                                                                                </p>
                                                                            </div>
                                                                            <p class="vault-v270-no-description" x-show="scrollPreviewSpell._detail_loading" x-cloak>Carregando o verbete do Grimório...</p>
                                                                            <div class="vault-v270-no-description" x-show="scrollPreviewSpell._detail_error && !scrollPreviewSpell._detail_loading" x-cloak>
                                                                                Não foi possível consultar a descrição.
                                                                                <button type="button" @click="loadScrollSpellDetail(scrollPreviewSpell)">Tentar novamente</button>
                                                                            </div>
                                                                            <template x-if="scrollPreviewSpell._detail_loaded">
                                                                                <div>
                                                                                    <dl class="vault-v272-facts">
                                                                                        <div><dt>Conjuração:</dt><dd x-text="scrollPreviewSpell.casting_time_display || '—'"></dd></div>
                                                                                        <div><dt>Alcance:</dt><dd x-text="scrollPreviewSpell.range_display || '—'"></dd></div>
                                                                                        <div><dt>Componentes:</dt><dd x-text="scrollPreviewSpell.components_display || '—'"></dd></div>
                                                                                        <div><dt>Duração:</dt><dd><span x-text="scrollPreviewSpell.duration_display || '—'"></span><span x-show="scrollPreviewSpell.concentration"> (Concentração)</span></dd></div>
                                                                                    </dl>
                                                                                    <div class="vault-v272-rich-copy spell-reader-copy spell-rich-copy" x-show="scrollPreviewSpell.description_html" x-html="scrollPreviewSpell.description_html"></div>
                                                                                    <p class="vault-v270-no-description" x-show="!scrollPreviewSpell.description_html">Este feitiço não possui descrição cadastrada no Grimório.</p>
                                                                                    <section class="vault-v272-higher" x-show="scrollPreviewSpell.higher_level_html || scrollPreviewSpell.scaling_level_dice.length" x-cloak>
                                                                                        <p class="vault-v272-higher-line" x-show="scrollPreviewSpell.higher_level_html">
                                                                                            <strong x-text="(scrollPreviewSpell.higher_level_label || 'Em Níveis Superiores') + ':'"></strong>
                                                                                            <span class="vault-v272-rich-copy spell-reader-copy spell-rich-copy" x-html="scrollPreviewSpell.higher_level_html"></span>
                                                                                        </p>
                                                                                        <template x-if="scrollPreviewSpell.scaling_level_dice.length">
                                                                                            <div class="vault-v272-scaling">
                                                                                                <template x-for="(progression, index) in scrollPreviewSpell.scaling_level_dice" :key="'scroll-scaling-'+index">
                                                                                                    <p><strong x-text="progression.label"></strong>
                                                                                                        <template x-for="(row, n) in (progression.levels || [])" :key="'scroll-progression-'+n">
                                                                                                            <span><span x-show="n > 0"> · </span><span x-text="'Nv. ' + row.level + ' — ' + row.value"></span></span>
                                                                                                        </template>
                                                                                                    </p>
                                                                                                </template>
                                                                                            </div>
                                                                                        </template>
                                                                                    </section>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                    <div class="vault-v270-preview-action">
                                                                        <small x-text="String(form.properties.spell_scroll?.spell_id)===String(scrollPreviewSpell.id)?'Feitiço já vinculado a este pergaminho':'O texto permanece no Grimório; o pergaminho salva a referência.'"></small>
                                                                        <button type="button" class="vault-v267-link-spell" @click="selectScrollSpell(scrollPreviewSpell)" x-text="String(form.properties.spell_scroll?.spell_id)===String(scrollPreviewSpell.id)?'Confirmar feitiço':'Vincular ao pergaminho'"></button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <div class="vault-v267-preview-empty vault-v270-preview-empty" x-show="!scrollPreviewSpell"><strong>Selecione um feitiço</strong><span>O verbete completo será exibido aqui, conforme os dados do Grimório.</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="vault-v270-books-tab" x-show="scrollLibraryTab==='books'" x-cloak>
                                                    <div class="vault-v270-campaign-bar">
                                                        <div class="vault-v270-section-label"><strong>Livros de uma campanha</strong><small>Carregue as fontes da campanha, sem alterar as configurações dela.</small></div>
                                                        <div class="vault-v270-campaign-choices">
                                                            <button type="button" :class="{'is-active':!scrollCampaignId && !scrollExtraBooks.length}" @click="applyScrollCampaign('')">Somente oficiais</button>
                                                            <template x-for="campaign in spellCampaigns" :key="'scroll-campaign-'+campaign.id">
                                                                <button type="button" :class="{'is-active':scrollCampaignId===campaign.id}" @click="applyScrollCampaign(campaign.id)" :title="campaign.name">
                                                                    <span x-text="campaign.name"></span><small x-text="campaign.allow_all ? 'Todas as fontes' : campaign.sources.length+' fonte(s)'"></small>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        <small x-show="!spellCampaigns.length" x-cloak>Sem campanhas cadastradas para este mestre.</small>
                                                    </div>
                                                    <div class="vault-v270-books-workspace">
                                                        <div class="vault-v270-book-browser">
                                                            <div class="vault-v270-book-browser-head"><strong>Livros adicionais</strong><span x-text="scrollExtraBooks.length+' ativo(s)'"></span></div>
                                                            <label class="vault-v270-book-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/></svg><input type="search" x-model.debounce.120ms="scrollBookSearch" placeholder="Buscar livro..." aria-label="Pesquisar livros"></label>
                                                            <div class="vault-v270-book-results">
                                                                <template x-for="book in filteredScrollBookOptions" :key="'book-'+book.code">
                                                                    <div class="vault-v270-book-line" :class="{'is-selected':scrollInspectBookKey===book.code,'is-enabled':scrollExtraBooks.includes(book.code)}">
                                                                        <button type="button" class="vault-v270-book-explore" @click="inspectScrollBook(book.code)" :title="'Ver feitiços de '+book.label">
                                                                            <strong x-text="book.label"></strong><small x-text="book.total+' feitiço(s)'"></small>
                                                                        </button>
                                                                        <button type="button" class="vault-v270-book-enable" :aria-label="(scrollExtraBooks.includes(book.code)?'Desativar ':'Ativar ')+book.label" :aria-pressed="scrollExtraBooks.includes(book.code)" @click="toggleScrollBook(book.code)" x-text="scrollExtraBooks.includes(book.code)?'✓':'+'"></button>
                                                                    </div>
                                                                </template>
                                                                <p class="vault-v267-books-empty" x-show="!filteredScrollBookOptions.length">Nenhum livro encontrado.</p>
                                                            </div>
                                                            <button type="button" class="vault-v270-clear-books" x-show="scrollExtraBooks.length" @click="clearScrollExtraBooks()">Desativar livros extras</button>
                                                        </div>
                                                        <div class="vault-v270-book-detail">
                                                            <template x-if="scrollInspectedBook">
                                                                <div class="vault-v270-inspector-layout">
                                                                    <header class="vault-v270-inspector-title"><small>CONTEÚDO DO LIVRO</small><h4 x-text="scrollInspectedBook.label"></h4><span x-text="scrollInspectedBook.total+' feitiço(s)'"></span>
                                                                        <button type="button" :class="{'is-active':scrollExtraBooks.includes(scrollInspectedBook.code)}" @click="toggleScrollBook(scrollInspectedBook.code)" x-text="scrollExtraBooks.includes(scrollInspectedBook.code)?'✓ Livro habilitado':'Habilitar livro'"></button>
                                                                    </header>
                                                                    <label class="vault-v270-inspector-search"><input type="search" x-model.debounce.120ms="scrollInspectSearch" placeholder="Encontrar feitiço neste livro..." aria-label="Buscar feitiço no livro"></label>
                                                                    <div class="vault-v270-inspector-list">
                                                                        <template x-for="spell in displayedScrollInspectedSpells" :key="'book-spell-'+spell.id">
                                                                            <button type="button" @click="previewSpellFromBook(spell)"><span><strong x-text="spell.name"></strong><small x-text="scrollSchoolLabel(spell.school)"></small></span><span x-text="spell.level_label"></span></button>
                                                                        </template>
                                                                        <p x-show="!scrollInspectedBookSpells.length" class="vault-v267-books-empty">Nenhum feitiço corresponde à pesquisa neste livro.</p>
                                                                    </div>
                                                                    <button type="button" class="vault-v267-load-more" x-show="scrollInspectedBookSpells.length>scrollInspectLimit" x-cloak @click="scrollInspectLimit+=30">Mostrar mais feitiços</button>
                                                                </div>
                                                            </template>
                                                            <div class="vault-v270-inspector-empty" x-show="!scrollInspectedBook"><strong>Escolha um livro</strong><span>Veja os feitiços que ele contém e ative-o apenas se quiser incluí-lo no catálogo.</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <footer class="vault-v267-library-footer vault-v270-footer"><span x-text="scrollLibraryTab==='spells' ? scrollVisibleCount+' feitiço(s) nos filtros atuais' : scrollExtraBooks.length+' livro(s) adicional(is) habilitado(s)'"></span><button type="button" @click="scrollLibraryTab==='books' ? scrollLibraryTab='spells' : closeScrollLibrary()" x-text="scrollLibraryTab==='books'?'Voltar aos feitiços':'Voltar ao editor'"></button></footer>
                                            </section>
                                        </div>
                                    </template>
                                </article>
                                </template>

                                {{-- ARMA --}}
                                <article
                                    x-show="itemCategory() === 'weapon'"
                                    x-cloak
                                    class="master-vault-v4-module vault-v19-equipment vault-v250-weapon"
                                >
                                    <header class="master-vault-v4-module-head">
                                        <div>
                                            <span class="vault-v260-eyebrow">Combate · Ofensiva</span>
                                            <h3>Arma</h3>

                                            <p>
                                                Dano, propriedades e maestrias em uma única configuração.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            class="master-vault-v4-remove"
                                            @click="setItemCategory('other')"
                                        >
                                            Limpar Categoria
                                        </button>
                                    </header>

                                    <div class="master-vault-v4-module-body">
                                        <section class="master-vault-v4-rule">
                                            <p class="master-vault-v4-rule-title">
                                                Tipo de Arma
                                            </p>

                                            <div class="master-vault-v4-choice-grid is-3 mt-2 vault-v19-options vault-v19-options--weapon">
                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice vault-v19-option"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'simple' }"
                                                    @click="form.properties.weapon.category = 'simple'"
                                                >
                                                    <strong>Simples</strong><small>Treinamento básico</small>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice vault-v19-option"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'martial' }"
                                                    @click="form.properties.weapon.category = 'martial'"
                                                >
                                                    <strong>Marcial</strong><small>Uso especializado</small>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-choice vault-v19-option"
                                                    :class="{ 'is-active': form.properties.weapon.category === 'custom' }"
                                                    @click="form.properties.weapon.category = 'custom'"
                                                >
                                                    <strong>Personalizada</strong><small>Crie seu próprio tipo</small>
                                                </button>
                                            </div>

                                            <input
                                                x-show="form.properties.weapon.category === 'custom'"
                                                x-cloak
                                                type="text"
                                                maxlength="120"
                                                x-model="form.properties.weapon.category_custom"
                                                placeholder="Ex.: Arma Exótica"
                                                class="master-vault-v4-input mt-2 sm:max-w-lg"
                                            >


                                            <label class="master-vault-v4-field vault-v250-model">
                                                <span>Modelo / Tipo</span>
                                                <input type="text" maxlength="120"
                                                       x-model="form.properties.weapon.weapon_type"
                                                       placeholder="Ex.: Espada Longa, Machado, Cajado..."
                                                       class="master-vault-v4-input">
                                            </label>
                                        </section>

                                        <section class="master-vault-v4-rule vault-v250-traits">
                                            <div class="vault-v250-rule-heading">
                                                <div>
                                                    <p class="master-vault-v4-rule-title">Propriedades da Arma</p>
                                                    <p class="master-vault-v4-rule-copy">Selecione as propriedades e consulte suas regras nas gavetas abaixo.</p>
                                                </div>
                                                <span class="vault-v250-count"
                                                      x-text="form.properties.weapon.traits.length + ' selecionada' + (form.properties.weapon.traits.length === 1 ? '' : 's')"></span>
                                            </div>

                                            <div class="master-vault-v4-chip-row">
                                                <template
                                                    x-for="definition in weaponTraitDefinitions"
                                                    :key="definition.key"
                                                >
                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-chip-button vault-v250-choice"
                                                        :class="{ 'is-active': traitSelected(definition.key) }"
                                                        :title="definition.description"
                                                        :aria-pressed="traitSelected(definition.key)"
                                                        @click="toggleTrait(definition)"
                                                    ><span class="vault-v250-choice-indicator" aria-hidden="true">✓</span>
                                                     <span x-text="definition.key === 'loading' && traitSelected('loading') && form.properties.weapon.loading_capacity > 0 ? 'Carregamento ('+form.properties.weapon.loading_capacity+'/'+form.properties.weapon.loading_capacity+')' : definition.name"></span></button>
                                                </template>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-chip-button vault-v250-choice vault-v250-choice--custom"
                                                    @click="addCustomTrait()"
                                                >
                                                    + Custom
                                                </button>
                                            </div>

                                            <div
                                                x-show="form.properties.weapon.traits.some(trait => !trait.custom)"
                                                x-cloak
                                                class="master-vault-v10-8-selected"
                                            >
                                                <div class="master-vault-v10-8-selected-head">
                                                    <strong>Propriedades em uso</strong>

                                                    <span
                                                        x-text="
                                                            form.properties.weapon.traits
                                                                .filter(trait => !trait.custom)
                                                                .length
                                                            + (
                                                                form.properties.weapon.traits
                                                                    .filter(trait => !trait.custom)
                                                                    .length === 1
                                                                    ? ' propriedade'
                                                                    : ' propriedades'
                                                            )
                                                        "
                                                    ></span>
                                                </div>

                                                <div class="master-vault-v10-8-drawer-list">
                                                    <template
                                                        x-for="
                                                            trait in
                                                            form.properties.weapon.traits.filter(
                                                                current => !current.custom
                                                            )
                                                        "
                                                        :key="'standard-trait-' + trait.key"
                                                    >
                                                        <article
                                                            x-data="{ open: false }"
                                                            class="master-vault-v10-8-drawer"
                                                        >
                                                            <header class="master-vault-v10-8-drawer-head">
                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-toggle"
                                                                    @click="open = !open"
                                                                    :aria-expanded="open"
                                                                >
                                                                    <svg
                                                                        class="master-vault-v10-8-chevron"
                                                                        :class="{ 'is-open': open }"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="m9 6 6 6-6 6"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                        />
                                                                    </svg>

                                                                    <span class="master-vault-v10-8-drawer-title">
                                                                        <strong x-text="trait.key === 'loading' && form.properties.weapon.loading_capacity > 0 ? 'Carregamento ('+form.properties.weapon.loading_capacity+'/'+form.properties.weapon.loading_capacity+')' : trait.name"></strong>

                                                                        <small
                                                                            x-show="
                                                                                weaponRuleCompactValue(
                                                                                    trait,
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponRuleCompactValue(
                                                                                    trait,
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></small>
                                                                    </span>
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-remove"
                                                                    @click="toggleTrait(trait)"
                                                                    :title="'Remover ' + trait.name"
                                                                    :aria-label="'Remover ' + trait.name"
                                                                >
                                                                    <svg
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="M6 6l12 12M18 6 6 18"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </header>

                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                x-transition.opacity.duration.120ms
                                                                class="master-vault-v10-8-drawer-body"
                                                            >
                                                                <p
                                                                    class="master-vault-v10-8-rule-description"
                                                                    x-text="
                                                                        trait.description
                                                                        || 'Sem descrição cadastrada.'
                                                                    "
                                                                ></p>

                                                                <div
                                                                    x-show="trait.key === 'range'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Distância da Arma</strong>

                                                                        <span
                                                                            x-show="
                                                                                weaponRangeText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponRangeText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance Normal (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.range_normal_ft
                                                                                "
                                                                                placeholder="Ex.: 80"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>

                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance Máximo (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.range_long_ft
                                                                                "
                                                                                placeholder="Ex.: 320"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="trait.key === 'loading'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Capacidade antes de recarregar</strong>

                                                                        <span
                                                                            x-show="form.properties.weapon.loading_capacity"
                                                                            x-text="weaponRuleCompactValue(trait, form.properties.weapon)"
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid is-single">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Munições no carregador</span>

                                                                            <input
                                                                                type="number"
                                                                                min="1"
                                                                                max="999999"
                                                                                step="1"
                                                                                x-model.number="form.properties.weapon.loading_capacity"
                                                                                placeholder="Ex.: 6"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="trait.key === 'reach'"
                                                                    x-cloak
                                                                    class="master-vault-v10-8-measure"
                                                                >
                                                                    <div class="master-vault-v10-8-measure-head">
                                                                        <strong>Alcance Corpo a Corpo</strong>

                                                                        <span
                                                                            x-show="
                                                                                weaponReachText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                            x-text="
                                                                                weaponReachText(
                                                                                    form.properties.weapon
                                                                                )
                                                                            "
                                                                        ></span>
                                                                    </div>

                                                                    <div class="master-vault-v10-8-measure-grid is-single">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Alcance (Ft)</span>

                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                step="1"
                                                                                x-model.number="
                                                                                    form.properties.weapon.reach_ft
                                                                                "
                                                                                placeholder="Ex.: 10"
                                                                                class="master-vault-v4-input"
                                                                            >
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="master-vault-v4-repeat">
                                                <template
                                                    x-for="(trait, index) in form.properties.weapon.traits"
                                                    :key="trait.key"
                                                >
                                                    <div
                                                        x-show="trait.custom"
                                                        x-cloak
                                                        class="master-vault-v4-repeat-row"
                                                    >
                                                        <input
                                                            type="text"
                                                            maxlength="120"
                                                            x-model="trait.name"
                                                            placeholder="Nome da propriedade"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <input
                                                            type="text"
                                                            maxlength="2000"
                                                            x-model="trait.description"
                                                            placeholder="O que esta propriedade faz?"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <button
                                                            type="button"
                                                            class="master-vault-v4-remove"
                                                            @click="removeTrait(index)"
                                                        >
                                                            Remover
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>

<section class="master-vault-v4-rule vault-v250-masteries">
                                            <div class="vault-v250-rule-heading">
                                                <div>
                                                    <p class="master-vault-v4-rule-title">Maestrias</p>
                                                    <p class="master-vault-v4-rule-copy">Selecione uma ou mais. Abra as escolhidas para ler o efeito.</p>
                                                </div>
                                                <span class="vault-v250-count"
                                                      x-text="form.properties.weapon.masteries.length + ' selecionada' + (form.properties.weapon.masteries.length === 1 ? '' : 's')"></span>
                                            </div>

                                            <div class="master-vault-v4-chip-row">
                                                <template
                                                    x-for="definition in masteryDefinitions"
                                                    :key="definition.key"
                                                >
                                                    <button
                                                        type="button"
                                                        class="master-vault-v4-chip-button vault-v250-choice"
                                                        :class="{ 'is-active': masterySelected(definition.key) }"
                                                        :title="definition.description"
                                                        :aria-pressed="masterySelected(definition.key)"
                                                        @click="toggleMastery(definition)"
                                                    ><span class="vault-v250-choice-indicator" aria-hidden="true">✓</span>
                                                     <span x-text="definition.name"></span></button>
                                                </template>

                                                <button
                                                    type="button"
                                                    class="master-vault-v4-chip-button vault-v250-choice vault-v250-choice--custom"
                                                    @click="addCustomMastery()"
                                                >
                                                    + Custom
                                                </button>
                                            </div>

                                            <div
                                                x-show="
                                                    form.properties.weapon.masteries.some(
                                                        mastery => !mastery.custom
                                                    )
                                                "
                                                x-cloak
                                                class="master-vault-v10-8-selected"
                                            >
                                                <div class="master-vault-v10-8-selected-head">
                                                    <strong>Maestrias em uso</strong>

                                                    <span
                                                        x-text="
                                                            form.properties.weapon.masteries
                                                                .filter(mastery => !mastery.custom)
                                                                .length
                                                            + (
                                                                form.properties.weapon.masteries
                                                                    .filter(mastery => !mastery.custom)
                                                                    .length === 1
                                                                    ? ' maestria'
                                                                    : ' maestrias'
                                                            )
                                                        "
                                                    ></span>
                                                </div>

                                                <div class="master-vault-v10-8-drawer-list">
                                                    <template
                                                        x-for="
                                                            mastery in
                                                            form.properties.weapon.masteries.filter(
                                                                current => !current.custom
                                                            )
                                                        "
                                                        :key="'standard-mastery-' + mastery.key"
                                                    >
                                                        <article
                                                            x-data="{ open: false }"
                                                            class="master-vault-v10-8-drawer"
                                                        >
                                                            <header class="master-vault-v10-8-drawer-head">
                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-toggle"
                                                                    @click="open = !open"
                                                                    :aria-expanded="open"
                                                                >
                                                                    <svg
                                                                        class="master-vault-v10-8-chevron"
                                                                        :class="{ 'is-open': open }"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="m9 6 6 6-6 6"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                        />
                                                                    </svg>

                                                                    <span class="master-vault-v10-8-drawer-title">
                                                                        <strong x-text="mastery.name"></strong>
                                                                    </span>
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    class="master-vault-v10-8-drawer-remove"
                                                                    @click="toggleMastery(mastery)"
                                                                    :title="'Remover ' + mastery.name"
                                                                    :aria-label="'Remover ' + mastery.name"
                                                                >
                                                                    <svg
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            d="M6 6l12 12M18 6 6 18"
                                                                            stroke-width="1.8"
                                                                            stroke-linecap="round"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </header>

                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                x-transition.opacity.duration.120ms
                                                                class="master-vault-v10-8-drawer-body"
                                                            >
                                                                <p
                                                                    class="master-vault-v10-8-rule-description"
                                                                    x-text="
                                                                        mastery.description
                                                                        || 'Sem descrição cadastrada.'
                                                                    "
                                                                ></p>
                                                            </div>
                                                        </article>
                                                    </template>
                                                </div>
                                            </div>

                                            <div class="master-vault-v4-repeat">
                                                <template
                                                    x-for="(mastery, index) in form.properties.weapon.masteries"
                                                    :key="mastery.key"
                                                >
                                                    <div
                                                        x-show="mastery.custom"
                                                        x-cloak
                                                        class="master-vault-v4-repeat-row"
                                                    >
                                                        <input
                                                            type="text"
                                                            maxlength="120"
                                                            x-model="mastery.name"
                                                            placeholder="Nome da maestria"
                                                            class="master-vault-v4-input"
                                                        >

                                                        <textarea
                                                            rows="2"
                                                            maxlength="5000"
                                                            x-model="mastery.description"
                                                            placeholder="O que esta maestria faz?"
                                                            class="master-vault-v4-textarea"
                                                        ></textarea>

                                                        <button
                                                            type="button"
                                                            class="master-vault-v4-remove"
                                                            @click="removeMastery(index)"
                                                        >
                                                            Remover
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>

                                        {{-- V66.2.54: ordem de preferência e atributos permitidos para a ficha. --}}
                                        <section class="master-vault-v4-rule vault-v251-attributes vault-v254-attributes">
                                            <div class="vault-v250-rule-heading">
                                                <div>
                                                    <p class="master-vault-v4-rule-title">Atributos da Arma</p>
                                                    <p class="master-vault-v4-rule-copy">Selecione os atributos permitidos. O primeiro escolhido receberá o número 1.</p>
                                                </div>
                                                <span class="vault-v250-count" x-text="weaponAbilityAllowed('none') ? 'Sem atributo' : (form.properties.weapon.allowed_abilities?.length || 0) + ' permitido(s)'"></span>
                                            </div>
                                            <div class="vault-v251-ability-row vault-v254-ability-row">
                                                <template x-for="attribute in [{key:'strength',name:'Força'},{key:'dexterity',name:'Destreza'},{key:'constitution',name:'Constituição'},{key:'intelligence',name:'Inteligência'},{key:'wisdom',name:'Sabedoria'},{key:'charisma',name:'Carisma'},{key:'none',name:'Sem Atributo'}]" :key="'weapon-attr-'+attribute.key">
                                                    <button type="button" class="vault-v251-option vault-v254-ability-chip"
                                                            :class="{'is-selected': weaponAbilityAllowed(attribute.key), 'is-locked': weaponAbilityLocked(attribute.key)}"
                                                            :aria-pressed="weaponAbilityAllowed(attribute.key)"
                                                            :title="weaponAbilityLocked(attribute.key) ? 'Acuidade exige Força e Destreza; remova a propriedade para desmarcar.' : 'Clique para incluir ou remover o atributo'"
                                                            :disabled="weaponAbilityLocked(attribute.key)"
                                                            @click="toggleWeaponAbility(attribute.key)">
                                                        <span class="vault-v254-ability-index" x-show="weaponAbilityAllowed(attribute.key)" x-text="attribute.key === 'none' ? '—' : weaponAbilityOrder(attribute.key)" aria-hidden="true"></span>
                                                        <span class="vault-v251-option-dot" x-show="!weaponAbilityAllowed(attribute.key)" aria-hidden="true"></span>
                                                        <span x-text="attribute.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <div class="vault-v254-ability-foot">
                                                <span class="vault-v254-ability-mark" x-text="weaponAbilityAllowed('none') ? '—' : '1'"></span>
                                                <p x-text="weaponAbilityAllowed('none') ? 'Ataques com esta arma não usam atributo do personagem.' : 'O primeiro atributo é usado inicialmente. O jogador poderá trocar na aba Ataques.'"></p>
                                            </div>
                                            <p class="vault-v251-quiet vault-v254-finesse-hint" x-show="traitSelected('finesse')" x-cloak>Acuidade seleciona e mantém <strong>Força e Destreza</strong> automaticamente.</p>
                                        </section>

                                        {{-- V66.2.54: rolagem ágil, modificador padrão e bônus condicional opcional. --}}
                                        <section class="master-vault-v4-rule vault-v250-damage vault-v251-damage vault-v254-damage vault-v255-damage">
                                            <div class="vault-v250-damage-header">
                                                <div>
                                                    <p class="master-vault-v4-rule-title">Danos da Arma</p>
                                                    
                                                </div>
                                                <div class="vault-v258-damage-actions">
                                                    <div class="vault-v250-damage-summary vault-v258-damage-summary" :title="weaponDamagePreview(form.properties.weapon)">
                                                        <span>Composição</span>
                                                        <strong x-text="weaponDamageCompact(form.properties.weapon)"></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vault-v251-damage-list">
                                                <article class="vault-v251-damage-line vault-v254-damage-line">
                                                    <div class="vault-v251-damage-line-head">
                                                        <strong>Dano principal</strong>
                                                        <span class="vault-v253-roll-preview" x-text="damageExpression(form.properties.weapon.damage)"></span>
                                                    </div>
                                                    <div class="vault-v254-damage-content">
                                                        <div class="vault-v254-dice-panel">
                                                            <span class="vault-v252-field-label">Rolagem de dados</span>
                                                            <div class="vault-v254-dice-composer" role="group" aria-label="Montar rolagem de dados">
                                                                <div class="vault-v254-stepper">
                                                                    <button type="button" @click="adjustWeaponDiceCount(form.properties.weapon.damage,-1)" :disabled="Number(form.properties.weapon.damage.count) <= 1" aria-label="Diminuir quantidade de dados">‹</button>
                                                                    <input type="text" inputmode="numeric" maxlength="7" :value="form.properties.weapon.damage.count"
                                                                           @input="previewWeaponDiceNumber(form.properties.weapon.damage,'count',$event.target.value)"
                                                                           @change="commitWeaponDiceNumber(form.properties.weapon.damage,'count',$event.target.value);$event.target.value=form.properties.weapon.damage.count"
                                                                           @keydown.enter.prevent="$event.target.blur()" aria-label="Quantidade de dados principais">
                                                                    <button type="button" @click="adjustWeaponDiceCount(form.properties.weapon.damage,1)" :disabled="Number(form.properties.weapon.damage.count) >= 99" aria-label="Aumentar quantidade de dados">›</button>
                                                                </div>
                                                                <span class="vault-v254-dice-d" aria-hidden="true">d</span>
                                                                <div class="vault-v254-stepper">
                                                                    <button type="button" @click="adjustWeaponDie(form.properties.weapon.damage,-1)" :disabled="weaponDieSides(form.properties.weapon.damage) <= 2" aria-label="Dado anterior">‹</button>
                                                                    <input type="text" inputmode="numeric" maxlength="7" :value="weaponDieSides(form.properties.weapon.damage)"
                                                                           @input="previewWeaponDiceNumber(form.properties.weapon.damage,'die',$event.target.value)"
                                                                           @change="commitWeaponDiceNumber(form.properties.weapon.damage,'die',$event.target.value);$event.target.value=weaponDieSides(form.properties.weapon.damage)"
                                                                           @keydown.enter.prevent="$event.target.blur()" aria-label="Lados do dado principal">
                                                                    <button type="button" @click="adjustWeaponDie(form.properties.weapon.damage,1)" :disabled="weaponDieSides(form.properties.weapon.damage) >= 100" aria-label="Próximo dado">›</button>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="vault-v251-damage-types vault-v254-type-panel">
                                                            <span>Tipo de dano</span>
                                                            <div class="vault-v251-damage-type-list">
                                                                <template x-for="damageType in damageTypeDefinitions" :key="'damage-main-'+damageType">
                                                                    <button type="button" class="vault-v251-type-chip"
                                                                            :class="{'is-selected': damageTypeSelected(form.properties.weapon.damage,damageType)}"
                                                                            :aria-pressed="damageTypeSelected(form.properties.weapon.damage,damageType)"
                                                                            @click="toggleDamageType(form.properties.weapon.damage,damageType)" x-text="damageType"></button>
                                                                </template>
                                                                <button type="button" class="vault-v251-type-chip vault-v254-custom-chip"
                                                                        :class="{'is-selected': !!form.properties.weapon.damage.type_custom_mode || !!form.properties.weapon.damage.custom_type}"
                                                                        :aria-expanded="!!form.properties.weapon.damage.type_custom_mode"
                                                                        @click="form.properties.weapon.damage.type_custom_mode = !form.properties.weapon.damage.type_custom_mode">Outro tipo de dano</button>
                                                            </div>
                                                            <label x-show="form.properties.weapon.damage.type_custom_mode" x-cloak class="master-vault-v4-field vault-v254-custom-type-input">
                                                                <span>Nome do tipo de dano</span>
                                                                <input type="text" class="master-vault-v4-input" maxlength="80" x-model="form.properties.weapon.damage.custom_type" placeholder="Ex.: Sangramento">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="vault-v254-modifier-row vault-v258-modifier-row">
                                                        <button type="button" class="vault-v258-modifier-trigger"
                                                                :class="{'is-on': damageModifierKeys(form.properties.weapon.damage).length > 0, 'is-open': !!form.properties.weapon.damage.modifiers_open}"
                                                                :aria-expanded="!!form.properties.weapon.damage.modifiers_open"
                                                                @click="form.properties.weapon.damage.modifiers_open = !form.properties.weapon.damage.modifiers_open">
                                                            <span class="vault-v258-modifier-symbol" aria-hidden="true">+</span>
                                                            <span class="vault-v258-modifier-label" x-text="damageModifierText(form.properties.weapon.damage) || 'Sem modificador'"></span>
                                                            <span class="vault-v258-modifier-chevron vault-v259-chevron" aria-hidden="true"><svg viewBox="0 0 16 16" width="14" height="14" fill="none" focusable="false"><path d="M3.5 6 8 10.5 12.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                                        </button>
                                                        <button type="button" class="vault-v254-extra-toggle"
                                                                :aria-expanded="!!form.properties.weapon.damage.extra_bonus_open"
                                                                @click="form.properties.weapon.damage.extra_bonus_open = !form.properties.weapon.damage.extra_bonus_open">
                                                            <span x-text="form.properties.weapon.damage.extra_bonus_open ? '− Recolher bônus extra' : '+ Bônus extra'"></span>
                                                            <small x-show="!form.properties.weapon.damage.extra_bonus_open && Number(form.properties.weapon.damage.flat_bonus)>0" x-text="'+'+form.properties.weapon.damage.flat_bonus"></small>
                                                        </button>
                                                    </div>
                                                    <div x-show="!!form.properties.weapon.damage.modifiers_open" x-cloak
                                                         x-transition.opacity.scale.95.duration.180ms
                                                         class="vault-v258-modifier-picker vault-v259-modifier-picker">
                                                        <div class="vault-v259-picker-heading">
                                                            <strong>Modificadores de dano</strong>
                                                            <span class="vault-v259-picker-key" title="O losango identifica o atributo utilizado no ataque"><span aria-hidden="true">◆</span> Atributo do ataque</span>
                                                        </div>
                                                        <div class="vault-v258-modifier-chips vault-v259-modifier-chips">
                                                            <template x-for="ability in [{key:'strength',name:'Força'},{key:'dexterity',name:'Destreza'},{key:'constitution',name:'Constituição'},{key:'intelligence',name:'Inteligência'},{key:'wisdom',name:'Sabedoria'},{key:'charisma',name:'Carisma'}]" :key="'damage-mod-main-'+ability.key">
                                                                <button type="button" class="vault-v258-mod-chip vault-v259-mod-chip"
                                                                        :class="{'is-active':damageModifierSelectedForAbility(form.properties.weapon.damage,ability.key),'is-attack':weaponPrimaryAbilityKey()===ability.key}"
                                                                        :aria-pressed="damageModifierSelectedForAbility(form.properties.weapon.damage,ability.key)"
                                                                        :title="weaponPrimaryAbilityKey()===ability.key ? 'Atributo do ataque: acompanha a escolha feita pelo jogador na ficha' : 'Somar '+ability.name+' ao dano'"
                                                                        @click="toggleDamageModifierForAbility(form.properties.weapon.damage,ability.key)">
                                                                    <span class="vault-v259-chip-state" aria-hidden="true" x-text="damageModifierSelectedForAbility(form.properties.weapon.damage,ability.key)?'✓':'+'"></span>
                                                                    <span x-text="ability.name"></span>
                                                                    <span class="vault-v259-attack-icon" x-show="weaponPrimaryAbilityKey()===ability.key" aria-hidden="true">◆</span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="vault-v254-bonus-detail" x-show="!!form.properties.weapon.damage.extra_bonus_open" x-cloak>
                                                        <label class="master-vault-v4-field vault-v254-bonus-number"><span>Bônus de dano (+)</span>
                                                            <input class="master-vault-v4-input" type="number" min="0" max="99" step="1" x-model.number="form.properties.weapon.damage.flat_bonus" @change="clampWeaponExtraBonus(form.properties.weapon.damage)" placeholder="0">
                                                        </label>
                                                        <label class="master-vault-v4-field vault-v254-bonus-condition"><span>Condição para aplicar (opcional)</span>
                                                            <input class="master-vault-v4-input" type="text" maxlength="180" x-model="form.properties.weapon.damage.bonus_condition" placeholder="Ex.: enquanto estiver voando">
                                                        </label>
                                                    </div>
                                                </article>
                                                <template x-for="(part,index) in form.properties.weapon.extra_damage" :key="'extra-'+index">
                                                    <article class="vault-v251-damage-line vault-v254-damage-line">
                                                    <div class="vault-v251-damage-line-head">
                                                        <strong x-text="'Dano adicional '+(index+1)"></strong>
                                                        <span class="vault-v253-roll-preview" x-text="damageExpression(part)"></span>
                                                        <button type="button" class="vault-v250-remove-damage" @click="removeExtraDamage(index)">Remover</button>
                                                    </div>
                                                    <div class="vault-v254-damage-content">
                                                        <div class="vault-v254-dice-panel">
                                                            <span class="vault-v252-field-label">Rolagem de dados</span>
                                                            <div class="vault-v254-dice-composer" role="group" aria-label="Montar rolagem de dados">
                                                                <div class="vault-v254-stepper">
                                                                    <button type="button" @click="adjustWeaponDiceCount(part,-1)" :disabled="Number(part.count) <= 1" aria-label="Diminuir quantidade de dados">‹</button>
                                                                    <input type="text" inputmode="numeric" maxlength="7" :value="part.count"
                                                                           @input="previewWeaponDiceNumber(part,'count',$event.target.value)"
                                                                           @change="commitWeaponDiceNumber(part,'count',$event.target.value);$event.target.value=part.count"
                                                                           @keydown.enter.prevent="$event.target.blur()" aria-label="Quantidade de dados adicionais">
                                                                    <button type="button" @click="adjustWeaponDiceCount(part,1)" :disabled="Number(part.count) >= 99" aria-label="Aumentar quantidade de dados">›</button>
                                                                </div>
                                                                <span class="vault-v254-dice-d" aria-hidden="true">d</span>
                                                                <div class="vault-v254-stepper">
                                                                    <button type="button" @click="adjustWeaponDie(part,-1)" :disabled="weaponDieSides(part) <= 2" aria-label="Dado anterior">‹</button>
                                                                    <input type="text" inputmode="numeric" maxlength="7" :value="weaponDieSides(part)"
                                                                           @input="previewWeaponDiceNumber(part,'die',$event.target.value)"
                                                                           @change="commitWeaponDiceNumber(part,'die',$event.target.value);$event.target.value=weaponDieSides(part)"
                                                                           @keydown.enter.prevent="$event.target.blur()" aria-label="Lados do dado adicional">
                                                                    <button type="button" @click="adjustWeaponDie(part,1)" :disabled="weaponDieSides(part) >= 100" aria-label="Próximo dado">›</button>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="vault-v251-damage-types vault-v254-type-panel">
                                                            <span>Tipo de dano</span>
                                                            <div class="vault-v251-damage-type-list">
                                                                <template x-for="damageType in damageTypeDefinitions" :key="'damage-'+'extra-'+index+'-'+damageType">
                                                                    <button type="button" class="vault-v251-type-chip"
                                                                            :class="{'is-selected': damageTypeSelected(part,damageType)}"
                                                                            :aria-pressed="damageTypeSelected(part,damageType)"
                                                                            @click="toggleDamageType(part,damageType)" x-text="damageType"></button>
                                                                </template>
                                                                <button type="button" class="vault-v251-type-chip vault-v254-custom-chip"
                                                                        :class="{'is-selected': !!part.type_custom_mode || !!part.custom_type}"
                                                                        :aria-expanded="!!part.type_custom_mode"
                                                                        @click="part.type_custom_mode = !part.type_custom_mode">Outro tipo de dano</button>
                                                            </div>
                                                            <label x-show="part.type_custom_mode" x-cloak class="master-vault-v4-field vault-v254-custom-type-input">
                                                                <span>Nome do tipo de dano</span>
                                                                <input type="text" class="master-vault-v4-input" maxlength="80" x-model="part.custom_type" placeholder="Ex.: Sangramento">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="vault-v254-modifier-row vault-v258-modifier-row">
                                                        <button type="button" class="vault-v258-modifier-trigger"
                                                                :class="{'is-on': damageModifierKeys(part).length > 0, 'is-open': !!part.modifiers_open}"
                                                                :aria-expanded="!!part.modifiers_open"
                                                                @click="part.modifiers_open = !part.modifiers_open">
                                                            <span class="vault-v258-modifier-symbol" aria-hidden="true">+</span>
                                                            <span class="vault-v258-modifier-label" x-text="damageModifierText(part) || 'Sem modificador'"></span>
                                                            <span class="vault-v258-modifier-chevron vault-v259-chevron" aria-hidden="true"><svg viewBox="0 0 16 16" width="14" height="14" fill="none" focusable="false"><path d="M3.5 6 8 10.5 12.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                                        </button>
                                                        <button type="button" class="vault-v254-extra-toggle"
                                                                :aria-expanded="!!part.extra_bonus_open"
                                                                @click="part.extra_bonus_open = !part.extra_bonus_open">
                                                            <span x-text="part.extra_bonus_open ? '− Recolher bônus extra' : '+ Bônus extra'"></span>
                                                            <small x-show="!part.extra_bonus_open && Number(part.flat_bonus)>0" x-text="'+'+part.flat_bonus"></small>
                                                        </button>
                                                    </div>
                                                    <div x-show="!!part.modifiers_open" x-cloak
                                                         x-transition.opacity.scale.95.duration.180ms
                                                         class="vault-v258-modifier-picker vault-v259-modifier-picker">
                                                        <div class="vault-v259-picker-heading">
                                                            <strong>Modificadores de dano</strong>
                                                            <span class="vault-v259-picker-key" title="O losango identifica o atributo utilizado no ataque"><span aria-hidden="true">◆</span> Atributo do ataque</span>
                                                        </div>
                                                        <div class="vault-v258-modifier-chips vault-v259-modifier-chips">
                                                            <template x-for="ability in [{key:'strength',name:'Força'},{key:'dexterity',name:'Destreza'},{key:'constitution',name:'Constituição'},{key:'intelligence',name:'Inteligência'},{key:'wisdom',name:'Sabedoria'},{key:'charisma',name:'Carisma'}]" :key="'damage-mod-extra-'+index+'-'+ability.key">
                                                                <button type="button" class="vault-v258-mod-chip vault-v259-mod-chip"
                                                                        :class="{'is-active':damageModifierSelectedForAbility(part,ability.key),'is-attack':weaponPrimaryAbilityKey()===ability.key}"
                                                                        :aria-pressed="damageModifierSelectedForAbility(part,ability.key)"
                                                                        :title="weaponPrimaryAbilityKey()===ability.key ? 'Atributo do ataque: acompanha a escolha feita pelo jogador na ficha' : 'Somar '+ability.name+' ao dano'"
                                                                        @click="toggleDamageModifierForAbility(part,ability.key)">
                                                                    <span class="vault-v259-chip-state" aria-hidden="true" x-text="damageModifierSelectedForAbility(part,ability.key)?'✓':'+'"></span>
                                                                    <span x-text="ability.name"></span>
                                                                    <span class="vault-v259-attack-icon" x-show="weaponPrimaryAbilityKey()===ability.key" aria-hidden="true">◆</span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="vault-v254-bonus-detail" x-show="!!part.extra_bonus_open" x-cloak>
                                                        <label class="master-vault-v4-field vault-v254-bonus-number"><span>Bônus de dano (+)</span>
                                                            <input class="master-vault-v4-input" type="number" min="0" max="99" step="1" x-model.number="part.flat_bonus" @change="clampWeaponExtraBonus(part)" placeholder="0">
                                                        </label>
                                                        <label class="master-vault-v4-field vault-v254-bonus-condition"><span>Condição para aplicar (opcional)</span>
                                                            <input class="master-vault-v4-input" type="text" maxlength="180" x-model="part.bonus_condition" placeholder="Ex.: enquanto estiver voando">
                                                        </label>
                                                    </div>
                                                </article>
                                                </template>
                                            </div>
                                            <button type="button" class="vault-v250-add-damage" @click="addExtraDamage()">+ Adicionar dano</button>
                                            
                                        </section>
                                    </div>
                                </article>

                                {{-- ARMADURA — V66.2.62: configuração livre e categorias com ícones. --}}
                                <template x-if="itemCategory() === 'armor'">
                                <article class="master-vault-v4-module vault-v19-equipment vault-v260-defense vault-v261-armor-editor">
                                    <header class="master-vault-v4-module-head vault-v260-defense-header">
                                        <div>
                                            <span class="vault-v260-eyebrow">Proteção · Classe de Armadura</span>
                                            <h3>Armadura</h3>
                                        </div>
                                        <button type="button" class="master-vault-v4-remove" @click="setItemCategory('other')">Limpar Categoria</button>
                                    </header>
                                    <div class="master-vault-v4-module-body">
                                        <section class="master-vault-v4-rule vault-v260-rule vault-v261-category-rule">
                                            <div class="vault-v260-row-heading"><strong>Categoria</strong></div>
                                            <div class="vault-v261-category-grid">
                                                <template x-for="choice in [{key:'light',title:'Leve',icon:'armor-light-class.png',muted:false},{key:'medium',title:'Média',icon:'armor-medium-class.png',muted:false},{key:'heavy',title:'Pesada',icon:'armor-heavy-class.png',muted:false},{key:'custom',title:'Especial',icon:'editor-armor.png',muted:true}]" :key="choice.key">
                                                    <button type="button" class="vault-v261-category-card"
                                                            :class="{'is-active':form.properties.armor.category===choice.key, 'is-muted':choice.muted}"
                                                            :aria-pressed="form.properties.armor.category===choice.key"
                                                            @click="setArmorCategory(choice.key)">
                                                        <span class="vault-v261-category-icon">
                                                            <img :src="'{{ asset('images/vault/icons') }}/' + choice.icon" alt="" aria-hidden="true">
                                                        </span>
                                                        <span class="vault-v261-category-text">
                                                            <strong x-text="choice.title"></strong>
                                                            <small x-show="choice.key==='custom'" x-cloak>Opcional · manual</small>
                                                        </span>
                                                        <span class="vault-v264-category-check" aria-hidden="true">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4.2 4.2L19 6.5"/></svg>
                                                        </span>
                                                    </button>
                                                </template>
                                            </div>
                                            <input x-show="form.properties.armor.category==='custom'" x-cloak
                                                   type="text" maxlength="120" x-model="form.properties.armor.category_custom"
                                                   placeholder="Ex.: Armadura Astral" class="master-vault-v4-input vault-v260-custom-category">
                                        </section>

                                        <section class="master-vault-v4-rule vault-v260-rule">
                                            <div class="vault-v261-identity-grid">
                                                <label class="master-vault-v4-field vault-v260-model"><span>Tipo / Modelo</span>
                                                    <input type="text" maxlength="120" x-model="form.properties.armor.armor_type"
                                                           placeholder="Ex.: Meia Armadura" class="master-vault-v4-input">
                                                </label>
                                                <label class="master-vault-v4-field vault-v260-number"><span>CA base</span>
                                                    <input type="number" min="0" max="99" step="1"
                                                           x-model.number="form.properties.armor.base_ac" class="master-vault-v4-input">
                                                </label>
                                            </div>
                                        </section>

                                        <section class="master-vault-v4-rule vault-v260-rule vault-v261-properties-rule vault-v263-properties">
                                            <div class="vault-v260-row-heading"><strong>Propriedades da armadura</strong></div>
                                            <div class="vault-v263-property-layout">
                                                <div class="vault-v261-property-card vault-v263-main-ability">
                                                    <div class="vault-v261-property-head">
                                                        <strong>Atributo utilizado na CA</strong>
                                                        <small>Escolha o atributo que entra na fórmula</small>
                                                    </div>
                                                    <div class="vault-v263-ability-grid" role="group" aria-label="Atributo utilizado na Classe de Armadura">
                                                        <template x-for="ability in ['strength','dexterity','constitution','intelligence','wisdom','charisma','none']" :key="'armor-base-'+ability">
                                                            <button type="button" class="vault-v263-ability-choice"
                                                                    :class="{'is-active':form.properties.armor.defense_ability===ability, 'is-none':ability==='none'}"
                                                                    :aria-pressed="form.properties.armor.defense_ability===ability"
                                                                    @click="setArmorDefenseAbility(ability)">
                                                                <span class="vault-v264-ability-indicator" aria-hidden="true"></span>
                                                                <span x-text="armorAbilityLabel(ability)"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                    <div class="vault-v263-mode-panel" x-show="form.properties.armor.defense_ability!=='none'" x-cloak>
                                                        <span class="vault-v263-field-caption">Aplicação do modificador</span>
                                                        <div class="vault-v263-mode-row">
                                                            <button type="button" class="vault-v263-mode-button"
                                                                    :class="{'is-active':form.properties.armor.defense_ability_mode==='full'}"
                                                                    :aria-pressed="form.properties.armor.defense_ability_mode==='full'"
                                                                    @click="setArmorAbilityMode('full')">Integral</button>
                                                            <button type="button" class="vault-v263-mode-button"
                                                                    :class="{'is-active':form.properties.armor.defense_ability_mode==='capped'}"
                                                                    :aria-pressed="form.properties.armor.defense_ability_mode==='capped'"
                                                                    @click="setArmorAbilityMode('capped')">Limitado</button>
                                                            <div class="vault-v263-cap" x-show="form.properties.armor.defense_ability_mode==='capped'" x-cloak>
                                                                <span>Limite</span>
                                                                <button type="button" aria-label="Diminuir limite do modificador" @click="stepArmorAbilityCap(-1)">−</button>
                                                                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="2" aria-label="Limite do modificador"
                                                                       :value="form.properties.armor.defense_ability_cap" @input="setArmorAbilityCap($event.target.value); $event.target.value = form.properties.armor.defense_ability_cap">
                                                                <button type="button" aria-label="Aumentar limite do modificador" @click="stepArmorAbilityCap(1)">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="vault-v263-secondary-properties">
                                                    <div class="vault-v261-property-card vault-v263-stealth-card">
                                                        <div class="vault-v263-compact-heading"><strong>Furtividade</strong></div>
                                                        <button type="button" class="vault-v263-stealth-switch"
                                                                :class="{'is-disadvantage':form.properties.armor.stealth_disadvantage}"
                                                                :aria-pressed="!!form.properties.armor.stealth_disadvantage"
                                                                @click="setArmorStealthDisadvantage(!form.properties.armor.stealth_disadvantage)">
                                                            <span class="vault-v263-stealth-indicator" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11m0 4v.2"/></svg></span>
                                                            <span x-text="form.properties.armor.stealth_disadvantage ? 'Desvantagem' : 'Normal'"></span>
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </button>
                                                    </div>

                                                    <div class="vault-v261-property-card vault-v263-strength-card">
                                                        <div class="vault-v263-strength-heading">
                                                            <strong>Força mínima</strong>
                                                            <button type="button" class="vault-v263-enable-switch"
                                                                    role="switch" :aria-checked="!!form.properties.armor.strength_requirement_enabled"
                                                                    :class="{'is-on':form.properties.armor.strength_requirement_enabled}"
                                                                    @click="toggleArmorStrengthRequirement()">
                                                                <span class="vault-v263-enable-track" aria-hidden="true"><i></i></span>
                                                                <span x-text="form.properties.armor.strength_requirement_enabled ? 'Ativada' : 'Opcional'"></span>
                                                            </button>
                                                        </div>
                                                        <div class="vault-v263-strength-stepper" x-show="form.properties.armor.strength_requirement_enabled" x-cloak>
                                                            <button type="button" aria-label="Diminuir Força mínima" @click="stepArmorStrengthRequirement(-1)">−</button>
                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="2" aria-label="Força mínima"
                                                                   :value="form.properties.armor.strength_requirement"
                                                                   @input="setArmorStrengthRequirement($event.target.value); $event.target.value = form.properties.armor.strength_requirement">
                                                            <button type="button" aria-label="Aumentar Força mínima" @click="stepArmorStrengthRequirement(1)">+</button>
                                                        </div>
                                                        <span class="vault-v263-strength-empty" x-show="!form.properties.armor.strength_requirement_enabled" x-cloak>Sem requisito de Força</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <details class="vault-v263-extras">
                                                <summary>
                                                    <span class="vault-v263-extras-label">Somar outros atributos <small>Opcional</small></span>
                                                    <span class="vault-v263-extras-chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg></span>
                                                </summary>
                                                <div class="vault-v263-extras-options">
                                                    <template x-for="ability in ['strength','dexterity','constitution','intelligence','wisdom','charisma']" :key="'armor-extra-'+ability">
                                                        <button type="button" class="vault-v263-extra-chip"
                                                                x-show="form.properties.armor.defense_ability!==ability"
                                                                :class="{'is-active':form.properties.armor.ability_modifiers.includes(ability)}"
                                                                :aria-pressed="form.properties.armor.ability_modifiers.includes(ability)"
                                                                @click="toggleArmorAbilityModifier(ability)"
                                                                x-text="armorAbilityLabel(ability)"></button>
                                                    </template>
                                                </div>
                                            </details>

                                            <div class="vault-v260-defense-result vault-v261-armor-result vault-v263-formula" aria-label="Fórmula defensiva da armadura">
                                                <span class="vault-v260-result-symbol" aria-hidden="true">✦</span>
                                                <div>
                                                    <span>Fórmula de CA</span>
                                                    <strong x-text="armorFormulaText(form.properties.armor)"></strong>
                                                    <div class="vault-v261-result-tags">
                                                        <span class="vault-v261-result-tag" x-show="form.properties.armor.stealth_disadvantage" x-cloak>Desvantagem em Furtividade</span>
                                                        <span class="vault-v261-result-tag" x-show="form.properties.armor.strength_requirement_enabled" x-cloak x-text="'FOR mínima ' + form.properties.armor.strength_requirement"></span>
                                                    </div>
                                                </div>
                                                <span class="vault-v260-magic-tag" x-show="form.properties.armor.enchantment_enabled" x-cloak x-text="'Encantada +'+form.properties.armor.magic_bonus"></span>
                                            </div>
                                        </section>
                                    </div>
                                </article>
                                </template>

                                {{-- ESCUDO — V66.2.60: bônus base independente de magia. --}}

                                <template x-if="itemCategory() === 'shield'">
                                <article class="master-vault-v4-module vault-v19-equipment vault-v260-defense vault-v260-shield">
                                    <header class="master-vault-v4-module-head vault-v260-defense-header">
                                        <div>
                                            <span class="vault-v260-eyebrow">Proteção · Defesa adicional</span>
                                            <h3>Escudo</h3>
                                        </div>
                                        <button type="button" class="master-vault-v4-remove" @click="setItemCategory('other')">Limpar Categoria</button>
                                    </header>
                                    <div class="master-vault-v4-module-body">
                                        <section class="master-vault-v4-rule vault-v260-rule">
                                            <div class="vault-v260-fields">
                                                <label class="master-vault-v4-field vault-v260-model"><span>Nome / Modelo</span>
                                                    <input type="text" maxlength="120" x-model="form.properties.shield.label"
                                                           placeholder="Escudo" class="master-vault-v4-input">
                                                </label>
                                                <label class="master-vault-v4-field vault-v260-number"><span>Bônus base de CA</span>
                                                    <input type="number" min="0" max="99" step="1"
                                                           x-model.number="form.properties.shield.ac_bonus" class="master-vault-v4-input">
                                                </label>
                                            </div>
                                        </section>
                                        <section class="vault-v260-defense-result" aria-label="Bônus de CA do escudo">
                                            <span class="vault-v260-result-symbol" aria-hidden="true">✦</span>
                                            <div><span>Bônus total de CA</span>
                                                <strong x-text="'+' + shieldTotalBonus(form.properties.shield)"></strong>
                                            </div>
                                            <span class="vault-v260-magic-tag" x-show="form.properties.shield.enchantment_enabled" x-cloak
                                                  x-text="'Mágico +'+form.properties.shield.magic_bonus"></span>
                                        </section>
                                    </div>
                                </article>
                                </template>
                            </section>

                            {{-- =====================================================
                                 HABILIDADES — GAVETAS POR ETAPA (V66.1.19)
                                 Cada etapa usa properties.vestige.stages[key].features.
                            ====================================================== --}}
                            <section class="master-vault-v5-abilities vault-v19-abilities" x-ref="vaultEditorAbilities">
                                <header class="master-vault-v5-abilities-head">
                                    <div class="master-vault-v5-abilities-head-main">
                                        <span class="master-vault-v5-abilities-mark">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8" stroke-width="1.6" stroke-linecap="round"/>
                                                <circle cx="12" cy="12" r="3.2" stroke-width="1.6"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <span x-text="editingVestige ? 'Artefato · Vestígio' : 'Habilidades do item'"></span>
                                            <h3 x-text="editingVestige ? 'Habilidades por Etapa' : 'Habilidades do Item'"></h3>
                                            <p class="vault-v82-ability-subtitle" x-show="editingVestige" x-cloak>O poder de cada etapa acompanha a proficiência na ficha do jogador.</p>
                                        </div>
                                    </div>
                                    <button x-show="!editingVestige" type="button"
                                            class="master-vault-v5-add-ability" @click="addFeature('normal')">
                                        <span class="text-base leading-none">+</span> Adicionar Habilidade
                                    </button>
                                </header>

                                {{-- V66.2.53: encanto administrado na própria seção Habilidades.
                                     A prévia é reativa, não depende do salvamento. --}}
                                {{-- Encantamentos defensivos usam a mesma seção de Habilidades da arma. --}}
                                <template x-if="!editingVestige && (itemCategory()==='armor' || itemCategory()==='shield')">
                                    <article class="vault-v253-enchant-ability vault-v260-defense-enchant"
                                             :class="{'is-enchanted':form.properties[itemCategory()]?.enchantment_enabled}">
                                        <div class="vault-v253-enchant-heading">
                                            <span class="vault-v253-enchant-emblem" aria-hidden="true">
                                                <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.45" focusable="false">
                                                    <path d="M16 3 27 8v9c0 7-5 11-11 13C10 28 5 24 5 17V8L16 3Z" stroke-linejoin="round"/>
                                                    <path d="m11 16 4 4 7-8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <div class="vault-v253-enchant-title">
                                                <span class="vault-v253-enchant-overline" x-text="itemCategory()==='armor' ? 'Proteção encantada' : 'Proteção mágica'"></span>
                                                <strong x-text="itemCategory()==='armor' ? 'Armadura Encantada' : 'Escudo Mágico'"></strong>
                                                <small x-text="form.properties[itemCategory()]?.enchantment_enabled
                                                    ? ('Bônus mágico de +'+form.properties[itemCategory()].magic_bonus+' na CA')
                                                    : 'Opcional · Adiciona um bônus mágico à CA.'"></small>
                                            </div>
                                            <details class="vault-v258-enchant-help">
                                                <summary title="Sobre o encantamento" aria-label="Ajuda do encantamento">?</summary>
                                                <div class="vault-v258-enchant-help-content">A proteção encantada concede +1 a +6 de bônus mágico à CA, além de sua proteção base. Ao ativar, uma habilidade editável é adicionada abaixo. O valor mecânico continua vinculado ao bônus escolhido.</div>
                                            </details>
                                            <button type="button" class="vault-v253-enchant-toggle"
                                                    role="switch" :aria-checked="!!form.properties[itemCategory()]?.enchantment_enabled"
                                                    :class="{'is-on':!!form.properties[itemCategory()]?.enchantment_enabled}"
                                                    @click="toggleDefenseEnchantment(itemCategory())">
                                                <span class="vault-v253-enchant-toggle-track" aria-hidden="true"><i></i></span>
                                                <span x-text="form.properties[itemCategory()]?.enchantment_enabled ? 'Ativada' : 'Ativar'"></span>
                                            </button>
                                        </div>
                                        <div x-show="form.properties[itemCategory()]?.enchantment_enabled" x-cloak
                                             x-transition.opacity.duration.150ms class="vault-v253-enchant-detail">
                                            <div class="vault-v253-enchant-bonuses">
                                                <span class="vault-v253-enchant-bonuses-title">Intensidade do encanto</span>
                                                <div class="vault-v253-enchant-levels" role="group" aria-label="Bônus mágico da proteção">
                                                    <template x-for="bonus in [1,2,3,4,5,6]" :key="'defense-magic-'+bonus">
                                                        <button type="button" :aria-pressed="Number(form.properties[itemCategory()]?.magic_bonus)===bonus"
                                                                :class="{'is-selected':Number(form.properties[itemCategory()]?.magic_bonus)===bonus}"
                                                                @click="setDefenseEnchantmentBonus(itemCategory(),bonus)" x-text="'+'+bonus"></button>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="vault-v258-enchant-edit-prompt">
                                                <div><strong x-text="defenseEnchantmentFeature(itemCategory())?.title || (itemCategory()==='armor' ? 'Armadura Encantada' : 'Escudo Mágico')"></strong>
                                                    <small>Passiva · Nome e descrição editáveis</small></div>
                                                <button type="button" @click="openDefenseEnchantmentFeature(itemCategory())">Editar habilidade <span aria-hidden="true">↗</span></button>
                                            </div>
                                        </div>
                                    </article>
                                </template>

                                <template x-if="!editingVestige && itemCategory() === 'weapon'">
                                <article class="vault-v253-enchant-ability"
                                         :class="{'is-enchanted': !!form?.properties?.weapon?.enchantment_enabled}"
                                         aria-label="Configuração da habilidade Arma Encantada">
                                    <div class="vault-v253-enchant-heading">
                                        <span class="vault-v253-enchant-emblem" aria-hidden="true">
                                            <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M16 2.5 18 11l8.5 5-8.5 5-2 8.5L14 21l-8.5-5 8.5-5L16 2.5Z" stroke-linejoin="round"/>
                                                <path d="M24.5 4.5v6m-3-3h6M6 23v5m-2.5-2.5h5" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <div class="vault-v253-enchant-title">
                                            <span class="vault-v253-enchant-overline">Habilidade especial da arma</span>
                                            <strong>Arma Encantada</strong>
                                            <small x-text="form.properties.weapon.enchantment_enabled
                                                ? 'Passiva automática · Bônus mágico de ataque e dano'
                                                : 'Ative para acrescentar esta habilidade ao item.'"></small>
                                        </div>
                                        <details class="vault-v258-enchant-help">
                                            <summary title="O que é Arma Encantada?" aria-label="Sobre Arma Encantada">?</summary>
                                            <div class="vault-v258-enchant-help-content">Ao ativar, esta arma recebe um bônus mágico de +1 a +6 nos ataques e danos. Uma habilidade correspondente aparece imediatamente abaixo e pode ter nome e descrição personalizados; alterar o texto não altera o bônus mecânico.</div>
                                        </details>
                                        <button type="button" class="vault-v253-enchant-toggle"
                                                role="switch" :aria-checked="!!form.properties.weapon.enchantment_enabled"
                                                :class="{'is-on': !!form.properties.weapon.enchantment_enabled}"
                                                @click="toggleWeaponEnchantment()">
                                            <span class="vault-v253-enchant-toggle-track" aria-hidden="true"><i></i></span>
                                            <span x-text="form.properties.weapon.enchantment_enabled ? 'Ativada' : 'Ativar'"></span>
                                        </button>
                                    </div>
                                    <div x-show="form.properties.weapon.enchantment_enabled" x-cloak
                                         x-transition.opacity.duration.150ms class="vault-v253-enchant-detail">
                                        <div class="vault-v253-enchant-bonuses">
                                            <span class="vault-v253-enchant-bonuses-title">Intensidade do encanto</span>
                                            <div class="vault-v253-enchant-levels" role="group" aria-label="Escolher bônus mágico da arma">
                                                <template x-for="bonus in [1,2,3,4,5,6]" :key="'weapon-enchantment-level-'+bonus">
                                                    <button type="button" :aria-pressed="Number(form.properties.weapon.magic_bonus) === bonus"
                                                            :class="{'is-selected': Number(form.properties.weapon.magic_bonus) === bonus}"
                                                            @click="setWeaponEnchantmentBonus(bonus)"
                                                            x-text="'+'+bonus"></button>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="vault-v258-enchant-edit-prompt">
                                            <div><strong x-text="weaponEnchantFeature?.title || 'Arma Encantada'"></strong><small>Habilidade passiva · Editável na gaveta abaixo</small></div>
                                            <button type="button" @click="openWeaponEnchantFeature()">Editar habilidade <span aria-hidden="true">↗</span></button>
                                        </div>
                                    </div>
                                </article>
                                </template>

                                <div class="vault-v19-stage-groups">
                                    <template x-for="group in editingAbilityGroups" :key="group.key">
                                        <section class="vault-v19-stage-group" :class="{ 'is-vestige': editingVestige, 'is-open': !editingVestige || vestigeEditorStage === group.key }">
                                            <div x-show="editingVestige" class="vault-v19-stage-bar">
                                                <button type="button" class="vault-v19-stage-toggle"
                                                        :aria-expanded="vestigeEditorStage === group.key"
                                                        @click="vestigeEditorStage = vestigeEditorStage === group.key ? null : group.key">
                                                    <span class="vault-v19-stage-symbol" x-text="group.order"></span>
                                                    <span class="vault-v19-stage-text">
                                                        <strong x-text="group.label"></strong>
                                                        <small x-text="group.range"></small>
                                                    </span>
                                                    <span class="vault-v19-stage-count">
                                                        <span x-text="group.features.length"></span>
                                                        <span x-text="group.features.length === 1 ? 'habilidade' : 'habilidades'"></span>
                                                    </span>
                                                    <svg class="vault-v19-stage-chevron" :class="{ 'is-open': vestigeEditorStage === group.key }"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                        <path d="m6 9 6 6 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="vault-v19-stage-add"
                                                        @click="vestigeEditorStage = group.key; addFeature(group.key)">
                                                    <span aria-hidden="true">+</span> Nova habilidade
                                                </button>
                                            </div>
                                            <div x-show="!editingVestige || vestigeEditorStage === group.key"
                                                 x-collapse class="vault-v19-stage-content">
                                                <template x-if="editingVestige">
                                                    <div class="vault-v19-stage-lore">
                                                        <div class="master-vault-v4-field vault-v31-rich-field" x-data="vaultTiptapField(() => form?.properties?.vestige?.stages?.[group.key], 'lore', 30000, 'Descrição da etapa do vestígio')">
        <div class="vault-v31-rich-toolbar" role="toolbar" aria-label="Formatar descrição da etapa">
            <span class="vault-v31-rich-label">Descrição da etapa</span>
            <div class="vault-v31-rich-actions">
                <button type="button" title="Negrito (Ctrl+B)" aria-label="Negrito" @mousedown.prevent @click="action('bold')" :class="{ 'is-active': active('bold') }"><b>B</b></button>
                <button type="button" title="Itálico (Ctrl+I)" aria-label="Itálico" @mousedown.prevent @click="action('italic')" :class="{ 'is-active': active('italic') }"><i>I</i></button>
                <button type="button" title="Riscado" aria-label="Riscado" @mousedown.prevent @click="action('strike')" :class="{ 'is-active': active('strike') }"><s>S</s></button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Subtítulo" aria-label="Subtítulo" @mousedown.prevent @click="action('heading')" :class="{ 'is-active': active('heading') }">T</button>
                <button type="button" title="Lista com marcadores" aria-label="Lista com marcadores" @mousedown.prevent @click="action('bulletList')" :class="{ 'is-active': active('bulletList') }">• ≡</button>
                <button type="button" title="Lista numerada" aria-label="Lista numerada" @mousedown.prevent @click="action('orderedList')" :class="{ 'is-active': active('orderedList') }">1. ≡</button>
                <button type="button" title="Citação" aria-label="Citação" @mousedown.prevent @click="action('blockquote')" :class="{ 'is-active': active('blockquote') }">❝</button>
                <button type="button" title="Linha divisória (ou digite --- e Enter)" aria-label="Inserir linha divisória" @mousedown.prevent @click="action('horizontalRule')">―</button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Desfazer (Ctrl+Z)" aria-label="Desfazer" @mousedown.prevent @click="action('undo')">↶</button>
                <button type="button" title="Refazer" aria-label="Refazer" @mousedown.prevent @click="action('redo')">↷</button>
                <button type="button" title="Limpar formatação" aria-label="Limpar formatação" @mousedown.prevent @click="action('clear')">Tx</button>
            </div>
        </div>
        <div class="vault-v31-rich-canvas">
            <div x-ref="fallbackHost" x-show="!mounted" x-cloak
                 @input="syncFallback()" @paste="pasteFallback($event)"
                 @keydown="fallbackHotkey($event)">
                <div x-ignore data-vault-fallback contenteditable="true"
                     role="textbox" aria-multiline="true" spellcheck="true"
                     class="vault-v31-fallback-input" aria-label="Campo de texto do item"></div>
            </div>
            <div x-show="mounted" x-cloak>
                <div x-ref="editorSurface" class="vault-v31-rich-surface" x-on:click.self="richEditor?.focus()">
                    <div x-ignore data-vault-tiptap-mount></div>
                </div>
            </div>
        </div>
        <div class="vault-v31-rich-foot">
            <span>Escreva normalmente e formate somente o que desejar.</span>
            <span :class="{ 'is-over': contentLength > maxLength }" x-text="contentLength + ' / ' + maxLength"></span>
        </div>
    </div>
                                                    </div>
                                                </template>
                                <div class="master-vault-v5-abilities-body">
                                    <div
                                        x-show="group.features.length === 0 && !(group.key === 'normal' && ['weapon','armor','shield'].includes(itemCategory()) && form?.properties?.[itemCategory()]?.enchantment_enabled)"
                                        x-cloak
                                        class="master-vault-v5-abilities-empty"
                                    >
                                        <span x-show="!editingVestige">Nenhuma habilidade cadastrada. Adicione um poder para este item.</span>
                                        <span x-show="editingVestige">Esta etapa ainda não possui habilidades. Adicione o primeiro poder desta fase.</span>
                                    </div>

                                    <template
                                        x-for="(feature, index) in group.features"
                                        :key="feature._editor_uid || ('feature-drawer-' + group.key + '-' + index)"
                                    >
                                        <article
                                            class="master-vault-v5-ability-drawer"
                                            :class="{ 'is-open': feature.editor_open, 'is-weapon-enchantment': ['weapon_enchantment','armor_enchantment','shield_enchantment'].includes(feature.system_key) }"
                                        >
                                            <div class="master-vault-v5-ability-bar">
                                                <span
                                                    class="master-vault-v5-ability-number"
                                                    x-text="index + 1"
                                                ></span>

                                                <button
                                                    type="button"
                                                    class="master-vault-v5-ability-title"
                                                    @click="toggleFeatureDrawer(feature)"
                                                >
                                                    <strong
                                                        x-text="feature.title?.trim() || ('Habilidade ' + (index + 1))"
                                                    ></strong>

                                                    <span
                                                        x-text="feature.system_key === 'spell_scroll_rules' ? (feature.usage.enabled ? 'Regras de uso · Consumo configurável' : 'Regras de uso · Sem rastreador') : (feature.system_key === 'spell_scroll_cast' ? 'Feitiço vinculado · Dados de conjuração' : (['weapon_enchantment','armor_enchantment','shield_enchantment'].includes(feature.system_key) ? 'Encantamento mágico · Passiva' : (
                                                            feature.usage.enabled
                                                                ? (
                                                                    feature.usage.counter_mode === 'build'
                                                                        ? 'Tracker de acúmulo'
                                                                        : 'Tracker de consumo'
                                                                )
                                                                : 'Habilidade passiva')))
                                                        "
                                                    ></span>
                                                </button>

                                                <div class="master-vault-v5-ability-tools">
                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        :disabled="index === 0"
                                                        @click="moveFeature(index, -1, group.key)"
                                                        title="Mover para cima"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="m6 14 6-6 6 6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        :disabled="index === group.features.length - 1"
                                                        @click="moveFeature(index, 1, group.key)"
                                                        title="Mover para baixo"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="m6 10 6 6 6-6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool is-danger"
                                                        x-show="!['weapon_enchantment','armor_enchantment','shield_enchantment','spell_scroll_rules','spell_scroll_cast'].includes(feature.system_key)"
                                                        @click="removeFeature(index, group.key)"
                                                        title="Remover habilidade"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M5 7h14M9 7V4h6v3M8 10v7M12 10v7M16 10v7M7 7l1 13h8l1-13"
                                                                  stroke-width="1.55"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="master-vault-v5-ability-tool"
                                                        @click="toggleFeatureDrawer(feature)"
                                                        :title="feature.editor_open ? 'Fechar gaveta' : 'Abrir gaveta'"
                                                    >
                                                        <svg
                                                            class="master-vault-v5-ability-chevron"
                                                            :class="{ 'is-open': feature.editor_open }"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                        >
                                                            <path d="m6 9 6 6 6-6"
                                                                  stroke-width="1.8"
                                                                  stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div
                                                x-show="feature.editor_open"
                                                x-collapse
                                                class="master-vault-v5-ability-content"
                                            >
                                                <div class="master-vault-v5-ability-content-grid">
                                                    <div>
                                                        <label class="master-vault-v4-field">
                                                            <span>Nome da Habilidade</span>

                                                            <input
                                                                type="text"
                                                                maxlength="120"
                                                                x-model="feature.title"
                                                                placeholder="Ex.: Descarga Arcana"
                                                                class="master-vault-v4-input"
                                                            >
                                                        </label>

                                                        <div class="master-vault-v4-field vault-v31-rich-field" x-data="vaultTiptapField(feature, 'description', 10000, 'Descrição da habilidade', feature)">
        <div class="vault-v31-rich-toolbar" role="toolbar" aria-label="Formatar descrição da habilidade">
            <span class="vault-v31-rich-label">Descrição da habilidade</span>
            <div class="vault-v31-rich-actions">
                <button type="button" title="Negrito (Ctrl+B)" aria-label="Negrito" @mousedown.prevent @click="action('bold')" :class="{ 'is-active': active('bold') }"><b>B</b></button>
                <button type="button" title="Itálico (Ctrl+I)" aria-label="Itálico" @mousedown.prevent @click="action('italic')" :class="{ 'is-active': active('italic') }"><i>I</i></button>
                <button type="button" title="Riscado" aria-label="Riscado" @mousedown.prevent @click="action('strike')" :class="{ 'is-active': active('strike') }"><s>S</s></button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Subtítulo" aria-label="Subtítulo" @mousedown.prevent @click="action('heading')" :class="{ 'is-active': active('heading') }">T</button>
                <button type="button" title="Lista com marcadores" aria-label="Lista com marcadores" @mousedown.prevent @click="action('bulletList')" :class="{ 'is-active': active('bulletList') }">• ≡</button>
                <button type="button" title="Lista numerada" aria-label="Lista numerada" @mousedown.prevent @click="action('orderedList')" :class="{ 'is-active': active('orderedList') }">1. ≡</button>
                <button type="button" title="Citação" aria-label="Citação" @mousedown.prevent @click="action('blockquote')" :class="{ 'is-active': active('blockquote') }">❝</button>
                <button type="button" title="Linha divisória (ou digite --- e Enter)" aria-label="Inserir linha divisória" @mousedown.prevent @click="action('horizontalRule')">―</button>
                <span class="vault-v31-rich-separator" aria-hidden="true"></span>
                <button type="button" title="Desfazer (Ctrl+Z)" aria-label="Desfazer" @mousedown.prevent @click="action('undo')">↶</button>
                <button type="button" title="Refazer" aria-label="Refazer" @mousedown.prevent @click="action('redo')">↷</button>
                <button type="button" title="Limpar formatação" aria-label="Limpar formatação" @mousedown.prevent @click="action('clear')">Tx</button>
            </div>
        </div>
        <div class="vault-v31-rich-canvas">
            <div x-ref="fallbackHost" x-show="!mounted" x-cloak
                 @input="syncFallback()" @paste="pasteFallback($event)"
                 @keydown="fallbackHotkey($event)">
                <div x-ignore data-vault-fallback contenteditable="true"
                     role="textbox" aria-multiline="true" spellcheck="true"
                     class="vault-v31-fallback-input" aria-label="Campo de texto do item"></div>
            </div>
            <div x-show="mounted" x-cloak>
                <div x-ref="editorSurface" class="vault-v31-rich-surface" x-on:click.self="richEditor?.focus()">
                    <div x-ignore data-vault-tiptap-mount></div>
                </div>
            </div>
        </div>
        <div class="vault-v31-rich-foot">
            <span>Escreva normalmente e formate somente o que desejar.</span>
            <span :class="{ 'is-over': contentLength > maxLength }" x-text="contentLength + ' / ' + maxLength"></span>
        </div>
    </div>
                                                    </div>

                                                    <aside class="master-vault-v5-ability-side vault-v256-ability-side" x-show="!['weapon_enchantment','armor_enchantment','shield_enchantment','spell_scroll_cast'].includes(feature.system_key)" x-cloak>
                                                        <div class="master-vault-v4-toggle-card vault-v82-tracker-toggle vault-v256-tracker-head">
                                                            <div>
                                                                <strong>Rastreador de usos</strong>
                                                                <small x-text="!feature.usage.enabled ? 'Controle opcional de cargas' : (feature.usage.counter_mode === 'build' ? 'Acúmulo progressivo' : 'Consumo de cargas')"></small>
                                                            </div>
                                                            <button type="button" class="master-vault-v4-switch"
                                                                    role="switch" aria-label="Ativar rastreador de usos"
                                                                    :aria-checked="!!feature.usage.enabled"
                                                                    :class="{ 'is-on': feature.usage.enabled }"
                                                                    @click="setFeatureUsage(feature, !feature.usage.enabled)"></button>
                                                        </div>

                                                        <div x-show="feature.usage.enabled" x-cloak class="vault-v256-tracker">
                                                            <div class="vault-v256-mode-select" role="group" aria-label="Funcionamento do rastreador">
                                                                <button type="button" :class="{ 'is-active': feature.usage.counter_mode === 'spend' }"
                                                                        :aria-pressed="feature.usage.counter_mode === 'spend'"
                                                                        @click="setFeatureCounterMode(feature, 'spend')">
                                                                    <span class="vault-v256-mode-mark" aria-hidden="true">−</span>
                                                                    <span><strong>Consumo</strong><small>Gasta cargas</small></span>
                                                                </button>
                                                                <button type="button" :class="{ 'is-active': feature.usage.counter_mode === 'build' }"
                                                                        :aria-pressed="feature.usage.counter_mode === 'build'"
                                                                        @click="setFeatureCounterMode(feature, 'build')">
                                                                    <span class="vault-v256-mode-mark" aria-hidden="true">+</span>
                                                                    <span><strong>Acúmulo</strong><small>Ganha cargas</small></span>
                                                                </button>
                                                            </div>

                                                            <div class="vault-v256-config-grid vault-v257-config-grid">
                                                                <label class="master-vault-v4-field vault-v256-config-field vault-v256-capacity">
                                                                    <span>Capacidade máxima</span>
                                                                    <input type="number" min="1" max="999999" step="1"
                                                                           x-model.number="feature.usage.max"
                                                                           @change="clampFeatureUsage(feature)"
                                                                           class="master-vault-v4-input">
                                                                </label>

                                                                <label x-show="feature.usage.counter_mode !== 'build'" x-cloak
                                                                       class="master-vault-v4-field vault-v256-config-field vault-v256-recovery">
                                                                    <span>Recuperação</span>
                                                                    <select x-model="feature.usage.recovery" @change="handleFeatureRecoveryChange(feature)"
                                                                            class="master-vault-v4-select">
                                                                        @foreach($recoveryLabels as $key => $label)
                                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </label>

                                                                <div x-show="feature.usage.counter_mode === 'build'" x-cloak
                                                                     class="vault-v257-build-recovery"
                                                                     :class="{ 'is-on': feature.usage.recovery_enabled }">
                                                                    <div class="vault-v257-recovery-row">
                                                                        <div class="vault-v257-recovery-copy">
                                                                            <strong>Recuperação</strong>
                                                                            <small x-text="feature.usage.recovery_enabled ? 'Habilitada' : 'Não recupera'"></small>
                                                                        </div>
                                                                        <button type="button" class="master-vault-v4-switch vault-v257-recovery-switch"
                                                                                role="switch" aria-label="Permitir recuperação de cargas acumuladas"
                                                                                :aria-checked="!!feature.usage.recovery_enabled"
                                                                                :class="{ 'is-on': feature.usage.recovery_enabled }"
                                                                                @click="setFeatureBuildRecovery(feature, !feature.usage.recovery_enabled)"></button>
                                                                    </div>
                                                                    <div class="vault-v257-build-recovery-options"
                                                                         x-show="feature.usage.recovery_enabled" x-cloak>
                                                                        <select aria-label="Quando as cargas acumuladas se recuperam"
                                                                                x-model="feature.usage.recovery" @change="handleFeatureRecoveryChange(feature)"
                                                                                class="master-vault-v4-select">
                                                                            @foreach($recoveryLabels as $key => $label)
                                                                                @if($key !== 'single_use')
                                                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <p x-show="feature.usage.counter_mode === 'spend' && feature.usage.recovery === 'single_use'" x-cloak
                                                               class="master-vault-v11-single-use-note vault-v256-single-note">
                                                                Uso único: cargas gastas não se recuperam.
                                                            </p>
                                                            <label x-show="feature.usage.recovery === 'custom' && (feature.usage.counter_mode === 'spend' || feature.usage.recovery_enabled)" x-cloak
                                                                   class="master-vault-v4-field vault-v256-custom-recovery vault-v257-custom-recovery">
                                                                <span>Regra de recuperação</span>
                                                                <input type="text" maxlength="120"
                                                                       x-model="feature.usage.recovery_custom"
                                                                       placeholder="Ex.: Ao realizar um descanso longo"
                                                                       class="master-vault-v4-input">
                                                            </label>

                                                            <div class="vault-v256-build" x-show="feature.usage.counter_mode === 'build'" x-cloak>
                                                                <div class="vault-v256-build-header">
                                                                    <span class="vault-v256-section-name">Estado inicial</span>
                                                                    <strong>0 <span x-text="' / ' + feature.usage.max"></span></strong>
                                                                </div>
                                                                <div class="vault-v256-progress" role="progressbar" aria-label="Cargas iniciais de acúmulo"
                                                                     aria-valuemin="0" :aria-valuemax="feature.usage.max" aria-valuenow="0">
                                                                    <span></span>
                                                                </div>
                                                                <p>Começa com 0 cargas. Cada uso definido pelo Mestre poderá aumentar o total.</p>
                                                            </div>

                                                            <div class="vault-v256-spend" x-show="feature.usage.counter_mode !== 'build'" x-cloak>
                                                                <div class="vault-v256-section-top">
                                                                    <span class="vault-v256-section-name">Quantidade inicial</span>
                                                                </div>
                                                                <div class="vault-v255-start-choices vault-v256-start-choices" role="group" aria-label="Quantidade inicial de cargas">
                                                                    <button type="button" :class="{ 'is-active': feature.usage.initial_mode === 'full' }"
                                                                            :aria-pressed="feature.usage.initial_mode === 'full'"
                                                                            @click="setFeatureInitialMode(feature,'full')">Completa</button>
                                                                    <button type="button" :class="{ 'is-active': feature.usage.initial_mode === 'fixed' }"
                                                                            :aria-pressed="feature.usage.initial_mode === 'fixed'"
                                                                            @click="setFeatureInitialMode(feature,'fixed')">Definir</button>
                                                                    <div class="vault-v257-random-option" :class="{ 'is-active': feature.usage.initial_mode === 'roll' }">
                                                                        <button type="button" class="vault-v257-random-select"
                                                                                :aria-pressed="feature.usage.initial_mode === 'roll'"
                                                                                @click="setFeatureInitialMode(feature,'roll')">Aleatória</button>
                                                                        <details class="vault-v256-help vault-v257-random-help" @click.stop>
                                                                            <summary title="Como funciona a quantidade aleatória?" aria-label="Como funciona a quantidade aleatória?">?</summary>
                                                                            <div class="vault-v256-help-content">
                                                                                <strong>Rolagem ao encontrar o item</strong>
                                                                                <p>Quando o item for entregue ao Inventário do jogador, uma tela oferecerá duas opções: rolar os dados na mesa e informar o resultado, ou usar a rolagem feita pelo sistema.</p>
                                                                                <p>A quantidade obtida será limitada à capacidade máxima do item.</p>
                                                                            </div>
                                                                        </details>
                                                                    </div>
                                                                </div>
                                                                <div class="vault-v256-complete" x-show="feature.usage.initial_mode === 'full'" x-cloak>
                                                                    <span aria-hidden="true">✓</span>
                                                                    <span>Começa com <strong x-text="feature.usage.max"></strong> cargas.</span>
                                                                </div>
                                                                <label class="master-vault-v4-field vault-v256-fixed" x-show="feature.usage.initial_mode === 'fixed'" x-cloak>
                                                                    <span>Cargas ao encontrar</span>
                                                                    <input type="number" min="0" :max="feature.usage.max" step="1"
                                                                           x-model.number="feature.usage.current"
                                                                           @change="clampFeatureUsage(feature)" class="master-vault-v4-input">
                                                                </label>
                                                                <div class="vault-v256-roll" x-show="feature.usage.initial_mode === 'roll'" x-cloak>
                                                                    <div class="vault-v256-roll-row">
                                                                        <label class="master-vault-v4-field">
                                                                            <span>Dados iniciais</span>
                                                                            <input type="text" inputmode="text" maxlength="20"
                                                                                   x-model="feature.usage.initial_formula"
                                                                                   @input="feature.usage.roll_preview = null"
                                                                                   @change="normalizeFeatureRollFormula(feature)"
                                                                                   class="master-vault-v4-input" placeholder="2d6+1">
                                                                        </label>
                                                                        <button type="button" class="vault-v256-roll-test"
                                                                                :disabled="!featureRollFormulaValid(feature.usage.initial_formula)"
                                                                                @click="rollFeatureUsagePreview(feature)" title="Simular a quantidade inicial">
                                                                            <span aria-hidden="true">⚄</span> Testar
                                                                        </button>
                                                                    </div>
                                                                    <small class="vault-v255-roll-feedback" x-show="!featureRollFormulaValid(feature.usage.initial_formula)" x-cloak>Fórmula inválida. Ex.: 2d6+1.</small>
                                                                    <span class="vault-v256-roll-result"
                                                                          x-show="feature.usage.roll_preview !== null && feature.usage.roll_preview !== undefined" x-cloak>
                                                                        Simulação: <strong x-text="feature.usage.roll_preview + ' / ' + feature.usage.max"></strong>
                                                                    </span>
                                                                    <p class="vault-v256-roll-description">Ao receber: rolagem na mesa ou pelo sistema.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </aside>
                                                </div>
                                            </div>
                                        </article>
                                    </template>
                                </div>
                                            </div>
                                        </section>
                                    </template>
                                </div>
                            </section>

                            {{-- =====================================================
                                 CAMADA PRIVADA DO MESTRE
                            ====================================================== --}}
                            <section class="master-vault-v4-secret" x-ref="vaultEditorSecrets">
                                <header class="master-vault-v4-secret-head">
                                    <span>Informações Privadas</span>

                                    <h3>Camada do Mestre</h3>
                                </header>

                                <div class="master-vault-v4-secret-body">
                                    <button
                                        x-show="!form.is_cursed"
                                        x-cloak
                                        type="button"
                                        class="master-vault-v11-curse-add"
                                        @click="form.is_cursed = true"
                                    >
                                        + Adicionar maldição
                                    </button>

                                    <div
                                        x-show="form.is_cursed"
                                        x-cloak
                                        class="master-vault-v11-curse-panel"
                                    >
                                        <div class="master-vault-v11-curse-panel-head">
                                            <div>
                                                <strong>Maldição do item</strong>
                                                <small class="block mt-1 text-[9px] text-[#876a53]">Oculta do Player até a revelação.</small>
                                            </div>

                                            <button
                                                type="button"
                                                @click="form.is_cursed = false; form.curse_name = ''; form.curse_description = ''"
                                            >
                                                Remover maldição
                                            </button>
                                        </div>

                                        <label class="master-vault-v4-field">
                                            <span>Nome da Maldição</span>
                                            <input
                                                type="text"
                                                maxlength="160"
                                                x-model="form.curse_name"
                                                placeholder="Ex.: Fome Eterna"
                                                class="master-vault-v4-input"
                                            >
                                        </label>

                                        <label class="master-vault-v4-field">
                                            <span>Descrição Secreta da Maldição</span>
                                            <textarea
                                                maxlength="30000"
                                                x-model="form.curse_description"
                                                placeholder="Somente o Mestre conhece esta descrição até a revelação."
                                                class="master-vault-v4-textarea"
                                            ></textarea>
                                        </label>
                                    </div>

                                    <label class="master-vault-v4-field">
                                        <span>Notas Privadas do Mestre</span>

                                        <textarea
                                            maxlength="30000"
                                            x-model="form.notes"
                                            placeholder="Origem, pistas, CD sugerida, localização, contexto narrativo..."
                                            class="master-vault-v4-textarea"
                                        ></textarea>
                                    </label>
                                </div>
                            </section>

                            <p
                                x-show="saveError"
                                x-cloak
                                class="master-vault-error mt-4"
                                x-text="saveError"
                            ></p>
                        </div>
                    </template>

                    <footer class="master-vault-v4-foot">
                        <button
                            x-show="editingId"
                            x-cloak
                            type="button"
                            class="master-vault-v4-danger"
                            @click="deleteItem()"
                            :disabled="saving"
                        >
                            Excluir do Vault
                        </button>

                        <span x-show="!editingId"></span>

                        <div class="master-vault-v4-foot-actions">
                            <button
                                type="button"
                                class="master-vault-v4-secondary"
                                @click="closeEditor()"
                                :disabled="saving"
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                class="master-vault-v4-primary"
                                @click="saveItem()"
                                :disabled="saving"
                            >
                                <span
                                    x-show="!saving"
                                    x-text="editingId ? 'Salvar Alterações' : 'Criar no Vault'"
                                ></span>

                                <span
                                    x-show="saving"
                                    x-cloak
                                >
                                    Salvando...
                                </span>
                            </button>
                        </div>
                    </footer>

                    {{-- V66.1.39: enquadramento real da imagem, sem mudar o backend de upload. --}}
                    <div x-show="cropOpen" x-cloak class="vault-v39-crop-overlay"
                         @keydown.escape.stop.prevent="closeCropEditor()"
                         role="dialog" aria-modal="true" aria-label="Ajustar imagem do item">
                        <div class="vault-v39-crop-backdrop" @click="closeCropEditor()"></div>
                        <div class="vault-v39-crop-panel" @click.stop>
                            <header class="vault-v39-crop-header">
                                <div><h3>Enquadrar foto</h3><small>Arraste para posicionar</small></div>
                                <button type="button" @click="closeCropEditor()" aria-label="Fechar ajuste de imagem">×</button>
                            </header>
                            <div class="vault-v39-crop-layout">
                                <div class="vault-v39-crop-stage" x-ref="vaultCropStage"
                                     @pointerdown.prevent="startCropDrag($event)"
                                     @pointermove="moveCropDrag($event)"
                                     @pointerup="stopCropDrag($event)"
                                     @pointercancel="stopCropDrag($event)">
                                    <img x-ref="vaultCropImage" :src="cropSource" :style="cropImageStyle()"
                                         @load="cropImageLoaded($event)" alt="Imagem a enquadrar" draggable="false">
                                    <span class="vault-v39-crop-guides" aria-hidden="true"></span>
                                </div>
                                <div class="vault-v39-crop-tools vault-v40-crop-controls">
                                    <div class="vault-v40-crop-control">
                                        <label for="vault-crop-zoom">Zoom <output x-text="Math.round(cropZoom * 100) + '%'"></output></label>
                                        <input id="vault-crop-zoom" type="range" min="1" max="3" step="0.05"
                                               x-model.number="cropZoom" @input="clampCropOffset()" :disabled="!cropReady">
                                    </div>
                                    <div class="vault-v40-crop-control">
                                        <label for="vault-crop-horizontal">Horizontal <output x-text="cropHorizontalPercent() + '%'"></output></label>
                                        <input id="vault-crop-horizontal" type="range" min="0" max="100" step="1"
                                               :value="cropHorizontalPercent()" @input="setCropHorizontalPercent($event.target.value)" :disabled="!cropReady">
                                        <div class="vault-v40-align-row" role="group" aria-label="Alinhar imagem horizontalmente">
                                            <button type="button" @click="alignCropHorizontal('start')" title="Alinhar à esquerda">Esquerda</button>
                                            <button type="button" @click="alignCropHorizontal('center')" title="Centralizar horizontalmente">Centro</button>
                                            <button type="button" @click="alignCropHorizontal('end')" title="Alinhar à direita">Direita</button>
                                        </div>
                                    </div>
                                    <div class="vault-v40-crop-control">
                                        <label for="vault-crop-vertical">Vertical <output x-text="cropVerticalPercent() + '%'"></output></label>
                                        <input id="vault-crop-vertical" type="range" min="0" max="100" step="1"
                                               :value="cropVerticalPercent()" @input="setCropVerticalPercent($event.target.value)" :disabled="!cropReady">
                                    </div>
                                    <div class="vault-v40-crop-file-actions">
                                        <button type="button" @click="resetCrop()">Restaurar</button>
                                        <button type="button" @click="$refs.vaultCropInput.click()">Trocar foto</button>
                                    </div>
                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" x-ref="vaultCropInput"
                                           @click="$event.target.value = null" @change="replaceCropImage($event)">
                                </div>
                            </div>
                            <footer class="vault-v39-crop-footer">
                                <button type="button" @click="closeCropEditor()">Cancelar</button>
                                <button type="button" class="vault-v39-crop-apply" :disabled="!cropReady || cropSaving"
                                        @click="applyCropImage()" x-text="cropSaving ? 'Aplicando...' : 'Aplicar'"></button>
                            </footer>
                        </div>
                    </div>
                </section>
            </div>
        </template>

