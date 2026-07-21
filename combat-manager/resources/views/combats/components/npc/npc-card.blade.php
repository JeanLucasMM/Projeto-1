@once
    @push('styles')
        <style>

@keyframes damage-hit{
    0%{
        transform:translate3d(0,0,0) scale(1);
        filter:brightness(1);
    }
    8%{
        transform:translateX(-7px) rotate(-1deg) scale(.992);
    }
    18%{
        transform:translateX(6px) rotate(.8deg);
    }
    30%{
        transform:translateX(-5px);
    }
    42%{
        transform:translateX(4px);
    }
    56%{
        transform:translateX(-2px);
    }
    70%{
        transform:translateX(1px);
    }
    100%{
        transform:translate3d(0,0,0) scale(1);
        filter:brightness(1);
    }
}

/* DANO FLASH */
@keyframes damageFlash{
    0%{
        box-shadow:
            inset 0 0 0 rgba(185,28,28,0),
            0 0 0 rgba(220,38,38,0);
    }
    25%{
        box-shadow:
            inset 0 0 70px rgba(185,28,28,.55),
            0 0 20px rgba(220,38,38,.20);
    }
    60%{
        box-shadow:
            inset 0 0 25px rgba(185,28,28,.25),
            0 0 8px rgba(220,38,38,.08);
    }
    100%{
        box-shadow:
            inset 0 0 0 rgba(185,28,28,0),
            0 0 0 rgba(220,38,38,0);
    }
}

/* CURA */
@keyframes heal{
    0%{
        transform:scale(1);
        filter:brightness(1);
    }
    18%{
        transform:scale(1.018);
    }
    45%{
        transform:scale(1.032);
        filter:brightness(1.25);
    }
    75%{
        transform:scale(1.01);
        filter:brightness(1.08);
    }
    100%{
        transform:scale(1);
        filter:brightness(1);
    }
}

@keyframes healFlash{
    0%{
        box-shadow:
            inset 0 0 0 rgba(34,197,94,0),
            0 0 0 rgba(34,197,94,0);
    }
    30%{
        box-shadow:
            inset 0 0 45px rgba(34,197,94,.22),
            0 0 26px rgba(34,197,94,.45);
    }
    70%{
        box-shadow:
            inset 0 0 18px rgba(34,197,94,.10),
            0 0 8px rgba(34,197,94,.12);
    }
    100%{
        box-shadow:
            inset 0 0 0 rgba(34,197,94,0),
            0 0 0 rgba(34,197,94,0);
    }
}

/* ESCUDO */
@keyframes shieldAura{
    0%{
        box-shadow: 0 0 4px rgba(59,130,246,.12);
    }
    50%{
        box-shadow:
            0 0 12px rgba(56,189,248,.45),
            0 0 28px rgba(96,165,250,.18);
    }
    100%{
        box-shadow: 0 0 4px rgba(59,130,246,.12);
    }
}

@keyframes shield-hit{
    0%{ transform:scale(1); }
    30%{ transform:scale(.986); }
    60%{ transform:scale(1.008); }
    100%{ transform:scale(1); }
}

/* BLOODIED (50%) */
@keyframes bloodiedPulse{
    0%,100%{
        filter:saturate(1);
        box-shadow: 0 0 0 rgba(127,29,29,.10);
    }
    50%{
        filter:saturate(1.08);
        box-shadow:
            0 0 18px rgba(127,29,29,.30),
            inset 0 0 18px rgba(127,29,29,.18);
    }
}

/* CRITICAL (<25%) */
@keyframes criticalPulse{
    0%,100%{
        transform:scale(1);
        box-shadow: 0 0 0 rgba(220,38,38,.20);
        filter:brightness(1);
    }
    50%{
        transform:scale(1.01);
        box-shadow:
            0 0 26px rgba(220,38,38,.55),
            inset 0 0 22px rgba(220,38,38,.28);
        filter:brightness(1.05);
    }
}

/* CLASSES */
.animate-damage-red{
    animation:
        damage-hit .55s cubic-bezier(.18,.89,.32,1.2),
        damageFlash .55s ease-out;
}

.animate-damage-blue{
    animation:
        shield-hit .45s ease-out,
        damageFlash .45s ease-out,
        shieldAura .6s ease-out;
}

.animate-heal-green{
    animation:
        heal .65s cubic-bezier(.2,.9,.2,1),
        healFlash .75s ease-out;
}

.animate-photo-red-internal{
    animation: damageFlash .55s ease-out;
}

.temp-hp-active{
    animation: shieldAura 3s ease-in-out infinite;
}

.bloodied-card{
    animation: bloodiedPulse 2.1s ease-in-out infinite;
}

