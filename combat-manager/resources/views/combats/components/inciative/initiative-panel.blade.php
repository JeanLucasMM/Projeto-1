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
@endphp

<div
    id="combat-initiative-panel-host"

    x-data="{

        loading: false,

        diceOpen: false,

        expression: '',

        result: null,

        error: null,

        isRolling: false,

        selectedNpc: sessionStorage.getItem('combat_{{ $combat->id }}_selected') ? parseInt(sessionStorage.getItem('combat_{{ $combat->id }}_selected')) : null,

        playerStates: @js($initialPlayerStates),

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
                ||
                Object.keys(
                    this.playerStates
                    ?? {}
                ).length === 0
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
                                    return false;
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

                    if (
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

                currentPanel.innerHTML =
                    updatedPanel.innerHTML;

                if (window.Alpine) {
                    Alpine.initTree(
                        currentPanel
                    );
                }

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

        async rollDice() {

            if (this.expression.trim() === '') return;

            this.isRolling = true;

            this.result = null;

            this.error = null;

            try {

                const response = await fetch('/api/roll', {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': '{{ csrf_token() }}'

                    },

                    body: JSON.stringify({ expression: this.expression })

                });

                const json = await response.json();

                if (json.success) {

                    this.result = json.formatted;

                } else {

                    this.error = json.message;

                }

            } catch (err) {

                this.error = 'Erro na comunicação.';

            } finally {

                this.isRolling = false;

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

    {{-- SEÇÃO 1: INFORMACÕES DO COMBATE & DADOS --}}

    <div class="border-b border-[#d8cebe] bg-[#eee8dc]/95 backdrop-blur-md p-4 shadow-sm sticky top-0 z-30 transition-all">

        {{-- Título do Encontro e Rodada --}}

        <div class="flex items-center justify-between gap-3 mb-3">

            <div class="min-w-0 flex-1">

                <h1 class="font-serif text-[15px] md:text-base font-bold text-[#6b1d14] truncate leading-tight drop-shadow-sm" title="{{ $combat->name }}">

                    {{ $combat->name }}

                </h1>

                <p class="text-[10px] uppercase tracking-[0.15em] text-[#8c6239]/90 font-bold mt-0.5">Gerenciador</p>

            </div>

            {{-- Badge de Rodada / Status --}}

            <div class="flex items-center gap-2 shrink-0">

                @if($combat->is_active)

                    <div class="flex items-center gap-1.5 bg-white border border-[#d8cebe] px-2 py-1 rounded-md shadow-sm transition-transform hover:scale-105">

                        <span class="text-[9.5px] font-bold uppercase tracking-wider text-[#8c6239]">Rodada</span>

                        <span class="w-5 h-5 rounded bg-[#6b1d14] text-white flex items-center justify-center font-serif text-[11.5px] font-bold shadow-inner">

                            {{ $combat->current_round }}

                        </span>

                    </div>

                @else

                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-amber-800 bg-amber-100/90 px-2 py-1 rounded-md border border-amber-300 shadow-sm animate-pulse">

                        Preparação

                    </span>

                @endif

                {{-- Botão Dado --}}

                <button

                    @click="diceOpen =  !diceOpen"

                    class="w-7 h-7 rounded-md bg-white hover:bg-stone-50 border border-[#d8cebe] flex items-center justify-center text-[#6b1d14] transition-all shadow-sm hover:shadow active:scale-95"

                    :class="diceOpen ? 'bg-stone-100 ring-2 ring-[#d8cebe] ring-offset-1 ring-offset-[#eee8dc]' : ''"

                    title="Rolador de Dados"

                >

                    <span class="text-sm transform transition-transform duration-300" :class="diceOpen ? 'rotate-12' : ''">🎲</span>

                </button>

            </div>

        </div>

        {{-- PAINEL RETRÁTIL DE DADOS --}}

        <div x-show="diceOpen" x-collapse.duration.300ms class="mt-3 pt-3 border-t border-[#d8cebe]/60">

            <div class="flex items-center gap-2">

                <input

                    x-model="expression"

                    @keydown.enter="rollDice"

                    type="text"

                    placeholder="Ex: 1d20+5"

                    class="flex-1 rounded-md bg-white border border-[#cdbb9f] px-3 py-1.5 text-[13px] font-mono uppercase focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14]/50 outline-none transition-all placeholder:normal-case placeholder:font-sans placeholder:text-stone-400 shadow-inner"

                    :disabled="isRolling"

                >

                <button

                    @click="rollDice"

                    :disabled="isRolling"

                    class="px-3 py-1.5 rounded-md bg-[#6b1d14] text-white text-xs font-bold tracking-wide hover:bg-[#53150f] transition-colors disabled:opacity-60 flex items-center shadow-sm active:scale-95"

                >

                    <span x-text="isRolling ? '...' : 'Rolar'"></span>

                </button>

            </div>

            <template x-if="result">

                <div x-transition.opacity.duration.300ms class="mt-2 p-1.5 rounded-md bg-white border border-[#cdbb9f]/60 text-center shadow-sm">

                    <p class="font-mono text-sm font-bold text-[#6b1d14]" x-text="result"></p>

                </div>

            </template>

            <template x-if="error">

                <div x-transition.opacity.duration.300ms class="mt-2 p-1.5 rounded-md bg-red-50 border border-red-200 text-center shadow-sm">

                    <p class="text-xs font-bold text-red-600" x-text="error"></p>

                </div>

            </template>

        </div>

        {{-- Ações Rápidas de Combate --}}

        <div class="flex flex-wrap gap-1.5 mt-3">

            @if(!$combat->is_active)

                <button
                    type="button"
                    @click="openPlayerModal = true"
                    class="flex-1 py-1.5 px-2 rounded-md bg-white hover:bg-stone-50 border border-[#d8cebe] text-[#6b1d14] text-[10.5px] font-bold uppercase tracking-wide transition-colors text-center shadow-sm active:scale-[0.98]"
                >
                    + Jogador
                </button>

                <button
                    type="button"
                    @click="openNpcModal = true"
                    class="flex-1 py-1.5 px-2 rounded-md bg-white hover:bg-stone-50 border border-[#d8cebe] text-[#6b1d14] text-[10.5px] font-bold uppercase tracking-wide transition-colors text-center shadow-sm active:scale-[0.98]"
                >
                    + NPC
                </button>

                <form action="{{ route('combats.start', $combat->id) }}" method="POST" @submit.prevent="handleInitiativeAction($event)" class="flex-1">

                    @csrf

                    <button class="w-full py-1.5 px-2 rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-bold uppercase tracking-wide text-[10.5px] transition-all text-center shadow-sm active:scale-[0.98] flex items-center justify-center gap-1.5">

                        <span class="text-xs">⚔</span>

                        <span>Iniciar</span>

                    </button>

                </form>

            @else

                <form action="{{ route('combats.next', $combat->id) }}" method="POST" @submit.prevent="handleInitiativeAction($event)" class="w-full">

                    @csrf

                    <button class="w-full py-1.5 px-3 rounded-md bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-bold uppercase tracking-widest text-xs transition-all text-center shadow-sm hover:shadow active:scale-[0.99] flex items-center justify-center gap-2">

                        <span>Próximo Turno</span>

                        <span class="text-sm leading-none">→</span>

                    </button>

                </form>

                <form action="{{ route('combats.reset', $combat->id) }}" method="POST" @submit.prevent="if(confirm('Tem certeza que deseja encerrar o combate?')) handleInitiativeAction($event)" class="w-full mt-1">

                    @csrf

                    <button class="w-full py-1 text-[#6b1d14]/60 hover:text-[#6b1d14] font-bold uppercase text-[10px] tracking-wide transition-colors text-center decoration-dashed hover:underline underline-offset-2">

                        Encerrar Encontro

                    </button>

                </form>

            @endif

        </div>

    </div>

    {{-- SEÇÃO 2: LISTA DE INICIATIVA --}}

    <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar">

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

                class="relative rounded-lg border transition-all duration-300 overflow-hidden group/row"

                :class="[

                    {{ $isCurrentTurn ? "'border-[#6b1d14] bg-gradient-to-r from-[#6b1d14]/10 to-transparent shadow-sm js-active-turn scale-[1.01]'" : "'border-[#d8cebe]/80 bg-white hover:border-[#cdbb9f] hover:shadow-sm'" }},

                    (selectedNpc == {{ $id }} && {{ $isNpc ? 'true' : 'false' }}) ? 'ring-2 ring-amber-500/50 bg-amber-50/50' : '',

                    {{ $isDeadNpc ? "'opacity-75 grayscale-[0.6] bg-red-50/30 border-red-200'" : "''" }}

                ]"

            >

                {{-- Marcador visual do turno atual --}}

                @if($isCurrentTurn)

                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#6b1d14] z-10 rounded-l-lg shadow-[2px_0_5px_rgba(107,29,20,0.2)]"></div>

                @endif

                <div class="py-2 px-2.5 flex items-center justify-between gap-2 relative z-20">

                    {{-- Info Principal --}}

                    <div

                        @if($isNpc)

                            @click="
                                selectNpc(
                                    {{ $id }}
                                )
                            "

                        @endif

                        class="flex items-center gap-2.5 min-w-0 flex-1 {{ $isNpc ? 'cursor-pointer group/name' : '' }}"

                    >

                        {{-- Ordem --}}

                        <div class="w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center font-serif text-[11px] font-black transition-colors duration-300 {{ $isCurrentTurn ? 'bg-[#6b1d14] text-white shadow-inner' : ($isDeadNpc ? 'bg-stone-200 text-stone-500' : 'bg-[#eee8dc] text-[#8c6239]') }}">

                            {{ $loop->iteration }}

                        </div>

                        {{-- Nome e Status --}}

                        <div class="min-w-0 flex-1">

                            <div class="flex items-center gap-1.5">

                                <h3 class="font-serif text-[14px] font-bold truncate leading-tight transition-colors duration-300 {{ $isNpc ? 'group-hover/name:text-amber-700' : '' }} {{ $isDeadNpc ? 'line-through decoration-red-700/60 decoration-2 text-stone-500' : 'text-[#6b1d14]' }}" title="{{ $name }}">

                                    {{ $name }}

                                </h3>

                            </div>

                            <div class="flex items-center gap-1.5 mt-1">

                                @if($isNpc)

                                    <span class="text-[8px] uppercase font-bold text-red-800 bg-red-100/80 px-1 py-0.5 rounded border border-red-200 shrink-0">NPC</span>

                                    @if($npcHPData)

                                        <div class="flex items-center gap-1.5 flex-1 min-w-0 max-w-[100px] group-hover/row:max-w-full transition-all duration-300">

                                            {{-- Barra de Vida Suavizada --}}

                                            <div class="h-1.5 flex-1 rounded-full bg-stone-200 overflow-hidden shadow-inner relative">

                                                <div class="{{ $npcHPData['color'] }} h-full transition-all duration-500 ease-out relative" style="width: {{ $npcHPData['percent'] }}%">

                                                    <div class="absolute inset-0 bg-gradient-to-b from-white/30 to-transparent"></div>

                                                </div>

                                            </div>

                                            <span class="font-mono text-[9px] font-bold tracking-tighter shrink-0 transition-colors {{ $isDeadNpc ? 'text-red-700' : 'text-stone-500' }}">

                                                {{ $isDeadNpc ? 'Morto' : $npcHPData['current'].'/'.$npcHPData['max'] }}

                                            </span>

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
                                            class="flex items-center gap-1.5 flex-1 min-w-0 max-w-[115px] group-hover/row:max-w-full transition-all duration-300"
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

                    <div class="flex items-center gap-1 shrink-0" @click.stop>

                        <form action="{{ $updateRoute }}" method="POST" @submit.prevent="handleInitiativeAction($event)">

                            @csrf @method('PATCH')

                            <input type="number" name="initiative" value="{{ $initiativeVal }}" @change="$el.form.requestSubmit()" {{ $isDeadNpc ? 'disabled' : '' }}

                                class="w-8 h-6 rounded bg-white border border-[#cdbb9f]/70 text-center font-serif text-[13px] font-bold outline-none p-0 focus:ring-2 focus:ring-[#6b1d14]/30 focus:border-[#6b1d14]/50 transition-all shadow-inner {{ $isDeadNpc ? 'text-stone-400 bg-stone-100 border-stone-200' : 'text-[#6b1d14]' }}">

                        </form>

                        <form action="{{ $destroyRoute }}" method="POST" @submit.prevent="if(confirm('Remover {{ $name }} da iniciativa?')) handleInitiativeAction($event)">

                            @csrf @method('DELETE')

                            <button type="submit" class="w-6 h-6 hover:bg-red-100/80 text-stone-400 hover:text-red-600 flex items-center justify-center rounded transition-colors" title="Remover">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />

                                </svg>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="rounded-lg border-2 border-dashed border-[#d8cebe] bg-[#eee8dc]/40 py-6 px-4 text-center mt-4">

                <span class="text-xl block mb-2 opacity-50">⚔️</span>

                <p class="text-[13px] font-serif font-bold text-[#8c6239]">Nenhum combatente</p>

                <p class="text-[10px] text-stone-500 mt-1">Adicione jogadores ou NPCs acima</p>

            </div>

        @endforelse

    </div>

</div>