<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeMultiAttackData;


class NativeMultiAttackInterpreter
{


    public function interpret(array $multiAttacks): array
    {

        if (!is_array($multiAttacks)) {
            return [];
        }


        return array_map(

            fn(array $multiAttack) =>
                NativeMultiAttackData::fromArray(
                    $this->normalize($multiAttack)
                ),

            $multiAttacks

        );

    }




    private function normalize(array $multiAttack): array
    {

        return [

            'id' =>
                (string)(
                    $multiAttack['id']
                    ?? ''
                ),


            'title' =>
                $multiAttack['title']
                ?? 'Multiataque',


            'mode' =>
                $multiAttack['mode']
                ?? 'automatic',



            'customText' =>
                $multiAttack['customText']
                ?? '',



            'entries' =>
                $this->normalizeEntries(
                    $multiAttack['entries']
                    ?? []
                ),

        ];

    }




    private function normalizeEntries(array $entries): array
    {

        return array_map(

            function(array $entry){

                return [

                    'id' =>
                        (string)(
                            $entry['id']
                            ?? ''
                        ),



                    'source' =>
                        $entry['source']
                        ?? 'attack',



                    'sourceId' =>
                        (string)(
                            $entry['sourceId']
                            ?? ''
                        ),



                    'quantity' =>
                        (int)(
                            $entry['quantity']
                            ?? 1
                        ),

                ];

            },

            $entries

        );

    }


}