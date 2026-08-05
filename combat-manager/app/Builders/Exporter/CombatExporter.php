<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;

class CombatExporter
{
    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        $combat = $npc->combat();

        $json['combat'] = [

            'languages' => $combat->languages,

            'senses' => $combat->senses,

            'resistances' => $combat->resistances,

            'immunities' => $combat->immunities,

            'conditionImmunities' => $combat->conditionImmunities,

            'vulnerabilities' => $combat->vulnerabilities,

        ];

    }
}