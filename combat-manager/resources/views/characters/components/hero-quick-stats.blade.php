@props([
    'character',
    'proficiencyBonus' => 2,
    'exhaustionLevel' => 0,
])

@php
    $combat = $character->combat;
    $abilities = $character->abilities;

    $strengthScore =
        (int) (
            $abilities?->strength
            ?? 10
        );

    $dexterityScore =
        (int) (
            $abilities?->dexterity
            ?? 10
        );

    $wisdomScore =
        (int) (
            $abilities?->wisdom
            ?? 10
        );

    $strengthModifier =
        (int) floor(
            ($strengthScore - 10) / 2
        );

    $dexterityModifier =
        (int) floor(
            ($dexterityScore - 10) / 2
        );

    $wisdomModifier =
        (int) floor(
            ($wisdomScore - 10) / 2
        );

    /*
    |--------------------------------------------------------------------------
    | OVERRIDES / QUICK STATS
    |--------------------------------------------------------------------------
    */

    $overrides =
        $combat?->overrides
        ?? [];

    if (is_string($overrides)) {
        $decodedOverrides =
            json_decode(
                $overrides,
                true
            );

        $overrides =
            is_array($decodedOverrides)
                ? $decodedOverrides
                : [];
    }

    if (!is_array($overrides)) {
        $overrides = [];
    }

    $quickStats =
        data_get(
            $overrides,
            'quick_stats',
            []
        );

    if (!is_array($quickStats)) {
        $quickStats = [];
    }

    /*
    |--------------------------------------------------------------------------
    | INICIATIVA
    |--------------------------------------------------------------------------
    */

    $initiativeConfig =
        $quickStats['initiative']
        ?? [];

    $storedInitiativeMode =
        $initiativeConfig['mode']
        ?? 'none';

    $initiativeMode =
        in_array(
            $storedInitiativeMode,
            [
                'none',
                'proficient',
                'expertise',
            ],
            true
        )
            ? $storedInitiativeMode
            : 'none';

    /*
    | Se o personagem já tinha initiative_bonus salvo antes deste componente,
    | tentamos preservar um valor customizado diferente de zero.
    */

    $legacyInitiative =
        (int) (
            $combat?->initiative_bonus
            ?? 0
        );

    $initiativeBonus =
        array_key_exists(
            'bonus',
            $initiativeConfig
        )
            ? (int) $initiativeConfig['bonus']
            : (
                $legacyInitiative !== 0
                    ? (
                        $legacyInitiative
                        - $dexterityModifier
                    )
                    : 0
            );

    $initiativeCombatValue =
        array_key_exists(
            'combat_value',
            $initiativeConfig
        )
        &&
        $initiativeConfig['combat_value'] !== null
        &&
        $initiativeConfig['combat_value'] !== ''
            ? (int) $initiativeConfig['combat_value']
            : null;

    /*
    |--------------------------------------------------------------------------
    | DESLOCAMENTO / SALTOS
    |--------------------------------------------------------------------------
    */

    $movementConfig =
        $quickStats['movement']
        ?? [];

    $storedSpeed =
        max(
            0,
            (int) (
                $combat?->speed
                ?? 30
            )
        );

    $movementBase =
        max(
            0,
            (int) (
                $movementConfig['base']
                ?? (
                    $storedSpeed > 0
                        ? $storedSpeed
                        : 30
                )
            )
        );

    $movementBonus =
        (int) (
            $movementConfig['bonus']
            ?? 0
        );

    $jumpEnabled =
        (bool) (
            $movementConfig['jump_enabled']
            ?? true
        );

    $jumpHorizontalBonus =
        (int) (
            $movementConfig[
                'jump_horizontal_bonus'
            ]
            ?? 0
        );

    $jumpVerticalBonus =
        (int) (
            $movementConfig[
                'jump_vertical_bonus'
            ]
            ?? 0
        );

    /*
    |--------------------------------------------------------------------------
    | PERCEPÇÃO PASSIVA
    |--------------------------------------------------------------------------
    |
    | Regra:
    |
    | 10 + modificador de Percepção + bônus adicional.
    |
    | Procuramos uma perícia "Perception / Percepção" caso o modelo possua
    | essa relação. Se não houver, o modificador de Percepção é SAB.
    |
    */

    $characterSkills =
        collect(
            $character->skills
            ?? []
        );

    $perceptionSkill =
        $characterSkills->first(
            function ($skill) {
                $candidates = [
                    data_get(
                        $skill,
                        'key'
                    ),

                    data_get(
                        $skill,
                        'slug'
                    ),

                    data_get(
                        $skill,
                        'name'
                    ),

                    data_get(
                        $skill,
                        'skill'
                    ),

                    data_get(
                        $skill,
                        'skill.key'
                    ),

                    data_get(
                        $skill,
                        'skill.slug'
                    ),

                    data_get(
                        $skill,
                        'skill.name'
                    ),
                ];

                foreach ($candidates as $candidate) {
                    $normalized =
                        mb_strtolower(
                            trim(
                                (string) $candidate
                            )
                        );

                    if (
                        in_array(
                            $normalized,
                            [
                                'perception',
                                'percepção',
                                'percepcao',
                            ],
                            true
                        )
                    ) {
                        return true;
                    }
                }

                return false;
            }
        );

    $directPerceptionModifier =
        data_get(
            $perceptionSkill,
            'modifier'
        )
        ?? data_get(
            $perceptionSkill,
            'total_modifier'
        )
        ?? data_get(
            $perceptionSkill,
            'calculated_modifier'
        );

    if (
        $directPerceptionModifier !== null
        &&
        is_numeric(
            $directPerceptionModifier
        )
    ) {
        $perceptionSkillModifier =
            (int) $directPerceptionModifier;
    } else {
        $perceptionProficient =
            (bool) (
                data_get(
                    $perceptionSkill,
                    'proficient'
                )
                ?? data_get(
                    $perceptionSkill,
                    'enabled'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.proficient'
                )
                ?? false
            );

        $perceptionExpertise =
            (bool) (
                data_get(
                    $perceptionSkill,
                    'expertise'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.expertise'
                )
                ?? false
            );

        $perceptionExtraBonus =
            (int) (
                data_get(
                    $perceptionSkill,
                    'bonus'
                )
                ?? data_get(
                    $perceptionSkill,
                    'extra_bonus'
                )
                ?? data_get(
                    $perceptionSkill,
                    'pivot.bonus'
                )
                ?? 0
            );

        $perceptionSkillModifier =
            $wisdomModifier
            +
            (
                $perceptionExpertise
                    ? (
                        (int) $proficiencyBonus
                        * 2
                    )
                    : (
                        $perceptionProficient
                            ? (int) $proficiencyBonus
                            : 0
                    )
            )
            +
            $perceptionExtraBonus;
    }

    $perceptionConfig =
        $quickStats['passive_perception']
        ?? [];

    $passivePerceptionBonus =
        (int) (
            $perceptionConfig['bonus']
            ?? 0
        );
