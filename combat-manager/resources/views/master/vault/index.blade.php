@if(request()->has('_vault_spell_detail'))
    @php
        // Resposta JSON pequena: apenas o feitiço solicitado, nunca o catálogo inteiro.
        $vaultSpellDetail = ['ok' => false, 'spell' => null];
        $requestedSpellId = filter_var(request()->query('_vault_spell_detail'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($requestedSpellId) {
            try {
                $schema = \Illuminate\Support\Facades\Schema::class;
                if ($schema::hasTable('spells')) {
                    // V66.2.72: o Cofre usa EXATAMENTE o serviço de exibição
                    // já utilizado no Grimório do Player. Ele corrige as tags
                    // scaledamage(BASE, FAIXA, INCREMENTO) no texto de upcast e
                    // produz o mesmo HTML, termos e tradução dos dados.
                    // Uma única magia é consultada por pedido: NÃO serializar
                    // raw_data/descrições de todo o catálogo no carregamento.
                    $available = array_flip($schema::getColumnListing('spells'));
                    $fields = ['id', 'name', 'level', 'school', 'source'];
                    foreach ([
                        'provider', 'page', 'rules_version',
                        'casting_time', 'range', 'duration', 'ritual',
                        'concentration', 'components', 'description',
                        'description_data', 'higher_level_text',
                        'higher_level_data', 'raw_data', 'scaling_level_dice',
                        'saving_throw', 'attack_type', 'area_tags',
                    ] as $column) {
                        if (isset($available[$column])) $fields[] = $column;
                    }
                    $spellQuery = \Illuminate\Support\Facades\DB::table('spells')
                        ->where('id', (int) $requestedSpellId)
                        ->where(function ($query) {
                            $query->whereNull('source')->orWhereRaw('UPPER(TRIM(source)) <> ?', ['PHB']);
                        });
                    if (isset($available['active'])) $spellQuery->where('active', true);
                    if (isset($available['upstream_missing'])) $spellQuery->where('upstream_missing', false);
                    $detail = $spellQuery->first($fields);
                    if ($detail) {
                        $display = app(\App\Services\Spells\SpellDisplayService::class);
                        $payload = $display->payload($detail);
                        $vaultSpellDetail = [
                            'ok' => true,
                            // Apenas os campos que o leitor do Cofre precisa.
                            'spell' => array_intersect_key($payload, array_flip([
                                'id', 'name', 'level', 'school_pt', 'subtitle_pt',
                                'source_label', 'casting_time_display', 'range_display',
                                'components', 'duration_display', 'ritual',
                                'concentration', 'description_html',
                                'higher_level_html', 'higher_level_label',
                                'scaling_level_dice',
                            ])),
                        ];
                    }
                }
            } catch (\Throwable $exception) {
                $vaultSpellDetail = ['ok' => false, 'spell' => null];
            }
        }
    @endphp
    @json($vaultSpellDetail)
@else
<x-app-layout>
    @php
        // V66.2.71: somente metadados leves na página inicial.
        // Descrições são consultadas individualmente no mesmo endpoint autenticado.
        $vaultSpellCatalog = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('spells')) {
                $schema = \Illuminate\Support\Facades\Schema::class;
                $hasProvider = $schema::hasColumn('spells', 'provider');
                $spellColumns = ['id', 'name', 'level', 'source', 'school'];
                if ($hasProvider) $spellColumns[] = 'provider';
                $spellTable = \Illuminate\Support\Facades\DB::table('spells');
                if ($schema::hasColumn('spells', 'active')) $spellTable->where('active', true);
                if ($schema::hasColumn('spells', 'upstream_missing')) $spellTable->where('upstream_missing', false);
                $spellTable->whereNotNull('name')->where('name', '!=', '')
                    ->where(function ($query) {
                        $query->whereNull('source')->orWhereRaw('UPPER(TRIM(source)) <> ?', ['PHB']);
                    })
                    ->orderBy('name')->orderBy('level');
                // cursor() evita materializar coleção E cópia transformada juntas.
                foreach ($spellTable->cursor($spellColumns) as $spell) {
                    $source = strtoupper(trim((string) ($spell->source ?? '')));
                    $provider = strtolower(trim((string) ($hasProvider ? ($spell->provider ?? '') : '')));
                    $isTestOrHomebrew = preg_match('/^(?:UA|HB|HOME|AU$)/', $source) === 1;
                    $isOfficial = ! $isTestOrHomebrew && (
                        $provider === '5etools'
                        || ($source === 'XPHB' && ! in_array($provider, ['homebrew', 'custom', 'hb'], true))
                        || (! $hasProvider && $source === 'XPHB')
                    );
                    $vaultSpellCatalog[] = [
                        'id' => (int) $spell->id,
                        'name' => (string) $spell->name,
                        'level' => (int) $spell->level,
                        'source' => $source ?: 'PERSONALIZADO',
                        'provider' => $provider ?: 'local',
                        'is_official' => $isOfficial,
                        'school' => (string) ($spell->school ?? ''),
                    ];
                }
            }
        } catch (\Throwable $exception) {
            $vaultSpellCatalog = [];
        }

        // V66.2.69 — nomes reais das fontes e permissões das campanhas do Mestre.
        // Leitura apenas: escolher uma campanha na biblioteca não altera suas regras.
        $vaultSpellSourceNames = [];
        $vaultSpellCampaigns = [];
        try {
            $db = \Illuminate\Support\Facades\DB::class;
            $schema = \Illuminate\Support\Facades\Schema::class;
            if ($schema::hasTable('homebrew_spell_sources')) {
                $sourceRows = $db::table('homebrew_spell_sources')
                    ->where('is_available', true)
                    ->get(['provider', 'source_code', 'name']);
                foreach ($sourceRows as $row) {
                    $key = strtolower(trim((string) $row->provider)) . '|' . strtoupper(trim((string) $row->source_code));
                    $name = trim((string) ($row->name ?? ''));
                    if ($name !== '') {
                        $vaultSpellSourceNames[$key] = $name;
                    }
                }
            }
            if ($schema::hasTable('campaigns') && $schema::hasColumn('campaigns', 'owner_user_id')) {
                $campaignQuery = $db::table('campaigns')
                    ->where('owner_user_id', (int) auth()->id());
                if ($schema::hasColumn('campaigns', 'deleted_at')) {
                    $campaignQuery->whereNull('deleted_at');
                }
                $campaignColumns = ['id', 'name'];
                $hasAllowAll = $schema::hasColumn('campaigns', 'allow_all_homebrew_spells');
                if ($hasAllowAll) {
                    $campaignColumns[] = 'allow_all_homebrew_spells';
                }
                $campaignRows = $campaignQuery->orderBy('name')->get($campaignColumns);
                $allowedByCampaign = [];
                if ($campaignRows->isNotEmpty()
                    && $schema::hasTable('campaign_homebrew_spell_sources')
                    && $schema::hasTable('homebrew_spell_sources')) {
                    $enabledRows = $db::table('campaign_homebrew_spell_sources as ch')
                        ->join('homebrew_spell_sources as hs', 'hs.id', '=', 'ch.homebrew_spell_source_id')
                        ->whereIn('ch.campaign_id', $campaignRows->pluck('id')->all())
                        ->where('hs.is_available', true)
                        ->get(['ch.campaign_id', 'hs.provider', 'hs.source_code']);
                    foreach ($enabledRows as $entry) {
                        $key = strtolower(trim((string) $entry->provider)) . '|'
                            . strtoupper(trim((string) $entry->source_code));
                        $allowedByCampaign[(int) $entry->campaign_id][] = $key;
                    }
                }
                foreach ($campaignRows as $campaignRow) {
                    $id = (int) $campaignRow->id;
                    $vaultSpellCampaigns[] = [
                        'id' => $id,
                        'name' => (string) $campaignRow->name,
                        'allow_all' => $hasAllowAll && (bool) ($campaignRow->allow_all_homebrew_spells ?? false),
                        'sources' => array_values(array_unique($allowedByCampaign[$id] ?? [])),
                    ];
                }
            }
        } catch (\Throwable $exception) {
            // Ausência de tabelas auxiliares não bloqueia a criação de pergaminhos.
            $vaultSpellSourceNames = [];
            $vaultSpellCampaigns = [];
        }

        $rarityLabels = [
            'common' => 'Comum',
            'uncommon' => 'Incomum',
            'rare' => 'Raro',
            'very_rare' => 'Muito Raro',
            'legendary' => 'Lendário',
            'artifact' => 'Artefato',
        ];

        $abilityLabels = [
            'strength' => 'Força',
            'dexterity' => 'Destreza',
            'constitution' => 'Constituição',
            'intelligence' => 'Inteligência',
            'wisdom' => 'Sabedoria',
            'charisma' => 'Carisma',
            'none' => 'Sem atributo',
            'custom' => 'Custom',
        ];

        $recoveryLabels = [
            'day' => 'Dia',
            'short_rest' => 'Descanso Curto',
            'long_rest' => 'Descanso Longo',
            'dawn' => 'Amanhecer',
            'single_use' => 'Uso Único',
            'custom' => 'Custom',
        ];

        $damageTypeDefinitions = [
            'Cortante',
            'Impacto',
            'Perfurante',
            'Ácido',
            'Frio',
            'Fogo',
            'Elétrico',
            'Energia',
            'Necrótico',
            'Veneno',
            'Psíquico',
            'Radiante',
            'Trovão',
        ];

        $weaponTraitDefinitions = [
            [
                'key' => 'ammunition',
                'name' => 'Munição',
                'description' => 'Você só pode usar uma arma com a propriedade Munição para realizar um ataque à distância se tiver munição disponível. O tipo de munição necessário é especificado junto com o alcance da arma. Cada ataque consome uma peça de munição. Sacar a munição faz parte do ataque, e você precisa de uma mão livre para carregar uma arma de uma mão. Após um combate, você pode gastar 1 minuto para recuperar metade da munição usada, arredondando para baixo; o restante é perdido.',
            ],
            [
                'key' => 'finesse',
                'name' => 'Acuidade',
                'description' => 'Ao realizar um ataque com uma arma que possui Acuidade, você pode usar o modificador de Força ou de Destreza, à sua escolha, para as jogadas de ataque e dano. Você deve usar o mesmo modificador para ambas as jogadas.',
            ],
            [
                'key' => 'heavy',
                'name' => 'Pesada',
                'description' => 'Você tem Desvantagem nas jogadas de ataque com uma arma Pesada se ela for uma arma corpo a corpo e sua Força for menor que 13, ou se ela for uma arma à distância e sua Destreza for menor que 13.',
            ],
            [
                'key' => 'light',
                'name' => 'Leve',
                'description' => 'Quando você usa a ação Atacar no seu turno e ataca com uma arma Leve, pode fazer um ataque extra como uma Ação Bônus mais tarde no mesmo turno. Esse ataque extra deve ser feito com uma arma Leve diferente, e você não adiciona seu modificador de habilidade ao dano do ataque extra, a menos que esse modificador seja negativo.',
            ],
            [
                'key' => 'loading',
                'name' => 'Carregamento',
                'description' => 'Você só pode disparar uma munição de uma arma com Carregamento quando usa uma ação, uma Ação Bônus ou uma Reação para dispará-la, independentemente do número de ataques que normalmente poderia realizar.',
            ],
            [
                'key' => 'range',
                'name' => 'Distância',
                'description' => 'Uma arma de longo alcance apresenta dois valores de alcance. O primeiro é o alcance normal da arma em Ft e o segundo é o alcance máximo. Ao atacar um alvo além do alcance normal, você tem Desvantagem na jogada de ataque. Você não pode atacar um alvo além do alcance máximo.',
            ],
            [
                'key' => 'reach',
                'name' => 'Alcance',
                'description' => 'Uma arma com Alcance adiciona 5 Ft ao seu alcance quando você ataca com ela e também ao determinar seu alcance para Ataques de Oportunidade realizados com essa arma.',
            ],
            [
                'key' => 'thrown',
                'name' => 'Arremesso',
                'description' => 'Se uma arma possuir a propriedade Arremesso, você pode arremessá-la para realizar um ataque à distância e pode sacá-la como parte desse ataque. Se for uma arma corpo a corpo, use o mesmo modificador de habilidade para as jogadas de ataque e dano que usaria em um ataque corpo a corpo com essa arma.',
            ],
            [
                'key' => 'two_handed',
                'name' => 'Duas Mãos',
                'description' => 'Uma arma de Duas Mãos exige o uso das duas mãos para atacar com ela.',
            ],
            [
                'key' => 'versatile',
                'name' => 'Versátil',
                'description' => 'Uma arma Versátil pode ser usada com uma ou duas mãos. Um valor de dano alternativo aparece associado à propriedade. A arma causa esse dano quando usada com as duas mãos para realizar um ataque corpo a corpo.',
            ],
        ];

        $masteryDefinitions = [
            [
                'key' => 'cleave',
                'name' => 'Cleave',
                'description' => 'Se você acertar uma criatura com uma jogada de ataque corpo a corpo usando esta arma, pode realizar uma jogada de ataque corpo a corpo com a arma contra uma segunda criatura que esteja a até 5 Ft da primeira e também dentro do seu alcance. Se acertar, a segunda criatura sofre o dano da arma, mas você não adiciona seu modificador de habilidade a esse dano, a menos que o modificador seja negativo. Você só pode realizar esse ataque extra uma vez por turno.',
            ],
            [
                'key' => 'graze',
                'name' => 'Graze',
                'description' => 'Se sua jogada de ataque com esta arma errar uma criatura, você pode causar a ela dano igual ao modificador de habilidade usado para realizar a jogada de ataque. Esse dano é do mesmo tipo causado pela arma e só pode ser aumentado aumentando esse modificador de habilidade.',
            ],
            [
                'key' => 'nick',
                'name' => 'Nick',
                'description' => 'Quando você realiza o ataque extra concedido pela propriedade Leve, pode fazê-lo como parte da ação Atacar em vez de usar uma Ação Bônus. Você só pode realizar esse ataque extra uma vez por turno.',
            ],
            [
                'key' => 'push',
                'name' => 'Push',
                'description' => 'Se você acertar uma criatura com esta arma, pode empurrá-la até 10 Ft em linha reta para longe de você, desde que ela seja de tamanho Grande ou menor.',
            ],
            [
                'key' => 'sap',
                'name' => 'Sap',
                'description' => 'Se você acertar uma criatura com esta arma, ela terá Desvantagem na próxima jogada de ataque que realizar antes do início do seu próximo turno.',
            ],
            [
                'key' => 'slow',
                'name' => 'Slow',
                'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, pode reduzir o Deslocamento dela em 10 Ft até o início do seu próximo turno. Se a criatura for atingida mais de uma vez por armas com esta maestria, a redução de Deslocamento não ultrapassa 10 Ft.',
            ],
            [
                'key' => 'topple',
                'name' => 'Topple',
                'description' => 'Se você acertar uma criatura com esta arma, pode forçá-la a realizar um teste de resistência de Constituição. A CD é igual a 8 + o modificador de habilidade usado na jogada de ataque + seu Bônus de Proficiência. Em caso de falha, a criatura fica Caída.',
            ],
            [
                'key' => 'vex',
                'name' => 'Vex',
                'description' => 'Se você acertar uma criatura com esta arma e causar dano a ela, terá Vantagem na sua próxima jogada de ataque contra essa criatura antes do fim do seu próximo turno.',
            ],
        ];
    @endphp

    @push('styles')
        <style>
