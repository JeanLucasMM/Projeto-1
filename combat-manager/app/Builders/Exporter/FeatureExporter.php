<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;
use App\Builders\Objects\Feature;
use App\Builders\Objects\Section;

class FeatureExporter
{
    protected const SECTION_MAP = [

        'Características'   => 'traits',

        'Ações'             => 'actions',

        'Ações Bônus'       => 'bonusActions',

        'Reações'           => 'reactions',

        'Ações Lendárias'   => 'legendaryActions',

        'Ações Míticas'     => 'mythicActions',

    ];

    public static function export(
        NpcBuilder $npc,
        array &$json
    ): void {

        foreach ($npc->sections as $section) {

            $jsonKey = self::SECTION_MAP[$section->title] ?? null;

            if (!$jsonKey) {
                continue;
            }

            $entries = [];

            foreach ($section->features as $feature) {

                $entries[] = self::exportFeature($feature);

            }

            $json[$jsonKey] = $entries;

        }

    }

    private static function exportFeature(
        Feature $feature
    ): array {

        return [

            'title' => $feature->title,

            'text' => $feature->text,

        ];

    }
}