<?php

namespace App\Services\Characters;

use App\Models\Character;
use App\Models\CharacterItem;

/**
 * Spellbound V66.2.112 — Ataques derivados de armas equipadas.
 * Não cria CharacterAttack: a referência é o CharacterItem e os usos
 * continuam no JSON properties, evitando cópias e ataques duplicados.
 */
class EquippedWeaponAttack
{
    public function isWeapon(CharacterItem $item): bool
    {
        $weapon = data_get($item->properties, 'weapon');
        return is_array($weapon) && in_array($weapon['category'] ?? null, ['simple', 'martial', 'custom'], true);
    }

    public function row(Character $character, CharacterItem $item): ?array
    {
        if (!$item->equipped || !$this->isWeapon($item)) {
            return null;
        }
        $weapon = (array) data_get($item->properties, 'weapon', []);
        $allowed = $this->allowedAbilities($weapon);
        $config = is_array($weapon['attack_config'] ?? null) ? $weapon['attack_config'] : [];
        // A escolha feita ao equipar é a autoridade. attack_config pode conter
        // um atributo antigo, pois não é sobrescrito pela confirmação curta.
        $choice = (string) ($weapon['attack_ability_choice'] ?? $config['attack_ability'] ?? '');
        $ability = in_array($choice, $allowed, true) ? $choice : $allowed[0];
        $modifier = $ability === 'none' ? 0 : $this->abilityModifier($character, $ability);
        $proficient = $this->isProficient($character, $item, $weapon);
        $proficiencyBonus = $this->proficiencyBonus($character);
        $magic = (int) ($weapon['magic_bonus'] ?? 0);
        $staticBonus = array_key_exists('attack_bonus', $config) ? (int) $config['attack_bonus'] : $magic;
        $attackModifier = $modifier + ($proficient ? $proficiencyBonus : 0) + $staticBonus;

        $damage = is_array($weapon['damage'] ?? null) ? $weapon['damage'] : [];
        $die = trim((string) ($damage['die'] ?? 'd4'));
        if (!preg_match('/^d(4|6|8|10|12|20|100)$/i', $die)) {
            $die = 'd4';
        }
        $count = max(1, min(30, (int) ($damage['count'] ?? 1)));
        $damageModifier = $magic + (int) ($damage['flat_bonus'] ?? 0);
        if ($this->includesAbility($damage)) {
            $damageModifier += $modifier;
        }
        $parts = [$this->damagePart('base', $count . $die, (string) ($damage['type'] ?? ''), $damageModifier)];
        $editParts = [[
            'id' => 'base', 'expression' => $count . $die,
            'type' => (string) ($damage['type'] ?? ''),
            'abilities' => $this->includesAbility($damage) && $ability !== 'none' ? [$ability] : [],
            'bonus' => $magic + (int) ($damage['flat_bonus'] ?? 0),
        ]];
        foreach ((array) ($weapon['extra_damage'] ?? []) as $index => $extra) {
            if (!is_array($extra)) continue;
            $extraDie = trim((string) ($extra['die'] ?? ''));
            if (!preg_match('/^d(4|6|8|10|12|20|100)$/i', $extraDie)) continue;
            $extraCount = max(1, min(30, (int) ($extra['count'] ?? 1)));
            $parts[] = $this->damagePart('extra-' . $index, $extraCount . $extraDie, (string) ($extra['type'] ?? ''), 0);
            $editParts[] = [
                'id' => 'extra-' . $index, 'expression' => $extraCount . $extraDie,
                'type' => (string) ($extra['type'] ?? ''), 'abilities' => [], 'bonus' => 0,
            ];
        }
        $defaultEditParts = $editParts;
        // Ajustes do mesmo editor de Ataques; a arma e seus recursos
        // permanecem no Inventário, sem criar CharacterAttack duplicado.
        if (isset($config['damage_parts']) && is_array($config['damage_parts']) && $config['damage_parts'] !== []) {
            $adjustedParts = [];
            $newEditParts = [];
            foreach (array_slice($config['damage_parts'], 0, 15) as $index => $entry) {
                if (!is_array($entry)) continue;
                $expression = trim((string) ($entry['expression'] ?? ''));
                if (!preg_match('/^\d{1,2}d(?:4|6|8|10|12|20|100)$/i', $expression)) continue;
                $selected = array_values(array_intersect(
                    array_unique(array_filter((array) ($entry['abilities'] ?? []), 'is_string')),
                    ['strength','dexterity','constitution','intelligence','wisdom','charisma']
                ));
                // Se o jogador alterou FOR/DES ao equipar, os danos
                // personalizados que usavam o atributo antigo acompanham
                // a escolha, sem modificar outras parcelas de dano.
                if ($index === 0) {
                    $previousAbility = (string) ($config['attack_ability'] ?? '');
                    if ($previousAbility !== $ability
                        && in_array($previousAbility, ['strength','dexterity'], true)
                        && in_array($previousAbility, $selected, true)) {
                        $selected = array_values(array_diff($selected, [$previousAbility]));
                        if ($ability !== 'none' && !in_array($ability, $selected, true)) {
                            $selected[] = $ability;
                        }
                    }
                }
                $bonus = max(-999, min(999, (int) ($entry['bonus'] ?? 0)));
                $total = $bonus;
                foreach ($selected as $stat) $total += $this->abilityModifier($character, $stat);
                $type = (string) ($entry['type'] ?? '');
                $id = 'configured-' . $index;
                $adjustedParts[] = $this->damagePart($id, $expression, $type, $total);
                $newEditParts[] = [
                    'id' => $id, 'expression' => $expression, 'type' => $type,
                    'abilities' => $selected, 'bonus' => $bonus,
                ];
            }
            if ($adjustedParts !== []) {
                $parts = $adjustedParts;
                $editParts = $newEditParts;
            }
        }
        $masteries = [];
        foreach ((array) ($weapon['masteries'] ?? []) as $mastery) {
            $name = trim((string) (is_array($mastery) ? ($mastery['name'] ?? $mastery['key'] ?? '') : $mastery));
            if ($name !== '') $masteries[] = $name;
        }

        if (isset($config['masteries']) && is_array($config['masteries'])) {
            $masteries = array_values(array_unique(array_filter(array_map(
                static fn ($value) => trim((string) $value), $config['masteries']
            ))));
        }
        $features = (array) data_get($item->properties, 'features', []);
        $descriptions = [];
        $trackers = [];
        $weaponFeatures = [];
        $weaponTraits = [];
        $weaponMasteryDetails = [];
        foreach ((array) ($weapon['traits'] ?? []) as $trait) {
            if (!is_array($trait)) continue;
            $name = trim((string) ($trait['name'] ?? $trait['key'] ?? ''));
            if ($name === '') continue;
            $weaponTraits[] = [
                'title' => $name,
                'description' => $this->readableText((string) ($trait['description'] ?? '')),
            ];
        }
        foreach ((array) ($weapon['masteries'] ?? []) as $mastery) {
            if (!is_array($mastery)) continue;
            $name = trim((string) ($mastery['name'] ?? $mastery['key'] ?? ''));
            if ($name === '') continue;
            $weaponMasteryDetails[] = [
                'title' => $name,
                'description' => $this->readableText((string) ($mastery['description'] ?? '')),
            ];
        }
        foreach ($features as $index => $feature) {
            if (!is_array($feature)) continue;
            $title = trim((string) ($feature['title'] ?? 'Habilidade'));
            $description = $this->readableText((string) ($feature['description'] ?? ''));
            if ($title !== '' || $description !== '') {
                $descriptions[] = $title . ($description !== '' ? ': ' . $description : '');
            }
            $usage = is_array($feature['usage'] ?? null) ? $feature['usage'] : [];
            if ($title !== '' || $description !== '') {
                $weaponFeatures[] = [
                    'title' => $title ?: 'Habilidade',
                    'description' => $description,
                    'tracker_index' => !empty($usage['enabled']) ? (int) $index : null,
                ];
            }
            if (!empty($usage['enabled'])) {
                $max = max(0, (int) ($usage['max'] ?? 0));
                $trackers[] = [
                    'index' => (int) $index,
                    'title' => $title ?: 'Habilidade',
                    'current' => max(0, min($max, (int) ($usage['current'] ?? $max))),
                    'max' => $max,
                    'recovery' => (string) ($usage['recovery'] ?? ''),
                ];
            }
        }
        $capacity = $this->loadingCapacity($weapon);
        $current = $capacity !== null
            ? max(0, min($capacity, (int) ($weapon['loading_current'] ?? $capacity)))
            : null;
        $defaultRange = $this->weaponRange($weapon);
        $range = array_key_exists('range', $config)
            ? trim((string) $config['range']) : $defaultRange;
        $extraNote = trim((string) ($config['description'] ?? ''));
        if ($extraNote !== '') {
            $weaponFeatures[] = [
                'title' => trim((string) ($config['effect'] ?? 'Observações do ataque')) ?: 'Observações do ataque',
                'description' => $extraNote, 'tracker_index' => null,
            ];
            $descriptions[] = $extraNote;
        }
        return [
            'key' => 'weapon-item-' . $item->id,
            'source' => 'weapon_item',
            'source_label' => 'Arma equipada',
            'id' => null,
            'item_id' => (int) $item->id,
            'editable' => true,
            'name' => trim((string) ($config['name'] ?? '')) ?: (string) $item->name,
            'effect' => trim((string) ($config['effect'] ?? '')),
            'description' => $extraNote,
            'allowed_abilities' => $allowed,
            'edit_damage_parts' => $editParts,
            'weapon_attack_config' => $config,
            'weapon_original_name' => (string) $item->name,
            'weapon_original_attack_bonus' => $magic,
            'weapon_original_range' => $defaultRange,
            'weapon_original_masteries' => array_values(array_filter(array_map(
                static fn ($m) => trim((string) (is_array($m) ? ($m['name'] ?? $m['key'] ?? '') : $m)),
                (array) ($weapon['masteries'] ?? [])
            ))),
            'weapon_original_damage_parts' => $defaultEditParts,
            'attack_ability' => $ability,
            'use_proficiency' => $proficient,
            'attack_bonus' => $staticBonus,
            'attack_modifier' => $attackModifier,
            'attack_expression' => '1d20' . $this->signed($attackModifier),
            'damage_parts' => $parts,
            'range' => $range,
            'masteries' => $masteries,
            'notes' => implode("\n\n", $descriptions),
            'feature_trackers' => $trackers,
            'weapon_features' => $weaponFeatures,
            'weapon_traits' => $weaponTraits,
            'weapon_mastery_details' => $weaponMasteryDetails,
            'weapon_category' => (string) ($weapon['category'] ?? ''),
            'weapon_type' => (string) ($weapon['weapon_type'] ?? ''),
            'linked_item' => true,
            'ammo' => $capacity === null ? null : [
                'capacity' => $capacity,
                'current' => $current,
            ],
            'uses_current' => null,
            'uses_max' => null,
            'recovery' => null,
            'counter_mode' => 'spend',
            'visible' => true,
            'sort_order' => 100000 + (int) $item->id,
            'data' => [],
        ];
    }

