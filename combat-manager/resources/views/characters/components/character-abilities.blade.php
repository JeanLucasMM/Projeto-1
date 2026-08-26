@props([
    'character',
])

@php
    /*
    |--------------------------------------------------------------------------
    | DEFINIÇÕES
    |--------------------------------------------------------------------------
    */

    $abilityDefinitions = [
        'strength' => [
            'label' => 'Força',
            'short' => 'FOR',
        ],

        'intelligence' => [
            'label' => 'Inteligência',
            'short' => 'INT',
        ],

        'dexterity' => [
            'label' => 'Destreza',
            'short' => 'DES',
        ],

        'wisdom' => [
            'label' => 'Sabedoria',
            'short' => 'SAB',
        ],

        'constitution' => [
            'label' => 'Constituição',
            'short' => 'CON',
        ],

        'charisma' => [
            'label' => 'Carisma',
            'short' => 'CAR',
        ],
    ];

    /*
    | A ordem acima reproduz a leitura visual:
    |
    | FOR | INT
    | DES | SAB
    | CON | CAR
    */

    $skillDefinitions = [
        'acrobatics' => [
            'label' => 'Acrobacia',
            'ability' => 'dexterity',
        ],

        'animal_handling' => [
            'label' => 'Adestrar Animais',
            'ability' => 'wisdom',
        ],

        'arcana' => [
            'label' => 'Arcanismo',
            'ability' => 'intelligence',
        ],

        'athletics' => [
            'label' => 'Atletismo',
            'ability' => 'strength',
        ],

        'deception' => [
            'label' => 'Enganação',
            'ability' => 'charisma',
        ],

        'history' => [
            'label' => 'História',
            'ability' => 'intelligence',
        ],

        'insight' => [
            'label' => 'Intuição',
            'ability' => 'wisdom',
        ],

        'intimidation' => [
            'label' => 'Intimidação',
            'ability' => 'charisma',
        ],

        'investigation' => [
            'label' => 'Investigação',
            'ability' => 'intelligence',
        ],

        'medicine' => [
            'label' => 'Medicina',
            'ability' => 'wisdom',
        ],

        'nature' => [
            'label' => 'Natureza',
            'ability' => 'intelligence',
        ],

        'perception' => [
            'label' => 'Percepção',
            'ability' => 'wisdom',
        ],

        'performance' => [
            'label' => 'Atuação',
            'ability' => 'charisma',
        ],

        'persuasion' => [
            'label' => 'Persuasão',
            'ability' => 'charisma',
        ],

        'religion' => [
            'label' => 'Religião',
            'ability' => 'intelligence',
        ],

        'sleight_of_hand' => [
            'label' => 'Prestidigitação',
            'ability' => 'dexterity',
        ],

        'stealth' => [
            'label' => 'Furtividade',
            'ability' => 'dexterity',
        ],

        'survival' => [
            'label' => 'Sobrevivência',
            'ability' => 'wisdom',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | PROFICIÊNCIA
    |--------------------------------------------------------------------------
    */

    $level =
        max(
            1,
            (int) (
                $character->level
                ?? 1
            )
        );

    $calculatedProficiency = match (true) {
        $level >= 17 => 6,
        $level >= 13 => 5,
        $level >= 9 => 4,
        $level >= 5 => 3,
        default => 2,
    };

    $proficiencyBonus =
        (int) (
            $character->proficiency_bonus
            ?? $calculatedProficiency
        );

    /*
    |--------------------------------------------------------------------------
    | MODELOS CARREGADOS
    |--------------------------------------------------------------------------
    */

    $abilities =
        $character->abilities;

    $temporaryBonuses =
        is_array(
            $abilities?->temporary_bonuses
        )
            ? $abilities->temporary_bonuses
            : [];

    $abilityOverrides =
        is_array(
            $abilities?->overrides
        )
            ? $abilities->overrides
            : [];

    $savingThrows =
        $character
            ->savingThrows
            ->keyBy('ability');

    $skills =
        $character
            ->skills
            ->keyBy('skill');

    /*
    |--------------------------------------------------------------------------
    | ESTADO INICIAL PARA O ALPINE
    |--------------------------------------------------------------------------
    */

    $initialAbilities = [];

    foreach (
        $abilityDefinitions as
        $abilityKey => $definition
    ) {
        $baseScore =
            (int) (
                $abilities?->{$abilityKey}
                ?? 10
            );

        $temporaryBonus =
            (int) (
                $temporaryBonuses[
                    $abilityKey
                ]
                ?? 0
            );

        /*
         * O Bônus Extra representa o SCORE temporário inteiro.
         * Compatibilidade com temporary_bonuses antigo:
         * Base 12 + bônus antigo 4 => Bônus Extra 16.
         */
        $override =
            array_key_exists(
                $abilityKey,
                $abilityOverrides
            )
                ? (int) $abilityOverrides[$abilityKey]
                : (
                    $temporaryBonus !== 0
                        ? $baseScore + $temporaryBonus
                        : null
                );

        $save =
            $savingThrows->get(
                $abilityKey
            );

        $abilitySkills = [];

        foreach (
            $skillDefinitions as
            $skillKey => $skillDefinition
        ) {
            if (
                $skillDefinition['ability']
                !== $abilityKey
            ) {
                continue;
            }

            $skill =
                $skills->get(
                    $skillKey
                );

            $abilitySkills[$skillKey] = [
                'key' =>
                    $skillKey,

                'label' =>
                    $skillDefinition[
                        'label'
                    ],

                'proficient' =>
                    (bool) (
                        $skill?->proficient
                        ?? false
                    ),

                'expertise' =>
                    (bool) (
                        $skill?->expertise
                        ?? false
                    ),

                'bonus_override' =>
                    $skill?->bonus_override,

                'temporary_bonus' =>
                    (int) (
                        $skill?->temporary_bonus
                        ?? 0
                    ),
            ];
        }

        $initialAbilities[$abilityKey] = [
            'key' =>
                $abilityKey,

            'label' =>
                $definition['label'],

            'short' =>
                $definition['short'],

            'score' =>
                $baseScore,

            // Mantido apenas para compatibilidade do payload.
            'temporary_bonus' =>
                0,

            'override' =>
                $override,

            'saving_throw' => [
                'proficient' =>
                    (bool) (
                        $save?->proficient
                        ?? false
                    ),

                'bonus_override' =>
                    $save?->bonus_override,

                'temporary_bonus' =>
                    (int) (
                        $save?->temporary_bonus
                        ?? 0
                    ),
            ],

            'skills' =>
                $abilitySkills,
        ];
    }
@endphp


@once
    @push('styles')
        <style>
            /*
            |--------------------------------------------------------------------------
            | BLOCO DE ATRIBUTOS — CONTINUIDADE DA FOLHA
            |--------------------------------------------------------------------------
            */

            .character-stats-sheet {
                position: relative;
                background:
                    linear-gradient(
                        180deg,
                        #faf8f2 0%,
                        #f7f3ea 100%
                    );
            }

            .character-stats-sheet::before {
                content: "";
                position: absolute;
                inset: 3px;
                pointer-events: none;
                border-left: 1px solid rgba(216, 199, 171, .52);
                border-right: 1px solid rgba(216, 199, 171, .52);
                border-bottom: 1px solid rgba(216, 199, 171, .52);
                border-radius: 0 0 13px 13px;
            }

            .character-stat-panel {
                position: relative;
                overflow: hidden;
                align-self: flex-start;

                border: 1px solid rgba(205, 187, 159, .72);
                border-radius: 11px;

                background:
                    linear-gradient(
                        180deg,
                        rgba(255,253,249,.98) 0%,
                        rgba(250,248,242,.98) 100%
                    );

                box-shadow:
                    0 1px 2px rgba(83, 21, 15, .035);
            }

            .character-stat-panel::after {
                content: "";
                position: absolute;
                inset: 3px;
                pointer-events: none;

                border: 1px solid rgba(216, 199, 171, .45);
                border-radius: 8px;
            }

            .character-stat-panel-corner {
                position: absolute;
                z-index: 1;

                width: 13px;
                height: 13px;

                pointer-events: none;
                opacity: .62;
            }

            .character-stat-panel-corner::before,
            .character-stat-panel-corner::after {
                content: "";
                position: absolute;
                background: #cdbb9f;
            }

            .character-stat-panel-corner::before {
                width: 9px;
                height: 1px;
            }

            .character-stat-panel-corner::after {
                width: 1px;
                height: 9px;
            }

            .character-stat-panel-corner.tl {
                left: 6px;
                top: 6px;
            }

            .character-stat-panel-corner.tr {
                right: 6px;
                top: 6px;
                transform: scaleX(-1);
            }

            .character-stat-panel-corner.bl {
                left: 6px;
                bottom: 6px;
                transform: scaleY(-1);
            }

            .character-stat-panel-corner.br {
                right: 6px;
                bottom: 6px;
                transform: scale(-1);
            }

            .character-stat-training-dot {
                width: 10px;
                height: 10px;
                flex: none;

                border: 1px solid rgba(140, 98, 57, .70);
                border-radius: 9999px;

                background: #fffdf9;
            }

            .character-stat-training-dot.proficient {
                border-color: #6b1d14;
                background: #6b1d14;

                box-shadow:
                    inset 0 0 0 2px #faf8f2;
            }

            .character-stat-training-dot.expertise {
                border-color: #53150f;

                background:
                    radial-gradient(
                        circle,
                        #53150f 0 30%,
                        #faf8f2 32% 50%,
                        #53150f 52% 100%
                    );
            }

            .character-stat-rule {
                display: flex;
                align-items: center;
                gap: 7px;
            }

            .character-stat-rule::before,
            .character-stat-rule::after {
                content: "";
                height: 1px;
                flex: 1;
                background:
                    linear-gradient(
                        90deg,
                        transparent,
                        rgba(205,187,159,.82)
                    );
            }

            .character-stat-rule::after {
                transform: scaleX(-1);
            }

            .character-stat-value-orb {
                box-shadow:
                    inset 0 0 0 3px rgba(239,233,220,.72),
                    0 1px 2px rgba(83,21,15,.05);
            }

            .character-stat-row {
                min-height: 27px;
            }

            .character-stat-row:hover {
                background: rgba(239, 233, 220, .62);
            }

            .character-stat-name-button {
                transition: color .15s ease, letter-spacing .15s ease;
            }

            .character-stat-name-button:hover {
                color: #6b1d14;
                letter-spacing: .16em;
            }

            .character-modal-tab {
                border-bottom: 2px solid transparent;
                transition: color .15s ease, background .15s ease, border-color .15s ease;
            }

            .character-modal-tab.active {
                border-bottom-color: #6b1d14;
                color: #53150f;
                background: rgba(239,233,220,.58);
            }

            .character-modal-card {
                border: 1px solid rgba(216,199,171,.68);
                border-radius: 12px;
                background: linear-gradient(180deg, rgba(255,253,249,.98), rgba(247,243,234,.96));
            }

            .character-modal-score {
                box-shadow:
                    inset 0 0 0 3px rgba(239,233,220,.76),
                    0 1px 3px rgba(83,21,15,.06);
            }

            .character-roll-toast {
                box-shadow:
                    0 16px 42px rgba(43,29,23,.18),
                    0 2px 8px rgba(83,21,15,.10);
            }
        </style>
    @endpush


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'characterAbilitiesPanel',
                (initialAbilities, proficiencyBonus, saveUrlTemplate, diceRollUrl) => ({
                    abilities: initialAbilities,
                    proficiencyBonus: parseInt(proficiencyBonus) || 0,
                    saveUrlTemplate,
                    diceRollUrl,

                    modalOpen: false,
                    activeAbility: null,
                    activeTab: 'ability',
                    draft: null,
                    originalScore: null,
                    saving: false,
                    saveError: null,
                    confirmScoreChange: false,

                    rolling: false,
                    lastRoll: null,
                    rollToastOpen: false,
                    rollToastTimer: null,

                    get current() {
                        return this.activeAbility
                            ? (this.abilities[this.activeAbility] ?? null)
                            : null;
                    },

                    get draftEffectiveScore() {
                        return this.effectiveScoreFrom(this.draft);
                    },

                    get draftBaseModifier() {
                        return this.modifierFromScore(this.draft?.score ?? 10);
                    },

                    get draftEffectiveModifier() {
                        return this.modifierFromScore(this.draftEffectiveScore);
                    },

                    get permanentScoreChanged() {
                        if (!this.draft) {
                            return false;
                        }

                        return (parseInt(this.draft.score) || 10)
                            !== (parseInt(this.originalScore) || 10);
                    },

                    signed(value) {
                        const number = parseInt(value) || 0;
                        return number >= 0 ? `+${number}` : `${number}`;
                    },

                    modifierFromScore(score) {
                        return Math.floor(((parseInt(score) || 10) - 10) / 2);
                    },

                    effectiveScoreFrom(ability) {
                        if (!ability) {
                            return 10;
                        }

                        if (
                            ability.override !== null &&
                            ability.override !== '' &&
                            !Number.isNaN(parseInt(ability.override))
                        ) {
                            return Math.max(1, parseInt(ability.override));
                        }

                        return Math.max(1, parseInt(ability.score) || 10);
                    },

                    currentScore(abilityKey) {
                        return this.effectiveScoreFrom(this.abilities[abilityKey]);
                    },

                    modifier(abilityKey) {
                        return this.modifierFromScore(this.currentScore(abilityKey));
                    },

                    savingThrowTotalFrom(ability) {
                        if (!ability) {
                            return 0;
                        }

                        const save = ability.saving_throw;
                        const temporary = parseInt(save.temporary_bonus) || 0;

                        if (
                            save.bonus_override !== null &&
                            save.bonus_override !== '' &&
                            !Number.isNaN(parseInt(save.bonus_override))
                        ) {
                            return parseInt(save.bonus_override) + temporary;
                        }

                        return this.modifierFromScore(this.effectiveScoreFrom(ability))
                            + (save.proficient ? this.proficiencyBonus : 0)
                            + temporary;
                    },

                    savingThrowTotal(abilityKey) {
                        return this.savingThrowTotalFrom(this.abilities[abilityKey]);
                    },

                    skillTotalFrom(ability, skill) {
                        if (!ability || !skill) {
                            return 0;
                        }

                        const temporary = parseInt(skill.temporary_bonus) || 0;

                        if (
                            skill.bonus_override !== null &&
                            skill.bonus_override !== '' &&
                            !Number.isNaN(parseInt(skill.bonus_override))
                        ) {
                            return parseInt(skill.bonus_override) + temporary;
                        }

                        let training = 0;

                        if (skill.expertise) {
                            training = this.proficiencyBonus * 2;
                        } else if (skill.proficient) {
                            training = this.proficiencyBonus;
                        }

                        return this.modifierFromScore(this.effectiveScoreFrom(ability))
                            + training
                            + temporary;
                    },

                    skillTotal(abilityKey, skillKey) {
                        const ability = this.abilities[abilityKey];
                        const skill = ability?.skills?.[skillKey];

                        return this.skillTotalFrom(ability, skill);
                    },

                    trainingClass(skill) {
                        if (skill?.expertise) return 'expertise';
                        if (skill?.proficient) return 'proficient';
                        return '';
                    },

                    skillTrainingMode(skill) {
                        if (skill?.expertise) return 'expertise';
                        if (skill?.proficient) return 'proficient';
                        return 'none';
                    },

                    setSkillTraining(skill, mode) {
                        if (mode === 'expertise') {
                            skill.proficient = true;
                            skill.expertise = true;
                            return;
                        }

                        if (mode === 'proficient') {
                            skill.proficient = true;
                            skill.expertise = false;
                            return;
                        }

                        skill.proficient = false;
                        skill.expertise = false;
                    },

                    openAbility(abilityKey, tab = 'ability') {
                        const source = this.abilities[abilityKey];

                        if (!source) {
                            return;
                        }

                        this.activeAbility = abilityKey;
                        this.activeTab = tab;
                        this.draft = JSON.parse(JSON.stringify(source));
                        this.originalScore = parseInt(source.score) || 10;
                        this.saveError = null;
                        this.confirmScoreChange = false;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        if (this.saving) {
                            return;
                        }

                        this.modalOpen = false;
                        this.activeAbility = null;
                        this.activeTab = 'ability';
                        this.draft = null;
                        this.originalScore = null;
                        this.saveError = null;
                        this.confirmScoreChange = false;
                    },

                    setTab(tab) {
                        this.activeTab = tab;
                        this.saveError = null;
                    },

                    clearExtraScore() {
                        if (this.draft) {
                            this.draft.override = null;
                        }
                    },

                    async rollCheck(label, modifier) {
                        if (this.rolling) {
                            return;
                        }

                        const bonus = parseInt(modifier) || 0;
                        const expression = bonus > 0
                            ? `1d20+${bonus}`
                            : (bonus < 0 ? `1d20${bonus}` : '1d20');

                        this.rolling = true;

                        let total = null;
                        let die = null;
                        let formatted = null;

                        try {
                            const response = await fetch(this.diceRollUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector('meta[name="csrf-token"]')
                                            ?.getAttribute('content')
                                        ?? '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ expression }),
                            });

                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                throw new Error(
                                    data?.message ?? 'Não foi possível rolar.'
                                );
                            }

                            total = parseInt(
                                data?.data?.total
                                ?? data?.data?.result
                                ?? data?.data?.value
                                ?? data?.data?.sum
                                ?? data?.total
                                ?? data?.result
                            );

                            formatted = data?.formatted ?? null;

                            if (Number.isNaN(total)) {
                                total = null;
                            }

                            if (total !== null) {
                                die = total - bonus;
                            }
                        } catch (error) {
                            console.error('Erro ao rolar dado:', error);

                            die = Math.floor(Math.random() * 20) + 1;
                            total = die + bonus;
                        } finally {
                            this.rolling = false;
                        }

                        this.lastRoll = {
                            label,
                            modifier: bonus,
                            die,
                            total,
                            formatted,
                        };

                        this.rollToastOpen = true;

                        window.dispatchEvent(
                            new CustomEvent('character-dice-rolled', {
                                detail: this.lastRoll,
                            })
                        );

                        if (this.rollToastTimer) {
                            clearTimeout(this.rollToastTimer);
                        }

                        this.rollToastTimer = setTimeout(() => {
                            this.rollToastOpen = false;
                        }, 4200);
                    },

                    requestSave() {
                        if (!this.draft || this.saving) {
                            return;
                        }

                        if (this.permanentScoreChanged) {
                            this.confirmScoreChange = true;
                            return;
                        }

                        this.persistCurrent();
                    },

                    confirmPermanentScoreChange() {
                        this.confirmScoreChange = false;
                        this.persistCurrent();
                    },

                    async persistCurrent() {
                        if (!this.draft || !this.activeAbility || this.saving) {
                            return;
                        }

                        this.saving = true;
                        this.saveError = null;

                        const abilityKey = this.activeAbility;
                        const ability = this.draft;

                        const payload = {
                            ability: {
                                score: Math.max(1, parseInt(ability.score) || 10),
                                temporary_bonus: 0,
                                override:
                                    ability.override === null ||
                                    ability.override === ''
                                        ? null
                                        : Math.max(
                                            1,
                                            parseInt(ability.override) || 1
                                        ),
                            },

                            saving_throw: {
                                proficient: !!ability.saving_throw.proficient,
                                bonus_override:
                                    ability.saving_throw.bonus_override === null ||
                                    ability.saving_throw.bonus_override === ''
                                        ? null
                                        : parseInt(
                                            ability.saving_throw.bonus_override
                                        ),
                                temporary_bonus:
                                    parseInt(
                                        ability.saving_throw.temporary_bonus
                                    ) || 0,
                            },

                            skills: Object.values(ability.skills).map(skill => ({
                                skill: skill.key,
                                proficient: !!skill.proficient,
                                expertise: !!skill.expertise,
                                bonus_override:
                                    skill.bonus_override === null ||
                                    skill.bonus_override === ''
                                        ? null
                                        : parseInt(skill.bonus_override),
                                temporary_bonus:
                                    parseInt(skill.temporary_bonus) || 0,
                            })),
                        };

                        try {
                            const response = await fetch(
                                this.saveUrlTemplate.replace(
                                    '__ABILITY__',
                                    abilityKey
                                ),
                                {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN':
                                            document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute('content')
                                            ?? '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: JSON.stringify(payload),
                                }
                            );

                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                const validationMessages = data?.errors
                                    ? Object.values(data.errors)
                                        .flat()
                                        .filter(Boolean)
                                    : [];

                                throw new Error(
                                    validationMessages.length
                                        ? validationMessages.join(' ')
                                        : (
                                            data?.message
                                            ?? 'Não foi possível salvar.'
                                        )
                                );
                            }

                            this.draft.score = payload.ability.score;
                            this.draft.temporary_bonus = 0;
                            this.draft.override = payload.ability.override;

                            this.abilities[abilityKey] =
                                JSON.parse(JSON.stringify(this.draft));

                            /*
                             * Fechamento manual porque closeModal()
                             * bloqueia enquanto saving === true.
                             */
                            this.modalOpen = false;
                            this.activeAbility = null;
                            this.activeTab = 'ability';
                            this.draft = null;
                            this.originalScore = null;
                            this.saveError = null;
                            this.confirmScoreChange = false;

                        } catch (error) {
                            console.error('Erro ao salvar atributo:', error);

                            this.saveError =
                                error.message
                                ?? 'Não foi possível salvar.';
                        } finally {
                            this.saving = false;
                        }
                    },
                })
            );
        });
    </script>
