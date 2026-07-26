<div class="header">
    <h1 class="header-title">
        {{ $npc->header?->name ?? 'Sem Nome' }}
    </h1>

    <div class="subtitle">
        {{ $npc->header?->size }} {{ $npc->header?->type }}@if(!empty($npc->header?->alignment)), {{ $npc->header->alignment }}@endif
    </div>

    <div class="header-details">
        <div class="header-left">
            @if(!empty($npc->header?->armorClass))
                <p>
                    <strong>Classe de Armadura:</strong> {{ $npc->header->armorClass }}
                </p>
            @endif

            @if(!empty($npc->header?->hitPoints))
                <p>
                    <strong>Pontos de Vida:</strong> {{ $npc->header->hitPoints }}@if(!empty($npc->header?->hitDice)) ({{ $npc->header->hitDice }})@endif
                </p>
            @endif

            @if(!empty($npc->combat?->speed))
                <p>
                    <strong>Deslocamento:</strong> {{ $npc->combat->speed }}
                </p>
            @endif
        </div>

        <div class="header-right">
            @if(isset($npc->header?->initiative) && $npc->header?->initiative !== '')
                @php
                    $init = $npc->header->initiative;
                    $formattedInit = is_numeric($init) ? ($init >= 0 ? '' . $init : $init) : $init;
                @endphp
                <p>
                    <strong>Iniciativa:</strong> {{ $formattedInit }}
                </p>
            @endif
        </div>
    </div>
</div>