

<div class="native-statblock">
    <div class="native-content-flow">

        {{-- ====================================================================== --}}
        {{-- Cabeçalho --}}
        {{-- ====================================================================== --}}

        <x-statblock.native.header
            :header="$npc->header"
        />

        {{-- Linha 1: Logo abaixo do cabeçalho --}}
        <hr class="native-header-divider">

        {{-- ====================================================================== --}}
        {{-- Coluna esquerda (Vida, Movimento, Salto) --}}
        {{-- ====================================================================== --}}

        <x-statblock.native.inicial
            :combat="$npc->combat"
            :abilities="$npc->abilities"
            :speed="$npc->speed"
        />

        {{-- Linha 2: Abaixo do bloco inicial, isolando para os Atributos --}}
        <hr class="native-header-divider">

        <x-statblock.native.abilities
            :abilities="$npc->abilities"
            :savingThrows="$npc->savingThrows"
            :combat="$npc->combat"
        />

        <x-statblock.native.combat
            :combat="$npc->combat"
            :abilities="$npc->abilities"
            :skills="$npc->skills"
            :header="$npc->header"
        />

        {{-- ====================================================================== --}}
        {{-- Habilidades --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->features))

            <div class="native-section-title">
                Habilidades
            </div>

            @foreach($npc->features as $feature)

                <x-statblock.native.entry
                    :entry="$feature"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Ações --}}
        {{-- ====================================================================== --}}

        @if(
            !empty($npc->multiAttacks)
            ||
            !empty($npc->attacks)
            ||
            !empty($npc->actions)
        )

            <div class="native-section-title">
                Ações
            </div>

            {{-- Multiataques --}}
            @foreach($npc->multiAttacks as $multiAttack)

                <x-statblock.native.multi-attack
                    :multiAttack="$multiAttack"
                    :attacks="$npc->attacks"
                    :actions="$npc->actions"
                    :header="$npc->header"
                />

            @endforeach

            {{-- Ataques --}}
            @foreach($npc->attacks as $attack)

                <x-statblock.native.attack
                    :attack="$attack"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                    :header="$npc->header"
                />

            @endforeach

            {{-- Ações normais --}}
            @foreach($npc->actions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Ações Bônus --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->bonusActions))

            <div class="native-section-title">
                Ações Bônus
            </div>

            @foreach($npc->bonusActions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Reações --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->reactions))

            <div class="native-section-title">
                Reações
            </div>

            @foreach($npc->reactions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Ações Lendárias --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->legendaryActions))

            <div class="native-section-title">
                Ações Lendárias
            </div>

            @foreach($npc->legendaryActions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                    :section="'legendary'"
                    :isFirst="$loop->first"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Ações de Covil --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->lairActions))

            <div class="native-section-title">
                Ações de Covil
            </div>

            @foreach($npc->lairActions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                    :section="'lair'"
                    :isFirst="$loop->first"
                />

            @endforeach

        @endif


        {{-- ====================================================================== --}}
        {{-- Ações Míticas --}}
        {{-- ====================================================================== --}}

        @if(!empty($npc->mythicActions))

            <div class="native-section-title">
                Ações Míticas
            </div>

            @foreach($npc->mythicActions as $action)

                <x-statblock.native.entry
                    :entry="$action"
                    :header="$npc->header"
                    :abilities="$npc->abilities"
                    :combat="$npc->combat"
                />

            @endforeach

        @endif

    </div>
</div>