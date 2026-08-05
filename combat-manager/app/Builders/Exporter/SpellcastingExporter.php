<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;
use App\Builders\Objects\Spell;
use App\Builders\Objects\SpellLevel;

class SpellcastingExporter
{
    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        $spellcasting = $npc->spellcasting();

        if (empty($spellcasting->levels)) {
            return;
        }

        $levels = [];

        foreach ($spellcasting->levels as $level) {

            $levels[] = self::exportLevel($level);

        }

        $json['spellcasting'] = [[

            'levels' => $levels,

        ]];

    }

    private static function exportLevel(
        SpellLevel $level
    ): array {

        $spells = [];

        foreach ($level->spells as $spell) {

            $spells[] = self::exportSpell($spell);

        }

        return [

            'level' => $level->level,

            'spells' => $spells,

        ];

    }

    private static function exportSpell(
        Spell $spell
    ): array {

        return [

            'name' => $spell->name,

            'prepared' => $spell->prepared,

        ];

    }
}