<?php

namespace App\Builders\Objects;

class Spellcasting
{
    /** @var SpellLevel[] */
    public array $levels = [];

    public function spellLevel(
        int $level
    ): SpellLevel {

        foreach ($this->levels as $spellLevel) {

            if ($spellLevel->level === $level) {
                return $spellLevel;
            }

        }

        $spellLevel = new SpellLevel($level);

        $this->levels[] = $spellLevel;

        return $spellLevel;

    }
}