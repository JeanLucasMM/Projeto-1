<?php

namespace App\Builders\Importers\Native;


class NativeNpcMapper
{

    public static function map(array $json): array
    {

        $header = $json['header'] ?? [];

        $combat = $json['combat'] ?? [];


        return [

            'name' =>
                $header['name']
                ?? 'Sem nome',


            'nickname' =>
                null,


            'creature_type' =>
                self::firstValue(
                    $header['types'] ?? []
                ),


            'size' =>
                $header['size']
                ?? 'medium',


            'alignment' =>
                self::firstValue(
                    $header['alignments'] ?? []
                ),


            'armor_class' =>
                self::calculateArmorClass($combat),


            'challenge_rating' =>
                (float) (
                    $header['challengeRating']
                    ?? 0
                ),


            'json_data' =>
                $json,

        ];

    }



    private static function calculateArmorClass(
        array $combat
    ): int
    {

        return

            ($combat['ac_base'] ?? 10)

            +

            ($combat['ac_bonus'] ?? 0);

    }



    private static function firstValue(
        array $values
    ): ?string
    {

        return $values[0] ?? null;

    }

}