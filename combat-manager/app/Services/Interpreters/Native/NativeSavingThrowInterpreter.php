<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeSavingThrowData;


class NativeSavingThrowInterpreter
{


    private array $abilities = [
        'str',
        'dex',
        'con',
        'int',
        'wis',
        'cha',
    ];



    public function interpret(array $json): NativeSavingThrowData
    {

        $source = $json['savingThrows'] ?? [];


        $result = [];


        foreach ($this->abilities as $ability) {

            $save = $source[$ability] ?? [];


            $result[$ability] = [

                'enabled' =>
                    (bool)($save['enabled'] ?? false),


                'proficient' =>
                    (bool)($save['proficient'] ?? false),


                'bonus' =>
                    (int)($save['bonus'] ?? 0),

            ];

        }


        return new NativeSavingThrowData($result);

    }

}