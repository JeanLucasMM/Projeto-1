@php
    $details = [
        'Perícias' => $npc->combat?->skills ?? null,
        'Resistências a Dano' => $npc->combat?->resistances ?? null,
        'Imunidades a Dano' => $npc->combat?->immunities ?? null,
        'Imunidades a Condição' => $npc->combat?->conditionImmunities ?? null,
        'Vulnerabilidades a Dano' => $npc->combat?->vulnerabilities ?? null,
        'Sentidos' => $npc->combat?->senses ?? null,
        'Idiomas' => $npc->combat?->languages ?? null,
    ];
@endphp

<div class="details">
    @foreach($details as $label => $value)
        @if(!empty($value) && $value !== '-')
            <div class="detail-row">
                <strong>{{ $label }}:</strong> {{ $value }}
            </div>
        @endif
    @endforeach

    @if(!empty($npc->header?->challengeRating))
        <div class="detail-row">
            <strong>Nível de Desafio:</strong> {{ $npc->header->challengeRating }}
            @if(isset($npc->header?->proficiencyBonus))
                (PB +{{ $npc->header->proficiencyBonus }})
            @endif
        </div>
    @endif
</div>