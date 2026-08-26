<x-app-layout>
    <div
        x-data="{
            createOpen: {{ $errors->any() ? 'true' : 'false' }},
            advancedOpen: false,
            xpEnabled: {{ old('xp_enabled') ? 'true' : 'false' }},
            customProfEnabled: {{ old('custom_prof_enabled') ? 'true' : 'false' }},
            multiClass: {{ old('multi_class_enabled') ? 'true' : 'false' }},
            imagePreview: null,

            classes: [
                {
                    class: '{{ old('classes.0.class', '') }}',
                    subclass: '{{ old('classes.0.subclass', '') }}',
                    level: {{ (int) old('classes.0.level', 1) }}
                }
            ],

            addClass() {
                if (this.classes.length < 6) {
                    this.multiClass = true;
                    this.classes.push({
                        class: '',
                        subclass: '',
                        level: 1
                    });
                }
            },

            removeClass(index) {
                if (this.classes.length <= 1) return;
                this.classes.splice(index, 1);
                this.multiClass = this.classes.length > 1;
            },

            get totalLevel() {
                return this.classes.reduce(
                    (total, item) => total + (parseInt(item.level) || 0),
                    0
                );
            },

            updatePreview(event) {
                const file = event.target.files[0];

                if (!file) {
                    this.imagePreview = null;
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => this.imagePreview = e.target.result;
                reader.readAsDataURL(file);
            },

            closeCreate() {
                this.createOpen = false;
            }
        }"
        class="min-h-full px-5 py-8 md:px-8"
        @keydown.escape.window="closeCreate()"
    >
        <div class="mx-auto max-w-7xl">

            {{-- Header --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#8c6239]">
                        Grimório de Aventureiro
                    </p>

                    <div class="mt-1 flex items-center gap-3">
                        <h1 class="font-serif text-3xl font-black text-[#53150f]">
                            Personagens
                        </h1>

                        @if ($characters->isNotEmpty())
                            <span class="rounded-lg border border-[#cdbb9f]/60 bg-[#efe9dc] px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                {{ $characters->count() }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-2 text-sm text-[#8c6239]">
                        Crie e gerencie seus aventureiros.
                    </p>
                </div>

                <button
                    type="button"
                    @click="createOpen = true"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#6b1d14] px-5 py-3 text-[10px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:-translate-y-0.5 hover:bg-[#53150f]"
                >
                    <span class="text-base leading-none">+</span>
                    Criar Personagem
                </button>
            </div>

            @if (session('success'))
                <div class="mb-6 flex items-center gap-2 rounded-xl border border-emerald-800/20 bg-emerald-50/60 px-4 py-3 text-sm text-emerald-900">
                    <span class="font-black">✓</span>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Lista --}}
            @if ($characters->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#cdbb9f] bg-[#f4f1e8]/60 px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#efe9dc] text-[#6b1d14]">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>

                    <h2 class="font-serif text-xl font-black text-[#53150f]">
                        Nenhum personagem ainda
                    </h2>

                    <p class="mt-2 text-sm text-[#8c6239]">
                        Crie seu primeiro personagem para começar a aventura.
                    </p>

                    <button
                        type="button"
                        @click="createOpen = true"
                        class="mt-5 rounded-xl border border-[#6b1d14]/20 bg-[#efe9dc] px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#6b1d14] transition hover:bg-[#6b1d14] hover:text-[#f4f1e8]"
                    >
                        Criar personagem
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($characters as $character)
                        @php
                            $primaryClass = $character->primary_class;
                            $className = $primaryClass?->class ?? 'Classe não definida';
                            $subclassName = $primaryClass?->subclass;
                            $combat = $character->combat;

                            $currentHp = (int) ($combat?->current_hp ?? 0);
                            $maxHp = (int) ($combat?->max_hp ?? 0);
                            $bonusMaxHp = (int) ($combat?->temporary_max_hp ?? 0);
                            $effectiveMaxHp = max(0, $maxHp + $bonusMaxHp);

                            $hpPercent = $effectiveMaxHp > 0
                                ? max(0, min(100, ($currentHp / $effectiveMaxHp) * 100))
                                : 0;

                            $status = 'Estável';
                            $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-800';

                            if ($currentHp <= 0) {
                                $status = 'Caído';
                                $statusClass = 'border-red-200 bg-red-50 text-red-800';
                            } elseif ($hpPercent <= 25) {
                                $status = 'Crítico';
                                $statusClass = 'border-red-200 bg-red-50 text-red-800';
                            } elseif ($hpPercent <= 50) {
                                $status = 'Ferido';
                                $statusClass = 'border-amber-200 bg-amber-50 text-amber-800';
                            }

                            $usesExperience = (bool) data_get(
                                $combat?->overrides,
                                'uses_experience',
                                false
                            );
                        @endphp

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-[#cdbb9f]/60 bg-[#f4f1e8] shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative aspect-[4/3] overflow-hidden bg-[#efe9dc]">
                                @if ($character->image_path)
                                    <img
                                        src="{{ Storage::url($character->image_path) }}"
                                        alt="{{ $character->name }}"
                                        class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-105"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-tr from-[#efe9dc] to-[#f4f1e8] text-[#8c6239]/35">
                                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7-7h14a7 7 0 00-7 7z"/>
                                        </svg>
                                    </div>
                                @endif

                                <span class="absolute right-3 top-3 rounded-lg border border-[#cdbb9f]/60 bg-[#f4f1e8]/90 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] backdrop-blur-sm">
                                    Nível {{ $character->level }}
                                </span>

                                <span class="absolute bottom-3 left-3 rounded-lg border px-2.5 py-1 text-[8px] font-black uppercase tracking-widest shadow-sm backdrop-blur-sm {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <h2 class="truncate font-serif text-xl font-black text-[#53150f] transition group-hover:text-[#6b1d14]">
                                    {{ $character->name }}
                                </h2>

                                <p class="mt-1 truncate text-xs font-semibold text-[#8c6239]">
                                    {{ $character->species ?? 'Espécie não definida' }}
                                    <span class="opacity-60">·</span>
                                    {{ $className }}

                                    @if ($character->classes->count() > 1)
                                        <span class="opacity-60">
                                            + {{ $character->classes->count() - 1 }}
                                        </span>
                                    @endif
                                </p>

                                @if ($subclassName)
                                    <p class="mt-0.5 truncate text-[9px] font-black uppercase tracking-widest text-[#8c6239]/70">
                                        {{ $subclassName }}
                                    </p>
                                @endif

                                <div class="mt-5">
                                    <div class="mb-1.5 flex items-center justify-between">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                            Vida
                                        </span>

                                        <span class="text-[10px] font-black text-[#53150f]">
                                            {{ $currentHp }}/{{ $effectiveMaxHp }}
                                        </span>
                                    </div>

                                    <div class="h-2.5 overflow-hidden rounded-full bg-[#d8c7ab]">
                                        @if ($effectiveMaxHp > 0)
                                            <div
                                                class="h-full rounded-full transition-all duration-500 {{ $hpPercent > 50 ? 'bg-emerald-600' : ($hpPercent > 25 ? 'bg-amber-500' : 'bg-red-600') }}"
                                                style="width: {{ $hpPercent }}%"
                                            ></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 grid {{ $usesExperience ? 'grid-cols-3' : 'grid-cols-2' }} gap-2">
                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 px-2 py-2 text-center">
                                        <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">CA</span>
                                        <span class="mt-0.5 block text-lg font-black text-[#53150f]">
                                            {{ $combat?->armor_class ?? '—' }}
                                        </span>
                                    </div>

                                    @if ($usesExperience)
                                        <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 px-2 py-2 text-center">
                                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">XP</span>
                                            <span class="mt-0.5 block truncate text-xs font-black text-[#53150f]">
                                                {{ number_format((int) ($combat?->experience_points ?? 0), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 px-2 py-2 text-center">
                                        <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">Prof.</span>
                                        <span class="mt-0.5 block text-lg font-black text-[#53150f]">
                                            {{ $character->proficiency_bonus >= 0 ? '+' : '' }}{{ $character->proficiency_bonus }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-auto flex items-center gap-2 border-t border-[#cdbb9f]/30 pt-4">
                                    <a
                                        href="{{ route('characters.show', $character) }}"
                                        class="flex-1 rounded-xl bg-[#6b1d14] px-4 py-2.5 text-center text-[10px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:bg-[#53150f]"
                                    >
                                        Abrir Ficha
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('characters.destroy', $character) }}"
                                        onsubmit="return confirm('Tem certeza que deseja remover este personagem? Esta ação não pode ser desfeita.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Excluir personagem"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-red-900/10 text-red-700 transition hover:bg-red-900 hover:text-[#f4f1e8]"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 011 1h-4a1 1 0 01-1-1V3m-6 4h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Modal --}}
        <div
            x-show="createOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2b1d17]/60 p-4 backdrop-blur-sm"
        >
            <div
                x-show="createOpen"
                x-transition
                @click.outside="closeCreate()"
                class="relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#f4f1e8] shadow-2xl"
            >
                <div class="flex shrink-0 items-center justify-between border-b border-[#cdbb9f]/70 bg-[#efe9dc]/60 px-5 py-4">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                            Novo Personagem
                        </p>
                        <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                            Forjar Aventureiro
                        </h2>
                    </div>

                    <button
                        type="button"
                        @click="closeCreate()"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-[#8c6239] hover:bg-[#6b1d14]/10 hover:text-[#6b1d14]"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </button>
                </div>

                <form
                    method="POST"
                    action="{{ route('characters.store') }}"
                    enctype="multipart/form-data"
                    class="min-h-0 overflow-y-auto"
                >
                    @csrf

                    <input type="hidden" name="multi_class_enabled" :value="classes.length > 1 ? 1 : 0">
                    <input type="hidden" name="level" :value="totalLevel">

                    <div class="space-y-5 p-5 sm:p-6">

                        {{-- Identidade --}}
                        <div>
                            <div class="mb-4">
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                                    Identidade
                                </p>
                                <p class="mt-1 text-xs text-[#8c6239]/70">
                                    Informações básicas do aventureiro.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Nome
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        maxlength="120"
                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                    >
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Espécie
                                    </label>
                                    <input
                                        type="text"
                                        name="species"
                                        value="{{ old('species') }}"
                                        required
                                        maxlength="80"
                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                    >
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Antecedente
                                    </label>
                                    <input
                                        type="text"
                                        name="background"
                                        value="{{ old('background') }}"
                                        required
                                        maxlength="120"
                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                    >
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Alinhamento
                                    </label>
                                    <input
                                        type="text"
                                        name="alignment"
                                        value="{{ old('alignment') }}"
                                        required
                                        maxlength="50"
                                        class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Classes --}}
                        <div class="border-t border-[#cdbb9f]/40 pt-5">
                            <div class="mb-4 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                                        Classes
                                    </p>

                                    <p class="mt-1 text-xs text-[#8c6239]/70">
                                        Defina a composição inicial do personagem.
                                    </p>
                                </div>

                                <div class="rounded-lg border border-[#cdbb9f]/50 bg-[#efe9dc] px-3 py-1.5 text-center">
                                    <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Nível Total
                                    </span>
                                    <span class="block font-serif text-lg font-black text-[#53150f]" x-text="totalLevel"></span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in classes" :key="index">
                                    <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                                Classe <span x-text="index + 1"></span>
                                            </span>

                                            <button
                                                x-show="classes.length > 1"
                                                type="button"
                                                @click="removeClass(index)"
                                                class="text-[9px] font-black uppercase tracking-widest text-red-700 hover:text-red-900"
                                            >
                                                Remover
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                                            <div class="sm:col-span-5">
                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                                    Classe
                                                </label>

                                                <input
                                                    type="text"
                                                    :name="`classes[${index}][class]`"
                                                    x-model="item.class"
                                                    required
                                                    maxlength="80"
                                                    placeholder="Ex.: Clérigo"
                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                                >
                                            </div>

                                            <div class="sm:col-span-5">
                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                                    Subclasse
                                                </label>

                                                <input
                                                    type="text"
                                                    :name="`classes[${index}][subclass]`"
                                                    x-model="item.subclass"
                                                    maxlength="80"
                                                    placeholder="Opcional"
                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                                >
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                                    Nível
                                                </label>

                                                <input
                                                    type="number"
                                                    :name="`classes[${index}][level]`"
                                                    x-model.number="item.level"
                                                    min="1"
                                                    max="20"
                                                    required
                                                    class="w-full rounded-lg border border-[#cdbb9f] bg-white px-3 py-2 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <button
                                type="button"
                                x-show="classes.length === 1"
                                @click="addClass()"
                                class="mt-3 inline-flex items-center gap-2 rounded-lg border border-[#cdbb9f] bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] transition hover:border-[#6b1d14]/30 hover:bg-[#efe9dc]"
                            >
                                <span class="text-sm">+</span>
                                Adicionar multiclasse
                            </button>

                            <button
                                type="button"
                                x-show="classes.length > 1"
                                @click="addClass()"
                                class="mt-3 text-[9px] font-black uppercase tracking-widest text-[#6b1d14] hover:text-[#53150f]"
                                x-show="classes.length < 6"
                            >
                                + Adicionar outra classe
                            </button>
                        </div>

                        {{-- Avançado --}}
                        <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40">
                            <button
                                type="button"
                                @click="advancedOpen = !advancedOpen"
                                class="flex w-full items-center justify-between px-4 py-3 text-left"
                            >
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#8c6239]">
                                        Configurações avançadas
                                    </span>

                                    <p class="mt-0.5 text-xs text-[#8c6239]/70">
                                        XP e regras personalizadas.
                                    </p>
                                </div>

                                <svg
                                    class="h-4 w-4 text-[#8c6239] transition-transform"
                                    :class="{ 'rotate-180': advancedOpen }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="advancedOpen" x-transition class="border-t border-[#cdbb9f]/40 px-4 pb-4">
                                <div class="space-y-3 pt-4">

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <input
                                                type="checkbox"
                                                name="xp_enabled"
                                                value="1"
                                                x-model="xpEnabled"
                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"
                                            >

                                            <span>
                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">
                                                    Usar experiência
                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">
                                                    Ative somente se a campanha usar XP.
                                                </span>
                                            </span>
                                        </label>

                                        <div x-show="xpEnabled" x-transition class="mt-3">
                                            <input
                                                type="number"
                                                name="experience_points"
                                                min="0"
                                                value="{{ old('experience_points', 0) }}"
                                                :disabled="!xpEnabled"
                                                placeholder="XP inicial"
                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                            >
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] p-3">
                                        <label class="flex cursor-pointer items-center gap-3">
                                            <input
                                                type="checkbox"
                                                name="custom_prof_enabled"
                                                value="1"
                                                x-model="customProfEnabled"
                                                class="rounded border-[#cdbb9f] text-[#6b1d14] focus:ring-[#6b1d14]/30"
                                            >

                                            <span>
                                                <span class="block text-[10px] font-black uppercase tracking-widest text-[#53150f]">
                                                    Proficiência personalizada
                                                </span>

                                                <span class="text-[11px] text-[#8c6239]">
                                                    Sobrescreva o valor padrão.
                                                </span>
                                            </span>
                                        </label>

                                        <div x-show="customProfEnabled" x-transition class="mt-3">
                                            <input
                                                type="number"
                                                name="proficiency_bonus"
                                                value="{{ old('proficiency_bonus') }}"
                                                :disabled="!customProfEnabled"
                                                placeholder="Bônus de proficiência"
                                                class="w-full rounded-xl border border-[#cdbb9f] bg-white px-4 py-2.5 text-sm font-black text-[#53150f] focus:border-[#6b1d14] focus:ring focus:ring-[#6b1d14]/20"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Foto --}}
                        <div class="border-t border-[#cdbb9f]/40 pt-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#cdbb9f] bg-[#efe9dc]">
                                    <svg class="h-7 w-7 text-[#8c6239]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>

                                    <img
                                        x-show="imagePreview"
                                        x-cloak
                                        :src="imagePreview"
                                        alt="Pré-visualização"
                                        class="absolute inset-0 h-full w-full object-cover object-top"
                                    >
                                </div>

                                <div class="flex-1">
                                    <label class="mb-1.5 block text-[10px] font-black uppercase tracking-widest text-[#8c6239]">
                                        Foto de Perfil
                                    </label>

                                    <input
                                        type="file"
                                        name="image"
                                        accept="image/*"
                                        @change="updatePreview"
                                        class="block w-full text-sm text-[#8c6239] file:mr-4 file:cursor-pointer file:rounded-xl file:border-0 file:bg-[#efe9dc] file:px-4 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-[#6b1d14] hover:file:bg-[#6b1d14] hover:file:text-[#f4f1e8]"
                                    >
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-red-800/20 bg-red-50/50 p-4 text-sm text-red-900">
                                <p class="mb-2 text-[9px] font-black uppercase tracking-widest text-red-800">
                                    Corrija os campos abaixo
                                </p>

                                <ul class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex gap-2">
                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-red-600"></span>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-[#cdbb9f]/50 bg-[#efe9dc]/40 px-5 py-4 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            @click="closeCreate()"
                            class="rounded-xl px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#8c6239] hover:bg-[#8c6239]/10"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-[#6b1d14] px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#f4f1e8] transition hover:bg-[#53150f]"
                        >
                            Confirmar Criação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>