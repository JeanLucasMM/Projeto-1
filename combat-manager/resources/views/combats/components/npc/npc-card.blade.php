@once

    @push('styles')

        <style>

@keyframes damage-hit{

    0%{

        transform:translate3d(0,0,0) scale(1);

        filter:brightness(1);

    }

    8%{

        transform:translateX(-7px) rotate(-1deg) scale(.992);

    }

    18%{

        transform:translateX(6px) rotate(.8deg);

    }

    30%{

        transform:translateX(-5px);

    }

    42%{

        transform:translateX(4px);

    }

    56%{

        transform:translateX(-2px);

    }

    70%{

        transform:translateX(1px);

    }

    100%{

        transform:translate3d(0,0,0) scale(1);

        filter:brightness(1);

    }

}



/* DANO FLASH */

@keyframes damageFlash{

    0%{

        box-shadow:

            inset 0 0 0 rgba(185,28,28,0),

            0 0 0 rgba(220,38,38,0);

    }

    25%{

        box-shadow:

            inset 0 0 70px rgba(185,28,28,.55),

            0 0 20px rgba(220,38,38,.20);

    }

    60%{

        box-shadow:

            inset 0 0 25px rgba(185,28,28,.25),

            0 0 8px rgba(220,38,38,.08);

    }

    100%{

        box-shadow:

            inset 0 0 0 rgba(185,28,28,0),

            0 0 0 rgba(220,38,38,0);

    }

}



/* CURA */

@keyframes heal{

    0%{

        transform:scale(1);

        filter:brightness(1);

    }

    18%{

        transform:scale(1.018);

    }

    45%{

        transform:scale(1.032);

        filter:brightness(1.25);

    }

    75%{

        transform:scale(1.01);

        filter:brightness(1.08);

    }

    100%{

        transform:scale(1);

        filter:brightness(1);

    }

}



@keyframes healFlash{

    0%{

        box-shadow:

            inset 0 0 0 rgba(34,197,94,0),

            0 0 0 rgba(34,197,94,0);

    }

    30%{

        box-shadow:

            inset 0 0 45px rgba(34,197,94,.22),

            0 0 26px rgba(34,197,94,.45);

    }

    70%{

        box-shadow:

            inset 0 0 18px rgba(34,197,94,.10),

            0 0 8px rgba(34,197,94,.12);

    }

    100%{

        box-shadow:

            inset 0 0 0 rgba(34,197,94,0),

            0 0 0 rgba(34,197,94,0);

    }

}



/* ESCUDO */

@keyframes shieldAura{

    0%{

        box-shadow: 0 0 4px rgba(59,130,246,.12);

    }

    50%{

        box-shadow:

            0 0 12px rgba(56,189,248,.45),

            0 0 28px rgba(96,165,250,.18);

    }

    100%{

        box-shadow: 0 0 4px rgba(59,130,246,.12);

    }

}



@keyframes shield-hit{

    0%{ transform:scale(1); }

    30%{ transform:scale(.986); }

    60%{ transform:scale(1.008); }

    100%{ transform:scale(1); }

}



/* BLOODIED (50%) */

@keyframes bloodiedPulse{

    0%,100%{

        filter:saturate(1);

        box-shadow: 0 0 0 rgba(127,29,29,.10);

    }

    50%{

        filter:saturate(1.08);

        box-shadow:

            0 0 18px rgba(127,29,29,.30),

            inset 0 0 18px rgba(127,29,29,.18);

    }

}



/* CRITICAL (<25%) */

@keyframes criticalPulse{

    0%,100%{

        transform:scale(1);

        box-shadow: 0 0 0 rgba(220,38,38,.20);

        filter:brightness(1);

    }

    50%{

        transform:scale(1.01);

        box-shadow:

            0 0 26px rgba(220,38,38,.55),

            inset 0 0 22px rgba(220,38,38,.28);

        filter:brightness(1.05);

    }

}



/* CLASSES */

.animate-damage-red{

    animation:

        damage-hit .55s cubic-bezier(.18,.89,.32,1.2),

        damageFlash .55s ease-out;

}



.animate-damage-blue{

    animation:

        shield-hit .45s ease-out,

        damageFlash .45s ease-out,

        shieldAura .6s ease-out;

}



.animate-heal-green{

    animation:

        heal .65s cubic-bezier(.2,.9,.2,1),

        healFlash .75s ease-out;

}



.animate-photo-red-internal{

    animation: damageFlash .55s ease-out;

}



.temp-hp-active{

    animation: shieldAura 3s ease-in-out infinite;

}



.bloodied-card{

    animation: bloodiedPulse 2.1s ease-in-out infinite;

}