    /** A configuração da arma é a autoridade para os atributos permitidos. */
    public function allowedAbilities(array $weapon): array
    {
        $allowed = array_values(array_unique(array_filter(
            (array) ($weapon['allowed_abilities'] ?? []),
            static fn ($value) => in_array($value, ['strength', 'dexterity', 'none'], true)
        )));
        if ($allowed !== []) return $allowed;
        // Compatibilidade com armas antigas: o normalizador removia allowed_abilities.
        // Para armas inequivocamente à distância (não de arremesso), DES é o padrão.
        $traits = (array) ($weapon['traits'] ?? []);
        $isThrown = collect($traits)->contains(fn ($trait) => is_array($trait)
            && in_array(strtolower((string) ($trait['key'] ?? '')), ['thrown','arremesso'], true));
        $type = strtolower((string) ($weapon['weapon_type'] ?? ''));
        $ranged = (int) ($weapon['range_normal_ft'] ?? 0) > 0
            || preg_match('/rifle|pistola|mosquete|besta|arco|crossbow|bow|firearm/u', $type);
        if ($ranged && !$isThrown) return ['dexterity'];
        $damage = is_array($weapon['damage'] ?? null) ? $weapon['damage'] : [];
        $fixed = strtolower(trim((string) ($damage['ability'] ?? '')));
        if (in_array($fixed, ['strength', 'dexterity', 'none'], true)) return [$fixed];
        foreach ((array) ($weapon['traits'] ?? []) as $trait) {
            if (!is_array($trait)) continue;
            if (in_array(strtolower((string) ($trait['key'] ?? '')), ['finesse','acuidade'], true)) {
                return ['strength','dexterity'];
            }
        }
        return (int) ($weapon['range_normal_ft'] ?? 0) > 0 ? ['dexterity'] : ['strength'];
    }

