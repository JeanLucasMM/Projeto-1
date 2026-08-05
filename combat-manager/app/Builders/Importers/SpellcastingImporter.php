<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class SpellcastingImporter
{
    public static function import(
        NpcBuilder $npc,
        array $json
    ): void {

        if (empty($json['spellcasting'])) {
            return;
        }

        foreach ($json['spellcasting'] as $casting) {

            foreach ($casting['levels'] ?? [] as $level) {

                $spellLevel = $npc
                    ->spellcasting()
                    ->spellLevel(
                        (int) $level['level']
                    );

                foreach ($level['spells'] ?? [] as $spell) {

                    $spellLevel
                        ->spell(
                            $spell['name']
                        )
                        ->prepared(
                            $spell['prepared'] ?? true
                        );

                }

            }

        }

    }
}