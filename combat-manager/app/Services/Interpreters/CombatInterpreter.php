<?php

namespace App\Services\Interpreters;

use App\DTOs\CombatData;
use App\Services\Interpreters\SkillInterpreter;
use App\Services\Calculators\StatisticCalculator;

class CombatInterpreter
{
         public function __construct(
        private SkillInterpreter $skillInterpreter,
        private StatisticCalculator $calculator
    ) {
    }

    public function interpret(array $json): CombatData
    {
        
        return new CombatData(

            speed: $this->speed($json),

            initiative: $this->calculator->initiative($json),

            skills: $this->skills($json),

            gear: $this->gear($json),

            resistances: $this->resistances($json),

            immunities: $this->immunities($json),

            conditionImmunities: $this->conditionImmunities($json),

            vulnerabilities: $this->vulnerabilities($json),

            senses: $this->senses($json),

            languages: $this->languages($json),

            challenge: (string) ($json['CR'] ?? '-')
);
    }

    private function speed(array $json): string
    {
        if (empty($json['speeds'])) {
            return '-';
        }

        $parts = [];

        foreach ($json['speeds'] as $speed) {

            $text = $speed['speed'] . ' ft.';

            if ($speed['type'] !== 'walk') {
                $text = ucfirst($speed['type']) . ' ' . $text;
            }

            $parts[] = $text;
        }

        return implode(', ', $parts);
    }

    private function gear(array $json): string
    {
        if (empty($json['gear'])) {
            return '-';
        }

        return implode(', ', $json['gear']);
    }

    private function languages(array $json): string
    {
        return $json['languages'] ?? '-';
    }

private function passivePerception(array $json): int
{
    if (
        ($json['passivePerception']['override'] ?? false)
        && isset($json['passivePerception']['overrideValue'])
    ) {
        return (int) $json['passivePerception']['overrideValue'];
    }

    foreach ($json['skills'] ?? [] as $skill) {

        if (($skill['key'] ?? '') !== 'PERCEPTION') {
            continue;
        }

        if ($skill['override']) {
            return 10 + $skill['overrideValue'];
        }

        $ability = $json['stats'][$skill['skill']['stat']] ?? 10;

        $bonus = $this->calculator->skill(
            abilityScore: $ability,
            proficient: $skill['proficient'],
            expertise: $skill['expertise'],
            override: false,
            overrideValue: 0,
            proficiencyBonus: $this->calculator->proficiencyBonus(
    $json['CR'] ?? 0
),
        );

        return 10 + $bonus;
    }

    return 10;
}


private function senses(array $json): string
{
    $parts = [];

    if (!empty($json['senses'])) {

        foreach ($json['senses'] as $sense => $distance) {

            if ($distance <= 0) {
                continue;
            }

            $parts[] = ucfirst($sense) . " {$distance} ft.";
        }
    }

    $parts[] = 'Passive Perception ' . $this->passivePerception($json);

    return empty($parts)
        ? '-'
        : implode(', ', $parts);
}

    private function resistances(array $json): string
    {
        return empty($json['resistances'])
            ? '-'
            : implode(', ', $json['resistances']);
    }

private function immunities(array $json): string
{
    if (empty($json['immunities'])) {
        return '-';
    }

    return implode(', ', $json['immunities']);
}

private function conditionImmunities(array $json): string
{
    if (empty($json['conditions'])) {
        return '-';
    }

    return implode(', ', $json['conditions']);
}

    private function vulnerabilities(array $json): string
    {
        return empty($json['vulnerabilities'])
            ? '-'
            : implode(', ', $json['vulnerabilities']);
    }


    private function skills(array $json): string
{
    $skills = $this->skillInterpreter->interpret($json);

    if (empty($skills)) {
        return '-';
    }

    return collect($skills)
        ->map(function ($skill) {

            return sprintf(
                '%s %s%d',
                $skill->name,
                $skill->value >= 0 ? '+' : '',
                $skill->value
            );

        })
        ->implode(', ');
}
}