@props([
    'abilities',
    'savingThrows',
    'combat',
])

@php
    $modifier = function ($value) {
        return floor(($value - 10) / 2);
    };

    $format = function ($value) use ($modifier) {
        $mod = $modifier($value);
        return $mod >= 0 ? "+{$mod}" : $mod;
    };

    $proficiencyBonus = $combat->proficiencyBonus ?? 2;

    $groups = [
        ['str', 'int'],
        ['dex', 'wis'],
        ['con', 'cha'],
    ];

    $labels = [
        'str' => 'STR',
        'dex' => 'DEX',
        'con' => 'CON',
        'int' => 'INT',
        'wis' => 'WIS',
        'cha' => 'CHA',
    ];
@endphp

<div class="ability-groups">
    @foreach($groups as $group)
        <div class="ability-group">
            
            {{-- Header MOD/SAVE --}}
            <div class="ability-header">
                <div></div>
                <div>MOD</div>
                <div>SAVE</div>
            </div>

            @foreach($group as $key)
                @php
                    $value = $abilities->$key ?? 10;
                    $save = $savingThrows->get($key);
                    $abilityModifier = $modifier($value);
                    $saveBonus = $abilityModifier;

                    if ($save && ($save['proficient'] ?? false)) {
                        $saveBonus += $proficiencyBonus;
                    }

                    if ($save && (($save['bonus'] ?? 0) != 0)) {
                        $saveBonus += (int) $save['bonus'];
                    }

                    $saveEnabled = $save && (
                        ($save['enabled'] ?? false) || 
                        ($save['proficient'] ?? false) || 
                        (($save['bonus'] ?? 0) != 0)
                    );
                @endphp

                <div class="ability-row">
                    <div class="ability-name">
                        {{ $labels[$key] }}
                        <span>{{ $value }}</span>
                    </div>

                    <div>
                        {{ $format($value) }}
                    </div>

                    <div>
                        @if($saveEnabled)
                            {{ $saveBonus >= 0 ? '+' . $saveBonus : $saveBonus }}
                        @else
                            {{ $format($value) }}
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    @endforeach
</div>