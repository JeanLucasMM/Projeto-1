<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class CombatImporter
{
    public static function import(
        NpcBuilder $npc,
        array $combat
    ): void {

        $npc
            ->combat()

            ->senses(
                $combat['senses'] ?? ''
            )

            ->languages(
                $combat['languages'] ?? ''
            )

            ->resistances(
                $combat['resistances'] ?? ''
            )

            ->immunities(
                $combat['immunities'] ?? ''
            )

            ->conditionImmunities(
                $combat['conditionImmunities'] ?? ''
            )

            ->vulnerabilities(
                $combat['vulnerabilities'] ?? ''
            );

    }
}