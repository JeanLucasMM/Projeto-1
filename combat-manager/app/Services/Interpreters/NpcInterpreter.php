<?php

namespace App\Services\Interpreters;

use App\DTOs\AbilityData;
use App\DTOs\FeatureData;
use App\DTOs\SavingThrowData;
use App\DTOs\SectionData;
use App\Services\Calculators\StatisticCalculator;
use App\DTOs\HeaderData;
use App\Services\Interpreters\HeaderInterpreter;
use App\DTOs\CombatData;
use App\DTOs\SkillData;

class NpcInterpreter
{
public function __construct(
    private StatisticCalculator $calculator,
    private SectionInterpreter $sectionInterpreter,
    private SkillInterpreter $skillInterpreter,
    private CombatInterpreter $combatInterpreter,
    private AbilityInterpreter $abilityInterpreter,
    private SavingThrowInterpreter $savingThrowInterpreter,
    private HeaderInterpreter $headerInterpreter,
) {
}

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */
    public function name(array $json): string
{
    return $json['name'] ?? '';
}

public function size(array $json): string
{
    return $json['size'] ?? '';
}

public function type(array $json): string
{
    return $json['type'] ?? '';
}

public function alignment(array $json): string
{
    return $json['alignment'] ?? '';
}
    public function armorClass(array $json): int
    {
        return $json['AC'] ?? 0;
    }

    public function hitPoints(array $json): int
    {
        return $this->calculator->hitPoints(
            $json['HP']
        );
    }

    public function challengeRating(array $json): float
    {
        return (float) ($json['CR'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Abilities
    |--------------------------------------------------------------------------
    */

    public function abilities(array $json): array
{
    return $this->abilityInterpreter->interpret($json);
}

    public function savingThrows(array $json): array
{
        return $this->savingThrowInterpreter->interpret($json);
}

    public function combat(array $json): CombatData
{
        return $this->combatInterpreter->interpret($json);
}

    public function skills(array $json): array
{
        return $this->skillInterpreter->interpret($json);
}
    public function header(array $json): HeaderData
{
        return $this->headerInterpreter->interpret($json);
}
    

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    public function sections(array $json): array
{
    return $this->sectionInterpreter->interpret($json);
}
}