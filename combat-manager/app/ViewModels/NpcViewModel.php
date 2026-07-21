<?php

namespace App\ViewModels;

use App\DTOs\AbilityData;
use App\DTOs\CombatData;
use App\DTOs\HeaderData;
use App\DTOs\SavingThrowData;
use App\DTOs\SectionData;
use App\DTOs\SkillData;

class NpcViewModel
{
    /**
     * @param AbilityData[] $abilities
     * @param SavingThrowData[] $savingThrows
     * @param SkillData[] $skills
     * @param SectionData[] $sections
     */
    public function __construct(

        public HeaderData $header,

        public CombatData $combat,

        public array $abilities,

        public array $savingThrows,

        public array $skills,

        public array $sections,
        
        public ?string $imagePath,

    ) {
    }
}