@endphp


@once
    <script>
        document.addEventListener(
            'alpine:init',
            () => {
                Alpine.data(
                    'heroQuickStats',
                    (
                        proficiencyBonus,
                        dexterityModifier,
                        strengthScore,
                        strengthModifier,
                        perceptionSkillModifier,
                        initialExhaustionLevel,
                        initialConfig,
                        persistCombatCallback,
                        rollUrl
                    ) => ({
                        proficiencyBonus:
                            parseInt(
                                proficiencyBonus
                            ) || 0,

                        dexterityModifier:
                            parseInt(
                                dexterityModifier
                            ) || 0,

                        strengthScore:
                            parseInt(
                                strengthScore
                            ) || 0,

                        strengthModifier:
                            parseInt(
                                strengthModifier
                            ) || 0,

                        perceptionSkillModifier:
                            parseInt(
                                perceptionSkillModifier
                            ) || 0,

                        exhaustionLevel:
                            Math.min(
                                6,
                                Math.max(
                                    0,
                                    parseInt(
                                        initialExhaustionLevel
                                    ) || 0
                                )
                            ),

                        persistCombatCallback,
                        rollUrl,

                        initiativeOpen: false,
                        movementOpen: false,
                        perceptionOpen: false,

                        saving: false,
                        rollingInitiative: false,

                        initiativeMode:
                            initialConfig?.initiative?.mode
                            ?? 'none',

                        initiativeBonus:
                            parseInt(
                                initialConfig?.initiative?.bonus
                            ) || 0,

                        combatInitiative:
                            initialConfig?.initiative?.combat_value === null
                            ||
                            initialConfig?.initiative?.combat_value === undefined
                            ||
                            initialConfig?.initiative?.combat_value === ''
                                ? null
                                : parseInt(
                                    initialConfig
                                        .initiative
                                        .combat_value
                                ),

                        lastInitiativeRoll: null,

                        movementBase:
                            Math.max(
                                0,
                                parseInt(
                                    initialConfig?.movement?.base
                                ) || 30
                            ),

                        movementBonus:
                            parseInt(
                                initialConfig?.movement?.bonus
                            ) || 0,

                        jumpEnabled:
                            initialConfig
                                ?.movement
                                ?.jump_enabled
                                ?? true,

                        jumpHorizontalBonus:
                            parseInt(
                                initialConfig
                                    ?.movement
                                    ?.jump_horizontal_bonus
                            ) || 0,

                        jumpVerticalBonus:
                            parseInt(
                                initialConfig
                                    ?.movement
                                    ?.jump_vertical_bonus
                            ) || 0,

                        passivePerceptionBonus:
                            parseInt(
                                initialConfig
                                    ?.passive_perception
                                    ?.bonus
                            ) || 0,

                        /*
                        |--------------------------------------------------------------------------
                        | FUTURA INTEGRAÇÃO COM O COMBATE
                        |--------------------------------------------------------------------------
                        |
                        | Nenhuma ligação é feita aqui ainda.
                        |
                        | O componente apenas:
                        |
                        | 1. emite character-initiative-updated;
                        | 2. aceita character-turn-state.
                        |
                        */

                        isCurrentTurn: false,

                        get initiativeProficiencyBonus() {
                            if (
                                this.initiativeMode ===
                                'expertise'
                            ) {
                                return (
                                    this.proficiencyBonus
                                    * 2
                                );
                            }

                            if (
                                this.initiativeMode ===
                                'proficient'
                            ) {
                                return (
                                    this.proficiencyBonus
                                );
                            }

                            return 0;
                        },

                        get initiativeModifier() {
                            return (
                                this.dexterityModifier
                                +
                                this.initiativeProficiencyBonus
                                +
                                (
                                    parseInt(
                                        this.initiativeBonus
                                    ) || 0
                                )
                            );
                        },

                        get initiativeModifierLabel() {
                            return (
                                this.initiativeModifier >= 0
                                    ? '+'
                                    : ''
                            ) + this.initiativeModifier;
                        },

                        /*
                        |--------------------------------------------------------------------------
                        | VALOR EXIBIDO NO HEADER
                        |--------------------------------------------------------------------------
                        |
                        | O Header mostra sempre o modificador REAL da iniciativa.
                        | O resultado rolado/manual de combate fica apenas no modal.
                        |
                        */

                        get initiativeDisplay() {
                            return this.initiativeModifierLabel;
                        },


                        get exhaustionRollPenalty() {
                            return Math.max(
                                0,
                                this.exhaustionLevel * 2
                            );
                        },


                        get exhaustionSpeedPenalty() {
                            return Math.max(
                                0,
                                this.exhaustionLevel * 5
                            );
                        },


                        syncExhaustion(payload) {
                            const level =
                                payload?.level
                                ?? 0;

                            this.exhaustionLevel =
                                Math.min(
                                    6,
                                    Math.max(
                                        0,
                                        parseInt(level) || 0
                                    )
                                );
                        },


                        get movementTotal() {
                            return Math.max(
                                0,
                                (
                                    parseInt(
                                        this.movementBase
                                    ) || 0
                                )
                                +
                                (
                                    parseInt(
                                        this.movementBonus
                                    ) || 0
                                )
                                -
                                this.exhaustionSpeedPenalty
                            );
                        },

                        /*
                        |--------------------------------------------------------------------------
                        | SALTOS
                        |--------------------------------------------------------------------------
                        |
                        | Com 10 ft de corrida:
                        |
                        | Horizontal = valor de FOR.
                        | Vertical   = 3 + modificador de FOR.
                        |
                        | Sem corrida, a distância é reduzida pela metade.
                        |
                        */

                        get horizontalJump() {
                            return Math.max(
                                0,
                                this.strengthScore
                                +
                                (
                                    parseInt(
                                        this.jumpHorizontalBonus
                                    ) || 0
                                )
                            );
                        },

                        get verticalJump() {
                            return Math.max(
                                0,
                                3
                                +
                                this.strengthModifier
                                +
                                (
                                    parseInt(
                                        this.jumpVerticalBonus
                                    ) || 0
                                )
                            );
                        },

                        get passivePerception() {
                            return Math.max(
                                0,
                                10
                                +
                                this.perceptionSkillModifier
                                +
                                (
                                    parseInt(
                                        this.passivePerceptionBonus
                                    ) || 0
                                )
                            );
                        },

                        get quickStatsPayload() {
                            return {
                                initiative: {
                                    mode:
                                        [
                                            'none',
                                            'proficient',
                                            'expertise',
                                        ].includes(
                                            this.initiativeMode
                                        )
                                            ? this.initiativeMode
                                            : 'none',

                                    bonus:
                                        parseInt(
                                            this.initiativeBonus
                                        ) || 0,

                                    combat_value:
                                        this.combatInitiative === null
                                        ||
                                        this.combatInitiative === ''
                                            ? null
                                            : (
                                                parseInt(
                                                    this.combatInitiative
                                                ) || 0
                                            ),
                                },

                                movement: {
                                    base:
                                        Math.max(
                                            0,
                                            parseInt(
                                                this.movementBase
                                            ) || 0
                                        ),

                                    bonus:
                                        parseInt(
                                            this.movementBonus
                                        ) || 0,

                                    jump_enabled:
                                        !!this.jumpEnabled,

                                    jump_horizontal_bonus:
                                        parseInt(
                                            this.jumpHorizontalBonus
                                        ) || 0,

                                    jump_vertical_bonus:
                                        parseInt(
                                            this.jumpVerticalBonus
                                        ) || 0,
                                },

                                passive_perception: {
                                    bonus:
                                        parseInt(
                                            this.passivePerceptionBonus
                                        ) || 0,
                                },
                            };
                        },

                        closeAll() {
                            this.initiativeOpen = false;
                            this.movementOpen = false;
                            this.perceptionOpen = false;
                        },

                        syncTurnState(payload) {
                            if (!payload) {
                                return;
                            }

                            this.isCurrentTurn =
                                !!payload.isCurrentTurn;
                        },

                        emitInitiativeChange(
                            source = 'manual'
                        ) {
                            window.dispatchEvent(
                                new CustomEvent(
                                    'character-initiative-updated',
                                    {
                                        detail: {
                                            value:
                                                this.combatInitiative,

                                            modifier:
                                                this.initiativeModifier,

                                            source,
                                        },
                                    }
                                )
                            );
                        },

                        async persist(
                            closeAfterSave = true
                        ) {
                            if (
                                this.saving
                                ||
                                typeof this.persistCombatCallback !==
                                    'function'
                            ) {
                                return false;
                            }

                            this.saving = true;

                            try {
                                const response =
                                    await this.persistCombatCallback({
                                        speed:
                                            this.movementTotal,

                                        initiative_bonus:
                                            this.initiativeModifier,

                                        overrides:
                                            JSON.stringify({
                                                quick_stats:
                                                    this.quickStatsPayload,
                                            }),
                                    });

                                if (!response) {
                                    throw new Error(
                                        'Não foi possível salvar os dados rápidos.'
                                    );
                                }

                                if (closeAfterSave) {
                                    this.closeAll();
                                }

                                return true;

                            } catch (error) {
                                console.error(
                                    'Erro ao salvar dados rápidos:',
                                    error
                                );

                                return false;

                            } finally {
                                this.saving = false;
                            }
                        },

                        async rollInitiative() {
                            if (
                                this.rollingInitiative
                                ||
                                this.saving
                            ) {
                                return;
                            }

                            this.rollingInitiative =
                                true;

                            let roll =
                                Math.floor(
                                    Math.random() * 20
                                ) + 1;

                            try {
                                const response =
                                    await fetch(
                                        this.rollUrl,
                                        {
                                            method: 'POST',

                                            headers: {
                                                'Content-Type':
                                                    'application/json',

                                                'Accept':
                                                    'application/json',

                                                'X-CSRF-TOKEN':
                                                    document
                                                        .querySelector(
                                                            'meta[name="csrf-token"]'
                                                        )
                                                        ?.getAttribute(
                                                            'content'
                                                        )
                                                    ?? '{{ csrf_token() }}',

                                                'X-Requested-With':
                                                    'XMLHttpRequest',
                                            },

                                            body:
                                                JSON.stringify({
                                                    expression:
                                                        '1d20',
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
                                        apiValue !== null
                                        &&
                                        !Number.isNaN(
                                            parseInt(
                                                apiValue
                                            )
                                        )
                                    ) {
                                        roll =
                                            parseInt(
                                                apiValue
                                            );
                                    }
                                }

                            } catch (error) {
                                console.warn(
                                    'API de dados indisponível. Usando rolagem local.',
                                    error
                                );
                            }

                            this.lastInitiativeRoll =
                                roll;

                            this.combatInitiative =
                                roll
                                +
                                this.initiativeModifier
                                -
                                this.exhaustionRollPenalty;

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'roll'
                                );
                            }

                            this.rollingInitiative =
                                false;
                        },

                        async saveManualInitiative() {
                            if (
                                this.combatInitiative === ''
                            ) {
                                this.combatInitiative =
                                    null;
                            }

                            if (
                                this.combatInitiative !== null
                            ) {
                                const parsed =
                                    parseInt(
                                        this.combatInitiative
                                    );

                                this.combatInitiative =
                                    Number.isNaN(parsed)
                                        ? null
                                        : parsed;
                            }

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'manual'
                                );
                            }
                        },

                        async clearCombatInitiative() {
                            this.combatInitiative =
                                null;

                            this.lastInitiativeRoll =
                                null;

                            const success =
                                await this.persist(
                                    false
                                );

                            if (success) {
                                this.emitInitiativeChange(
                                    'clear'
                                );
                            }
                        },

                        resetMovementDefault() {
                            this.movementBase = 30;
                            this.movementBonus = 0;
                        },
                    })
                );
            }
        );
    </script>
