<?php

namespace App\DTOs;

class AbilityData
{
    public function __construct(
        public string $name,
        public int $value,
        public int $modifier
    ) {}
}