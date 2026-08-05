<?php

namespace App\Builders\Exporters;

use App\Builders\NpcBuilder;

class NpcExporter
{
    public static function export(
        NpcBuilder $npc
    ): array {

        $json = [];

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $json['name'] = $npc->name;

        $json['size'] = $npc->size;

        $json['types'] = $npc->types;

        // Compatibilidade com exportadores antigos
        $json['type'] = implode(', ', $npc->types);

        $json['alignment'] = $npc->alignment;

        $json['cr'] = $npc->challengeRating;

        $json['xp'] = $npc->xp();

        /*
        |--------------------------------------------------------------------------
        | Combat
        |--------------------------------------------------------------------------
        */

        $json['ac'] = $npc
            ->combat()
            ->armorClass;

        $json['hp'] = $npc
            ->combat()
            ->hitPoints;

        $json['hit_dice'] = $npc
            ->combat()
            ->hitDice;

        $json['speed'] = $npc
            ->combat()
            ->speed;

        /*
        |--------------------------------------------------------------------------
        | Nested Exporters
        |--------------------------------------------------------------------------
        */

        AbilityExporter::export(
            $npc,
            $json
        );

        SavingThrowExporter::export(
            $npc,
            $json
        );

        SkillExporter::export(
            $npc,
            $json
        );

        CombatExporter::export(
            $npc,
            $json
        );

        SpellcastingExporter::export(
            $npc,
            $json
        );

        FeatureExporter::export(
            $npc,
            $json
        );

        return $json;
    }
}