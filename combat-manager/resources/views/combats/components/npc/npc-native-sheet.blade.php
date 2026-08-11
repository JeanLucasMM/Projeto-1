@php
    use App\Support\Dictionaries\AbilityNames;
    use App\Support\Dictionaries\Alignments;
    use App\Support\Dictionaries\Conditions;
    use App\Support\Dictionaries\DamageTypes;
    use App\Support\Dictionaries\Languages;
    use App\Support\Dictionaries\NpcSizes;
    use App\Support\Dictionaries\NpcTypes;
    use App\Support\Dictionaries\Senses;
    use App\Support\Dictionaries\SkillNames;
    use App\ViewModels\TrackerViewModel;

    $npcModel = $combatNpc->npc ?? null;
    $json = $npcModel ? ($npcModel->json_data ?? []) : [];

    // Nome global da criatura
    $npcName = 'A criatura';
    if (!empty($json['header']['name'])) {
        $npcName = $json['header']['name'];
    } elseif (!empty($npcModel->name)) {
        $npcName = $npcModel->name;
    }

    // Nível de Desafio (CR / ND)
    $crStr = $json['header']['challengeRating'] ?? $json['header']['cr'] ?? $json['header']['nd'] ?? '1';
    $crNum = 1;
    if (strpos($crStr, '/') !== false) {
        $parts = explode('/', $crStr);
        $crNum = intval($parts[0]) / max(1, intval($parts[1]));
    } else {
        $crNum = floatval($crStr);
    }

    // Bônus de Proficiência baseado no CR
    $profBonus = 2;
    if ($crNum >= 17) $profBonus = 6;
    elseif ($crNum >= 13) $profBonus = 5;
    elseif ($crNum >= 9) $profBonus = 4;
    elseif ($crNum >= 5) $profBonus = 3;
    else $profBonus = 2;

    if (isset($json['header']['proficiencyBonus']) && is_numeric($json['header']['proficiencyBonus'])) {
        $profBonus = (int) $json['header']['proficiencyBonus'];
    }

    // Grade de Atributos do Builder
    $rawAbilities = $json['abilities'] ?? [];
    $abilitiesMap = [
        'str' => ['name' => AbilityNames::label('str'), 'val' => $rawAbilities['str'] ?? 10],
        'dex' => ['name' => AbilityNames::label('dex'), 'val' => $rawAbilities['dex'] ?? 10],
        'con' => ['name' => AbilityNames::label('con'), 'val' => $rawAbilities['con'] ?? 10],
        'int' => ['name' => AbilityNames::label('int'), 'val' => $rawAbilities['int'] ?? 10],
        'wis' => ['name' => AbilityNames::label('wis'), 'val' => $rawAbilities['wis'] ?? 10],
        'cha' => ['name' => AbilityNames::label('cha'), 'val' => $rawAbilities['cha'] ?? 10],
    ];

    // Normaliza saving throws
    $saves = collect($combatNpc->viewModel->savingThrows ?? [])->keyBy(function($item) {
        return is_array($item) ? ($item['ability'] ?? '') : ($item->ability ?? '');
    });

    $formattedAbilities = [];
    foreach ($abilitiesMap as $key => $data) {
        $val = $data['val'];
        $mod = floor(($val - 10) / 2);
        
        $saveBonus = $mod;
        if (isset($json['savingThrows'][$key])) {
            $st = $json['savingThrows'][$key];
            if (!empty($st['proficient'])) {
                $saveBonus = $mod + $profBonus + ($st['bonus'] ?? 0);
            } else {
                $saveBonus = $mod + ($st['bonus'] ?? 0);
            }
        }

        $formattedAbilities[] = (object)[
            'name' => $data['name'],
            'value' => $val,
            'modifier' => $mod,
            'save' => $saveBonus
        ];
    }
    $columns = [
        [$formattedAbilities[0] ?? null, $formattedAbilities[3] ?? null],
        [$formattedAbilities[1] ?? null, $formattedAbilities[4] ?? null],
        [$formattedAbilities[2] ?? null, $formattedAbilities[5] ?? null],
    ];
@endphp

