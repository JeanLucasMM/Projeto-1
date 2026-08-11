@props([
    'combat',
    'abilities',
    'skills',
    'header',
])

@php

use App\Support\Dictionaries\DamageTypes;
use App\Support\Dictionaries\Conditions;


/*
|--------------------------------------------------------------------------
| Tabela de Desafio
|--------------------------------------------------------------------------
*/

$challengeTable = [

    '0'   => ['xp' => 10, 'pb' => 2],
    '1/8' => ['xp' => 25, 'pb' => 2],
    '1/4' => ['xp' => 50, 'pb' => 2],
    '1/2' => ['xp' => 100, 'pb' => 2],

    '1' => ['xp' => 200, 'pb' => 2],
    '2' => ['xp' => 450, 'pb' => 2],
    '3' => ['xp' => 700, 'pb' => 2],
    '4' => ['xp' => 1100, 'pb' => 2],

    '5' => ['xp' => 1800, 'pb' => 3],
    '6' => ['xp' => 2300, 'pb' => 3],
    '7' => ['xp' => 2900, 'pb' => 3],
    '8' => ['xp' => 3900, 'pb' => 3],

    '9'  => ['xp' => 5000, 'pb' => 4],
    '10' => ['xp' => 5900, 'pb' => 4],
    '11' => ['xp' => 7200, 'pb' => 4],
    '12' => ['xp' => 8400, 'pb' => 4],

    '13' => ['xp' => 10000, 'pb' => 5],
    '14' => ['xp' => 11500, 'pb' => 5],
    '15' => ['xp' => 13000, 'pb' => 5],
    '16' => ['xp' => 15000, 'pb' => 5],

    '17' => ['xp' => 18000, 'pb' => 6],
    '18' => ['xp' => 20000, 'pb' => 6],
    '19' => ['xp' => 22000, 'pb' => 6],
    '20' => ['xp' => 25000, 'pb' => 6],

    '21' => ['xp' => 33000, 'pb' => 7],
    '22' => ['xp' => 41000, 'pb' => 7],
    '23' => ['xp' => 50000, 'pb' => 7],
    '24' => ['xp' => 62000, 'pb' => 7],

    '25' => ['xp' => 75000, 'pb' => 8],
    '26' => ['xp' => 90000, 'pb' => 8],
    '27' => ['xp' => 105000, 'pb' => 8],
    '28' => ['xp' => 120000, 'pb' => 8],

    '29' => ['xp' => 135000, 'pb' => 9],
    '30' => ['xp' => 155000, 'pb' => 9],

];


/*
|--------------------------------------------------------------------------
| Desafio
|--------------------------------------------------------------------------
*/

$cr = (string) $header->challengeRating;

$challengeData = $challengeTable[$cr] ?? [
    'xp' => 0,
    'pb' => 2,
];

$proficiencyBonus = $challengeData['pb'];
$xp = $challengeData['xp'];


/*
|--------------------------------------------------------------------------
| Perícias
|--------------------------------------------------------------------------
*/

$activeSkills = [];

foreach ($skills->skills ?? [] as $skill) {

    if (
        !($skill['enabled'] ?? false)
        &&
        !($skill['proficient'] ?? false)
        &&
        !($skill['expertise'] ?? false)
        &&
        (($skill['bonus'] ?? 0) == 0)
    ) {
        continue;
    }


    $ability = $skill['ability'] ?? null;

    if (
        !$ability
        ||
        !isset($abilities->{$ability})
    ) {
        continue;
    }


    $abilityModifier = floor(
        ($abilities->{$ability} - 10) / 2
    );


    $bonus = $abilityModifier;


    if ($skill['proficient'] ?? false) {

        $bonus += $proficiencyBonus;

    }


    if ($skill['expertise'] ?? false) {

        $bonus += $proficiencyBonus;

    }


    if (($skill['bonus'] ?? 0) != 0) {

        $bonus += (int) $skill['bonus'];

    }


    $activeSkills[] = [

        'key' =>
            $skill['key'] ?? '',

        'label' =>
            $skill['label'] ?? '',

        'bonus' =>
            $bonus,

    ];

}


/*
|--------------------------------------------------------------------------
| Sentidos
|--------------------------------------------------------------------------
*/

$senses = [];

foreach ($combat->senses ?? [] as $type => $value) {

    if ((int) $value <= 0) {
        continue;
    }


    $label = match ($type) {

        'darkvision' =>
            'Visão no Escuro',

        'blindsight' =>
            'Visão às Cegas',

        'tremorsense' =>
            'Sentido Sísmico',

        'truesight' =>
            'Visão Verdadeira',

        default =>
            ucfirst($type),

    };


    $senses[] =
        "{$label} {$value} ft";

}