@endonce


<section
    x-data="characterAbilitiesPanel(
        @js($initialAbilities),
        {{ $proficiencyBonus }},
        @js(
            route(
                'characters.stats.ability.update',
                [
                    'character' => $character,
                    'ability' => '__ABILITY__',
                ]
            )
        ),
        @js(url('/api/roll'))
    )"
    @keydown.escape.window="
        if (confirmScoreChange) {
            confirmScoreChange = false;
        } else {
            closeModal();
        }
    "
    class="
        character-stats-sheet
        relative
        overflow-hidden
        rounded-b-2xl
        rounded-t-none
        border
        border-t-0
        border-[#cdbb9f]/60
        bg-[#faf8f2]
        shadow-[0_2px_5px_rgba(83,21,15,.035)]
    "
>
    {{-- ============================================================
         TÍTULO
    ============================================================= --}}

    <div class="relative z-10 px-3.5 pb-2 pt-2.5">
        <div class="flex items-center gap-2">
            <span
                class="
                    h-px
                    flex-1
                    bg-gradient-to-r
                    from-transparent
                    via-[#cdbb9f]/75
                    to-[#cdbb9f]/75
                "
            ></span>

            <div class="flex items-center gap-2">
                <span class="text-[10px] text-[#b29161]">◆</span>

                <h2
                    class="
                        font-serif
                        text-[15px]
                        font-black
                        uppercase
                        tracking-[0.08em]
                        text-[#53150f]
                    "
                >
                    Atributos
                </h2>

                <span class="text-[10px] text-[#b29161]">◆</span>
            </div>

            <span
                class="
                    h-px
                    flex-1
                    bg-gradient-to-l
                    from-transparent
                    via-[#cdbb9f]/75
                    to-[#cdbb9f]/75
                "
            ></span>
        </div>
    </div>


    {{-- ============================================================
         COLUNAS
    ============================================================= --}}

    <div
        class="
            relative
            z-10
            grid
            grid-cols-2
            items-start
            gap-2.5
            px-2.5
            pb-3
        "
    >
        <template
            x-for="
                column in [
                    ['strength', 'dexterity', 'constitution'],
                    ['intelligence', 'wisdom', 'charisma']
                ]
            "
            :key="column.join('-')"
        >
            <div class="min-w-0 space-y-2.5">
                <template x-for="abilityKey in column" :key="abilityKey">
                    <article class="character-stat-panel group w-full">
                        <span class="character-stat-panel-corner tl"></span>
                        <span class="character-stat-panel-corner tr"></span>
                        <span class="character-stat-panel-corner bl"></span>
                        <span class="character-stat-panel-corner br"></span>


                        {{-- NOME: ÚNICO PONTO QUE ABRE O MODAL --}}

                        <div
                            class="
                                relative
                                z-10
                                border-b
                                border-[#d8c7ab]/60
                                px-3
                                pb-2
                                pt-2.5
                                text-center
                            "
                        >
                            <div class="character-stat-rule">
                                <button
                                    type="button"
                                    @click="openAbility(abilityKey, 'ability')"
                                    class="
                                        character-stat-name-button
                                        whitespace-nowrap
                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.13em]
                                        text-[#53150f]
                                    "
                                    :title="
                                        'Editar ' +
                                        abilities[abilityKey].label
                                    "
                                >
                                    <span
                                        x-text="abilities[abilityKey].label"
                                    ></span>
                                </button>
                            </div>


                            {{-- VALOR: ROLA TESTE DE ATRIBUTO --}}

                            <button
                                type="button"
                                @click="
                                    rollCheck(
                                        abilities[abilityKey].label,
                                        modifier(abilityKey)
                                    )
                                "
                                class="
                                    mt-2
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    gap-2.5
                                    rounded-lg
                                    py-0.5
                                    transition
                                    hover:bg-[#efe9dc]/45
                                "
                                :title="
                                    'Rolar teste de ' +
                                    abilities[abilityKey].label
                                "
                            >
                                <span
                                    class="
                                        character-stat-value-orb
                                        flex
                                        h-[50px]
                                        w-[50px]
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        border
                                        border-[#cdbb9f]/90
                                        bg-[#fffdf9]
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[23px]
                                            font-black
                                            leading-none
                                            text-[#53150f]
                                        "
                                        x-text="signed(modifier(abilityKey))"
                                    ></span>
                                </span>

                                <span
                                    class="
                                        flex
                                        min-w-[38px]
                                        flex-col
                                        items-center
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[21px]
                                            font-black
                                            leading-none
                                            text-[#6b1d14]
                                        "
                                        x-text="currentScore(abilityKey)"
                                    ></span>

                                    <span
                                        class="
                                            mt-1
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-[0.11em]
                                            text-[#8c6239]/70
                                        "
                                    >
                                        Valor
                                    </span>
                                </span>
                            </button>
                        </div>


                        {{-- SALVAGUARDA: ROLA DADO --}}

                        <button
                            type="button"
                            @click="
                                rollCheck(
                                    abilities[abilityKey].label +
                                    ' — Salvaguarda',
                                    savingThrowTotal(abilityKey)
                                )
                            "
                            class="
                                character-stat-row
                                relative
                                z-10
                                flex
                                w-full
                                items-center
                                gap-2
                                border-b
                                border-[#d8c7ab]/45
                                px-3
                                py-1.5
                                text-left
                                transition
                            "
                            title="Rolar salvaguarda"
                        >
                            <span
                                class="character-stat-training-dot"
                                :class="{
                                    'proficient':
                                        abilities[abilityKey]
                                            .saving_throw
                                            .proficient
                                }"
                            ></span>

                            <span
                                class="
                                    min-w-0
                                    flex-1
                                    truncate
                                    text-[10.5px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Salvamento
                            </span>

                            <span
                                class="
                                    shrink-0
                                    font-serif
                                    text-[12px]
                                    font-black
                                    text-[#6b1d14]
                                "
                                x-text="
                                    signed(
                                        savingThrowTotal(abilityKey)
                                    )
                                "
                            ></span>
                        </button>


                        {{-- PERÍCIAS: ROLAM DADO --}}

                        <div class="relative z-10">
                            <template
                                x-for="
                                    skill in Object.values(
                                        abilities[abilityKey].skills
                                    )
                                "
                                :key="skill.key"
                            >
                                <button
                                    type="button"
                                    @click="
                                        rollCheck(
                                            skill.label,
                                            skillTotal(
                                                abilityKey,
                                                skill.key
                                            )
                                        )
                                    "
                                    class="
                                        character-stat-row
                                        flex
                                        w-full
                                        items-center
                                        gap-2
                                        border-b
                                        border-[#d8c7ab]/30
                                        px-3
                                        py-1.5
                                        text-left
                                        transition
                                        last:border-b-0
                                    "
                                    :title="'Rolar ' + skill.label"
                                >
                                    <span
                                        class="character-stat-training-dot"
                                        :class="trainingClass(skill)"
                                    ></span>

                                    <span
                                        class="
                                            min-w-0
                                            flex-1
                                            truncate
                                            text-[9px]
                                            font-semibold
                                            text-[#5f3a27]
                                        "
                                        x-text="skill.label"
                                    ></span>

                                    <span
                                        class="
                                            shrink-0
                                            font-serif
                                            text-[11px]
                                            font-black
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            signed(
                                                skillTotal(
                                                    abilityKey,
                                                    skill.key
                                                )
                                            )
                                        "
                                    ></span>
                                </button>
                            </template>
                        </div>
                    </article>
                </template>
            </div>
        </template>
    </div>


    {{-- ============================================================
         TOAST DE ROLAGEM
    ============================================================= --}}

    <div
        x-show="rollToastOpen && lastRoll"
        x-cloak
        x-transition
        class="
            character-roll-toast
            fixed
            bottom-5
            left-1/2
            z-[190]
            w-[min(92vw,340px)]
            -translate-x-1/2
            overflow-hidden
            rounded-xl
            border
            border-[#cdbb9f]
            bg-[#faf8f2]
        "
    >
        <div class="flex items-center gap-3 px-3.5 py-3">
            <div
                class="
                    flex
                    h-11
                    w-11
                    shrink-0
                    items-center
                    justify-center
                    rounded-lg
                    bg-[#53150f]
                    font-serif
                    text-xl
                    font-black
                    text-[#f4f1e8]
                "
                x-text="lastRoll?.total ?? '—'"
            ></div>

            <div class="min-w-0 flex-1">
                <p
                    class="
                        truncate
                        text-[10px]
                        font-black
                        text-[#53150f]
                    "
                    x-text="lastRoll?.label ?? ''"
                ></p>

                <p
                    class="
                        mt-0.5
                        text-[8px]
                        font-bold
                        text-[#8c6239]
                    "
                >
                    <span x-text="lastRoll?.die ?? '—'"></span>
                    <span> no d20</span>

                    <span x-show="lastRoll?.modifier !== 0">
                        ·
                        <span
                            x-text="
                                signed(
                                    lastRoll?.modifier ?? 0
                                )
                            "
                        ></span>
                        modificador
                    </span>
                </p>
            </div>

            <button
                type="button"
                @click="rollToastOpen = false"
                class="
                    flex
                    h-7
                    w-7
                    shrink-0
                    items-center
                    justify-center
                    rounded-md
                    text-[#8c6239]
                    hover:bg-[#efe9dc]
                "
            >
                ×
            </button>
        </div>
    </div>


    {{-- ============================================================
         MODAL — DUAS ABAS
    ============================================================= --}}

    <template x-teleport="body">
        <div
            x-show="modalOpen"
            x-cloak
            class="
                fixed
                inset-0
                z-[170]
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
                    bg-[#2b1d17]/60
                    backdrop-blur-sm
                "
                @click="closeModal()"
            ></div>


            <div
                x-show="modalOpen"
                @click.stop
                class="
                    relative
                    z-10
                    flex
                    max-h-[86vh]
                    w-full
                    max-w-xl
                    flex-col
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#cdbb9f]
                    bg-[#faf8f2]
                    shadow-2xl
                "
            >
                {{-- HEADER --}}

                <div
                    class="
                        shrink-0
                        border-b
                        border-[#d8c7ab]/60
                        bg-[#efe9dc]/65
                    "
                >
                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-4
                            px-4
                            pb-3
                            pt-4
                        "
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    border
                                    border-[#cdbb9f]/70
                                    bg-[#faf8f2]
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]
                                "
                                x-text="draft?.short ?? ''"
                            ></div>

                            <div class="min-w-0">
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.18em]
                                        text-[#8c6239]
                                    "
                                >
                                    Configuração
                                </p>

                                <h3
                                    class="
                                        mt-0.5
                                        truncate
                                        font-serif
                                        text-xl
                                        font-black
                                        text-[#53150f]
                                    "
                                    x-text="draft?.label ?? ''"
                                ></h3>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="closeModal()"
                            class="
                                flex
                                h-9
                                w-9
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                text-lg
                                text-[#8c6239]
                                transition
                                hover:bg-[#e8dfd1]
                                hover:text-[#53150f]
                            "
                        >
                            ×
                        </button>
                    </div>


                    {{-- ABAS --}}

                    <div
                        class="
                            grid
                            grid-cols-2
                            border-t
                            border-[#d8c7ab]/45
                        "
                    >
                        <button
                            type="button"
                            @click="setTab('ability')"
                            class="
                                character-modal-tab
                                px-3
                                py-2.5
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#8c6239]
                            "
                            :class="{ 'active': activeTab === 'ability' }"
                        >
                            Atributo
                        </button>

                        <button
                            type="button"
                            @click="setTab('training')"
                            class="
                                character-modal-tab
                                px-3
                                py-2.5
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.12em]
                                text-[#8c6239]
                            "
                            :class="{ 'active': activeTab === 'training' }"
                        >
                            Salvamento & Perícias
                        </button>
                    </div>
                </div>


                {{-- CONTEÚDO --}}

                <div class="min-h-0 flex-1 overflow-y-auto p-4">

                    {{-- ABA 1 — ATRIBUTO --}}

                    <div x-show="activeTab === 'ability'">
                        <div class="character-modal-card overflow-hidden">

                            {{-- VALOR PERMANENTE --}}

                            <div
                                class="
                                    border-b
                                    border-[#d8c7ab]/55
                                    p-4
                                "
                            >
                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-4
                                    "
                                >
                                    <div>
                                        <p
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.16em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Valor Permanente
                                        </p>

                                        <p
                                            class="
                                                mt-1
                                                max-w-[330px]
                                                text-[10px]
                                                leading-relaxed
                                                text-[#8c6239]/75
                                            "
                                        >
                                            Este é o valor real do personagem.
                                            Alterá-lo muda permanentemente o atributo.
                                        </p>
                                    </div>

                                    <div
                                        class="
                                            character-modal-score
                                            flex
                                            h-[62px]
                                            min-w-[92px]
                                            items-center
                                            justify-center
                                            gap-1.5
                                            rounded-xl
                                            border
                                            border-[#cdbb9f]
                                            bg-[#fffdf9]
                                            px-3
                                        "
                                    >
                                        <span
                                            class="
                                                font-serif
                                                text-[27px]
                                                font-black
                                                leading-none
                                                text-[#53150f]
                                            "
                                            x-text="draft?.score ?? 10"
                                        ></span>

                                        <span
                                            class="
                                                font-serif
                                                text-sm
                                                font-black
                                                text-[#8c6239]
                                            "
                                        >
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftBaseModifier
                                                    )
                                                "
                                            ></span>)
                                        </span>
                                    </div>
                                </div>

                                <label class="mt-4 block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.12em]
                                            text-[#53150f]
                                        "
                                        x-text="draft?.label ?? 'Atributo'"
                                    ></span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model.number="draft.score"
                                        class="
                                            mt-1.5
                                            w-full
                                            rounded-xl
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-3
                                            py-2.5
                                            text-center
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            transition
                                            focus:border-[#6b1d14]
                                            focus:ring-2
                                            focus:ring-[#6b1d14]/10
                                        "
                                    >
                                </label>

                                <div
                                    x-show="permanentScoreChanged"
                                    x-cloak
                                    class="
                                        mt-3
                                        rounded-lg
                                        border
                                        border-[#d4b36b]/55
                                        bg-[#f8efd9]/70
                                        px-3
                                        py-2
                                        text-[9px]
                                        font-bold
                                        leading-relaxed
                                        text-[#8a6418]
                                    "
                                >
                                    Você está alterando o valor permanente de
                                    <strong x-text="draft?.label ?? ''"></strong>
                                    de
                                    <strong x-text="originalScore"></strong>
                                    para
                                    <strong x-text="draft?.score ?? ''"></strong>.
                                    A confirmação será solicitada ao salvar.
                                </div>
                            </div>


                            {{-- BÔNUS EXTRA --}}

                            <div class="p-4">
                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-4
                                    "
                                >
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p
                                                class="
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.16em]
                                                    text-[#6b1d14]
                                                "
                                            >
                                                Bônus Extra
                                            </p>

                                            <span
                                                x-show="
                                                    draft?.override !== null &&
                                                    draft?.override !== ''
                                                "
                                                x-cloak
                                                class="
                                                    rounded-md
                                                    bg-[#6b1d14]
                                                    px-1.5
                                                    py-0.5
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    text-[#f4f1e8]
                                                "
                                            >
                                                Ativo
                                            </span>
                                        </div>

                                        <p
                                            class="
                                                mt-1
                                                max-w-[330px]
                                                text-[10px]
                                                leading-relaxed
                                                text-[#8c6239]/75
                                            "
                                        >
                                            Digite o score inteiro.
                                            Ex.: Uma poção que deixa sua em Força 16:
                                            digite 16. Ao apagar, volta para o valor original.
                                        </p>
                                    </div>

                                    <div
                                        class="
                                            character-modal-score
                                            flex
                                            h-[62px]
                                            min-w-[92px]
                                            items-center
                                            justify-center
                                            gap-1.5
                                            rounded-xl
                                            border
                                            border-[#d4b36b]/70
                                            bg-[#fffaf0]
                                            px-3
                                        "
                                    >
                                        <span
                                            class="
                                                font-serif
                                                text-[27px]
                                                font-black
                                                leading-none
                                                text-[#6b1d14]
                                            "
                                            x-text="draftEffectiveScore"
                                        ></span>

                                        <span
                                            class="
                                                font-serif
                                                text-sm
                                                font-black
                                                text-[#9a6f16]
                                            "
                                        >
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftEffectiveModifier
                                                    )
                                                "
                                            ></span>)
                                        </span>
                                    </div>
                                </div>

                                <label class="mt-4 block">
                                    <span
                                        class="
                                            flex
                                            items-center
                                            justify-between
                                            gap-2
                                        "
                                    >
                                        <span
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]
                                                text-[#53150f]
                                            "
                                        >
                                            Score Temporário
                                        </span>

                                        <button
                                            x-show="
                                                draft?.override !== null &&
                                                draft?.override !== ''
                                            "
                                            x-cloak
                                            type="button"
                                            @click="clearExtraScore()"
                                            class="
                                                text-[8px]
                                                font-black
                                                uppercase
                                                tracking-wide
                                                text-[#6b1d14]
                                                hover:underline
                                            "
                                        >
                                            Remover bônus
                                        </button>
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        x-model="draft.override"
                                        placeholder="Ex.: 16"
                                        class="
                                            mt-1.5
                                            w-full
                                            rounded-xl
                                            border
                                            border-[#d4b36b]/70
                                            bg-[#fffaf0]
                                            px-3
                                            py-2.5
                                            text-center
                                            font-serif
                                            text-lg
                                            font-black
                                            text-[#6b1d14]
                                            outline-none
                                            transition
                                            placeholder:text-[#b7a98f]
                                            focus:border-[#9a6f16]
                                            focus:ring-2
                                            focus:ring-[#d4b36b]/15
                                        "
                                    >
                                </label>

                                <div
                                    class="
                                        mt-3
                                        rounded-lg
                                        border
                                        border-[#d8c7ab]/60
                                        bg-[#efe9dc]/45
                                        px-3
                                        py-2.5
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
                                        <span
                                            class="
                                                text-[9px]
                                                font-bold
                                                text-[#8c6239]
                                            "
                                        >
                                            Resultado usado na ficha
                                        </span>

                                        <strong
                                            class="
                                                font-serif
                                                text-base
                                                text-[#53150f]
                                            "
                                        >
                                            <span
                                                x-text="draftEffectiveScore"
                                            ></span>
                                            (<span
                                                x-text="
                                                    signed(
                                                        draftEffectiveModifier
                                                    )
                                                "
                                            ></span>)
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- ABA 2 — SALVAGUARDA & PERÍCIAS --}}

                    <div
                        x-show="activeTab === 'training'"
                        x-cloak
                        class="space-y-3"
                    >
                        <section class="character-modal-card p-4">
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
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.15em]
                                            text-[#8c6239]
                                        "
                                    >
                                        Salvaguarda
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-[10px]
                                            text-[#8c6239]/75
                                        "
                                    >
                                        Treinamento e ajustes especiais.
                                    </p>
                                </div>

                                <span
                                    class="
                                        font-serif
                                        text-2xl
                                        font-black
                                        text-[#53150f]
                                    "
                                    x-text="
                                        draft
                                            ? signed(
                                                savingThrowTotalFrom(draft)
                                            )
                                            : '+0'
                                    "
                                ></span>
                            </div>

                            <label
                                class="
                                    mt-3
                                    flex
                                    items-center
                                    justify-between
                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/65
                                    bg-[#efe9dc]/45
                                    px-3
                                    py-2.5
                                "
                            >
                                <span>
                                    <span
                                        class="
                                            block
                                            text-[10px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Proficiência
                                    </span>
                                    <span
                                        class="
                                            mt-0.5
                                            block
                                            text-[8px]
                                            text-[#8c6239]/70
                                        "
                                    >
                                        Adiciona o bônus de proficiência.
                                    </span>
                                </span>

                                <input
                                    type="checkbox"
                                    x-model="draft.saving_throw.proficient"
                                    class="
                                        h-5
                                        w-5
                                        rounded
                                        border-[#cdbb9f]
                                        text-[#6b1d14]
                                        focus:ring-[#6b1d14]/20
                                    "
                                >
                            </label>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <label class="block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wide
                                            text-[#8c6239]
                                        "
                                    >
                                        Bônus Extra
                                    </span>

                                    <input
                                        type="number"
                                        x-model.number="
                                            draft
                                                .saving_throw
                                                .temporary_bonus
                                        "
                                        class="
                                            mt-1
                                            w-full
                                            rounded-lg
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-2.5
                                            py-2
                                            text-center
                                            text-sm
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            focus:border-[#6b1d14]
                                        "
                                    >
                                </label>

                                <label class="block">
                                    <span
                                        class="
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wide
                                            text-[#8c6239]
                                        "
                                    >
                                        Total Manual
                                    </span>

                                    <input
                                        type="number"
                                        x-model="
                                            draft
                                                .saving_throw
                                                .bonus_override
                                        "
                                        placeholder="Automático"
                                        class="
                                            mt-1
                                            w-full
                                            rounded-lg
                                            border
                                            border-[#cdbb9f]
                                            bg-white
                                            px-2.5
                                            py-2
                                            text-center
                                            text-sm
                                            font-black
                                            text-[#53150f]
                                            outline-none
                                            placeholder:text-[#b7a98f]
                                            focus:border-[#6b1d14]
                                        "
                                    >
                                </label>
                            </div>
                        </section>


                        <section>
                            <div class="mb-2 px-1">
                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.15em]
                                        text-[#8c6239]
                                    "
                                >
                                    Perícias
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-[9px]
                                        text-[#8c6239]/70
                                    "
                                >
                                    Treinamento, expertise e ajustes especiais.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <template
                                    x-for="
                                        skill in Object.values(
                                            draft?.skills ?? {}
                                        )
                                    "
                                    :key="skill.key"
                                >
                                    <div class="character-modal-card p-3">
                                        <div
                                            class="
                                                flex
                                                items-center
                                                justify-between
                                                gap-3
                                            "
                                        >
                                            <div class="min-w-0">
                                                <p
                                                    class="
                                                        truncate
                                                        text-[11px]
                                                        font-black
                                                        text-[#53150f]
                                                    "
                                                    x-text="skill.label"
                                                ></p>

                                                <p
                                                    class="
                                                        mt-0.5
                                                        text-[8px]
                                                        text-[#8c6239]/70
                                                    "
                                                >
                                                    Resultado atual
                                                </p>
                                            </div>

                                            <span
                                                class="
                                                    shrink-0
                                                    font-serif
                                                    text-xl
                                                    font-black
                                                    text-[#53150f]
                                                "
                                                x-text="
                                                    draft
                                                        ? signed(
                                                            skillTotalFrom(
                                                                draft,
                                                                skill
                                                            )
                                                        )
                                                        : '+0'
                                                "
                                            ></span>
                                        </div>

                                        <div
                                            class="
                                                mt-2.5
                                                grid
                                                grid-cols-3
                                                overflow-hidden
                                                rounded-lg
                                                border
                                                border-[#d8c7ab]/70
                                                bg-[#f4f1e8]/50
                                            "
                                        >
                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'none'
                                                    )
                                                "
                                                class="
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'none'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Normal
                                            </button>

                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'proficient'
                                                    )
                                                "
                                                class="
                                                    border-x
                                                    border-[#d8c7ab]/60
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'proficient'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Prof.
                                            </button>

                                            <button
                                                type="button"
                                                @click="
                                                    setSkillTraining(
                                                        skill,
                                                        'expertise'
                                                    )
                                                "
                                                class="
                                                    px-1
                                                    py-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-wide
                                                    transition
                                                "
                                                :class="
                                                    skillTrainingMode(skill)
                                                        === 'expertise'
                                                        ? 'bg-[#6b1d14] text-[#f4f1e8]'
                                                        : 'text-[#8c6239] hover:bg-[#efe9dc]'
                                                "
                                            >
                                                Expertise
                                            </button>
                                        </div>

                                        <div class="mt-2 grid grid-cols-2 gap-2">
                                            <label class="block">
                                                <span
                                                    class="
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#8c6239]
                                                    "
                                                >
                                                    Bônus Extra
                                                </span>

                                                <input
                                                    type="number"
                                                    x-model.number="
                                                        skill
                                                            .temporary_bonus
                                                    "
                                                    class="
                                                        mt-1
                                                        w-full
                                                        rounded-lg
                                                        border
                                                        border-[#cdbb9f]
                                                        bg-white
                                                        px-2
                                                        py-1.5
                                                        text-center
                                                        text-sm
                                                        font-black
                                                        text-[#53150f]
                                                        outline-none
                                                        focus:border-[#6b1d14]
                                                    "
                                                >
                                            </label>

                                            <label class="block">
                                                <span
                                                    class="
                                                        text-[8px]
                                                        font-black
                                                        uppercase
                                                        tracking-wide
                                                        text-[#8c6239]
                                                    "
                                                >
                                                    Total Manual
                                                </span>

                                                <input
                                                    type="number"
                                                    x-model="skill.bonus_override"
                                                    placeholder="Automático"
                                                    class="
                                                        mt-1
                                                        w-full
                                                        rounded-lg
                                                        border
                                                        border-[#cdbb9f]
                                                        bg-white
                                                        px-2
                                                        py-1.5
                                                        text-center
                                                        text-sm
                                                        font-black
                                                        text-[#53150f]
                                                        outline-none
                                                        placeholder:text-[#b7a98f]
                                                        focus:border-[#6b1d14]
                                                    "
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>


                    <div
                        x-show="saveError"
                        x-cloak
                        class="
                            mt-3
                            rounded-lg
                            border
                            border-red-200
                            bg-red-50
                            px-3
                            py-2.5
                            text-[10px]
                            font-bold
                            text-red-700
                        "
                        x-text="saveError"
                    ></div>
                </div>


                {{-- FOOTER --}}

                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3
                        border-t
                        border-[#d8c7ab]/60
                        bg-[#efe9dc]/45
                        px-4
                        py-3
                    "
                >
                    <p
                        class="
                            hidden
                            text-[8px]
                            font-bold
                            text-[#8c6239]/70
                            sm:block
                        "
                    >
                        As alterações só entram na ficha após salvar.
                    </p>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="saving"
                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#8c6239]
                                transition
                                hover:bg-[#e8dfd1]
                                disabled:opacity-50
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="requestSave()"
                            :disabled="saving"
                            class="
                                rounded-lg
                                bg-[#6b1d14]
                                px-4
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-widest
                                text-[#f4f1e8]
                                transition
                                hover:bg-[#53150f]
                                disabled:opacity-50
                            "
                        >
                            <span x-show="!saving">Salvar</span>
                            <span x-show="saving" x-cloak>Salvando...</span>
                        </button>
                    </div>
                </div>


                {{-- CONFIRMAÇÃO DE ALTERAÇÃO PERMANENTE --}}

                <div
                    x-show="confirmScoreChange"
                    x-cloak
                    class="
                        absolute
                        inset-0
                        z-30
                        flex
                        items-center
                        justify-center
                        bg-[#2b1d17]/45
                        p-4
                        backdrop-blur-[2px]
                    "
                >
                    <div
                        @click.stop
                        class="
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
                                border-b
                                border-[#d8c7ab]/60
                                bg-[#efe9dc]/65
                                px-4
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]
                                    text-[#8c6239]
                                "
                            >
                                Alteração permanente
                            </p>

                            <h4
                                class="
                                    mt-0.5
                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Confirmar novo atributo?
                            </h4>
                        </div>

                        <div class="p-4">
                            <p
                                class="
                                    text-[11px]
                                    leading-relaxed
                                    text-[#5f3a27]
                                "
                            >
                                Tem certeza que deseja mudar
                                <strong x-text="draft?.label ?? 'este atributo'"></strong>
                                de
                                <strong>
                                    <span x-text="originalScore"></span>
                                    (<span
                                        x-text="
                                            signed(
                                                modifierFromScore(
                                                    originalScore
                                                )
                                            )
                                        "
                                    ></span>)
                                </strong>
                                para
                                <strong>
                                    <span x-text="draft?.score ?? ''"></span>
                                    (<span
                                        x-text="
                                            signed(
                                                modifierFromScore(
                                                    draft?.score ?? 10
                                                )
                                            )
                                        "
                                    ></span>)
                                </strong>?
                            </p>

                            <div
                                class="
                                    mt-3
                                    rounded-lg
                                    border
                                    border-[#d4b36b]/55
                                    bg-[#f8efd9]/70
                                    px-3
                                    py-2.5
                                    text-[9px]
                                    leading-relaxed
                                    text-[#8a6418]
                                "
                            >
                                Essa alteração modifica o valor permanente.
                                O <strong>Bônus Extra</strong> é temporário e
                                não exige confirmação.
                            </div>
                        </div>

                        <div
                            class="
                                flex
                                justify-end
                                gap-2
                                border-t
                                border-[#d8c7ab]/60
                                bg-[#efe9dc]/45
                                px-4
                                py-3
                            "
                        >
                            <button
                                type="button"
                                @click="confirmScoreChange = false"
                                class="
                                    rounded-lg
                                    px-3
                                    py-2
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                    hover:bg-[#e8dfd1]
                                "
                            >
                                Voltar
                            </button>

                            <button
                                type="button"
                                @click="confirmPermanentScoreChange()"
                                class="
                                    rounded-lg
                                    bg-[#6b1d14]
                                    px-4
                                    py-2
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#f4f1e8]
                                    hover:bg-[#53150f]
                                "
                            >
                                Sim, alterar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</section>