    public function loadingCapacity(array $weapon): ?int
    {
        $capacity = (int) ($weapon['loading_capacity'] ?? 0);
        if ($capacity <= 0) return null;
        $loading = collect($weapon['traits'] ?? [])->contains(
            fn ($trait) => is_array($trait) && strtolower((string) ($trait['key'] ?? '')) === 'loading'
        );
        return $loading ? min(999999, $capacity) : null;
    }

    private function weaponRange(array $weapon): ?string
    {
        $normal = (int) ($weapon['range_normal_ft'] ?? 0);
        $long = (int) ($weapon['range_long_ft'] ?? 0);
        if ($normal > 0) return $normal . ($long > $normal ? '/' . $long : '') . ' ft.';
        $hasReach = collect($weapon['traits'] ?? [])->contains(
            fn ($trait) => is_array($trait) && strtolower((string) ($trait['key'] ?? '')) === 'reach'
        );
        $reach = $hasReach ? (int) ($weapon['reach_ft'] ?? 10) : 5;
        return max(5, $reach) . ' ft.';
    }

    private function isProficient(Character $character, CharacterItem $item, array $weapon): bool
    {
        $category = strtolower((string) ($weapon['category'] ?? ''));
        $names = $character->features()->where('type', 'weapon_training')->pluck('name')
            ->map(fn ($name) => $this->normalize((string) $name))->all();
        if (in_array('todas as armas', $names, true)) return true;
        if ($category === 'simple' && in_array('armas simples', $names, true)) return true;
        if ($category === 'martial' && in_array('armas marciais', $names, true)) return true;
        $specific = [
            $this->normalize((string) ($weapon['weapon_type'] ?? '')),
            $this->normalize((string) $item->name),
        ];
        return count(array_intersect(array_filter($specific), $names)) > 0;
    }