@include('master.vault.partials.estilos-base')
@include('master.vault.partials.estilos-complementares')
        </style>
    @endpush

    {{-- Registrar o componente ANTES do Vite: o módulo Tiptap carrega de forma assíncrona. --}}
    @once
    <script>
        // V66.1.33: Tiptap permanece prioritário; modo compatível visual se Vite falhar.
        // Nenhum editor/DOM de terceiros é guardado diretamente no Alpine reativo.
        window.vaultTiptapField = (modelRef, field, maxLength = 30000, placeholder = '', owner = null) => ({
            modelRef, field, maxLength, placeholder, owner,
            richEditor: null,
            mounted: false,
            fallbackInitialized: false,
            changed: 0,
            readyListener: null,

            // Recebe uma função para recuperar o formulário atual. O modal Blade
            // permanece montado entre aberturas: guardar `form` por valor ficava obsoleto.
            activeModel() {
                return typeof this.modelRef === 'function' ? this.modelRef() : this.modelRef;
            },

            resetForCurrentItem() {
                this.unmount();
                this.fallbackInitialized = false;
                this.prepareFallback();
                this.mount();
            },

            init() {
                this.readyListener = () => this.$nextTick(() => this.mount());
                window.addEventListener('spellbound:vault-tiptap-ready', this.readyListener);
                this.$watch('editorOpen', open => {
                    if (open) this.$nextTick(() => this.resetForCurrentItem());
                    else this.unmount();
                });
                if (this.owner) {
                    this.$watch('owner.editor_open', open => {
                        if (open) this.$nextTick(() => this.mount());
                        else this.unmount();
                    });
                }
                this.$nextTick(() => {
                    if (this.editorOpen) this.resetForCurrentItem();
                });
            },

            // O fallback também é contenteditable: os atalhos formatam visualmente,
            // nunca acrescentam ** ou * ao texto do usuário.
            fallbackElement() {
                return this.$refs.fallbackHost?.querySelector('[data-vault-fallback]') ?? null;
            },

            // Conversão e sanitização em lista de tags permitidas (inclusive texto legado).
            safeEditorHtml(raw) {
                if (window.SpellboundVaultRichText?.toEditorHtml) {
                    return window.SpellboundVaultRichText.toEditorHtml(raw);
                }
                const source = String(raw ?? '');
                const escape = text => String(text).replace(/[&<>"']/g, c => ({
                    '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;',
                })[c]);
                const markdown = text => escape(text)
                    .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/(^|\n)\*\*([^*\n]+)(?=\n|$)/g, '$1<strong>$2</strong>')
                    .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                if (!/<(?:p|br|strong|b|em|i|ul|ol|li|blockquote|h[1-6]|s|del|div|hr)\b/i.test(source)) {
                    const result = [];
                    let paragraph = [];
                    const flush = () => {if (paragraph.length) {result.push('<p>' + paragraph.join('<br>') + '</p>'); paragraph = [];}};
                    for (const line of markdown(source.replace(/\r\n?/g, '\n')).split('\n')) {
                        if (/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/.test(line)) {flush(); result.push('<hr>');}
                        else if (!line.trim()) flush();
                        else paragraph.push(line);
                    }
                    flush();
                    return result.join('');
                }
                return this.sanitizeFallbackHtml(source);
            },

            sanitizeFallbackHtml(source) {
                const doc = new DOMParser().parseFromString('<body>' + String(source ?? '') + '</body>', 'text/html');
                const allowed = new Set(['p','br','strong','em','s','ul','ol','li','blockquote','h3','code','hr']);
                const blocked = new Set(['script','style','iframe','svg','math','object','embed','form','template']);
                const escape = text => String(text).replace(/[&<>"']/g, c => ({
                    '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;',
                })[c]);
                const clean = node => {
                    if (node.nodeType === 3) return escape(node.textContent)
                        .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/^\*\*([^*\n]+)$/g, '<strong>$1</strong>')
                        .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                    if (node.nodeType !== 1) return '';
                    const tag = node.tagName.toLowerCase();
                    if (blocked.has(tag)) return '';
                    if (tag === 'hr' || ((tag === 'p' || tag === 'div') && /^(?:-{3,}|\*{3,}|_{3,})$/.test(node.textContent.trim()))) return '<hr>';
                    const body = Array.from(node.childNodes).map(clean).join('');
                    const name = ({b:'strong', i:'em', del:'s', strike:'s', div:'p'})[tag] ?? tag;
                    return allowed.has(name) ? '<' + name + '>' + body + '</' + name + '>' : body;
                };
                return Array.from(doc.body.childNodes).map(clean).join('');
            },

            prepareFallback() {
                if (this.fallbackInitialized) return;
                const editable = this.fallbackElement();
                if (!editable) return;
                editable.setAttribute('data-placeholder', this.placeholder);
                editable.innerHTML = this.safeEditorHtml(this.activeModel()?.[this.field] ?? '');
                this.fallbackInitialized = true;
            },

            syncFallback() {
                if (this.mounted) return;
                const editable = this.fallbackElement();
                const model = this.activeModel();
                if (!editable || !model) return;
                const safe = this.sanitizeFallbackHtml(editable.innerHTML);
                model[this.field] = (safe === '<p><br></p>' || safe === '<br>') ? '' : safe;
                this.changed++;
            },

            pasteFallback(event) {
                event.preventDefault();
                const text = event.clipboardData?.getData('text/plain') ?? '';
                document.execCommand('insertText', false, text);
                this.syncFallback();
            },

            fallbackDividerFromText(event) {
                if (event.key !== 'Enter' && event.key !== ' ') return false;
                const editable = this.fallbackElement();
                const selection = window.getSelection();
                const anchor = selection?.anchorNode;
                if (!editable || !anchor || !editable.contains(anchor)) return false;
                const parent = anchor.nodeType === Node.ELEMENT_NODE ? anchor : anchor.parentElement;
                const block = parent?.closest('p,div,blockquote,li');
                const candidate = block && block !== editable && editable.contains(block)
                    ? block
                    : (anchor.nodeType === Node.TEXT_NODE && anchor.parentElement === editable ? anchor : editable);
                if (!/^(?:-{3,}|\*{3,}|_{3,})$/.test((candidate.textContent ?? '').trim())) return false;
                event.preventDefault();
                const hr = document.createElement('hr');
                const paragraph = document.createElement('p');
                paragraph.appendChild(document.createElement('br'));
                if (candidate === editable) editable.replaceChildren(hr, paragraph);
                else candidate.replaceWith(hr, paragraph);
                const range = document.createRange();
                range.selectNodeContents(paragraph);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
                this.syncFallback();
                return true;
            },

            fallbackHotkey(event) {
                if (this.fallbackDividerFromText(event)) return;
                if (!(event.ctrlKey || event.metaKey) || event.altKey) return;
                const shortcuts = { b:'bold', i:'italic', z:event.shiftKey ? 'redo' : 'undo', y:'redo' };
                const command = shortcuts[String(event.key).toLowerCase()];
                if (!command) return;
                event.preventDefault();
                this.action(command);
            },

            mount() {
                if (this.richEditor || !this.editorOpen || !this.activeModel() || (this.owner && !this.owner.editor_open)) return;
                const creator = window.SpellboundVaultRichText?.createEditor;
                const surface = this.$refs.editorSurface?.querySelector('[data-vault-tiptap-mount]');
                if (!creator || !surface || !surface.isConnected) return;
                this.prepareFallback();
                // Preserva o texto digitado antes da inicialização tardia do módulo.
                this.syncFallback();
                try {
                    this.richEditor = creator({
                        element: surface,
                        content: this.activeModel()?.[this.field] ?? '',
                        placeholder: this.placeholder,
                        onUpdate: html => {
                            const model = this.activeModel();
                            if (model) model[this.field] = html;
                            this.changed++;
                        },
                        onSelectionUpdate: () => { this.changed++; },
                    });
                    this.mounted = true;
                    this.changed++;
                } catch (error) {
                    console.error('[Spellbound] Inicialização do Tiptap falhou:', error);
                    this.richEditor = null;
                    this.mounted = false;
                }
            },

            unmount() {
                if (this.richEditor) {
                    try { this.richEditor.destroy(); } catch (error) { console.warn(error); }
                }
                this.richEditor = null;
                this.mounted = false;
            },

            destroy() {
                if (this.readyListener) {
                    window.removeEventListener('spellbound:vault-tiptap-ready', this.readyListener);
                }
                this.unmount();
            },

            action(command) {
                if (!this.richEditor) this.mount();
                if (this.richEditor) {
                    this.richEditor.execute(command);
                    this.changed++;
                    return;
                }
                // Fallback rich-text de verdade: HTML formatado, não Markdown.
                this.prepareFallback();
                const editable = this.fallbackElement();
                if (!editable) return;
                editable.focus();
                const options = {
                    horizontalRule:'insertHorizontalRule', bold:'bold', italic:'italic', strike:'strikeThrough',
                    bulletList:'insertUnorderedList', orderedList:'insertOrderedList',
                    blockquote:'formatBlock', heading:'formatBlock',
                    undo:'undo', redo:'redo', clear:'removeFormat',
                };
                const cmd = options[command];
                if (!cmd) return;
                const param = command === 'heading' ? 'h3' : (command === 'blockquote' ? 'blockquote' : null);
                document.execCommand(cmd, false, param);
                this.syncFallback();
            },

            active(command) {
                this.changed;
                if (this.richEditor) return this.richEditor.isActive(command);
                const state = {bold:'bold',italic:'italic',strike:'strikeThrough',
                    bulletList:'insertUnorderedList',orderedList:'insertOrderedList'}[command];
                return state && document.activeElement === this.fallbackElement()
                    ? !!document.queryCommandState(state) : false;
            },

            get contentLength() {
                this.changed;
                return String(this.activeModel()?.[this.field] ?? '').length;
            },
        });
    </script>
    @endonce

    {{-- O editor continua utilizável se o bundle Vite não estiver disponível. --}}
    @vite('resources/js/master-vault-richtext.js')

    @once
    <script>
        window.masterVaultPage = (
            initialItems,
            initialFolders,
            weaponTraitDefinitions,
            masteryDefinitions,
            abilityLabels,
            recoveryLabels,
            damageTypeDefinitions,
            urls,
            folderConfig,
            initialSpellCatalog,
            initialSpellCampaigns,
            initialSpellSourceNames
        ) => ({
@include('master.vault.partials.comportamento')
        });
    </script>
    @endonce

    <div
        class="master-vault-page"
        x-data="masterVaultPage(
            @js($vaultItems),
            @js($vaultFolders ?? []),
            @js($weaponTraitDefinitions),
            @js($masteryDefinitions),
            @js($abilityLabels),
            @js($recoveryLabels),
            @js($damageTypeDefinitions),
            {
                spellDetail: @js(route('master.vault.items.index')),
                store: @js(route('master.vault.items.store')),
                update: @js(route('master.vault.items.update', ['vaultItem' => '__ITEM__'])),
                duplicate: @js(route('master.vault.items.duplicate', ['vaultItem' => '__ITEM__'])),
                destroy: @js(route('master.vault.items.destroy', ['vaultItem' => '__ITEM__'])),
                folderStore: @js(route('workspace-folders.store')),
                folderUpdate: @js(route('workspace-folders.update', ['workspaceFolder' => '__FOLDER__'])),
                folderDestroy: @js(route('workspace-folders.destroy', ['workspaceFolder' => '__FOLDER__'])),
                moveFolder: @js(route('workspace-folder-items.move')),
            },
            {
                ready: @js($vaultFolderSystemReady ?? false),
                scopeId: @js($vaultScopeId ?? auth()->id()),
                userId: @js(auth()->id()),
            },
            @js($vaultSpellCatalog),
            @js($vaultSpellCampaigns),
            @js($vaultSpellSourceNames)
        )"
        x-init="
            if (typeof openCreate !== 'function') {
                console.error('[Vault] Alpine não inicializou masterVaultPage corretamente.');
            }
        "
        @keydown.escape.window="scrollBookModalOpen ? closeScrollLibrary() : (folderModalOpen ? closeFolderModal() : (actionMenuId ? closeActionMenu() : (confirmOpen ? closeConfirm() : (previewOpen ? closePreview() : (editorOpen ? closeEditor() : null)))))"
    >