.critical-card{

    animation: criticalPulse 2.5s ease-in-out infinite;

}

        /* V65 - efeitos aplicados apenas ao retrato/moldura, sem deslocar a ficha. */
        @keyframes sb-v65-photo-hit {
            0%, 100% { transform: translateX(0) rotate(0); filter: none; }
            15% { transform: translateX(-6px) rotate(-1.5deg); filter: sepia(.4) saturate(1.8) brightness(.96); }
            31% { transform: translateX(5px) rotate(1deg); filter: sepia(.35) saturate(1.65); }
            48% { transform: translateX(-4px) rotate(-.7deg); }
            66% { transform: translateX(2px) rotate(.3deg); }
            83% { transform: translateX(-1px); }
        }
        @keyframes sb-v65-red-rim {
            0%, 100% { box-shadow: inset 0 0 0 rgba(190,38,38,0); }
            24% { box-shadow: inset 0 0 28px rgba(190,38,38,.64), 0 0 14px rgba(190,38,38,.32); }
        }
        @keyframes sb-v65-blue-shield {
            0%, 100% { box-shadow: inset 0 0 0 rgba(56,189,248,0); }
            35% { box-shadow: inset 0 0 35px rgba(56,189,248,.57), 0 0 18px rgba(56,189,248,.38); }
        }
        @keyframes sb-v65-green-heal {
            0%, 100% { box-shadow: inset 0 0 0 rgba(74,164,102,0); filter: none; }
            40% { box-shadow: inset 0 0 26px rgba(74,164,102,.38), 0 0 14px rgba(74,164,102,.27); filter: brightness(1.15); }
        }
        .sb-v65-photo.animate-damage-red { animation: sb-v65-photo-hit .58s cubic-bezier(.22,.7,.25,1) !important; }
        .sb-v65-photo-rim.animate-photo-red-internal { animation: sb-v65-red-rim .6s ease-out !important; }
        .sb-v65-photo-rim.animate-damage-blue:not(.animate-photo-red-internal) { animation: sb-v65-blue-shield .6s ease-out !important; }
        .sb-v65-photo-rim.animate-heal-green { animation: sb-v65-green-heal .65s ease-out !important; }
        .sb-v65-hp-bar.sb-v65-damage-bar { animation: sb-v65-red-rim .6s ease-out; }
        .sb-v65-hp-bar.sb-v65-shield-bar { animation: sb-v65-blue-shield .6s ease-out; }
        .sb-v65-hp-bar.sb-v65-heal-bar { animation: sb-v65-green-heal .65s ease-out; }
        .sb-v65-hp-popover { box-shadow: 0 16px 40px rgba(61,30,14,.18), 0 3px 9px rgba(61,30,14,.09); }
        @media (prefers-reduced-motion: reduce) {
            .sb-v65-photo.animate-damage-red,
            .sb-v65-photo-rim.animate-photo-red-internal,
            .sb-v65-photo-rim.animate-damage-blue,
            .sb-v65-photo-rim.animate-heal-green,
            .sb-v65-hp-bar.sb-v65-damage-bar,
            .sb-v65-hp-bar.sb-v65-shield-bar,
            .sb-v65-hp-bar.sb-v65-heal-bar { animation: none !important; }
        }

        /* V65.2 — proteção no retrato: impacto absorvido, sem ferimento vermelho. */
        @keyframes sb-v652-photo-repel {
            0%, 100% { transform: translateZ(0) scale(1); }
            19% { transform: translateX(2px) scale(.987); }
            44% { transform: translateX(-1px) scale(1.008); }
            65% { transform: translateX(1px) scale(.997); }
        }
        @keyframes sb-v652-ward-wave {
            0%, 100% { opacity: 0; }
            20% { opacity: .95; }
            60% { opacity: .35; }
        }
        @keyframes sb-v652-ward-ring {
            0% { opacity: 0; transform: scale(.7); }
            28% { opacity: 1; }
            100% { opacity: 0; transform: scale(1.2); }
        }
        .sb-v65-photo.sb-v652-photo-repel {
            animation: sb-v652-photo-repel .6s cubic-bezier(.2,.72,.2,1) !important;
        }
        .sb-v652-photo-ward {
            background: radial-gradient(ellipse at 50% 48%, rgba(224,242,254,.28) 0%, rgba(56,189,248,.28) 52%, rgba(3,105,161,.68) 100%);
            animation: sb-v652-ward-wave .66s ease-out both;
        }
        .sb-v652-photo-ward-ring {
            box-shadow: 0 0 22px rgba(56,189,248,.55), inset 0 0 18px rgba(186,230,253,.27);
            animation: sb-v652-ward-ring .64s ease-out both;
        }
        .sb-v652-photo-ward-icon { filter: drop-shadow(0 0 10px rgba(186,230,253,.96)); }
        @media (prefers-reduced-motion: reduce) {
            .sb-v65-photo.sb-v652-photo-repel, .sb-v652-photo-ward, .sb-v652-photo-ward-ring { animation: none !important; }
        }
        /* V65.3 — aumento de PV máximos: expansão dourada apenas na área preenchida. */
        @keyframes sb-v653-max-glow {
            0%, 100% { box-shadow: inset 0 0 0 rgba(250,204,21,0), 0 0 0 rgba(217,164,65,0); }
            28% { box-shadow: inset 0 0 23px rgba(255,225,140,.48), 0 0 15px rgba(217,164,65,.36); }
            62% { box-shadow: inset 0 0 11px rgba(255,225,140,.20), 0 0 5px rgba(217,164,65,.13); }
        }
        @keyframes sb-v653-max-fill {
            0% { opacity: 0; }
            22% { opacity: .85; }
            60% { opacity: .54; }
            100% { opacity: 0; }
        }
        @keyframes sb-v653-max-shine {
            from { transform: translateX(-115%) skewX(-18deg); }
            to { transform: translateX(335%) skewX(-18deg); }
        }
        .sb-v65-hp-bar.sb-v653-boost-bar { animation: sb-v653-max-glow 1.05s ease-out; }
        .sb-v653-boost-fill {
            background: linear-gradient(180deg, rgba(255,246,209,.36), rgba(252,211,77,.10) 55%, rgba(184,109,18,.16));
            animation: sb-v653-max-fill 1.05s ease-out both;
        }
        .sb-v653-boost-shine {
            position: absolute; inset-block: 0; left: 0; width: 38%;
            background: linear-gradient(90deg, transparent, rgba(255,250,205,.85), transparent);
            animation: sb-v653-max-shine .85s ease-out both;
        }
        @media (prefers-reduced-motion: reduce) {
            .sb-v65-hp-bar.sb-v653-boost-bar, .sb-v653-boost-fill, .sb-v653-boost-shine { animation: none !important; }
        }
        </style>

    @endpush

@endonce