    /** Converte Tiptap e descrições antigas para texto seguro e legível. */
    private function readableText(string $html): string
    {
        $html = preg_replace('~<(?:br|/p|/div|/li|/h[1-6])\b[^>]*>~iu', "\n", $html) ?? $html;
        $html = preg_replace('~<li\b[^>]*>~iu', '• ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/ *\n */u', "\n", $text) ?? $text;
        return trim(preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text);
    }

    private function normalize(string $value): string
    {
        $value = trim($value);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = strtolower($ascii ?: $value);
        return trim(preg_replace('/\s+/u', ' ', preg_replace('/[^a-z0-9 ]/i', ' ', $value)));
    }

    private function proficiencyBonus(Character $character): int
    {
        if ($character->proficiency_bonus !== null) return (int) $character->proficiency_bonus;
        return min(6, 2 + intdiv(max(1, (int) ($character->level ?? 1)) - 1, 4));
    }

    private function abilityModifier(Character $character, string $key): int
    {
        $abilities = $character->abilities;
        $overrides = (array) ($abilities?->overrides ?? []);
        $temporary = (array) ($abilities?->temporary_bonuses ?? []);
        $score = array_key_exists($key, $overrides) && $overrides[$key] !== null && $overrides[$key] !== ''
            ? (int) $overrides[$key]
            : (int) ($abilities?->{$key} ?? 10) + (int) ($temporary[$key] ?? 0);
        return (int) floor(($score - 10) / 2);
    }

    private function includesAbility(array $damage): bool
    {
        if (array_key_exists('include_ability', $damage)) return (bool) $damage['include_ability'];
        return true;
    }

    private function damagePart(string $id, string $expression, string $type, int $modifier): array
    {
        $translated = [
            'cortante' => 'Cortante', 'slashing' => 'Cortante',
            'perfurante' => 'Perfurante', 'piercing' => 'Perfurante',
            'impacto' => 'Impacto', 'bludgeoning' => 'Impacto',
        ];
        return [
            'id' => $id,
            'expression' => $expression,
            'type' => $type,
            'type_label' => $translated[strtolower(trim($type))] ?? ($type ?: 'Dano'),
            'abilities' => [],
            'bonus' => $modifier,
            'modifier' => $modifier,
            'roll_expression' => $expression . ($modifier !== 0 ? $this->signed($modifier) : ''),
        ];
    }

    private function signed(int $number): string
    {
        return $number >= 0 ? '+' . $number : (string) $number;
    }
}
