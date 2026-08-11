@props([
    'header'
])

@php
    $sizeLabels = [
        'tiny' => 'Minúsculo',
        'small' => 'Pequeno',
        'medium' => 'Médio',
        'large' => 'Grande',
        'huge' => 'Enorme',
        'gargantuan' => 'Gargantual',
    ];

    $typeLabels = [
        'aberration' => 'Aberração',
        'beast' => 'Besta',
        'celestial' => 'Celestial',
        'construct' => 'Constructo',
        'dragon' => 'Dragão',
        'elemental' => 'Elemental',
        'fey' => 'Feérico',
        'fiend' => 'Ínfero',
        'giant' => 'Gigante',
        'humanoid' => 'Humanoide',
        'monstrosity' => 'Monstruosidade',
        'ooze' => 'Gosma',
        'plant' => 'Planta',
        'undead' => 'Morto-vivo',
    ];

    $alignmentLabels = [
        'lawful_good' => 'Leal e Bom',
        'neutral_good' => 'Neutro e Bom',
        'chaotic_good' => 'Caótico e Bom',
        'lawful_neutral' => 'Leal e Neutro',
        'true_neutral' => 'Verdadeiro Neutro',
        'neutral' => 'Neutro',
        'chaotic_neutral' => 'Caótico e Neutro',
        'lawful_evil' => 'Leal e Mau',
        'neutral_evil' => 'Neutro e Mau',
        'chaotic_evil' => 'Caótico e Mau',
    ];

    $size = $sizeLabels[$header->size] ?? $header->size;

    $types = collect($header->types)
        ->map(fn($type) => $typeLabels[$type] ?? ucfirst($type))
        ->implode(', ');

    $alignments = collect($header->alignments)
        ->map(fn($alignment) => $alignmentLabels[$alignment] ?? $alignment)
        ->implode(', ');
@endphp

<div class="mb-1">
    <h1 class="text-xl font-bold tracking-wide">
        {{ $header->name }}
    </h1>

    <div class="subtitle text-xs mt-0.5 mb-1">
        {{ $size }}
        @if($types)
            {{ $types }},
        @endif
        @if($alignments)
            {{ $alignments }}
        @endif
    </div>
</div>