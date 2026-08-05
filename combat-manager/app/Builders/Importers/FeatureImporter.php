<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;
use App\Builders\Objects\Feature;

class FeatureImporter
{
    protected const SECTION_MAP = [

        'traits'             => 'Características',

        'actions'            => 'Ações',

        'bonusActions'       => 'Ações Bônus',

        'reactions'          => 'Reações',

        'legendaryActions'   => 'Ações Lendárias',

        'mythicActions'      => 'Ações Míticas',

    ];

    public static function import(
        NpcBuilder $npc,
        array $json
    ): void {

        foreach (self::SECTION_MAP as $jsonKey => $sectionTitle) {

            if (empty($json[$jsonKey])) {
                continue;
            }

            $section = $npc->section($sectionTitle);

            foreach ($json[$jsonKey] as $entry) {

                self::importFeature(
                    $section,
                    $entry
                );

            }

        }

    }

    private static function importFeature(
        $section,
        array $entry
    ): Feature {

        $feature = $section
            ->feature(
                $entry['title']
                ?? $entry['name']
                ?? 'Sem Nome'
            );

        $feature->text(
            $entry['text']
            ?? $entry['description']
            ?? ''
        );

        return $feature;
    }
}