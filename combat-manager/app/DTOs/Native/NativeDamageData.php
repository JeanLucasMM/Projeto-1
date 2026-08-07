<?php

namespace App\DTOs\Native;

class NativeDamageData
{
    public function __construct(

        public int $count,

        public string $die,

        public string $type,

        public string $ability,

        public int $extra,

    ) {}
}