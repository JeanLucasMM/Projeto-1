<?php

namespace App\DTOs;

class HeaderData
{
public function __construct(
    public string $name,
    public string $type,
    public string $size,
    public ?string $alignment,
    public string $hitDice,

    public int $armorClass,
    public int $hitPoints,
    public float $challengeRating,
    public ?string $initiative,
    public int $proficiencyBonus,
    
) {
}
}