.critical-card{
    animation: criticalPulse 2.5s ease-in-out infinite;
}
        </style>
    @endpush
@endonce

<div
    x-data="{
        editingHp: false,
        currentHp: {{ $combatNpc->current_hp }},
        ghostHp: {{ $combatNpc->current_hp }},
        maxHp: {{ $combatNpc->max_hp }},
        directHp: {{ $combatNpc->current_hp }},
        
        editingTempHp: false,
        directTempHp: {{ $combatNpc->temporary_hp }},
        
        shakingRed: false,
        shakingBlue: false,
        flashingGreen: false,

        get hpPercent() {
            return this.maxHp > 0 ? Math.max(0, Math.min(100, (this.currentHp / this.maxHp) * 100)) : 0;
        },
        get ghostHpPercent() {
            return this.maxHp > 0 ? Math.max(0, Math.min(100, (this.ghostHp / this.maxHp) * 100)) : 0;
        },
        get isBloodied() {
            return this.currentHp > 0 && this.hpPercent <= 50 && this.hpPercent > 5;
        },
        get isCritical() {
            return this.currentHp > 0 && this.hpPercent <= 5;
        },

        async triggerAction(url, fieldName, value) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append(fieldName, value);

            if (fieldName === 'damage') {
                if (this.directTempHp > 0) {
                    this.shakingBlue = true;
                    setTimeout(() => this.shakingBlue = false, 700);
                    
                    let damageRemaining = value - this.directTempHp;
                    this.directTempHp = Math.max(0, this.directTempHp - value);
                    if (damageRemaining > 0) {
                        this.currentHp = Math.max(0, this.currentHp - damageRemaining);
                    }
                } else {
                    this.shakingRed = true;
                    setTimeout(() => this.shakingRed = false, 700);
                    
                    this.currentHp = Math.max(0, this.currentHp - value);
                }

                setTimeout(() => this.ghostHp = this.currentHp, 350);

            } else if (fieldName === 'heal') {
                this.flashingGreen = true;
                setTimeout(() => this.flashingGreen = false, 800);

                this.currentHp = Math.min(this.maxHp, this.currentHp + value);
                this.ghostHp = this.currentHp;
            }

            await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            await this.refreshGrid();
        },

        async handleDirectHp(damageUrl, healUrl) {
            if (!this.editingHp) return;
            this.editingHp = false;

            const targetHp = parseInt(this.directHp);
            if (isNaN(targetHp) || targetHp === parseInt(this.currentHp)) return;

            let diff = targetHp - this.currentHp;

            if (diff < 0) {
                await this.triggerAction(damageUrl, 'damage', Math.abs(diff));
            } else {
                await this.triggerAction(healUrl, 'heal', diff);
            }
        },

        async handleTemporaryHp(url) {
            if (!this.editingTempHp) return;
            this.editingTempHp = false;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('temporary_hp', this.directTempHp);

            await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            await this.refreshGrid();
        },

        async refreshGrid() {
            const wrapper = document.getElementById('combat-panels-wrapper');
            const res = await fetch(window.location.href);
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nextWrapper = doc.getElementById('combat-panels-wrapper');

            if (wrapper && nextWrapper) {
                wrapper.innerHTML = nextWrapper.innerHTML;
                if (window.Alpine) {
                    Alpine.initTree(wrapper);
                }
            }
        }
    }"
    :class="{ 
        'bloodied-card': isBloodied, 
        'critical-card': isCritical 
    }"
    class="bg-[#faf8f2] border border-[#cdbb9f]/60 rounded-2xl shadow-md overflow-hidden relative transition-all duration-300"
