<div>

    <h1>
        {{ $npc->header->name }}
    </h1>

    <div class="subtitle">
        {{ $npc->header->size }}
        {{ $npc->header->type }}

        @if($npc->header->alignment)
            ,
            {{ $npc->header->alignment }}
        @endif
    </div>

    <div class="header-details">

        <div class="header-left">

            <p>
                <strong>Armadura:</strong>
                {{ $npc->header->armorClass }}
            </p>

            <p>
                <strong>Pontos de Vida:</strong>

                {{ $npc->header->hitPoints }}

                 @if($npc->header->hitDice)
                    ({{ $npc->header->hitDice }})
                 @endif
</p>

            <p>
                <strong>Velocidade:</strong>
                {{ $npc->combat->speed }}
            </p>

        </div>

        <div class="header-right">

@if($npc->header->initiative)

        <p>
    <strong>Iniciativa:</strong>

    {{ $npc->header->initiative }}
        </p>

        @endif

        </div>

    </div>

</div>