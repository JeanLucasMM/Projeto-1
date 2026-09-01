@props([
    'character',
])

@php
    $combat = $character->combat;

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE DANO PADRÃO
    |--------------------------------------------------------------------------
    |
    | A chave é o valor salvo no banco.
    | O label é apenas a apresentação em português.
    |
    */

    $damageTypes = [
        ['value' => 'acid', 'label' => 'Ácido'],
        ['value' => 'bludgeoning', 'label' => 'Contundente'],
        ['value' => 'cold', 'label' => 'Frio'],
        ['value' => 'fire', 'label' => 'Fogo'],
        ['value' => 'force', 'label' => 'Força'],
        ['value' => 'lightning', 'label' => 'Elétrico'],
        ['value' => 'necrotic', 'label' => 'Necrótico'],
        ['value' => 'piercing', 'label' => 'Perfurante'],
        ['value' => 'poison', 'label' => 'Veneno'],
        ['value' => 'psychic', 'label' => 'Psíquico'],
        ['value' => 'radiant', 'label' => 'Radiante'],
        ['value' => 'slashing', 'label' => 'Cortante'],
        ['value' => 'thunder', 'label' => 'Trovejante'],
    ];

    $damageTypeLabels = collect($damageTypes)
        ->pluck('label', 'value')
        ->all();

    /*
    |--------------------------------------------------------------------------
    | NORMALIZAÇÃO DO VALOR VINDO DO MODEL
    |--------------------------------------------------------------------------
    |
    | Aceita array, Collection e JSON para manter compatibilidade com
    | registros antigos ou casts diferentes no CharacterCombat.
    |
    */

    $normalizeList = static function ($value): array {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            $value = is_array($decoded)
                ? $decoded
                : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(
                fn ($entry) =>
                    trim((string) $entry)
            )
            ->filter()
            ->unique(
                fn (string $entry) =>
                    mb_strtolower($entry)
            )
            ->values()
            ->all();
    };

    $damageResistances =
        $normalizeList(
            $combat?->damage_resistances
        );

    $damageImmunities =
        $normalizeList(
            $combat?->damage_immunities
        );

    $damageVulnerabilities =
        $normalizeList(
            $combat?->damage_vulnerabilities
        );
@endphp


{{-- ================================================================
     CORREÇÃO DE SOBREPOSIÇÃO COM ATRIBUTOS

     O wrapper .character-sheet-hero-defenses pertence ao show.blade
     e ocupa toda a largura da linha para posicionar Defesas apenas
     na coluna direita. A área vazia da coluna esquerda, porém, ainda
     recebe eventos de ponteiro e bloqueia os atributos abaixo.

     Desabilitamos eventos no wrapper e os reativamos somente no
     conteúdo real de Defesas. Assim a área transparente "atravessa"
     os cliques sem alterar a geometria atual.
================================================================= --}}
@once
    @push('styles')
        <style>
            .character-sheet-hero-defenses {
                pointer-events: none;
            }

            .character-sheet-hero-defenses > * {
                pointer-events: auto;
            }
        </style>
    @endpush
@endonce


