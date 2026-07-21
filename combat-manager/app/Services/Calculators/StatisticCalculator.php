<?php

namespace App\Services\Calculators;

class StatisticCalculator
{
    public function abilityModifier(int $value): int
    {
        return (int) floor(($value - 10) / 2);
    }

    public function proficiencyBonus(float|int $cr): int
    {
        return match (true) {
            $cr >= 29 => 9,
            $cr >= 25 => 8,
            $cr >= 21 => 7,
            $cr >= 17 => 6,
            $cr >= 13 => 5,
            $cr >= 9 => 4,
            $cr >= 5 => 3,
            default => 2,
        };
    }

    public function hitPoints(array $hp): int
    {
        return (int) floor(
            ($hp['HD'] * (($hp['type'] + 1) / 2))
            + $hp['modifier']
        );
    }

    public function savingThrow(
        int $abilityScore,
        bool $proficient,
        bool $override,
        int $overrideValue,
        int $proficiencyBonus
    ): int {

        if ($override) {
            return $overrideValue;
        }

        $modifier = $this->abilityModifier($abilityScore);

        if ($proficient) {
            return $modifier + $proficiencyBonus;
        }

        return $modifier;
    }
public function skill(
    int $abilityScore,
    bool $proficient,
    bool $expertise,
    bool $override,
    ?int $overrideValue,
    int $proficiencyBonus
): int
{
    if ($override && $overrideValue !== null) {
        return $overrideValue;
    }

    $modifier = $this->abilityModifier($abilityScore);

    if ($expertise) {
        return $modifier + ($proficiencyBonus * 2);
    }

    if ($proficient) {
        return $modifier + $proficiencyBonus;
    }

    return $modifier;
}
public function speed(array $json): string
{
    if (empty($json['speeds'])) {
        return '-';
    }

    $parts = [];

    foreach ($json['speeds'] as $speed) {

        $type = strtolower($speed['type']);
        $value = $speed['speed'];

        if ($type === 'walk') {
            $parts[] = "{$value} ft.";
        } else {
            $parts[] = ucfirst($type) . " {$value} ft.";
        }
    }

    return implode(', ', $parts);
}
public function gear(array $json): string
{
    if (empty($json['gear'])) {
        return '-';
    }

    return implode(', ', $json['gear']);
}
public function languages(array $json): string
{
    return $json['languages'] ?? '-';
}
public function challenge(array $json): string
{
    return (string) ($json['CR'] ?? '-');
}
public function senses(array $json): string
{
    if (empty($json['senses'])) {
        return '-';
    }

    $parts = [];

    foreach ($json['senses'] as $sense => $distance) {

        if ($distance <= 0) {
            continue;
        }

        $parts[] = ucfirst($sense) . " {$distance} ft.";
    }

    if (empty($parts)) {
        return '-';
    }

    return implode(', ', $parts);
}
public function initiative(array $json): ?string
{
    if (empty($json['skills'])) {
        return null;
    }

    $proficiencyBonus = $this->proficiencyBonus(
        $json['CR'] ?? 0
    );

    foreach ($json['skills'] as $skill) {

        if (($skill['key'] ?? '') !== 'INITIATIVE') {
            continue;
        }

        $dex = $json['stats']['DEX'] ?? 10;

        $value = $this->skill(
            abilityScore: $dex,
            proficient: $skill['proficient'],
            expertise: $skill['expertise'],
            override: $skill['override'],
            overrideValue: $skill['overrideValue'],
            proficiencyBonus: $proficiencyBonus
        );

        return ($value >= 0 ? '+' : '') . $value;
    }

    return null;
}

}