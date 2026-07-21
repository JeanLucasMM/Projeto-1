<?php

namespace App\Services\Interpreters;

use App\DTOs\HeaderData;
use App\Services\Calculators\StatisticCalculator;

class HeaderInterpreter
{
        
    public function __construct(
        private StatisticCalculator $calculator
    ) {
    }

    public function interpret(array $json): HeaderData
    {
        
    
        return new HeaderData(

            name: $json['name'] ?? '',

            type: $json['type'] ?? '',

            size: $json['size'] ?? '',

            alignment: $json['alignment'] ?? null,

            armorClass: $json['AC'] ?? 0,

            hitPoints: $this->hitPoints($json),

            hitDice: $this->hitDice($json),

            challengeRating: (float) ($json['CR'] ?? 0),

            initiative: $this->calculator->initiative($json),
            
            proficiencyBonus: $json['proficiency'],
            
        );
        
    }

    private function hitPoints(array $json): int
    {
        
        if (isset($json['hitPoints'])) {
            return (int) $json['hitPoints'];
        }

        if (isset($json['HP']['HD'])) {

            return (int) (
                ($json['HP']['HD'] * (($json['HP']['type'] + 1) / 2))
                + ($json['HP']['modifier'] ?? 0)
            );
        }

        

        return 0;
    }
    private function hitDice(array $json): string
{
    if (!isset($json['HP'])) {
        return '';
    }

    $hd = $json['HP']['HD'] ?? 0;
    $type = $json['HP']['type'] ?? 0;
    $modifier = $json['HP']['modifier'] ?? 0;

    $text = "{$hd}d{$type}";

    if ($modifier > 0) {
        $text .= " + {$modifier}";
    } elseif ($modifier < 0) {
        $text .= " - " . abs($modifier);
    }

    return $text;
}
}