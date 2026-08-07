<?php

namespace App\DTOs\Native;


class NativeSavingThrowData
{

    public function __construct(

        public array $abilities = [],

    ) {}



    public function get(string $ability): array
    {
        return $this->abilities[$ability] ?? [
            'enabled' => false,
            'proficient' => false,
            'bonus' => 0,
        ];
    }



    public function toArray(): array
    {
        return $this->abilities;
    }

}