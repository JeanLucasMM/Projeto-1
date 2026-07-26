@php

$parsedText = preg_replace_callback(
    '/_{3,}\/(\d+)/u',
    function ($matches) use ($combatNpc, $resourcePrefix) {

        $label = $matches[0];
        $max = (int) $matches[1];

        $resourceKey = $resourcePrefix.'-'.$label;

        $current = $combatNpc->getResource(
            $resourceKey,
            $max
        );

        return view(
            'combats.components.npc.trackers.resource-counter',
            [
                'combatNpc'   => $combatNpc,
                'resourceKey' => $resourceKey,
                'current'     => $current,
                'max'         => $max,
            ]
        )->render();

    },
    $text
);

@endphp

{!! nl2br($parsedText) !!}