{{-- Grade de Atributos --}}
<div class="ability-groups grid grid-cols-1 md:grid-cols-3 gap-2 w-full my-1.5 text-xs">
    @foreach($columns as $column)
        <div class="ability-group flex-1">
            <div class="ability-header text-[10px] text-black/60 font-bold px-1">
                <div></div>
                <div>MOD</div>
                <div>SALV</div>
            </div>
            
            @foreach($column as $ability)
                @if($ability)
                    <div class="ability-row py-0.5 px-1 text-xs">
                        <div class="ability-name">
                            {{ strtoupper(substr($ability->name, 0, 3)) }}
                            <span class="text-[11px]">{{ $ability->value }}</span>
                        </div>
                        <div class="text-[11px]">
                            {{ $ability->modifier >= 0 ? '+' : '' }}{{ $ability->modifier }}
                        </div>
                        <div class="text-[11px]">
                            {{ $ability->save >= 0 ? '+' : '' }}{{ $ability->save }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>

{{-- Perícias, Sentidos, Resistências, ND e Proficiência --}}
<div class="details w-full space-y-0.5 text-xs my-1.5">
    @php
        $skillsData = $json['skills'] ?? [];
        $activeSkills = collect($skillsData)->filter(function($skill) {
            $skillKey = strtolower($skill['key'] ?? '');
            if ($skillKey === 'initiative' || $skillKey === 'iniciativa') return false;
            return ($skill['enabled'] ?? false) || ($skill['proficient'] ?? false) || ($skill['expertise'] ?? false) || (($skill['bonus'] ?? 0) != 0);
        });

        $skillsString = $activeSkills->map(function($s) use ($rawAbilities, $profBonus) {
            $abilityKey = $s['ability'] ?? 'str';
            $abilityVal = $rawAbilities[$abilityKey] ?? 10;
            $abilityMod = floor(($abilityVal - 10) / 2);

            $bonus = $abilityMod;
            if (!empty($s['expertise'])) {
                $bonus += ($profBonus * 2);
            } elseif (!empty($s['proficient']) || !empty($s['enabled'])) {
                $bonus += $profBonus;
            }
            $bonus += ($s['bonus'] ?? 0);

            $skillKey = $s['key'] ?? '';
            $skillLabel = SkillNames::label($skillKey);

            return "{$skillLabel} " . ($bonus >= 0 ? '+' : '') . $bonus;
        })->join(', ');

        // Cálculo da Percepção Passiva (Incluindo passivePerceptionBonus)
        $wisVal = $rawAbilities['wis'] ?? 10;
        $wisMod = (int) floor(($wisVal - 10) / 2);
        
        $perceptionSkill = collect($skillsData)->first(function($s) {
            return in_array(strtolower($s['key'] ?? ''), ['perception', 'percepcao', 'percepção']);
        });

        $perceptionBonus = $wisMod;
        if ($perceptionSkill) {
            if (!empty($perceptionSkill['expertise'])) {
                $perceptionBonus += ($profBonus * 2);
            } elseif (!empty($perceptionSkill['proficient']) || !empty($perceptionSkill['enabled'])) {
                $perceptionBonus += $profBonus;
            }
            $perceptionBonus += ($perceptionSkill['bonus'] ?? 0);
        }

        $passivePerceptionBonus = (int) ($json['combat']['passivePerceptionBonus'] ?? $json['combat']['passive_perception_bonus'] ?? 0);

        $passivePerception = $json['combat']['passivePerception'] ?? $json['combat']['passive_perception'] ?? (10 + $perceptionBonus + $passivePerceptionBonus);

        // Sentidos + Percepção Passiva
        $combatData = $json['combat'] ?? [];
        $sensesArr = $combatData['senses'] ?? [];
        $formattedSenses = [];
        foreach($sensesArr as $senseKey => $senseVal) {
            if ($senseVal > 0) {
                $translateSense = Senses::label($senseKey);
                $formattedSenses[] = "{$translateSense} {$senseVal} ft";
            }
        }
        $formattedSenses[] = "Percepção Passiva {$passivePerception}";
        $sensesString = implode(', ', $formattedSenses);

        $translateList = fn($items, $dict) => collect($items)->map(fn($item) => $dict::label($item))->implode(', ');

        $extras = [
            'Resistências a Dano' => $translateList($combatData['resistances'] ?? [], DamageTypes::class),
            'Imunidades a Dano' => $translateList($combatData['immunities'] ?? [], DamageTypes::class),
            'Imunidades a Condições' => $translateList($combatData['conditionImmunities'] ?? [], Conditions::class),
            'Vulnerabilidades a Dano' => $translateList($combatData['vulnerabilities'] ?? [], DamageTypes::class),
            'Sentidos' => $sensesString,
            'Idiomas' => $translateList($json['header']['languages'] ?? [], Languages::class),
            'Nível de Desafio' => "{$crStr} (Bônus de Proficiência +{$profBonus})",
        ];
    @endphp

    @if(!empty($skillsString))
        <div class="detail-row">
            <strong>Perícias:</strong>
            {{ $skillsString }}
        </div>
    @endif

    @foreach($extras as $label => $value)
        @if($value && $value !== '-' && (!is_array($value) || count($value) > 0))
            <div class="detail-row">
                <strong>{{ $label }}:</strong> 
                {{ is_array($value) ? implode(', ', $value) : $value }}
            </div>
        @endif
    @endforeach
</div>

<div class="stat-divider my-1.5"></div>

{{-- Fluxo de Ações do Builder --}}
<div class="content-flow w-full space-y-2 text-xs">
    @php
        $builtSections = [];
        
        if (!empty($json['features'])) {
            $builtSections[] = (object)[
                'title' => 'Habilidades',
                'items' => collect($json['features'])->map(fn($a) => (object)[
                    'title' => $a['title'] ?? '', 
                    'text' => $a['content'] ?? '',
                    'tracker' => $a['tracker'] ?? null,
                    'type' => $a['type'] ?? 'normal',
                    'spellcasting' => $a['spellcasting'] ?? []
                ])->all()
            ];
        }
        
        $actionItems = [];
        
        // 1. Processar Multiataques
        if (!empty($json['multiAttacks'])) {
            $numeros = [
                1 => 'um', 2 => 'dois', 3 => 'três', 4 => 'quatro', 5 => 'cinco',
                6 => 'seis', 7 => 'sete', 8 => 'oito', 9 => 'nove', 10 => 'dez',
            ];

            $attackMap = [];
            if (!empty($json['attacks'])) {
                foreach ($json['attacks'] as $attack) {
                    $attackArr = is_object($attack) ? json_decode(json_encode($attack), true) : $attack;
                    $id = (string) ($attackArr['id'] ?? '');
                    if ($id !== '') $attackMap[$id] = $attackArr;
                }
            }

            $actionMap = [];
            if (!empty($json['actions'])) {
                foreach ($json['actions'] as $action) {
                    $actionArr = is_object($action) ? json_decode(json_encode($action), true) : $action;
                    $id = (string) ($actionArr['id'] ?? '');
                    if ($id !== '') $actionMap[$id] = $actionArr;
                }
            }

            foreach ($json['multiAttacks'] as $ma) {
                $maTitle = trim((string) ($ma['title'] ?? 'Multiataque'));
                if ($maTitle !== '' && !str_ends_with($maTitle, '.')) $maTitle .= '.';
                
                $maMode = $ma['mode'] ?? 'automatic';
                $customText = trim((string) ($ma['customText'] ?? ''));
                $entries = $ma['entries'] ?? [];

                $multiAttackParts = [];
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        if (is_object($entry)) $entry = (array) $entry;
                        if (!is_array($entry)) continue;

                        $source = $entry['source'] ?? 'attack';
                        $sourceId = (string) ($entry['sourceId'] ?? '');
                        $quantity = max(1, (int) ($entry['quantity'] ?? 1));
                        $extenso = $numeros[$quantity] ?? $quantity;

                        if ($source === 'attack' && isset($attackMap[$sourceId])) {
                            $attackItem = $attackMap[$sourceId];
                            $nome = trim((string) ($attackItem['title'] ?? $attackItem['name'] ?? 'Ataque sem nome'));
                            $multiAttackParts[] = $quantity <= 1 ? "um ataque usando {$nome}" : "{$extenso} ({$quantity}) ataques usando {$nome}";
                        } 
                        elseif ($source === 'action' && isset($actionMap[$sourceId])) {
                            $actionItem = $actionMap[$sourceId];
                            $nome = trim((string) ($actionItem['title'] ?? $actionItem['name'] ?? 'Ação sem nome'));
                            $multiAttackParts[] = $quantity <= 1 ? "a ação {$nome}" : "{$extenso} ({$quantity}) utilizações da ação {$nome}";
                        }
                    }
                }

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

                $finalText = ($maMode === 'custom' || $maMode === 'text') ? $customText : $automaticText;
                
                $actionItems[] = (object)[
                    'title' => $maTitle, 
                    'text' => $finalText,
                    'isMultiAttack' => true,
                    'tracker' => $ma['tracker'] ?? null,
                    'type' => 'normal',
                    'spellcasting' => []
                ];
            }
        }

        // 2. Processar Ataques do Construtor
        if (!empty($json['attacks'])) {
            foreach ($json['attacks'] as $att) {
                $attTitle = trim((string) ($att['title'] ?? 'Ataque'));
                if ($attTitle !== '' && !str_ends_with($attTitle, '.')) $attTitle .= '.';

                $builderData = $att['builder'] ?? [];
                $range = $builderData['range'] ?? 'melee';
                $attackMode = ($range === 'ranged') ? 'à Distância' : 'Corpo a Corpo';
                $bAttackType = $builderData['attackType'] ?? 'weapon';
                $attackType = match ($bAttackType) {
                    'weapon'  => "Ataque {$attackMode} com Arma",
                    'spell'   => "Ataque Mágico {$attackMode}",
                    'feature' => "Ataque {$attackMode} Desarmado",
                    default   => "Ataque {$attackMode}",
                };

                $getModifier = function($abilityKey) use ($json) {
                    $key = strtolower($abilityKey);
                    if ($key === 'none' || !isset($json['abilities'])) return 0;
                    $value = $json['abilities'][$key] ?? 10;
                    return (int) floor(($value - 10) / 2);
                };

                $attackAbility = $builderData['attackAbility'] ?? 'str';
                $hitTotal = $getModifier($attackAbility); 
                
                if (filter_var($builderData['proficiency'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
                    $hitTotal += $profBonus; 
                }
                $hitTotal += (int) ($builderData['extraHitBonus'] ?? 0); 
                $hitFormat = $hitTotal >= 0 ? "+{$hitTotal}" : (string) $hitTotal;

                $bReach = (int) ($builderData['reach'] ?? 0);
                if ($range === 'ranged') {
                    $reach1 = $bReach ?: 20;
                    $reach2 = $bReach ?: 60;
                    $reachText = "<strong>{$reach1}</strong>/<strong>{$reach2}</strong> ft";
                } else {
                    $reachVal = $bReach ?: 5;
                    $reachText = "<strong>{$reachVal}</strong> ft";
                }

                $targetsText = $builderData['targets'] ?? 'Um alvo';
                $damages = $builderData['damages'] ?? [];
                $damageStrings = [];

                $damageTranslations = [
                    'bludgeoning' => 'Concussão', 'piercing' => 'Perfurante', 'slashing' => 'Cortante',
                    'acid' => 'Ácido', 'cold' => 'Frio', 'fire' => 'Fogo', 'force' => 'Energia',
                    'lightning' => 'Elétrico', 'necrotic' => 'Necrótico', 'poison' => 'Veneno',
                    'psychic' => 'Psíquico', 'radiant' => 'Radiante', 'thunder' => 'Trovejante',
                ];

                foreach ($damages as $dmg) {
                    $count = max(1, (int) ($dmg['count'] ?? 1));
                    $dieSides = (int) str_replace('d', '', strtolower($dmg['die'] ?? 'd6'));
                    $dmgType = $damageTranslations[$dmg['type'] ?? 'slashing'] ?? $dmg['type'];
                    $dmgMod = $getModifier($dmg['ability'] ?? 'none') + (int) ($dmg['extra'] ?? 0);
                    
                    $average = ($dieSides / 2) + 0.5;
                    $totalDmg = (int) floor(($count * $average) + $dmgMod);
                    
                    $modText = $dmgMod > 0 ? " + <strong>{$dmgMod}</strong>" : ($dmgMod < 0 ? " - <strong>" . abs($dmgMod) . "</strong>" : '');
                    $damageStrings[] = "<strong>{$totalDmg}</strong> (<strong>{$count}</strong>d<strong>{$dieSides}</strong>{$modText}) {$dmgType}";
                }

                $damageText = empty($damageStrings) ? '' : implode(', mais ', $damageStrings);

                $effectsText = '';
                $effects = $builderData['effects'] ?? [];
                if (!empty($effects)) {
                    $parsedEffects = array_map(fn($ef) => preg_replace('/<\/p>/i', ' ', preg_replace('/<p[^>]*>/i', '', (string) ($ef['content'] ?? ''))), $effects);
                    $effectsText = ' ' . trim(implode(' ', $parsedEffects));
                }

                $attackTextContent = preg_replace('/\s+/', ' ', trim("{$attackType}: <strong>{$hitFormat}</strong> Acerto, {$reachText} Alcance, {$targetsText}. Dano: {$damageText}.{$effectsText}"));

                $actionItems[] = (object)[
                    'title' => $attTitle, 
                    'text' => $attackTextContent,
                    'tracker' => $att['tracker'] ?? null,
                    'type' => 'normal',
                    'spellcasting' => []
                ];
            }
        }

        // 3. Processar Ações Comuns
        if (!empty($json['actions'])) {
            foreach($json['actions'] as $a) {
                $aTitle = trim((string) ($a['title'] ?? 'Ação'));
                if ($aTitle !== '' && !str_ends_with($aTitle, '.')) $aTitle .= '.';
                $actionItems[] = (object)[
                    'title' => $aTitle, 
                    'text' => $a['content'] ?? '',
                    'tracker' => $a['tracker'] ?? null,
                    'type' => $a['type'] ?? 'normal',
                    'spellcasting' => $a['spellcasting'] ?? []
                ];
            }
        }

        if (!empty($actionItems)) {
            $builtSections[] = (object)[
                'title' => 'Ações',
                'items' => $actionItems
            ];
        }

        $mapSection = fn($key, $title) => !empty($json[$key]) ? (object)[
            'title' => $title,
            'items' => collect($json[$key])->map(function($a) {
                $t = trim((string) ($a['title'] ?? ''));
                if ($t !== '' && !str_ends_with($t, '.')) $t .= '.';
                return (object)[
                    'title' => $t, 
                    'text' => $a['content'] ?? '',
                    'tracker' => $a['tracker'] ?? null,
                    'type' => $a['type'] ?? 'normal',
                    'spellcasting' => $a['spellcasting'] ?? []
                ];
            })->all()
        ] : null;

        if ($bActions = $mapSection('bonusActions', 'Ações Bônus')) $builtSections[] = $bActions;
        if ($reActions = $mapSection('reactions', 'Reações')) $builtSections[] = $reActions;
        if ($legActions = $mapSection('legendaryActions', 'Ações Lendárias')) $builtSections[] = $legActions;
        if ($lairActs = $mapSection('lairActions', 'Ações de Covil')) $builtSections[] = $lairActs;

        $sections = $builtSections;
    @endphp

    @foreach($sections as $section)
        <div class="section w-full">
            @php
                $secTitle = is_array($section) ? ($section['title'] ?? '') : ($section->title ?? '');
            @endphp
            <h3 class="section-title text-sm font-bold text-[#5a1810] border-b border-[#5a1810]/30 pb-0.5 mb-1">{{ $secTitle }}</h3>
            
            @foreach((is_array($section) ? ($section['items'] ?? []) : ($section->items ?? [])) as $item)
                @php
                    $itemTitle = is_array($item) ? ($item['title'] ?? '') : ($item->title ?? '');
                    $itemText = is_array($item) ? ($item['text'] ?? '') : ($item->text ?? '');
                    $isMultiAttackItem = is_array($item) ? ($item['isMultiAttack'] ?? false) : ($item->isMultiAttack ?? false);
                    $trackerConfig = is_array($item) ? ($item['tracker'] ?? null) : ($item->tracker ?? null);
                    
                    $itemType = is_array($item) ? ($item['type'] ?? 'normal') : ($item->type ?? 'normal');
                    $itemSpellcasting = is_array($item) ? ($item['spellcasting'] ?? []) : ($item->spellcasting ?? []);
                    $isSpellcasting = $itemType === 'spellcasting';
                    
                    $spellcastingIntro = '';
                    if ($isSpellcasting) {
                        $abilityMap = ['str'=>'Força','dex'=>'Destreza','con'=>'Constituição','int'=>'Inteligência','wis'=>'Sabedoria','cha'=>'Carisma'];
                        $casterLevel = (int) ($itemSpellcasting['casterLevel'] ?? 1);
                        $abilityKey = strtolower($itemSpellcasting['ability'] ?? 'cha');
                        $abilityName = $abilityMap[$abilityKey] ?? 'Carisma';

                        $mod = 0;
                        $val = $json['abilities'][$abilityKey] ?? 10;
                        $mod = (int) floor(($val - 10) / 2);

                        $atkExtra = (int) ($itemSpellcasting['attackBonusExtra'] ?? 0);
                        $dcExtra = (int) ($itemSpellcasting['saveDCExtra'] ?? 0);
                        $spellAttack = $profBonus + $mod + $atkExtra;
                        $spellDC = 8 + $profBonus + $mod + $dcExtra;
                        $spellAttackFormat = $spellAttack >= 0 ? "+{$spellAttack}" : (string) $spellAttack;

                        $spellcastingIntro = "{$npcName} é um conjurador de {$casterLevel}º Nível, usando {$abilityName} como canalização. ({$spellAttackFormat} Acerto, CD {$spellDC}).";
                    }

                    $rawTitle = ltrim(rtrim($itemTitle, '. :'), '. :');
                    $cleanTitle = $rawTitle;
                    if ($cleanTitle !== '' && !str_ends_with($cleanTitle, '.')) $cleanTitle .= '.';

                    $trackerVm = null;
                    $maxUses = 0;
                    $trackerLabel = '';

                    if (is_array($trackerConfig) && !empty($trackerConfig['enabled'])) {
                        $uses = (int) ($trackerConfig['uses'] ?? 0);
                        $maxConfig = (int) ($trackerConfig['max'] ?? 0);
                        $reset = $trackerConfig['reset'] ?? '';

                        if ($reset === 'recharge') {
                            $maxUses = $maxConfig > 0 ? $maxConfig : 6;
                        } else {
                            $maxUses = $uses > 0 ? $uses : $maxConfig;
                        }
                        if ($maxUses <= 0) $maxUses = 1;

                        $unitLabel = match ($reset) {
                            'day' => 'Dia',
                            'short_rest' => 'Des. Curto',
                            'long_rest' => 'Des. Longo',
                            'turn' => 'Turno',
                            'recharge' => 'Recarga ' . ($trackerConfig['min'] ?? 4) . '–' . ($trackerConfig['max'] ?? 6),
                            'custom' => trim((string) ($trackerConfig['customReset'] ?? '')),
                            default => '',
                        };

                        $unitLabel = preg_replace('/^[\d\/\s\(\)]+/', '', $unitLabel);
                        if (is_numeric($unitLabel)) $unitLabel = '';

                        $currentUses = method_exists($combatNpc, 'getResource') ? $combatNpc->getResource(rtrim($cleanTitle, '.'), $maxUses) : $maxUses;

                        $trackerVm = new TrackerViewModel(
                            type: $reset !== '' ? $reset : 'counter',
                            resourceKey: rtrim($cleanTitle, '.'),
                            current: $currentUses,
                            max: $maxUses,
                            label: $unitLabel !== '' ? $unitLabel : null
                        );
                    } else {
                        if (preg_match('/^(.*?)_{3,}\s*(.*)$/', $rawTitle, $matches)) {
                            $titleName = rtrim($matches[1]);
                            $usageText = trim($matches[2], "() :."); 
                            if (strpos($usageText, '/') !== false) {
                                $parts = explode('/', $usageText);
                                $maxUses = $parts[0] === '' ? (isset($parts[1]) ? intval($parts[1]) : 0) : intval($parts[0]);
                                $trackerLabel = isset($parts[1]) ? trim($parts[1]) : '';
                                $cleanTitle = $titleName;
                                if ($cleanTitle !== '' && !str_ends_with($cleanTitle, '.')) $cleanTitle .= '.';
                            } else {
                                if (preg_match('/^(\d+)(.*)$/', $usageText, $subMatches)) {
                                    $maxUses = intval($subMatches[1]);
                                    $trackerLabel = trim($subMatches[2]);
                                    $cleanTitle = $titleName;
                                    if ($cleanTitle !== '' && !str_ends_with($cleanTitle, '.')) $cleanTitle .= '.';
                                }
                            }
                        } 
                        elseif (preg_match('/^(.*?)(?:\s*\(\s*|\s+)(?:(\d+)\/)?\/(\d+)\s*([a-zA-Zà-úÀ-Ú\s]*)\)?$/iu', $rawTitle, $matches)) {
                            $cleanTitle = rtrim($matches[1], " (");
                            if ($cleanTitle !== '' && !str_ends_with($cleanTitle, '.')) $cleanTitle .= '.';
                            $maxUses = intval($matches[3]);
                            $trackerLabel = trim($matches[4]);
                        } 
                        elseif (preg_match('/^(.*?)(?:\s*\(\s*|\s+)(\d+)\/([a-zA-Zà-úÀ-Ú\s]*)\)?$/iu', $rawTitle, $matches)) {
                            $cleanTitle = rtrim($matches[1], " (");
                            if ($cleanTitle !== '' && !str_ends_with($cleanTitle, '.')) $cleanTitle .= '.';
                            $maxUses = intval($matches[2]);
                            $trackerLabel = trim($matches[3]);
                        }

                        $trackerLabel = preg_replace('/^[\d\/\s\(\)]+/', '', $trackerLabel);
                        if (is_numeric($trackerLabel)) $trackerLabel = '';

                        if ($maxUses > 0) {
                            $currentUses = method_exists($combatNpc, 'getResource') ? $combatNpc->getResource(rtrim($cleanTitle, '.'), $maxUses) : $maxUses;
                            $trackerVm = new TrackerViewModel(
                                type: 'counter',
                                resourceKey: rtrim($cleanTitle, '.'),
                                current: $currentUses,
                                max: $maxUses,
                                label: $trackerLabel !== '' ? $trackerLabel : null
                            );
                        }
                    }
                @endphp
                <div class="feature w-full mb-1.5 leading-normal">
                    @if($isMultiAttackItem)
                        <div class="text-sm font-bold italic text-[#000000e6] leading-tight">
                            <span>
                                {{ $cleanTitle ?: 'Multiataque.' }}
                            </span>
                            @if($itemText !== '')
                                <span class="ml-1">
                                    {!! $itemText !!}
                                </span>
                            @endif
                        </div>
                    @else
                        @if(!empty($cleanTitle))
                            <strong class="feature-title font-semibold">
                                {!! $cleanTitle !!}@if($trackerVm)<span 
                                    x-data="{ 
                                        current: {{ $trackerVm->current }}, 
                                        max: {{ $trackerVm->max }}, 
                                        label: '{{ $trackerVm->label ?? '' }}',
                                        npcId: '{{ $combatNpc->id }}',
                                        featureTitle: '{{ addslashes($trackerVm->resourceKey) }}',
                                        async saveData() {
                                            try {
                                                await fetch('/combat/npc/update-resource', {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                    body: JSON.stringify({ combat_npc_id: this.npcId, feature_title: this.featureTitle, current_uses: this.current }),
                                                    keepalive: true
                                                });
                                            } catch (e) {
                                                console.error(e);
                                            }
                                        }
                                    }" 
                                    x-init="$watch('current', value => { saveData(); });"
                                    @visibilitychange.window="if (document.visibilityState === 'hidden') saveData()"
                                    @beforeunload.window="saveData()"
                                    class="inline-flex items-center ml-1 px-1.5 py-0.2 bg-[#5a1810] text-[#f4f1e8] rounded border border-black/20 font-sans tracking-normal not-italic select-none"
                                    style="display: inline-flex; font-style: normal; vertical-align: middle; white-space: nowrap; font-size: 10px; font-weight: bold;"
                                >
                                    <button type="button" @click="if(current > 0) current--" class="hover:text-white/70 active:text-white/40 transition font-bold text-xs focus:outline-none cursor-pointer border-none bg-transparent p-0 m-0 text-[#f4f1e8]">-</button>
                                    <span class="mx-1 min-w-[24px] text-center" x-text="label && label.startsWith('Recarga') ? '(' + label + ')' : '(' + current + '/' + max + (label ? (['Dia', 'Des. Curto', 'Des. Longo', 'Turno'].includes(label) ? '' : ' ') + label : '') + ')'"></span>
                                    <button type="button" @click="if(current < max) current++" class="hover:text-white/70 active:text-white/40 transition font-bold text-xs focus:outline-none cursor-pointer border-none bg-transparent p-0 m-0 text-[#f4f1e8]">+</button>
                                </span>@endif
                            </strong>
                        @endif
                        @if($isSpellcasting && !empty($spellcastingIntro))
                            <span class="spellcasting-intro">{{ $spellcastingIntro }}</span>
                        @endif
                        @if($itemText !== '')
                            <span class="feature-content">{!! $itemText !!}</span>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>