@php

    use App\Support\Dictionaries\AbilityNames;

    use App\Support\Dictionaries\Alignments;

    use App\Support\Dictionaries\Conditions;

    use App\Support\Dictionaries\DamageTypes;

    use App\Support\Dictionaries\Languages;

    use App\Support\Dictionaries\NpcSizes;

    use App\Support\Dictionaries\NpcTypes;

    use App\Support\Dictionaries\Senses;

    use App\Support\Dictionaries\SkillNames;



    $header = $combatNpc->viewModel->header ?? null;

    $combatData = $combatNpc->viewModel->combat ?? null;



    $npcModel = $combatNpc->npc ?? null;

    $json = $npcModel ? ($npcModel->json_data ?? []) : [];

    $isBuilder = ($json['format'] ?? null) === 'npc-builder';



    // === LÓGICA DE INTERPRETAÇÃO DE VIDA (HP) PARA OS DOIS FORMATOS ===

    $maxHp = (int) ($combatNpc->max_hp ?? 0);

    $tempHp = (int) ($combatNpc->temporary_hp ?? 0);



    if ($isBuilder) {

        $builderCombat = $json['combat'] ?? [];

        $hpMode = $builderCombat['hp_mode'] ?? 'average';



        if ($hpMode === 'custom' && isset($builderCombat['custom_hp'])) {

            $maxHp = (int) $builderCombat['custom_hp'];

        } else {

            $hdCount = (int) ($builderCombat['hit_dice_count'] ?? 1);

            $hdFace = (int) str_replace('d', '', $builderCombat['hit_die'] ?? '8');

            $hpExtra = (int) ($builderCombat['hp_mod_extra'] ?? 0);



            $conScore = (int) ($json['abilities']['con'] ?? 10);

            $conMod = floor(($conScore - 10) / 2);



            $avgPerDie = match($hdFace) {

                4 => 2.5,

                6 => 3.5,

                8 => 4.5,

                10 => 5.5,

                12 => 6.5,

                20 => 10.5,

                default => ($hdFace / 2) + 0.5

            };



            $maxHp = (int) floor(($hdCount * $avgPerDie) + ($hdCount * $conMod) + $hpExtra);

            if ($maxHp < 1) $maxHp = 1;

        }

    } else {

        // Formato 5emm / Padrão

        if ($maxHp <= 1 && isset($header->hitPoints)) {

            $maxHp = (int) $header->hitPoints;

        } elseif ($maxHp <= 1 && isset($combatData->hp)) {

            $maxHp = (int) $combatData->hp;

        }

        if ($maxHp <= 1) {

            $maxHp = 10; // Fallback seguro

        }

    }



    // V65.2: 'max_hp' salvo no combate é o máximo EFETIVO. Os modificadores
    // pertencem apenas à instância em combate, sem alterar a ficha original do NPC.
    // base_max_hp será preenchido pela migration para NPCs já em andamento.
    $persistedMaxHp = (int) ($combatNpc->max_hp ?? 0);
    $baseMaxHp = max(1, (int) ($combatNpc->base_max_hp ?? ($persistedMaxHp > 0 ? $persistedMaxHp : $maxHp)));
    $maxHpBonus = max(0, (int) ($combatNpc->max_hp_bonus ?? 0));
    $maxHpReduction = max(0, (int) ($combatNpc->max_hp_reduction ?? 0));
    $maxHp = $persistedMaxHp > 0
        ? $persistedMaxHp
        : max(1, $baseMaxHp + $maxHpBonus - $maxHpReduction);

    // Respeita o current_hp salvo no banco, sem forçar reset se estiver baixo ou 0

    $currentHp = isset($combatNpc->current_hp) ? (int) $combatNpc->current_hp : $maxHp;

    // =================================================================



    // Nome

    $npcName = $header->name ?? $npcModel->name ?? 'Desconhecido';



    // Tamanho (Traduzido via Dicionário)

    $rawSize = $header->size ?? ($json['header']['size'] ?? '');

    $npcSize = NpcSizes::label($rawSize);



    // Tipos (Traduzido via Dicionário, suporta string ou array)

    $rawTypes = $header->type ?? $header->types ?? ($json['header']['types'] ?? '');

    if (is_array($rawTypes)) {

        $npcTypes = collect($rawTypes)->map(fn($t) => NpcTypes::label($t))->implode(', ');

    } else {

        $npcTypes = NpcTypes::label($rawTypes);

    }



    // Classe de Armadura

    $npcAc = $header->armorClass ?? ($npcModel->calculated_ac ?? ($json['combat']['ac_base'] ?? 10));



    // Proficiência baseada no CR para cálculo de iniciativa do Builder

    $crStr = $json['header']['challengeRating'] ?? ($header->challengeRating ?? '1');

    $crNum = 1;

    if (strpos($crStr, '/') !== false) {

        $parts = explode('/', $crStr);

        $crNum = intval($parts[0]) / max(1, intval($parts[1]));

    } else {

        $crNum = floatval($crStr);

    }

    $profBonus = 2;

    if ($crNum >= 17) $profBonus = 6;

    elseif ($crNum >= 13) $profBonus = 5;

    elseif ($crNum >= 9) $profBonus = 4;

    elseif ($crNum >= 5) $profBonus = 3;

    else $profBonus = 2;



    // Deslocamento

    $npcSpeed = '-';

    if ($isBuilder && isset($json['speed'])) {

        $speedArr = $json['speed'];

        $speedsFormatted = [];



        if (!empty($speedArr['walk']) && $speedArr['walk'] > 0) {

            $speedsFormatted[] = "{$speedArr['walk']} ft";

        }

        if (!empty($speedArr['climb']) && $speedArr['climb'] > 0) {

            $speedsFormatted[] = "escalada {$speedArr['climb']} ft";

        }

        if (!empty($speedArr['swim']) && $speedArr['swim'] > 0) {

            $speedsFormatted[] = "natação {$speedArr['swim']} ft";

        }

        if (!empty($speedArr['fly']) && $speedArr['fly'] > 0) {

            $hoverText = !empty($speedArr['hover']) ? ' (hover)' : '';

            $speedsFormatted[] = "voo {$speedArr['fly']} ft{$hoverText}";

        }

        if (!empty($speedArr['burrow']) && $speedArr['burrow'] > 0) {

            $speedsFormatted[] = "escavação {$speedArr['burrow']} ft";

        }



        $npcSpeed = count($speedsFormatted) > 0 ? implode(', ', $speedsFormatted) : '0 ft';

    } elseif (isset($combatData->speed)) {

        $npcSpeed = is_object($combatData->speed) && isset($combatData->speed->walk) 

            ? "{$combatData->speed->walk} ft" 

            : $combatData->speed;

    } elseif ($npcModel) {

        $npcSpeed = '30 ft';

    }



    // Experiência: valor explícito da criatura tem prioridade; ND é fallback D&D 5e.
    // Mantemos o valor desconhecido como nulo (não exibimos XP fictício).
    $xpRaw = $json['header']['xp']
        ?? $json['header']['experiencePoints']
        ?? $json['header']['experience']
        ?? $npcModel?->xp
        ?? $npcModel?->experience_points
        ?? $header?->xp
        ?? null;

    $xpByCr = [
        '0' => 10, '1/8' => 25, '1/4' => 50, '1/2' => 100,
        '1' => 200, '2' => 450, '3' => 700, '4' => 1100,
        '5' => 1800, '6' => 2300, '7' => 2900, '8' => 3900,
        '9' => 5000, '10' => 5900, '11' => 7200, '12' => 8400,
        '13' => 10000, '14' => 11500, '15' => 13000, '16' => 15000,
        '17' => 18000, '18' => 20000, '19' => 22000, '20' => 25000,
        '21' => 33000, '22' => 41000, '23' => 50000, '24' => 62000,
        '25' => 75000, '26' => 90000, '27' => 105000, '28' => 120000,
        '29' => 135000, '30' => 155000,
    ];
    $rawCr = $json['header']['challengeRating']
        ?? $json['header']['cr']
        ?? $json['header']['nd']
        ?? $header?->challengeRating
        ?? $header?->cr
        ?? $header?->nd
        ?? $npcModel?->challenge_rating
        ?? null;
    $crKey = $rawCr !== null && is_scalar($rawCr) ? trim((string) $rawCr) : null;
    if ($crKey !== null && is_numeric(str_replace(',', '.', $crKey))) {
        $crFloat = (float) str_replace(',', '.', $crKey);
        if (abs($crFloat - 0.125) < .0001) $crKey = '1/8';
        elseif (abs($crFloat - 0.25) < .0001) $crKey = '1/4';
        elseif (abs($crFloat - 0.5) < .0001) $crKey = '1/2';
        elseif (floor($crFloat) === $crFloat) $crKey = (string) (int) $crFloat;
    }
    $npcXp = null;
    if ($xpRaw !== null && is_scalar($xpRaw) && is_numeric($xpRaw)) {
        $npcXp = max(0, (int) $xpRaw);
    } elseif ($crKey !== null && array_key_exists($crKey, $xpByCr)) {
        $npcXp = $xpByCr[$crKey];
    }
    $speedChips = [];
    foreach (preg_split('/,\s*/u', is_scalar($npcSpeed) ? trim((string) $npcSpeed) : '-') as $speedText) {
        $speedText = trim($speedText);
        if ($speedText === '') continue;
        $speedLabel = 'Andar';
        $speedValue = $speedText;
        if (preg_match('/^(voo|escalada|natação|escavação)\s+(.+)$/ui', $speedText, $speedMatches)) {
            $speedLabel = ucfirst($speedMatches[1]);
            $speedValue = $speedMatches[2];
        }
        $speedChips[] = ['label' => $speedLabel, 'value' => $speedValue];
    }

    // Reconhecer e calcular Iniciativa corretamente

    $npcInitiative = null;

    if ($isBuilder) {

        $rawAbilities = $json['abilities'] ?? [];

        $dexVal = $rawAbilities['dex'] ?? 10;

        $dexMod = floor(($dexVal - 10) / 2);



        $skillsData = $json['skills'] ?? [];

        $initSkill = collect($skillsData)->first(function($s) {

            return in_array(strtolower($s['key'] ?? ''), ['initiative', 'iniciativa']);

        });



        $initBonus = $dexMod;

        if ($initSkill) {

            if (!empty($initSkill['expertise'])) {

                $initBonus += ($profBonus * 2);

            } elseif (!empty($initSkill['proficient']) || !empty($initSkill['enabled'])) {

                $initBonus += $profBonus;

            }

            $initBonus += ($initSkill['bonus'] ?? 0);

        }

        $npcInitiative = ($initBonus >= 0 ? '+' : '') . $initBonus;

    } else {

        $npcInitiative = $combatNpc->viewModel->combat->initiative ?? null;

        if (!$npcInitiative && isset($combatNpc->viewModel->skills)) {

            $initSkillVm = collect($combatNpc->viewModel->skills)->first(function($s) {

                $name = strtolower(is_array($s) ? ($s['name'] ?? '') : ($s->name ?? ''));

                return in_array($name, ['initiative', 'iniciativa']);

            });

            if ($initSkillVm) {

                $val = is_array($initSkillVm) ? ($initSkillVm['value'] ?? 0) : ($initSkillVm->value ?? 0);

                $npcInitiative = ($val >= 0 ? '+' : '') . $val;

            }

        }

    }

@endphp



