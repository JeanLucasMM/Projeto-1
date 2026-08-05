@switch($tracker->type)

    @case('counter')

        @include(
            'combats.components.npc.trackers.counter',
            [
                'tracker' => $tracker,
                'combatNpc' => $combatNpc,
            ]
        )

        @break

@endswitch