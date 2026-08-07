<?php

namespace App\Services\Interpreters\Native;

use App\DTOs\Native\NativeAbilityData;

class NativeAbilityInterpreter
{
    public function interpret(array $abilities): NativeAbilityData
    {
        return new NativeAbilityData(

            str: (int) ($abilities['str'] ?? 10),

            dex: (int) ($abilities['dex'] ?? 10),

            con: (int) ($abilities['con'] ?? 10),

            int: (int) ($abilities['int'] ?? 10),

            wis: (int) ($abilities['wis'] ?? 10),

            cha: (int) ($abilities['cha'] ?? 10),

        );
    }
}