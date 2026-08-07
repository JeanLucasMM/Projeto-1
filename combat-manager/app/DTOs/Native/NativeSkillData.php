<?php

namespace App\DTOs\Native;


class NativeSkillData
{

    public function __construct(

        public array $skills = [],

    ) {}



    public function proficient(): array
    {

        return array_filter(
            $this->skills,
            fn($skill) =>
                ($skill['proficient'] ?? false)
        );

    }



    public function toArray(): array
    {
        return $this->skills;
    }

}