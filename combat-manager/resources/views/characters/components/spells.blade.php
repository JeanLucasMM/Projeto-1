@props(['character'])

@php
    $characterSpells = $character->characterSpells
        ->filter(fn ($characterSpell) =>
            $characterSpell->spell !== null &&
            $characterSpell->known
        );

    $cantrips = $characterSpells->filter(
        fn ($characterSpell) => $characterSpell->spell->level === 0
    );

    $leveledSpells = $characterSpells
        ->filter(fn ($characterSpell) => $characterSpell->spell->level > 0)
        ->groupBy(fn ($characterSpell) => $characterSpell->spell->level);

    $spellcasting = $character->spellcasting->first();

    $spellSaveDc = $spellcasting?->spell_save_dc_override;

    $spellAttackBonus = $spellcasting?->spell_attack_bonus_override;

    /*
     * Caso ainda não exista override, deixamos o valor em aberto.
     *
     * Posteriormente podemos calcular automaticamente:
     *
     * 8 + proficiência + modificador do atributo de conjuração
     *
     * e:
     *
     * proficiência + modificador do atributo
     */
    $spellAbility = $spellcasting?->ability;

    $abilityModifier = 0;

    if ($spellAbility && $character->abilities) {
        $abilityScore = (int) ($character->abilities->{$spellAbility} ?? 10);
        $abilityModifier = (int) floor(($abilityScore - 10) / 2);
    }

    $calculatedSpellSaveDc = 8
        + (int) ($character->proficiency_bonus ?? 0)
        + $abilityModifier;

    $calculatedSpellAttackBonus =
        (int) ($character->proficiency_bonus ?? 0)
        + $abilityModifier;

    $spellSaveDc = $spellSaveDc ?? $calculatedSpellSaveDc;
    $spellAttackBonus = $spellAttackBonus ?? $calculatedSpellAttackBonus;
@endphp

<div
    x-data="{ open: false, selectedSpell: null }"
    class="relative"
