@props([
    'multiAttack',
    'attacks' => [],
    'actions' => [],
    'header' => null,
])

@php
    /*
    |--------------------------------------------------------------------------
    | Normalização de Dados
    |--------------------------------------------------------------------------
    */
    if (is_object($multiAttack)) {
        $multiAttack = json_decode(json_encode($multiAttack), true);
    }

    $title = trim((string) ($multiAttack['title'] ?? 'Multiataque'));
    $mode = $multiAttack['mode'] ?? 'automatic';
    $customText = trim((string) ($multiAttack['customText'] ?? ''));
    $entries = $multiAttack['entries'] ?? [];

    if (!is_array($entries)) {
        $entries = [];
    }

    // Nome da Criatura vindo do Header
    $npcName = 'A criatura';
    if ($header) {
        $npcName = is_array($header) ? ($header['name'] ?? 'A criatura') : ($header->name ?? 'A criatura');
    }

    /*
    |--------------------------------------------------------------------------
    | Dicionário de Números por Extenso
    |--------------------------------------------------------------------------
    */
    $numeros = [
        1 => 'um',
        2 => 'dois',
        3 => 'três',
        4 => 'quatro',
        5 => 'cinco',
        6 => 'seis',
        7 => 'sete',
        8 => 'oito',
        9 => 'nove',
        10 => 'dez',
    ];

    /*
    |--------------------------------------------------------------------------
    | Índices de Ataques e Ações (Permite busca O(1) por ID)
    |--------------------------------------------------------------------------
    */
    $attackMap = [];
    foreach ($attacks as $attack) {
        $attackArr = is_object($attack) ? json_decode(json_encode($attack), true) : $attack;
        $id = (string) ($attackArr['id'] ?? '');
        if ($id !== '') {
            $attackMap[$id] = $attackArr;
        }
    }

    $actionMap = [];
    foreach ($actions as $action) {
        $actionArr = is_object($action) ? json_decode(json_encode($action), true) : $action;
        $id = (string) ($actionArr['id'] ?? '');
        if ($id !== '') {
            $actionMap[$id] = $actionArr;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Construção das Partes do Multiataque
    |--------------------------------------------------------------------------
    */
    $multiAttackParts = [];

    foreach ($entries as $entry) {
        if (is_object($entry)) {
            $entry = (array) $entry;
        }

        if (!is_array($entry)) {
            continue;
        }

        $source = $entry['source'] ?? 'attack';
        $sourceId = (string) ($entry['sourceId'] ?? '');
        $quantity = max(1, (int) ($entry['quantity'] ?? 1));
        $extenso = $numeros[$quantity] ?? $quantity;

        // Fonte = Ataque
        if ($source === 'attack' && isset($attackMap[$sourceId])) {
            $attackItem = $attackMap[$sourceId];
            $nome = trim((string) ($attackItem['title'] ?? $attackItem['name'] ?? 'Ataque sem nome'));

            if ($quantity <= 1) {
                $multiAttackParts[] = "um ataque usando {$nome}";
            } else {
                $multiAttackParts[] = "{$extenso} ({$quantity}) ataques usando {$nome}";
            }
        } 
        // Fonte = Ação (Ex: Conjuração, Habilidade especial)
        elseif ($source === 'action' && isset($actionMap[$sourceId])) {
            $actionItem = $actionMap[$sourceId];
            $nome = trim((string) ($actionItem['title'] ?? $actionItem['name'] ?? 'Ação sem nome'));

            if ($quantity <= 1) {
                $multiAttackParts[] = "a ação {$nome}";
            } else {
                $multiAttackParts[] = "{$extenso} ({$quantity}) utilizações da ação {$nome}";
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Texto Automático (Formatação Gramatical com 'e' e vírgulas)
    |--------------------------------------------------------------------------
    */
    $automaticText = '';

    if (!empty($multiAttackParts)) {
        $totalParts = count($multiAttackParts);

        if ($totalParts === 1) {
            $formattedParts = $multiAttackParts[0];
        } else {
            $lastPart = array_pop($multiAttackParts);
            $formattedParts = implode(', ', $multiAttackParts) . ' e ' . $lastPart;
        }

        $automaticText = "{$npcName} realiza {$formattedParts}.";
    }

    /*
    |--------------------------------------------------------------------------
    | Texto Final
    |--------------------------------------------------------------------------
    */
    $finalText = ($mode === 'custom' || $mode === 'text')
        ? $customText
        : $automaticText;
@endphp

{{-- ====================================================================== --}}
{{-- MULTIATAQUE (Negrito + Itálico em tudo) --}}
{{-- ====================================================================== --}}

<div class="mb-2 text-sm leading-relaxed font-bold italic">
    <span class="text-[#1f2937]">
        {{ $title ? $title . '.' : 'Multiataque.' }}
    </span>

    @if($finalText !== '')
        <span class="ml-1 text-gray-800">
            {!! $finalText !!}
        </span>
    @endif
</div>