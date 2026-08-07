<?php

namespace App\ViewModels;

use App\DTOs\Native\NativeHeaderData;
use App\DTOs\Native\NativeCombatData;
use App\DTOs\Native\NativeSpeedData;
use App\DTOs\Native\NativeAbilityData;
use App\DTOs\Native\NativeSavingThrowData;
use App\DTOs\Native\NativeSkillData;


class NativeNpcViewModel
{
    public string $type = 'native';
    public function __construct(

        public NativeHeaderData $header,

        public NativeCombatData $combat,

        public NativeSpeedData $speed,

        public NativeAbilityData $abilities,


        public NativeSavingThrowData $savingThrows,

        public NativeSkillData $skills,


        public array $sections,


        public array $attacks,

        public array $multiAttacks,


        public array $features,

        public array $actions,

        public array $bonusActions,

        public array $reactions,


        public array $legendaryActions,

        public array $lairActions,

        public array $mythicActions,


        public ?string $imagePath = null,


    ) {
    }
}