>
    @php
        $challengeRating = $combatNpc->viewModel->header->challengeRating ?? $combatNpc->viewModel->header->cr ?? $combatNpc->viewModel->header->nd ?? null;
        $hasTempHp = $combatNpc->temporary_hp > 0;
        $hpBorderClass = $hasTempHp ? 'border border-sky-400/60 ring-1 ring-sky-400/30 shadow-[0_0_8px_rgba(56,189,248,0.25)]' : 'border border-black/40';
    @endphp

    {{-- CORPO PRINCIPAL DO CARD --}}
    <div class="p-4 flex flex-col md:flex-row gap-5 items-center">
        
        {{-- COLUNA ESQUERDA: AVATAR E CA --}}
        <div class="flex flex-col items-center gap-2 shrink-0 w-full md:w-32 text-center select-none rounded-2xl transition-all duration-300">
            {{-- Moldura da Imagem --}}
            <div 
                :class="{ 'animate-photo-red-internal': shakingRed, 'animate-heal-green': flashingGreen }"
                class="relative w-32 h-32 rounded-2xl bg-[#d9c7a7]/30 border border-[#cdbb9f]/40 p-1 shadow-inner group transition-shadow duration-300"
            >
                {{-- Container Interno para Recorte Seguro da Foto --}}
                <div 
                    :class="{ 
                        'animate-damage-red': shakingRed, 
                        'animate-damage-blue': shakingBlue 
                    }"
                    class="w-full h-full rounded-xl overflow-hidden relative z-0"
                >
                    @if($combatNpc->npc && $combatNpc->npc->image_path)
                        <img 
                            src="{{ asset('storage/'.$combatNpc->npc->image_path) }}" 
                            :class="{ 'grayscale opacity-60 contrast-125': currentHp === 0 }"
                            class="w-full h-full object-cover object-top transition-all duration-500"
                        >
                    @else
                        <div 
                            :class="{ 'grayscale opacity-60': currentHp === 0 }"
                            class="w-full h-full flex items-center justify-center bg-[#efe9dc] text-[#6b1d14] font-serif font-bold text-2xl transition-all duration-500"
                        >
                            {{ strtoupper(substr($combatNpc->viewModel->header->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                {{-- Badge da CA --}}
                <div class="absolute -top-2 -right-2 bg-white border border-[#cdbb9f] rounded-xl py-1 px-2 shadow-md text-center min-w-[42px] z-20">
                    <span class="block text-[8px] font-extrabold text-stone-400 uppercase tracking-wider leading-none">CA</span>
                    <span class="block text-base font-serif font-bold text-[#6b1d14] leading-none mt-0.5">
                        {{ $combatNpc->viewModel->header->armorClass ?? 10 }}
                    </span>
                </div>
            </div>

            {{-- Tamanho e Tipo --}}
            <div class="text-[9px] font-extrabold tracking-widest text-[#8c6239]/80 uppercase mt-0.5 flex flex-col items-center gap-0.5 w-full">
                <span>{{ ucfirst($combatNpc->viewModel->header->size) }}</span>
                <span>{{ ucfirst($combatNpc->viewModel->header->type) }}</span>
            </div>
        </div>

        {{-- COLUNA DIREITA: INFORMAÇÕES DE COMBATE --}}
        <div class="flex-1 w-full flex flex-col gap-3">
            
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-serif text-2xl font-bold text-[#6b1d14] tracking-wide leading-tight">
                    {{ $combatNpc->viewModel->header->name }}
                </h2>
            </div>

            {{-- SEÇÃO DE STATUS --}}
            <div class="flex flex-col gap-2.5 w-full mt-1">
                
                {{-- BARRA DE HP --}}
                <div 
                    :class="{ 
                        'animate-damage-red': shakingRed, 
                        'animate-damage-blue': shakingBlue, 
                        'animate-heal-green': flashingGreen
                    }"
                    class="w-full relative h-8 rounded-md overflow-hidden bg-[#1f1b1b] {{ $hpBorderClass }} shadow-inner flex items-center justify-between transition-all duration-300"
                >
                    {{-- 1. Barra Fantasma --}}
                    <div 
                        class="absolute inset-y-0 left-0 bg-red-950/80 transition-all duration-1000 ease-out" 
                        :style="`width: ${ghostHpPercent}%`"
                    ></div>

                    {{-- 2. Preenchimento de Vida Real --}}
                    <div 
                        class="absolute inset-y-0 left-0 transition-all duration-700 ease-out z-0"
                        :class="hpPercent > 50 ? 'bg-emerald-700' : (hpPercent > 25 ? 'bg-amber-600' : 'bg-red-700')"
                        :style="`width: ${hpPercent}%`"
                    ></div>

                    {{-- Escudo / Vida Temporária --}}
                    @if($hasTempHp)
                        <div class="absolute inset-0 bg-gradient-to-r from-sky-500/25 via-sky-400/15 to-sky-500/25 animate-pulse pointer-events-none z-10 border-t border-b border-sky-300/30"></div>
                    @endif

                    {{-- BOTÕES DE DANO --}}
                    <div class="relative z-30 flex items-center h-full px-1">
                        <button type="button" title="-5" @click="await triggerAction('{{ route('combats.npcs.damage', [$combat->id, $combatNpc->id]) }}', 'damage', 5)" class="text-white/70 hover:text-white hover:bg-white/10 px-2 h-full font-bold transition">«</button>
                        <button type="button" title="-1" @click="await triggerAction('{{ route('combats.npcs.damage', [$combat->id, $combatNpc->id]) }}', 'damage', 1)" class="text-white/70 hover:text-white hover:bg-white/10 px-2 h-full font-bold transition">‹</button>
                    </div>

                    {{-- TEXTO HP / INPUT DIRETO --}}
                    <div class="relative z-20 flex-1 flex justify-center items-center gap-2">
                        <span x-show="!editingHp"
                            @click="directHp = currentHp; editingHp = true; $nextTick(() => { $refs.hpInput.focus(); $refs.hpInput.select(); })"
                            class="cursor-pointer text-white font-bold tracking-wider drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)] text-xs pointer-events-auto select-none"
                        >
                            <span x-text="currentHp"></span> / <span x-text="maxHp"></span>
                        </span>
                        
                        <input 
                            x-ref="hpInput" 
                            x-show="editingHp" 
                            type="number" 
                            x-model.number="directHp"
                            @keydown.enter="$el.blur()"
                            @blur="await handleDirectHp('{{ route('combats.npcs.damage', [$combat->id, $combatNpc->id]) }}', '{{ route('combats.npcs.heal', [$combat->id, $combatNpc->id]) }}')"
                            class="w-16 h-6 rounded bg-black/80 text-center text-white font-bold text-xs pointer-events-auto focus:outline-none focus:ring-1 focus:ring-amber-500"
                        >

                        {{-- Badge de Vida Temporária --}}
                        <template x-if="!editingTempHp">
                            <span
                                @click="
                                    directTempHp = {{ $combatNpc->temporary_hp }};
                                    editingTempHp = true;
                                    $nextTick(() => {
                                        $refs.tempHp.focus();
                                        $refs.tempHp.select();
                                    });
                                "
                                class="cursor-pointer inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold border shadow-xs select-none transition-all duration-300 {{ $hasTempHp ? 'bg-sky-600/90 hover:bg-sky-500 text-white border-sky-300 shadow-[0_0_8px_rgba(56,189,248,0.4)]' : 'bg-black/40 hover:bg-black/60 text-stone-400/80 border-stone-600/40 opacity-60 hover:opacity-100' }}"
                                title="Vida Temporária"
                            >
                                🛡 <span x-text="directTempHp"></span>
                            </span>
                        </template>

                        <input
                            x-ref="tempHp"
                            x-show="editingTempHp"
                            type="number"
                            min="0"
                            x-model.number="directTempHp"
                            @keydown.enter="$el.blur()"
                            @blur="await handleTemporaryHp('{{ route('combats.npcs.temporaryHp', [$combat->id, $combatNpc->id]) }}')"
                            class="w-14 h-6 rounded bg-sky-900 text-center text-white font-bold text-xs pointer-events-auto focus:outline-none focus:ring-1 focus:ring-sky-300 border border-sky-400/50"
                        >
                    </div>

                    {{-- BOTÕES DE CURA --}}
                    <div class="relative z-30 flex items-center h-full px-1">
                        <button type="button" title="+1" @click="await triggerAction('{{ route('combats.npcs.heal', [$combat->id, $combatNpc->id]) }}', 'heal', 1)" class="text-white/70 hover:text-white hover:bg-white/10 px-2 h-full font-bold transition">›</button>
                        <button type="button" title="+5" @click="await triggerAction('{{ route('combats.npcs.heal', [$combat->id, $combatNpc->id]) }}', 'heal', 5)" class="text-white/70 hover:text-white hover:bg-white/10 px-2 h-full font-bold transition">»</button>
                    </div>
                </div>

                @if($combatNpc->viewModel->combat->initiative)
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-[#f5efe6] border border-[#cdbb9f]/60 rounded-xl shadow-xs self-start">
                        <span class="text-[10px] font-bold text-[#8c6239] uppercase tracking-wider">Iniciativa</span>
                        <span class="font-serif text-xs font-bold text-[#6b1d14] bg-white px-2 py-0.5 rounded-md border border-[#cdbb9f]/40 shadow-2xs lining-nums">{{ $combatNpc->viewModel->combat->initiative }}</span>
                    </div>
                @endif

                {{-- DESLOCAMENTO --}}
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-[#f5efe6] border border-[#cdbb9f]/60 rounded-xl shadow-xs self-start">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-[#8c6239] uppercase tracking-wider">Deslocamento</span>
                        <span class="font-serif text-xs font-bold text-[#6b1d14] bg-white px-2 py-0.5 rounded-md border border-[#cdbb9f]/40 shadow-2xs lining-nums">
                            {{ $combatNpc->viewModel->combat->speed ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FICHA EXPANDIDA --}}
    <div class="border-t border-[#d8c7ab] bg-[#fcfaf6] p-5 w-full">
        @include('combats.components.npc.npc-sheet')
    </div>
</div>