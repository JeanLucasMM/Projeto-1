<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeAttackData;


class NativeAttackInterpreter
{


    public function interpret(array $attacks): array
    {

        if (!is_array($attacks)) {
            return [];
        }


        return array_map(

            fn(array $attack) =>
                NativeAttackData::fromArray(
                    $this->normalize($attack)
                ),

            $attacks

        );

    }



    private function normalize(array $attack): array
    {

        return [

            'id' =>
                (string) (
                    $attack['id']
                    ?? ''
                ),


            'title' =>
                $attack['title']
                ?? '',


            'mode' =>
                $attack['mode']
                ?? 'builder',


            'content' =>
                $attack['content']
                ?? '',



            'builder' => [

                'targets' =>
                    $attack['builder']['targets']
                    ?? 'Um alvo',



                'range' =>
                    $attack['builder']['range']
                    ?? 'melee',



                'reach' =>
                    (int) (
                        $attack['builder']['reach']
                        ?? 5
                    ),



                'attackAbility' =>
                    $attack['builder']['attackAbility']
                    ?? 'str',



                'proficiency' =>
                    (bool) (
                        $attack['builder']['proficiency']
                        ?? true
                    ),



                'extraHitBonus' =>
                    (int) (
                        $attack['builder']['extraHitBonus']
                        ?? 0
                    ),



                'attackType' =>
                    $attack['builder']['attackType']
                    ?? 'weapon',



                'damages' =>
                    $this->normalizeDamages(
                        $attack['builder']['damages']
                        ?? []
                    ),



                'effects' =>
                    $this->normalizeEffects(
                        $attack['builder']['effects']
                        ?? []
                    ),

            ],

        ];

    }





    private function normalizeDamages(array $damages): array
    {

        return array_map(

            function(array $damage){

                return [

                    'id' =>
                        (string)(
                            $damage['id']
                            ?? ''
                        ),


                    'count' =>
                        (int)(
                            $damage['count']
                            ?? 1
                        ),


                    'die' =>
                        $damage['die']
                        ?? 'd6',


                    'type' =>
                        $damage['type']
                        ?? 'slashing',


                    'ability' =>
                        $damage['ability']
                        ?? 'str',


                    'extra' =>
                        (int)(
                            $damage['extra']
                            ?? 0
                        ),

                ];

            },

            $damages

        );

    }





    private function normalizeEffects(array $effects): array
    {

        return array_map(

            function($effect){

                if (is_string($effect)) {

                    return [

                        'id' => '',

                        'content' => $effect,

                    ];

                }


                return [

                    'id' =>
                        (string)(
                            $effect['id']
                            ?? ''
                        ),


                    'content' =>
                        $effect['content']
                        ?? '',

                ];

            },

            $effects

        );

    }


}