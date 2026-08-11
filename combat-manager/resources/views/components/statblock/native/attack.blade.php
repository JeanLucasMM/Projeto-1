@props([
    'attack',
    'abilities' => null,
    'combat' => null,
    'header' => null,
])

@php
    /*
    |--------------------------------------------------------------------------
    | Inicialização
    |--------------------------------------------------------------------------
    */
    if (is_object($attack)) {
        $attack = json_decode(json_encode($attack), true);
    }

    $title = $attack['title'] ?? '';
    $mode = $attack['mode'] ?? 'builder';
    $content = $attack['content'] ?? '';
    $builder = $attack['builder'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | Busca Inteligente do Bônus de Proficiência (PB)
    |--------------------------------------------------------------------------
    */
    $pb = null;

    // 1. Tenta pegar do $combat
    if ($combat) {
        $pb = is_array($combat) ? ($combat['proficiencyBonus'] ?? null) : ($combat->proficiencyBonus ?? null);
    }

    // 2. Se não encontrou no $combat, calcula a partir do Nível de Desafio (CR) no $header
    if (empty($pb) && $header) {
        $cr = is_array($header) ? ($header['challengeRating'] ?? '0') : ($header->challengeRating ?? '0');
        $cr = (string) $cr;

        $challengeTable = [
            '0'   => 2, '1/8' => 2, '1/4' => 2, '1/2' => 2,
            '1'   => 2, '2'   => 2, '3'   => 2, '4'   => 2,
            '5'   => 3, '6'   => 3, '7'   => 3, '8'   => 3,
            '9'   => 4, '10'  => 4, '11'  => 4, '12'  => 4,
            '13'  => 5, '14'  => 5, '15'  => 5, '16'  => 5,
            '17'  => 6, '18'  => 6, '19'  => 6, '20'  => 6,
            '21'  => 7, '22'  => 7, '23'  => 7, '24'  => 7,
            '25'  => 8, '26'  => 8, '27'  => 8, '28'  => 8,
            '29'  => 9, '30'  => 9,
        ];

        $pb = $challengeTable[$cr] ?? 2;
    }

    // Fallback de segurança
    $pb = (int) ($pb ?? 2);

    /*
    |--------------------------------------------------------------------------
    | Modificador de Atributo
    |--------------------------------------------------------------------------
    */
    $getModifier = function($abilityKey) use ($abilities) {
        $key = strtolower($abilityKey);
        
        if ($key === 'none' || !$abilities) {
            return 0;
        }

        $value = is_array($abilities) ? ($abilities[$key] ?? 10) : ($abilities->$key ?? 10);
        return (int) floor(($value - 10) / 2);
    };

    /*
    |--------------------------------------------------------------------------
    | Lógica do Builder
    |--------------------------------------------------------------------------
    */
    $attackText = '';
    
    if ($mode === 'builder' && !empty($builder)) {
        
        $range = $builder['range'] ?? 'melee';
        $attackMode = ($range === 'ranged') ? 'à Distância' : 'Corpo a Corpo';

        // Tipo de Ataque
        $bAttackType = $builder['attackType'] ?? 'weapon';
        $attackType = match ($bAttackType) {
            'weapon'  => "Ataque {$attackMode} com Arma",
            'spell'   => "Ataque Mágico {$attackMode}",
            'feature' => "Ataque {$attackMode} Desarmado",
            default   => "Ataque {$attackMode}",
        };

        // Bônus de Acerto: Modificador + PB + Extra
        $attackAbility = $builder['attackAbility'] ?? 'str';
        $hitTotal = $getModifier($attackAbility); 
        
        // Padrão 'true' idêntico ao normalizeAttack do JavaScript
        $hasProficiency = filter_var($builder['proficiency'] ?? true, FILTER_VALIDATE_BOOLEAN);
        if ($hasProficiency) {
            $hitTotal += $pb; 
        }
        
        $hitTotal += (int) ($builder['extraHitBonus'] ?? 0); 
        $hitFormat = $hitTotal >= 0 ? "+{$hitTotal}" : (string) $hitTotal;

        // Alcance
        $bReach = (int) ($builder['reach'] ?? 0);
        if ($range === 'ranged') {
            $reach1 = $bReach ?: 20;
            $reach2 = $bReach ?: 60;
            $reachText = "<strong>{$reach1}</strong>/<strong>{$reach2}</strong> ft";
        } else {
            $reachVal = $bReach ?: 5;
            $reachText = "<strong>{$reachVal}</strong> ft";
        }

        $targetsText = $builder['targets'] ?? 'Um alvo';

        // Dano
        $damages = $builder['damages'] ?? [];
        $damageStrings = [];

        $damageTranslations = [
            'bludgeoning' => 'Concussão', 'piercing' => 'Perfurante', 'slashing' => 'Cortante',
            'acid' => 'Ácido', 'cold' => 'Frio', 'fire' => 'Fogo', 'force' => 'Energia',
            'lightning' => 'Elétrico', 'necrotic' => 'Necrótico', 'poison' => 'Veneno',
            'psychic' => 'Psíquico', 'radiant' => 'Radiante', 'thunder' => 'Trovejante',
        ];

        foreach ($damages as $dmg) {
            $count = (int) ($dmg['count'] ?? 1);
            if ($count < 1) $count = 1;
            
            $dieStr = strtolower($dmg['die'] ?? 'd6');
            $dieSides = (int) str_replace('d', '', $dieStr);
            $dmgType = $damageTranslations[$dmg['type'] ?? 'slashing'] ?? $dmg['type'];

            $dmgMod = $getModifier($dmg['ability'] ?? 'none') + (int) ($dmg['extra'] ?? 0);

            $average = ($dieSides / 2) + 0.5;
            $totalDmg = (int) floor(($count * $average) + $dmgMod);
            
            $modText = '';
            if ($dmgMod > 0) $modText = " + <strong>{$dmgMod}</strong>";
            elseif ($dmgMod < 0) $modText = " - <strong>" . abs($dmgMod) . "</strong>";

            $damageStrings[] = "<strong>{$totalDmg}</strong> (<strong>{$count}</strong>d<strong>{$dieSides}</strong>{$modText}) {$dmgType}";
        }

        $damageText = empty($damageStrings) ? '' : implode(', mais ', $damageStrings);

        // Efeitos
        $effectsText = '';
        $effects = $builder['effects'] ?? [];
        if (!empty($effects)) {
            $parsedEffects = array_map(function($ef) {
                $text = preg_replace('/<p[^>]*>/i', '', (string) ($ef['content'] ?? ''));
                return preg_replace('/<\/p>/i', ' ', $text);
            }, $effects);
            $effectsText = ' ' . trim(implode(' ', $parsedEffects));
        }

        $attackText = "{$attackType}: <strong>{$hitFormat}</strong> Acerto, {$reachText} Alcance, {$targetsText}. Dano: {$damageText}.{$effectsText}";
        $attackText = preg_replace('/\s+/', ' ', trim($attackText));
    }

    $cleanContent = '';
    if ($mode === 'text' || $mode === 'custom' || empty($builder)) {
        if (!empty($content)) {
            $cleanContent = preg_replace('/^<p[^>]*>/i', '', $content);
            $cleanContent = preg_replace('/<\/p>$/i', '', $cleanContent);
        }
    }
@endphp

<div class="text-sm leading-relaxed mb-2 [&>p]:inline [&>div]:inline text-gray-800">

    @if($mode === 'builder' && $attackText !== '')
        <strong class="font-bold text-[#6b1d14]">{{ $title ? $title . '.' : 'Ataque.' }}</strong>
        <span class="ml-1">
            {!! $attackText !!}
        </span>
    @else
        <span class="inline">
            @if($title)
                <strong class="font-bold text-[#6b1d14] italic pr-1">{{ $title }}.</strong>
            @endif
            <span class="inline">
                {!! $cleanContent !!}
            </span>
        </span>
    @endif

</div>