@php
    /*
    |--------------------------------------------------------------------------
    | Estado inicial de HP dos Players
    |--------------------------------------------------------------------------
    |
    | Depois do primeiro render, o Alpine atualiza estes valores a cada
    | segundo usando o endpoint leve de playerStates().
    |
    */

    // Cada execução preserva seu início/fim, mesmo que o encontro seja reiniciado.
    $latestCombatRun = \Illuminate\Support\Facades\DB::table('combat_runs')
        ->where('combat_id', $combat->id)
        ->orderByDesc('id')
        ->first();

    $initialPlayerStates =
        $combatPlayers
            ->filter(
                fn ($player) =>
                    $player->character_id !== null
            )
            ->mapWithKeys(
                function ($player): array {
                    $character =
                        $player->character;

                    $characterCombat =
                        $character?->combat;

                    if (!$characterCombat) {
                        return [
                            (string) $player->id => [
                                'combat_player_id' =>
                                    (int) $player->id,

                                'character_id' =>
                                    $character
                                        ? (int) $character->id
                                        : null,

                                'has_combat' =>
                                    false,

                                'initiative' =>
                                    (int) $player->initiative,
                            ],
                        ];
                    }

                    $maxHp =
                        max(
                            1,
                            (int) $characterCombat->max_hp
                            +
                            (int) $characterCombat->temporary_max_hp
                        );

                    $currentHp =
                        max(
                            0,
                            (int) $characterCombat->current_hp
                        );

                    return [
                        (string) $player->id => [
                            'combat_player_id' =>
                                (int) $player->id,

                            'character_id' =>
                                (int) $character->id,

                            'has_combat' =>
                                true,

                            'current_hp' =>
                                $currentHp,

                            'max_hp' =>
                                $maxHp,

                            'temporary_hp' =>
                                max(
                                    0,
                                    (int) $characterCombat->temporary_hp
                                ),

                            'percent' =>
                                round(
                                    max(
                                        0,
                                        min(
                                            100,
                                            (
                                                $currentHp
                                                /
                                                $maxHp
                                            )
                                            * 100
                                        )
                                    ),
                                    2
                                ),

                            'at_zero_hp' =>
                                $currentHp <= 0,

                            'initiative' =>
                                (int) $player->initiative,
                        ],
                    ];
                }
            )
            ->all();
    // Estados iniciais das condições por participante; este é um estado por combate.
    $conditionPreset = \Illuminate\Support\Facades\DB::table('combat_player_conditions')
        ->where('combat_id', $combat->id)
        ->where('is_active', true)
        ->get(['combat_player_id', 'condition_key'])
        ->groupBy('combat_player_id')
        ->map(fn ($rows) => $rows->pluck('condition_key')->all())
        ->all();
    // Exaustão permanece exclusivamente no módulo próprio da ficha.
    // Invisibilidade não faz mais parte das condições que o Mestre aplica na iniciativa.
    $conditionCatalog = collect(\App\Support\CombatConditions::all())
        ->except(['invisible', 'exhaustion'])
        ->all();
    // Apenas o rótulo da iniciativa muda; a chave persistida permanece 'restrained'.
    if (isset($conditionCatalog['restrained']) && is_array($conditionCatalog['restrained'])) {
        $conditionCatalog['restrained']['name'] = 'Restringido';
    }
@endphp

