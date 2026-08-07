<?php

namespace App\DTOs\Native;

class NativeCombatData
{
    public function __construct(

        public int $acBase = 10,

        public int $acBonus = 0,

        public string $acType = '',


        public string $hpMode = 'average',

        public int $hitDiceCount = 1,

        public string $hitDie = 'd8',

        public int $hpModifierExtra = 0,

        public int $customHp = 0,


        public array $senses = [],

        public array $customSenses = [],


        public array $languages = [],


        public array $resistances = [],

        public array $immunities = [],

        public array $conditionImmunities = [],

        public array $vulnerabilities = [],


        public int $passivePerceptionBonus = 0,

    ) {}
}