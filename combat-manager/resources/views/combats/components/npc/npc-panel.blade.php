@php
    $currentTurnNpcId = null;

    if ($combat->is_active && isset($initiative) && isset($initiative[$combat->current_turn])) {
        $currentParticipant = $initiative[$combat->current_turn];
        $isAnArray = is_array($currentParticipant);
        
        $isNpc = $isAnArray
            ? (($currentParticipant['type'] ?? '') === 'npc')
            : $currentParticipant->isNpc();

        if ($isNpc) {
            $currentTurnNpcId = $isAnArray ? $currentParticipant['id'] : $currentParticipant->id;
        }
    }

    // Se não houver turnos ou NPCs, define como a string 'null' para o Javascript não quebrar
    $initialNpcId = $currentTurnNpcId ?? $combatNpcs->first()?->id ?? 'null';
@endphp

<div
    x-data="{
        selectedNpc: sessionStorage.getItem('combat_{{ $combat->id }}_selected')
            ? parseInt(sessionStorage.getItem('combat_{{ $combat->id }}_selected'))
            : {{ $initialNpcId }}
    }"
    x-init="
        const currentTurn = '{{ $combat->current_turn ?? 0 }}';
        const savedTurn = sessionStorage.getItem('combat_{{ $combat->id }}_turn');

        if (savedTurn !== currentTurn) {
            sessionStorage.setItem('combat_{{ $combat->id }}_turn', currentTurn);
            selectedNpc = {{ $initialNpcId }};
            sessionStorage.setItem('combat_{{ $combat->id }}_selected', selectedNpc);
        }

        $watch('selectedNpc', value => {
            if (value) {
                sessionStorage.setItem('combat_{{ $combat->id }}_selected', value);
            }
        });
    "
    class="w-full relative"
>
    {{-- Container com Grid para evitar o salto de layout (layout shift) --}}
    <div class="relative z-10 grid grid-cols-1 grid-rows-1 items-start">
        @forelse($combatNpcs as $combatNpc)
            <div
                x-show="selectedNpc == {{ $combatNpc->id }}"
                x-transition.opacity.duration.150ms
                x-cloak
                class="col-start-1 row-start-1 w-full"
            >
                @include('combats.components.npc.npc-card', [
                    'combatNpc' => $combatNpc,
                    'participant' => $combatNpc,
                    'iteration' => $loop->iteration,
                    'position' => $loop->iteration,
                ])
            </div>
        @empty
            <div class="col-start-1 row-start-1 w-full rounded-2xl border border-dashed border-[#cdbb9f]/60 bg-[#faf8f2]/50 py-16 px-4 text-center max-w-xl mx-auto mt-4">
                <div class="text-3xl mb-2">👁️‍🗨️</div>
                <h3 class="text-base font-serif font-bold text-[#6b1d14]">Nenhum NPC Ativo</h3>
                <p class="text-xs text-[#8c6239]/80 mt-1 max-w-sm mx-auto">
                    Adicione monstros ao grimório ou clique em um NPC na lista de iniciativa lateral para visualizar os blocos de estatísticas e ações aqui.
                </p>
            </div>
        @endforelse
    </div>
</div>