>

    {{-- Botão do Grimório --}}
    <button
        type="button"
        @click="open = true"
        class="group w-full overflow-hidden rounded-2xl border border-[#cdbb9f]/60 bg-[#f4f1e8] text-left shadow-sm transition-all duration-200 hover:border-[#6b1d14]/40 hover:shadow-md"
    >

        <div class="flex items-center justify-between border-b border-[#cdbb9f]/50 bg-[#efe9dc]/60 px-5 py-4">

            <div>

                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                    Grimório
                </p>

                <h2 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                    Magias
                </h2>

            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-[#cdbb9f]/60 bg-[#f4f1e8] text-[#6b1d14] transition-transform duration-200 group-hover:scale-105">

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                    />
                </svg>

            </div>

        </div>

        <div class="grid grid-cols-3 gap-2 p-4">

            <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 p-3 text-center">

                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                    Magias
                </span>

                <span class="mt-1 block font-serif text-lg font-black text-[#53150f]">
                    {{ $characterSpells->count() }}
                </span>

            </div>

            <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 p-3 text-center">

                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                    CD
                </span>

                <span class="mt-1 block font-serif text-lg font-black text-[#53150f]">
                    {{ $spellSaveDc }}
                </span>

            </div>

            <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/50 p-3 text-center">

                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                    Ataque
                </span>

                <span class="mt-1 block font-serif text-lg font-black text-[#53150f]">
                    {{ $spellAttackBonus >= 0 ? '+' : '' }}{{ $spellAttackBonus }}
                </span>

            </div>

        </div>

        <div class="px-4 pb-4">

            <div class="flex items-center justify-center rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 px-4 py-2.5">

                <span class="text-[9px] font-black uppercase tracking-widest text-[#6b1d14]">
                    Abrir Grimório
                </span>

            </div>

        </div>

    </button>


    {{-- Overlay --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50"
        @keydown.escape.window="open = false"
    >

        <div
            x-show="open"
            x-transition.opacity
            class="absolute inset-0 bg-[#2a1712]/60 backdrop-blur-sm"
            @click="open = false"
        ></div>


        {{-- Livro --}}
        <div class="relative flex min-h-screen items-center justify-center p-4 sm:p-6">

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                class="relative flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-[#bda98c] bg-[#f4f1e8] shadow-2xl"
                @click.stop
            >

                {{-- Cabeçalho do livro --}}
                <div class="shrink-0 border-b border-[#cdbb9f]/60 bg-[#efe9dc]">

                    <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">

                        <div>

                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-[#8c6239]">
                                Grimório de Aventureiro
                            </p>

                            <h2 class="mt-1 font-serif text-2xl font-black text-[#53150f]">
                                {{ $character->name }}
                            </h2>

                        </div>

                        <button
                            type="button"
                            @click="open = false"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#cdbb9f] bg-[#f4f1e8] text-[#8c6239] transition-colors hover:border-[#6b1d14] hover:text-[#6b1d14]"
                        >
                            ×
                        </button>

                    </div>


                    {{-- Dados de conjuração --}}
                    <div class="grid grid-cols-2 gap-2 border-t border-[#cdbb9f]/40 px-5 py-3 sm:grid-cols-4 sm:px-6">

                        <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] px-3 py-2">

                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                Atributo
                            </span>

                            <span class="mt-1 block text-xs font-black capitalize text-[#53150f]">
                                {{ $spellAbility ?? '—' }}
                            </span>

                        </div>

                        <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] px-3 py-2">

                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                Modificador
                            </span>

                            <span class="mt-1 block text-xs font-black text-[#53150f]">
                                {{ $abilityModifier >= 0 ? '+' : '' }}{{ $abilityModifier }}
                            </span>

                        </div>

                        <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] px-3 py-2">

                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                CD
                            </span>

                            <span class="mt-1 block text-lg font-black text-[#53150f]">
                                {{ $spellSaveDc }}
                            </span>

                        </div>

                        <div class="rounded-xl border border-[#cdbb9f]/40 bg-[#f4f1e8] px-3 py-2">

                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                Ataque
                            </span>

                            <span class="mt-1 block text-lg font-black text-[#53150f]">
                                {{ $spellAttackBonus >= 0 ? '+' : '' }}{{ $spellAttackBonus }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Conteúdo --}}
                <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-6">

                    @if ($characterSpells->isEmpty())

                        <div class="flex min-h-[300px] items-center justify-center">

                            <div class="text-center">

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-[#cdbb9f] bg-[#efe9dc] text-[#8c6239]">

                                    <svg
                                        class="h-7 w-7"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                                        />
                                    </svg>

                                </div>

                                <p class="mt-4 font-serif text-lg font-black text-[#53150f]">
                                    Grimório vazio
                                </p>

                                <p class="mt-1 text-sm text-[#8c6239]">
                                    Nenhuma magia foi adicionada a este personagem.
                                </p>

                            </div>

                        </div>

                    @else

                        {{-- Truques --}}
                        @if ($cantrips->isNotEmpty())

                            <section class="mb-8">

                                <div class="mb-3 flex items-end justify-between">

                                    <div>

                                        <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                            Nível 0
                                        </span>

                                        <h3 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                                            Truques
                                        </h3>

                                    </div>

                                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#8c6239]">
                                        Sem gasto de slot
                                    </span>

                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

                                    @foreach ($cantrips as $characterSpell)

                                        @php
                                            $spell = $characterSpell->spell;
                                        @endphp

                                        <div class="group rounded-2xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-4 transition-all hover:border-[#6b1d14]/40 hover:bg-[#efe9dc]/70">

                                            <div class="flex items-start justify-between gap-3">

                                                <div class="min-w-0">

                                                    <h4 class="font-serif text-base font-black text-[#53150f]">
                                                        {{ $spell->name }}
                                                    </h4>

                                                    <p class="mt-0.5 text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                                        {{ $spell->school }}
                                                    </p>

                                                </div>

                                                @if ($characterSpell->prepared)

                                                    <span class="shrink-0 rounded-lg bg-[#6b1d14] px-2 py-1 text-[8px] font-black uppercase tracking-widest text-[#f4f1e8]">
                                                        Preparada
                                                    </span>

                                                @endif

                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-1.5">

                                                @if ($spell->ritual)
                                                    <span class="rounded-md bg-[#f4f1e8] px-2 py-1 text-[8px] font-bold text-[#8c6239]">
                                                        Ritual
                                                    </span>
                                                @endif

                                                @if ($spell->concentration)
                                                    <span class="rounded-md bg-[#f4f1e8] px-2 py-1 text-[8px] font-bold text-[#8c6239]">
                                                        Concentração
                                                    </span>
                                                @endif

                                            </div>

                                            <div class="mt-4 flex items-center gap-2">

                                                <button
                                                    type="button"
                                                    @click="selectedSpell = {{ $spell->id }}"
                                                    class="flex-1 rounded-xl border border-[#cdbb9f] bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#53150f] transition-colors hover:border-[#6b1d14] hover:text-[#6b1d14]"
                                                >
                                                    Ver magia
                                                </button>

                                                <button
                                                    type="button"
                                                    class="rounded-xl bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#f4f1e8] transition-colors hover:bg-[#53150f]"
                                                >
                                                    Conjurar
                                                </button>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </section>

                        @endif


                        {{-- Magias com nível --}}
                        @foreach ($leveledSpells->sortKeys() as $level => $spells)

                            @php
                                $slots = $character->spellSlots
                                    ->where('slot_level', $level)
                                    ->where('slot_type', 'spell');

                                $availableSlots = $slots->sum('current');
                                $maximumSlots = $slots->sum('maximum');
                            @endphp

                            <section class="mb-8 last:mb-0">

                                <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                                    <div>

                                        <span class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                            Nível {{ $level }}
                                        </span>

                                        <h3 class="mt-1 font-serif text-xl font-black text-[#53150f]">
                                            Magias de {{ $level }}º nível
                                        </h3>

                                    </div>

                                    <div class="flex items-center gap-2">

                                        <div class="rounded-xl border border-[#cdbb9f]/50 bg-[#efe9dc] px-3 py-2 text-center">

                                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                                Slots
                                            </span>

                                            <span class="mt-1 block font-serif text-lg font-black text-[#53150f]">
                                                {{ $availableSlots }}/{{ $maximumSlots }}
                                            </span>

                                        </div>

                                    </div>

                                </div>


                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

                                    @foreach ($spells as $characterSpell)

                                        @php
                                            $spell = $characterSpell->spell;
                                        @endphp

                                        <div class="rounded-2xl border border-[#cdbb9f]/50 bg-[#efe9dc]/40 p-4 transition-all hover:border-[#6b1d14]/40 hover:bg-[#efe9dc]/70">

                                            <div class="flex items-start justify-between gap-3">

                                                <div class="min-w-0">

                                                    <h4 class="font-serif text-base font-black text-[#53150f]">
                                                        {{ $spell->name }}
                                                    </h4>

                                                    <p class="mt-0.5 text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                                        {{ $spell->school }}
                                                    </p>

                                                </div>

                                                @if ($characterSpell->prepared)

                                                    <span class="shrink-0 rounded-lg bg-[#6b1d14] px-2 py-1 text-[8px] font-black uppercase tracking-widest text-[#f4f1e8]">
                                                        Preparada
                                                    </span>

                                                @endif

                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-1.5">

                                                @if ($spell->ritual)

                                                    <span class="rounded-md bg-[#f4f1e8] px-2 py-1 text-[8px] font-bold text-[#8c6239]">
                                                        Ritual
                                                    </span>

                                                @endif

                                                @if ($spell->concentration)

                                                    <span class="rounded-md bg-[#f4f1e8] px-2 py-1 text-[8px] font-bold text-[#8c6239]">
                                                        Concentração
                                                    </span>

                                                @endif

                                            </div>

                                            <div class="mt-4 flex items-center gap-2">

                                                <button
                                                    type="button"
                                                    @click="selectedSpell = {{ $spell->id }}"
                                                    class="flex-1 rounded-xl border border-[#cdbb9f] bg-[#f4f1e8] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#53150f] transition-colors hover:border-[#6b1d14] hover:text-[#6b1d14]"
                                                >
                                                    Ver magia
                                                </button>

                                                <button
                                                    type="button"
                                                    class="rounded-xl bg-[#6b1d14] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-[#f4f1e8] transition-colors hover:bg-[#53150f]"
                                                >
                                                    Conjurar
                                                </button>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </section>

                        @endforeach

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- Detalhes da magia --}}
    <div
        x-show="selectedSpell !== null"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center bg-[#2a1712]/60 p-4 backdrop-blur-sm"
        @click.self="selectedSpell = null"
    >

        <div
            class="max-h-[85vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-[#bda98c] bg-[#f4f1e8] p-6 shadow-2xl"
        >

            @foreach ($characterSpells as $characterSpell)

                @php
                    $spell = $characterSpell->spell;
                @endphp

                <div
                    x-show="selectedSpell === {{ $spell->id }}"
                    x-cloak
                >

                    <div class="flex items-start justify-between gap-4">

                        <div>

                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-[#8c6239]">
                                Magia
                            </p>

                            <h3 class="mt-1 font-serif text-2xl font-black text-[#53150f]">
                                {{ $spell->name }}
                            </h3>

                            <p class="mt-1 text-xs text-[#8c6239]">
                                {{ $spell->school }}
                                ·
                                Nível {{ $spell->level }}
                            </p>

                        </div>

                        <button
                            type="button"
                            @click="selectedSpell = null"
                            class="text-2xl leading-none text-[#8c6239] hover:text-[#6b1d14]"
                        >
                            ×
                        </button>

                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">

                        @if ($spell->casting_time)

                            <div class="rounded-xl bg-[#efe9dc] p-3">

                                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                    Tempo
                                </span>

                                <span class="mt-1 block text-xs font-black text-[#53150f]">
                                    {{ $spell->casting_time }}
                                </span>

                            </div>

                        @endif

                        @if ($spell->range)

                            <div class="rounded-xl bg-[#efe9dc] p-3">

                                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                    Alcance
                                </span>

                                <span class="mt-1 block text-xs font-black text-[#53150f]">
                                    {{ $spell->range }}
                                </span>

                            </div>

                        @endif

                        @if ($spell->duration)

                            <div class="rounded-xl bg-[#efe9dc] p-3">

                                <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                    Duração
                                </span>

                                <span class="mt-1 block text-xs font-black text-[#53150f]">
                                    {{ $spell->duration }}
                                </span>

                            </div>

                        @endif

                        <div class="rounded-xl bg-[#efe9dc] p-3">

                            <span class="block text-[8px] font-black uppercase tracking-widest text-[#8c6239]">
                                Concentração
                            </span>

                            <span class="mt-1 block text-xs font-black text-[#53150f]">
                                {{ $spell->concentration ? 'Sim' : 'Não' }}
                            </span>

                        </div>

                    </div>

                    @if ($spell->description)

                        <div class="mt-6">

                            <p class="text-[9px] font-black uppercase tracking-widest text-[#8c6239]">
                                Descrição
                            </p>

                            <div class="mt-2 rounded-xl border border-[#cdbb9f]/40 bg-[#efe9dc]/40 p-4">

                                <p class="whitespace-pre-line text-sm leading-7 text-[#6b5548]">
                                    {{ $spell->description }}
                                </p>

                            </div>

                        </div>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

</div>