<div
    data-defenses-layout="v14-sheet"

    x-data="{
        open: false,
        saving: false,
        error: null,

        activeTab: 'resistances',

        damageTypes:
            @js($damageTypes),

        damageTypeLabels:
            @js($damageTypeLabels),

        resistances:
            @js($damageResistances),

        immunities:
            @js($damageImmunities),

        vulnerabilities:
            @js($damageVulnerabilities),

        savedResistances:
            @js($damageResistances),

        savedImmunities:
            @js($damageImmunities),

        savedVulnerabilities:
            @js($damageVulnerabilities),

        customResistance: '',
        customImmunity: '',
        customVulnerability: '',


        /*
        |--------------------------------------------------------------------------
        | LABELS / APRESENTAÇÃO
        |--------------------------------------------------------------------------
        */

        labelFor(value) {
            return (
                this.damageTypeLabels[value]
                ?? value
            );
        },

        formattedList(list) {
            if (!list.length) {
                return '—';
            }

            return list
                .map(
                    value =>
                        this.labelFor(value)
                )
                .join(' · ');
        },

        customValues(list) {
            return list.filter(
                value =>
                    !Object.prototype.hasOwnProperty.call(
                        this.damageTypeLabels,
                        value
                    )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | EDITOR
        |--------------------------------------------------------------------------
        */

        openEditor() {
            this.resistances =
                [...this.savedResistances];

            this.immunities =
                [...this.savedImmunities];

            this.vulnerabilities =
                [...this.savedVulnerabilities];

            this.customResistance = '';
            this.customImmunity = '';
            this.customVulnerability = '';

            this.error = null;
            this.activeTab = 'resistances';
            this.open = true;
        },

        cancelEditor() {
            if (this.saving) {
                return;
            }

            this.resistances =
                [...this.savedResistances];

            this.immunities =
                [...this.savedImmunities];

            this.vulnerabilities =
                [...this.savedVulnerabilities];

            this.customResistance = '';
            this.customImmunity = '';
            this.customVulnerability = '';

            this.error = null;
            this.activeTab = 'resistances';
            this.open = false;
        },

        toggle(list, value) {
            const index =
                list.indexOf(value);

            if (index === -1) {
                list.push(value);
                return;
            }

            list.splice(
                index,
                1
            );
        },

        addCustom(list, property) {
            const value =
                String(this[property] ?? '')
                    .trim();

            if (!value) {
                return;
            }

            const exists =
                list.some(
                    item =>
                        String(item)
                            .toLocaleLowerCase() ===
                        value.toLocaleLowerCase()
                );

            if (!exists) {
                list.push(value);
            }

            this[property] = '';
        },

        removeValue(list, value) {
            const index =
                list.indexOf(value);

            if (index !== -1) {
                list.splice(
                    index,
                    1
                );
            }
        },


        /*
        |--------------------------------------------------------------------------
        | PERSISTÊNCIA
        |--------------------------------------------------------------------------
        */

        async save() {
            if (this.saving) {
                return;
            }

            this.saving = true;
            this.error = null;

            const formData =
                new FormData();

            formData.append(
                '_token',
                '{{ csrf_token() }}'
            );

            formData.append(
                '_method',
                'PATCH'
            );

            formData.append(
                'damage_resistances',
                JSON.stringify(
                    this.resistances
                )
            );

            formData.append(
                'damage_immunities',
                JSON.stringify(
                    this.immunities
                )
            );

            formData.append(
                'damage_vulnerabilities',
                JSON.stringify(
                    this.vulnerabilities
                )
            );

            try {
                const response =
                    await fetch(
                        '{{ route('characters.combat.update', $character) }}',
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },

                            body:
                                formData,
                        }
                    );

                let data = null;

                try {
                    data =
                        await response.json();
                } catch (error) {
                    data = null;
                }

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível salvar as defesas.'
                    );
                }

                const combat =
                    data?.combat ?? {};

                this.resistances =
                    Array.isArray(
                        combat.damage_resistances
                    )
                        ? [...combat.damage_resistances]
                        : [...this.resistances];

                this.immunities =
                    Array.isArray(
                        combat.damage_immunities
                    )
                        ? [...combat.damage_immunities]
                        : [...this.immunities];

                this.vulnerabilities =
                    Array.isArray(
                        combat.damage_vulnerabilities
                    )
                        ? [...combat.damage_vulnerabilities]
                        : [...this.vulnerabilities];

                this.savedResistances =
                    [...this.resistances];

                this.savedImmunities =
                    [...this.immunities];

                this.savedVulnerabilities =
                    [...this.vulnerabilities];

                this.open = false;

                window.dispatchEvent(
                    new CustomEvent(
                        'character-defenses-updated',
                        {
                            detail: {
                                combat: combat,
                            }
                        }
                    )
                );

            } catch (error) {
                console.error(
                    'Erro ao salvar defesas:',
                    error
                );

                this.error =
                    error?.message
                    ?? 'Não foi possível salvar as defesas.';

            } finally {
                this.saving = false;
            }
        },
    }"

    class="
        relative
        min-w-0
        w-full
    "
