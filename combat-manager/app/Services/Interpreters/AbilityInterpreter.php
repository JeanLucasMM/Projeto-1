<?php

namespace App\Services\Interpreters;

use App\DTOs\AbilityData;
use App\Services\Calculators\StatisticCalculator;

class AbilityInterpreter
{
    public function __construct(
        private StatisticCalculator $calculator
    ) {
    }

    public function interpret(array $json): array
    {
        $abilities = [];

        $map = [
            'STR',
            'DEX',
            'CON',
            'INT',
            'WIS',
            'CHA',
        ];

        foreach ($map as $ability) {

            $value = $json['stats'][$ability] ?? 10;

            $abilities[] = new AbilityData(
                name: $ability,
                value: $value,
                modifier: $this->calculator->abilityModifier($value)
            );
        }

        return $abilities;
    }
}