foreach ($combat->customSenses ?? [] as $customSense) {

    if (is_array($customSense)) {

        $name = trim(
            (string) ($customSense['name'] ?? '')
        );

        $distance =
            $customSense['distance'] ?? null;


        if ($name === '') {
            continue;
        }


        if ($distance !== null && $distance !== '') {

            $senses[] =
                "{$name} {$distance} ft";

        } else {

            $senses[] =
                $name;

        }

        continue;
    }


    if (
        is_string($customSense)
        &&
        trim($customSense) !== ''
    ) {

        $senses[] =
            trim($customSense);

    }

}


/*
|--------------------------------------------------------------------------
| Percepção Passiva
|--------------------------------------------------------------------------
*/

$wisModifier = floor(
    ($abilities->wis - 10) / 2
);


$passivePerception =
    10
    + $wisModifier;


$perceptionSkill = null;

foreach ($skills->skills ?? [] as $skill) {

    if (
        ($skill['key'] ?? '') === 'perception'
    ) {

        $perceptionSkill = $skill;

        break;

    }

}


if ($perceptionSkill) {

    if ($perceptionSkill['proficient'] ?? false) {

        $passivePerception +=
            $proficiencyBonus;

    }


    if ($perceptionSkill['expertise'] ?? false) {

        $passivePerception +=
            $proficiencyBonus;

    }


    if (($perceptionSkill['bonus'] ?? 0) != 0) {

        $passivePerception +=
            (int) $perceptionSkill['bonus'];

    }

}


$passivePerception +=
    (int) ($combat->passivePerceptionBonus ?? 0);


/*
|--------------------------------------------------------------------------
| Idiomas
|--------------------------------------------------------------------------
*/

$languages =
    $combat->languages ?? [];


/*
|--------------------------------------------------------------------------
| Tradução dos tipos de dano
|--------------------------------------------------------------------------
*/

$resistances = collect($combat->resistances ?? [])
    ->map(fn ($type) => DamageTypes::label($type))
    ->all();

$immunities = collect($combat->immunities ?? [])
    ->map(fn ($type) => DamageTypes::label($type))
    ->all();

$vulnerabilities = collect($combat->vulnerabilities ?? [])
    ->map(fn ($type) => DamageTypes::label($type))
    ->all();


/*
|--------------------------------------------------------------------------
| Tradução das condições
|--------------------------------------------------------------------------
*/

$conditionImmunities = collect($combat->conditionImmunities ?? [])
    ->map(fn ($condition) => Conditions::label($condition))
    ->all();

@endphp


<div class="native-combat-details">

    {{-- ====================================================================== --}}
    {{-- Perícias --}}
    {{-- ====================================================================== --}}

    @if(count($activeSkills))

        <div class="native-combat-row">

            <strong>
                Perícias:
            </strong>


            @foreach($activeSkills as $skill)

                {{ $skill['label'] }}

                {{ $skill['bonus'] >= 0
                    ? '+' . $skill['bonus']
                    : $skill['bonus']
                }}

                @if(!$loop->last)
                    ,
                @endif

            @endforeach

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Resistências --}}
    {{-- ====================================================================== --}}

    @if(count($resistances))

        <div class="native-combat-row">

            <strong>
                Resistências a Dano:
            </strong>

            {{ implode(', ', $resistances) }}

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Imunidades a Dano --}}
    {{-- ====================================================================== --}}

    @if(count($immunities))

        <div class="native-combat-row">

            <strong>
                Imunidades a Dano:
            </strong>

            {{ implode(', ', $immunities) }}

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Imunidades a Condição --}}
    {{-- ====================================================================== --}}

    @if(count($conditionImmunities))

        <div class="native-combat-row">

            <strong>
                Imunidades a Condição:
            </strong>

            {{ implode(', ', $conditionImmunities) }}

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Vulnerabilidades --}}
    {{-- ====================================================================== --}}

    @if(count($vulnerabilities))

        <div class="native-combat-row">

            <strong>
                Vulnerabilidades a Dano:
            </strong>

            {{ implode(', ', $vulnerabilities) }}

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Sentidos --}}
    {{-- ====================================================================== --}}

    @if(count($senses) || $passivePerception != 10)

        <div class="native-combat-row">

            <strong>
                Sentidos:
            </strong>


            @if(count($senses))

                {{ implode(', ', $senses) }}

                @if($passivePerception != 10)
                    ,
                @endif

            @endif


            @if($passivePerception != 10)

                Percepção passiva {{ $passivePerception }}

            @endif

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Idiomas --}}
    {{-- ====================================================================== --}}

    @if(count($languages))

        <div class="native-combat-row">

            <strong>
                Idiomas:
            </strong>

            {{ implode(', ', $languages) }}

        </div>

    @endif


    {{-- ====================================================================== --}}
    {{-- Nível de Desafio --}}
    {{-- ====================================================================== --}}

    <div class="native-combat-row">

        <strong>
            Nível de Desafio:
        </strong>

        {{ $header->challengeRating }}

        (Prof. +{{ $proficiencyBonus }},
        {{ number_format($xp, 0, ',', '.') }} XP)

    </div>

</div>