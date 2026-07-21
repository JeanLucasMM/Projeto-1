<div class="bg-white/90 rounded-xl border border-[#cdbb9f]/40 shadow-sm transition-all">

    {{-- Cabeçalho do Painel --}}
    <div class="flex justify-between items-center px-6 py-4 border-b border-[#cdbb9f]/20 bg-[#efe9dc]/30">
        <h2 class="text-lg font-serif font-bold text-[#6b1d14] tracking-wide">
            Gerenciar Jogadores
        </h2>

        {{-- Botão que aciona o modal do Alpine --}}
        <button
            @click="openPlayerModal = true"
            class="bg-[#8c6239] hover:bg-[#724b24] text-[#f4f1e8] px-4 py-2 rounded-lg font-serif font-bold text-xs uppercase tracking-wider transition shadow-sm">
            + Jogador
        </button>
    </div>

    {{-- Área de Conteúdo dos Cards --}}
    <div class="p-6">
        @if($combatPlayers->isEmpty())
            {{-- Estado Vazio Temático --}}
            <div class="rounded-xl border border-dashed border-[#cdbb9f]/60 bg-[#faf8f2]/50 py-12 px-4 text-center">
                <div class="text-2xl mb-2">👥</div>
                <h3 class="text-sm font-serif font-bold text-[#6b1d14]">Nenhum herói na mesa</h3>
                <p class="text-[11px] text-[#8c6239] mt-1">Clique no botão acima para registrar os integrantes do grupo neste encontro.</p>
            </div>
        @else
            {{-- Grid Dinâmico (Muda de colunas dependendo do tamanho da tela) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($combatPlayers as $player)
                    @include('combats.components.player.player-card', [
                        'player' => $player,
                        'participant' => $player,
                        'iteration' => $loop->iteration,
                        'position' => $loop->iteration,
                    ])
                @endforeach
            </div>
        @endif
    </div>

</div>