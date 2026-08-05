<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;

class SkillExporter
{
    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        $skills = [];

        foreach ($npc->skills as $skill) {

            $ability = $npc->ability($skill->ability);

            $skills[] = [

                'name' => $skill->name,

                'ability' => $skill->ability,

                'proficient' => $skill->proficient,

                'expertise' => $skill->expertise,

                'miscBonus' => $skill->miscBonus,

                'value' => $skill->modifier(
                    $ability,
                    $npc->proficiencyBonus()
                ),

            ];

        }

        $json['skills'] = $skills;

    }
}