<?php

namespace App\Builders\Objects;

class Skill
{
    public string $name;

    public string $ability;

    public bool $proficient = false;

    public bool $expertise = false;

    /**
     * Bônus vindos de itens, magias, talentos, etc.
     */
    public int $miscBonus = 0;

    public function __construct(
        string $name,
        string $ability = 'dex'
    ) {
        $this->name = $name;
        $this->ability = strtolower($ability);
    }

    public function ability(string $ability): static
    {
        $this->ability = strtolower($ability);

        return $this;
    }

    public function proficient(bool $value = true): static
    {
        $this->proficient = $value;

        return $this;
    }

    public function expertise(bool $value = true): static
    {
        $this->expertise = $value;

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

        if ($this->expertise) {

            $total += $proficiencyBonus * 2;

        } elseif ($this->proficient) {

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
