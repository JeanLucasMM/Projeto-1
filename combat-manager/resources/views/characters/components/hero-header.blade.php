@props(['character'])

@php
    $combat = $character->combat;

    /*
    |--------------------------------------------------------------------------
    | VIDA
    |--------------------------------------------------------------------------
    */

    $currentHp = max(
        0,
        (int) ($combat?->current_hp ?? 0)
    );

    $maxHp = max(
        1,
        (int) ($combat?->max_hp ?? 1)
    );

    $temporaryHp = max(
        0,
        (int) ($combat?->temporary_hp ?? 0)
    );

    $temporaryMaxHp = max(
        0,
        (int) ($combat?->temporary_max_hp ?? 0)
    );

    /*
    |--------------------------------------------------------------------------
    | DEATH SAVES
    |--------------------------------------------------------------------------
    */

    $deathSuccesses = min(
        3,
        max(
            0,
            (int) ($combat?->death_save_successes ?? 0)
        )
    );

    $deathFailures = min(
        3,
        max(
            0,
            (int) ($combat?->death_save_failures ?? 0)
        )
    );

    /*
    |--------------------------------------------------------------------------
    | IDENTIDADE
    |--------------------------------------------------------------------------
    */

    $classes = $character->classes
        ->sortByDesc(
            fn ($class) => [
                (int) ($class->is_primary ?? false),
                (int) $class->level,
                -((int) ($class->sort_order ?? 0)),
            ]
        )
        ->values();

    $primaryClass =
        $character->primary_class ??
        $classes->first();

    $className =
        $primaryClass?->class ??
        'Classe não definida';

    $subclassName =
        $primaryClass?->subclass;

    $imageUrl =
        $character->image_path
            ? Storage::url(
                $character->image_path
            )
            : null;

    $hasMulticlass =
        $classes->count() > 1;

    /*
    |--------------------------------------------------------------------------
    | PROFICIÊNCIA
    |--------------------------------------------------------------------------
    */

    $calculatedProficiency = match (true) {
        (int) $character->level >= 17 => 6,
        (int) $character->level >= 13 => 5,
        (int) $character->level >= 9 => 4,
        (int) $character->level >= 5 => 3,
        default => 2,
    };

    $proficiencyBonus =
        $character->proficiency_bonus ??
        $calculatedProficiency;

    /*
    |--------------------------------------------------------------------------
    | ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    $abilities =
        $character->abilities;

    $abilityScores = [
        'str' => (int) ($abilities?->strength ?? 10),
        'dex' => (int) ($abilities?->dexterity ?? 10),
        'con' => (int) ($abilities?->constitution ?? 10),
        'int' => (int) ($abilities?->intelligence ?? 10),
        'wis' => (int) ($abilities?->wisdom ?? 10),
        'cha' => (int) ($abilities?->charisma ?? 10),
    ];

    $abilityModifiers = collect($abilityScores)
        ->mapWithKeys(
            fn ($score, $key) => [
                $key => (int) floor(
                    ($score - 10) / 2
                ),
            ]
        )
        ->all();

    /*
    |--------------------------------------------------------------------------
    | OVERRIDES DE COMBATE
    |--------------------------------------------------------------------------
    |
    | Mantemos o objeto completo para que Armadura e Dados Rápidos
    | não apaguem as configurações um do outro.
    |
    */

    $combatOverrides =
        $combat?->overrides
        ?? [];

    if (is_string($combatOverrides)) {

        $decodedCombatOverrides =
            json_decode(
                $combatOverrides,
                true
            );

        $combatOverrides =
            is_array($decodedCombatOverrides)
                ? $decodedCombatOverrides
                : [];
    }

    if (!is_array($combatOverrides)) {
        $combatOverrides = [];
    }

    /*
    |--------------------------------------------------------------------------
    | ARMADURA
    |--------------------------------------------------------------------------
    */

    $armorConfig = data_get(
        $combatOverrides,
        'armor',
        []
    );

    $storedArmorMode =
        $armorConfig['mode'] ??
        ['dex'];

    $armorMode =
        is_array($storedArmorMode)
            ? array_values($storedArmorMode)
            : [$storedArmorMode];

    if (
        empty($armorMode) &&
        !array_key_exists(
            'mode',
            $armorConfig
        )
    ) {
        $armorMode = ['dex'];
    }

    $shieldEnabled =
        (bool) (
            $armorConfig['shield_enabled'] ??
            false
        );

    $shieldBonus =
        (int) (
            $armorConfig['shield_bonus'] ??
            2
        );

    $namedAcBonuses = collect(
        $armorConfig['bonuses'] ?? []
    )
        ->map(
            fn ($bonus) => [
                'name' => trim(
                    (string) (
                        $bonus['name'] ?? ''
                    )
                ),
                'value' => (int) (
                    $bonus['value'] ?? 0
                ),
            ]
        )
        ->filter(
            fn ($bonus) =>
                $bonus['name'] !== ''
        )
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | DADOS DE VIDA
    |--------------------------------------------------------------------------
    */

    $hitDice = collect(
        $combat?->hit_dice ?? []
    )
        ->map(
            fn ($hitDie) => [
                'die' => strtolower(
                    (string) (
                        $hitDie['die'] ??
                        'd8'
                    )
                ),
                'current' => max(
                    0,
                    (int) (
                        $hitDie['current'] ??
                        0
                    )
                ),
                'maximum' => max(
                    0,
                    (int) (
                        $hitDie['maximum'] ??
                        0
                    )
                ),
            ]
        )
        ->filter(
            fn ($die) =>
                $die['maximum'] > 0
        )
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | DADOS RÁPIDOS
    |--------------------------------------------------------------------------
    */

    $speed =
        $combat?->speed;

    $initiative =
        $combat?->initiative_bonus;
@endphp


