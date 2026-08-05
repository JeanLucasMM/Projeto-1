<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;

class SavingThrowExporter
{
    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        $savingThrows = [];

        foreach ($npc->savingThrows as $ability => $value) {

            $savingThrows[] = [

                'ability' => $ability,

                'value' => $value,

            ];

        }

        $json['savingThrows'] = $savingThrows;

    }
}