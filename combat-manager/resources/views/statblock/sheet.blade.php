<div class="statblock">

    <div class="content-flow">

        <x-statblock.header
            :npc="$npc"
        />

        <x-statblock.abilities
            :npc="$npc"
        />

        <x-statblock.details
            :npc="$npc"
        />

        <hr class="stat-divider">

        @foreach($npc->sections as $section)

            <x-statblock.section
                :section="$section"
            />

        @endforeach

    </div>

</div>