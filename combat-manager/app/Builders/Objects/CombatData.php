<?php

namespace App\Builders\Objects;

class CombatData
{
    public int $armorClass = 10;

    public int $hitPoints = 1;

    public string $hitDice = '';

    public string $speed = '';

    public string $senses = '';

    public string $languages = '';

    public string $resistances = '';

    public string $immunities = '';

    public string $conditionImmunities = '';

    public string $vulnerabilities = '';

    public function armorClass(int $value): static
    {
        $this->armorClass = $value;

        return $this;
    }

    public function hitPoints(int $value): static
    {
        $this->hitPoints = $value;

        return $this;
    }

    public function hitDice(string $value): static
    {
        $this->hitDice = $value;

        return $this;
    }

    public function speed(string $value): static
    {
        $this->speed = $value;

        return $this;
    }

    public function senses(string $value): static
    {
        $this->senses = $value;

        return $this;
    }

    public function languages(string $value): static
    {
        $this->languages = $value;

        return $this;
    }

    public function resistances(string $value): static
    {
        $this->resistances = $value;

        return $this;
    }

    public function immunities(string $value): static
    {
        $this->immunities = $value;

        return $this;
    }

    public function conditionImmunities(string $value): static
    {
        $this->conditionImmunities = $value;

        return $this;
    }

    public function vulnerabilities(string $value): static
    {
        $this->vulnerabilities = $value;

        return $this;
    }
}