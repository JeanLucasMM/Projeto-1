<?php

namespace App\Services\Dice;

use App\DTOs\DiceRollResult;

class DiceRoller
{
    public function __construct(
        private DiceExpressionParser $parser
    ) {
    }

    public function roll(string $expression): DiceRollResult
    {
        $parsed = $this->parser->parse($expression);

        $rolls = [];

        for ($i = 0; $i < $parsed['dice']; $i++) {
            $rolls[] = random_int(1, $parsed['faces']);
        }

        $total = array_sum($rolls) + $parsed['modifier'];

        return new DiceRollResult(
            expression: $parsed['expression'],
            dice: $parsed['dice'],
            faces: $parsed['faces'],
            rolls: $rolls,
            modifier: $parsed['modifier'],
            total: $total,
        );
    }
}