<div
    id="combat-initiative-panel-host"

    x-data="{

        loading: false,

        toolDrawerOpen: false,
        quickToolsExpanded: false,
        addMenuOpen: false,
        toolsView: 'home',
        floatingCalcOpen: false,
        calcPositionX: 0,
        calcPositionY: 0,
        calcDrag: null,
        spellQuery: '',
        spellResults: [],
        spellSearching: false,
        spellSearched: false,
        spellError: '',
        spellRequestSequence: 0,
        selectedSpell: null,
        calculationSteps: [],
        spellLookupUrl: '{{ route('master.spells.lookup') }}',
        spellLookupCombatId: {{ (int) $combat->id }},
        diceRollUrl: '{{ url('/api/roll') }}',


        expression: '',

        result: null,

        error: null,

        isRolling: false,

        selectedNpc: sessionStorage.getItem('combat_{{ $combat->id }}_selected') ? parseInt(sessionStorage.getItem('combat_{{ $combat->id }}_selected')) : null,

        playerStates: @js($initialPlayerStates),

        conditionByPlayer: @js($conditionPreset),
        conditionCatalog: @js($conditionCatalog),
        conditionMenuPlayerId: null,
        conditionBusy: false,
        conditionError: '',
        conditionSearch: '',

        togglePlayerConditions(playerId) {
            this.conditionMenuPlayerId = this.conditionMenuPlayerId === playerId ? null : playerId;
            this.conditionSearch = '';
            this.conditionError = '';
        },

        availablePlayerConditions(playerId) {
            const query = String(this.conditionSearch || '').normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
            return Object.entries(this.conditionCatalog).filter(([key, meta]) => {
                if (this.isPlayerConditionActive(playerId, key)) return false;
                if (!query) return true;
                return String(meta.name || key).normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '').toLowerCase().includes(query);
            });
        },
        activePlayerConditions(playerId) {
            return Object.entries(this.conditionCatalog).filter(([key]) =>
                this.isPlayerConditionActive(playerId, key));
        },
        isPlayerConditionActive(playerId, key) {
            return (this.conditionByPlayer[String(playerId)] || []).includes(key);
        },

        async setPlayerCondition(playerId, url, key, forceRemove = false) {
            if (this.conditionBusy) return;
            const active = !forceRemove && !this.isPlayerConditionActive(playerId,key);
            this.conditionBusy = true;
            this.conditionError = '';
            try {
                const response = await fetch(url, {
                    method: 'PATCH', credentials: 'same-origin',
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                    body: JSON.stringify({condition:key, active, duration_turns: null, source: null})
                });
                if (!response.ok) throw new Error(response.status === 419 ? 'Sessão expirada. Atualize a página.' : ('Falha ao salvar condição (' + response.status + ').'));
                const data = await response.json();
                if (!data.success) throw new Error('O servidor não confirmou a alteração.');
                this.conditionByPlayer = {...this.conditionByPlayer, [String(playerId)]: (data.conditions || []).map(c => c.key)};
                window.dispatchEvent(new CustomEvent('spellbound:conditions-changed', {detail:{playerId}}));
            } catch (error) {
                this.conditionError = error.message || 'Não foi possível salvar.';
            } finally { this.conditionBusy = false; }
        },

        playerStatesUrl: @js(
            route(
                'combats.players.states',
                $combat
            )
        ),

        playerHpSyncing: false,

        playerHpTimer: null,

        initiativePanelRefreshing: false,

        init() {
            this.startPlayerHpSync();
        },

        destroy() {
            this.stopPlayerHpSync();
        },

        startPlayerHpSync() {
            const registryKey =
                'combat_{{ $combat->id }}';

            window.__spellboundCombatHpTimers =
                window.__spellboundCombatHpTimers
                ?? {};

            /*
            | Se o wrapper foi reconstruído por AJAX, encerra o timer antigo
            | antes de iniciar o novo.
            */
            if (
                window
                    .__spellboundCombatHpTimers[
                        registryKey
                    ]
            ) {
                clearInterval(
                    window
                        .__spellboundCombatHpTimers[
                            registryKey
                        ]
                );
            }

            this.refreshPlayerStates();

            this.playerHpTimer =
                setInterval(
                    () => {
                        this.refreshPlayerStates();
                    },
                    1000
                );

            window
                .__spellboundCombatHpTimers[
                    registryKey
                ] =
                    this.playerHpTimer;
        },

        stopPlayerHpSync() {
            if (
                this.playerHpTimer
            ) {
                clearInterval(
                    this.playerHpTimer
                );

                this.playerHpTimer =
                    null;
            }
        },

        async refreshPlayerStates() {
            if (
                this.playerHpSyncing
                ||
                document.hidden
            ) {
                return;
            }

            this.playerHpSyncing =
                true;

            try {
                const response =
                    await fetch(
                        this.playerStatesUrl,
                        {
                            method:
                                'GET',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            cache:
                                'no-store',
                        }
                    );

                if (
                    response.status === 401
                    ||
                    response.status === 403
                    ||
                    response.status === 404
                ) {
                    this.stopPlayerHpSync();
                    return;
                }

                if (!response.ok) {
                    return;
                }

                const data =
                    await response.json();

                if (
                    data?.success
                    &&
                    data?.players
                ) {
                    const previousStates =
                        this.playerStates
                        ?? {};

                    const nextStates =
                        data.players;

                    const previousKeys =
                        Object.keys(
                            previousStates
                        )
                            .sort()
                            .join(',');

                    const nextKeys =
                        Object.keys(
                            nextStates
                        )
                            .sort()
                            .join(',');

                    const rosterChanged =
                        previousKeys
                        !==
                        nextKeys;

                    const initiativeChanged =
                        Object.entries(
                            nextStates
                        ).some(
                            ([
                                playerId,
                                state
                            ]) => {
                                const previous =
                                    previousStates?.[
                                        playerId
                                    ];

                                if (!previous) {
                                    return true;
                                }

                                return (
                                    Number(
                                        previous.initiative
                                        ?? 0
                                    )
                                    !==
                                    Number(
                                        state?.initiative
                                        ?? 0
                                    )
                                );
                            }
                        );

                    this.playerStates =
                        nextStates;

                    // A ficha do jogador pode encerrar uma condição. O Mestre vê
                    // a atualização no polling já existente, sem recarregar a iniciativa.
                    Object.entries(nextStates).forEach(([playerId, state]) => {
                        if (!Array.isArray(state?.condition_keys)) return;
                        this.conditionByPlayer = {
                            ...this.conditionByPlayer,
                            [String(playerId)]: state.condition_keys
                        };
                    });

                    if (
                        rosterChanged
                        ||
                        initiativeChanged
                    ) {
                        await this.refreshInitiativePanel();
                    }
                }

            } catch (error) {
                /*
                | Falha transitória de rede não deve quebrar a interface.
                | O próximo intervalo tenta novamente.
                */

            } finally {
                this.playerHpSyncing =
                    false;
            }
        },

        async refreshInitiativePanel() {
            if (
                this.initiativePanelRefreshing
            ) {
                return;
            }

            const currentPanel =
                document.getElementById(
                    'combat-initiative-panel-host'
                );

            if (!currentPanel) {
                return;
            }

            this.initiativePanelRefreshing =
                true;

            try {
                const response =
                    await fetch(
                        window.location.href,
                        {
                            cache:
                                'no-store',

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                if (!response.ok) {
                    return;
                }

                const html =
                    await response.text();

                const updatedDocument =
                    new DOMParser()
                        .parseFromString(
                            html,
                            'text/html'
                        );

                const updatedPanel =
                    updatedDocument
                        .getElementById(
                            'combat-initiative-panel-host'
                        );

                if (!updatedPanel) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Atualização parcial da fila
                |--------------------------------------------------------------------------
                |
                | A rolagem do Player pode alterar a posição dele na iniciativa.
                | Recarregamos somente o conteúdo do painel lateral para que a
                | ordenação calculada pelo CombatInitiativeService apareça sem F5.
                |
                | O root Alpine permanece vivo, então:
                | - polling de HP continua;
                | - selectedNpc continua preservado;
                | - não reconstruímos a área central.
                |
                */

                /*
                 * NÃO reinicialize o root Alpine aqui.
                 *
                 * O próprio currentPanel é o elemento que possui x-data.
                 * Reinicializá-lo após cada atualização parcial zera
                 * playerStates para o snapshot do HTML recém-buscado e pode
                 * fazer o próximo polling detectar a mesma iniciativa como
                 * a mesma mudança novamente, criando um ciclo de atualização.
                 *
                 * Alpine observa mutações no DOM e inicializa normalmente os
                 * novos nós inseridos no innerHTML. O estado do root precisa
                 * permanecer vivo.
                 */
                currentPanel.innerHTML =
                    updatedPanel.innerHTML;

            } catch (error) {
                console.error(
                    'Erro ao atualizar iniciativa dos Players:',
                    error
                );

            } finally {
                this.initiativePanelRefreshing =
                    false;
            }
        },

        playerHpClass(playerId) {
            const percent =
                Number(
                    this.playerStates?.[
                        playerId
                    ]?.percent
                    ?? 0
                );

            if (percent > 50) {
                return 'bg-emerald-500';
            }

            if (percent > 25) {
                return 'bg-amber-400';
            }

            return 'bg-red-500';
        },

        async selectNpc(
            npcId
        ) {
            this.selectedNpc =
                npcId;

            sessionStorage.setItem(
                'combat_{{ $combat->id }}_selected',
                npcId
            );

            const currentHost =
                document.getElementById(
                    'combat-npc-panel-host'
                );

            if (!currentHost) {
                return;
            }

            try {
                const response =
                    await fetch(
                        window.location.href,
                        {
                            cache:
                                'no-store',

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );

                if (!response.ok) {
                    return;
                }

                const html =
                    await response.text();

                const updatedDocument =
                    new DOMParser()
                        .parseFromString(
                            html,
                            'text/html'
                        );

                const updatedHost =
                    updatedDocument
                        .getElementById(
                            'combat-npc-panel-host'
                        );

                if (!updatedHost) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Atualiza somente a área da ficha do NPC
                |--------------------------------------------------------------------------
                |
                | O npc-panel é reinicializado depois que o ID escolhido já foi
                | salvo no sessionStorage. Assim ele abre imediatamente a mesma
                | ficha que já seria restaurada em um F5/refresh do combate.
                |
                | A iniciativa não é reconstruída, portanto:
                | - o polling de HP dos Players continua ativo;
                | - inputs da iniciativa não piscam;
                | - o NPC clicado continua destacado.
                |
                */

                currentHost.innerHTML =
                    updatedHost.innerHTML;

                if (window.Alpine) {
                    Alpine.initTree(
                        currentHost
                    );
                }

            } catch (error) {
                console.error(
                    'Erro ao abrir ficha do NPC:',
                    error
                );
            }
        },

        openToolDrawer(view = 'home') {
            this.toolsView = view;
            this.toolDrawerOpen = true;
            if (view === 'spells') this.$nextTick(() => this.$refs.globalSpellSearch?.focus());
        },
        openFloatingCalculator() {
            if (!this.floatingCalcOpen) {
                this.calcPositionX = Math.max(12, Math.min(window.innerWidth - 340, window.innerWidth * .5 - 160));
                this.calcPositionY = Math.max(24, Math.min(window.innerHeight - 440, 110));
            }
            this.floatingCalcOpen = true;
            this.toolDrawerOpen = false;
            this.$nextTick(() => this.$refs.calcExpression?.focus());
        },
        closeFloatingCalculator() {
            this.floatingCalcOpen = false;
            this.calcDrag = null;
        },
        startCalcDrag(event) {
            if (event.button !== 0) return;
            this.calcDrag = {
                pointer: event.pointerId,
                startX: event.clientX,
                startY: event.clientY,
                x: this.calcPositionX,
                y: this.calcPositionY
            };
            event.preventDefault();
        },
        moveCalcDrag(event) {
            if (!this.calcDrag || this.calcDrag.pointer !== event.pointerId) return;
            const maxX = Math.max(10, window.innerWidth - 320);
            const maxY = Math.max(10, window.innerHeight - 120);
            this.calcPositionX = Math.max(10, Math.min(maxX, this.calcDrag.x + event.clientX - this.calcDrag.startX));
            this.calcPositionY = Math.max(10, Math.min(maxY, this.calcDrag.y + event.clientY - this.calcDrag.startY));
        },
        stopCalcDrag() { this.calcDrag = null; },
        appendCalculatorToken(token) {
            if (this.isRolling) return;
            const part = String(token || '');
            if (!/^(?:[0-9]|[+*/().-]|d(?:4|6|8|10|12|20|100))$/i.test(part)) return;
            const current = String(this.expression || '');
            const prefix = /^d/i.test(part) && !/[0-9]$/.test(current.trim()) ? '1' : '';
            this.expression = current + prefix + part;
            this.result = null;
            this.error = null;
            this.calculationSteps = [];
            this.$nextTick(() => this.$refs.calcExpression?.focus());
        },
        backspaceCalculator() {
            if (this.isRolling) return;
            this.expression = String(this.expression || '').slice(0, -1);
            this.result = null;
            this.error = null;
            this.calculationSteps = [];
            this.$nextTick(() => this.$refs.calcExpression?.focus());
        },
        clearCalculator() {
            if (this.isRolling) return;
            this.expression = '';
            this.error = null;
            this.result = null;
            this.calculationSteps = [];
            this.$nextTick(() => this.$refs.calcExpression?.focus());
        },
        handleCalculatorKey(event) {
            if (!this.floatingCalcOpen || event.ctrlKey || event.altKey || event.metaKey) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                this.closeFloatingCalculator();
                return;
            }
            const target = event.target;
            if (target?.isContentEditable || /^(?:INPUT|TEXTAREA|SELECT)$/.test(target?.tagName || '')) return;
            if (event.key === 'Enter') { event.preventDefault(); this.calculateRoll(); return; }
            if (event.key === 'Backspace') { event.preventDefault(); this.backspaceCalculator(); return; }
            const key = event.key === 'x' || event.key === 'X' ? '*' : event.key;
            if (/^[0-9dD+*/().-]$/.test(key)) {
                event.preventDefault();
                this.appendCalculatorToken(key.toLowerCase());
            }
        },
        tokenizeCalculation(source) {
            const input = String(source || '').replace(/\s+/g, '')
                .replace(/,/g, '.').replace(/[×xX]/g, '*').replace(/÷/g, '/')
                .replace(/[−–]/g, '-');
            if (!input || input.length > 180) throw new Error('Expressão vazia ou muito longa (máximo 180 caracteres).');
            const tokens = [];
            let position = 0;
            let totalDice = 0;
            while (position < input.length) {
                const rest = input.slice(position);
                const dice = rest.match(/^(\d*)d(100|20|12|10|8|6|4)(?!\d)/i);
                if (dice) {
                    const count = dice[1] === '' ? 1 : Number(dice[1]);
                    if (!Number.isInteger(count) || count < 1 || count > 100)
                        throw new Error('Use entre 1 e 100 dados por grupo.');
                    totalDice += count;
                    if (totalDice > 200) throw new Error('Limite de 200 dados por cálculo.');
                    tokens.push({type: 'dice', count, sides: Number(dice[2]), label: count + 'd' + dice[2]});
                    position += dice[0].length;
                    continue;
                }
                const number = rest.match(/^(?:\d+(?:\.\d+)?|\.\d+)/);
                if (number) {
                    tokens.push({type: 'number', value: Number(number[0])});
                    position += number[0].length;
                    continue;
                }
                if (/^[()+*/-]/.test(rest)) {
                    tokens.push({type: 'operator', value: rest[0]});
                    position++;
                    continue;
                }
                throw new Error('Expressão inválida perto de: ' + rest.slice(0, 12));
            }
            if (tokens.length > 90) throw new Error('Expressão muito longa.');
            return tokens;
        },
        parseCalculation(tokens) {
            let i = 0;
            const peek = () => tokens[i]?.value;
            const atom = () => {
                if (peek() === '+' || peek() === '-') {
                    const op = tokens[i++].value;
                    return {type: 'unary', op, inner: atom()};
                }
                if (peek() === '(') {
                    i++;
                    const inner = sum();
                    if (peek() !== ')') throw new Error('Feche todos os parênteses.');
                    i++;
                    return inner;
                }
                const token = tokens[i++];
                if (!token || !['number', 'dice'].includes(token.type))
                    throw new Error('Expressão incompleta. Confira números e operadores.');
                return token;
            };
            const product = () => {
                let value = atom();
                while (peek() === '*' || peek() === '/') {
                    const op = tokens[i++].value;
                    value = {type: 'binary', op, left: value, right: atom()};
                }
                return value;
            };
            const sum = () => {
                let value = product();
                while (peek() === '+' || peek() === '-') {
                    const op = tokens[i++].value;
                    value = {type: 'binary', op, left: value, right: product()};
                }
                return value;
            };
            const tree = sum();
            if (i !== tokens.length) throw new Error('Verifique operadores e parênteses.');
            return tree;
        },
        async evaluateCalculation(node, details) {
            if (node.type === 'number') return node.value;
            if (node.type === 'unary') {
                const n = await this.evaluateCalculation(node.inner, details);
                return node.op === '-' ? -n : n;
            }
            if (node.type === 'binary') {
                const a = await this.evaluateCalculation(node.left, details);
                const b = await this.evaluateCalculation(node.right, details);
                if (node.op === '/' && b === 0) throw new Error('Não é possível dividir por zero.');
                return node.op === '+' ? a + b : node.op === '-' ? a - b : node.op === '*' ? a * b : a / b;
            }
            const response = await fetch(this.diceRollUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json', 'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({expression: node.label})
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || data.success === false)
                throw new Error(data.message || 'Não foi possível rolar ' + node.label + '.');
            const total = Number(data?.data?.total ?? data?.data?.result ?? data?.data?.value ?? data?.total ?? data?.result);
            if (!Number.isFinite(total)) throw new Error('A rolagem ' + node.label + ' não retornou um total válido.');
            details.push({label: node.label, total, formatted: data.formatted || ''});
            return total;
        },
        formatCalculationNumber(value) {
            return (Math.round(value * 100000000) / 100000000).toLocaleString('pt-BR', {maximumFractionDigits: 8});
        },
        async calculateRoll() {
            if (this.isRolling) return;
            this.error = null;
            this.result = null;
            this.calculationSteps = [];
            this.isRolling = true;
            try {
                const tree = this.parseCalculation(this.tokenizeCalculation(this.expression));
                const details = [];
                const value = await this.evaluateCalculation(tree, details);
                if (!Number.isFinite(value) || Math.abs(value) > 1e12)
                    throw new Error('O total está fora do intervalo permitido.');
                this.result = this.formatCalculationNumber(value);
                this.calculationSteps = details;
            } catch (err) {
                this.error = err.message || 'Não foi possível calcular.';
            } finally {
                this.isRolling = false;
            }
        },
        async rollDice() { return this.calculateRoll(); },
        async searchGlobalSpells() {
            const query = String(this.spellQuery || '').trim();
            const requestId = ++this.spellRequestSequence;
            this.selectedSpell = null;
            this.spellError = '';
            this.spellSearched = query.length >= 2;
            if (query.length < 2) {
                this.spellResults = [];
                this.spellSearching = false;
                return;
            }
            this.spellSearching = true;
            try {
                const params = new URLSearchParams({
                    combat_id: String(this.spellLookupCombatId),
                    q: query,
                });
                const response = await fetch(this.spellLookupUrl + '?' + params.toString(), {
                    credentials: 'same-origin', cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || data.success === false) {
                    const validationError = Object.values(data.errors || {})[0];
                    const message = Array.isArray(validationError) ? validationError[0] : null;
                    throw new Error(message || data.message ||
                        (response.status === 403 ? 'Sem permissão para consultar este combate.' :
                         response.status === 404 ? 'Combate não encontrado ou não autorizado.' :
                         'Falha na consulta de feitiços (HTTP ' + response.status + '). Verifique o log do Laravel.'));
                }
                if (requestId === this.spellRequestSequence) {
                    this.spellResults = Array.isArray(data.spells) ? data.spells : [];
                    this.spellError = this.spellResults.length ? '' : (data.message || '');
                }
            } catch (err) {
                if (requestId === this.spellRequestSequence) {
                    this.spellResults = [];
                    this.spellError = err.message || 'Erro ao pesquisar os feitiços.';
                }
            } finally {
                if (requestId === this.spellRequestSequence) this.spellSearching = false;
            }
        },
        async handleInitiativeAction(e) {

            this.loading = true;

            const form = e.target;

            const wrapper = document.getElementById('combat-panels-wrapper');

            try {

                await fetch(form.action, {

                    method: 'POST',

                    body: new FormData(form),

                    headers: { 'X-Requested-With': 'XMLHttpRequest' }

                });

                const res = await fetch(window.location.href, { cache: 'no-store' });

                const html = await res.text();

                const doc = new DOMParser().parseFromString(html, 'text/html');

                const newContent = doc.getElementById('combat-panels-wrapper');

                if (newContent) {

                    wrapper.innerHTML = newContent.innerHTML;

                    if (window.Alpine) Alpine.initTree(wrapper);

                }

            } catch (err) {

                console.error('Erro ao atualizar:', err);

            } finally {

                this.loading = false;

            }

        }

    }"

    class="h-full flex flex-col bg-[#f9f6f0] select-none transition-opacity duration-300"

    :class="loading ? 'opacity-50 pointer-events-none scale-[0.99]' : 'opacity-100 scale-100'"

>
    <style>

        /* V66.0.39 - Central de ferramentas sem alterar largura da iniciativa */
        .sb-master-tools { --sb-surface: var(--theme-paper-strong, #fffaf4); --sb-ink: var(--theme-text, #4d2b23); --sb-accent: var(--theme-accent-strong, #6b1d14); --sb-border: var(--theme-border, #d8c2a9); font-family:system-ui, sans-serif; color:var(--sb-ink); }
        .sb-master-tools[x-cloak], .sb-master-tools [x-cloak] { display:none!important; }
        .sb-tools-backdrop { position:fixed; inset:0; z-index:10000; background:rgba(20,15,12,.22); backdrop-filter:blur(2px); }
        .sb-tools-drawer { position:fixed; z-index:10001; top:16px; bottom:16px; right:clamp(8px, 18vw, 316px); width:min(392px,calc(100vw - 20px)); border:1px solid var(--sb-border); border-radius:15px; box-shadow:0 25px 85px rgba(27,12,9,.23); background:var(--sb-surface); display:flex; flex-direction:column; overflow:hidden; }
        .sb-tools-heading { display:flex; align-items:center; justify-content:space-between; gap:10px; background:color-mix(in srgb,var(--sb-accent) 8%,var(--sb-surface)); border-bottom:1px solid var(--sb-border); padding:16px 18px; }
        .sb-tools-heading strong { font:700 17px Georgia,serif; color:var(--sb-accent); }
        .sb-tools-heading p { font-size:11px; opacity:.72; margin-top:3px; }
        .sb-tools-close { display:grid; place-items:center; flex-shrink:0; width:30px; height:30px; border:1px solid var(--sb-border); border-radius:9px; background:var(--sb-surface); color:var(--sb-accent); font-weight:800; cursor:pointer; }
        .sb-tools-content { overflow-y:auto; min-height:0; padding:16px; }
        .sb-tools-action { display:flex; width:100%; text-align:left; align-items:center; gap:12px; padding:13px 12px; background:var(--sb-surface); border:1px solid var(--sb-border); border-radius:11px; margin-bottom:9px; cursor:pointer; transition:border-color .16s,transform .16s,background .16s; }
        .sb-tools-action:hover { border-color:var(--sb-accent); background:color-mix(in srgb,var(--sb-accent) 5%,var(--sb-surface)); transform:translateY(-1px); }
        .sb-tools-action-icon { width:39px;height:39px;border:1px solid var(--sb-border);border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--sb-accent) 10%,var(--sb-surface));color:var(--sb-accent);font-size:22px;flex-shrink:0; }
        .sb-tools-action strong { display:block;font:700 14px Georgia,serif;color:var(--sb-accent); }
        .sb-tools-action small { display:block;font-size:11px;line-height:1.45;opacity:.7;margin-top:3px; }
        .sb-tools-input { display:block; width:100%; min-width:0; padding:10px 12px; border:1px solid var(--sb-border);border-radius:9px;background:var(--sb-surface);color:var(--sb-ink);outline:none;font-size:14px; }
        .sb-tools-input:focus { border-color:var(--sb-accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--sb-accent) 15%,transparent); }
        .sb-tools-result { width:100%; display:block; padding:11px 12px;text-align:left;border-bottom:1px solid var(--sb-border);color:var(--sb-ink); background:transparent;cursor:pointer; }
        .sb-tools-result:hover { background:color-mix(in srgb,var(--sb-accent) 7%,var(--sb-surface)); }
        .sb-tools-result strong { display:block;font-size:13px;color:var(--sb-accent); }
        .sb-tools-result small { font-size:10px;opacity:.75; }
        .sb-tools-spell-detail { margin-top:12px;border:1px solid var(--sb-border);border-radius:11px;padding:13px;background:color-mix(in srgb,var(--sb-accent) 3%,var(--sb-surface)); }
        .sb-tools-spell-detail h4 {font:700 17px Georgia,serif;color:var(--sb-accent);margin-bottom:9px}
        .sb-tools-spell-meta {display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:10px;font-size:11px}
        .sb-tools-spell-meta span {background:var(--sb-surface);border:1px solid var(--sb-border);border-radius:6px;padding:6px;overflow-wrap:anywhere}
        .sb-tools-spell-description {font-size:12px;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere;}
        .sb-floating-calc {position:fixed;z-index:10002;width:min(318px,calc(100vw - 20px));border:1px solid var(--sb-border);border-radius:14px;background:var(--sb-surface);box-shadow:0 18px 56px rgba(22,12,6,.3);overflow:hidden;user-select:none;}
        .sb-floating-head {padding:12px 13px;display:flex;align-items:center;justify-content:space-between;gap:8px;cursor:grab;background:color-mix(in srgb,var(--sb-accent) 10%,var(--sb-surface));border-bottom:1px solid var(--sb-border);touch-action:none;}
        .sb-floating-head:active{cursor:grabbing}
        .sb-floating-head strong{font:700 14px Georgia,serif;color:var(--sb-accent)}
        .sb-floating-body{padding:12px;max-height:calc(100vh - 120px);overflow:auto;}
        .sb-floating-calc-input {font:600 20px ui-monospace,SFMono-Regular,Consolas,monospace;text-align:right;font-variant-numeric:tabular-nums;}
        .sb-floating-calc-ops {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:6px;margin:9px 0;}
        .sb-floating-calc-dice {display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:4px;margin-bottom:9px;}
        .sb-floating-calc-numbers {display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:6px;}
        .sb-floating-key {min-width:0;min-height:40px;border:1px solid var(--sb-border);border-radius:8px;background:color-mix(in srgb,var(--sb-accent) 4%,var(--sb-surface));color:var(--sb-ink);font:700 13px system-ui,sans-serif;cursor:pointer;}
        .sb-floating-key:hover {border-color:var(--sb-accent);background:color-mix(in srgb,var(--sb-accent) 10%,var(--sb-surface));}
        .sb-floating-key--op {font-size:19px;color:var(--sb-accent);}
        .sb-floating-key--primary {background:var(--sb-accent);color:#fffaf6;font-size:16px;}
        .sb-floating-key--primary:hover{background:color-mix(in srgb,var(--sb-accent) 84%,black)}
        .sb-floating-key:disabled{opacity:.5;cursor:wait}
        .sb-floating-total{padding:10px 12px;border-radius:9px;margin-top:9px;text-align:right;background:color-mix(in srgb,var(--sb-accent) 8%,var(--sb-surface));color:var(--sb-accent);font:800 26px Georgia,serif}
        .sb-floating-roll-line {margin-top:5px;font-size:11px;line-height:1.4;overflow-wrap:anywhere;}
        .sb-tools-error {color:#a22222;border:1px solid #d89c9c;background:#fff0ed;border-radius:7px;padding:9px;font-size:11px;margin-top:9px;}
        .sb-master-tools button:focus-visible{outline:2px solid var(--sb-accent);outline-offset:2px}
        @media(max-width:760px){.sb-tools-drawer{right:8px;top:8px;bottom:8px;}.sb-floating-calc{max-width:calc(100vw - 20px)}.sb-tools-spell-meta{grid-template-columns:1fr}}

        #combat-initiative-panel-host .sb-initiative-conditions {
            padding: 12px; border-top: 1px solid #dbc9b3;
            background: linear-gradient(165deg, #fffcf6 0%, #f5ede2 100%);
            color: #552f23;
        }
        #combat-initiative-panel-host .sb-initiative-conditions__header { display:flex; justify-content:space-between; gap:8px; align-items:center; padding-bottom:10px; }
        #combat-initiative-panel-host .sb-initiative-conditions__title { font:700 15px Georgia,serif; color:#6b1d14; line-height:1.3; }
        #combat-initiative-panel-host .sb-initiative-conditions__close { width:26px; height:26px; border:1px solid #ddcbb7; border-radius:7px; background:#fffaf4; font-size:19px; color:#926c50; line-height:1; }
        #combat-initiative-panel-host .sb-initiative-conditions__close:hover { background:#f3e3d9; color:#6b1d14; }
        #combat-initiative-panel-host .sb-initiative-conditions__section-heading { display:block; margin:9px 0 7px; font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; color:#8d664a; }
        #combat-initiative-panel-host .sb-initiative-conditions__active { border-radius:9px; padding:9px; border:1px solid #dcc6b5; background:#f8f0e8; }
        #combat-initiative-panel-host .sb-initiative-conditions__active .sb-initiative-conditions__section-heading { margin-top:0; }
        #combat-initiative-panel-host .sb-initiative-conditions__active-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; }
        #combat-initiative-panel-host .sb-initiative-conditions__choices { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; }
        #combat-initiative-panel-host .sb-initiative-condition { display:flex; min-width:0; text-align:left; gap:6px; align-items:center; min-height:33px; padding:6px 7px; border:1px solid #dccbb8; border-radius:8px; background:#fffdf9; font-size:10px; line-height:1.25; font-weight:700; color:#6c4837; transition:border-color .17s ease,background .17s ease,box-shadow .17s ease,transform .17s ease; }
        #combat-initiative-panel-host .sb-initiative-condition:hover:not(:disabled) { background:#f3e6d7; border-color:#ae8969; box-shadow:0 2px 7px #593c2415; transform:translateY(-1px); }
        #combat-initiative-panel-host .sb-initiative-condition:disabled { opacity:.55; cursor:wait; }
        #combat-initiative-panel-host .sb-initiative-condition:focus-visible { outline:2px solid #865435; outline-offset:2px; }
        #combat-initiative-panel-host .sb-initiative-condition__mark { display:grid; place-items:center; width:17px; height:17px; flex-shrink:0; border-radius:5px; background:#ede0d1; color:#795438; font-size:12px; }
        #combat-initiative-panel-host .sb-initiative-condition--active { background:#f5e3dd; border-color:#d7a997; color:#7e3024; }
        #combat-initiative-panel-host .sb-initiative-condition--active .sb-initiative-condition__mark { background:#ead0c5; color:#882e23; }
        #combat-initiative-panel-host .sb-initiative-condition__remove { font-size:15px; font-weight:800; color:#a34e42; }
        #combat-initiative-panel-host .sb-initiative-conditions__hint { display:block; color:#97755f; font-size:9px; padding:5px 0 0; line-height:1.35; }
        #combat-initiative-panel-host .sb-initiative-conditions__search { margin-top:2px; display:flex; align-items:center; gap:7px; background:#fffdf9; border:1px solid #dac7b4; border-radius:8px; padding:0 9px; color:#916948; }
        #combat-initiative-panel-host .sb-initiative-conditions__search input { width:100%; min-width:0; height:33px; outline:none; background:transparent; font-size:11px; color:#543124; }
        #combat-initiative-panel-host .sb-initiative-conditions__search:focus-within { border-color:#ae8969; box-shadow:0 0 0 2px #d2b69944; }
        #combat-initiative-panel-host .sb-initiative-conditions__error { margin-top:8px; padding:7px; border:1px solid #d8a19e; border-radius:7px; color:#902c27; background:#fff1ed; font-size:10px; }
        @media (max-width: 330px) { #combat-initiative-panel-host .sb-initiative-conditions__active-list, #combat-initiative-panel-host .sb-initiative-conditions__choices { grid-template-columns:1fr; } }
        /* V66.0.38: refinamento sem alterar as dimensões gerais da lateral. */
        #combat-initiative-panel-host { min-width:0; box-sizing:border-box; }
        #combat-initiative-panel-host .sb-tool-button {
            width:28px; height:28px; flex:0 0 28px;
            display:inline-grid; place-items:center; border:1px solid #d8cebe; border-radius:7px;
            background:#fffbf6; color:#6b1d14; box-shadow:0 1px 3px #381b120a;
            transition:background .16s ease,border-color .16s ease,transform .16s ease,box-shadow .16s ease;
        }
        #combat-initiative-panel-host .sb-tool-button:hover { background:#fff4e7; border-color:#af9274; transform:translateY(-1px); }
        #combat-initiative-panel-host .sb-tool-button:focus-visible,
        #combat-initiative-panel-host .sb-calc-key:focus-visible { outline:2px solid #92623b; outline-offset:2px; }
        #combat-initiative-panel-host .sb-tool-button[aria-pressed='true'] { border-color:#ad875e; background:#f2e5d4; }
        #combat-initiative-panel-host .sb-tool-panel { min-width:0; margin-top:8px; padding-top:10px; border-top:1px solid #d8cebe9c; }
        #combat-initiative-panel-host .sb-tool-tabs { display:flex; gap:5px; margin-bottom:7px; }
        #combat-initiative-panel-host .sb-tool-tab { padding:4px 10px; border:1px solid #d6c7b4; border-radius:6px; background:#fcf8f2; color:#79573f; font-size:10px!important; font-weight:700; }
        #combat-initiative-panel-host .sb-tool-tab[aria-pressed='true'] { background:#6b1d14; color:#fffaf2; border-color:#6b1d14; }
        #combat-initiative-panel-host .sb-tool-input { min-width:0; width:100%; border-radius:7px; border:1px solid #cdbb9f; background:#fffcf8; color:#492920; padding:7px 9px; font:600 12px ui-monospace,monospace; outline:none; }
        #combat-initiative-panel-host .sb-tool-input:focus { border-color:#a47d58; box-shadow:0 0 0 2px #a47d5829; }
        #combat-initiative-panel-host .sb-calculator { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:5px; margin-top:7px; }
        #combat-initiative-panel-host .sb-calc-key { display:grid; place-items:center; min-width:0; min-height:31px; padding:4px 2px; border:1px solid #dbc9b5; border-radius:7px; background:#fffaf3; color:#6b2b1f; font-size:12px!important; font-weight:700; transition:background .14s ease,border-color .14s ease; }
        #combat-initiative-panel-host .sb-calc-key:hover { background:#f3e6d7; border-color:#b89776; }
        #combat-initiative-panel-host .sb-calc-key--accent { background:#6b1d14; color:#fff; border-color:#6b1d14; }
        #combat-initiative-panel-host .sb-calc-key--accent:hover { background:#812b22; }
        #combat-initiative-panel-host .sb-tool-result { margin-top:7px; padding:7px 9px; background:#fffaf4; border:1px solid #d9cab8; border-radius:7px; color:#6b1d14; font:700 13px ui-monospace,monospace; overflow-wrap:anywhere; }
        #combat-initiative-panel-host .sb-spell-result { display:block; width:100%; min-width:0; padding:7px 9px; border-bottom:1px solid #e9dac9; text-align:left; font:600 11px system-ui,sans-serif; color:#5e3025; overflow-wrap:anywhere; }
        #combat-initiative-panel-host .sb-spell-result:hover { background:#f5e9db; }
        #combat-npc-panel-host .sb-initiative-spell-target { outline:2px solid #c3883f; background:#f1cc8744; outline-offset:3px; border-radius:3px; }
        #combat-initiative-panel-host .sb-initiative-row { min-width:0; max-width:100%; box-sizing:border-box; }
        #combat-npc-panel-host { min-width:0; max-width:100%; }
        #combat-npc-panel-host .ability-groups, #combat-npc-panel-host .ability-group,
        #combat-npc-panel-host .ability-row { min-width:0; max-width:100%; }
        #combat-npc-panel-host .ability-row { overflow-wrap:anywhere; }
        #combat-npc-panel-host img { max-width:100%; }
        #combat-npc-panel-host table { max-width:100%; }
        #combat-npc-panel-host a:focus-visible { outline:2px solid #ad804f; outline-offset:2px; }

        #combat-initiative-panel-host .sb-initiative-row .sb-initiative-name { display:block; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        #combat-initiative-panel-host .sb-initiative-row:focus-within { border-color:#b78d68; box-shadow:0 0 0 1px #b78d684a; }
        #combat-initiative-panel-host .sb-combat-timestamp { margin-top:5px; font-size:9px; color:#865e47; line-height:1.4; }
        #combat-initiative-panel-host .sb-combat-timestamp span { display:block; }
        @media (max-width:360px) {
            #combat-initiative-panel-host .sb-calc-key { min-height:29px; }
            #combat-initiative-panel-host .sb-initiative-condition { padding:5px; }
            #combat-initiative-panel-host .sb-initiative-conditions__choices { grid-template-columns:1fr; }
        }
    
        /* V66.0.42 — Acesso recolhível a feitiços e calculadora. */
        #combat-initiative-panel-host .sb-initiative-header {
            isolation: isolate;
            border-bottom-color: var(--theme-border, #d8cebe);
        }
        #combat-initiative-panel-host .sb-quick-tools-tab {
            width: 29px; height: 29px; flex: 0 0 29px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--theme-border, #d1bca6);
            border-radius: 9px;
            background: var(--theme-surface-card, #fffcf7);
            color: var(--theme-accent-strong, #6b1d14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.55), 0 2px 6px rgba(83,38,19,.055);
            cursor: pointer; transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
        }
        #combat-initiative-panel-host .sb-quick-tools-tab:hover,
        #combat-initiative-panel-host .sb-quick-tools-tab[aria-expanded='true'] {
            background: var(--theme-card-hover, #fff2e7);
            border-color: var(--theme-accent, #b58a6f);
            box-shadow: 0 3px 10px rgba(92,45,23,.12);
        }
        #combat-initiative-panel-host .sb-quick-tools-tab:focus-visible,
        #combat-initiative-panel-host .sb-quick-tool:focus-visible {
            outline: 2px solid var(--theme-accent-strong, #6b1d14);
            outline-offset: 2px;
        }
        #combat-initiative-panel-host .sb-quick-tools-chevron {
            width: 17px; height: 17px;
            transition: transform .22s cubic-bezier(.2,.8,.2,1);
        }
        #combat-initiative-panel-host .sb-quick-tools-tab[aria-expanded='true'] .sb-quick-tools-chevron {
            transform: rotate(-90deg);
        }
        #combat-initiative-panel-host .sb-quick-tools-pocket {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 7px;
            margin-top: 11px;
            padding: 8px;
            border-radius: 11px;
            border: 1px solid var(--theme-border, #d8c8b4);
            background: var(--theme-surface-card, #fffbf6);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.52), 0 5px 14px rgba(65,29,15,.06);
        }
        #combat-initiative-panel-host .sb-quick-tool {
            display: flex; align-items: center; justify-content: flex-start; gap: 7px;
            min-width: 0; min-height: 38px;
            padding: 6px 7px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--theme-text-primary, #5e3025);
            transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
            cursor: pointer;
        }
        #combat-initiative-panel-host .sb-quick-tool:hover {
            background: var(--theme-card-hover, #f4e9dc);
            border-color: var(--theme-border, #d8c2a9);
            transform: translateY(-1px);
        }
        #combat-initiative-panel-host .sb-quick-tool__icon {
            display: grid; place-items: center; flex: 0 0 28px; height: 28px;
            color: var(--theme-accent-strong, #7b3a2b);
            background: var(--theme-surface-inset, #f3eadf);
            border: 1px solid var(--theme-border-soft, #ead9c5);
            border-radius: 8px;
        }
        #combat-initiative-panel-host .sb-quick-tool__label {
            display: block; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
            font: 700 11px/1.2 Georgia, 'Times New Roman', serif;
        }
        #combat-initiative-panel-host .sb-initiative-empty {
            margin-top: 16px;
            padding: 25px 16px 23px;
            text-align: center;
            border: 1px dashed var(--theme-border, #d6c6b1);
            border-radius: 14px;
            background: linear-gradient(150deg,
                var(--theme-surface-card, #fffcf7),
                var(--theme-surface-inset, #f2eadf));
        }
        #combat-initiative-panel-host .sb-initiative-empty__seal {
            display: grid; place-items: center;
            margin: 0 auto 12px;
            width: 45px; height: 45px;
            border-radius: 13px;
            color: var(--theme-accent-strong, #986c50);
            background: var(--theme-surface-card, #fffbf6);
            border: 1px solid var(--theme-border, #d8c8b4);
            box-shadow: 0 4px 14px rgba(60,35,22,.055);
        }
        #combat-initiative-panel-host .sb-initiative-empty__title {
            font: 700 14px/1.3 Georgia, 'Times New Roman', serif;
            color: var(--theme-text-strong, #703629);
        }
        #combat-initiative-panel-host .sb-initiative-empty__body {
            max-width: 205px;
            margin: 6px auto 0;
            font: 500 10px/1.6 system-ui, sans-serif;
            color: var(--theme-text-secondary, #927560);
        }
        #combat-initiative-panel-host .sb-initiative-empty__rule {
            width: 42px; height: 1px; margin: 14px auto 0;
            background: linear-gradient(90deg, transparent, var(--theme-border-strong, #bd9d7b), transparent);
        }
        @media (max-width: 350px) {
            #combat-initiative-panel-host .sb-quick-tools-pocket { padding: 5px; gap: 3px; }
            #combat-initiative-panel-host .sb-quick-tool { padding: 5px 4px; gap: 5px; }
            #combat-initiative-panel-host .sb-quick-tool__icon { flex-basis: 26px; height: 26px; }
            #combat-initiative-panel-host .sb-quick-tool__label { font-size: 10px; }
        }
        @media (prefers-reduced-motion: reduce) {
            #combat-initiative-panel-host .sb-quick-tools-chevron,
            #combat-initiative-panel-host .sb-quick-tool { transition: none; transform: none !important; }
        }

        /* V66.0.40: classificação de NPC — não altera a altura das fichas. */
        #combat-initiative-panel-host .sb-npc-side-indicator {
            display:inline-flex; align-items:center; justify-content:center;
            max-width:55px; padding:2px 4px; border:1px solid transparent;
            border-radius:5px; font:700 8px/1.1 system-ui,sans-serif;
            cursor:default;
            white-space:nowrap;
        }
        #combat-initiative-panel-host .sb-npc-side-indicator--enemy {
            color:#8e5147; background:#f8ece7; border-color:#e9cfc7;
        }
        #combat-initiative-panel-host .sb-npc-side-indicator--ally {
            color:#176c5e; background:#e7f4ec; border-color:#abd5c0;
        }
        #combat-initiative-panel-host .sb-npc-side-indicator:focus-visible {
            outline:2px solid #9a714e; outline-offset:1px;
        }

        /* ===========================================================
           V66.0.43 — hierarquia de comandos compacta, compatível com tema
           =========================================================== */
        #combat-initiative-panel-host .sb-initiative-header {
            overflow: visible; isolation: isolate;
            padding: 12px 12px 11px !important;
            background: var(--theme-paper, #eee8dc);
            border-bottom-color: var(--theme-border, #d8cebe);
            box-shadow: 0 4px 12px rgba(62,33,19,.035);
        }
        #combat-initiative-panel-host .sb-v43-identity {
            display:flex; align-items:center; justify-content:space-between; gap:8px;
            min-width:0; margin-bottom:11px;
        }
        #combat-initiative-panel-host .sb-v43-identity__text { flex:1; min-width:0; }
        #combat-initiative-panel-host .sb-v43-name {
            margin:0; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;
            font:700 15px/1.28 Georgia,'Times New Roman',serif;
            color:var(--theme-text-strong,#6b1d14);
        }
        #combat-initiative-panel-host .sb-v43-eyebrow {
            display:flex; align-items:center; gap:5px; margin:3px 0 0;
            font:800 8px/1.2 system-ui,sans-serif; letter-spacing:.17em;
            color:var(--theme-label,#9a7250);
        }
        #combat-initiative-panel-host .sb-v43-history { font-size:13px;line-height:.8;letter-spacing:0;cursor:help; }
        #combat-initiative-panel-host .sb-v43-status {
            flex-shrink:0; display:inline-flex; align-items:center; min-height:23px;
            padding:0 8px; border-radius:6px; border:1px solid;
            font:800 9px/1.2 system-ui,sans-serif; text-transform:uppercase;letter-spacing:.045em;
        }
        #combat-initiative-panel-host .sb-v43-status--preparing { color:#90651e;background:#fff4d8;border-color:#e9cc83; }
        #combat-initiative-panel-host .sb-v43-status--closed { color:#136a51;background:#eaf7ef;border-color:#a7d8bb; }
        #combat-initiative-panel-host .sb-v43-round {
            flex-shrink:0; display:flex; align-items:center; gap:6px;
            background:var(--theme-surface-card,#fffcf8);border:1px solid var(--theme-border,#d6c6b4);
            border-radius:7px;padding:3px 4px 3px 7px;
            font:800 9px/1.2 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.04em;
            color:var(--theme-label,#8c6239);
        }
        #combat-initiative-panel-host .sb-v43-round strong { color:#fff;background:var(--theme-accent-strong,#6b1d14);padding:4px 6px;border-radius:4px;font-size:11px; }
        #combat-initiative-panel-host .sb-v43-actions { display:flex;align-items:stretch;gap:6px;min-width:0; }
        #combat-initiative-panel-host .sb-v43-menu-anchor { position:relative; min-width:0;flex:1 1 0; }
        #combat-initiative-panel-host .sb-v43-menu-anchor--tools,
        #combat-initiative-panel-host .sb-v43-menu-anchor--more { flex:0 0 33px; }
        #combat-initiative-panel-host .sb-v43-action-form { flex:1 1 0;min-width:0; }
        #combat-initiative-panel-host .sb-v43-action-form--wide { flex:1 1 auto; }
        #combat-initiative-panel-host .sb-v43-add-trigger,
        #combat-initiative-panel-host .sb-v43-primary,
        #combat-initiative-panel-host .sb-v43-icon-button {
            display:flex;align-items:center;justify-content:center;gap:5px;
            width:100%;height:34px;min-width:0;padding:0 8px;
            border:1px solid var(--theme-border,#d5c1aa);
            border-radius:8px; cursor:pointer;white-space:nowrap;
            font:800 10px/1 system-ui,sans-serif;letter-spacing:.01em;
            transition:background-color .16s ease,border-color .16s ease,box-shadow .16s ease,transform .16s ease;
        }
        #combat-initiative-panel-host .sb-v43-add-trigger {
            background:var(--theme-surface-card,#fffcf8);
            color:var(--theme-text-strong,#6b1d14);
            box-shadow:inset 0 1px 0 rgba(255,255,255,.38);
        }
        #combat-initiative-panel-host .sb-v43-add-trigger:hover,
        #combat-initiative-panel-host .sb-v43-icon-button:hover {
            background:var(--theme-card-hover,#f7ecdc);
            border-color:var(--theme-border-strong,#b99e83);
        }
        #combat-initiative-panel-host .sb-v43-primary {
            border-color:var(--theme-accent-strong,#6b1d14);
            background:var(--theme-button-background,#6b1d14);
            color:var(--theme-button-text,#fffaf2);
            box-shadow:0 2px 5px rgba(71,27,18,.12);
        }
        #combat-initiative-panel-host .sb-v43-primary:hover { background:var(--theme-button-hover-background,#83291c);transform:translateY(-1px); }
        #combat-initiative-panel-host .sb-v43-icon-button {
            padding:0;width:33px;min-width:33px;
            background:var(--theme-surface-card,#fffcf8);
            color:var(--theme-accent-strong,#6b1d14);
        }
        #combat-initiative-panel-host .sb-v43-icon-button--tools {
            background:var(--theme-surface-inset,#f0e7d9);
        }
        #combat-initiative-panel-host .sb-v43-actions button:focus-visible {
            outline:2px solid var(--theme-accent-strong,#6b1d14);outline-offset:2px;
        }
        #combat-initiative-panel-host .sb-v43-actions svg {
            width:15px;height:15px;flex-shrink:0;fill:none;
            stroke:currentColor;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round;
        }
        #combat-initiative-panel-host .sb-v43-actions .sb-v43-down { width:11px;height:11px;opacity:.64; }
        #combat-initiative-panel-host .sb-v43-popover {
            position:absolute;top:calc(100% + 8px);left:0;z-index:70;
            width:226px;max-width:calc(100vw - 22px);
            padding:7px;border:1px solid var(--theme-border,#d8c5ae);
            border-radius:12px;background:var(--theme-surface-raised,var(--theme-surface-card,#fffaf3));
            color:var(--theme-text-primary,#654334);
            box-shadow:0 14px 35px rgba(49,26,16,.18),0 2px 7px rgba(49,26,16,.05);
        }
        #combat-initiative-panel-host .sb-v43-popover--tools,
        #combat-initiative-panel-host .sb-v43-popover--more { left:auto;right:0; }
        #combat-initiative-panel-host .sb-v43-popover__heading {
            padding:5px 9px 9px;margin:0 0 3px;
            font:800 9px/1 system-ui,sans-serif;letter-spacing:.11em;text-transform:uppercase;
            color:var(--theme-label,#966d4e);border-bottom:1px solid var(--theme-divider,#e5d9ca);
        }
        #combat-initiative-panel-host .sb-v43-popover__item {
            width:100%;display:flex;align-items:center;gap:9px;
            min-height:48px;padding:7px 8px;border-radius:8px;
            background:transparent;border:1px solid transparent;text-align:left;
            color:var(--theme-text-primary,#604337);cursor:pointer;
        }
        #combat-initiative-panel-host .sb-v43-popover__item:hover {
            background:var(--theme-card-hover,#f3e8d9);
            border-color:var(--theme-border-soft,#e7d8c6);
        }
        #combat-initiative-panel-host .sb-v43-popover__item strong {
            display:block;font:700 12px/1.15 Georgia,'Times New Roman',serif;
            color:var(--theme-text-strong,#672b20);
        }
        #combat-initiative-panel-host .sb-v43-popover__item small {
            display:block;margin-top:3px;font:500 10px/1.2 system-ui,sans-serif;
            color:var(--theme-text-secondary,#92765e);
        }
        #combat-initiative-panel-host .sb-v43-item-icon {
            flex:0 0 30px;height:30px;display:grid;place-items:center;
            background:var(--theme-surface-inset,#f3e7d8);
            color:var(--theme-accent-strong,#7c4332);
            border:1px solid var(--theme-border-soft,#e5d1bc);border-radius:8px;
        }
        #combat-initiative-panel-host .sb-v43-actions .sb-v43-item-icon svg {width:17px;height:17px;}
        #combat-initiative-panel-host .sb-v43-popover__item--danger strong { color:#9c3828; }
        @media(max-width:350px) {
            #combat-initiative-panel-host .sb-initiative-header {padding-inline:9px!important;}
            #combat-initiative-panel-host .sb-v43-actions {gap:4px;}
            #combat-initiative-panel-host .sb-v43-add-trigger,
            #combat-initiative-panel-host .sb-v43-primary {padding:0 5px;font-size:9px;}
            #combat-initiative-panel-host .sb-v43-name {font-size:14px;}
        }
        @media(prefers-reduced-motion:reduce){
            #combat-initiative-panel-host .sb-v43-actions button {transition:none!important;transform:none!important;}
        }

        /* V66.0.45 — Dock no rodapé + cards refinados. Compatível com os temas. */
        #combat-initiative-panel-host {
            position: relative;
            z-index: 40;
            min-height: 0;
            overflow: visible;
        }
        #combat-initiative-panel-host .sb-v43-identity__text {
            display:flex;
            align-items:center;
            gap:7px;
        }
        #combat-initiative-panel-host .sb-v43-history {
            flex-shrink:0;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:19px;
            height:19px;
            font-size:13px;
            border:1px solid var(--theme-border, #d8cebe);
            border-radius:50%;
            background:var(--theme-surface-card, #fffaf4);
            color:var(--theme-label, #986b4c);
        }
        #combat-initiative-panel-host .sb-v43-actions:has(.sb-v43-action-form--wide) .sb-v43-menu-anchor:not(.sb-v43-menu-anchor--more) {
            flex:0 0 97px;
        }
        #combat-initiative-panel-host .sb-v43-actions:has(.sb-v43-action-form--wide) .sb-v43-primary {
            padding-inline:5px;
        }

        /* Apenas a lista rola: barra de ferramentas permanece no rodapé. */
        #combat-initiative-panel-host .sb-v45-initiative-scroll {
            min-height:0;
            flex:1 1 0;
            overscroll-behavior:contain;
        }
        #combat-initiative-panel-host .sb-v45-dock {
            flex:0 0 auto;
            position:relative;
            z-index:34;
            padding:10px 11px 11px;
            border-top:1px solid var(--theme-border,#d8cebe);
            background:linear-gradient(180deg,
                var(--theme-paper,#f9f6f0) 0%,
                var(--theme-surface-inset,#f4eee5) 100%);
            box-shadow:0 -7px 18px rgba(50,30,18,.04);
        }
        #combat-initiative-panel-host .sb-v45-dock::before {
            content:'';
            display:block;
            width:35px;
            height:1px;
            margin:-10px auto 9px;
            background:var(--theme-accent-strong,#8c6239);
            opacity:.44;
        }
        #combat-initiative-panel-host .sb-v45-dock-grid {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:8px;
        }
        #combat-initiative-panel-host .sb-v45-dock-button {
            display:flex;
            align-items:center;
            justify-content:center;
            gap:9px;
            min-width:0;
            min-height:44px;
            padding:7px 10px;
            border:1px solid var(--theme-border,#d9c6af);
            border-radius:10px;
            background:var(--theme-surface-card,#fffdf8);
            color:var(--theme-text-strong,#6b1d14);
            font:700 12px/1 Georgia,'Times New Roman',serif;
            box-shadow:0 2px 6px rgba(70,37,20,.035),inset 0 1px 0 rgba(255,255,255,.22);
            cursor:pointer;
            transition:background .16s,border-color .16s,box-shadow .16s,transform .16s;
        }
        #combat-initiative-panel-host .sb-v45-dock-button:hover {
            background:var(--theme-card-hover,#f6ebdf);
            border-color:var(--theme-border-strong,#b48f6f);
            box-shadow:0 5px 11px rgba(70,37,20,.085);
            transform:translateY(-1px);
        }
        #combat-initiative-panel-host .sb-v45-dock-button:active {transform:translateY(0)}
        #combat-initiative-panel-host .sb-v45-dock-button:focus-visible {
            outline:2px solid var(--theme-accent-strong,#6b1d14);
            outline-offset:2px;
        }
        #combat-initiative-panel-host .sb-v45-dock-icon {
            display:grid;
            place-items:center;
            width:27px;
            height:27px;
            flex:0 0 27px;
            border:1px solid var(--theme-border-soft,#e7d7c5);
            border-radius:8px;
            background:var(--theme-surface-inset,#f1e6d9);
            color:var(--theme-accent-strong,#8c6239);
        }
        #combat-initiative-panel-host .sb-v45-dock-icon svg {
            display:block;
            width:17px;
            height:17px;
            stroke:currentColor;
            fill:none;
            stroke-width:1.7;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        /* Os cards ficam mais legíveis nos 300px disponíveis, sem esconder ações. */
        #combat-initiative-panel-host .sb-v45-card {
            position:relative;
            isolation:isolate;
            border:1px solid var(--theme-border,#d8cebe)!important;
            border-radius:12px!important;
            background:var(--theme-surface-card,#fffdf8)!important;
            box-shadow:0 2px 6px rgba(52,32,21,.045);
            transform:none!important;
            transition:border-color .18s ease,background .18s ease,box-shadow .18s ease;
        }
        #combat-initiative-panel-host .sb-v45-card::before {
            content:'';
            position:absolute;
            left:0;
            top:12px;
            bottom:12px;
            width:3px;
            border-radius:0 6px 6px 0;
            background:var(--theme-accent-strong,#a1815b);
            opacity:.32;
            pointer-events:none;
        }
        #combat-initiative-panel-host .sb-v45-card--ally::before {background:#278876;opacity:.75;}
        #combat-initiative-panel-host .sb-v45-card--enemy::before {background:#ad6b55;opacity:.66;}
        #combat-initiative-panel-host .sb-v45-card--active {
            border-color:var(--theme-accent-strong,#9b7950)!important;
            background:linear-gradient(110deg,
                color-mix(in srgb,var(--theme-accent,#a1815b) 7%,var(--theme-surface-card,#fffdf8)),
                var(--theme-surface-card,#fffdf8) 85%)!important;
            box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--theme-accent,#8c6239) 16%,transparent),
                0 5px 12px rgba(52,32,21,.09);
        }
        #combat-initiative-panel-host .sb-v45-card--active::before {opacity:1;width:4px;top:0;bottom:0;border-radius:0;}
        #combat-initiative-panel-host .sb-v45-card:not(.sb-v45-card--active):hover {
            border-color:var(--theme-border-strong,#c5a98a)!important;
            box-shadow:0 5px 14px rgba(52,32,21,.085);
        }
        #combat-initiative-panel-host .sb-v45-card-top {
            position:relative;
            min-width:0;
            padding:10px 10px 10px 12px;
        }
        #combat-initiative-panel-host .sb-v45-card-main {
            width:100%;
            min-width:0;
            align-items:flex-start;
            gap:9px;
        }
        #combat-initiative-panel-host .sb-v45-order {
            width:22px;
            height:23px;
            margin-top:1px;
            border-radius:7px;
            font:800 11px Georgia,'Times New Roman',serif;
            background:var(--theme-surface-inset,#eee8dc);
            color:var(--theme-label,#8c6239);
            border:1px solid var(--theme-border-soft,#e9dfcf);
            box-shadow:none;
        }
        #combat-initiative-panel-host .sb-v45-card--active .sb-v45-order {
            color:var(--theme-button-text,#fffaf2);
            background:var(--theme-accent-strong,#6b1d14);
            border-color:var(--theme-accent-strong,#6b1d14);
        }
        #combat-initiative-panel-host .sb-v45-card-content {min-width:0; flex:1;}
        #combat-initiative-panel-host .sb-v45-card-title {
            min-width:0;
            min-height:24px;
            padding-right:70px;
        }
        #combat-initiative-panel-host .sb-v45-card .sb-initiative-name {
            display:-webkit-box!important;
            -webkit-box-orient:vertical;
            -webkit-line-clamp:2;
            white-space:normal!important;
            overflow:hidden;
            text-overflow:ellipsis;
            font:700 13px/1.2 Georgia,'Times New Roman',serif;
            color:var(--theme-text-strong,#6b1d14)!important;
            overflow-wrap:anywhere;
        }
        #combat-initiative-panel-host .sb-v45-card .sb-v45-meta {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:4px 5px;
            margin-top:6px;
            min-width:0;
        }
        #combat-initiative-panel-host .sb-v45-card .sb-v45-health {
            flex:1 1 112px;
            width:auto;
            min-width:92px;
            max-width:none!important;
            margin-left:2px;
            gap:5px;
        }
        #combat-initiative-panel-host .sb-v45-card .sb-v45-health > div:first-child {
            min-width:18px;
            height:6px;
            background:var(--theme-surface-inset,#e4dfd6);
        }
        #combat-initiative-panel-host .sb-v45-card .sb-v45-health > span {
            color:var(--theme-text-secondary,#756255);
            font-size:9px;
            font-variant-numeric:tabular-nums;
        }
        #combat-initiative-panel-host .sb-v45-controls {
            position:absolute;
            top:9px;
            right:8px;
            align-items:center;
            gap:3px;
        }
        #combat-initiative-panel-host .sb-v45-controls input[name='initiative'] {
            width:34px;
            height:26px;
            border:1px solid var(--theme-border,#cdbb9f);
            border-radius:7px;
            background:var(--theme-surface-raised,var(--theme-surface-card,#fffaf6));
            color:var(--theme-text-strong,#6b1d14);
            box-shadow:inset 0 1px 2px rgba(30,20,15,.035);
        }
        #combat-initiative-panel-host .sb-v45-remove {
            width:25px;
            height:26px;
            border-radius:7px;
            color:var(--theme-text-secondary,#a58e83);
        }
        #combat-initiative-panel-host .sb-v45-remove:hover {
            color:#a22e24;
            background:color-mix(in srgb,#e85a49 10%,var(--theme-surface-card,#fffaf3));
        }
        #combat-initiative-panel-host .sb-v45-card--dead .sb-initiative-name {
            color:var(--theme-text-secondary,#898079)!important;
        }
        #combat-initiative-panel-host .sb-v45-card .sb-npc-side-indicator {
            border-radius:5px;
            padding:2px 5px;
        }
        @media(max-width:350px){
            #combat-initiative-panel-host .sb-v45-card-top {padding-left:8px;padding-right:6px;}
            #combat-initiative-panel-host .sb-v45-card-title {padding-right:67px;}
            #combat-initiative-panel-host .sb-v45-dock {padding-inline:7px;}
            #combat-initiative-panel-host .sb-v45-dock-button {gap:6px;padding-inline:5px;}
            #combat-initiative-panel-host .sb-v43-actions:has(.sb-v43-action-form--wide) .sb-v43-menu-anchor:not(.sb-v43-menu-anchor--more) {flex-basis:83px;}
        }
        @media(prefers-reduced-motion:reduce){
            #combat-initiative-panel-host .sb-v45-card,
            #combat-initiative-panel-host .sb-v45-dock-button {transition:none!important; transform:none!important;}
        }
</style>


    {{-- ===============================================================
         V66.0.45: cabeçalho e ferramentas diretas, cards refinados
         Os formulários, as rotas e as ferramentas existentes são preservados.
    ================================================================ --}}
    <div class="sb-initiative-header sticky top-0 z-30 border-b border-[#d8cebe] bg-[#eee8dc]/95 backdrop-blur-md">

        <div class="sb-v43-identity">
            <div class="sb-v43-identity__text">
                <h1 class="sb-v43-name" title="{{ $combat->name }}">{{ $combat->name }}</h1>
                @if($latestCombatRun)
                    <span class="sb-v43-history" title="Início: {{ $latestCombatRun->started_at ? \Illuminate\Support\Carbon::parse($latestCombatRun->started_at)->format('d/m/Y H:i') : 'anterior à atualização (horário não disponível)' }}@if($latestCombatRun->ended_at) | Encerrado: {{ \Illuminate\Support\Carbon::parse($latestCombatRun->ended_at)->format('d/m/Y H:i') }} | Rodada final: {{ (int) $latestCombatRun->end_round }}@endif" aria-label="Histórico do encontro">◷</span>
                @endif
            </div>

            @if($combat->is_active)
                <div class="sb-v43-round" title="Rodada atual">
                    <span>Rodada</span><strong>{{ $combat->current_round }}</strong>
                </div>
            @elseif($latestCombatRun?->ended_at)
                <span class="sb-v43-status sb-v43-status--closed">Encerrado</span>
            @else
                <span class="sb-v43-status sb-v43-status--preparing">Preparação</span>
            @endif
        </div>

        <div class="sb-v43-actions">
            {{-- Mesmo com o encontro em andamento, jogadores e NPCs podem entrar depois. --}}
            {{-- A inclusão reutiliza os modais e endpoints existentes. --}}
                <div class="sb-v43-menu-anchor" x-on:keydown.escape="addMenuOpen = false" x-on:click.outside="addMenuOpen = false">
                    <button type="button" class="sb-v43-add-trigger"
                        x-on:click="addMenuOpen = !addMenuOpen; quickToolsExpanded = false"
                        x-bind:aria-expanded="String(addMenuOpen)" aria-controls="sb-v43-add-menu">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        <span>Adicionar</span>
                        <svg class="sb-v43-down" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="sb-v43-add-menu" class="sb-v43-popover sb-v43-popover--add"
                        x-show="addMenuOpen" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">
                        <p class="sb-v43-popover__heading">Adicionar participantes</p>
                        <button type="button" class="sb-v43-popover__item"
                            x-on:click="addMenuOpen = false; openPlayerModal = true">
                            <span class="sb-v43-item-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M19 8v8M15 12h8"/></svg></span>
                            <span><strong>Jogador</strong><small>Ficha de personagem</small></span>
                        </button>
                        <button type="button" class="sb-v43-popover__item"
                            x-on:click="addMenuOpen = false; openNpcModal = true">
                            <span class="sb-v43-item-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 6 4 3 4-3 4 3 4-3v11l-8 4-8-4Z"/><path d="m8 13 2 1m4 0 2-1"/></svg></span>
                            <span><strong>NPC</strong><small>Aliado ou inimigo</small></span>
                        </button>
                    </div>
                </div>

            @if(!$combat->is_active)
                <form action="{{ route('combats.start', $combat->id) }}" method="POST"
                    x-on:submit.prevent="handleInitiativeAction($event)" class="sb-v43-action-form">
                    @csrf
                    <button type="submit" class="sb-v43-primary">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 8 7-8 7"/></svg>
                        <span>{{ $latestCombatRun ? 'Reiniciar' : 'Iniciar' }}</span>
                    </button>
                </form>
            @else
                <form action="{{ route('combats.next', $combat->id) }}" method="POST"
                    x-on:submit.prevent="handleInitiativeAction($event)" class="sb-v43-action-form sb-v43-action-form--wide">
                    @csrf
                    <button type="submit" class="sb-v43-primary">
                        <span>Próximo turno</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg>
                    </button>
                </form>
                <div class="sb-v43-menu-anchor sb-v43-menu-anchor--more" x-data="{ moreActionsOpen: false }" x-on:keydown.escape="moreActionsOpen = false" x-on:click.outside="moreActionsOpen = false">
                    <button type="button" class="sb-v43-icon-button" aria-label="Mais ações do encontro"
                        aria-controls="sb-v43-more-actions" x-on:click="moreActionsOpen = !moreActionsOpen; quickToolsExpanded = false"
                        x-bind:aria-expanded="String(moreActionsOpen)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                    </button>
                    <div id="sb-v43-more-actions" class="sb-v43-popover sb-v43-popover--more"
                        x-show="moreActionsOpen" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">
                        <p class="sb-v43-popover__heading">Ações do encontro</p>
                        @php
                            $clockRun = \App\Models\CombatRun::query()->where('combat_id', $combat->id)
                                ->whereNull('ended_at')->latest('id')->first();
                            $clockNeedsStart = $clockRun && $clockRun->active_seconds === null;
                            $clockPaused = $clockRun && $clockRun->clock_paused;
                        @endphp
                        @if($clockRun)
                        <form action="{{ route('master.combat.archives.clock', $combat) }}" method="POST"
                            x-on:submit.prevent="handleInitiativeAction($event)">
                            @csrf
                            <input type="hidden" name="action" value="{{ $clockPaused || $clockNeedsStart ? 'resume' : 'pause' }}">
                            <button type="submit" class="sb-v43-popover__item">
                                <span class="sb-v43-item-icon" aria-hidden="true">◷</span>
                                <span>
                                    <strong>{{ $clockPaused || $clockNeedsStart ? 'Retomar cronômetro' : 'Pausar cronômetro' }}</strong>
                                    <small>{{ $clockNeedsStart ? 'Contar tempo ativo a partir de agora' : ($clockPaused ? 'Começar nova sessão' : 'Intervalos entre sessões não contam') }}</small>
                                </span>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('combats.reset', $combat->id) }}" method="POST"
                            x-on:submit.prevent="if (confirm('Tem certeza que deseja encerrar o combate?')) handleInitiativeAction($event)">
                            @csrf
                            <button type="submit" class="sb-v43-popover__item sb-v43-popover__item--danger">
                                <span class="sb-v43-item-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="5" width="14" height="14" rx="2"/><path d="M9 9h6v6H9z"/></svg></span>
                                <span><strong>Encerrar encontro</strong><small>Salvar a execução</small></span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- SEÇÃO 2: LISTA DE INICIATIVA --}}

    <div class="sb-v45-initiative-scroll flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar">

        @forelse($initiative as $participant)

            @php

                $isAnArray = is_array($participant);

                $isNpc = $isAnArray ? (($participant['type']??'') === 'npc') : $participant->isNpc();

                $name = $isAnArray ? $participant['name'] : $participant->name;

                $id = $isAnArray ? $participant['id'] : $participant->id;

                $initiativeVal = $isAnArray ? ($participant['initiative']??0) : $participant->initiative;

                $isCurrentTurn = $combat->is_active && ($loop->index === $combat->current_turn);

                $updateRoute = $isNpc ? route('combats.npcs.initiative', [$combat->id, $id]) : route('combats.players.initiative', [$combat->id, $id]);

                $destroyRoute = $isNpc ? route('combats.npcs.destroy', [$combat->id, $id]) : route('combats.players.destroy', [$combat->id, $id]);

                $isDeadNpc = false;

                $npcHPData = null;

                $playerHPData = null;

                $linkedCharacter = null;

                if ($isNpc) {

                    $npcModel = $combatNpcs->firstWhere('id', $id);

                    if ($npcModel) {

                        $isDeadNpc = $npcModel->current_hp <= 0;

                        $percent = $npcModel->max_hp > 0 ? max(0, min(100, ($npcModel->current_hp / $npcModel->max_hp) * 100)) : 0;

                        $npcHPData = [

                            'current' => $npcModel->current_hp,

                            'max' => $npcModel->max_hp,

                            'temporary' => max(0, (int) ($npcModel->temporary_hp ?? 0)),

                            'percent' => $percent,

                            'color' => $percent > 50 ? 'bg-emerald-500' : ($percent > 25 ? 'bg-amber-400' : 'bg-red-500')

                        ];

                    }

                } else {

                    $playerModel =
                        $combatPlayers
                            ->firstWhere(
                                'id',
                                $id
                            );

                    $linkedCharacter =
                        $playerModel?->character;

                    $characterCombat =
                        $linkedCharacter?->combat;

                    if ($characterCombat) {

                        $effectiveMaxHp =
                            max(
                                1,
                                (int) $characterCombat->max_hp
                                +
                                (int) $characterCombat->temporary_max_hp
                            );

                        $playerCurrentHp =
                            max(
                                0,
                                (int) $characterCombat->current_hp
                            );

                        $playerPercent =
                            max(
                                0,
                                min(
                                    100,
                                    (
                                        $playerCurrentHp
                                        /
                                        $effectiveMaxHp
                                    )
                                    * 100
                                )
                            );

                        $playerHPData = [
                            'current' =>
                                $playerCurrentHp,

                            'max' =>
                                $effectiveMaxHp,

                            'temporary' =>
                                max(
                                    0,
                                    (int) $characterCombat->temporary_hp
                                ),

                            'percent' =>
                                $playerPercent,

                            'color' =>
                                $playerPercent > 50
                                    ? 'bg-emerald-500'
                                    : (
                                        $playerPercent > 25
                                            ? 'bg-amber-400'
                                            : 'bg-red-500'
                                    ),
                        ];

                    }

                }

            @endphp

            <div
                @if($isNpc)
                    x-data="{ rowHp: @js($npcHPData), rowDead: @js($isDeadNpc) }"
                    x-on:spellbound:npc-hp-change.window="
                        if (Number($event.detail.npcId) === {{ (int) $id }} && rowHp) {
                            rowHp.current = Math.max(0, Number($event.detail.currentHp) || 0);
                            rowHp.max = Math.max(1, Number($event.detail.maxHp) || 1);
                            rowHp.temporary = Math.max(0, Number($event.detail.temporaryHp) || 0);
                            rowHp.percent = Math.max(0, Math.min(100, rowHp.current / rowHp.max * 100));
                            rowDead = rowHp.current <= 0;
                        }
                    "
                @endif
                class="sb-initiative-row sb-v45-card {{ $isNpc ? (($npcModel ?? null)?->isAlly() ? 'sb-v45-card--ally' : 'sb-v45-card--enemy') : 'sb-v45-card--player' }} {{ $isCurrentTurn ? 'sb-v45-card--active' : '' }} relative rounded-lg border transition-all duration-300 overflow-hidden group/row"

                :class="[

                    {{ $isCurrentTurn ? "'border-[#6b1d14] bg-gradient-to-r from-[#6b1d14]/10 to-transparent shadow-sm js-active-turn scale-[1.01]'" : "'border-[#d8cebe]/80 bg-white hover:border-[#cdbb9f] hover:shadow-sm'" }},

                    (selectedNpc == {{ $id }} && {{ $isNpc ? 'true' : 'false' }}) ? 'ring-2 ring-amber-500/50 bg-amber-50/50' : '',

                    {{ $isNpc ? "(rowDead ? 'opacity-75 grayscale-[0.6] bg-red-50/30 border-red-200' : '')" : "''" }}

                ]"

            >

                {{-- Marcador visual do turno atual --}}

                @if($isCurrentTurn)

                    <div class="sb-v45-turn-mark absolute left-0 top-0 bottom-0 w-1.5 bg-[#6b1d14] z-10 rounded-l-lg shadow-[2px_0_5px_rgba(107,29,20,0.2)]"></div>

                @endif

                <div class="sb-v45-card-top py-2 px-2.5 flex items-center justify-between gap-2 min-w-0 relative z-20">

                    {{-- Info Principal --}}

                    <div

                        @if($isNpc)

                            @click="
                                selectNpc(
                                    {{ $id }}
                                )
                            "

                        @elseif($linkedCharacter)
                            x-on:click="togglePlayerConditions({{ (int) $id }})"
                        @endif

                        class="sb-v45-card-main flex items-center gap-2.5 min-w-0 flex-1 {{ $isNpc || $linkedCharacter ? 'cursor-pointer group/name' : '' }}"

                    >

                        {{-- Ordem --}}

                        <div class="sb-v45-order w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center font-serif text-[11px] font-black transition-colors duration-300 {{ $isCurrentTurn ? 'bg-[#6b1d14] text-white shadow-inner' : ($isDeadNpc ? 'bg-stone-200 text-stone-500' : 'bg-[#eee8dc] text-[#8c6239]') }}">

                            {{ $loop->iteration }}

                        </div>

                        {{-- Nome e Status --}}

                        <div class="sb-v45-card-content min-w-0 flex-1">

                            <div class="sb-v45-card-title flex items-center gap-1.5">

                                <h3 class="sb-initiative-name font-serif text-[14px] font-bold truncate leading-tight transition-colors duration-300 {{ $isNpc ? 'group-hover/name:text-amber-700' : '' }} {{ $isNpc ? '' : ($isDeadNpc ? 'line-through decoration-red-700/60 decoration-2 text-stone-500' : 'text-[#6b1d14]') }}"
                                    @if($isNpc) :class="rowDead ? 'line-through decoration-red-700/60 decoration-2 text-stone-500' : 'text-[#6b1d14]'" @endif
                                    title="{{ $name }}">

                                    {{ $name }}

                                </h3>

                            </div>

                            <div class="sb-v45-meta flex items-center gap-1.5 mt-1">

                                @if($isNpc)

                                    <span class="text-[8px] uppercase font-bold text-red-800 bg-red-100/80 px-1 py-0.5 rounded border border-red-200 shrink-0">NPC</span>

                                    @if($npcModel)
                                        <span
                                            class="sb-npc-side-indicator {{ $npcModel->isAlly() ? 'sb-npc-side-indicator--ally' : 'sb-npc-side-indicator--enemy' }}"
                                            title="Definido quando o NPC foi adicionado ao combate">
                                            {{ $npcModel->isAlly() ? 'Aliado' : 'Inimigo' }}
                                        </span>
                                    @endif

                                    @if($npcHPData)

                                        <div class="sb-v45-health flex items-center gap-1.5 flex-1 min-w-0 max-w-[100px] group-hover/row:max-w-full transition-all duration-300">

                                            {{-- Barra de Vida Suavizada --}}

                                            <div class="h-1.5 flex-1 rounded-full bg-stone-200 overflow-hidden shadow-inner relative">

                                                <div class="h-full transition-all duration-500 ease-out relative"
                                                    :class="rowHp.percent > 50 ? 'bg-emerald-500' : (rowHp.percent > 25 ? 'bg-amber-400' : 'bg-red-500')"
                                                    :style="`width: ${rowHp.percent}%`">

                                                    <div class="absolute inset-0 bg-gradient-to-b from-white/30 to-transparent"></div>

                                                </div>

                                            </div>

                                            <span class="font-mono text-[9px] font-bold tracking-tighter shrink-0 transition-colors"
                                                :class="rowDead ? 'text-red-700' : 'text-stone-500'"
                                                x-text="rowDead ? 'Morto' : `${rowHp.current}/${rowHp.max}`">
                                            </span>
                                            <span x-show="rowHp.temporary > 0" x-cloak title="PV temporários"
                                                class="shrink-0 text-[8px] font-bold text-sky-700" x-text="`+${rowHp.temporary}`"></span>

                                        </div>

                                    @endif

                                @else

                                    <span class="text-[8px] uppercase font-bold text-[#8c6239] bg-[#eee8dc] px-1 py-0.5 rounded border border-[#d8cebe] shrink-0">
                                        PJ
                                    </span>

                                    @if($linkedCharacter)

                                        <span class="text-[7px] uppercase font-black text-[#7f5c18] bg-[#f8efd9] px-1 py-0.5 rounded border border-[#d4b36b]/45 shrink-0">
                                            Ficha
                                        </span>

                                        <div
                                            x-show="
                                                playerStates?.[
                                                    {{ $id }}
                                                ]?.has_combat
                                            "
                                            x-cloak
                                            class="sb-v45-health flex items-center gap-1.5 flex-1 min-w-0 max-w-[115px] group-hover/row:max-w-full transition-all duration-300"
                                        >

                                            <div class="h-1.5 flex-1 rounded-full bg-stone-200 overflow-hidden shadow-inner relative">

                                                <div
                                                    class="h-full transition-all duration-500 ease-out relative"
                                                    :class="
                                                        playerHpClass(
                                                            {{ $id }}
                                                        )
                                                    "
                                                    :style="
                                                        `width: ${
                                                            playerStates?.[
                                                                {{ $id }}
                                                            ]?.percent
                                                            ?? 0
                                                        }%`
                                                    "
                                                >
                                                    <div class="absolute inset-0 bg-gradient-to-b from-white/30 to-transparent"></div>
                                                </div>

                                            </div>

                                            <span
                                                class="font-mono text-[9px] font-bold tracking-tighter shrink-0 transition-colors"
                                                :class="
                                                    playerStates?.[
                                                        {{ $id }}
                                                    ]?.at_zero_hp
                                                        ? 'text-red-700'
                                                        : 'text-stone-500'
                                                "
                                                x-text="
                                                    `${
                                                        playerStates?.[
                                                            {{ $id }}
                                                        ]?.current_hp
                                                        ?? 0
                                                    }/${
                                                        playerStates?.[
                                                            {{ $id }}
                                                        ]?.max_hp
                                                        ?? 1
                                                    }`
                                                "
                                            ></span>

                                            <span
                                                x-show="
                                                    (
                                                        playerStates?.[
                                                            {{ $id }}
                                                        ]?.temporary_hp
                                                        ?? 0
                                                    ) > 0
                                                "
                                                x-cloak
                                                class="text-[7px] font-black text-[#8c6239]"
                                                title="PV temporários"
                                                x-text="
                                                    `+${
                                                        playerStates?.[
                                                            {{ $id }}
                                                        ]?.temporary_hp
                                                        ?? 0
                                                    }`
                                                "
                                            ></span>

                                        </div>

                                    @else

                                        <span class="text-[7px] uppercase font-black text-stone-500 bg-stone-100 px-1 py-0.5 rounded border border-stone-200 shrink-0">
                                            Manual
                                        </span>

                                    @endif

                                @endif

                            </div>

                        </div>

                    </div>

                    {{-- Ações (Input de Iniciativa e Delete) --}}

                    <div class="sb-v45-controls flex items-center gap-1 shrink-0" @click.stop>

                        <form action="{{ $updateRoute }}" method="POST" @submit.prevent="handleInitiativeAction($event)">

                            @csrf @method('PATCH')

                            <input type="number" name="initiative" value="{{ $initiativeVal }}" aria-label="Iniciativa de {{ $name }}" title="Alterar iniciativa" @change="$el.form.requestSubmit()"
                                @if($isNpc) :disabled="rowDead" @elseif($isDeadNpc) disabled @endif
                                class="w-8 h-6 rounded bg-white border border-[#cdbb9f]/70 text-center font-serif text-[13px] font-bold outline-none p-0 focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14]/50 transition-all shadow-inner text-[#6b1d14]"
                                @if($isNpc) :class="rowDead ? 'text-stone-400 bg-stone-100 border-stone-200' : 'text-[#6b1d14]'" @endif>

                        </form>

                        <form action="{{ $destroyRoute }}" method="POST" @submit.prevent="if(confirm('Remover {{ $name }} da iniciativa?')) handleInitiativeAction($event)">

                            @csrf @method('DELETE')

                            <button type="submit" class="sb-v45-remove w-6 h-6 hover:bg-red-100/80 text-stone-400 hover:text-red-600 flex items-center justify-center rounded transition-colors" title="Remover da iniciativa" aria-label="Remover {{ $name }} da iniciativa">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />

                                </svg>

                            </button>

                        </form>

                    </div>

                </div>

                @if (!$isNpc && $linkedCharacter)
                    {{-- Gerenciamento de condições: só a gaveta aberta utiliza espaço na iniciativa. --}}
                    <div x-show="conditionMenuPlayerId === {{ (int) $id }}" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="sb-initiative-conditions" x-on:click.stop>
                        <div class="sb-initiative-conditions__header">
                            <h4 class="sb-initiative-conditions__title">Condições de combate</h4>
                            <button type="button" x-on:click="conditionMenuPlayerId = null"
                                class="sb-initiative-conditions__close" aria-label="Fechar condições">×</button>
                        </div>

                        <template x-if="activePlayerConditions({{ (int) $id }}).length > 0">
                            <div class="sb-initiative-conditions__active">
                                <span class="sb-initiative-conditions__section-heading">Ativas · <span x-text="activePlayerConditions({{ (int) $id }}).length"></span></span>
                                <div class="sb-initiative-conditions__active-list">
                                    <template x-for="entry in activePlayerConditions({{ (int) $id }})" :key="'active-' + entry[0]">
                                        <button type="button" class="sb-initiative-condition sb-initiative-condition--active"
                                            x-on:click="setPlayerCondition({{ (int) $id }}, @js(route('combats.players.conditions.update', [$combat->id, $id])), entry[0], true)"
                                            :disabled="conditionBusy" :title="entry[1].effect + ' — clique para remover'">
                                            <span class="sb-initiative-condition__mark">✓</span>
                                            <span class="min-w-0 flex-1 truncate" x-text="entry[1].name"></span>
                                            <span class="sb-initiative-condition__remove" aria-hidden="true">×</span>
                                        </button>
                                    </template>
                                </div>
                                <span class="sb-initiative-conditions__hint">Clique em uma condição ativa para removê-la.</span>
                            </div>
                        </template>

                        <label class="sb-initiative-conditions__search">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
                            <input type="search" x-model="conditionSearch" placeholder="Buscar condição para aplicar..."
                                aria-label="Buscar condições" />
                        </label>
                        <div class="sb-initiative-conditions__section-heading">Aplicar condição</div>
                        <div class="sb-initiative-conditions__choices">
                            <template x-for="entry in availablePlayerConditions({{ (int) $id }})" :key="'available-' + entry[0]">
                                <button type="button" class="sb-initiative-condition"
                                    x-on:click="setPlayerCondition({{ (int) $id }}, @js(route('combats.players.conditions.update', [$combat->id, $id])), entry[0])"
                                    :disabled="conditionBusy" :title="entry[1].effect">
                                    <span class="sb-initiative-condition__mark">+</span>
                                    <span class="min-w-0 flex-1 truncate" x-text="entry[1].name"></span>
                                </button>
                            </template>
                        </div>
                        <p x-show="availablePlayerConditions({{ (int) $id }}).length === 0" x-cloak
                            class="sb-initiative-conditions__hint">Nenhuma condição disponível com esse filtro.</p>
                        <p x-show="conditionError" x-text="conditionError" x-cloak
                            class="sb-initiative-conditions__error" role="alert"></p>
                    </div>
                @endif

            </div>

        @empty

            <div class="sb-initiative-empty" role="status">
                <div class="sb-initiative-empty__seal" aria-hidden="true">
                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 3 14 14M19 3 5 17M4 16l4 4m12-4-4 4M3 21l4-4m14 4-4-4"/>
                    </svg>
                </div>
                <p class="sb-initiative-empty__title">Iniciativa vazia</p>
                <p class="sb-initiative-empty__body">Adicione jogadores ou criaturas para preparar este encontro.</p>
                <div class="sb-initiative-empty__rule" aria-hidden="true"></div>
            </div>

        @endforelse

    </div>



    {{-- V66.0.45 — Acesso direto: nenhuma gaveta intermediária. --}}
    <nav class="sb-v45-dock" aria-label="Ferramentas rápidas do combate">
        <div class="sb-v45-dock-grid">
            <button type="button" class="sb-v45-dock-button"
                x-on:click="openToolDrawer('spells')"
                title="Pesquisar feitiços liberados nesta campanha" aria-label="Abrir busca de feitiços da campanha">
                <span class="sb-v45-dock-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 6.5C9.5 5 6.6 4.9 3 5.7v13.4c3.6-.8 6.5-.7 9 1.1 2.5-1.8 5.4-1.9 9-1.1V5.7c-3.6-.8-6.5-.7-9 1.1Z"/><path d="M12 6.5v13.7M6.2 9h2.5M15.4 9h2.5"/></svg>
                </span>
                <span>Feitiços</span>
            </button>
            <button type="button" class="sb-v45-dock-button"
                x-on:click="openFloatingCalculator()"
                title="Abrir dados e calculadora" aria-label="Abrir dados e calculadora">
                <span class="sb-v45-dock-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 2.5 20.7 8 17.8 18.9 12 22 6.2 18.9 3.3 8Z"/><path d="M3.3 8h17.4M12 2.5 7 12.8 12 22l5-9.2-5-10.3ZM7 12.8h10"/></svg>
                </span>
                <span>Dados</span>
            </button>
        </div>
    </nav>

    {{-- Ferramentas teletransportadas para o BODY: não sofrem o recorte da iniciativa. --}}
    <template x-teleport="body">
        <div class="sb-master-tools" x-show="toolDrawerOpen" x-cloak x-on:keydown.escape.window="toolDrawerOpen = false">
            <div class="sb-tools-backdrop" x-on:click="toolDrawerOpen = false"></div>
            <section class="sb-tools-drawer" role="dialog" aria-modal="true" aria-label="Ferramentas do mestre">
                <div class="sb-tools-heading">
                    <div><strong>Ferramentas do mestre</strong><p>Consultas rápidas durante o combate</p></div>
                    <button type="button" class="sb-tools-close" x-on:click="toolDrawerOpen = false" aria-label="Fechar ferramentas">×</button>
                </div>
                <div class="sb-tools-content">
                    <template x-if="toolsView === 'home'">
                        <div>
                            <button type="button" class="sb-tools-action" x-on:click="openFloatingCalculator()">
                                <span class="sb-tools-action-icon">⚄</span>
                                <span><strong>Dados e calculadora</strong><small>Janela independente, arrastável e com teclado. Aceita contas e dados juntos.</small></span>
                            </button>
                            <button type="button" class="sb-tools-action" x-on:click="toolsView='spells'; $nextTick(() => $refs.globalSpellSearch?.focus())">
                                <span class="sb-tools-action-icon">⌕</span>
                                <span><strong>Catálogo de feitiços</strong><small>Pesquise todos os feitiços cadastrados, mesmo fora da ficha do NPC.</small></span>
                            </button>
                        </div>
                    </template>
                    <div x-show="toolsView === 'spells'" x-cloak>
                        <button type="button" class="sb-tools-close" style="width:auto;padding:0 10px;margin-bottom:12px;" x-on:click="toolsView = 'home'; selectedSpell = null">← Voltar</button>
                        <label for="sb-spell-global-search" style="font-size:12px;font-weight:700;color:var(--sb-accent);display:block;margin-bottom:7px;">Buscar nos feitiços liberados na campanha</label>
                        <input id="sb-spell-global-search" x-ref="globalSpellSearch" class="sb-tools-input" type="search"
                            x-model="spellQuery" x-on:input.debounce.280ms="searchGlobalSpells()" maxlength="80" autocomplete="off" placeholder="Ex.: escudo, counterspell, fireball...">
                        <p style="font-size:10px;opacity:.75;margin:7px 0 12px;">Digite pelo menos 2 letras. Apenas fontes autorizadas pelo mestre nesta campanha.</p>
                        <p x-show="spellSearching" style="font-size:12px;">Pesquisando...</p>
                        <p x-show="spellError" x-text="spellError" class="sb-tools-error" role="alert"></p>
                        <div x-show="!selectedSpell && spellResults.length" style="border:1px solid var(--sb-border);border-radius:9px;overflow:hidden;">
                            <template x-for="spell in spellResults" :key="spell.id">
                                <button type="button" class="sb-tools-result" x-on:click="selectedSpell = spell">
                                    <strong x-text="spell.name"></strong>
                                    <small x-text="(spell.level === 0 ? 'Truque' : spell.level + 'º nível') + ' · ' + (spell.school || 'Escola não informada')"></small>
                                </button>
                            </template>
                        </div>
                        <p x-show="spellSearched && !spellSearching && !spellResults.length && !spellError" style="font-size:12px;opacity:.75;">Nenhum feitiço permitido corresponde à busca.</p>
                        <template x-if="selectedSpell">
                            <article class="sb-tools-spell-detail">
                                <button type="button" class="sb-tools-close" style="float:right;" x-on:click="selectedSpell = null" aria-label="Voltar aos resultados">×</button>
                                <h4 x-text="selectedSpell.name"></h4>
                                <div class="sb-tools-spell-meta">
                                    <span x-text="selectedSpell.level === 0 ? 'Truque' : selectedSpell.level + 'º nível'"></span>
                                    <span x-text="selectedSpell.school || 'Escola não informada'"></span>
                                    <span x-text="'Conjuração: ' + (selectedSpell.casting_time || '—')"></span>
                                    <span x-text="'Alcance: ' + (selectedSpell.range || '—')"></span>
                                    <span x-text="'Duração: ' + (selectedSpell.duration || '—')"></span>
                                    <span x-text="selectedSpell.concentration ? 'Concentração' : selectedSpell.ritual ? 'Ritual' : 'Sem concentração'"></span>
                                </div>
                                <div class="sb-tools-spell-description" x-text="selectedSpell.description || 'Sem descrição cadastrada.'"></div>
                                <p x-show="selectedSpell.source" x-text="'Fonte: ' + (selectedSpell.source || '')" style="font-size:10px;margin-top:12px;opacity:.7;"></p>
                            </article>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </template>

    <template x-teleport="body">
        <div class="sb-master-tools" x-show="floatingCalcOpen" x-cloak x-on:keydown.window="handleCalculatorKey($event)"
            x-on:pointermove.window="moveCalcDrag($event)" x-on:pointerup.window="stopCalcDrag()" x-on:pointercancel.window="stopCalcDrag()">
            <section class="sb-floating-calc" role="dialog" aria-label="Calculadora de dados"
                x-bind:style="'left:' + calcPositionX + 'px;top:' + calcPositionY + 'px'">
                <div class="sb-floating-head" x-on:pointerdown="startCalcDrag($event)" title="Arraste para mover a calculadora">
                    <strong>⚄ Dados e calculadora</strong>
                    <button type="button" class="sb-tools-close" x-on:pointerdown.stop x-on:click="closeFloatingCalculator()" aria-label="Fechar calculadora">×</button>
                </div>
                <div class="sb-floating-body">
                    <input x-ref="calcExpression" x-model="expression" type="text" maxlength="180" class="sb-tools-input sb-floating-calc-input"
                        aria-label="Digite a conta com dados" spellcheck="false" autocomplete="off"
                        placeholder="2d10+15-10" x-on:keydown.enter.prevent="calculateRoll()" x-on:input="result=null;error=null;calculationSteps=[]">
                    <div class="sb-floating-calc-ops">
                        <template x-for="op in ['+','-','*','/']" :key="op">
                            <button type="button" class="sb-floating-key sb-floating-key--op" x-text="op === '*' ? '×' : op === '/' ? '÷' : op" x-on:click="appendCalculatorToken(op)"></button>
                        </template>
                    </div>
                    <div class="sb-floating-calc-dice">
                        <template x-for="die in ['d4','d6','d8','d10','d12','d20']" :key="die">
                            <button type="button" class="sb-floating-key" x-text="die" x-on:click="appendCalculatorToken(die)"></button>
                        </template>
                    </div>
                    <div class="sb-floating-calc-numbers">
                        <template x-for="key in ['7','8','9','(', '4','5','6',')', '1','2','3','.','C','0','⌫','=']" :key="key">
                            <button type="button" class="sb-floating-key" :class="key === '=' ? 'sb-floating-key--primary' : ''"
                                x-text="key" :disabled="isRolling"
                                x-on:click="key === '=' ? calculateRoll() : key === 'C' ? clearCalculator() : key === '⌫' ? backspaceCalculator() : appendCalculatorToken(key)"></button>
                        </template>
                    </div>
                    <p style="font-size:10px;opacity:.73;line-height:1.4;margin-top:10px;">Digite pelo teclado ou use os botões. Ex.: 135 - 50 + 2d10.</p>
                    <div x-show="result !== null" x-cloak class="sb-floating-total" role="status" x-text="result"></div>
                    <div x-show="calculationSteps.length" x-cloak>
                        <template x-for="(step,i) in calculationSteps" :key="i">
                            <p class="sb-floating-roll-line" x-text="step.label + ' → ' + step.total + (step.formatted ? ' · ' + step.formatted : '')"></p>
                        </template>
                    </div>
                    <p x-show="error" x-cloak x-text="error" class="sb-tools-error" role="alert"></p>
                </div>
            </section>
        </div>
    </template>

</div>