<div

    x-data="{
        currentHp: {{ $currentHp }},
        ghostHp: {{ $currentHp }},
        maxHpBoosting: false,
        maxHpGainAmount: 0,
        maxHpGainTimer: null,
        maxHp: {{ $maxHp }},
        baseMaxHp: {{ $baseMaxHp }},
        bonusMaxHp: {{ $maxHpBonus }},
        reducedMaxHp: {{ $maxHpReduction }},
        maxHpBonusDraft: {{ $maxHpBonus }},
        maxHpReductionDraft: {{ $maxHpReduction }},
        maxHpSettingsOpen: false,
        maxHpSettingsSaving: false,
        maxHpSettingsError: '',
        directTempHp: {{ $tempHp }},
        hpPopoverOpen: false,
        tempPopoverOpen: false,
        quickDamage: '',
        quickHeal: '',
        tempHpDraft: {{ $tempHp }},
        shakingRed: false,
        shakingBlue: false,
        flashingGreen: false,
        hpPending: [],
        hpSaving: false,
        hpError: '',
        damageFxTimer: null,
        healFxTimer: null,
        shieldFxTimer: null,
        ghostFxTimer: null,
        init() { this.$nextTick(() => this.announceNpcHp()); },
        get hpPercent() {
            return Math.max(0, Math.min(100, (this.currentHp / this.potentialMaxHp) * 100));
        },
        // PV temporários representam uma proteção, não uma segunda barra de HP.
        // A película azul ocupa sempre os 100% quando há qualquer proteção ativa.
        get potentialMaxHp() { return Math.max(1, this.baseMaxHp + this.bonusMaxHp); },
        get normalHpPercent() {
            return Math.max(0, Math.min(100, Math.min(this.currentHp, this.baseMaxHp) / this.potentialMaxHp * 100));
        },
        get bonusHpPercent() {
            return Math.max(0, Math.min(100, Math.max(0, this.currentHp - this.baseMaxHp) / this.potentialMaxHp * 100));
        },
        get reducedMaxHpPercent() {
            return Math.max(0, Math.min(100, this.reducedMaxHp / this.potentialMaxHp * 100));
        },
        get ghostHpPercent() {
            return Math.max(0, Math.min(100, (this.ghostHp / this.potentialMaxHp) * 100));
        },
        get isBloodied() {
            return this.currentHp > 0 && this.currentHp / Math.max(1, this.maxHp) <= .5 && this.currentHp / Math.max(1, this.maxHp) > .05;
        },
        get isCritical() {
            return this.currentHp > 0 && this.currentHp / Math.max(1, this.maxHp) <= .05;
        },
        flashNpcHp(kind) {
            const property = kind === 'heal' ? 'flashingGreen' : (kind === 'shield' ? 'shakingBlue' : 'shakingRed');
            const timer = kind === 'heal' ? 'healFxTimer' : (kind === 'shield' ? 'shieldFxTimer' : 'damageFxTimer');
            clearTimeout(this[timer]);
            this[property] = false;
            this.$nextTick(() => {
                this[property] = true;
                this[timer] = setTimeout(() => { this[property] = false; }, 680);
            });
        },
        // Expansão mágica dourada somente quando o máximo efetivo cresce.
        flashMaxHpGain(amount) {
            clearTimeout(this.maxHpGainTimer);
            this.maxHpGainAmount = Math.max(0, Math.trunc(Number(amount) || 0));
            this.maxHpBoosting = false;
            this.$nextTick(() => {
                this.maxHpBoosting = true;
                this.maxHpGainTimer = setTimeout(() => { this.maxHpBoosting = false; }, 1100);
            });
        },
        announceNpcHp() {
            window.dispatchEvent(new CustomEvent('spellbound:npc-hp-change', {
                detail: {
                    npcId: {{ (int) $combatNpc->id }},
                    currentHp: this.currentHp,
                    maxHp: this.maxHp,
                    temporaryHp: this.directTempHp,
                },
            }));
        },
        updateGhostHp() {
            clearTimeout(this.ghostFxTimer);
            this.ghostFxTimer = setTimeout(() => { this.ghostHp = this.currentHp; }, 350);
        },
        applyNpcHpLocally(fieldName, amount) {
            const value = Math.max(0, Math.trunc(Number(amount) || 0));
            if (value < 1) return;
            if (fieldName === 'damage') {
                const shield = Math.min(Math.max(0, Number(this.directTempHp) || 0), value);
                const remainder = value - shield;
                if (shield > 0) {
                    this.directTempHp = Math.max(0, this.directTempHp - shield);
                    this.flashNpcHp('shield');
                }
                if (remainder > 0) {
                    this.currentHp = Math.max(0, this.currentHp - remainder);
                    this.flashNpcHp('damage');
                }
            } else if (fieldName === 'heal') {
                const next = Math.min(this.maxHp, this.currentHp + value);
                if (next !== this.currentHp) this.flashNpcHp('heal');
                this.currentHp = next;
            }
            this.updateGhostHp();
            this.announceNpcHp();
        },
        async triggerAction(url, fieldName, value) {
            if (this.maxHpSettingsSaving) return;
            const amount = Math.max(0, Math.trunc(Number(value) || 0));
            if (amount < 1 || !['damage', 'heal'].includes(fieldName)) return;
            this.hpError = '';
            this.applyNpcHpLocally(fieldName, amount);
            this.hpPending.push({ url, field: fieldName, amount });
            void this.flushNpcHpQueue();
        },
        // As setas mantêm os incrementos imediatos originais: -5/-1/+1/+5.
        quickStep(fieldName, amount) {
            const url = fieldName === 'damage'
                ? @js(route('combats.npcs.damage', [$combat->id, $combatNpc->id]))
                : @js(route('combats.npcs.heal', [$combat->id, $combatNpc->id]));
            void this.triggerAction(url, fieldName, amount);
        },
        toggleHpQuick() {
            this.maxHpSettingsOpen = false;
            this.hpPopoverOpen = !this.hpPopoverOpen;
            this.tempPopoverOpen = false;
            if (this.hpPopoverOpen) {
                this.$nextTick(() => this.$refs.hpQuickDamageInput?.focus());
            }
        },
        applyQuickHp(fieldName) {
            if (this.maxHpSettingsSaving) return;
            const raw = fieldName === 'damage' ? this.quickDamage : this.quickHeal;
            if (raw === '' || raw === null || raw === undefined) return;
            const amount = Number(raw);
            if (!Number.isSafeInteger(amount) || amount < 1 || amount > 999999) return;
            this.quickStep(fieldName, amount);
            if (fieldName === 'damage') this.quickDamage = '';
            else this.quickHeal = '';
            this.hpPopoverOpen = false;
        },
        toggleTempQuick() {
            this.maxHpSettingsOpen = false;
            this.tempPopoverOpen = !this.tempPopoverOpen;
            this.hpPopoverOpen = false;
            if (this.tempPopoverOpen) {
                this.tempHpDraft = this.directTempHp;
                this.$nextTick(() => {
                    this.$refs.tempQuickAmount?.focus();
                    this.$refs.tempQuickAmount?.select();
                });
            }
        },
        closeTempQuick() {
            this.tempPopoverOpen = false;
            this.tempHpDraft = this.directTempHp;
        },
        // Como na ficha do jogador: editar o VALOR TOTAL de PV temporários,
        // sem salvar no servidor até clicar em Aplicar ou pressionar Enter.
        applyQuickTemp() {
            if (this.maxHpSettingsSaving) return;
            const raw = this.tempHpDraft;
            if (raw === '' || raw === null || raw === undefined) return;
            const next = Number(raw);
            if (!Number.isSafeInteger(next) || next < 0 || next > 999999) return;
            const previous = Math.max(0, Math.trunc(Number(this.directTempHp) || 0));
            if (next === previous) { this.closeTempQuick(); return; }
            this.directTempHp = next;
            this.hpError = '';
            this.flashNpcHp('shield');
            this.announceNpcHp();
            this.hpPending.push({
                url: @js(route('combats.npcs.temporaryHp', [$combat->id, $combatNpc->id])),
                field: 'temporary_hp', amount: next,
            });
            void this.flushNpcHpQueue();
            this.closeTempQuick();
        },
        // Edita o máximo de PV com valores separados, seguindo o padrão do jogador.
        // Persistência: coluna base_max_hp + bônus e redução; max_hp é o valor efetivo.
        toggleMaxHpSettings() {
            if (this.maxHpSettingsSaving) return;
            this.maxHpSettingsOpen = !this.maxHpSettingsOpen;
            this.hpPopoverOpen = false;
            this.tempPopoverOpen = false;
            this.maxHpBonusDraft = this.bonusMaxHp;
            this.maxHpReductionDraft = this.reducedMaxHp;
            this.maxHpSettingsError = '';
            if (this.maxHpSettingsOpen) {
                this.$nextTick(() => this.$refs.maxHpBonusInput?.focus());
            }
        },
        closeMaxHpSettings() {
            if (this.maxHpSettingsSaving) return;
            this.maxHpSettingsOpen = false;
            this.maxHpBonusDraft = this.bonusMaxHp;
            this.maxHpReductionDraft = this.reducedMaxHp;
            this.maxHpSettingsError = '';
        },
        get draftEffectiveMaxHp() {
            const bonus = Number(this.maxHpBonusDraft);
            const reduction = Number(this.maxHpReductionDraft);
            if (!Number.isSafeInteger(bonus) || !Number.isSafeInteger(reduction)) return '—';
            return Math.max(1, this.baseMaxHp + bonus - reduction);
        },
        validMaxHpDrafts() {
            return [this.maxHpBonusDraft, this.maxHpReductionDraft].every(raw =>
                raw !== '' && raw !== null && raw !== undefined &&
                Number.isSafeInteger(Number(raw)) && Number(raw) >= 0 && Number(raw) <= 999999
            );
        },
        async applyMaxHpSettings() {
            if (this.maxHpSettingsSaving || this.hpSaving || this.hpPending.length || !this.validMaxHpDrafts()) return;
            const bonus = Number(this.maxHpBonusDraft);
            const reduction = Number(this.maxHpReductionDraft);
            if (bonus === this.bonusMaxHp && reduction === this.reducedMaxHp) {
                this.closeMaxHpSettings(); return;
            }
            this.maxHpSettingsSaving = true;
            this.maxHpSettingsError = '';
            try {
                const response = await fetch(@js(url('/combats/'.$combat->id.'/npcs/'.$combatNpc->id.'/max-hp')), {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': @js(csrf_token()),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ max_hp_bonus: bonus, max_hp_reduction: reduction }),
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const result = await response.json();
                if (typeof result.max_hp !== 'number' || typeof result.current_hp !== 'number') {
                    throw new Error('Resposta inválida do servidor');
                }
                const previousMaximum = this.maxHp;
                const previousCurrentHp = this.currentHp;
                this.baseMaxHp = Math.max(1, Number(result.base_max_hp));
                this.bonusMaxHp = Math.max(0, Number(result.max_hp_bonus));
                this.reducedMaxHp = Math.max(0, Number(result.max_hp_reduction));
                this.maxHp = Math.max(1, Number(result.max_hp));
                this.currentHp = Math.max(0, Number(result.current_hp));
                if (this.maxHp > previousMaximum) {
                    this.flashMaxHpGain(Math.max(0, this.currentHp - previousCurrentHp));
                }
                this.updateGhostHp();
                this.announceNpcHp();
                this.maxHpSettingsOpen = false;
            } catch (error) {
                console.error('Erro ao configurar máximo de PV do NPC:', error);
                this.maxHpSettingsError = 'Não foi possível salvar. Confira a migration e a rota da V65.2.';
            } finally {
                this.maxHpSettingsSaving = false;
            }
        },
        async resetMaxHpSettings() {
            this.maxHpBonusDraft = 0;
            this.maxHpReductionDraft = 0;
            await this.applyMaxHpSettings();
        },
        async flushNpcHpQueue() {
            if (this.hpSaving) return;
            this.hpSaving = true;
            try {
                while (this.hpPending.length) {
                    const operation = this.hpPending.shift();
                    const data = new FormData();
                    data.append('_token', '{{ csrf_token() }}');
                    data.append('_method', 'PATCH');
                    data.append(operation.field, operation.amount);
                    const response = await fetch(operation.url, {
                        method: 'POST', body: data, credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                }
            } catch (error) {
                console.error('Não foi possível sincronizar os PV do NPC:', error);
                this.hpError = 'A alteração pode não ter sido salva. Atualize o combate para sincronizar.';
                this.hpPending = [];
            } finally {
                this.hpSaving = false;
            }
        },
    }"
    x-on:keydown.escape.window="hpPopoverOpen = false; tempPopoverOpen = false; closeMaxHpSettings()"
    :class="{ 

        'bloodied-card': isBloodied, 

        'critical-card': isCritical 

    }"

    class="bg-[#faf8f2] border border-[#cdbb9f]/60 rounded-2xl shadow-md overflow-visible relative transition-all duration-300"

>

    @php

        $challengeRating = $header->challengeRating ?? $header->cr ?? $header->nd ?? null;

        $hasTempHp = $tempHp > 0;

        $hpBorderClass = $hasTempHp ? 'border border-sky-400/60 ring-1 ring-sky-400/30 shadow-[0_0_8px_rgba(56,189,248,0.25)]' : 'border border-black/40';

    @endphp



    {{-- CORPO PRINCIPAL DO CARD --}}

    <div class="rounded-t-2xl p-4 flex flex-col md:flex-row gap-5 items-center">



        {{-- COLUNA ESQUERDA: AVATAR E CA --}}

        <div class="flex flex-col items-center gap-2 shrink-0 w-full md:w-32 text-center select-none rounded-2xl transition-all duration-300">

            {{-- Moldura da Imagem --}}

            <div 

                :class="{ 'animate-photo-red-internal': shakingRed, 'animate-damage-blue': shakingBlue, 'animate-heal-green': flashingGreen }"

                class="sb-v65-photo-rim relative w-32 h-32 rounded-2xl bg-[#d9c7a7]/30 border border-[#cdbb9f]/40 p-1 shadow-inner group transition-shadow duration-300"

            >

                {{-- Container Interno para Recorte Seguro da Foto --}}

                <div 

                    class="sb-v65-photo w-full h-full rounded-xl overflow-hidden relative z-0"
                    :class="{ 'animate-damage-red': shakingRed, 'sb-v652-photo-repel': shakingBlue && !shakingRed }"

                >

                    @if($npcModel && $npcModel->image_path)

                        <img 

                            src="{{ asset('storage/'.$npcModel->image_path) }}" 

                            :class="{ 'grayscale opacity-60 contrast-125': currentHp === 0 }"

                            class="w-full h-full object-cover object-top transition-all duration-500"

                        >

                    @else

                        <div 

                            :class="{ 'grayscale opacity-60': currentHp === 0 }"

                            class="w-full h-full flex items-center justify-center bg-[#efe9dc] text-[#6b1d14] font-serif font-bold text-2xl transition-all duration-500"

                        >

                            {{ strtoupper(substr($npcName, 0, 2)) }}

                        </div>

                    @endif

                    {{-- V65.2: barreira de proteção no RETRATO, não somente na barra. --}}
                    <div x-show="shakingBlue" x-cloak
                        class="sb-v652-photo-ward pointer-events-none absolute inset-0 z-10 flex items-center justify-center"
                        aria-hidden="true">
                        <span class="sb-v652-photo-ward-ring absolute inset-[9%] rounded-xl border border-sky-100/80"></span>
                        <svg class="sb-v652-photo-ward-icon relative h-12 w-12 text-sky-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3 20 6v5c0 5.1-3.3 8.3-8 10-4.7-1.7-8-4.9-8-10V6l8-3Z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>

                </div>



                {{-- Badge da CA --}}

                <div class="absolute -top-2 -right-2 bg-white border border-[#cdbb9f] rounded-xl py-1 px-2 shadow-md text-center min-w-[42px] z-20">

                    <span class="block text-[8px] font-extrabold text-stone-400 uppercase tracking-wider leading-none">CA</span>

                    <span class="block text-base font-serif font-bold text-[#6b1d14] leading-none mt-0.5">

                        {{ $npcAc }}

                    </span>

                </div>

            </div>



            {{-- Tamanho e Tipo --}}

            <div class="text-[9px] font-extrabold tracking-widest text-[#8c6239]/80 uppercase mt-0.5 flex flex-col items-center gap-0.5 w-full">

                <span>{{ ucfirst($npcSize) }}</span>

                <span>{{ ucfirst($npcTypes) }}</span>

            </div>

        </div>



        {{-- COLUNA DIREITA: INFORMAÇÕES DE COMBATE --}}

        <div class="flex-1 w-full flex flex-col gap-3">



            <div class="flex items-center justify-between gap-3">

                <h2 class="font-serif text-2xl font-bold text-[#6b1d14] tracking-wide leading-tight">

                    {{ $npcName }}

                </h2>

            </div>



            {{-- SEÇÃO DE STATUS --}}

            <div class="flex flex-col gap-2.5 w-full mt-1">



                {{-- Barra de vida: preserva as setas originais e reproduz o painel da ficha do jogador. --}}
                <div class="relative z-30 group/npc-life" x-on:click.outside="hpPopoverOpen = false; closeTempQuick(); closeMaxHpSettings()">
                    {{-- Engrenagem: pequena e discreta no canto da barra, sem competir com as setas. --}}
                    <button type="button"
                        x-on:click.stop="toggleMaxHpSettings()"
                        :aria-expanded="maxHpSettingsOpen ? 'true' : 'false'"
                        aria-label="Configurar vida máxima do NPC"
                        title="Configurar vida máxima: bônus e redução"
                        class="absolute -top-[21px] right-1 z-20 flex h-5 w-5 items-center justify-center rounded-md border border-[#d8c7ab]/80 bg-[#faf8f2] opacity-50 shadow-sm transition hover:border-[#b79d77] hover:opacity-100 focus-visible:opacity-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#c5975b] group-hover/npc-life:opacity-85">
                        <img src="{{ asset('images/icons/sb-gear.svg') }}" alt="" class="h-3.5 w-3.5" aria-hidden="true" />
                    </button>
                    <div
                        class="sb-v65-hp-bar relative flex h-9 w-full items-center justify-between overflow-hidden rounded-lg border border-[#795842]/30 bg-[#1f1b1b] shadow-inner"
                        :class="{ 'sb-v65-damage-bar': shakingRed, 'sb-v65-shield-bar': shakingBlue && !shakingRed, 'sb-v65-heal-bar': flashingGreen, 'sb-v653-boost-bar': maxHpBoosting, 'ring-1 ring-sky-400/50': directTempHp > 0 }"
                    >
                        {{-- Retardo visual do dano e barra normal. --}}
                        <div class="pointer-events-none absolute inset-y-0 left-0 bg-red-950/75 transition-all duration-1000 ease-out" :style="`width:${ghostHpPercent}%`"></div>
                        {{-- PV normais, ouro para PV acima do máximo base e roxo para redução do máximo. --}}
                        <div class="pointer-events-none absolute inset-y-0 left-0 z-[1] transition-all duration-700 ease-out"
                            :class="currentHp / Math.max(1, maxHp) > .5 ? 'bg-emerald-700' : (currentHp / Math.max(1, maxHp) > .25 ? 'bg-amber-600' : 'bg-red-700')"
                            :style="`width:${normalHpPercent}%`"></div>
                        <div x-show="bonusHpPercent > 0" x-cloak
                            class="pointer-events-none absolute inset-y-0 z-[2] border-l border-amber-100/75 bg-gradient-to-r from-[#a7751e] via-[#d2a443] to-[#eed08a] transition-all duration-700 ease-out"
                            :style="`left:${normalHpPercent}%;width:${bonusHpPercent}%`"></div>
                        <div x-show="reducedMaxHp > 0" x-cloak
                            class="pointer-events-none absolute inset-y-0 right-0 z-[3] border-l border-violet-300/50 bg-[repeating-linear-gradient(135deg,rgba(106,58,145,.63),rgba(106,58,145,.63)_5px,rgba(53,24,73,.72)_5px,rgba(53,24,73,.72)_9px)] transition-all duration-700 ease-out"
                            :style="`width:${reducedMaxHpPercent}%`"></div>
                        {{-- Armadura = película AZUL COMPLETA. O número representa a reserva,
                             mas a largura não representa uma quantidade de vida. --}}
                        <div x-show="directTempHp > 0" x-cloak
                            class="pointer-events-none absolute inset-0 z-[5] overflow-hidden border-y border-sky-200/80 bg-gradient-to-b from-[#bde9fc]/90 via-[#64b5e0]/83 to-[#286b9f]/86 transition-opacity duration-300">
                            <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/25 to-transparent"></div>
                            <div class="absolute inset-0 opacity-[.13] bg-[repeating-linear-gradient(125deg,transparent,transparent_10px,#ffffff_10px,#ffffff_11px)]"></div>
                        </div>

                        {{-- V65.3: energia dourada na VIDA ATUAL, sem ser confundida com escudo azul. --}}
                        <div x-show="maxHpBoosting" x-cloak
                            class="sb-v653-boost-fill pointer-events-none absolute inset-y-0 left-0 z-[7] overflow-hidden"
                            :style="`width:${hpPercent}%`" aria-hidden="true">
                            <span class="sb-v653-boost-shine"></span>
                        </div>

                        {{-- DANO RÁPIDO: -5 / -1 --}}
                        <div class="relative z-10 flex h-full shrink-0 items-center px-1" role="group" aria-label="Dano rápido">
                            <button type="button" title="Aplicar 5 de dano" aria-label="Aplicar 5 de dano"
                                x-on:click="quickStep('damage', 5)"
                                class="flex h-full min-w-7 items-center justify-center px-1.5 font-serif text-lg font-bold text-white/75 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-sky-200">«</button>
                            <button type="button" title="Aplicar 1 de dano" aria-label="Aplicar 1 de dano"
                                x-on:click="quickStep('damage', 1)"
                                class="flex h-full min-w-7 items-center justify-center px-1.5 font-serif text-lg font-bold text-white/75 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-sky-200">‹</button>
                        </div>

                        {{-- PV: clique abre a gaveta; escudo tem editor independente. --}}
                        <div class="relative z-10 flex min-w-0 flex-1 items-center justify-center gap-1.5 px-1">
                            <button type="button"
                                x-on:click.stop="toggleHpQuick()"
                                :aria-expanded="hpPopoverOpen ? 'true' : 'false'"
                                aria-label="Abrir gaveta de dano e cura do NPC"
                                title="Aplicar dano ou cura"
                                class="inline-flex h-7 min-w-0 items-center gap-1 rounded-md px-2 font-serif text-xs font-bold tracking-wide text-white drop-shadow-[0_1px_2px_rgba(0,0,0,.7)] transition hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-200">
                                <span class="tabular-nums" x-text="currentHp"></span>
                                <span class="text-white/60">/</span>
                                <span class="tabular-nums" x-text="maxHp"></span>
                            </button>
                            <button type="button"
                                x-on:click.stop="toggleTempQuick()"
                                :aria-expanded="tempPopoverOpen ? 'true' : 'false'"
                                aria-label="Abrir editor de pontos de vida temporários do NPC"
                                title="Editar PV temporários"
                                class="inline-flex h-6 min-w-10 shrink-0 items-center justify-center gap-1 rounded-md border px-1.5 text-[10px] font-black tabular-nums transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-200"
                                :class="directTempHp > 0 ? 'border-sky-300/70 bg-sky-100/90 text-sky-800 hover:bg-sky-100' : 'border-white/20 bg-black/25 text-white/75 hover:bg-black/40'">
                                <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"/></svg>
                                <span x-text="directTempHp"></span>
                            </button>
                        </div>

                        {{-- CURA RÁPIDA: +1 / +5 --}}
                        <div class="relative z-10 flex h-full shrink-0 items-center px-1" role="group" aria-label="Cura rápida">
                            <button type="button" title="Curar 1 PV" aria-label="Curar 1 PV"
                                x-on:click="quickStep('heal', 1)"
                                class="flex h-full min-w-7 items-center justify-center px-1.5 font-serif text-lg font-bold text-white/75 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-sky-200">›</button>
                            <button type="button" title="Curar 5 PV" aria-label="Curar 5 PV"
                                x-on:click="quickStep('heal', 5)"
                                class="flex h-full min-w-7 items-center justify-center px-1.5 font-serif text-lg font-bold text-white/75 transition hover:bg-white/10 hover:text-white focus-visible:outline-2 focus-visible:outline-sky-200">»</button>
                        </div>
                    </div>

                    {{-- Modificadores discretos de vida máxima, sem ocupar a barra. --}}
                    <div x-show="bonusMaxHp > 0 || reducedMaxHp > 0" x-cloak
                        class="absolute -top-[21px] right-8 z-10 flex h-5 items-center gap-1 text-[9px] font-black tabular-nums">
                        <span x-show="bonusMaxHp > 0" x-text="'+' + bonusMaxHp"
                            class="rounded border border-amber-300/75 bg-amber-50 px-1 text-amber-800" title="Bônus de vida máxima"></span>
                        <span x-show="reducedMaxHp > 0" x-text="'−' + reducedMaxHp"
                            class="rounded border border-violet-300/75 bg-violet-50 px-1 text-violet-800" title="Redução de vida máxima"></span>
                    </div>

                    {{-- GAVETA DANO/CURA: duas ações independentes, padrão da ficha do jogador. --}}
                    <div x-show="hpPopoverOpen" x-cloak
                        x-transition:enter="transition duration-150 ease-out"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition duration-100 ease-in"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
                        x-on:click.stop
                        class="sb-v65-hp-popover absolute left-1/2 top-full z-[115] mt-2 w-[min(320px,calc(100vw-2rem))] -translate-x-1/2 rounded-xl border border-[#cdbb9f]/85 bg-[#fffaf2] p-2 text-[#34231b]"
                        role="group" aria-label="Dano e cura rápidos">
                        <span class="absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-l border-t border-[#cdbb9f]/85 bg-[#fffaf2]" aria-hidden="true"></span>
                        <div class="relative grid grid-cols-2 gap-2">
                            <div class="rounded-lg border border-[#a0523d]/25 bg-[#f8f0e6] p-2">
                                <label for="npc-v65-damage-{{ $combatNpc->id }}" class="block text-[9px] font-black uppercase tracking-[.12em] text-[#8b2a20]">Dano</label>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <input id="npc-v65-damage-{{ $combatNpc->id }}" x-ref="hpQuickDamageInput"
                                        x-model.number="quickDamage" x-on:keydown.enter.prevent="applyQuickHp('damage')"
                                        type="number" min="1" max="999999" step="1" inputmode="numeric" placeholder="0"
                                        class="h-8 min-w-0 flex-1 rounded-md border border-[#cdbb9f] bg-[#fffdf8] px-2 text-center font-serif text-sm font-black text-[#53150f] outline-none focus:border-[#8b2a20] focus:ring-2 focus:ring-[#8b2a20]/10" />
                                    <button type="button" x-on:click="applyQuickHp('damage')"
                                        :disabled="quickDamage === '' || !Number.isSafeInteger(Number(quickDamage)) || Number(quickDamage) < 1 || Number(quickDamage) > 999999"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-[#7f261d]/25 bg-[#7f261d] font-serif text-sm font-black text-[#fffaf2] transition hover:bg-[#651e18] disabled:cursor-not-allowed disabled:opacity-40"
                                        title="Aplicar dano" aria-label="Aplicar dano">−</button>
                                </div>
                            </div>
                            <div class="rounded-lg border border-emerald-800/20 bg-[#f8f0e6] p-2">
                                <label for="npc-v65-heal-{{ $combatNpc->id }}" class="block text-[9px] font-black uppercase tracking-[.12em] text-emerald-800">Cura</label>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <input id="npc-v65-heal-{{ $combatNpc->id }}"
                                        x-model.number="quickHeal" x-on:keydown.enter.prevent="applyQuickHp('heal')"
                                        type="number" min="1" max="999999" step="1" inputmode="numeric" placeholder="0"
                                        class="h-8 min-w-0 flex-1 rounded-md border border-[#cdbb9f] bg-[#fffdf8] px-2 text-center font-serif text-sm font-black text-emerald-900 outline-none focus:border-emerald-800 focus:ring-2 focus:ring-emerald-800/10" />
                                    <button type="button" x-on:click="applyQuickHp('heal')"
                                        :disabled="quickHeal === '' || !Number.isSafeInteger(Number(quickHeal)) || Number(quickHeal) < 1 || Number(quickHeal) > 999999"
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-emerald-900/15 bg-[#047857] font-serif text-sm font-black text-white transition hover:bg-[#065f46] disabled:cursor-not-allowed disabled:opacity-40"
                                        title="Aplicar cura" aria-label="Aplicar cura">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1.5 flex items-center justify-between gap-2 px-0.5 text-[9px] text-[#8a6d5a]">
                            <span>Enter aplica o valor.</span>
                            <button type="button" x-on:click="hpPopoverOpen = false" class="font-black uppercase tracking-[.08em] text-[#93623d] hover:text-[#53150f]">Fechar</button>
                        </div>
                    </div>

                    {{-- V65.2: engrenagem de máximo original + bônus / redução persistentes. --}}
                    <div x-show="maxHpSettingsOpen" x-cloak
                        x-transition:enter="transition duration-150 ease-out"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition duration-100 ease-in"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
                        x-on:click.stop
                        class="sb-v65-hp-popover absolute right-0 top-full z-[118] mt-2 w-[min(335px,calc(100vw-2rem))] rounded-xl border border-[#cdbb9f]/85 bg-[#fffaf2] p-3 text-[#34231b]"
                        role="group" aria-label="Configuração de vida máxima do NPC">
                        <span class="absolute -top-1.5 right-1.5 h-3 w-3 rotate-45 border-l border-t border-[#cdbb9f]/85 bg-[#fffaf2]" aria-hidden="true"></span>
                        <div class="flex items-center gap-2 border-b border-[#eadfce] pb-2">
                            <img src="{{ asset('images/icons/sb-gear.svg') }}" alt="" class="h-4 w-4" aria-hidden="true" />
                            <div class="min-w-0 flex-1">
                                <h3 class="font-serif text-[13px] font-bold text-[#6b1d14]">Vida máxima</h3>
                                <p class="text-[10px] text-[#8c6b53]">Ajustes durante este combate</p>
                            </div>
                            <button type="button" x-on:click="closeMaxHpSettings()" class="rounded p-1 text-[#9b7a60] hover:bg-[#f5ede1] hover:text-[#6b1d14]" title="Fechar configuração" aria-label="Fechar configuração">×</button>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-2 rounded-lg border border-[#eadfce] bg-[#f8f2e8] px-2.5 py-1.5 text-[11px]">
                            <span class="font-semibold text-[#806148]">Máximo original</span>
                            <strong class="font-serif text-sm text-[#6b1d14]" x-text="baseMaxHp"></strong>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <div class="rounded-lg border border-amber-700/20 bg-amber-50/80 p-2">
                                <label for="npc-maxhp-bonus-{{ $combatNpc->id }}" class="block text-[9px] font-black uppercase tracking-wide text-amber-800">+ Vida máxima</label>
                                <input id="npc-maxhp-bonus-{{ $combatNpc->id }}" x-ref="maxHpBonusInput"
                                    x-model.number="maxHpBonusDraft" x-on:keydown.enter.prevent="applyMaxHpSettings()"
                                    type="number" min="0" max="999999" step="1" inputmode="numeric"
                                    class="mt-1 h-9 w-full rounded-md border border-[#decda8] bg-white px-2 text-center font-serif text-sm font-bold text-amber-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400/20" />
                            </div>
                            <div class="rounded-lg border border-violet-700/20 bg-violet-50/70 p-2">
                                <label for="npc-maxhp-reduction-{{ $combatNpc->id }}" class="block text-[9px] font-black uppercase tracking-wide text-violet-800">− Vida máxima</label>
                                <input id="npc-maxhp-reduction-{{ $combatNpc->id }}" x-model.number="maxHpReductionDraft"
                                    x-on:keydown.enter.prevent="applyMaxHpSettings()"
                                    type="number" min="0" max="999999" step="1" inputmode="numeric"
                                    class="mt-1 h-9 w-full rounded-md border border-[#d6c8e4] bg-white px-2 text-center font-serif text-sm font-bold text-violet-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-400/20" />
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between rounded-lg bg-[#eee4d5]/65 px-2.5 py-2 text-[11px]">
                            <span class="font-semibold text-[#806148]">Máximo efetivo</span>
                            <strong class="font-serif text-base font-bold text-[#6b1d14]" x-text="draftEffectiveMaxHp"></strong>
                        </div>
                        <p class="mt-1.5 text-[9px] leading-relaxed text-[#8c6b53]">Aumentar o máximo acrescenta a diferença aos PV atuais. Reduções limitam os PV ao novo máximo.</p>
                        <p x-show="maxHpSettingsError" x-cloak x-text="maxHpSettingsError" class="mt-2 text-[10px] font-bold text-red-800" role="alert"></p>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <button type="button" x-on:click="resetMaxHpSettings()"
                                :disabled="maxHpSettingsSaving || hpSaving || (bonusMaxHp === 0 && reducedMaxHp === 0)"
                                class="rounded-md border border-[#d3c1a9] bg-white px-2.5 py-2 text-[10px] font-bold text-[#805c42] transition hover:bg-[#f2e9dd] disabled:opacity-40">Restaurar</button>
                            <button type="button" x-on:click="applyMaxHpSettings()"
                                :disabled="maxHpSettingsSaving || hpSaving || !validMaxHpDrafts()"
                                class="rounded-md border border-[#6b1d14] bg-[#6b1d14] px-3.5 py-2 text-[10px] font-black text-white transition hover:bg-[#53150f] disabled:opacity-40">
                                <span x-text="maxHpSettingsSaving ? 'Salvando…' : 'Aplicar'">Aplicar</span>
                            </button>
                        </div>
                    </div>
                    {{-- PV temporários: valor total, só salva ao confirmar (mesmo fluxo do jogador). --}}
                    <div x-show="tempPopoverOpen" x-cloak
                        x-transition:enter="transition duration-150 ease-out"
                        x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition duration-100 ease-in"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
                        x-on:click.stop
                        class="sb-v65-hp-popover absolute left-1/2 top-full z-[116] mt-2 w-[min(285px,calc(100vw-2rem))] -translate-x-1/2 rounded-xl border border-[#cdbb9f]/85 bg-[#fffaf2] p-3 text-[#34231b]"
                        role="group" aria-label="Editar pontos de vida temporários">
                        <span class="absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-l border-t border-[#cdbb9f]/85 bg-[#fffaf2]" aria-hidden="true"></span>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-sky-500/25 bg-sky-400/10 text-sky-600" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 19 6v5.2c0 4.3-2.7 7.7-7 9.3-4.3-1.6-7-5-7-9.3V6l7-2.5Z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <label for="npc-v65-temp-{{ $combatNpc->id }}" class="block text-[10px] font-black uppercase tracking-[.08em] text-[#53150f]">PV temporários</label>
                                <p class="mt-0.5 text-[9px] text-[#8a6d5a]">Defina o valor total.</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <input id="npc-v65-temp-{{ $combatNpc->id }}" x-ref="tempQuickAmount" x-model.number="tempHpDraft"
                                x-on:keydown.enter.prevent="applyQuickTemp()"
                                type="number" min="0" max="999999" step="1" inputmode="numeric"
                                class="h-9 min-w-0 flex-1 rounded-lg border border-[#cdbb9f] bg-[#fffdf8] px-3 text-center font-serif text-sm font-black text-[#53150f] outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/15" />
                            <button type="button" x-on:click="applyQuickTemp()"
                                :disabled="tempHpDraft === '' || !Number.isSafeInteger(Number(tempHpDraft)) || Number(tempHpDraft) < 0 || Number(tempHpDraft) > 999999"
                                class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg border border-[#cdbb9f] bg-[#6b1d14] px-3 text-[10px] font-black text-white transition hover:bg-[#53150f] disabled:cursor-not-allowed disabled:opacity-40">Aplicar</button>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-2 text-[9px] text-[#8a6d5a]">
                            <span>Use 0 para remover.</span>
                            <button type="button" x-on:click="closeTempQuick()" class="font-bold text-[#93623d] hover:text-[#53150f]">Fechar</button>
                        </div>
                    </div>
                </div>
                <p x-cloak x-show="hpError" x-text="hpError" class="text-[11px] font-semibold text-red-800" role="alert"></p>

                {{-- V65.3 — Informações de combate mais leves, sem caixas dentro de caixas. --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 pt-1 text-[#65452d]">
                    <div class="inline-flex min-h-8 max-w-full flex-wrap items-center gap-x-2 gap-y-1 border-b border-[#d7c3a4]/75 pb-1" title="Deslocamentos disponíveis">
                        <span aria-hidden="true" class="npc-speed-icon inline-block h-[17px] w-[17px] shrink-0 select-none bg-[#a88763]" style="-webkit-mask-image: url('{{ asset('images/ui/combat/movement-reference.svg') }}'); mask-image: url('{{ asset('images/ui/combat/movement-reference.svg') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></span>
                        <span class="text-[9px] font-extrabold uppercase tracking-[.12em] text-[#8c6239]">Deslocamento</span>
                        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5 font-serif text-[12px] text-[#6b1d14]">
                            @forelse($speedChips as $speedChip)
                                @unless($loop->first)<span class="font-sans text-[10px] text-[#b89a77]" aria-hidden="true">·</span>@endunless
                                <span class="whitespace-nowrap">
                                    @if($speedChip['label'] !== 'Andar')<span class="font-sans text-[10px] font-semibold text-[#8c6239]">{{ $speedChip['label'] }}</span> @endif<strong class="font-bold">{{ $speedChip['value'] }}</strong>
                                </span>
                            @empty
                                <span>—</span>
                            @endforelse
                        </div>
                    </div>
                    @if($npcInitiative !== null && $npcInitiative !== '')
                        <div class="inline-flex min-h-8 items-center gap-2 border-b border-[#d7c3a4]/75 pb-1" title="Modificador de iniciativa">
                            <span aria-hidden="true" class="npc-initiative-icon inline-block h-[20px] w-[20px] shrink-0 select-none bg-[#a88763]" style="-webkit-mask-image: url('{{ asset('images/ui/combat/initiative-shape-mask.png') }}'); mask-image: url('{{ asset('images/ui/combat/initiative-shape-mask.png') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></span>
                            <span class="text-[9px] font-extrabold uppercase tracking-[.12em] text-[#8c6239]">Iniciativa</span>
                            <strong class="font-serif text-[14px] font-bold text-[#6b1d14]">{{ $npcInitiative }}</strong>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>



    {{-- FICHA EXPANDIDA (MÉTODO CESTA) --}}

    <div class="rounded-b-2xl border-t border-[#d8c7ab] bg-[#fcfaf6] p-5 w-full">

        @if($isBuilder)

            @include('combats.components.npc.npc-native-sheet', [

                'abilityNames' => AbilityNames::class,

                'alignments' => Alignments::class,

                'conditions' => Conditions::class,

                'damageTypes' => DamageTypes::class,

                'languages' => Languages::class,

                'npcSizes' => NpcSizes::class,

                'npcTypes' => NpcTypes::class,

                'senses' => Senses::class,

                'skillNames' => SkillNames::class,

            ])

        @else

            @include('combats.components.npc.npc-sheet', [

                'abilityNames' => AbilityNames::class,

                'alignments' => Alignments::class,

                'conditions' => Conditions::class,

                'damageTypes' => DamageTypes::class,

                'languages' => Languages::class,

                'npcSizes' => NpcSizes::class,

                'npcTypes' => NpcTypes::class,

                'senses' => Senses::class,

                'skillNames' => SkillNames::class,

            ])

        @endif

        @include('combats.components.npc.spell-hover', [
            'combatNpc' => $combatNpc,
            'combat' => $combat ?? null,
        ])
    </div>

</div>