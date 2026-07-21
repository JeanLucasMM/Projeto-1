<?php

namespace App\DTOs;

class SavingThrowData
{
    public function __construct(
        public string $ability,
        public int $value,
        public bool $proficient
    ) {}
}