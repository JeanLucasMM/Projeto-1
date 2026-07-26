<x-app-layout>

<div class="max-w-7xl mx-auto px-6 py-8">

    <a href="{{ route('npcs.index') }}" class="inline-flex items-center gap-2 mb-6 text-sm text-[#6b1d14] hover:underline">
        ← Voltar
    </a>

    <div class="rounded-2xl p-6 mb-8 border" style="background-color: {{ $folder->color }}20; border-color: {{ $folder->color }}80;">
        <h1 class="text-3xl font-serif font-bold text-[#6b1d14]">
            {{ $folder->name }}
        </h1>
        @if($folder->subtitle)
            <p class="italic text-[#8c6239] mt-1">
                {{ $folder->subtitle }}
            </p>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-start">
        @forelse($folder->npcs as $npc)
            
            {{-- Como a lógica do botão foi para dentro do componente, basta chamá-lo --}}
            @include('npcs.npc-card', ['npc' => $npc])

        @empty
            <div class="col-span-full rounded-xl border border-dashed border-[#cdbb9f]/60 p-12 text-center bg-[#f4f1e8]/50">
                <h2 class="font-serif text-xl text-[#8c6239]">
                    Nenhum NPC nesta pasta.
                </h2>
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