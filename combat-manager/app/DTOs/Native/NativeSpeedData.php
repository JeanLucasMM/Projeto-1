<?php

namespace App\DTOs\Native;


class NativeSpeedData
{

    public function __construct(

        public int $walk = 30,

        public int $climb = 0,

        public int $swim = 0,

        public int $burrow = 0,

        public int $fly = 0,


        public bool $hover = false,


        public bool $hasJumps = false,


        public int $jumpHorizontalBonus = 0,


        public int $jumpVerticalBonus = 0,

    ) {}

}