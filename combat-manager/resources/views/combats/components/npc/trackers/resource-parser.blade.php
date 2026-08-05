@php
use App\Services\Interpreters\SpellSlotParser;

$result = SpellSlotParser::parse(
    $text,
    $combatNpc,
    $resourcePrefix
);

// 1. Primeiro aplicamos o nl2br apenas no texto puro
$parsedText = nl2br($result['text']);

$trackers = $result['trackers'];

// 2. Depois fazemos o replace, injetando o HTML limpo (sem sofrer ação do nl2br)
foreach ($trackers as $index => $tracker) {
    $parsedText = str_replace(
        "__TRACKER__{$index}__",
        view(
            'combats.components.npc.trackers.tracker-renderer',
            [
                'tracker'   => $tracker,
                'combatNpc' => $combatNpc,
            ]
        )->render(),
        $parsedText
    );
}
@endphp

{{-- 3. Agora imprimimos o resultado final direto --}}
{!! $parsedText !!}