@include('master.vault.partials.biblioteca')
@include('master.vault.show')
@include('master.vault.partials.editor')
        <template x-if="folderModalOpen">
            <div class="master-vault-v11-modal-layer" @click.self="closeFolderModal()">
                <section class="master-vault-v11-modal" @click.stop>
                    <header class="master-vault-v11-modal-head">
                        <div class="master-vault-v11-modal-headline">
                            <span class="master-vault-v11-modal-headicon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h6l2 2h10v9H3z" stroke-width="1.7" stroke-linejoin="round"/>
                                </svg>
                            </span>

                            <div>
                                <h2 x-text="folderEditingId ? 'Editar pasta' : 'Nova pasta'"></h2>
                                <p>Organize seus itens e conteúdos</p>
                            </div>
                        </div>

                        <button type="button" class="master-vault-close" @click="closeFolderModal()" :disabled="folderSaving">×</button>
                    </header>

                    <div class="master-vault-v11-modal-body">
                        <div class="master-vault-v11-modal-stack">
                            <label class="master-vault-v4-field">
                                <span class="master-vault-v11-label">Nome da Pasta</span>
                                <input
                                    type="text"
                                    maxlength="120"
                                    x-model="folderForm.name"
                                    placeholder="Ex: Taverna do Dragão, Vilões..."
                                    class="master-vault-v4-input"
                                >
                            </label>

                            <label class="master-vault-v4-field">
                                <span class="master-vault-v11-label">Subtítulo <small>(opcional)</small></span>
                                <textarea
                                    maxlength="10000"
                                    x-model="folderForm.description"
                                    placeholder="Breve descrição sobre esta pasta..."
                                    class="master-vault-v4-textarea"
                                ></textarea>
                            </label>

                            <div>
                                <span class="master-vault-v11-label">Cor de Destaque</span>
                                <div class="master-vault-v11-color-grid">
                                    <template x-for="colorOption in folderPalette" :key="'vault-folder-color-' + colorOption.key">
                                        <button
                                            type="button"
                                            class="master-vault-v11-color-option"
                                            :class="{ 'is-active': String(folderForm.color).toUpperCase() === String(colorOption.color).toUpperCase() }"
                                            :style="{ '--swatch-color': colorOption.color }"
                                            @click="folderForm.color = colorOption.color"
                                        >
                                            <span class="master-vault-v11-color-swatch"></span>
                                            <strong x-text="colorOption.label"></strong>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <p x-show="folderError" x-cloak class="master-vault-error" x-text="folderError"></p>
                    </div>

                    <footer class="master-vault-v11-modal-foot">
                        <button type="button" class="master-vault-v4-secondary" @click="closeFolderModal()" :disabled="folderSaving">Cancelar</button>

                        <button type="button" class="master-vault-v4-primary" @click="saveFolder()" :disabled="folderSaving" x-text="folderSaving ? 'Salvando...' : (folderEditingId ? 'Salvar pasta' : 'Criar pasta')"></button>
                    </footer>
                </section>
            </div>
        </template>

        <div x-show="toastOpen" x-cloak class="master-vault-toast" :class="{ 'is-error': toastError }" x-transition.opacity.duration.120ms x-text="toastMessage"></div>
    </div>

</x-app-layout>
@endif
