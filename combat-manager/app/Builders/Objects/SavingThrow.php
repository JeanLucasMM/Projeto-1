<?php

namespace App\Builders\Objects;

class SavingThrow
{
    public string $ability;

    public bool $proficient = false;

    public int $miscBonus = 0;

    public function __construct(
        string $ability
    ) {
        $this->ability = strtolower($ability);
    }

    public function proficient(bool $value = true): static
    {
        $this->proficient = $value;

        return $this;
    }

    public function miscBonus(int $bonus): static
    {
        $this->miscBonus = $bonus;

        return $this;
    }

    public function modifier(
        Ability $ability,
        int $proficiencyBonus
    ): int {

        $total = $ability->modifier();

        if ($this->proficient) {
            $total += $proficiencyBonus;
        }

        $total += $this->miscBonus;

        return $total;
    }

    public function modifierString(
        Ability $ability,
        int $proficiencyBonus
    ): string {

        $modifier = $this->modifier(
            $ability,
            $proficiencyBonus
        );

        return $modifier >= 0
            ? '+'.$modifier
            : (string) $modifier;
    }
}
