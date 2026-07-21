<?php

namespace App\Services\Interpreters;

use App\DTOs\SavingThrowData;
use App\Services\Calculators\StatisticCalculator;

class SavingThrowInterpreter
{
    public function __construct(
        private StatisticCalculator $calculator
    ) {
    }

    public function interpret(array $json): array
    {
        if (empty($json['saves'])) {
            return [];
        }

        $savingThrows = [];

$proficiencyBonus = $json['proficiency'];

        foreach ($json['saves'] as $ability => $save) {

            $savingThrows[] = new SavingThrowData(
                ability: $ability,

                value: $this->calculator->savingThrow(
                    abilityScore: $json['stats'][$ability],
                    proficient: $save['proficient'],
                    override: $save['override'],
                    overrideValue: $save['overrideValue'],
                    proficiencyBonus: $proficiencyBonus
                ),

                proficient: $save['proficient']
            );
        }

        return $savingThrows;
    }
}