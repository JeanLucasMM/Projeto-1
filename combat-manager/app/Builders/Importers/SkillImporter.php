<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class SkillImporter
{
    public static function import(
        NpcBuilder $npc,
        array $skills
    ): void {

        foreach ($skills as $skill) {

            $npc

                ->skill(

                    $skill['name'],

                    $skill['ability'] ?? 'dex'

                )

                ->proficient(

                    $skill['proficient'] ?? false

                )

                ->expertise(

                    $skill['expertise'] ?? false

                )

                ->miscBonus(

                    (int) ($skill['miscBonus'] ?? 0)

                );

        }

    }
}