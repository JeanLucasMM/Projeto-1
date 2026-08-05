<x-app-layout>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Botão Voltar --}}
    <a href="{{ route('npcs.index') }}" class="inline-flex items-center gap-2 mb-6 text-xs font-serif font-bold uppercase tracking-wider text-[#6b1d14] hover:text-[#53150f] bg-white/60 hover:bg-white border border-[#cdbb9f]/40 px-3.5 py-2 rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
        </svg>
        Voltar
    </a>

    {{-- Cabeçalho da Pasta (Banner Temático) --}}
    <div class="relative rounded-3xl p-8 mb-10 border shadow-md overflow-hidden backdrop-blur-md" style="background-color: {{ $folder->color }}15; border-color: {{ $folder->color }}60;">
        {{-- Detalhe decorativo de fundo --}}
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full opacity-10 pointer-events-none" style="background-color: {{ $folder->color }};"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                </div>
                <h1 class="text-3xl md:text-4xl font-serif font-black text-[#53150f] tracking-tight drop-shadow-sm">
                    {{ $folder->name }}
                </h1>
                @if($folder->subtitle)
                    <p class="italic text-sm text-[#8c6239] font-medium mt-2 max-w-2xl leading-relaxed">
                        {{ $folder->subtitle }}
                    </p>
                @endif
            </div>

            {{-- Contador de NPCs na Pasta --}}
            <div class="self-start md:self-center bg-white/70 border border-[#cdbb9f]/50 px-5 py-3 rounded-2xl shadow-sm text-center backdrop-blur-sm">
                <span class="block text-[10px] font-serif font-bold text-[#8c6239] uppercase tracking-wider">Membros</span>
                <span class="text-xl font-mono font-bold text-[#6b1d14]">{{ $folder->npcs->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Grid de Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-start">
        @forelse($folder->npcs as $npc)
            @include('npcs.npc-card', ['npc' => $npc])
        @empty
            <div class="col-span-full rounded-2xl border-2 border-dashed border-[#cdbb9f]/60 p-16 text-center bg-gradient-to-b from-[#f4f1e8]/40 to-[#f4f1e8]/80 shadow-inner">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#cdbb9f]/20 border border-[#cdbb9f]/40 flex items-center justify-center text-[#8c6239]">
                    <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h2 class="font-serif text-xl font-bold text-[#6b1d14]">
                    Nenhum NPC nesta pasta.
                </h2>
                <p class="text-xs text-[#8c6239] mt-1 max-w-sm mx-auto">
                    Arraste cards para cá ou adicione novos registros para preencher este capítulo do seu grimório.
                </p>
            </div>
        @endforelse
    </div>

</div>

<script>
document.addEventListener('click', function(event) {
    const button = event.target.closest('.npc-menu-btn');
    const allMenus = document.querySelectorAll('.npc-dropdown-menu');

    if (button) {
        event.preventDefault();
        event.stopPropagation();
        
        const targetId = button.getAttribute('data-target');
        const targetMenu = document.getElementById(targetId);

        allMenus.forEach(menu => {
            if (menu.id !== targetId) {
                menu.classList.add('hidden');
            }
        });

        if (targetMenu) {
            targetMenu.classList.toggle('hidden');
        }
    } else {
        allMenus.forEach(menu => menu.classList.add('hidden'));
    }
});
</script>

</x-app-layout>