<?php

namespace App\Builders\Objects;

class SpellLevel
{
    public int $level;

    /** @var Spell[] */
    public array $spells = [];

    public function __construct(
        int $level
    ) {
        $this->level = $level;
    }

    public function spell(
        string $name
    ): Spell {

        foreach ($this->spells as $spell) {

            if (strcasecmp($spell->name, $name) === 0) {
                return $spell;
            }

        }

        $spell = new Spell($name);

        $this->spells[] = $spell;

        return $spell;

    }
}