@once
    @push('styles')
        <style>
            @keyframes damage-hit {
                0%, 100% {
                    transform: translate3d(0, 0, 0) scale(1);
                    filter: brightness(1);
                }

                8% {
                    transform: translateX(-7px) rotate(-1deg) scale(.992);
                }

                18% {
                    transform: translateX(6px) rotate(.8deg);
                }

                30% {
                    transform: translateX(-5px);
                }

                42% {
                    transform: translateX(4px);
                }

                56% {
                    transform: translateX(-2px);
                }

                70% {
                    transform: translateX(1px);
                }
            }

            @keyframes damage-flash {
                0%, 100% {
                    box-shadow:
                        inset 0 0 0 rgba(185,28,28,0),
                        0 0 0 rgba(220,38,38,0);
                }

                25% {
                    box-shadow:
                        inset 0 0 70px rgba(185,28,28,.55),
                        0 0 20px rgba(220,38,38,.20);
                }

                60% {
                    box-shadow:
                        inset 0 0 25px rgba(185,28,28,.25),
                        0 0 8px rgba(220,38,38,.08);
                }
            }

            @keyframes photo-damage {
                0%, 100% {
                    transform: translate3d(0,0,0) scale(1);
                    filter: brightness(1);
                }

                15% {
                    transform: translateX(-5px) rotate(-1deg);
                    filter: brightness(1.12);
                }

                30% {
                    transform: translateX(5px) rotate(.8deg);
                    box-shadow:
                        inset 0 0 30px rgba(185,28,28,.35),
                        0 0 15px rgba(220,38,38,.18);
                }

                50% {
                    transform: translateX(-3px);
                }

                70% {
                    transform: translateX(2px);
                    filter: brightness(1);
                }
            }

            @keyframes heal {
                0%, 100% {
                    transform: scale(1);
                    filter: brightness(1);
                }

                18% {
                    transform: scale(1.018);
                }

                45% {
                    transform: scale(1.032);
                    filter: brightness(1.25);
                }

                75% {
                    transform: scale(1.01);
                    filter: brightness(1.08);
                }
            }

            @keyframes heal-flash {
                0%, 100% {
                    box-shadow:
                        inset 0 0 0 rgba(34,197,94,0),
                        0 0 0 rgba(34,197,94,0);
                }

                30% {
                    box-shadow:
                        inset 0 0 45px rgba(34,197,94,.22),
                        0 0 26px rgba(34,197,94,.45);
                }

                70% {
                    box-shadow:
                        inset 0 0 18px rgba(34,197,94,.10),
                        0 0 8px rgba(34,197,94,.12);
                }
            }

            @keyframes shield {
                0%, 100% {
                    box-shadow:
                        0 0 4px rgba(59,130,246,.12);
                }

                50% {
                    box-shadow:
                        0 0 12px rgba(56,189,248,.45),
                        0 0 28px rgba(96,165,250,.18);
                }
            }

            @keyframes bloodied {
                0%, 100% {
                    box-shadow:
                        0 0 0 rgba(127,29,29,.10);
                }

                50% {
                    box-shadow:
                        0 0 18px rgba(127,29,29,.30),
                        inset 0 0 18px rgba(127,29,29,.18);
                }
            }

            @keyframes critical {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 rgba(220,38,38,.20);
                    filter: brightness(1);
                }

                50% {
                    transform: scale(1.01);

                    box-shadow:
                        0 0 26px rgba(220,38,38,.55),
                        inset 0 0 22px rgba(220,38,38,.28);

                    filter: brightness(1.05);
                }
            }

            @keyframes bonus-hp {
                0%, 100% {
                    filter: brightness(1);
                }

                50% {
                    filter: brightness(1.08);

                    box-shadow:
                        0 0 9px rgba(217,164,65,.32);
                }
            }

            @keyframes death-pulse {
                0%, 100% {
                    box-shadow:
                        0 0 0 rgba(127,29,29,.04);
                }

                50% {
                    box-shadow:
                        0 0 22px rgba(127,29,29,.16);
                }
            }

            @keyframes death-tab {
                0% {
                    transform: translateY(-8px);
                    opacity: 0;
                }

                100% {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes death-dead {
                0% {
                    opacity: 0;
                    transform: translate(-50%,-50%) scale(.92);
                }

                100% {
                    opacity: 1;
                    transform: translate(-50%,-50%) scale(1);
                }
            }

            .animate-damage-red {
                animation:
                    damage-hit .55s cubic-bezier(.18,.89,.32,1.2),
                    damage-flash .55s ease-out;
            }

            .animate-damage-photo {
                animation:
                    photo-damage .55s cubic-bezier(.18,.89,.32,1.2);
            }

            .animate-heal-green {
                animation:
                    heal .65s cubic-bezier(.2,.9,.2,1),
                    heal-flash .75s ease-out;
            }

            .temp-hp-active {
                animation:
                    shield 3s ease-in-out infinite;
            }

            .bonus-hp-active {
                animation:
                    bonus-hp 3s ease-in-out infinite;
            }

            .bloodied-card {
                animation:
                    bloodied 2.1s ease-in-out infinite;
            }

            .critical-card {
                animation:
                    critical 2.5s ease-in-out infinite;
            }

            .death-drawer-active {
                animation:
                    death-pulse 2.2s ease-in-out infinite;
            }

            .death-tab-enter {
                animation:
                    death-tab .22s ease-out;
            }

            .death-dead-overlay {
                animation:
                    death-dead .22s ease-out;
            }

            .clip-shield {
                clip-path: polygon(
                    50% 100%,
                    0 85%,
                    0 0,
                    100% 0,
                    100% 85%
                );
            }

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type="number"] {
                -moz-appearance: textfield;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTINUIDADE VISUAL DO HEADER
            |--------------------------------------------------------------------------
            */

            .hero-header-segment {
                position: relative;
                border-color: rgba(205,187,159,.72);
                background: #faf7f2;
            }

            .hero-header-segment::after {
                content: "";
                position: absolute;
                inset: 3px;
                pointer-events: none;
                border: 1px solid rgba(216,199,171,.72);
            }
        </style>
    @endpush
@endonce


<div
    x-data="{

        /*
        |--------------------------------------------------------------------------
        | VIDA
        |--------------------------------------------------------------------------
        */

        currentHp: {{ $currentHp }},
        ghostHp: {{ $currentHp }},
        maxHp: {{ $maxHp }},

        temporaryHp: {{ $temporaryHp }},
        temporaryMaxHp: {{ $temporaryMaxHp }},


        /*
        |--------------------------------------------------------------------------
        | DEATH SAVES
        |--------------------------------------------------------------------------
        */

        deathSaveSuccesses: {{ $deathSuccesses }},
        deathSaveFailures: {{ $deathFailures }},

        deathDrawerOpen: false,
        deathRolling: false,
        deathRollResult: null,


        /*
        |--------------------------------------------------------------------------
        | EDIÇÃO DE VIDA
        |--------------------------------------------------------------------------
        */

        editingHp: false,
        directHp: {{ $currentHp }},

        editingTempHp: false,
        directTempHp: {{ $temporaryHp }},

        hpSettingsOpen: false,
        hpSettingsStep: 'confirm',

        directMaxHp: {{ $maxHp }},
        directTemporaryMaxHp: {{ $temporaryMaxHp }},


        /*
        |--------------------------------------------------------------------------
        | ARMADURA
        |--------------------------------------------------------------------------
        */

        armorOpen: false,

        armorMode:
            @js($armorMode),

        shieldEnabled:
            {{ $shieldEnabled ? 'true' : 'false' }},

        shieldBonus:
            {{ $shieldBonus }},

        armorBonuses:
            @js($namedAcBonuses),

        savingArmor: false,

        combatOverrides:
            @js($combatOverrides),


        /*
        |--------------------------------------------------------------------------
        | DESCANSO
        |--------------------------------------------------------------------------
        */

        resting: false,
        restingType: null,
        restError: null,


        /*
        |--------------------------------------------------------------------------
        | ANIMAÇÕES
        |--------------------------------------------------------------------------
        */

        shakingRed: false,
        shakingBlue: false,
        flashingGreen: false,


        /*
        |--------------------------------------------------------------------------
        | VIDA CALCULADA
        |--------------------------------------------------------------------------
        */

        get effectiveMaxHp() {
            return Math.max(
                1,
                this.maxHp +
                this.temporaryMaxHp
            );
        },


        get hpPercent() {
            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        this.currentHp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get normalHpPercent() {
            const hp =
                Math.min(
                    this.currentHp,
                    this.maxHp
                );

            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        hp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get bonusHpPercent() {
            const bonus =
                Math.max(
                    0,
                    this.currentHp -
                    this.maxHp
                );

            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        bonus /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get ghostHpPercent() {
            return Math.max(
                0,
                Math.min(
                    100,
                    (
                        this.ghostHp /
                        this.effectiveMaxHp
                    ) * 100
                )
            );
        },


        get tempHpPercent() {
            return this.hpPercent;
        },


        /*
        |--------------------------------------------------------------------------
        | ESTADO DO PERSONAGEM
        |--------------------------------------------------------------------------
        */

        get isDowned() {
            return this.currentHp <= 0;
        },


        get isDead() {
            return (
                this.isDowned &&
                this.deathSaveFailures >= 3
            );
        },


        get isStable() {
            return (
                this.isDowned &&
                this.deathSaveSuccesses >= 3
            );
        },


        get isDying() {
            return (
                this.isDowned &&
                !this.isDead &&
                !this.isStable
            );
        },


        get isCritical() {
            return (
                !this.isDowned &&
                this.hpPercent <= 25
            );
        },


        get isBloodied() {
            return (
                !this.isDowned &&
                this.hpPercent <= 50 &&
                this.hpPercent > 25
            );
        },


        get statusLabel() {
            if (this.isDead) {
                return 'Morto';
            }

            if (this.isStable) {
                return 'Estabilizado';
            }

            if (this.isDowned) {
                return 'Caído';
            }

            if (this.isCritical) {
                return 'Crítico';
            }

            if (this.isBloodied) {
                return 'Ferido';
            }

            return 'Estável';
        },


        get statusClass() {
            if (this.isDead) {
                return 'text-red-700';
            }

            if (this.isStable) {
                return 'text-emerald-700';
            }

            if (
                this.isDowned ||
                this.isCritical
            ) {
                return 'text-red-700';
            }

            if (this.isBloodied) {
                return 'text-amber-700';
            }

            return 'text-[#8c6239]';
        },


        /*
        |--------------------------------------------------------------------------
        | ARMADURA CALCULADA
        |--------------------------------------------------------------------------
        */

        get armorBaseBonus() {

            const modifiers = {
                str: {{ $abilityModifiers['str'] }},
                dex: {{ $abilityModifiers['dex'] }},
                con: {{ $abilityModifiers['con'] }},
                int: {{ $abilityModifiers['int'] }},
                wis: {{ $abilityModifiers['wis'] }},
                cha: {{ $abilityModifiers['cha'] }}
            };

            return this.armorMode.reduce(
                (total, ability) =>
                    total +
                    (
                        modifiers[ability] ??
                        0
                    ),
                0
            );
        },


        get armorNamedBonusTotal() {
            return this.armorBonuses.reduce(
                (total, bonus) =>
                    total +
                    (
                        parseInt(
                            bonus.value
                        ) || 0
                    ),
                0
            );
        },


        get totalAc() {
            return (
                10 +
                this.armorBaseBonus +
                (
                    this.shieldEnabled
                        ? (
                            parseInt(
                                this.shieldBonus
                            ) || 0
                        )
                        : 0
                ) +
                this.armorNamedBonusTotal
            );
        },


        /*
        |--------------------------------------------------------------------------
        | PERSISTÊNCIA DE COMBATE
        |--------------------------------------------------------------------------
        */

        async persistCombat(fields) {

            /*
            |--------------------------------------------------------------------------
            | MERGE DE OVERRIDES
            |--------------------------------------------------------------------------
            |
            | CharacterCombatController persiste o array completo de overrides.
            | Por isso, quando um componente envia somente o próprio namespace,
            | preservamos os namespaces que já existiam.
            |
            */

            if (
                Object.prototype.hasOwnProperty.call(
                    fields,
                    'overrides'
                )
            ) {

                let incomingOverrides =
                    fields.overrides;

                if (
                    typeof incomingOverrides ===
                    'string'
                ) {

                    try {

                        incomingOverrides =
                            JSON.parse(
                                incomingOverrides
                            );

                    } catch (error) {

                        incomingOverrides = {};
                    }
                }

                if (
                    !incomingOverrides ||
                    typeof incomingOverrides !==
                        'object' ||
                    Array.isArray(
                        incomingOverrides
                    )
                ) {

                    incomingOverrides = {};
                }

                this.combatOverrides = {
                    ...(
                        this.combatOverrides ||
                        {}
                    ),

                    ...incomingOverrides
                };

                fields = {
                    ...fields,

                    overrides:
                        JSON.stringify(
                            this.combatOverrides
                        )
                };
            }


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

            Object.entries(fields).forEach(
                ([key, value]) => {

                    formData.append(
                        key,
                        value
                    );

                }
            );

            try {

                const response =
                    await fetch(
                        '{{ route('characters.combat.update', $character) }}',
                        {
                            method: 'POST',

                            body:
                                formData,

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error(
                        'Falha ao atualizar combate.'
                    );
                }


                const data =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | SINCRONIZA OVERRIDES RETORNADOS
                |--------------------------------------------------------------------------
                */

                let serverOverrides =
                    data?.combat?.overrides;

                if (
                    typeof serverOverrides ===
                    'string'
                ) {

                    try {

                        serverOverrides =
                            JSON.parse(
                                serverOverrides
                            );

                    } catch (error) {

                        serverOverrides = null;
                    }
                }

                if (
                    serverOverrides &&
                    typeof serverOverrides ===
                        'object' &&
                    !Array.isArray(
                        serverOverrides
                    )
                ) {

                    this.combatOverrides =
                        serverOverrides;
                }


                return data;

            } catch (error) {

                console.error(
                    'Erro ao atualizar combate:',
                    error
                );

                return null;
            }
        },



        /*
        |--------------------------------------------------------------------------
        | APLICA RESPOSTA DO DESCANSO
        |--------------------------------------------------------------------------
        */

        applyRestCombatState(combat) {

            if (!combat) {
                return;
            }

            if (
                combat.current_hp !== undefined &&
                combat.current_hp !== null
            ) {
                this.currentHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.current_hp
                        ) || 0
                    );
            }


            if (
                combat.max_hp !== undefined &&
                combat.max_hp !== null
            ) {
                this.maxHp =
                    Math.max(
                        1,
                        parseInt(
                            combat.max_hp
                        ) || 1
                    );
            }


            if (
                combat.temporary_hp !== undefined &&
                combat.temporary_hp !== null
            ) {
                this.temporaryHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.temporary_hp
                        ) || 0
                    );
            }


            if (
                combat.temporary_max_hp !== undefined &&
                combat.temporary_max_hp !== null
            ) {
                this.temporaryMaxHp =
                    Math.max(
                        0,
                        parseInt(
                            combat.temporary_max_hp
                        ) || 0
                    );
            }


            if (
                combat.death_save_successes !== undefined &&
                combat.death_save_successes !== null
            ) {
                this.deathSaveSuccesses =
                    Math.min(
                        3,
                        Math.max(
                            0,
                            parseInt(
                                combat.death_save_successes
                            ) || 0
                        )
                    );
            }


            if (
                combat.death_save_failures !== undefined &&
                combat.death_save_failures !== null
            ) {
                this.deathSaveFailures =
                    Math.min(
                        3,
                        Math.max(
                            0,
                            parseInt(
                                combat.death_save_failures
                            ) || 0
                        )
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | SINCRONIZA INPUTS
            |--------------------------------------------------------------------------
            */

            this.directHp =
                this.currentHp;

            this.ghostHp =
                this.currentHp;

            this.directTempHp =
                this.temporaryHp;

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;


            /*
            |--------------------------------------------------------------------------
            | MORTE
            |--------------------------------------------------------------------------
            */

            if (this.currentHp > 0) {

                this.deathDrawerOpen = false;
                this.deathRollResult = null;

            }
        },


        /*
        |--------------------------------------------------------------------------
        | DESCANSO
        |--------------------------------------------------------------------------
        */

        async performRest(type) {

            if (this.resting) {
                return null;
            }

            if (
                type !== 'short' &&
                type !== 'long'
            ) {
                return null;
            }


            this.resting = true;
            this.restingType = type;
            this.restError = null;


            try {

                const response =
                    await fetch(
                        '{{ route('characters.rest', $character) }}',
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
                                    'XMLHttpRequest'
                            },

                            body:
                                JSON.stringify({
                                    type: type
                                })
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
                        data?.message ??
                        'Não foi possível realizar o descanso.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ATUALIZA COMBATE
                |--------------------------------------------------------------------------
                */

                this.applyRestCombatState(
                    data?.combat
                );


                /*
                |--------------------------------------------------------------------------
                | ANIMAÇÃO
                |--------------------------------------------------------------------------
                */

                if (type === 'long') {

                    this.flashingGreen = true;

                    setTimeout(() => {
                        this.flashingGreen = false;
                    }, 900);
                }


                /*
                |--------------------------------------------------------------------------
                | EVENTO GLOBAL
                |--------------------------------------------------------------------------
                |
                | Todos os módulos da ficha podem ouvir:
                |
                | @character-rest-completed.window
                |
                | e ler:
                |
                | $event.detail.rest.type
                | $event.detail.combat
                |
                */

                window.dispatchEvent(
                    new CustomEvent(
                        'character-rest-completed',
                        {
                            detail: data
                        }
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | EVENTOS ESPECÍFICOS
                |--------------------------------------------------------------------------
                */

                window.dispatchEvent(
                    new CustomEvent(
                        type === 'long'
                            ? 'character-long-rest-completed'
                            : 'character-short-rest-completed',
                        {
                            detail: data
                        }
                    )
                );


                return data;

            } catch (error) {

                console.error(
                    'Erro ao realizar descanso:',
                    error
                );

                this.restError =
                    error?.message ??
                    'Não foi possível realizar o descanso.';

                return null;

            } finally {

                this.resting = false;
                this.restingType = null;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | DEATH SAVES
        |--------------------------------------------------------------------------
        */

        openDeathDrawer() {

            if (!this.isDying) {
                return;
            }

            this.deathDrawerOpen = true;
        },


        closeDeathDrawer() {

            this.deathDrawerOpen = false;
            this.deathRollResult = null;
        },


        async setDeathSave(type, index) {

            if (!this.isDying) {
                return;
            }

            const key =
                type === 'success'
                    ? 'deathSaveSuccesses'
                    : 'deathSaveFailures';

            if (this[key] === index) {

                this[key] =
                    Math.max(
                        0,
                        index - 1
                    );

            } else {

                this[key] =
                    index;
            }


            await this.persistCombat({
                death_save_successes:
                    this.deathSaveSuccesses,

                death_save_failures:
                    this.deathSaveFailures
            });


            if (
                this.isStable ||
                this.isDead
            ) {
                this.deathDrawerOpen = false;
            }
        },


        async rollDeathSave() {

            if (
                !this.isDying ||
                this.deathRolling
            ) {
                return;
            }


            this.deathRolling = true;
            this.deathRollResult = null;

            let roll = 1;


            try {

                const response =
                    await fetch(
                        '{{ url('/api/roll') }}',
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
                                    'XMLHttpRequest'
                            },

                            body:
                                JSON.stringify({
                                    expression:
                                        '1d20'
                                })
                        }
                    );


                if (response.ok) {

                    const data =
                        await response.json();

                    roll =
                        parseInt(
                            data?.data?.total ??
                            data?.data?.result ??
                            data?.data?.value ??
                            data?.data?.sum ??
                            data?.total ??
                            data?.result ??
                            1
                        ) || 1;
                }

            } catch (error) {

                roll =
                    Math.floor(
                        Math.random() * 20
                    ) + 1;
            }


            this.deathRollResult =
                roll;


            if (roll === 20) {

                this.currentHp = 1;
                this.directHp = 1;
                this.ghostHp = 1;

                this.deathSaveSuccesses = 0;
                this.deathSaveFailures = 0;


                await this.persistCombat({
                    current_hp: 1,
                    death_save_successes: 0,
                    death_save_failures: 0
                });


                this.flashingGreen = true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 900);


                this.deathDrawerOpen =
                    false;

            } else if (roll >= 10) {

                this.deathSaveSuccesses =
                    Math.min(
                        3,
                        this.deathSaveSuccesses + 1
                    );


                await this.persistCombat({
                    death_save_successes:
                        this.deathSaveSuccesses
                });


                if (this.isStable) {
                    this.deathDrawerOpen = false;
                }

            } else {

                this.deathSaveFailures =
                    Math.min(
                        3,
                        this.deathSaveFailures + 1
                    );


                await this.persistCombat({
                    death_save_failures:
                        this.deathSaveFailures
                });


                if (this.isDead) {
                    this.deathDrawerOpen = false;
                }
            }


            this.deathRolling =
                false;
        },


        /*
        |--------------------------------------------------------------------------
        | VIDA
        |--------------------------------------------------------------------------
        */

        changeHp(amount) {

            if (amount < 0) {

                let damage =
                    Math.abs(amount);


                if (this.temporaryHp > 0) {

                    const absorbed =
                        Math.min(
                            this.temporaryHp,
                            damage
                        );

                    this.temporaryHp -= absorbed;
                    damage -= absorbed;

                    this.directTempHp =
                        this.temporaryHp;

                    this.shakingBlue =
                        true;


                    setTimeout(() => {
                        this.shakingBlue = false;
                    }, 700);
                }


                if (damage > 0) {

                    const wasAlive =
                        this.currentHp > 0;


                    this.currentHp =
                        Math.max(
                            0,
                            this.currentHp -
                            damage
                        );


                    this.directHp =
                        this.currentHp;


                    this.shakingRed =
                        true;


                    setTimeout(() => {
                        this.shakingRed = false;
                    }, 700);


                    if (
                        wasAlive &&
                        this.currentHp === 0
                    ) {

                        this.deathRollResult =
                            null;

                        this.deathDrawerOpen =
                            true;
                    }
                }


                setTimeout(() => {

                    this.ghostHp =
                        this.currentHp;

                }, 350);


                this.persistCombat({
                    current_hp:
                        this.currentHp,

                    temporary_hp:
                        this.temporaryHp
                });


                return;
            }


            if (amount > 0) {

                this.currentHp =
                    Math.min(
                        this.effectiveMaxHp,
                        this.currentHp +
                        amount
                    );


                this.directHp =
                    this.currentHp;

                this.ghostHp =
                    this.currentHp;


                if (this.currentHp > 0) {

                    this.deathSaveSuccesses = 0;
                    this.deathSaveFailures = 0;

                    this.deathDrawerOpen = false;
                    this.deathRollResult = null;
                }


                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);


                this.persistCombat({
                    current_hp:
                        this.currentHp,

                    death_save_successes:
                        this.deathSaveSuccesses,

                    death_save_failures:
                        this.deathSaveFailures
                });
            }
        },


        async handleDirectHp() {

            if (!this.editingHp) {
                return;
            }


            this.editingHp =
                false;


            let targetHp =
                parseInt(
                    this.directHp
                );


            if (Number.isNaN(targetHp)) {

                this.directHp =
                    this.currentHp;

                return;
            }


            targetHp =
                Math.max(
                    0,
                    Math.min(
                        this.effectiveMaxHp,
                        targetHp
                    )
                );


            if (
                targetHp ===
                this.currentHp
            ) {
                return;
            }


            const wasDowned =
                this.isDowned;


            if (
                targetHp <
                this.currentHp
            ) {

                this.shakingRed =
                    true;


                setTimeout(() => {
                    this.shakingRed = false;
                }, 700);

            } else {

                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);
            }


            this.currentHp =
                targetHp;

            this.directHp =
                targetHp;

            this.ghostHp =
                targetHp;


            if (
                wasDowned &&
                targetHp > 0
            ) {

                this.deathSaveSuccesses = 0;
                this.deathSaveFailures = 0;
                this.deathDrawerOpen = false;
                this.deathRollResult = null;
            }


            if (targetHp === 0) {

                this.deathRollResult =
                    null;

                this.deathDrawerOpen =
                    true;
            }


            await this.persistCombat({
                current_hp:
                    this.currentHp,

                death_save_successes:
                    this.deathSaveSuccesses,

                death_save_failures:
                    this.deathSaveFailures
            });
        },


        async handleTemporaryHp() {

            if (!this.editingTempHp) {
                return;
            }


            this.editingTempHp =
                false;


            let value =
                parseInt(
                    this.directTempHp
                );


            if (Number.isNaN(value)) {
                value = 0;
            }


            this.temporaryHp =
                Math.max(
                    0,
                    value
                );


            await this.persistCombat({
                temporary_hp:
                    this.temporaryHp
            });


            this.shakingBlue =
                true;


            setTimeout(() => {
                this.shakingBlue = false;
            }, 700);
        },


        /*
        |--------------------------------------------------------------------------
        | CONFIGURAÇÃO DE VIDA
        |--------------------------------------------------------------------------
        */

        openHpSettings() {

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;

            this.hpSettingsStep =
                'confirm';

            this.hpSettingsOpen =
                true;
        },


        beginHpSettings() {

            this.hpSettingsStep =
                'edit';


            this.$nextTick(() => {

                this.$refs
                    .maxHpSettingsInput
                    ?.focus();

                this.$refs
                    .maxHpSettingsInput
                    ?.select();
            });
        },


        closeHpSettings() {

            this.hpSettingsOpen = false;

            this.hpSettingsStep =
                'confirm';

            this.directMaxHp =
                this.maxHp;

            this.directTemporaryMaxHp =
                this.temporaryMaxHp;
        },


        async saveHpSettings() {

            let newMaxHp =
                parseInt(
                    this.directMaxHp
                );

            let newTemporaryMaxHp =
                parseInt(
                    this.directTemporaryMaxHp
                );


            if (Number.isNaN(newMaxHp)) {

                newMaxHp =
                    this.maxHp;
            }


            if (
                Number.isNaN(
                    newTemporaryMaxHp
                )
            ) {

                newTemporaryMaxHp =
                    this.temporaryMaxHp;
            }


            newMaxHp =
                Math.max(
                    1,
                    newMaxHp
                );


            newTemporaryMaxHp =
                Math.max(
                    0,
                    newTemporaryMaxHp
                );


            const extraHpDifference =
                newTemporaryMaxHp -
                this.temporaryMaxHp;


            this.maxHp =
                newMaxHp;


            this.temporaryMaxHp =
                newTemporaryMaxHp;


            if (
                extraHpDifference > 0
            ) {

                this.currentHp =
                    Math.min(
                        this.effectiveMaxHp,
                        this.currentHp +
                        extraHpDifference
                    );
            }


            if (
                this.currentHp >
                this.effectiveMaxHp
            ) {

                this.currentHp =
                    this.effectiveMaxHp;
            }


            this.directHp =
                this.currentHp;

            this.ghostHp =
                this.currentHp;


            await this.persistCombat({
                max_hp:
                    this.maxHp,

                temporary_max_hp:
                    this.temporaryMaxHp,

                current_hp:
                    this.currentHp
            });


            if (
                extraHpDifference > 0
            ) {

                this.flashingGreen =
                    true;


                setTimeout(() => {
                    this.flashingGreen = false;
                }, 800);
            }


            this.closeHpSettings();
        },


        /*
        |--------------------------------------------------------------------------
        | ARMADURA
        |--------------------------------------------------------------------------
        */

        openArmor() {
            this.armorOpen = true;
        },


        addArmorBonus() {

            this.armorBonuses.push({
                name: 'Novo bônus',
                value: 1
            });
        },


        removeArmorBonus(index) {

            this.armorBonuses.splice(
                index,
                1
            );
        },


        async saveArmor() {

            this.savingArmor =
                true;


            await this.persistCombat({
                overrides:
                    JSON.stringify({
                        armor: {
                            mode:
                                this.armorMode,

                            shield_enabled:
                                this.shieldEnabled,

                            shield_bonus:
                                parseInt(
                                    this.shieldBonus
                                ) || 0,

                            bonuses:
                                this.armorBonuses.map(
                                    bonus => ({
                                        name:
                                            bonus.name,

                                        value:
                                            parseInt(
                                                bonus.value
                                            ) || 0
                                    })
                                )
                        }
                    })
            });


            this.savingArmor =
                false;

            this.armorOpen =
                false;
        }
    }"

    :class="{
        'bloodied-card': isBloodied,
        'critical-card': isCritical
    }"

    class="
        relative
        mb-6
        overflow-visible
        rounded-2xl
        border
        border-[#cdbb9f]/60
        bg-[#faf8f2]
        shadow-md
    "
>


    {{-- ============================================================
         HEADER PRINCIPAL
    ============================================================= --}}

    <div class="px-4 py-3 sm:px-5 sm:py-4">

        <div
            class="
                flex
                min-w-0
                flex-col
                gap-3

                xl:flex-row
                xl:items-stretch
                xl:gap-0
            "
        >

            {{-- ====================================================
                 HERO DATA
            ===================================================== --}}

            <div
                class="
                    relative
                    z-20
                    min-w-0
                    flex-1

                    xl:pr-0
                "
            >

                @include(
                    'characters.components.hero-data',
                    [
                        'character' => $character,
                        'imageUrl' => $imageUrl,
                        'className' => $className,
                        'subclassName' => $subclassName,
                        'proficiencyBonus' => $proficiencyBonus,
                        'classes' => $classes,
                        'hasMulticlass' => $hasMulticlass,
                    ]
                )

            </div>


            {{-- ====================================================
                 CA + VIDA + DESCANSO
            ===================================================== --}}

            <div
                class="
                    relative
                    z-10
                    flex
                    min-w-0
                    overflow-visible

                    rounded-xl
                    border
                    border-[#cdbb9f]/70
                    bg-[#faf8f2]

                    shadow-[0_2px_6px_rgba(83,21,15,.05)]

                    xl:-ml-2.5
                    xl:h-[176px]
                    xl:w-[550px]
                    xl:shrink-0
                    xl:rounded-l-none
                    xl:rounded-r-[12px]
                    xl:border-l-0
                "
            >

                {{-- MOLDURA INTERNA --}}

                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-[3px]
                        z-0

                        rounded-[9px]
                        border
                        border-[#d8c7ab]/60

                        xl:rounded-l-none
                        xl:border-l-0
                    "
                ></div>


                {{-- PONTE COM HERO-DATA --}}

                <div
                    class="
                        pointer-events-none
                        absolute
                        -left-3
                        bottom-[3px]
                        top-[3px]
                        hidden
                        w-4
                        bg-[#faf8f2]

                        xl:block
                    "
                ></div>


                {{-- =================================================
                     CLASSE DE ARMADURA
                ================================================== --}}

                <div
                    class="
                        relative
                        z-10
                        flex
                        w-[92px]
                        shrink-0
                        items-start
                        justify-center

                        pb-2
                        pl-2
                        pr-2.5
                        pt-2
                    "
                >

                    <div
                        class="
                            pointer-events-none
                            absolute
                            bottom-3
                            right-0
                            top-3
                            w-px
                            bg-[#d8c7ab]/55
                        "
                    ></div>


                    <div class="relative mt-0.5">

                        @include(
                            'characters.components.hero-armor',
                            [
                                'character' => $character,
                                'abilityScores' => $abilityScores,
                                'abilityModifiers' => $abilityModifiers,
                                'armorMode' => $armorMode,
                                'shieldEnabled' => $shieldEnabled,
                                'shieldBonus' => $shieldBonus,
                                'armorBonuses' => $namedAcBonuses,
                            ]
                        )

                    </div>

                </div>


                {{-- =================================================
                     VIDA
                ================================================== --}}

                <div
                    class="
                        relative
                        z-10
                        flex
                        min-w-0
                        flex-1
                        flex-col

                        px-3
                        pb-2
                        sm:px-3.5
                    "
                >

                    <div
                        class="
                            flex
                            min-h-0
                            w-full
                            flex-1
                            flex-col
                        "
                    >

                        {{-- =========================================
                             VIDA + HIT DICE
                        ========================================== --}}

                        <div class="relative w-full pt-2.5">

                            {{-- HIT DICE --}}

                            <div
                                class="
                                    pointer-events-auto
                                    relative
                                    z-30
                                    -mb-5
                                    flex
                                    justify-end
                                    pr-0.5
                                "
                            >

                                @include(
                                    'characters.components.hero-hit-dice',
                                    [
                                        'character' => $character,
                                        'hitDice' => $hitDice,
                                    ]
                                )

                            </div>


                            {{-- BARRA DE VIDA --}}

                            <div
                                class="
                                    relative
                                    z-20
                                    w-full
                                    origin-top
                                "
                            >

                                @include(
                                    'characters.components.hero-life',
                                    [
                                        'character' => $character,
                                        'maxHp' => $maxHp,
                                        'temporaryMaxHp' => $temporaryMaxHp,
                                    ]
                                )

                            </div>

                        </div>


                        {{-- =========================================
                             DADOS RÁPIDOS
                        ========================================== --}}

                        <div
                            class="
                                mt-auto
                                pt-1.5
                            "
                        >

                            @include(
                                'characters.components.hero-quick-stats',
                                [
                                    'character' =>
                                        $character,

                                    'proficiencyBonus' =>
                                        $proficiencyBonus,
                                ]
                            )

                        </div>


                        {{-- =========================================
                             RODAPÉ
                        ========================================== --}}

                        <div
                            class="
                                mt-1.5
                                flex
                                h-7
                                items-center
                                gap-2
                            "
                        >

                            {{-- DECORAÇÃO --}}

                            <div
                                class="
                                    flex
                                    min-w-0
                                    flex-1
                                    items-center
                                "
                            >

                                <div
                                    class="
                                        h-px
                                        flex-1
                                        bg-[#d8c7ab]/45
                                    "
                                ></div>

                                <span
                                    class="
                                        mx-2
                                        h-1
                                        w-1
                                        shrink-0
                                        rounded-full
                                        bg-[#cdbb9f]/80
                                    "
                                ></span>

                                <div
                                    class="
                                        h-px
                                        w-6
                                        bg-[#d8c7ab]/45
                                    "
                                ></div>

                            </div>


                            {{-- =====================================
                                 DESCANSO
                            ====================================== --}}

                            <div
                                x-data="{
                                    restOpen: false
                                }"

                                @click.outside="
                                    restOpen = false
                                "

                                @keydown.escape.window="
                                    restOpen = false
                                "

                                @character-rest-completed.window="
                                    restOpen = false
                                "

                                class="
                                    relative
                                    shrink-0
                                "
                            >

                                {{-- FOGUEIRA --}}

                                <button
                                    type="button"

                                    @click="
                                        if (!resting) {
                                            restOpen = !restOpen
                                        }
                                    "

                                    :aria-expanded="
                                        restOpen
                                    "

                                    :disabled="
                                        resting
                                    "

                                    class="
                                        group
                                        flex
                                        h-7
                                        w-7
                                        items-center
                                        justify-center

                                        rounded-md
                                        border
                                        border-[#d8c7ab]/70
                                        bg-[#f4f1e8]

                                        text-[#8c6239]

                                        shadow-[0_1px_3px_rgba(83,21,15,.05)]

                                        transition-all
                                        duration-150

                                        hover:border-[#cdbb9f]
                                        hover:bg-[#efe9dc]
                                        hover:text-[#6b1d14]

                                        active:scale-95

                                        disabled:cursor-wait
                                        disabled:opacity-60
                                    "

                                    title="Descanso"
                                >

                                    <svg
                                        class="
                                            h-[15px]
                                            w-[15px]
                                            transition-transform
                                            duration-150
                                            group-hover:scale-105
                                        "

                                        :class="{
                                            'animate-pulse':
                                                resting
                                        }"

                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >

                                        <path
                                            d="
                                                M12.2 3.5
                                                c.6 2.7-1.8 3.8-1.6 5.9
                                                .1 1.1.8 1.7 1.4 2
                                                .1-1.7 1.1-2.6 2.1-3.5
                                                .5 2.1 2.9 3.4 2.9 6.1
                                                0 2.8-2.2 4.5-5 4.5
                                                S7 16.7 7 14
                                                c0-2.6 1.7-4.4 3.1-6
                                                .1-1.7 1.1-3.2 2.1-4.5Z
                                            "
                                        />

                                        <path
                                            d="m6.5 20 11-2.5"
                                        />

                                        <path
                                            d="m6.5 17.5 11 2.5"
                                        />

                                    </svg>

                                </button>


                                {{-- =================================
                                     MENU
                                ================================== --}}

                                <div
                                    x-show="
                                        restOpen
                                    "

                                    x-cloak

                                    x-transition:enter="
                                        transition
                                        ease-out
                                        duration-150
                                    "

                                    x-transition:enter-start="
                                        opacity-0
                                        translate-y-1
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
                                        duration-100
                                    "

                                    x-transition:leave-start="
                                        opacity-100
                                        translate-y-0
                                        scale-100
                                    "

                                    x-transition:leave-end="
                                        opacity-0
                                        translate-y-1
                                        scale-[.97]
                                    "

                                    class="
                                        absolute
                                        bottom-[calc(100%+6px)]
                                        right-0
                                        z-50

                                        w-[196px]

                                        overflow-hidden
                                        rounded-xl
                                        border
                                        border-[#cdbb9f]/80

                                        bg-[#faf8f2]

                                        p-1.5

                                        shadow-[0_8px_22px_rgba(83,21,15,.12)]
                                    "
                                >

                                    {{-- TÍTULO --}}

                                    <div
                                        class="
                                            px-2
                                            pb-1.5
                                            pt-1
                                        "
                                    >

                                        <p
                                            class="
                                                text-[6px]
                                                font-black
                                                uppercase
                                                tracking-[0.2em]
                                                text-[#8c6239]
                                            "
                                        >
                                            Descanso
                                        </p>

                                    </div>


                                    {{-- =============================
                                         CURTO
                                    ============================== --}}

                                    <button
                                        type="button"

                                        @click="
                                            performRest('short')
                                        "

                                        :disabled="
                                            resting
                                        "

                                        class="
                                            flex
                                            w-full
                                            items-center
                                            gap-2

                                            rounded-lg
                                            px-2
                                            py-2

                                            text-left

                                            transition

                                            hover:bg-[#f4f1e8]

                                            disabled:cursor-wait
                                            disabled:opacity-50
                                        "
                                    >

                                        <span
                                            class="
                                                flex
                                                h-6
                                                w-6
                                                shrink-0
                                                items-center
                                                justify-center

                                                rounded-md
                                                border
                                                border-[#d8c7ab]/55
                                                bg-[#faf8f2]

                                                font-serif
                                                text-[11px]
                                                font-black
                                                text-[#6b1d14]
                                            "
                                        >
                                            C
                                        </span>


                                        <span class="min-w-0">

                                            <span
                                                class="
                                                    block
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.12em]
                                                    text-[#53150f]
                                                "
                                            >
                                                Descanso Curto
                                            </span>


                                            <span
                                                x-show="
                                                    !resting ||
                                                    restingType !== 'short'
                                                "

                                                class="
                                                    mt-0.5
                                                    block
                                                    text-[7px]
                                                    leading-tight
                                                    text-[#8c6239]/75
                                                "
                                            >
                                                Recupera recursos de descanso curto
                                            </span>


                                            <span
                                                x-show="
                                                    resting &&
                                                    restingType === 'short'
                                                "

                                                x-cloak

                                                class="
                                                    mt-0.5
                                                    block
                                                    text-[7px]
                                                    font-bold
                                                    leading-tight
                                                    text-[#6b1d14]
                                                "
                                            >
                                                Descansando...
                                            </span>

                                        </span>

                                    </button>


                                    {{-- =============================
                                         LONGO
                                    ============================== --}}

                                    <button
                                        type="button"

                                        @click="
                                            performRest('long')
                                        "

                                        :disabled="
                                            resting
                                        "

                                        class="
                                            mt-0.5
                                            flex
                                            w-full
                                            items-center
                                            gap-2

                                            rounded-lg
                                            px-2
                                            py-2

                                            text-left

                                            transition

                                            hover:bg-[#f4f1e8]

                                            disabled:cursor-wait
                                            disabled:opacity-50
                                        "
                                    >

                                        <span
                                            class="
                                                flex
                                                h-6
                                                w-6
                                                shrink-0
                                                items-center
                                                justify-center

                                                rounded-md
                                                border
                                                border-[#d8c7ab]/55
                                                bg-[#faf8f2]

                                                font-serif
                                                text-[11px]
                                                font-black
                                                text-[#6b1d14]
                                            "
                                        >
                                            L
                                        </span>


                                        <span class="min-w-0">

                                            <span
                                                class="
                                                    block
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.12em]
                                                    text-[#53150f]
                                                "
                                            >
                                                Descanso Longo
                                            </span>


                                            <span
                                                x-show="
                                                    !resting ||
                                                    restingType !== 'long'
                                                "

                                                class="
                                                    mt-0.5
                                                    block
                                                    text-[7px]
                                                    leading-tight
                                                    text-[#8c6239]/75
                                                "
                                            >
                                                Recupera vida e todos os recursos
                                            </span>


                                            <span
                                                x-show="
                                                    resting &&
                                                    restingType === 'long'
                                                "

                                                x-cloak

                                                class="
                                                    mt-0.5
                                                    block
                                                    text-[7px]
                                                    font-bold
                                                    leading-tight
                                                    text-[#6b1d14]
                                                "
                                            >
                                                Descansando...
                                            </span>

                                        </span>

                                    </button>


                                    {{-- =============================
                                         ERRO
                                    ============================== --}}

                                    <div
                                        x-show="
                                            restError
                                        "

                                        x-cloak

                                        class="
                                            mx-1
                                            mt-1.5

                                            rounded-lg
                                            border
                                            border-red-200
                                            bg-red-50

                                            px-2
                                            py-1.5
                                        "
                                    >

                                        <p
                                            class="
                                                text-[7px]
                                                font-bold
                                                leading-tight
                                                text-red-700
                                            "

                                            x-text="
                                                restError
                                            "
                                        ></p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         MORTO
    ============================================================= --}}

    <div
        x-show="isDead"
        x-cloak

        class="
            death-dead-overlay
            pointer-events-none
            absolute
            left-1/2
            top-1/2
            z-40
            w-[80%]
            -translate-x-1/2
            -translate-y-1/2
        "
    >

        <div
            class="
                rounded-md
                border
                border-white/10
                bg-gray-900/65
                px-4
                py-2
                text-center
                shadow-lg
                backdrop-blur-[2px]
            "
        >

            <span
                class="
                    font-black
                    uppercase
                    tracking-[0.3em]
                    text-red-500
                "
            >
                Morto
            </span>

        </div>

    </div>


    {{-- ============================================================
         GAVETA DE MORTE
    ============================================================= --}}

    <div
        x-show="
            isDying &&
            deathDrawerOpen
        "

        x-cloak

        x-transition:enter="
            transition-[transform,opacity]
            duration-300
            ease-out
        "

        x-transition:enter-start="
            translate-y-[-18px]
            opacity-0
            scale-[.98]
        "

        x-transition:enter-end="
            translate-y-0
            opacity-100
            scale-100
        "

        x-transition:leave="
            transition-[transform,opacity]
            duration-200
            ease-in
        "

        x-transition:leave-start="
            translate-y-0
            opacity-100
            scale-100
        "

        x-transition:leave-end="
            translate-y-[-12px]
            opacity-0
            scale-[.99]
        "

        class="
            absolute
            left-1/2
            top-full
            z-50
            w-full
            max-w-md
            -translate-x-1/2
        "
    >

        <div
            class="
                death-drawer-active

                rounded-b-2xl

                border
                border-t-0
                border-red-200/80

                bg-[#f8f3eb]

                px-4
                pb-3
                pt-2

                shadow-[0_12px_30px_rgba(72,20,15,.14)]
            "
        >

            <div class="flex justify-center">

                <button
                    type="button"

                    @click="
                        closeDeathDrawer()
                    "

                    class="
                        group
                        flex
                        h-5
                        w-16
                        items-center
                        justify-center

                        rounded-b-lg

                        border-x
                        border-b
                        border-red-200/70

                        bg-[#f4e8dd]

                        transition-all
                        duration-200

                        hover:h-6
                        hover:bg-red-50
                    "

                    title="Recolher"
                >

                    <span
                        class="
                            h-1
                            w-6
                            rounded-full
                            bg-red-300

                            transition-all
                            duration-200

                            group-hover:w-8
                            group-hover:bg-red-400
                        "
                    ></span>

                </button>

            </div>


            <div
                class="
                    mt-2
                    flex
                    items-center
                    justify-center
                    gap-2
                "
            >

                <span
                    class="
                        h-px
                        flex-1
                        bg-red-200/70
                    "
                ></span>


                <span
                    class="
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.22em]
                        text-red-800
                    "
                >
                    Salvamentos Contra a Morte
                </span>


                <span
                    class="
                        h-px
                        flex-1
                        bg-red-200/70
                    "
                ></span>

            </div>


            <div
                class="
                    mt-3
                    flex
                    items-center
                    justify-between
                    gap-3
                "
            >

                {{-- SUCESSOS --}}

                <div
                    class="
                        flex
                        items-center
                        gap-1.5
                    "
                >

                    <template
                        x-for="n in 3"
                        :key="'success-' + n"
                    >

                        <button
                            type="button"

                            @click="
                                setDeathSave(
                                    'success',
                                    n
                                )
                            "

                            class="
                                relative
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center

                                rounded-full
                                border-2

                                transition-all
                                duration-200
                            "

                            :class="
                                n <= deathSaveSuccesses
                                    ? 'border-emerald-500 bg-emerald-500 text-white shadow-[0_0_10px_rgba(16,185,129,.25)] scale-105'
                                    : 'border-emerald-300 bg-white text-transparent hover:border-emerald-400 hover:bg-emerald-50'
                            "
                        >

                            <span
                                x-show="
                                    n <=
                                    deathSaveSuccesses
                                "

                                x-transition

                                class="
                                    text-sm
                                    font-black
                                "
                            >
                                ✓
                            </span>

                        </button>

                    </template>

                </div>


                {{-- ROLAR --}}

                <button
                    type="button"

                    @click="
                        rollDeathSave()
                    "

                    :disabled="
                        deathRolling
                    "

                    class="
                        group
                        relative
                        flex
                        h-10
                        min-w-[78px]
                        items-center
                        justify-center
                        gap-2
                        overflow-hidden

                        rounded-xl

                        border
                        border-[#53150f]

                        bg-[#6b1d14]

                        px-3

                        text-[#f4f1e8]

                        shadow-[0_4px_12px_rgba(83,21,15,.18)]

                        transition-all
                        duration-200

                        hover:-translate-y-0.5
                        hover:bg-[#53150f]

                        disabled:cursor-wait
                        disabled:opacity-60
                    "
                >

                    <span
                        class="
                            font-serif
                            text-base
                            font-black
                        "
                    >
                        d20
                    </span>


                    <span
                        x-show="
                            !deathRolling
                        "

                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest
                        "
                    >
                        Rolar
                    </span>


                    <span
                        x-show="
                            deathRolling
                        "

                        x-cloak

                        class="
                            text-[8px]
                            font-black
                            uppercase
                            tracking-widest
                        "
                    >
                        ...
                    </span>

                </button>


                {{-- FALHAS --}}

                <div
                    class="
                        flex
                        items-center
                        gap-1.5
                    "
                >

                    <template
                        x-for="n in 3"
                        :key="'failure-' + n"
                    >

                        <button
                            type="button"

                            @click="
                                setDeathSave(
                                    'failure',
                                    n
                                )
                            "

                            class="
                                relative
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center

                                rounded-full
                                border-2

                                transition-all
                                duration-200
                            "

                            :class="
                                n <= deathSaveFailures
                                    ? 'border-red-500 bg-red-500 text-white shadow-[0_0_10px_rgba(239,68,68,.25)] scale-105'
                                    : 'border-red-300 bg-white text-transparent hover:border-red-400 hover:bg-red-50'
                            "
                        >

                            <span
                                x-show="
                                    n <=
                                    deathSaveFailures
                                "

                                x-transition

                                class="
                                    text-sm
                                    font-black
                                "
                            >
                                ×
                            </span>

                        </button>

                    </template>

                </div>

            </div>


            {{-- RESULTADO --}}

            <div
                x-show="
                    deathRollResult !== null
                "

                x-cloak
                x-transition

                class="
                    mt-3
                    border-t
                    border-red-200/60
                    pt-2
                    text-center
                "
            >

                <span
                    class="
                        font-serif
                        text-xl
                        font-black
                        text-[#53150f]
                    "

                    x-text="
                        deathRollResult
                    "
                ></span>


                <span
                    x-show="
                        deathRollResult === 20
                    "

                    class="
                        ml-1.5
                        text-[8px]
                        font-black
                        uppercase
                        tracking-widest
                        text-emerald-700
                    "
                >
                    Recuperou 1 PV
                </span>


                <span
                    x-show="
                        deathRollResult >= 10 &&
                        deathRollResult !== 20
                    "

                    class="
                        ml-1.5
                        text-[8px]
                        font-black
                        uppercase
                        tracking-widest
                        text-emerald-700
                    "
                >
                    Sucesso
                </span>


                <span
                    x-show="
                        deathRollResult < 10
                    "

                    class="
                        ml-1.5
                        text-[8px]
                        font-black
                        uppercase
                        tracking-widest
                        text-red-700
                    "
                >
                    Falha
                </span>

            </div>

        </div>

    </div>


    {{-- ============================================================
         REABRIR GAVETA
    ============================================================= --}}

    <div
        x-show="
            isDying &&
            !deathDrawerOpen
        "

        x-cloak
        x-transition

        class="
            death-tab-enter
            absolute
            left-1/2
            top-full
            z-50
            -translate-x-1/2
        "
    >

        <button
            type="button"

            @click="
                openDeathDrawer()
            "

            class="
                group
                flex
                h-7
                items-center
                gap-2

                rounded-b-xl

                border
                border-t-0
                border-red-200

                bg-[#f8f3eb]

                px-4

                text-[8px]
                font-black
                uppercase
                tracking-widest
                text-red-800

                shadow-[0_8px_18px_rgba(72,20,15,.10)]

                transition-all
                duration-200

                hover:bg-red-50
            "
        >

            <span
                class="
                    h-1.5
                    w-1.5
                    rounded-full
                    bg-red-500
                "
            ></span>

            Salvamentos

            <svg
                class="h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 15l7-7 7 7"
                />

            </svg>

        </button>

    </div>


    {{-- ============================================================
         MODAL DE VIDA
    ============================================================= --}}

    <div
        x-show="
            hpSettingsOpen
        "

        x-cloak

        class="
            fixed
            inset-0
            z-[100]
            flex
            items-center
            justify-center
            p-4
        "
    >

        {{-- BACKDROP --}}

        <div
            x-show="
                hpSettingsOpen
            "

            x-transition.opacity

            @click="
                closeHpSettings()
            "

            class="
                absolute
                inset-0
                bg-[#2b1d17]/60
                backdrop-blur-sm
            "
        ></div>


        {{-- PAINEL --}}

        <div
            x-show="
                hpSettingsOpen
            "

            x-transition

            @click.stop

            class="
                relative
                z-10
                w-full
                max-w-md
                overflow-hidden

                rounded-2xl

                border
                border-[#cdbb9f]

                bg-[#f4f1e8]

                shadow-2xl
            "
        >

            {{-- =====================================================
                 CONFIRMAÇÃO
            ====================================================== --}}

            <template
                x-if="
                    hpSettingsStep ===
                    'confirm'
                "
            >

                <div>

                    <div
                        class="
                            border-b
                            border-[#cdbb9f]/60
                            bg-[#efe9dc]/60
                            px-4
                            py-3
                        "
                    >

                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.25em]
                                text-[#8c6239]
                            "
                        >
                            Configuração de Combate
                        </p>

                        <h2
                            class="
                                mt-1
                                font-serif
                                text-lg
                                font-black
                                text-[#53150f]
                            "
                        >
                            Alterar Vida
                        </h2>

                    </div>


                    <div class="p-4">

                        <div
                            class="
                                rounded-xl
                                border
                                border-amber-200
                                bg-amber-50
                                p-3
                            "
                        >

                            <p
                                class="
                                    text-sm
                                    font-black
                                    text-amber-900
                                "
                            >
                                Alteração permanente
                            </p>


                            <p
                                class="
                                    mt-1
                                    text-xs
                                    leading-4
                                    text-amber-800/80
                                "
                            >
                                A Vida Máxima pertence à ficha.
                                A Vida Máxima Extra aumenta
                                temporariamente o limite e concede
                                os PV correspondentes.
                            </p>

                        </div>


                        <div
                            class="
                                mt-3
                                rounded-xl
                                border
                                border-[#cdbb9f]/50
                                bg-white/60
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
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Vida
                                </span>


                                <span
                                    class="
                                        font-serif
                                        text-lg
                                        font-black
                                        text-[#53150f]
                                    "
                                >

                                    <span
                                        x-text="
                                            currentHp
                                        "
                                    ></span>

                                    /

                                    <span
                                        x-text="
                                            effectiveMaxHp
                                        "
                                    ></span>

                                </span>

                            </div>


                            <div
                                class="
                                    mt-2
                                    flex
                                    gap-2
                                "
                            >

                                {{-- BASE --}}

                                <div
                                    class="
                                        flex-1
                                        rounded-lg
                                        bg-[#efe9dc]
                                        p-2
                                        text-center
                                    "
                                >

                                    <span
                                        class="
                                            block
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-widest
                                            text-[#8c6239]
                                        "
                                    >
                                        Base
                                    </span>


                                    <span
                                        x-text="
                                            maxHp
                                        "

                                        class="
                                            mt-0.5
                                            block
                                            font-serif
                                            text-base
                                            font-black
                                            text-[#53150f]
                                        "
                                    ></span>

                                </div>


                                {{-- EXTRA --}}

                                <div
                                    x-show="
                                        temporaryMaxHp > 0
                                    "

                                    class="
                                        flex-1
                                        rounded-lg
                                        bg-[#f4ead0]
                                        p-2
                                        text-center
                                    "
                                >

                                    <span
                                        class="
                                            block
                                            text-[7px]
                                            font-black
                                            uppercase
                                            tracking-widest
                                            text-[#9a6f16]
                                        "
                                    >
                                        Extra
                                    </span>


                                    <span
                                        class="
                                            mt-0.5
                                            block
                                            font-serif
                                            text-base
                                            font-black
                                            text-[#9a6f16]
                                        "
                                    >
                                        +

                                        <span
                                            x-text="
                                                temporaryMaxHp
                                            "
                                        ></span>

                                    </span>

                                </div>

                            </div>

                        </div>


                        <div
                            class="
                                mt-4
                                flex
                                justify-end
                                gap-2
                            "
                        >

                            <button
                                type="button"

                                @click="
                                    closeHpSettings()
                                "

                                class="
                                    rounded-lg
                                    px-3
                                    py-2

                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]

                                    hover:bg-[#efe9dc]
                                "
                            >
                                Cancelar
                            </button>


                            <button
                                type="button"

                                @click="
                                    beginHpSettings()
                                "

                                class="
                                    rounded-lg
                                    bg-[#6b1d14]
                                    px-4
                                    py-2

                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#f4f1e8]

                                    hover:bg-[#53150f]
                                "
                            >
                                Continuar
                            </button>

                        </div>

                    </div>

                </div>

            </template>


            {{-- =====================================================
                 EDIÇÃO
            ====================================================== --}}

            <template
                x-if="
                    hpSettingsStep ===
                    'edit'
                "
            >

                <div>

                    <div
                        class="
                            border-b
                            border-[#cdbb9f]/60
                            bg-[#efe9dc]/60
                            px-4
                            py-3
                        "
                    >

                        <p
                            class="
                                text-[8px]
                                font-black
                                uppercase
                                tracking-[0.25em]
                                text-[#8c6239]
                            "
                        >
                            Configuração de Combate
                        </p>


                        <h2
                            class="
                                mt-1
                                font-serif
                                text-lg
                                font-black
                                text-[#53150f]
                            "
                        >
                            Valores de Vida
                        </h2>

                    </div>


                    <div
                        class="
                            space-y-3
                            p-4
                        "
                    >

                        {{-- VIDA MÁXIMA --}}

                        <label class="block">

                            <span
                                class="
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]
                                "
                            >
                                Vida Máxima
                            </span>


                            <input
                                x-ref="
                                    maxHpSettingsInput
                                "

                                type="number"
                                min="1"

                                x-model.number="
                                    directMaxHp
                                "

                                class="
                                    mt-1.5
                                    w-full

                                    rounded-lg

                                    border
                                    border-[#cdbb9f]

                                    bg-white

                                    px-3
                                    py-2.5

                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#53150f]

                                    outline-none

                                    focus:border-[#6b1d14]
                                    focus:ring-1
                                    focus:ring-[#6b1d14]/10
                                "
                            >

                        </label>


                        {{-- VIDA EXTRA --}}

                        <label
                            class="
                                block

                                rounded-xl

                                border
                                border-[#d4b36b]/50

                                bg-[#f8efd9]/70

                                p-3
                            "
                        >

                            <span
                                class="
                                    block
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#9a6f16]
                                "
                            >
                                Vida Máxima Extra
                            </span>


                            <span
                                class="
                                    mt-0.5
                                    block
                                    text-[9px]
                                    text-[#9a6f16]/80
                                "
                            >
                                Aumenta o limite e cura a mesma
                                quantidade adicionada.
                            </span>


                            <input
                                type="number"
                                min="0"

                                x-model.number="
                                    directTemporaryMaxHp
                                "

                                class="
                                    mt-2
                                    w-full

                                    rounded-lg

                                    border
                                    border-[#d4b36b]/60

                                    bg-white

                                    px-3
                                    py-2.5

                                    font-serif
                                    text-lg
                                    font-black
                                    text-[#9a6f16]

                                    outline-none

                                    focus:border-[#b88920]
                                    focus:ring-1
                                    focus:ring-[#d4b36b]/20
                                "
                            >

                        </label>


                        {{-- PREVIEW --}}

                        <div
                            class="
                                rounded-xl

                                border
                                border-[#cdbb9f]/50

                                bg-[#efe9dc]/40

                                p-3
                            "
                        >

                            <div
                                class="
                                    mb-2
                                    flex
                                    items-center
                                    justify-between
                                "
                            >

                                <span
                                    class="
                                        text-[7px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-[#8c6239]
                                    "
                                >
                                    Novo limite
                                </span>


                                <span
                                    class="
                                        font-serif
                                        text-base
                                        font-black
                                        text-[#53150f]
                                    "
                                >

                                    <span
                                        x-text="
                                            Math.max(
                                                1,
                                                parseInt(
                                                    directMaxHp
                                                ) || maxHp
                                            )
                                        "
                                    ></span>


                                    <span
                                        x-show="
                                            (
                                                parseInt(
                                                    directTemporaryMaxHp
                                                ) || 0
                                            ) > 0
                                        "

                                        class="
                                            text-[#9a6f16]
                                        "
                                    >
                                        +

                                        <span
                                            x-text="
                                                Math.max(
                                                    0,
                                                    parseInt(
                                                        directTemporaryMaxHp
                                                    ) || 0
                                                )
                                            "
                                        ></span>

                                    </span>

                                </span>

                            </div>


                            <div
                                class="
                                    relative
                                    h-3
                                    overflow-hidden
                                    rounded-full
                                    bg-[#d8c7ab]
                                "
                            >

                                {{-- VIDA BASE --}}

                                <div
                                    class="
                                        absolute
                                        inset-y-0
                                        left-0
                                        bg-emerald-600
                                    "

                                    :style="`
                                        width:${
                                            (
                                                Math.max(
                                                    1,
                                                    parseInt(
                                                        directMaxHp
                                                    ) || maxHp
                                                )
                                                /
                                                (
                                                    Math.max(
                                                        1,
                                                        parseInt(
                                                            directMaxHp
                                                        ) || maxHp
                                                    )
                                                    +
                                                    Math.max(
                                                        0,
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    )
                                                )
                                            ) * 100
                                        }%;
                                    `"
                                ></div>


                                {{-- EXTRA --}}

                                <div
                                    class="
                                        absolute
                                        inset-y-0
                                        right-0
                                        bg-[#d9a441]
                                    "

                                    :style="`
                                        width:${
                                            (
                                                Math.max(
                                                    0,
                                                    parseInt(
                                                        directTemporaryMaxHp
                                                    ) || 0
                                                )
                                                /
                                                (
                                                    Math.max(
                                                        1,
                                                        parseInt(
                                                            directMaxHp
                                                        ) || maxHp
                                                    )
                                                    +
                                                    Math.max(
                                                        0,
                                                        parseInt(
                                                            directTemporaryMaxHp
                                                        ) || 0
                                                    )
                                                )
                                            ) * 100
                                        }%;
                                    `"
                                ></div>

                            </div>


                            {{-- CURA AUTOMÁTICA --}}

                            <div
                                x-show="
                                    Math.max(
                                        0,
                                        (
                                            parseInt(
                                                directTemporaryMaxHp
                                            ) || 0
                                        ) -
                                        temporaryMaxHp
                                    ) > 0
                                "

                                class="
                                    mt-2

                                    rounded-lg

                                    border
                                    border-emerald-200

                                    bg-emerald-50

                                    px-2.5
                                    py-2
                                "
                            >

                                <p
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-widest
                                        text-emerald-800
                                    "
                                >
                                    Cura automática
                                </p>


                                <p
                                    class="
                                        mt-0.5
                                        text-[10px]
                                        text-emerald-800/80
                                    "
                                >
                                    +

                                    <span
                                        x-text="
                                            Math.max(
                                                0,
                                                (
                                                    parseInt(
                                                        directTemporaryMaxHp
                                                    ) || 0
                                                ) -
                                                temporaryMaxHp
                                            )
                                        "
                                    ></span>

                                    PV
                                </p>

                            </div>

                        </div>


                        {{-- AÇÕES --}}

                        <div
                            class="
                                flex
                                justify-end
                                gap-2

                                border-t
                                border-[#cdbb9f]/40

                                pt-3
                            "
                        >

                            <button
                                type="button"

                                @click="
                                    closeHpSettings()
                                "

                                class="
                                    rounded-lg
                                    px-3
                                    py-2

                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#8c6239]

                                    hover:bg-[#efe9dc]
                                "
                            >
                                Cancelar
                            </button>


                            <button
                                type="button"

                                @click="
                                    saveHpSettings()
                                "

                                class="
                                    rounded-lg

                                    bg-[#6b1d14]

                                    px-4
                                    py-2

                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-widest
                                    text-[#f4f1e8]

                                    hover:bg-[#53150f]
                                "
                            >
                                Salvar
                            </button>

                        </div>

                    </div>

                </div>

            </template>

        </div>

    </div>

</div>