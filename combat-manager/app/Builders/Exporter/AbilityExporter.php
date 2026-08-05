<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;

class AbilityExporter
{
    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        $abilities = [];

        foreach ($npc->abilities as $ability) {

            $abilities[] = [

                'name' => $ability->name,

                'value' => $ability->score,

                'modifier' => $ability->modifier(),

            ];

        }

        $json['abilities'] = $abilities;

    }
}