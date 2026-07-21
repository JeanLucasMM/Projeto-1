<?php

namespace App\Services\Interpreters;

use App\DTOs\SkillData;
use App\Services\Calculators\StatisticCalculator;

class SkillInterpreter
{
    public function __construct(
        private StatisticCalculator $calculator
    ) {
    }

    public function interpret(array $json): array
    {



        if (empty($json['skills'])) {
            return [];
        }

        $skills = [];

        $proficiencyBonus = $json['proficiency'];

        foreach ($json['skills'] as $skill) {
            if (($skill['key'] ?? '') === 'INITIATIVE') {
    continue;
}

            $ability = $skill['skill']['stat'];

            $skills[] = new SkillData(
                name: $skill['skill']['label'],

                ability: $ability,

                value: $this->calculator->skill(
                    abilityScore: $json['stats'][$ability],
                    proficient: $skill['proficient'],
                    expertise: $skill['expertise'],
                    override: $skill['override'],
                    overrideValue: $skill['overrideValue'],
                    proficiencyBonus: $proficiencyBonus
                ),

                proficient: $skill['proficient'],

                expertise: $skill['expertise']
            );
        }

        return $skills;
    }
}