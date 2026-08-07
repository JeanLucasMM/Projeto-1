<?php

namespace App\DTOs\Native;


class NativeAbilityData
{

    public function __construct(

        public int $str = 10,

        public int $dex = 10,

        public int $con = 10,

        public int $int = 10,

        public int $wis = 10,

        public int $cha = 10,

    ) {}



    public function toArray(): array
    {

        return [

            'str' => $this->str,

            'dex' => $this->dex,

            'con' => $this->con,

            'int' => $this->int,

            'wis' => $this->wis,

            'cha' => $this->cha,

        ];

    }

}