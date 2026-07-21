<?php

namespace App\DTOs;

class SkillData
{
    public function __construct(
        public string $name,
        public string $ability,
        public int $value,
        public bool $proficient,
        public bool $expertise,
    ) {
    }
}