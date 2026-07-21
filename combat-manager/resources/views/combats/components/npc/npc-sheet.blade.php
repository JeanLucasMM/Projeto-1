@php
    $saves = collect($combatNpc->viewModel->savingThrows)->keyBy('ability');
@endphp

{{-- Grade de Atributos Integrada (Tamanho Reduzido) --}}
<div class="ability-groups grid grid-cols-1 md:grid-cols-3 gap-2 w-full my-1.5 text-xs">
    @php
        $abs = collect($combatNpc->viewModel->abilities);
        $columns = [
            [$abs[0] ?? null, $abs[3] ?? null],
            [$abs[1] ?? null, $abs[4] ?? null],
            [$abs[2] ?? null, $abs[5] ?? null],
        ];
    @endphp

    @foreach($columns as $column)
        <div class="ability-group flex-1">
            <div class="ability-header text-[10px] text-black/60 font-bold px-1">
                <div></div>
                <div>MOD</div>
                <div>SALV</div>
            </div>
            
            @foreach($column as $ability)
                @if($ability)
                    @php
                        $save = $saves[$ability->name]->value ?? $ability->modifier;
                    @endphp
                    <div class="ability-row py-0.5 px-1 text-xs">
                        <div class="ability-name">
                            {{ strtoupper(substr($ability->name, 0, 3)) }}
                            <span class="text-[11px]">{{ $ability->value }}</span>
                        </div>
                        <div class="text-[11px]">
                            {{ $ability->modifier >= 0 ? '+' : '' }}{{ $ability->modifier }}
                        </div>
                        <div class="text-[11px]">
                            {{ $save >= 0 ? '+' : '' }}{{ $save }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>

{{-- Proficiências, Sentidos e Resistências Compactos --}}
<div class="details w-full space-y-0.5 text-xs my-1.5">
    @if(count($combatNpc->viewModel->skills) > 0)
        <div class="detail-row">
            <strong>Perícias:</strong>
            {{ collect($combatNpc->viewModel->skills)->map(fn($s) => $s->name . ' ' . ($s->value >= 0 ? '+' : '') . $s->value)->join(', ') }}
        </div>
    @endif

    @php
        $extras = [
            'Resistências a Dano' => $combatNpc->viewModel->combat->resistances,
            'Imunidades a Dano' => $combatNpc->viewModel->combat->immunities,
            'Imunidades a Condition' => $combatNpc->viewModel->combat->conditionImmunities,
            'Vulnerabilidades a Dano' => $combatNpc->viewModel->combat->vulnerabilities,
            'Sentidos' => $combatNpc->viewModel->combat->senses,
            'Idiomas' => $combatNpc->viewModel->combat->languages,
        ];
    @endphp

    @foreach($extras as $label => $value)
        @if($value && $value !== '-')
            <div class="detail-row">
                <strong>{{ $label }}:</strong> {{ $value }}
            </div>
        @endif
    @endforeach
</div>

<div class="stat-divider my-1.5"></div>

{{-- Fluxo de Ações Compacto --}}
<div class="content-flow w-full space-y-2 text-xs">
    @foreach($combatNpc->viewModel->sections as $section)
        <div class="section w-full">
            <h3 class="section-title text-sm font-bold text-[#5a1810] border-b border-[#5a1810]/30 pb-0.5 mb-1">{{ $section->title }}</h3>
            
            @foreach($section->items as $item)
                @php
                    $rawTitle = ltrim(rtrim($item->title, '. :'), '. :');
                    $cleanTitle = $rawTitle;
                    $isTracker = false;
                    $maxUses = 0;
                    $trackerLabel = '';

                    if (preg_match('/^(.*?)_{3,}\s*(.*)$/', $rawTitle, $matches)) {
                        $titleName = rtrim($matches[1]);
                        $usageText = trim($matches[2], "() :."); 
                        
                        if (strpos($usageText, '/') !== false) {
                            $parts = explode('/', $usageText);
                            if ($parts[0] === '') {
                                $maxUses = isset($parts[1]) ? intval($parts[1]) : 0;
                                $trackerLabel = '';
                            } else {
                                $maxUses = intval($parts[0]);
                                $trackerLabel = isset($parts[1]) ? trim($parts[1]) : '';
                            }
                            $isTracker = $maxUses > 0;
                            $cleanTitle = $titleName;
                        } else {
                            if (preg_match('/^(\d+)(.*)$/', $usageText, $subMatches)) {
                                $isTracker = true;
                                $maxUses = intval($subMatches[1]);
                                $trackerLabel = trim($subMatches[2]);
                                $cleanTitle = $titleName;
                            }
                        }
                    }

                    $currentUses = $combatNpc->getResource($cleanTitle, $maxUses);
                @endphp
                <div class="feature w-full mb-1.5 leading-normal">
                    <strong class="feature-title font-semibold">
                        {!! $cleanTitle !!}@if($isTracker)<span 
                            x-data="{ 
                                current: {{ $currentUses }}, 
                                max: {{ $maxUses }}, 
                                label: '{{ $trackerLabel }}',
                                npcId: '{{ $combatNpc->id }}',
                                featureTitle: '{{ addslashes($cleanTitle) }}',
                                async saveData() {
                                    try {
                                        await fetch('/combat/npc/update-resource', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                combat_npc_id: this.npcId,
                                                feature_title: this.featureTitle,
                                                current_uses: this.current
                                            }),
                                            keepalive: true
                                        });
                                    } catch (e) {
                                        console.error(e);
                                    }
                                }
                            }" 
                            x-init="
                                $watch('current', value => {
                                    saveData();
                                });
                            "
                            @visibilitychange.window="if (document.visibilityState === 'hidden') saveData()"
                            @beforeunload.window="saveData()"
                            class="inline-flex items-center ml-1 px-1.5 py-0.2 bg-[#5a1810] text-[#f4f1e8] rounded border border-black/20 font-sans tracking-normal not-italic select-none"
                            style="display: inline-flex; font-style: normal; vertical-align: middle; white-space: nowrap; font-size: 10px; font-weight: bold;"
                        >
                            <button type="button" @click="if(current > 0) current--" class="hover:text-white/70 active:text-white/40 transition font-bold text-xs focus:outline-none cursor-pointer border-none bg-transparent p-0 m-0 text-[#f4f1e8]">-</button>
                            <span class="mx-1 min-w-[24px] text-center" x-text="current + '/' + max + (label ? ' ' + label : '')"></span>
                            <button type="button" @click="if(current < max) current++" class="hover:text-white/70 active:text-white/40 transition font-bold text-xs focus:outline-none cursor-pointer border-none bg-transparent p-0 m-0 text-[#f4f1e8]">+</button>
                        </span>@endif
                    </strong>
                    <span class="text-black/90">{!! nl2br($item->text) !!}</span>
                </div>
            @endforeach
        </div>
    @endforeach
</div>