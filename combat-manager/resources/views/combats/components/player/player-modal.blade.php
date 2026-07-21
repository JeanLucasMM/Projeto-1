<div
    x-show="openPlayerModal"
    x-cloak
    x-data="{
        loading: false,
        async submitPlayerForm(e) {
            this.loading = true;
            const form = e.target;
            const wrapper = document.getElementById('combat-panels-wrapper');

            try {
                // Envia os dados do novo jogador via AJAX
                await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                // Busca o HTML atualizado da página
                const res = await fetch(window.location.href, { cache: 'no-store' });
                const html = await res.text();
                const newContent = new DOMParser().parseFromString(html, 'text/html').getElementById('combat-panels-wrapper');

                // Atualiza o container dos painéis (fazendo o card aparecer)
                if (newContent && wrapper) {
                    wrapper.innerHTML = newContent.innerHTML;
                    if (window.Alpine) Alpine.initTree(wrapper);
                }

                // Limpa o formulário e fecha o modal com sucesso
                form.reset();
                openPlayerModal = false;
            } catch (err) {
                console.error('Erro ao adicionar jogador:', err);
            } finally {
                this.loading = false;
            }
        }
    }"
    class="fixed inset-0 z-50 flex items-center justify-center bg-[#1a1a1a]/80 p-4 transition-opacity"
    :class="loading ? 'opacity-75 pointer-events-none' : ''"
>

    <div
        @click.outside="!loading && (openPlayerModal = false)"
        class="bg-[#f4f1e8] border border-[#cdbb9f]/50 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
    >

        {{-- Cabeçalho Temático --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-[#cdbb9f]/30 bg-[#e8e4d9]/40">
            <h2 class="text-xl font-serif font-bold text-[#6b1d14]">
                Adicionar Jogador
            </h2>
            <button
                type="button"
                @click="openPlayerModal = false"
                :disabled="loading"
                class="text-[#8c6239] hover:text-[#6b1d14] transition-colors text-2xl disabled:opacity-30"
            >
                ×
            </button>
        </div>

        <form method="POST" action="{{ route('combats.players.store', $combat) }}" @submit.prevent="submitPlayerForm($event)">
            @csrf

            <div class="p-6 space-y-5">
                {{-- Campo Nome --}}
                <div>
                    <label class="block text-[10px] font-bold text-[#8c6239] uppercase tracking-wider mb-2">
                        Nome do Jogador
                    </label>
                    <input
                        type="text"
                        name="name"
                        required
                        class="w-full bg-[#e8e4d9]/50 border border-[#cdbb9f]/50 rounded-lg focus:ring-2 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all p-2.5 font-medium text-[#6b1d14]"
                    >
                </div>

                {{-- Campo Iniciativa --}}
                <div>
                    <label class="block text-[10px] font-bold text-[#8c6239] uppercase tracking-wider mb-2">
                        Iniciativa
                    </label>
                    <input
                        type="number"
                        name="initiative"
                        value="0"
                        class="w-full bg-[#e8e4d9]/50 border border-[#cdbb9f]/50 rounded-lg focus:ring-2 focus:ring-[#6b1d14] focus:border-[#6b1d14] transition-all p-2.5 font-medium text-[#6b1d14]"
                    >
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-[#cdbb9f]/30 bg-[#e8e4d9]/20">
                <button
                    type="button"
                    @click="openPlayerModal = false"
                    :disabled="loading"
                    class="px-4 py-2 text-[#8c6239] font-serif font-bold text-sm hover:text-[#6b1d14] transition-colors disabled:opacity-50"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-sm shadow-sm transition-all uppercase tracking-wider flex items-center gap-2"
                >
                    <span x-show="loading" class="animate-spin inline-block w-3 h-3 border-2 border-t-transparent border-white rounded-full"></span>
                    <span x-text="loading ? 'Salvando...' : 'Adicionar'"></span>
                </button>
            </div>
        </form>
    </div>
</div>