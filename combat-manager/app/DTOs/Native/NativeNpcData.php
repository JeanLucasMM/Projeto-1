<?php

namespace App\DTOs\Native;

class NativeNpcData
{
    public function __construct(

        public readonly string $format,

        public readonly int $version,

        public readonly NativeHeaderData $header,

        public readonly NativeCombatData $combat,

        public readonly NativeSpeedData $speed,

        public readonly NativeAbilityData $abilities,

        public readonly NativeSavingThrowData $savingThrows,

        public readonly NativeSkillData $skills,

        public readonly array $sections,

        public readonly array $attacks,

        public readonly array $multiAttacks,

        public readonly array $features,

        public readonly array $actions,

        public readonly array $bonusActions,

        public readonly array $reactions,

        public readonly array $legendaryActions,

        public readonly array $lairActions,

        public readonly array $mythicActions,

    ) {
    }
}