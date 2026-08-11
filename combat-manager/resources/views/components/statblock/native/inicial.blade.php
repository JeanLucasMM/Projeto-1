@props([
    'combat',
    'abilities',
    'speed'
])

@php

// =====================
// Classe de Armadura
// =====================

$dexModifier =
    floor(($abilities->dex - 10) / 2);

$armorClass =
    10
    + $dexModifier
    + $combat->acBonus;


// =====================
// Pontos de Vida
// =====================

$conModifier =
    floor(($abilities->con - 10) / 2);


if($combat->hpMode === 'custom') {

    $hp = $combat->customHp;

} else {

    $averageDie = match($combat->hitDie) {

        'd4'  => 2.5,
        'd6'  => 3.5,
        'd8'  => 4.5,
        'd10' => 5.5,
        'd12' => 6.5,
        'd20' => 10.5,

        default => 0

    };


    $hp =
        floor(
            ($combat->hitDiceCount * $averageDie)
            +
            ($combat->hitDiceCount * $conModifier)
            +
            $combat->hpModifierExtra
        );

}


$hpFormula =
    $combat->hitDiceCount
    . $combat->hitDie;


if($conModifier != 0) {

    $hpFormula .=
        ($conModifier > 0 ? '+' : '')
        . $conModifier;

}


if($combat->hpModifierExtra != 0) {

    $hpFormula .=
        ($combat->hpModifierExtra > 0 ? '+' : '')
        . $combat->hpModifierExtra;

}


// =====================
// Deslocamento
// =====================

$movement = [];


if($speed->walk){

    $movement[] =
        "{$speed->walk} ft";

}


if($speed->climb){

    $movement[] =
        "escalada {$speed->climb} ft";

}


if($speed->swim){

    $movement[] =
        "natação {$speed->swim} ft";

}


if($speed->burrow){

    $movement[] =
        "escavação {$speed->burrow} ft";

}


if($speed->fly){

    $fly =
        "voo {$speed->fly} ft";


    if($speed->hover){

        $fly .= " (pairar)";

    }


    $movement[] = $fly;

}


// =====================
// Salto
// =====================

$strModifier =
    floor(($abilities->str - 10) / 2);


$jump = [];


if($speed->hasJumps){


    $horizontal =
        max(
            0,
            3 * $strModifier
            + $speed->jumpHorizontalBonus
        );


    $vertical =
        max(
            0,
            3
            + $strModifier
            + $speed->jumpVerticalBonus
        );


    $jump[] =
        "{$horizontal} ft (horizontal)";


    $jump[] =
        "{$vertical} ft (vertical)";


}

@endphp


{{-- Classe de Armadura --}}
<div class="mb-1">
    <strong class="text-[#6b1d14]">
        Classe de Armadura:
    </strong>
    {{ $armorClass }}
    @if($combat->acType)
        ({{ $combat->acType }})
    @endif
</div>



{{-- Pontos de Vida --}}
<div class="mb-1">
    <strong class="text-[#6b1d14]">
        Pontos de Vida:
    </strong>
    {{ $hp }} ({{ $hpFormula }})
</div>


{{-- Deslocamento --}}
@if(count($movement))
    <div class="mb-1">
        <strong class="text-[#6b1d14]">
            Deslocamento:
        </strong>
        {{ implode(', ', $movement) }}
    </div>
@endif



{{-- Salto --}}
@if(count($jump))
    <div class="mb-1">
        <strong class="text-[#6b1d14]">
            Salto:
        </strong>
        {{ implode(', ', $jump) }}
    </div>
@endif