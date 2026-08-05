<?php

namespace App\Builders\Objects;

class Spell
{
    public string $name;

    public bool $prepared = true;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function prepared(
        bool $value = true
    ): static {

        $this->prepared = $value;

        return $this;

    }
}