@endonce


<div
    x-data="heroQuickStats(
        {{ (int) $proficiencyBonus }},
        {{ $dexterityModifier }},
        {{ $strengthScore }},
        {{ $strengthModifier }},
        {{ $perceptionSkillModifier }},
        {{ (int) $exhaustionLevel }},
        @js([
            'initiative' => [
                'mode' =>
                    $initiativeMode,

                'bonus' =>
                    $initiativeBonus,

                'combat_value' =>
                    $initiativeCombatValue,
            ],

            'movement' => [
                'base' =>
                    $movementBase,

                'bonus' =>
                    $movementBonus,

                'jump_enabled' =>
                    $jumpEnabled,

                'jump_horizontal_bonus' =>
                    $jumpHorizontalBonus,

                'jump_vertical_bonus' =>
                    $jumpVerticalBonus,
            ],

            'passive_perception' => [
                'bonus' =>
                    $passivePerceptionBonus,
            ],
        ]),
        (fields) => persistCombat(fields),
        @js(url('/api/roll'))
    )"

    @keydown.escape.window="
        closeAll()
    "

    @character-turn-state.window="
        syncTurnState(
            $event.detail
        )
    "

    @character-exhaustion-updated.window="
        syncExhaustion(
            $event.detail
        )
    "

    class="w-full"