>

    {{-- ================================================================
         VISUALIZAÇÃO NA FICHA — V21 UNIFICADA

         A geometria agora segue diretamente Ataques/Habilidades:
         - mesmo recuo externo;
         - conteúdo central em 820px;
         - cabeçalho com 40px;
         - título 17px;
         - filete no mesmo eixo;
         - quadro interno com borda, faixa bege e papel claro.
    ================================================================= --}}

    <section
        class="
            relative
            min-w-0
            overflow-hidden

            md:h-[108px]

            rounded-xl
            border
            border-[#b08c62]/35

            bg-[linear-gradient(180deg,rgba(255,253,248,.965)_0%,rgba(248,242,233,.94)_100%)]

            px-[17px]
            pb-[10px]
            pt-[15px]

            shadow-[inset_0_1px_0_rgba(255,255,255,.78),0_3px_10px_rgba(83,21,15,.040)]
        "
    >
        <div
            class="
                mx-auto
                w-full
                max-w-[820px]
            "
        >
            {{-- ============================================================
                 CABEÇALHO — MESMA GEOMETRIA DE ATAQUES
            ============================================================= --}}

            <div
                class="
                    flex
                    min-h-[40px]
                    min-w-0
                    items-center

                    border-b
                    border-[#a8845b]/32

                    px-[5px]
                    pb-[8px]
                    pt-[1px]
                "
            >
                <button
                    type="button"

                    @click="openEditor()"

                    class="
                        inline-flex
                        min-w-0
                        items-center

                        rounded-lg

                        px-[5px]
                        py-1

                        font-serif
                        text-[17px]
                        font-black
                        leading-none

                        text-[#53150f]

                        transition

                        hover:bg-[#fffdf8]/55
                        hover:text-[#6b1d14]

                        focus:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#8c6239]/20
                    "

                    title="Editar defesas"
                >
                    Defesas
                </button>
            </div>


            {{-- ============================================================
                 QUADRO DE DEFESAS

                 Em vez de três linhas soltas, o resumo passa a se comportar
                 como a tabela de Ataques / grade de Habilidades.
            ============================================================= --}}

            <div
                class="
                    mt-[2px]

                    grid
                    min-w-0

                    grid-cols-1
                    overflow-hidden

                    rounded-lg
                    border
                    border-[#b08c62]/35

                    bg-[#fbf8f1]

                    shadow-[inset_0_1px_0_rgba(255,255,255,.70)]

                    sm:grid-cols-3
                "
            >
                {{-- RESISTÊNCIAS --}}

                <div
                    class="
                        min-w-0

                        border-b
                        border-[#b08c62]/25

                        sm:border-b-0
                        sm:border-r
                    "
                >
                    <div
                        class="
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Resistências
                    </div>

                    <div
                        class="
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#53150f]
                        "
                    >
                        <span
                            class="
                                block
                                min-w-0
                                truncate
                            "

                            x-text="formattedList(resistances)"
                            :title="formattedList(resistances)"
                        ></span>
                    </div>
                </div>


                {{-- IMUNIDADES --}}

                <div
                    class="
                        min-w-0

                        border-b
                        border-[#b08c62]/25

                        sm:border-b-0
                        sm:border-r
                    "
                >
                    <div
                        class="
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Imunidades
                    </div>

                    <div
                        class="
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#53150f]
                        "
                    >
                        <span
                            class="
                                block
                                min-w-0
                                truncate
                            "

                            x-text="formattedList(immunities)"
                            :title="formattedList(immunities)"
                        ></span>
                    </div>
                </div>


                {{-- VULNERABILIDADES --}}

                <div class="min-w-0">
                    <div
                        class="
                            bg-[#eadbc8]

                            px-2.5
                            py-[4px]

                            text-[7px]
                            font-black
                            uppercase
                            leading-none
                            tracking-[0.12em]

                            text-[#6f472f]
                        "
                    >
                        Vulnerabilidades
                    </div>

                    <div
                        class="
                            min-w-0

                            bg-[#fffdf8]/80

                            px-2.5
                            py-[5px]

                            font-serif
                            text-[10.5px]
                            font-bold
                            leading-none

                            text-[#6b1d14]
                        "
                    >
                        <span
                            class="
                                block
                                min-w-0
                                truncate
                            "

                            x-text="formattedList(vulnerabilities)"
                            :title="formattedList(vulnerabilities)"
                        ></span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================================
         EDITOR
    ================================================================= --}}

    <template x-teleport="body">

        <div
            x-show="open"
            x-cloak

            @keydown.escape.window="cancelEditor()"

            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"

            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"

            class="
                fixed
                inset-0
                z-[180]

                flex
                items-center
                justify-center

                p-3
                sm:p-6
            "

            role="dialog"
            aria-modal="true"
            aria-label="Defesas Especiais"
        >

            {{-- BACKDROP --}}

            <div
                class="
                    absolute
                    inset-0
                    bg-black/70
                    backdrop-blur-[2px]
                "

                style="
                    background-color:
                        rgba(12, 8, 7, 0.72);
                
                "

                @click="cancelEditor()"
            ></div>


            {{-- PAINEL CENTRAL --}}

            <div
                @click.stop

                class="
                    relative
                    z-10

                    flex
                    max-h-[88vh]
                    w-full
                    max-w-[860px]
                    flex-col

                    overflow-hidden

                    rounded-2xl
                    border
                    border-[#b98e63]/70

                    bg-[#faf8f2]

                    shadow-[0_26px_80px_rgba(42,23,18,.30)]
                "
            >

                {{-- =====================================================
                     CABEÇALHO
                ====================================================== --}}

                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-4

                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3
                        sm:px-5

                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="
                                flex
                                h-10
                                w-10
                                shrink-0
                                items-center
                                justify-center

                                rounded-xl
                                border
                                border-[#cdbb9f]/75

                                bg-[#fffdf9]/70

                                text-[#6b1d14]

                                shadow-[inset_0_0_0_3px_rgba(239,233,220,.55)]
                            "
                            aria-hidden="true"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.55"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 3 5.5 5.8v5.1c0 4.4 2.5 7.6 6.5 10.1 4-2.5 6.5-5.7 6.5-10.1V5.8L12 3Z" />
                                <path d="M9 12.1 11 14l4-4.2" />
                            </svg>
                        </div>

                        <div class="min-w-0">

                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.16em]
                                    text-[#8c6239]
                                "
                            >
                                Configuração de combate
                            </p>

                            <h3
                                class="
                                    mt-0.5
                                    truncate
                                    font-serif
                                    text-[20px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                "
                            >
                                Defesas Especiais
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    leading-snug
                                    text-[#7a583f]
                                "
                            >
                                Resistências, imunidades e vulnerabilidades a dano.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"

                        @click="cancelEditor()"

                        :disabled="saving"

                        class="
                            flex
                            h-8
                            w-8
                            shrink-0
                            items-center
                            justify-center

                            rounded-lg

                            text-[#8c6239]

                            transition

                            hover:bg-[#fffdf8]/55
                            hover:text-[#53150f]

                            disabled:opacity-50
                        "

                        title="Fechar"
                        aria-label="Fechar editor de defesas"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        >
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>

                </div>


                {{-- =====================================================
                     ABAS
                ====================================================== --}}

                <div
                    class="
                        grid
                        shrink-0
                        grid-cols-3

                        border-b
                        border-[#d8c7ab]/60

                        bg-[#f4f1e8]/60

                        px-3
                        sm:px-5
                    "
                >

                    {{-- RESISTÊNCIAS --}}

                    <button
                        type="button"

                        @click="activeTab = 'resistances'"

                        class="
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'resistances'
                                ? 'border-[#6b1d14] bg-[#fffdf9]/55 text-[#53150f]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Resistências
                        </span>

                        <span
                            class="
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#e7dccb]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#6b1d14]
                            "

                            x-text="resistances.length"
                        ></span>
                    </button>


                    {{-- IMUNIDADES --}}

                    <button
                        type="button"

                        @click="activeTab = 'immunities'"

                        class="
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'immunities'
                                ? 'border-[#6b1d14] bg-[#fffdf9]/55 text-[#53150f]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Imunidades
                        </span>

                        <span
                            class="
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#e7dccb]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#6b1d14]
                            "

                            x-text="immunities.length"
                        ></span>
                    </button>


                    {{-- VULNERABILIDADES --}}

                    <button
                        type="button"

                        @click="activeTab = 'vulnerabilities'"

                        class="
                            relative

                            flex
                            min-w-0
                            items-center
                            justify-center
                            gap-2

                            border-b-2
                            px-2
                            py-2.5

                            text-[12px]
                            font-black
                            uppercase
                            tracking-[0.11em]

                            transition
                        "

                        :class="
                            activeTab === 'vulnerabilities'
                                ? 'border-[#7b2a21] bg-[#fffdf9]/55 text-[#6b1d14]'
                                : 'border-transparent text-[#8c6239] hover:bg-[#fffdf8]/50 hover:text-[#6b1d14]'
                        "
                    >
                        <span class="truncate">
                            Vulnerabilidades
                        </span>

                        <span
                            class="
                                inline-flex
                                min-w-[20px]
                                items-center
                                justify-center

                                rounded-full

                                bg-[#ead8d2]

                                px-1.5
                                py-0.5

                                font-serif
                                text-[11px]
                                font-black
                                leading-none
                                text-[#7b2a21]
                            "

                            x-text="vulnerabilities.length"
                        ></span>
                    </button>

                </div>


                {{-- =====================================================
                     CONTEÚDO
                ====================================================== --}}

                <div
                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto

                        px-4
                        py-4
                        sm:px-5
                    "
                >

                    {{-- =================================================
                         RESISTÊNCIAS
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'resistances'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano aos quais o personagem recebe dano reduzido.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'resistance-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(resistances, option.value)"

                                    class="
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        resistances.includes(option.value)
                                            ? 'border-[#6b1d14] bg-[#7a2418] text-[#fffaf2] shadow-[inset_0_0_0_1px_rgba(255,255,255,.12)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            resistances.includes(option.value)
                                                ? 'border-[#fffaf2]/55 bg-[#fffaf2]/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="resistances.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                Resistência personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customResistance"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            resistances,
                                            'customResistance'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Ex.: dano de armas não mágicas"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            resistances,
                                            'customResistance'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(resistances).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(resistances)"
                                    :key="'custom-resistance-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(resistances, value)"

                                        class="
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#53150f]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                         IMUNIDADES
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'immunities'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano que não causam dano ao personagem.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'immunity-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(immunities, option.value)"

                                    class="
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        immunities.includes(option.value)
                                            ? 'border-[#6b1d14] bg-[#6b1d14] text-[#fffaf2] shadow-[inset_0_0_0_1px_rgba(255,255,255,.12)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            immunities.includes(option.value)
                                                ? 'border-[#fffaf2]/55 bg-[#fffaf2]/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="immunities.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#8c6239]
                                "
                            >
                                Imunidade personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customImmunity"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            immunities,
                                            'customImmunity'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Adicionar imunidade personalizada"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            immunities,
                                            'customImmunity'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(immunities).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(immunities)"
                                    :key="'custom-immunity-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(immunities, value)"

                                        class="
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#53150f]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                         VULNERABILIDADES
                    ================================================== --}}

                    <section
                        x-show="activeTab === 'vulnerabilities'"
                        x-cloak
                    >

                        <div
                            class="
                                mb-3
                                flex
                                items-end
                                justify-between
                                gap-4
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#7b2a21]
                                    "
                                >
                                    Tipos de dano
                                </p>

                                <p
                                    class="
                                        mt-1
                                        max-w-[520px]
                                        text-[12px]
                                        leading-relaxed
                                        text-[#76553f]
                                    "
                                >
                                    Selecione os tipos de dano que causam dano aumentado ao personagem.
                                </p>
                            </div>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-1.5

                                sm:grid-cols-3
                                lg:grid-cols-4
                            "
                        >
                            <template
                                x-for="option in damageTypes"
                                :key="'vulnerability-' + option.value"
                            >
                                <button
                                    type="button"

                                    @click="toggle(vulnerabilities, option.value)"

                                    class="
                                        flex
                                        min-h-[36px]
                                        min-w-0
                                        items-center
                                        justify-between
                                        gap-2

                                        rounded-lg
                                        border

                                        px-2.5
                                        py-2

                                        text-left
                                        text-[11px]
                                        font-bold

                                        transition
                                    "

                                    :class="
                                        vulnerabilities.includes(option.value)
                                            ? 'border-[#7b2a21] bg-[#7b2a21] text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,.10)]'
                                            : 'border-[#d8c7ab]/80 bg-[#fffdf9] text-[#53150f] hover:border-[#c5ab88] hover:bg-[#f4f1e8]'
                                    "
                                >
                                    <span
                                        class="min-w-0 truncate"
                                        x-text="option.label"
                                    ></span>

                                    <span
                                        class="
                                            flex
                                            h-4
                                            w-4
                                            shrink-0
                                            items-center
                                            justify-center

                                            rounded-full
                                            border
                                        "

                                        :class="
                                            vulnerabilities.includes(option.value)
                                                ? 'border-white/55 bg-white/10'
                                                : 'border-[#cdbb9f] bg-white'
                                        "
                                        aria-hidden="true"
                                    >
                                        <svg
                                            x-show="vulnerabilities.includes(option.value)"
                                            x-cloak
                                            class="h-2.5 w-2.5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>


                        {{-- PERSONALIZADA --}}

                        <div
                            class="
                                mt-4

                                rounded-xl
                                border
                                border-[#d8c7ab]/60

                                bg-[#f4f1e8]/40

                                p-3
                            "
                        >
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#7b2a21]
                                "
                            >
                                Vulnerabilidade personalizada
                            </p>

                            <div
                                class="
                                    mt-2
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <input
                                    type="text"

                                    x-model="customVulnerability"

                                    @keydown.enter.prevent="
                                        addCustom(
                                            vulnerabilities,
                                            'customVulnerability'
                                        )
                                    "

                                    maxlength="160"

                                    placeholder="Ex.: Corte, impacto e perfurante de ataques não mágicos"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/85

                                        bg-[#fffdf9]

                                        px-3

                                        text-[12px]
                                        text-[#53150f]

                                        outline-none

                                        placeholder:text-[#a88769]/70

                                        focus:border-[#8c6239]
                                        focus:ring-1
                                        focus:ring-[#8c6239]/15
                                    "
                                >

                                <button
                                    type="button"

                                    @click="
                                        addCustom(
                                            vulnerabilities,
                                            'customVulnerability'
                                        )
                                    "

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#eadbc8]

                                        px-3

                                        text-[12px]
                                        font-black
                                        uppercase
                                        tracking-wide
                                        text-[#6b1d14]

                                        transition

                                        hover:bg-[#ecdcc8]
                                    "
                                >
                                    Adicionar
                                </button>
                            </div>


                            <div
                                x-show="customValues(vulnerabilities).length"
                                x-cloak

                                class="
                                    mt-2.5
                                    flex
                                    flex-wrap
                                    gap-1.5
                                "
                            >
                                <template
                                    x-for="value in customValues(vulnerabilities)"
                                    :key="'custom-vulnerability-' + value"
                                >
                                    <button
                                        type="button"

                                        @click="removeValue(vulnerabilities, value)"

                                        class="
                                            inline-flex
                                            max-w-full
                                            items-center
                                            gap-1.5

                                            rounded-full
                                            border
                                            border-[#d8c7ab]/80

                                            bg-[#fffdf9]

                                            px-2.5
                                            py-1

                                            text-[8.5px]
                                            font-bold
                                            text-[#6b1d14]

                                            transition

                                            hover:border-red-300
                                            hover:bg-red-50
                                            hover:text-red-700
                                        "

                                        title="Remover"
                                    >
                                        <span
                                            class="truncate"
                                            x-text="value"
                                        ></span>

                                        <span
                                            class="
                                                shrink-0
                                                text-[11px]
                                                leading-none
                                            "
                                            aria-hidden="true"
                                        >
                                            ×
                                        </span>
                                    </button>
                                </template>
                            </div>

                        </div>

                    </section>


                    {{-- ERRO --}}

                    <div
                        x-show="error"
                        x-cloak

                        class="
                            mt-4

                            rounded-lg
                            border
                            border-red-200

                            bg-red-50

                            px-3
                            py-2.5

                            text-[11px]
                            font-bold
                            text-red-700
                        "

                        x-text="error"
                    ></div>

                </div>


                {{-- =====================================================
                     AÇÕES
                ====================================================== --}}

                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3

                        border-t
                        border-[#d8c7ab]/60

                        bg-[#f4f1e8]/70

                        px-4
                        py-3
                        sm:px-5
                    "
                >

 


                    {{-- BOTÕES --}}

                    <div
                        class="
                            ml-auto
                            flex
                            items-center
                            justify-end
                            gap-2
                        "
                    >




                        <button
                            type="button"

                            @click="save()"

                            :disabled="saving"

                            class="
                                min-w-[124px]

                                rounded-lg

                                bg-[#6b1d14]

                                px-5
                                py-2.5

                                text-[12px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#fffaf2]

                                shadow-[0_1px_2px_rgba(83,21,15,.14)]

                                transition

                                hover:bg-[#53150f]

                                disabled:cursor-wait
                                disabled:opacity-60
                            "
                        >
                            <span x-show="!saving">
                                Salvar alterações
                            </span>

                            <span
                                x-show="saving"
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