<?php

namespace App\Services\Characters;

use App\Models\Character;

class CharacterArmorClassService
{
    public function calculate(Character $character): array
    {
        $character->loadMissing(['abilities', 'combat', 'items']);

        $scores = $this->effectiveAbilityScores($character);
        $overrides = $this->normalizedArray($character->combat?->overrides ?? []);
        $armorConfig = data_get($overrides, 'armor', []);
        $armorConfig = is_array($armorConfig) ? $armorConfig : [];

        $storedMode = $armorConfig['mode'] ?? ['dex'];
        $armorMode = is_array($storedMode)
            ? array_values($storedMode)
            : [$storedMode];

        if ($armorMode === [] && !array_key_exists('mode', $armorConfig)) {
            $armorMode = ['dex'];
        }

        $namedBonusTotal = (int) collect($armorConfig['bonuses'] ?? [])
            ->sum(fn ($bonus) => (int) ($bonus['value'] ?? 0));

        $baseBodyAc = 10 + (int) collect($armorMode)
            ->sum(fn ($ability) => $this->abilityModifier($scores, (string) $ability));

        $activeArmor = $character->items
            ->filter(fn ($item) =>
                (bool) $item->equipped
                && is_array(data_get($item->properties, 'armor'))
            )
            ->map(function ($item) use ($scores) {
                $armor = data_get($item->properties, 'armor', []);

                return [
                    'name' => (string) $item->name,
                    'body_ac' => $this->armorBodyAc(
                        is_array($armor) ? $armor : [],
                        $scores
                    ),
                ];
            })
            ->sortByDesc('body_ac')
            ->first();

        $bodyAc = $activeArmor
            ? (int) $activeArmor['body_ac']
            : $baseBodyAc;

        $shieldBonus = (int) $character->items
            ->filter(fn ($item) =>
                (bool) $item->equipped
                && is_array(data_get($item->properties, 'shield'))
            )
            ->sum(function ($item) {
                $shield = data_get($item->properties, 'shield', []);

                if (!is_array($shield)) {
                    return 0;
                }

                return (int) ($shield['ac_bonus'] ?? 2)
                    + (int) ($shield['magic_bonus'] ?? 0);
            });

        return [
            'total' => $bodyAc + $shieldBonus + $namedBonusTotal,
            'body' => $bodyAc,
            'shield_bonus' => $shieldBonus,
            'named_bonus' => $namedBonusTotal,
            'source' => $activeArmor['name'] ?? 'Defesa Base',
        ];
    }

    private function effectiveAbilityScores(Character $character): array
    {
        $abilities = $character->abilities;
        $temporary = is_array($abilities?->temporary_bonuses)
            ? $abilities->temporary_bonuses
            : [];
        $overrides = is_array($abilities?->overrides)
            ? $abilities->overrides
            : [];

        $map = [
            'str' => 'strength',
            'dex' => 'dexterity',
            'con' => 'constitution',
            'int' => 'intelligence',
            'wis' => 'wisdom',
            'cha' => 'charisma',
        ];

        $scores = [];

        foreach ($map as $short => $full) {
            $base = (int) ($abilities?->{$full} ?? 10);
            $bonus = (int) ($temporary[$full] ?? $temporary[$short] ?? 0);

            $hasFullOverride = array_key_exists($full, $overrides)
                && $overrides[$full] !== null
                && $overrides[$full] !== '';

            $hasShortOverride = array_key_exists($short, $overrides)
                && $overrides[$short] !== null
                && $overrides[$short] !== '';

            if ($hasFullOverride) {
                $effective = (int) $overrides[$full];
            } elseif ($hasShortOverride) {
                $effective = (int) $overrides[$short];
            } else {
                $effective = $base + $bonus;
            }

            $scores[$short] = max(1, $effective);
        }

        return $scores;
    }

    private function armorBodyAc(array $armor, array $scores): int
    {
        $baseAc = (int) ($armor['base_ac'] ?? 10);
        $magicBonus = (int) ($armor['magic_bonus'] ?? 0);

        $dexterityMode = $armor['dexterity_mode'] ?? match ($armor['category'] ?? null) {
            'light' => 'full',
            'medium' => 'capped',
            default => 'none',
        };

        $dexterity = $this->abilityModifier($scores, 'dex');

        $dexterityContribution = match ($dexterityMode) {
            'full' => $dexterity,
            'capped' => min(
                $dexterity,
                max(0, (int) ($armor['dexterity_cap'] ?? 2))
            ),
            default => 0,
        };

        $extraAbilityBonus = (int) collect(
            is_array($armor['ability_modifiers'] ?? null)
                ? $armor['ability_modifiers']
                : []
        )
            ->filter(fn ($ability) => in_array(
                $ability,
                [
                    'strength', 'constitution', 'intelligence',
                    'wisdom', 'charisma', 'str', 'con', 'int', 'wis', 'cha',
                ],
                true
            ))
            ->sum(fn ($ability) => $this->abilityModifier($scores, (string) $ability));

        return $baseAc + $dexterityContribution + $extraAbilityBonus + $magicBonus;
    }

    private function abilityModifier(array $scores, string $ability): int
    {
        $map = [
            'strength' => 'str',
            'dexterity' => 'dex',
            'constitution' => 'con',
            'intelligence' => 'int',
            'wisdom' => 'wis',
            'charisma' => 'cha',
        ];

        $key = $map[strtolower($ability)] ?? strtolower($ability);
        $score = (int) ($scores[$key] ?? 10);

        return (int) floor(($score - 10) / 2);
    }

    private function normalizedArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}