<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeHeaderData;


class NativeHeaderInterpreter
{


    public function interpret(array $json): NativeHeaderData
    {

        $header = $json['header'] ?? [];


        return new NativeHeaderData(

            name:
                $header['name'] ?? '',


            size:
                $header['size'] ?? 'medium',


            types:
                $this->arrayValue(
                    $header['types'] ?? []
                ),


            alignments:
                $this->arrayValue(
                    $header['alignments'] ?? []
                ),


            languages:
                $this->arrayValue(
                    $header['languages'] ?? []
                ),


            languageCustom:
                $header['languageCustom'] ?? '',


            challengeRating:
                (string)($header['challengeRating'] ?? '0'),

        );

    }



    private function arrayValue($value): array
    {

        if (is_array($value)) {
            return $value;
        }


        if (is_string($value) && trim($value)) {

            return array_map(
                'trim',
                explode(',', $value)
            );

        }


        return [];

    }

}