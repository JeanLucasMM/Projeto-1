<?php

namespace App\DTOs;

class CombatData
{
    public function __construct(
        public string $speed,
        public string $skills,
        public string $gear,
        public string $resistances,
        public string $immunities,
        public string $conditionImmunities,
        public string $vulnerabilities,
        public string $senses,
        public string $languages,
        public string $challenge,
        public ?string $initiative,
        
    ) {
    }
}