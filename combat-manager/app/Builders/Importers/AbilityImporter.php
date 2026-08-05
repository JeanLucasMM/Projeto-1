<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class AbilityImporter
{
    public static function import(
        NpcBuilder $npc,
        array $abilities
    ): void {

        foreach ($abilities as $ability) {

            $npc

                ->ability(
                    $ability['name']
                )

                ->value(
                    (int) $ability['value']
                );

        }

    }
}