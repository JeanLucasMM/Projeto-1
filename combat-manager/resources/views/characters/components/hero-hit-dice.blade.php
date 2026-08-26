@props([
    'character',
    'hitDice' => [],
])

@php
    $hitDice = collect($hitDice)
        ->map(fn ($die) => [
            'die' => strtolower((string) ($die['die'] ?? 'd8')),
            'current' => max(0, (int) ($die['current'] ?? 0)),
            'maximum' => max(0, (int) ($die['maximum'] ?? 0)),
        ])
        ->filter(fn ($die) => $die['maximum'] > 0)
        ->values();

    $constitutionScore = (int) ($character->abilities?->constitution ?? 10);

    $constitutionModifier = (int) floor(
        ($constitutionScore - 10) / 2
    );
@endphp

@once
    @push('styles')
        <style>
            @keyframes hit-dice-glow {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(34, 197, 94, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(34, 197, 94, .18);
                }
            }

            @keyframes hit-dice-danger {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(220, 38, 38, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(220, 38, 38, .18);
                }
            }

            @keyframes hit-dice-result {
                0% {
                    opacity: 0;
                    transform: translateY(-5px) scale(.97);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            @keyframes hit-dice-pulse {
                0%, 100% {
                    box-shadow: 0 0 0 rgba(140, 29, 24, 0);
                }

                50% {
                    box-shadow: 0 0 0 rgba(140, 29, 24, .12);
                }
            }

            .hit-dice-good {
                animation: hit-dice-glow 3s ease-in-out infinite;
            }

            .hit-dice-danger {
                animation: hit-dice-danger 2s ease-in-out infinite;
            }

            .hit-dice-result {
                animation: hit-dice-result .18s ease-out;
            }

            .hit-dice-active {
                animation: hit-dice-pulse 2.4s ease-in-out infinite;
            }

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type="number"] {
                -moz-appearance: textfield;
            }
        </style>
    @endpush

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'heroHitDice',
                (
                    initialDice,
                    constitutionModifier,
                    saveUrl,
                    rollUrl
                ) => ({
                    modalOpen: false,
                    modalTab: 'history',

                    saving: false,
                    rolling: false,

                    rollTimer: null,

                    constitutionModifier,

                    dice: initialDice,
                    history: [],

                    activeRoll: {
                        visible: false,
                        rolls: [],
                        total: 0,
                    },

                    get totalCurrent() {
                        return this.dice.reduce(
                            (total, die) =>
                                total + (parseInt(die.current) || 0),
                            0
                        );
                    },

                    get totalMaximum() {
                        return this.dice.reduce(
                            (total, die) =>
                                total + (parseInt(die.maximum) || 0),
                            0
                        );
                    },

                    get remainingPercent() {
                        if (this.totalMaximum <= 0) {
                            return 0;
                        }

                        return Math.max(
                            0,
                            Math.min(
                                100,
                                (
                                    this.totalCurrent /
                                    this.totalMaximum
                                ) * 100
                            )
                        );
                    },

                    get remainingClass() {
                        if (this.remainingPercent > 50) {
                            return 'bg-emerald-700 hit-dice-good';
                        }

                        if (this.remainingPercent > 25) {
                            return 'bg-amber-600';
                        }

                        return 'bg-red-700 hit-dice-danger';
                    },

                    get currentSummary() {
                        if (!this.dice.length) {
                            return '0';
                        }

                        return this.dice
                            .map(
                                die =>
                                    `${die.current}${String(die.die).toUpperCase()}`
                            )
                            .join(' / ');
                    },

                    get maximumSummary() {
                        if (!this.dice.length) {
                            return '0';
                        }

                        return this.dice
                            .map(
                                die =>
                                    `${die.maximum}${String(die.die).toUpperCase()}`
                            )
                            .join(' / ');
                    },

                    get activeRollSummary() {
                        if (!this.activeRoll.rolls.length) {
                            return '';
                        }

                        return this.activeRoll.rolls
                            .map(roll => roll.value)
                            .join(' + ');
                    },

                    get activeConstitutionTotal() {
                        return (
                            this.constitutionModifier *
                            this.activeRoll.rolls.length
                        );
                    },

                    get activeRollTotal() {
                        return Math.max(
                            0,
                            this.activeRoll.total +
                            this.activeConstitutionTotal
                        );
                    },

                    openModal(tab = 'history') {
                        this.modalTab = tab;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    clearRollTimer() {
                        if (this.rollTimer) {
                            clearTimeout(this.rollTimer);
                            this.rollTimer = null;
                        }
                    },

                    startRollWindow() {
                        this.clearRollTimer();

                        this.rollTimer = setTimeout(() => {
                            this.finishRollBatch();
                        }, 6000);
                    },

                    finishRollBatch() {
                        if (!this.activeRoll.rolls.length) {
                            this.activeRoll.visible = false;
                            this.clearRollTimer();
                            return;
                        }

                        const rawTotal =
                            Number(this.activeRoll.total) || 0;

                        const constitutionTotal =
                            this.constitutionModifier *
                            this.activeRoll.rolls.length;

                        const totalHealed = Math.max(
                            0,
                            rawTotal + constitutionTotal
                        );

                        const groups = Object.values(
                            this.activeRoll.rolls.reduce(
                                (grouped, roll) => {
                                    const die =
                                        String(
                                            roll.die ?? 'd8'
                                        ).toLowerCase();

                                    if (!grouped[die]) {
                                        grouped[die] = {
                                            die,
                                            values: [],
                                        };
                                    }

                                    grouped[die].values.push(
                                        parseInt(roll.value) || 0
                                    );

                                    return grouped;
                                },
                                {}
                            )
                        );

                        this.history.unshift({
                            id: Date.now(),

                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                            }),

                            rolls: [...this.activeRoll.rolls],

                            groups,

                            rawTotal,

                            modifier: this.constitutionModifier,

                            constitutionTotal,

                            totalHealed,
                        });

                        this.activeRoll = {
                            visible: false,
                            rolls: [],
                            total: 0,
                        };

                        this.clearRollTimer();
                    },

                    normalizeDice() {
                        return this.dice
                            .map(die => {
                                const maximum = Math.max(
                                    0,
                                    parseInt(die.maximum) || 0
                                );

                                const current = Math.max(
                                    0,
                                    Math.min(
                                        maximum,
                                        parseInt(die.current) || 0
                                    )
                                );

                                return {
                                    die: String(die.die).toLowerCase(),
                                    current,
                                    maximum,
                                };
                            })
                            .filter(die => die.maximum > 0);
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | SINCRONIZA APÓS DESCANSO
                    |--------------------------------------------------------------------------
                    |
                    | O CharacterRestService é a fonte de verdade.
                    |
                    | Não salvamos nada aqui novamente.
                    | Apenas recebemos o estado final retornado pelo backend.
                    |
                    */
                    syncAfterRest(payload) {
                        if (!payload) {
                            return;
                        }

                        const restType =
                            payload?.rest?.type ?? null;

                        /*
                        |--------------------------------------------------------------------------
                        | DESCANSO CURTO
                        |--------------------------------------------------------------------------
                        |
                        | Dados de Vida não são recuperados.
                        |
                        */
                        if (restType !== 'long') {
                            return;
                        }

                        const serverDice =
                            payload?.combat?.hit_dice;

                        if (!Array.isArray(serverDice)) {
                            console.warn(
                                'Descanso longo concluído, mas combat.hit_dice não foi retornado.'
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | CANCELA ESTADOS TRANSITÓRIOS
                        |--------------------------------------------------------------------------
                        */

                        this.clearRollTimer();

                        this.rolling = false;

                        this.activeRoll = {
                            visible: false,
                            rolls: [],
                            total: 0,
                        };

                        /*
                        |--------------------------------------------------------------------------
                        | HISTÓRICO DA SESSÃO
                        |--------------------------------------------------------------------------
                        |
                        | É somente estado visual do componente.
                        |
                        */
                        this.history = [];

                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO RETORNADO PELO SERVIDOR
                        |--------------------------------------------------------------------------
                        */

                        this.dice = serverDice
                            .map(die => {
                                const maximum = Math.max(
                                    0,
                                    parseInt(
                                        die?.maximum
                                    ) || 0
                                );

                                const current = Math.max(
                                    0,
                                    Math.min(
                                        maximum,
                                        parseInt(
                                            die?.current
                                        ) || 0
                                    )
                                );

                                return {
                                    die:
                                        String(
                                            die?.die ?? 'd8'
                                        ).toLowerCase(),

                                    current,

                                    maximum,
                                };
                            })
                            .filter(
                                die =>
                                    die.maximum > 0
                            );
                    },

                    async persistDice() {
                        const normalized = this.normalizeDice();

                        const formData = new FormData();

                        formData.append(
                            '_token',
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                ?.getAttribute('content')
                                ?? '{{ csrf_token() }}'
                        );

                        formData.append(
                            '_method',
                            'PATCH'
                        );

                        formData.append(
                            'hit_dice',
                            JSON.stringify(normalized)
                        );

                        try {
                            const response = await fetch(
                                saveUrl,
                                {
                                    method: 'POST',

                                    body: formData,

                                    headers: {
                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'Accept':
                                            'application/json',
                                    },
                                }
                            );

                            if (!response.ok) {
                                throw new Error(
                                    'Falha ao salvar os Dados de Vida.'
                                );
                            }

                            return true;
                        } catch (error) {
                            console.error(
                                'Erro ao salvar Dados de Vida:',
                                error
                            );

                            return false;
                        }
                    },

                    updateDieMax(die, value) {
                        const newMaximum = Math.max(
                            0,
                            parseInt(value) || 0
                        );

                        const oldMaximum = Math.max(
                            0,
                            parseInt(die.maximum) || 0
                        );

                        const difference =
                            newMaximum - oldMaximum;

                        die.maximum = newMaximum;

                        if (difference > 0) {
                            die.current = Math.min(
                                die.maximum,
                                (
                                    parseInt(die.current) || 0
                                ) + difference
                            );
                        } else if (
                            die.current > die.maximum
                        ) {
                            die.current = die.maximum;
                        }
                    },

                    addDie(type) {
                        const existing = this.dice.find(
                            die => die.die === type
                        );

                        if (existing) {
                            this.updateDieMax(
                                existing,
                                (
                                    parseInt(
                                        existing.maximum
                                    ) || 0
                                ) + 1
                            );

                            return;
                        }

                        this.dice.push({
                            die: type,
                            current: 1,
                            maximum: 1,
                        });
                    },

                    removeDie(index) {
                        this.dice.splice(index, 1);
                    },

                    async spendDie(die) {
                        if (
                            this.rolling ||
                            (
                                parseInt(die.current) || 0
                            ) <= 0
                        ) {
                            return;
                        }

                        die.current = Math.max(
                            0,
                            (
                                parseInt(die.current) || 0
                            ) - 1
                        );

                        await this.persistDice();
                    },

                    async roll(die) {
                        if (
                            this.rolling ||
                            (
                                parseInt(die.current) || 0
                            ) <= 0
                        ) {
                            return;
                        }

                        this.rolling = true;

                        die.current = Math.max(
                            0,
                            (
                                parseInt(die.current) || 0
                            ) - 1
                        );

                        const faces =
                            parseInt(
                                String(die.die).replace(
                                    'd',
                                    ''
                                )
                            ) || 8;

                        let value =
                            Math.floor(
                                Math.random() * faces
                            ) + 1;

                        try {
                            const response = await fetch(
                                rollUrl,
                                {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'Accept':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            '{{ csrf_token() }}',

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },

                                    body: JSON.stringify({
                                        expression:
                                            `1d${faces}`,
                                    }),
                                }
                            );

                            if (response.ok) {
                                const result =
                                    await response.json();

                                const apiValue =
                                    result?.data?.total
                                    ?? result?.data?.result
                                    ?? result?.data?.value
                                    ?? result?.data?.sum
                                    ?? result?.total
                                    ?? result?.result
                                    ?? null;

                                if (
                                    apiValue !== null &&
                                    !Number.isNaN(
                                        parseInt(apiValue)
                                    )
                                ) {
                                    value =
                                        parseInt(apiValue);
                                }
                            }
                        } catch (error) {
                            console.warn(
                                'API de dados indisponível. Usando rolagem local.',
                                error
                            );
                        }

                        if (!this.activeRoll.visible) {
                            this.activeRoll = {
                                visible: true,
                                rolls: [],
                                total: 0,
                            };
                        }

                        this.activeRoll.rolls.push({
                            die: die.die,
                            value,
                        });

                        this.activeRoll.total += value;

                        await this.persistDice();

                        this.startRollWindow();

                        this.rolling = false;
                    },

                    async saveConfig() {
                        this.saving = true;

                        try {
                            const success =
                                await this.persistDice();

                            if (success) {
                                this.modalOpen = false;
                            }
                        } finally {
                            this.saving = false;
                        }
                    },
                })
            );
        });
    </script>
