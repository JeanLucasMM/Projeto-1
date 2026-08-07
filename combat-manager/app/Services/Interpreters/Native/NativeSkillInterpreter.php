<?php

namespace App\Services\Interpreters\Native;

use App\DTOs\Native\NativeSkillData;

class NativeSkillInterpreter
{
    public function interpret(array $skills): NativeSkillData
    {
        return new NativeSkillData(

            array_map(

                function (array $skill) {

                    return [

                        'key' =>
                            $skill['key'] ?? '',

                        'label' =>
                            $skill['label'] ?? '',

                        'ability' =>
                            $skill['ability'] ?? 'str',

                        'enabled' =>
                            (bool) ($skill['enabled'] ?? false),

                        'proficient' =>
                            (bool) ($skill['proficient'] ?? false),

                        'expertise' =>
                            (bool) ($skill['expertise'] ?? false),

                        'bonus' =>
                            (int) ($skill['bonus'] ?? 0),

                    ];

                },

                is_array($skills) ? $skills : []

            )

        );
    }
}