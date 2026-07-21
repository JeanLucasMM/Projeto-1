@php

$groups = [
    ['STR', 'INT'],
    ['DEX', 'WIS'],
    ['CON', 'CHA'],
];

$abilities = collect($npc->abilities);
$saves = collect($npc->savingThrows);

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

                $ability = $abilities->firstWhere('name', $abbr);

                $save = $saves->firstWhere('ability', $abbr);

            @endphp

            <div class="ability-row">

                <div class="ability-name">

                    {{ $abbr }}

                    <span>{{ $ability->value }}</span>

                </div>

                <div>

                    {{ $ability->modifier >= 0 ? '+' : '' }}{{ $ability->modifier }}

                </div>

                <div>

                    {{ $save?->value >= 0 ? '+' : '' }}{{ $save?->value }}

                </div>

            </div>

        @endforeach

    </div>

@endforeach

</div>