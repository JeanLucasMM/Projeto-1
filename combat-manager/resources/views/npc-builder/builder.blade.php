{{-- COLUNA DA ESQUERDA: FORMULÁRIO (builder.blade.php) --}}
<div class="w-full lg:w-[100%] h-full overflow-y-auto custom-scrollbar pr-3 space-y-4 font-sans pb-24">

    {{-- Inicial --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('initial') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('initial')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('initial') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Inicial
            </span>
            <svg :class="{'rotate-180': openSections.includes('initial')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('initial')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.initial')
            </div>
        </div>
    </div>

    {{-- Testes de Resistência --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('savingThrows') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('savingThrows')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('savingThrows') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Testes de Resistência
            </span>
            <svg :class="{'rotate-180': openSections.includes('savingThrows')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('savingThrows')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.saving-throws')
            </div>
        </div>
    </div>

    {{-- Movimento --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('speed') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('speed')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('speed') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Movimento
            </span>
            <svg :class="{'rotate-180': openSections.includes('speed')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('speed')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.speed')
            </div>
        </div>
    </div>

    {{-- Perícias --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('skills') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('skills')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('skills') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Perícias
            </span>
            <svg :class="{ 'rotate-180': openSections.includes('skills') }" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('skills')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.skills')
            </div>
        </div>
    </div>

    {{-- Sentidos --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('senses') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('senses')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('senses') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Sentidos
            </span>
            <svg :class="{ 'rotate-180': openSections.includes('senses') }" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('senses')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.senses')
            </div>
        </div>
    </div>

    {{-- Combat / Resistências e Imunidades --}}
    <div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300"
         :class="openSections.includes('combat') ? 'overflow-visible' : 'overflow-hidden'">
        <button type="button" @click="toggleSection('combat')"
            class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b border-transparent"
            :class="openSections.includes('combat') ? 'bg-[#ece6d7] border-[#cdbb9f]/50' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2]'">
            <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
                Resistências e Imunidades
            </span>
            <svg :class="{'rotate-180': openSections.includes('combat')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="openSections.includes('combat')" x-collapse>
            <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
                @include('npc-builder.tabs.combat')
            </div>
        </div>
    </div>

{{-- Habilidades (Features) --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('features')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('features') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Habilidades
        </span>
        <svg :class="{'rotate-180': openSections.includes('features')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('features')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addFeature()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Nova Habilidade
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.habilidades')
        </div>
    </div>
</div>

{{-- Ações e Conjuração --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('acoes')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('acoes') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Ações
        </span>
        <svg :class="{'rotate-180': openSections.includes('acoes')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('acoes')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addAction()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Nova Ação
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.action')
        </div>
    </div>
</div>

{{-- Ações Bônus --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('bonusActions')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('bonusActions') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Ações Bônus
        </span>
        <svg :class="{'rotate-180': openSections.includes('bonusActions')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('bonusActions')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addBonusAction()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Ação Bônus
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.bonus-action')
        </div>
    </div>
</div>

{{-- Ataques Estruturados --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('actions')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('actions') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Ataques
        </span>
        <svg :class="{'rotate-180': openSections.includes('actions')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('actions')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addAttack()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Novo Ataque
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.attacks')
        </div>
    </div>
</div>

{{-- Multiataque --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('multiattack')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('multiattack') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Multiataque
        </span>
        <svg :class="{'rotate-180': openSections.includes('multiattack')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('multiattack')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada (Corrigido para addMultiAttack) --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addMultiAttack()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Multiataque
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.multiattack')
        </div>
    </div>
</div>

{{-- Reações --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('reaction')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('reaction') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Reações
        </span>
        <svg :class="{'rotate-180': openSections.includes('reaction')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('reaction')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addReaction()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Reação
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.reaction')
        </div>
    </div>
</div>

{{-- Ações Lendárias --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('legendaryActions')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('legendaryActions') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Ações Lendárias
        </span>
        <svg :class="{'rotate-180': openSections.includes('legendaryActions')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('legendaryActions')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addLegendaryAction()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Ação Lendária
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.acaoLendaria')
        </div>
    </div>
</div>

{{-- Ações de Covil --}}
<div class="bg-white/90 backdrop-blur-md border border-[#cdbb9f] rounded-xl shadow-sm transition-all duration-300 overflow-hidden">
    {{-- Cabeçalho do Acordeão --}}
    <button type="button" @click="toggleSection('lairActions')"
        class="w-full px-5 py-3.5 flex items-center justify-between transition-colors cursor-pointer border-b"
        :class="openSections.includes('lairActions') ? 'bg-[#ece6d7] border-[#cdbb9f]' : 'bg-gradient-to-r from-[#f4f1e8] to-[#fbf9f4] hover:bg-[#eae3d2] border-transparent'">
        <span class="text-[#6b1d14] font-black uppercase tracking-widest text-[11px] flex items-center gap-2.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#8a2519] shadow-sm"></span>
            Ações de Covil
        </span>
        <svg :class="{'rotate-180': openSections.includes('lairActions')}" class="w-4 h-4 text-[#8c6239] transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Corpo do Acordeão --}}
    <div x-show="openSections.includes('lairActions')" x-collapse>
        {{-- Botão Adicionar com Topo Reto e Base Arredondada --}}
        <div class="w-full pb-1 bg-white">
            <button
                type="button"
                @click="addLairAction()"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-[#6b1d14] to-[#8a2519] text-[#f4f1e8] text-[10px] sm:text-xs font-black uppercase tracking-widest hover:from-[#53150f] hover:to-[#6b1d14] transition-all cursor-pointer flex items-center justify-center gap-2 outline-none rounded-b-xl rounded-t-none shadow-sm border-x border-b border-[#cdbb9f]/40"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Ação de Covil
            </button>
        </div>

        {{-- Conteúdo da Aba --}}
        <div class="p-5 sm:p-6 bg-[#fbf9f4]/50">
            @include('npc-builder.tabs.acaoCovil')
        </div>
    </div>
</div>

</div>