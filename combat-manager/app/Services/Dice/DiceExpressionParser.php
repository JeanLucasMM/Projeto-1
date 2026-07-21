<?php

namespace App\Services\Dice;

use InvalidArgumentException;

class DiceExpressionParser
{
    public function parse(string $expression): array
    {
        $expression = strtolower(trim($expression));

        $expression = str_replace(' ', '', $expression);

        if (
            !preg_match(
                '/^(\d+)d(\d+)([+-]\d+)?$/',
                $expression,
                $matches
            )
        ) {
            throw new InvalidArgumentException(
                "Expressão inválida: {$expression}"
            );
        }

        return [

            'expression' => $expression,

            'dice' => (int) $matches[1],

            'faces' => (int) $matches[2],

            'modifier' => isset($matches[3])
                ? (int) $matches[3]
                : 0

        ];
    }
}