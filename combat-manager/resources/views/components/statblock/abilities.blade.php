@php
$groups = [
    ['STR', 'INT'],
    ['DEX', 'WIS'],
    ['CON', 'CHA'],
];

$abilities = collect($npc->abilities ?? []);
$saves = collect($npc->savingThrows ?? []);
@endphp

<div class="ability-groups">
@foreach($groups as $group)
    <div class="ability-group">
        <div class="ability-header">
            <span></span>
            <span>MOD</span>
            <span>SAVE</span>
        </div>

        @foreach($group as $abbr)
            @php
                // Compara as 3 primeiras letras ignorando maiúsculas/minúsculas (ex: "Strength" vira "STR")
                $ability = $abilities->first(function ($item) use ($abbr) {
                    $name = is_object($item) ? ($item->name ?? '') : ($item['name'] ?? '');
                    return strtoupper(substr($name, 0, 3)) === $abbr;
                });

                $save = $saves->first(function ($item) use ($abbr) {
                    $abilityName = is_object($item) ? ($item->ability ?? '') : ($item['ability'] ?? '');
                    return strtoupper(substr($abilityName, 0, 3)) === $abbr;
                });

                $val = $ability->value ?? 10;
                $mod = $ability->modifier ?? 0;
                // Se não houver Save específico, usa o próprio modificador do atributo
                $saveVal = $save?->value ?? $mod;
            @endphp

            <div class="ability-row">
                <div class="ability-name">
                    {{ $abbr }}
                    <span>{{ $val }}</span>
                </div>

                <div>
                    {{ $mod >= 0 ? '+' : '' }}{{ $mod }}
                </div>

                <div>
                    {{ $saveVal >= 0 ? '+' : '' }}{{ $saveVal }}
                </div>
            </div>
        @endforeach
    </div>
@endforeach
</div>