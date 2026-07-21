<?php

namespace App\Services\Dice;

use App\DTOs\DiceRollResult;

class DiceFormatter
{
    public function format(DiceRollResult $result): string
    {
        $rolls = implode(', ', $result->rolls);

        $modifier = '';

        if ($result->modifier > 0) {
            $modifier = " + {$result->modifier}";
        }

        if ($result->modifier < 0) {
            $modifier = " - " . abs($result->modifier);
        }

        return sprintf(
            '%s → (%s)%s = %d',
            strtoupper($result->expression),
            $rolls,
            $modifier,
            $result->total
        );
    }
}