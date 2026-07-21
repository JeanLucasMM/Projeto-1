<?php

namespace App\DTOs;

class DiceRollResult
{
    public function __construct(
        public readonly string $expression,
        public readonly int $dice,
        public readonly int $faces,
        public readonly array $rolls,
        public readonly int $modifier,
        public readonly int $total,
    ) {}
}