@endonce


<div
    x-data="heroHitDice(
        @js($hitDice->all()),
        {{ $constitutionModifier }},
        @js(route('characters.combat.update', $character)),
        @js(url('/api/roll'))
    )"

    @keydown.escape.window="
        closeModal()
    "

    @character-rest-completed.window="
        syncAfterRest($event.detail)
    "

    class="
        relative
        w-fit
        max-w-full
    "
>

    {{-- ============================================================
         ESTRUTURA PRINCIPAL
    ============================================================= --}}

    <div class="relative">

        {{-- ========================================================
             CAIXA PRINCIPAL DOS DADOS
        ========================================================= --}}

        <div
            class="
                hit-dice-active
                relative
                z-20
                flex
                left-[-3px]
                top-[-0.5px]
                items-center
                overflow-visible
                rounded-t-md
                border
                border-b-0
                border-[#cdbb9f]/70
                bg-[#faf8f2]
                shadow-sm
            "
        >

            {{-- NOME / ABRIR MODAL --}}

            <button
                type="button"

                @click="
                    openModal('history')
                "

                title="
                    Abrir Dados de Vida
                "

                class="
                    group
                    relative
                    z-10
                    flex
                    h-full
                    shrink-0
                    items-center
                    gap-1
                    border-r
                    border-[#cdbb9f]/40
                    px-1.5
                    py-1
                    transition
                    hover:bg-[#efe9dc]/70
                "
            >

                <span
                    class="
                        text-[7.5px]
                        font-black
                        uppercase
                        tracking-wider
                        text-[#8c6239]
                        transition
                        group-hover:text-[#53150f]
                    "
                >
                    Dados de Vida
                </span>



            </button>


            {{-- PREENCHIMENTO DE ESTADO --}}

            <div
                class="
                    pointer-events-none
                    absolute
                    inset-0
                    opacity-[.12]
                    transition-all
                    duration-500
                "
                :class="
                    remainingClass
                "
            ></div>


            {{-- DADOS --}}

            <div
                class="
                    relative
                    z-10
                    flex
                    items-center
                    px-1
                    py-0.5
                "
            >

                <template
                    x-for="
                        (die, index) in dice
                    "
                    :key="
                        `${die.die}-${index}`
                    "
                >

                    <span
                        class="
                            inline-flex
                            items-center
                        "
                    >

                        {{-- GASTAR --}}

                        <button
                            type="button"

                            @click="
                                spendDie(die)
                            "

                            :disabled="
                                rolling ||
                                parseInt(die.current) <= 0
                            "

                            title="
                                Gastar 1 Dado de Vida
                            "

                            class="
                                flex
                                h-5
                                items-center
                                px-0.5
                                text-[10px]
                                font-bold
                                text-[#8c7f70]
                                transition
                                hover:text-[#8c1d18]
                                active:scale-90
                                disabled:cursor-not-allowed
                                disabled:opacity-20
                            "
                        >
                            ‹
                        </button>


                        {{-- ROLAR --}}

                        <button
                            type="button"

                            @click="
                                roll(die)
                            "

                            :disabled="
                                rolling ||
                                parseInt(die.current) <= 0
                            "

                            title="
                                Rolar Dado de Vida
                            "

                            class="
                                px-0.5
                                font-serif
                                text-[10px]
                                font-black
                                tracking-wide
                                text-[#53150f]
                                transition
                                hover:scale-105
                                hover:text-[#8c1d18]
                                active:scale-95
                                disabled:cursor-not-allowed
                                disabled:opacity-25
                            "

                            x-text="
                                `${die.current}${die.die}`
                            "
                        ></button>


                        <span
                            x-show="
                                index <
                                dice.length - 1
                            "

                            class="
                                mx-0.5
                                text-[10px]
                                text-[#b5a898]
                            "
                        >
                            /
                        </span>

                    </span>

                </template>


                <span
                    x-show="
                        dice.length === 0
                    "

                    class="
                        px-1
                        text-[10px]
                        font-bold
                        text-[#a09383]
                    "
                >
                    0d0
                </span>

            </div>


        </div>


        {{-- ========================================================
             PONTE INFERIOR
        ========================================================= --}}

        <div
            class="
                pointer-events-none
                absolute
                left-1/2
                top-full
                z-[16]

                -translate-x-1/2
                -translate-y-[11px]

                rounded-b-2xl

                border-x
                border-b
                border-[#cdbb9f]/70

                bg-[#faf8f2]/70

                shadow-[0_8px_9px_rgba(83,21,15,.05)]
            "
        ></div>


        {{-- ========================================================
             RESULTADO DA ROLAGEM
             ABRE PARA BAIXO
        ========================================================= --}}

        <div
            x-show="
                activeRoll.visible
            "

            x-cloak

            x-transition:enter="
                transition
                ease-out
                duration-180
            "

            x-transition:enter-start="
                opacity-0
                -translate-y-1
                scale-[.97]
            "

            x-transition:enter-end="
                opacity-100
                translate-y-0
                scale-100
            "

            x-transition:leave="
                transition
                ease-in
                duration-140
            "

            x-transition:leave-start="
                opacity-100
                translate-y-0
                scale-100
            "

            x-transition:leave-end="
                opacity-0
                -translate-y-1
                scale-[.97]
            "

            class="
                absolute
                right-0
                top-full
                z-50

                mt-1.5

                min-w-[138px]

                overflow-hidden

                rounded-b-xl
                rounded-t-md

                border
                border-[#cdbb9f]/80

                bg-[#faf8f2]

                px-3
                pb-2.5
                pt-2

                text-center

                shadow-[0_7px_18px_rgba(83,21,15,.12)]
            "
        >

            {{-- CONEXÃO VISUAL --}}

            <div
                class="
                    pointer-events-none
                    absolute
                    left-1/2
                    top-0

                    h-px
                    w-[80%]

                    -translate-x-1/2

                    bg-[#d8c7ab]/70
                "
            ></div>


            {{-- TÍTULO --}}

            <div
                class="
                    text-[6.5px]
                    font-black
                    uppercase
                    tracking-[0.18em]
                    text-[#8c6239]
                "
            >
                Rolagem
            </div>


            {{-- RESULTADOS --}}

            <div
                class="
                    hit-dice-result
                    mt-1
                    font-serif
                    text-[15px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    activeRollSummary
                "
            ></div>


            {{-- CÁLCULO --}}

            <div
                class="
                    mt-1.5
                    flex
                    items-center
                    justify-center
                    gap-1
                    text-[7.5px]
                    text-[#8c7f70]
                "
            >

                <span>
                    Soma
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        activeRoll.total
                    "
                ></span>

                <span
                    class="
                        text-[#b5a898]
                    "
                >
                    +
                </span>

                <span>
                    CON
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        activeConstitutionTotal >= 0
                            ? '+' + activeConstitutionTotal
                            : activeConstitutionTotal
                    "
                ></span>

            </div>


            {{-- TOTAL RECUPERADO --}}

            <div
                class="
                    mt-1.5

                    border-t
                    border-[#e8dfd1]

                    pt-1.5
                "
            >

                <span
                    class="
                        text-[6px]
                        font-black
                        uppercase
                        tracking-[0.16em]
                        text-[#8c6239]
                    "
                >
                    Recupera
                </span>

                <div
                    class="
                        mt-0.5
                        font-serif
                        text-[15px]
                        font-black
                        leading-none
                        text-[#8c1d18]
                    "
                >
                    +

                    <span
                        x-text="
                            activeRollTotal
                        "
                    ></span>

                    PV
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         MODAL
    ============================================================= --}}

    <template x-teleport="body">

        <div
            x-show="
                modalOpen
            "

            x-cloak

            x-transition:enter="
                transition-opacity
                ease-out
                duration-150
            "

            x-transition:enter-start="
                opacity-0
            "

            x-transition:enter-end="
                opacity-100
            "

            x-transition:leave="
                transition-opacity
                ease-in
                duration-100
            "

            x-transition:leave-start="
                opacity-100
            "

            x-transition:leave-end="
                opacity-0
            "

            class="
                fixed
                inset-0
                z-[120]

                flex
                items-center
                justify-center

                bg-[#2b1d17]/55

                p-4

                backdrop-blur-sm
            "
        >

            {{-- BACKDROP --}}

            <div
                class="
                    absolute
                    inset-0
                "

                @click="
                    closeModal()
                "
            ></div>


            {{-- PAINEL --}}

            <div
                @click.stop

                class="
                    relative
                    z-10

                    flex
                    max-h-[80vh]
                    w-full
                    max-w-sm
                    flex-col

                    overflow-hidden

                    rounded-2xl

                    border
                    border-[#cdbb9f]

                    bg-[#faf7f2]

                    shadow-2xl
                "
            >

                {{-- =================================================
                     HEADER
                ================================================== --}}

                <div
                    class="
                        flex
                        items-center
                        justify-between

                        border-b
                        border-[#e8dfd1]

                        bg-[#efe9dc]/70

                        px-4
                        py-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c7f70]
                            "
                        ></p>

                        <h3
                            class="
                                mt-0.5
                                font-serif
                                text-base
                                font-black
                                text-[#53150f]
                            "
                        >
                            Dados de Vida
                        </h3>

                    </div>


                    <button
                        type="button"

                        @click="
                            closeModal()
                        "

                        class="
                            flex
                            h-7
                            w-7
                            items-center
                            justify-center

                            rounded-lg

                            text-[#8c7f70]

                            transition

                            hover:bg-[#e6d9c7]
                            hover:text-[#53150f]
                        "
                    >
                        ×
                    </button>

                </div>


                {{-- =================================================
                     ABAS
                ================================================== --}}

                <div
                    class="
                        flex

                        border-b
                        border-[#e8dfd1]

                        bg-[#f5efe6]

                        px-2
                    "
                >

                    <button
                        type="button"

                        @click="
                            modalTab = 'history'
                        "

                        class="
                            relative
                            flex-1
                            py-2

                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest

                            transition
                        "

                        :class="
                            modalTab === 'history'
                                ? 'text-[#53150f]'
                                : 'text-[#8c7f70] hover:text-[#53150f]'
                        "
                    >
                        Histórico

                        <span
                            x-show="
                                modalTab === 'history'
                            "

                            class="
                                absolute
                                inset-x-3
                                bottom-0
                                h-0.5
                                rounded-full
                                bg-[#6b1d14]
                            "
                        ></span>

                    </button>


                    <button
                        type="button"

                        @click="
                            modalTab = 'config'
                        "

                        class="
                            relative
                            flex-1
                            py-2

                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest

                            transition
                        "

                        :class="
                            modalTab === 'config'
                                ? 'text-[#53150f]'
                                : 'text-[#8c7f70] hover:text-[#53150f]'
                        "
                    >
                        Configurar

                        <span
                            x-show="
                                modalTab === 'config'
                            "

                            class="
                                absolute
                                inset-x-3
                                bottom-0
                                h-0.5
                                rounded-full
                                bg-[#6b1d14]
                            "
                        ></span>

                    </button>

                </div>


                {{-- =================================================
                     HISTÓRICO
                ================================================== --}}

                <div
                    x-show="
                        modalTab === 'history'
                    "

                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-4
                    "
                >

                    <div
                        class="
                            mb-3
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c7f70]
                                "
                            >
                                Restauração
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    font-serif
                                    text-sm
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Rolagens recentes
                            </p>

                        </div>


                        <div
                            class="
                                shrink-0
                                rounded-md
                                border
                                border-[#d8cdbc]/70
                                bg-[#efe9dc]/70
                                px-2
                                py-1
                                text-[7.5px]
                                font-black
                                uppercase
                                tracking-wide
                                text-[#8c6239]
                            "
                        >
                            CON
                            {{ $constitutionModifier >= 0 ? '+' : '' }}{{ $constitutionModifier }}
                            / dado
                        </div>

                    </div>


                    <div class="space-y-1.5">

                        <template
                            x-for="
                                entry in history
                            "

                            :key="
                                entry.id
                            "
                        >

                            <div
                                class="
                                    rounded-lg
                                    border
                                    border-[#e4d9ca]
                                    bg-white/65
                                    px-2.5
                                    py-2
                                "
                            >

                                <div
                                    class="
                                        flex
                                        flex-wrap
                                        items-center
                                        gap-x-1.5
                                        gap-y-1
                                    "
                                >

                                    {{-- DADOS ROLADOS --}}

                                    <template
                                        x-for="
                                            (group, groupIndex) in entry.groups
                                        "

                                        :key="
                                            `${entry.id}-${group.die}`
                                        "
                                    >

                                        <span
                                            class="
                                                inline-flex
                                                min-w-0
                                                items-baseline
                                                gap-1
                                            "
                                        >

                                            <span
                                                class="
                                                    font-serif
                                                    text-[10px]
                                                    font-black
                                                    lowercase
                                                    text-[#53150f]
                                                "

                                                x-text="
                                                    group.die
                                                "
                                            ></span>

                                            <span
                                                class="
                                                    text-[8px]
                                                    text-[#b5a898]
                                                "
                                            >
                                                —
                                            </span>

                                            <span
                                                class="
                                                    font-serif
                                                    text-[10px]
                                                    font-bold
                                                    tracking-[0.01em]
                                                    text-[#6b1d14]
                                                "

                                                x-text="
                                                    group.values.join(', ')
                                                "
                                            ></span>

                                            <span
                                                x-show="
                                                    groupIndex <
                                                    entry.groups.length - 1
                                                "

                                                class="
                                                    ml-0.5
                                                    text-[8px]
                                                    text-[#c0b3a3]
                                                "
                                            >
                                                /
                                            </span>

                                        </span>

                                    </template>


                                    {{-- CONSTITUIÇÃO --}}

                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            text-[#b5a898]
                                        "

                                        x-text="
                                            entry.constitutionTotal >= 0
                                                ? '+'
                                                : '−'
                                        "
                                    ></span>

                                    <span
                                        class="
                                            inline-flex
                                            items-baseline
                                            gap-1
                                        "
                                    >

                                        <span
                                            class="
                                                font-serif
                                                text-[10px]
                                                font-black
                                                text-[#8c6239]
                                            "

                                            x-text="
                                                Math.abs(
                                                    entry.constitutionTotal
                                                )
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c7f70]
                                            "
                                        >
                                            CON
                                        </span>

                                    </span>


                                    {{-- TOTAL --}}

                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            text-[#b5a898]
                                        "
                                    >
                                        =
                                    </span>

                                    <span
                                        class="
                                            inline-flex
                                            items-baseline
                                            gap-1
                                        "
                                    >

                                        <span
                                            class="
                                                font-serif
                                                text-[13px]
                                                font-black
                                                text-[#8c1d18]
                                            "

                                            x-text="
                                                entry.totalHealed
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[7px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c1d18]/75
                                            "
                                        >
                                            PV
                                        </span>

                                    </span>

                                </div>


                                <div
                                    class="
                                        mt-1
                                        flex
                                        items-center
                                        gap-1.5
                                        border-t
                                        border-[#eee6da]
                                        pt-1
                                    "
                                >

                                    <span
                                        class="
                                            h-px
                                            flex-1
                                            bg-[#eee6da]
                                        "
                                    ></span>

                                    <span
                                        class="
                                            text-[6.5px]
                                            font-bold
                                            tabular-nums
                                            text-[#a09383]
                                        "

                                        x-text="
                                            entry.time
                                        "
                                    ></span>

                                </div>

                            </div>

                        </template>


                        {{-- VAZIO --}}

                        <div
                            x-show="
                                history.length === 0
                            "

                            class="
                                rounded-xl
                                border
                                border-dashed
                                border-[#d8cdbc]
                                bg-[#f5efe6]
                                px-4
                                py-7
                                text-center
                            "
                        >

                            <div
                                class="
                                    font-serif
                                    text-lg
                                    text-[#a09383]
                                "
                            >
                                d
                            </div>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c7f70]
                                "
                            >
                                Nenhuma rolagem ainda
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[9px]
                                    text-[#a09383]
                                "
                            >
                                Role um Dado de Vida para registrar o resultado.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     CONFIGURAÇÃO
                ================================================== --}}

                <div
                    x-show="
                        modalTab === 'config'
                    "

                    x-cloak

                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-4
                    "
                >

                    <div class="mb-3">

                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c7f70]
                            "
                        >
                            Quantidade
                        </p>

                        <p
                            class="
                                mt-0.5
                                text-[9px]
                                text-[#a09383]
                            "
                        >
                            Configure quantos dados o personagem possui.
                        </p>

                    </div>


                    {{-- DADOS EXISTENTES --}}

                    <div class="space-y-2">

                        <template
                            x-for="
                                (die, index) in dice
                            "

                            :key="
                                `${die.die}-${index}`
                            "
                        >

                            <div
                                class="
                                    rounded-xl
                                    border
                                    border-[#e2d7c5]
                                    bg-[#f5efe6]
                                    p-2
                                "
                            >

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-2
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            h-8
                                            w-10
                                            items-center
                                            justify-center

                                            rounded-lg

                                            bg-[#faf7f2]

                                            font-serif
                                            text-xs
                                            font-black
                                            uppercase
                                            text-[#53150f]
                                        "
                                    >

                                        <span
                                            x-text="
                                                die.die
                                            "
                                        ></span>

                                    </div>


                                    <div
                                        class="
                                            flex
                                            items-center
                                            gap-1.5
                                        "
                                    >

                                        <label
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c7f70]
                                            "
                                        >
                                            Máx.
                                        </label>


                                        <input
                                            type="number"
                                            min="0"

                                            :value="
                                                die.maximum
                                            "

                                            @input="
                                                updateDieMax(
                                                    die,
                                                    $event.target.value
                                                )
                                            "

                                            class="
                                                w-12
                                                rounded-lg
                                                border
                                                border-[#d8cdbc]
                                                bg-[#faf7f2]
                                                px-1
                                                py-1
                                                text-center
                                                text-xs
                                                font-bold
                                                text-[#3c120a]
                                                outline-none
                                                focus:border-[#6b1d14]
                                            "
                                        >


                                        <span
                                            class="
                                                text-[#b5a898]
                                            "
                                        >
                                            /
                                        </span>


                                        <label
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#8c7f70]
                                            "
                                        >
                                            Atual
                                        </label>


                                        <input
                                            type="number"
                                            min="0"

                                            :max="
                                                die.maximum
                                            "

                                            x-model.number="
                                                die.current
                                            "

                                            @change="
                                                die.current = Math.max(
                                                    0,
                                                    Math.min(
                                                        parseInt(die.maximum) || 0,
                                                        parseInt(die.current) || 0
                                                    )
                                                )
                                            "

                                            class="
                                                w-12
                                                rounded-lg
                                                border
                                                border-[#d8cdbc]
                                                bg-[#faf7f2]
                                                px-1
                                                py-1
                                                text-center
                                                text-xs
                                                font-bold
                                                text-[#3c120a]
                                                outline-none
                                                focus:border-[#6b1d14]
                                            "
                                        >

                                    </div>


                                    <button
                                        type="button"

                                        @click="
                                            removeDie(index)
                                        "

                                        class="
                                            flex
                                            h-7
                                            w-7
                                            items-center
                                            justify-center

                                            rounded-lg

                                            text-[#a09383]

                                            transition

                                            hover:bg-red-50
                                            hover:text-red-600
                                        "

                                        title="Remover"
                                    >
                                        ×
                                    </button>

                                </div>

                            </div>

                        </template>

                    </div>


                    {{-- ADICIONAR DADO --}}

                    <div
                        class="
                            mt-3

                            rounded-xl

                            border
                            border-[#e2d7c5]

                            bg-[#f5efe6]

                            p-2.5
                        "
                    >

                        <div
                            class="
                                mb-2
                                text-[8px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c7f70]
                            "
                        >
                            Adicionar tipo
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-6
                                gap-1
                            "
                        >

                            <template
                                x-for="
                                    type in [
                                        'd4',
                                        'd6',
                                        'd8',
                                        'd10',
                                        'd12',
                                        'd20'
                                    ]
                                "

                                :key="
                                    type
                                "
                            >

                                <button
                                    type="button"

                                    @click="
                                        addDie(type)
                                    "

                                    class="
                                        rounded-lg

                                        border
                                        border-[#d8cdbc]

                                        bg-[#faf7f2]

                                        py-1.5

                                        font-serif
                                        text-[9px]
                                        font-bold
                                        text-[#4a1813]

                                        transition

                                        hover:border-[#bfae98]
                                        hover:bg-white

                                        active:scale-95
                                    "

                                    x-text="
                                        '+' + type
                                    "
                                ></button>

                            </template>

                        </div>

                    </div>


                    {{-- RESUMO --}}

                    <div
                        class="
                            mt-3

                            rounded-xl

                            border
                            border-[#d8d0c3]

                            bg-[#efe9dc]/70

                            px-3
                            py-2
                        "
                    >

                        <div
                            class="
                                flex
                                items-center
                                justify-between
                            "
                        >

                            <span
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c7f70]
                                "
                            >
                                Disponíveis
                            </span>


                            <span
                                class="
                                    font-serif
                                    text-sm
                                    font-black
                                    text-[#53150f]
                                "

                                x-text="
                                    currentSummary
                                "
                            ></span>

                        </div>


                        <div
                            class="
                                mt-1
                                text-[8px]
                                text-[#a09383]
                            "
                        >
                            Máximo:

                            <span
                                class="
                                    font-bold
                                    text-[#8c6239]
                                "

                                x-text="
                                    maximumSummary
                                "
                            ></span>

                        </div>

                    </div>


                    {{-- AÇÕES --}}

                    <div
                        class="
                            mt-4
                            flex
                            justify-end
                            gap-2

                            border-t
                            border-[#e8dfd1]

                            pt-3
                        "
                    >

                        <button
                            type="button"

                            @click="
                                closeModal()
                            "

                            class="
                                rounded-lg
                                px-3
                                py-2

                                text-[9px]
                                font-bold
                                text-[#8c7f70]

                                transition

                                hover:text-[#53150f]
                            "
                        >
                            Cancelar
                        </button>


                        <button
                            type="button"

                            @click="
                                saveConfig()
                            "

                            :disabled="
                                saving
                            "

                            class="
                                rounded-lg

                                border
                                border-[#d8cdbc]

                                bg-[#f2ebe1]

                                px-4
                                py-2

                                font-serif
                                text-[10px]
                                font-bold
                                text-[#4a1813]

                                transition

                                hover:bg-[#e8dfd1]

                                disabled:opacity-50
                            "
                        >

                            <span
                                x-show="
                                    !saving
                                "
                            >
                                Salvar
                            </span>

                            <span
                                x-show="
                                    saving
                                "

                                x-cloak
                            >
                                Salvando...
                            </span>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </template>

</div>