>
    {{-- ============================================================
         BARRA COMPACTA
    ============================================================= --}}

    <div
        class="
            grid
            h-[58px]
            w-full
            grid-cols-3
            overflow-hidden
            rounded-lg
            border
            border-[#d8c7ab]/70
            bg-[#faf8f2]
        "

        :class="{
            'ring-1 ring-[#6b1d14]/20':
                isCurrentTurn
        }"
    >
        {{-- INICIATIVA --}}
        <button
            type="button"

            @click="
                initiativeOpen = true
            "

            class="
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                border-r
                border-[#d8c7ab]/55
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            title="Iniciativa"
        >
            <span
                class="
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.11em]
                    text-[#8c6239]
                "
            >
                Iniciativa
            </span>

            <span
                class="
                    mt-1
                    font-serif
                    text-[18px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    initiativeDisplay
                "
            ></span>
        </button>


        {{-- DESLOCAMENTO --}}
        <button
            type="button"

            @click="
                movementOpen = true
            "

            class="
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                border-r
                border-[#d8c7ab]/55
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            title="Deslocamento e saltos"
        >
            <span
                class="
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.09em]
                    text-[#8c6239]
                "
            >
                Deslocamento
            </span>

            <span
                class="
                    mt-0.5
                    whitespace-nowrap
                    font-serif
                    text-[17px]
                    font-black
                    leading-none
                    text-[#53150f]
                "
            >
                <span
                    x-text="
                        movementTotal
                    "
                ></span>

                <span
                    class="
                        text-[8px]
                        font-black
                        text-[#8c6239]
                    "
                >
                    ft
                </span>
            </span>

            <span
                x-show="
                    jumpEnabled
                "

                x-cloak

                class="
                    mt-1.5
                    whitespace-nowrap
                    text-[9px]
                    font-bold
                    leading-none
                    text-[#8c6239]/85
                "
            >
                <span
                    class="
                        font-black
                        text-[#6b1d14]
                    "
                >
                    Salto
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        horizontalJump + 'ft'
                    "
                ></span>

                <span class="text-[#8c6239]/70">
                    (horiz.)
                </span>

                <span class="mx-0.5 text-[#c0ad8e]">
                    ·
                </span>

                <span
                    class="
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        verticalJump + 'ft'
                    "
                ></span>

                <span class="text-[#8c6239]/70">
                    (vert.)
                </span>
            </span>
        </button>


        {{-- PERCEPÇÃO PASSIVA --}}
        <button
            type="button"

            @click="
                perceptionOpen = true
            "

            class="
                group
                flex
                min-w-0
                flex-col
                items-center
                justify-center
                px-2
                py-1
                text-center
                transition
                duration-150
                hover:bg-[#f4f1e8]/70
            "

            title="Percepção Passiva"
        >
            <span
                class="
                    whitespace-nowrap
                    text-[7px]
                    font-black
                    uppercase
                    tracking-[0.07em]
                    text-[#8c6239]
                "
            >
                Percepção Passiva
            </span>

            <span
                class="
                    mt-1
                    font-serif
                    text-[18px]
                    font-black
                    leading-none
                    text-[#53150f]
                "

                x-text="
                    passivePerception
                "
            ></span>
        </button>
    </div>


    {{-- ============================================================
         MODAL — INICIATIVA
    ============================================================= --}}

    <template x-teleport="body">
        <div
            x-show="
                initiativeOpen
            "

            x-cloak

            class="
                fixed
                inset-0
                z-[160]
                flex
                items-center
                justify-center
                p-4
            "
        >
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

                @click="
                    initiativeOpen = false
                "
            ></div>

            <div
                @click.stop

                class="
                    relative
                    z-10
                    w-full
                    max-w-sm
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3

                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >
                    <div>
                        <p
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c6239]
                            "
                        >
                            Combate
                        </p>

                        <h3
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Iniciativa
                        </h3>
                    </div>

                    <div
                        class="
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <div
                            class="
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-white
                                px-2.5
                                py-1
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "

                            x-text="
                                initiativeModifierLabel
                            "
                        ></div>

                        <button
                            type="button"

                            @click="
                                initiativeOpen = false
                            "

                            class="
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center
                                rounded-lg
                                text-[#8c6239]
                                transition
                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]
                            "
                        >
                            ×
                        </button>
                    </div>
                </div>

                <div class="space-y-4 p-4">
                    {{-- CÁLCULO --}}
                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/60
                            bg-[#f4f1e8]/60
                            p-3
                        "
                    >
                        <div
                            class="
                                grid
                                grid-cols-3
                                gap-2
                                text-center
                            "
                        >
                            <div>
                                <span
                                    class="
                                        block
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    DES
                                </span>

                                <strong
                                    class="
                                        font-serif
                                        text-base
                                        text-[#53150f]
                                    "
                                >
                                    {{ $dexterityModifier >= 0 ? '+' : '' }}{{ $dexterityModifier }}
                                </strong>
                            </div>

                            <div>
                                <span
                                    class="
                                        block
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Prof.
                                </span>

                                <strong
                                    class="
                                        font-serif
                                        text-base
                                        text-[#53150f]
                                    "

                                    x-text="
                                        (
                                            initiativeProficiencyBonus >= 0
                                                ? '+'
                                                : ''
                                        )
                                        +
                                        initiativeProficiencyBonus
                                    "
                                ></strong>
                            </div>

                            <div>
                                <span
                                    class="
                                        block
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Bônus
                                </span>

                                <strong
                                    class="
                                        font-serif
                                        text-base
                                        text-[#53150f]
                                    "

                                    x-text="
                                        (
                                            initiativeBonus >= 0
                                                ? '+'
                                                : ''
                                        )
                                        +
                                        (
                                            parseInt(
                                                initiativeBonus
                                            ) || 0
                                        )
                                    "
                                ></strong>
                            </div>
                        </div>
                    </div>


                    {{-- PROFICIÊNCIA --}}
                    <div>
                        <p
                            class="
                                mb-1.5
                                text-[11px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                            "
                        >
                            Somar proficiência
                        </p>

                        <div
                            class="
                                grid
                                grid-cols-3
                                overflow-hidden
                                rounded-lg
                                border
                                border-[#cdbb9f]/70
                                bg-white
                            "
                        >
                            <button
                                type="button"

                                @click="
                                    initiativeMode =
                                        'none'
                                "

                                class="
                                    px-2
                                    py-2
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-wide
                                    transition
                                "

                                :class="
                                    initiativeMode ===
                                    'none'
                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                "
                            >
                                Nenhuma
                            </button>

                            <button
                                type="button"

                                @click="
                                    initiativeMode =
                                        'proficient'
                                "

                                class="
                                    border-x
                                    border-[#cdbb9f]/50
                                    px-2
                                    py-2
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-wide
                                    transition
                                "

                                :class="
                                    initiativeMode ===
                                    'proficient'
                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                "
                            >
                                Proficiência
                            </button>

                            <button
                                type="button"

                                @click="
                                    initiativeMode =
                                        'expertise'
                                "

                                class="
                                    px-2
                                    py-2
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-wide
                                    transition
                                "

                                :class="
                                    initiativeMode ===
                                    'expertise'
                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                "
                            >
                                Expertise
                            </button>
                        </div>
                    </div>


                    {{-- BÔNUS --}}
                    <label class="block">
                        <span
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                            "
                        >
                            Bônus adicional
                        </span>

                        <input
                            type="number"

                            x-model.number="
                                initiativeBonus
                            "

                            class="
                                mt-1.5
                                w-full
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-white
                                px-3
                                py-2
                                font-serif
                                text-base
                                font-black
                                text-[#53150f]
                                outline-none
                                focus:border-[#6b1d14]
                            "
                        >
                    </label>


                    {{-- INICIATIVA DE COMBATE --}}
                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/70
                            bg-[#f4f1e8]/60
                            p-3
                        "
                    >
                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                gap-3
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Iniciativa do combate
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[10px]
                                        leading-4
                                        text-[#8c6239]/70
                                    "
                                >
                                    Role ou informe o resultado manualmente.
                                </p>
                            </div>

                            <input
                                type="number"

                                x-model.number="
                                    combatInitiative
                                "

                                placeholder="—"

                                class="
                                    w-20
                                    rounded-lg
                                    border
                                    border-[#cdbb9f]
                                    bg-white
                                    px-2
                                    py-2
                                    text-center
                                    font-serif
                                    text-xl
                                    font-black
                                    text-[#53150f]
                                    outline-none
                                    focus:border-[#6b1d14]
                                "
                            >
                        </div>

                        <div
                            class="
                                mt-2
                                grid
                                grid-cols-2
                                gap-2
                            "
                        >
                            <button
                                type="button"

                                @click="
                                    rollInitiative()
                                "

                                :disabled="
                                    rollingInitiative
                                    ||
                                    saving
                                "

                                class="
                                    rounded-lg
                                    bg-[#6b1d14]
                                    px-3
                                    py-2
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#f4f1e8]
                                    transition
                                    hover:bg-[#53150f]
                                    disabled:opacity-50
                                "
                            >
                                <span
                                    x-show="
                                        !rollingInitiative
                                    "
                                >
                                    Rolar iniciativa
                                </span>

                                <span
                                    x-show="
                                        rollingInitiative
                                    "

                                    x-cloak
                                >
                                    Rolando...
                                </span>
                            </button>

                            <button
                                type="button"

                                @click="
                                    saveManualInitiative()
                                "

                                :disabled="
                                    saving
                                "

                                class="
                                    rounded-lg
                                    border
                                    border-[#cdbb9f]
                                    bg-[#efe9dc]
                                    px-3
                                    py-2
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#53150f]
                                    transition
                                    hover:bg-[#fffdf8]/55
                                    disabled:opacity-50
                                "
                            >
                                Usar valor
                            </button>
                        </div>

                        <div
                            x-show="
                                lastInitiativeRoll !== null
                            "

                            x-cloak

                            class="
                                mt-2
                                rounded-lg
                                border
                                border-[#d8c7ab]/50
                                bg-white/65
                                px-2
                                py-1.5
                                text-center
                                text-[10px]
                                text-[#8c6239]
                            "
                        >
                            d20

                            <strong
                                class="
                                    text-[#53150f]
                                "

                                x-text="
                                    lastInitiativeRoll
                                "
                            ></strong>

                            +

                            modificador

                            <strong
                                class="
                                    text-[#53150f]
                                "

                                x-text="
                                    initiativeModifierLabel
                                "
                            ></strong>

                            =

                            <strong
                                class="
                                    font-serif
                                    text-[#53150f]
                                "

                                x-text="
                                    combatInitiative
                                "
                            ></strong>
                        </div>

                        <button
                            x-show="
                                combatInitiative !== null
                            "

                            x-cloak

                            type="button"

                            @click="
                                clearCombatInitiative()
                            "

                            class="
                                mt-2
                                w-full
                                text-[11px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:text-[#6b1d14]
                            "
                        >
                            Limpar iniciativa do combate
                        </button>
                    </div>


                    {{-- AÇÕES --}}
                    <div
                        class="
                            flex
                            justify-end
                            gap-2
                            border-t
                            border-[#d8c7ab]/50
                            pt-3
                        "
                    >
                        <button
                            type="button"

                            @click="
                                initiativeOpen = false
                            "

                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:bg-[#efe9dc]
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"

                            @click="
                                persist()
                            "

                            :disabled="
                                saving
                            "

                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- ============================================================
         MODAL — DESLOCAMENTO
    ============================================================= --}}

    <template x-teleport="body">
        <div
            x-show="
                movementOpen
            "

            x-cloak

            class="
                fixed
                inset-0
                z-[160]
                flex
                items-center
                justify-center
                p-4
            "
        >
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

                @click="
                    movementOpen = false
                "
            ></div>

            <div
                @click.stop

                class="
                    relative
                    z-10
                    w-full
                    max-w-sm
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3

                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >
                    <div>
                        <p
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c6239]
                            "
                        >
                            Movimento
                        </p>

                        <h3
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Deslocamento
                        </h3>
                    </div>

                    <button
                        type="button"

                        @click="
                            movementOpen = false
                        "

                        class="
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-lg
                            text-[#8c6239]
                            transition
                            hover:bg-[#fffdf8]/55
                            hover:text-[#53150f]
                        "
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-4 p-4">
                    <div
                        class="
                            grid
                            grid-cols-2
                            gap-3
                        "
                    >
                        <label class="block">
                            <span
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Padrão
                            </span>

                            <div
                                class="
                                    relative
                                    mt-1.5
                                "
                            >
                                <input
                                    type="number"
                                    min="0"

                                    x-model.number="
                                        movementBase
                                    "

                                    class="
                                        w-full
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]
                                        bg-white
                                        px-3
                                        py-2
                                        pr-8
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                        outline-none
                                        focus:border-[#6b1d14]
                                    "
                                >

                                <span
                                    class="
                                        pointer-events-none
                                        absolute
                                        right-2.5
                                        top-1/2
                                        -translate-y-1/2
                                        text-[10px]
                                        font-bold
                                        text-[#8c6239]
                                    "
                                >
                                    ft
                                </span>
                            </div>
                        </label>

                        <label class="block">
                            <span
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Bônus
                            </span>

                            <div
                                class="
                                    relative
                                    mt-1.5
                                "
                            >
                                <input
                                    type="number"

                                    x-model.number="
                                        movementBonus
                                    "

                                    class="
                                        w-full
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]
                                        bg-white
                                        px-3
                                        py-2
                                        pr-8
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                        outline-none
                                        focus:border-[#6b1d14]
                                    "
                                >

                                <span
                                    class="
                                        pointer-events-none
                                        absolute
                                        right-2.5
                                        top-1/2
                                        -translate-y-1/2
                                        text-[10px]
                                        font-bold
                                        text-[#8c6239]
                                    "
                                >
                                    ft
                                </span>
                            </div>
                        </label>
                    </div>

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            rounded-xl
                            border
                            border-[#d8c7ab]/60
                            bg-[#f4f1e8]/60
                            px-3
                            py-2
                        "
                    >
                        <div>
                            <span
                                class="
                                    block
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Deslocamento final
                            </span>

                            <button
                                type="button"

                                @click="
                                    resetMovementDefault()
                                "

                                class="
                                    mt-0.5
                                    text-[11px]
                                    font-bold
                                    text-[#8c6239]/70
                                    transition
                                    hover:text-[#6b1d14]
                                "
                            >
                                Restaurar padrão 30 ft
                            </button>
                        </div>

                        <span
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            <span
                                x-text="
                                    movementTotal
                                "
                            ></span>

                            <span
                                class="
                                    text-[11px]
                                    text-[#8c6239]
                                "
                            >
                                ft
                            </span>
                        </span>
                    </div>


                    {{-- SALTOS --}}
                    <div
                        class="
                            border-t
                            border-[#d8c7ab]/50
                            pt-3
                        "
                    >
                        <div
                            class="
                                mb-3
                                flex
                                items-center
                                justify-between
                                gap-3
                                rounded-lg
                                border
                                border-[#d8c7ab]/55
                                bg-[#f4f1e8]/55
                                px-3
                                py-2
                            "
                        >
                            <div>
                                <p
                                    class="
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Exibir Salto
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[11px]
                                        text-[#8c6239]/65
                                    "
                                >
                                    Mostra os saltos no Header.
                                </p>
                            </div>

                            <button
                                type="button"

                                @click="
                                    jumpEnabled =
                                        !jumpEnabled
                                "

                                class="
                                    relative
                                    h-5
                                    w-9
                                    shrink-0
                                    rounded-full
                                    border
                                    transition
                                "

                                :class="
                                    jumpEnabled
                                        ? 'border-[#6b1d14] bg-[#6b1d14]'
                                        : 'border-[#cdbb9f] bg-[#e8dfd1]'
                                "
                            >
                                <span
                                    class="
                                        absolute
                                        top-1/2
                                        h-3
                                        w-3
                                        -translate-y-1/2
                                        rounded-full
                                        bg-white
                                        shadow-sm
                                        transition-all
                                    "

                                    :class="
                                        jumpEnabled
                                            ? 'left-[18px]'
                                            : 'left-[3px]'
                                    "
                                ></span>
                            </button>
                        </div>

                        <div
                            x-show="
                                jumpEnabled
                            "

                            x-cloak
                        >
                            <div class="mb-2">
                            <p
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Saltos
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[10px]
                                    leading-4
                                    text-[#8c6239]/70
                                "
                            >
                                Com corrida de 10 ft: H = FOR e V = 3 + modificador de FOR.
                                Sem corrida, use metade.
                            </p>
                        </div>

                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-3
                            "
                        >
                            <label
                                class="
                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/60
                                    bg-white/70
                                    p-3
                                "
                            >
                                <span
                                    class="
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Horizontal · H
                                </span>

                                <div
                                    class="
                                        mt-1
                                        flex
                                        items-end
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-xl
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        <span
                                            x-text="
                                                horizontalJump
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[10px]
                                                text-[#8c6239]
                                            "
                                        >
                                            ft
                                        </span>
                                    </span>

                                    <div class="text-right">
                                        <span
                                            class="
                                                block
                                                text-[5.5px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                                text-[#8c6239]/70
                                            "
                                        >
                                            Bônus
                                        </span>

                                        <input
                                            type="number"

                                            x-model.number="
                                                jumpHorizontalBonus
                                            "

                                            class="
                                                mt-0.5
                                                w-14
                                                rounded-md
                                                border
                                                border-[#cdbb9f]
                                                bg-[#faf8f2]
                                                px-1
                                                py-1
                                                text-center
                                                text-[11px]
                                                font-black
                                                text-[#53150f]
                                                outline-none
                                                focus:border-[#6b1d14]
                                            "
                                        >
                                    </div>
                                </div>
                            </label>

                            <label
                                class="
                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/60
                                    bg-white/70
                                    p-3
                                "
                            >
                                <span
                                    class="
                                        text-[11px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Vertical · V
                                </span>

                                <div
                                    class="
                                        mt-1
                                        flex
                                        items-end
                                        justify-between
                                        gap-3
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-xl
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        <span
                                            x-text="
                                                verticalJump
                                            "
                                        ></span>

                                        <span
                                            class="
                                                text-[10px]
                                                text-[#8c6239]
                                            "
                                        >
                                            ft
                                        </span>
                                    </span>

                                    <div class="text-right">
                                        <span
                                            class="
                                                block
                                                text-[5.5px]
                                                font-black
                                                uppercase
                                                tracking-wider
                                                text-[#8c6239]/70
                                            "
                                        >
                                            Bônus
                                        </span>

                                        <input
                                            type="number"

                                            x-model.number="
                                                jumpVerticalBonus
                                            "

                                            class="
                                                mt-0.5
                                                w-14
                                                rounded-md
                                                border
                                                border-[#cdbb9f]
                                                bg-[#faf8f2]
                                                px-1
                                                py-1
                                                text-center
                                                text-[11px]
                                                font-black
                                                text-[#53150f]
                                                outline-none
                                                focus:border-[#6b1d14]
                                            "
                                        >
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    </div>


                    {{-- AÇÕES --}}
                    <div
                        class="
                            flex
                            justify-end
                            gap-2
                            border-t
                            border-[#d8c7ab]/50
                            pt-3
                        "
                    >
                        <button
                            type="button"

                            @click="
                                movementOpen = false
                            "

                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:bg-[#efe9dc]
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"

                            @click="
                                persist()
                            "

                            :disabled="
                                saving
                            "

                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>


    {{-- ============================================================
         MODAL — PERCEPÇÃO PASSIVA
    ============================================================= --}}

    <template x-teleport="body">
        <div
            x-show="
                perceptionOpen
            "

            x-cloak

            class="
                fixed
                inset-0
                z-[160]
                flex
                items-center
                justify-center
                p-4
            "
        >
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

                @click="
                    perceptionOpen = false
                "
            ></div>

            <div
                @click.stop

                class="
                    relative
                    z-10
                    w-full
                    max-w-sm
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                <div
                    class="
                        flex
                        items-center
                        justify-between
                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        py-3

                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                    "
                >
                    <div>
                        <p
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.2em]
                                text-[#8c6239]
                            "
                        >
                            Sentidos
                        </p>

                        <h3
                            class="
                                font-serif
                                text-xl
                                font-black
                                text-[#53150f]
                            "
                        >
                            Percepção Passiva
                        </h3>
                    </div>

                    <button
                        type="button"

                        @click="
                            perceptionOpen = false
                        "

                        class="
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-lg
                            text-[#8c6239]
                            transition
                            hover:bg-[#e8dfd1]
                            hover:text-[#53150f]
                        "
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-4 p-4">
                    <div
                        class="
                            rounded-xl
                            border
                            border-[#d8c7ab]/60
                            bg-[#f4f1e8]/60
                            p-3
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
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Base
                            </span>

                            <span
                                class="
                                    font-serif
                                    text-base
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                10
                            </span>
                        </div>

                        <div
                            class="
                                mt-1
                                flex
                                items-center
                                justify-between
                            "
                        >
                            <span
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Percepção
                            </span>

                            <span
                                class="
                                    font-serif
                                    text-base
                                    font-black
                                    text-[#53150f]
                                "

                                x-text="
                                    (
                                        perceptionSkillModifier >= 0
                                            ? '+'
                                            : ''
                                    )
                                    +
                                    perceptionSkillModifier
                                "
                            ></span>
                        </div>

                        <div
                            class="
                                mt-1
                                flex
                                items-center
                                justify-between
                            "
                        >
                            <span
                                class="
                                    text-[11px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Bônus
                            </span>

                            <span
                                class="
                                    font-serif
                                    text-base
                                    font-black
                                    text-[#53150f]
                                "

                                x-text="
                                    (
                                        passivePerceptionBonus >= 0
                                            ? '+'
                                            : ''
                                    )
                                    +
                                    (
                                        parseInt(
                                            passivePerceptionBonus
                                        ) || 0
                                    )
                                "
                            ></span>
                        </div>

                        <div
                            class="
                                mt-2
                                border-t
                                border-[#d8c7ab]/50
                                pt-2
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
                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#6b1d14]
                                    "
                                >
                                    Total
                                </span>

                                <span
                                    class="
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#53150f]
                                    "

                                    x-text="
                                        passivePerception
                                    "
                                ></span>
                            </div>
                        </div>
                    </div>

                    <label class="block">
                        <span
                            class="
                                text-[11px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                            "
                        >
                            Bônus adicional
                        </span>

                        <input
                            type="number"

                            x-model.number="
                                passivePerceptionBonus
                            "

                            class="
                                mt-1.5
                                w-full
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-white
                                px-3
                                py-2
                                font-serif
                                text-base
                                font-black
                                text-[#53150f]
                                outline-none
                                focus:border-[#6b1d14]
                            "
                        >
                    </label>

                    <p
                        class="
                            text-[10px]
                            leading-4
                            text-[#8c6239]/70
                        "
                    >
                        A conta usa 10 + o modificador atual de Percepção.
                        Se a perícia não estiver cadastrada, usa o modificador de Sabedoria.
                    </p>


                    {{-- AÇÕES --}}
                    <div
                        class="
                            flex
                            justify-end
                            gap-2
                            border-t
                            border-[#d8c7ab]/50
                            pt-3
                        "
                    >
                        <button
                            type="button"

                            @click="
                                perceptionOpen = false
                            "

                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:bg-[#efe9dc]
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"

                            @click="
                                persist()
                            "

                            :disabled="
                                saving
                            "

                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[10px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>