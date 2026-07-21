<div class="details">

    


    @if($npc->combat->skills !== '-')

        <div class="detail-row">

            <strong>Perícias:</strong>

            {{ $npc->combat->skills }}

        </div>

    @endif





    @if($npc->combat->resistances !== '-')

        <div class="detail-row">

            <strong>Resistências:</strong>

            {{ $npc->combat->resistances }}

        </div>

    @endif
    
    



    @if($npc->combat->immunities !== '-')

        <div class="detail-row">

            <strong>Imunidades:</strong>

            {{ $npc->combat->immunities }}

        </div>

    @endif


    @if($npc->combat->conditionImmunities !== '-')

        <div class="detail-row">

            <strong>Condições Imunidades:</strong>

            {{ $npc->combat->conditionImmunities }}

        </div>

    @endif


    @if($npc->combat->vulnerabilities !== '-')

        <div class="detail-row">

            <strong>Vulnerabilidades:</strong>

            {{ $npc->combat->vulnerabilities }}

        </div>

    @endif


    @if($npc->combat->senses !== '-')

        <div class="detail-row">

            <strong>Sentidos:</strong>

            {{ $npc->combat->senses }}

        </div>

    @endif


    @if($npc->combat->languages !== '-')

        <div class="detail-row">

            <strong>Idiomas:</strong>

            {{ $npc->combat->languages }}

        </div>

    @endif


    <div class="detail-row">

        <strong>Nível Dificuldade:</strong>

     {{ $npc->header->challengeRating }}

    (PB +{{ $npc->header->proficiencyBonus }})
</p>

    </div>

</div>