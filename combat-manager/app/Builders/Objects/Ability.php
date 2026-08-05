<?php

namespace App\Builders\Objects;

class Ability
{
    public string $name;

    public int $score;

    public function __construct(
        string $name,
        int $score = 10
    ) {
        $this->name = strtolower($name);
        $this->score = $score;
    }

    public function score(int $value): static
    {
        $this->score = $value;

        return $this;
    }

    public function value(int $value): static
    {
        return $this->score($value);
    }

    public function modifier(): int
    {
        return (int) floor(($this->score - 10) / 2);
    }

    public function modifierString(): string
    {
        $modifier = $this->modifier();

        return $modifier >= 0
            ? '+'.$modifier
            : (string) $modifier;
    }
}
