@props([
    'entry',
    'section' => 'default',
    'header' => null,
    'abilities' => null,
    'combat' => null,
    'isFirst' => true,
])

@php
    /*
    |--------------------------------------------------------------------------
    | Normalização Inicial de Dados
    |--------------------------------------------------------------------------
    */
    if (is_object($entry)) {
        $entryArr = json_decode(json_encode($entry), true);
    } else {
        $entryArr = $entry;
    }

    $tracker = $entryArr['tracker'] ?? [];
    $spellcasting = $entryArr['spellcasting'] ?? [];
    $legendary = $entryArr['legendary'] ?? [];
    $lair = $entryArr['lair'] ?? [];

    $trackerEnabled = (bool) ($tracker['enabled'] ?? false);
    $spellcastingEnabled = ($entryArr['type'] ?? 'normal') === 'spellcasting';

    // Nome do NPC vindo do Header
    $npcName = 'A criatura';
    if ($header) {
        $npcName = is_array($header) ? ($header['name'] ?? 'A criatura') : ($header->name ?? 'A criatura');
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica para Introdução de Ações Lendárias
    |--------------------------------------------------------------------------
    */
    $legendaryIntroText = '';
    
    // Restrito APENAS para a seção 'legendary'
    if ($isFirst && $section === 'legendary' && !empty($legendary['intro'])) {
        $text = trim((string) $legendary['intro']);
        
        // Protege a expressão "outra criatura" para não ser afetada
        $text = str_ireplace('outra criatura', '@@OUTRA_CRIATURA@@', $text);
        
        // Substitui "a criatura" ou "criatura" pelo nome do NPC
        $text = preg_replace('/\ba criatura\b/iu', $npcName, $text);
        $text = preg_replace('/\bcriatura\b/iu', $npcName, $text);

        // Restaura a expressão "outra criatura"
        $text = str_replace('@@OUTRA_CRIATURA@@', 'outra criatura', $text);

        // Se o nome não estiver no texto de forma natural, adiciona no início
        if (!str_contains(mb_strtolower($text), mb_strtolower($npcName))) {
            $text = "{$npcName} {$text}";
        }

        $legendaryIntroText = $text;
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica para Introdução de Ações de Covil
    |--------------------------------------------------------------------------
    */
    $lairIntroText = '';
    
    // Restrito APENAS para a seção 'lair'
    if ($isFirst && $section === 'lair' && !empty($lair['intro'])) {
        $text = trim((string) $lair['intro']);

        $text = str_ireplace('outra criatura', '@@OUTRA_CRIATURA@@', $text);
        $text = preg_replace('/\ba criatura\b/iu', $npcName, $text);
        $text = preg_replace('/\bcriatura\b/iu', $npcName, $text);
        $text = str_replace('@@OUTRA_CRIATURA@@', 'outra criatura', $text);

        if (!str_contains(mb_strtolower($text), mb_strtolower($npcName))) {
            $text = "{$npcName} {$text}";
        }

        $lairIntroText = $text;
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica da Interpretação de Conjuração (Spellcasting Narrative)
    |--------------------------------------------------------------------------
    */
    $spellcastingIntro = '';

    if ($spellcastingEnabled) {
        $abilityMap = [
            'str' => 'Força',
            'dex' => 'Destreza',
            'con' => 'Constituição',
            'int' => 'Inteligência',
            'wis' => 'Sabedoria',
            'cha' => 'Carisma',
        ];

        $casterLevel = (int) ($spellcasting['casterLevel'] ?? 1);
        $abilityKey = strtolower($spellcasting['ability'] ?? 'cha');
        $abilityName = $abilityMap[$abilityKey] ?? 'Carisma';

        // Busca Inteligente do Bônus de Proficiência (PB)
        $pb = null;
        if ($combat) {
            $pb = is_array($combat) ? ($combat['proficiencyBonus'] ?? null) : ($combat->proficiencyBonus ?? null);
        }

        if (empty($pb) && $header) {
            $cr = is_array($header) ? ($header['challengeRating'] ?? '0') : ($header->challengeRating ?? '0');
            $cr = (string) $cr;

            $challengeToPb = [
                '0' => 2, '1/8' => 2, '1/4' => 2, '1/2' => 2,
                '1' => 2, '2' => 2, '3' => 2, '4' => 2,
                '5' => 3, '6' => 3, '7' => 3, '8' => 3,
                '9' => 4, '10' => 4, '11' => 4, '12' => 4,
                '13' => 5, '14' => 5, '15' => 5, '16' => 5,
                '17' => 6, '18' => 6, '19' => 6, '20' => 6,
                '21' => 7, '22' => 7, '23' => 7, '24' => 7,
                '25' => 8, '26' => 8, '27' => 8, '28' => 8,
                '29' => 9, '30' => 9,
            ];
            $pb = $challengeToPb[$cr] ?? 2;
        }

        $pb = (int) ($pb ?? 2);

        // Modificador de Atributo
        $mod = 0;
        if ($abilities) {
            $val = is_array($abilities) ? ($abilities[$abilityKey] ?? null) : ($abilities->{$abilityKey} ?? null);
            if (is_numeric($val)) {
                $mod = (int) floor(($val - 10) / 2);
            }
        }

        $attackBonusExtra = (int) ($spellcasting['attackBonusExtra'] ?? 0);
        $saveDCExtra = (int) ($spellcasting['saveDCExtra'] ?? 0);

        $spellAttack = $pb + $mod + $attackBonusExtra;
        $spellDC = 8 + $pb + $mod + $saveDCExtra;

        $spellAttackFormat = $spellAttack >= 0 ? "+{$spellAttack}" : (string) $spellAttack;

        $spellcastingIntro = "{$npcName} é um conjurador de {$casterLevel}º Nível, usando {$abilityName} como canalização. ({$spellAttackFormat} Acerto, CD {$spellDC}).";
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica do Tracker
    |--------------------------------------------------------------------------
    */
    $trackerLabel = '';

    if ($trackerEnabled) {
        $reset = $tracker['reset'] ?? '';
        $uses = (int) ($tracker['uses'] ?? 0);

        switch ($reset) {
            case 'day':
                $trackerLabel = "({$uses}/Dia)";
                break;

            case 'short_rest':
                $trackerLabel = "({$uses}/Des. Curto)";
                break;

            case 'long_rest':
                $trackerLabel = "({$uses}/Des. Longo)";
                break;

            case 'turn':
                $trackerLabel = "({$uses}/Turno)";
                break;

            case 'recharge':
                $min = (int) ($tracker['min'] ?? 4);
                $max = (int) ($tracker['max'] ?? 6);
                $trackerLabel = "(Recarga {$min}–{$max})";
                break;

            case 'custom':
                $customReset = trim((string) ($tracker['customReset'] ?? ''));
                $trackerLabel = $customReset !== '' ? "({$uses}/{$customReset})" : "({$uses}/{$uses})";
                break;

            default:
                $trackerLabel = "({$uses}/{$uses})";
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Limpeza de Conteúdo
    |--------------------------------------------------------------------------
    */
    $cleanContent = '';

    if (!empty($entryArr['content'])) {
        $cleanContent = preg_replace('/,+/', ',', trim((string) $entryArr['content'], " ,"));
        $cleanContent = str_replace(', ,', ',', $cleanContent);
    }
@endphp

{{-- ====================================================================== --}}
{{-- Texto Introdutório de Ações Lendárias (Apenas na 1ª ação Lendária) --}}
{{-- ====================================================================== --}}

@if($legendaryIntroText !== '')
    <div class="text-sm leading-relaxed mb-2 text-gray-800">
        {!! $legendaryIntroText !!}
    </div>
@endif

{{-- ====================================================================== --}}
{{-- Texto Introdutório de Ações de Covil (Apenas na 1ª ação de Covil) --}}
{{-- ====================================================================== --}}

@if($lairIntroText !== '')
    <div class="text-sm leading-relaxed mb-2 text-gray-800">
        {!! $lairIntroText !!}
    </div>
@endif

{{-- ====================================================================== --}}
{{-- Renderização da Entrada (Normal ou Conjuração) --}}
{{-- ====================================================================== --}}

<div class="text-sm leading-relaxed mb-2 [&>p]:inline [&>div]:inline">

    {{-- Título e Tracker em Negrito --}}
    @if(!empty($entryArr['title']))
        <strong class="font-bold text-[#6b1d14]">
            {{ $entryArr['title'] }}@if($trackerEnabled && $trackerLabel !== '') <span class="font-normal">{{ $trackerLabel }}</span>@endif.
        </strong>
    @endif

    {{-- Frase narrativa de Conjuração --}}
    @if($spellcastingEnabled && $spellcastingIntro !== '')
        <span class="ml-1 text-gray-800">
            {{ $spellcastingIntro }}
        </span>
    @endif

    {{-- Conteúdo principal --}}
    @if($cleanContent)
        <span class="ml-1 text-gray-800 [&>p]:inline [&>div]:inline">
            {!! $cleanContent !!}
        </span>
    @endif

</div>

{{-- ====================================================================== --}}
{{-- Lista de Spell Slots --}}
{{-- ====================================================================== --}}

@if($spellcastingEnabled && !empty($spellcasting['slots']))

    <div class="mt-1 space-y-0.5 ml-2">

        @foreach($spellcasting['slots'] as $slot)

            @php
                if (is_object($slot)) {
                    $slot = (array) $slot;
                }

                $slotLevel = (int) ($slot['level'] ?? 0);
                $slotUses = (int) ($slot['uses'] ?? 0);
                $slotSpells = $slot['spells'] ?? '';

                if ($slotLevel === 0) {
                    $slotLabel = 'Truques';
                } else {
                    $usesLabel = ($slotUses === 0) ? 'À vontade' : "{$slotUses}/Dia";
                    $slotLabel = "{$slotLevel}º Nível ({$usesLabel})";
                }

                if (is_array($slotSpells)) {
                    $slotSpells = implode(
                        ', ',
                        array_filter(
                            $slotSpells,
                            fn($value) => !is_null($value) && trim((string) $value) !== ''
                        )
                    );
                } else {
                    $slotSpells = preg_replace('/,+/', ',', trim((string) $slotSpells, " ,"));
                    $slotSpells = str_replace(', ,', ',', $slotSpells);
                }
            @endphp

            <div class="text-sm leading-relaxed">
                <strong class="text-[#6b1d14]">
                    {{ $slotLabel }}
                </strong>

                @if($slotSpells !== '')
                    —
                    <span class="italic text-gray-800">
                        {{ $slotSpells }}
                    </span>
                @endif
            </div>

        @endforeach

    </div>

@endif