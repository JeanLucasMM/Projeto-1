<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class NpcImporter
{
    public static function import(
        NpcBuilder $npc,
        array $json
    ): void {

        $npc

            ->name($json['name'] ?? '')

            ->size($json['size'] ?? '')

            ->alignment($json['alignment'] ?? '')

            ->challengeRating(
                $json['cr'] ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | Tipos
        |--------------------------------------------------------------------------
        */

        if (isset($json['types']) && is_array($json['types'])) {

            $npc->types($json['types']);

        } elseif (!empty($json['type'])) {

            $npc->addType($json['type']);

        }

        /*
        |--------------------------------------------------------------------------
        | Combate
        |--------------------------------------------------------------------------
        */

        $npc

            ->combat()

            ->armorClass((int) ($json['ac'] ?? 10))

            ->hitPoints((int) ($json['hp'] ?? 1))

            ->hitDice($json['hit_dice'] ?? '')

            ->speed($json['speed'] ?? '');

    }
}