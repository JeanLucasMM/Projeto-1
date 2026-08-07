<?php

namespace App\Services\Interpreters\Native;

use App\DTOs\Native\NativeHeaderData;
use App\DTOs\Native\NativeCombatData;
use App\DTOs\Native\NativeSpeedData;
use App\DTOs\Native\NativeAbilityData;
use App\DTOs\Native\NativeNpcData;
use App\DTOs\Native\NativeSavingThrowData;
use App\DTOs\Native\NativeSkillData;

class NativeNpcInterpreter
{

    public function __construct(

        private NativeHeaderInterpreter $headerInterpreter,

        private NativeCombatInterpreter $combatInterpreter,

        private NativeSpeedInterpreter $speedInterpreter,

        private NativeAbilityInterpreter $abilityInterpreter,

        private NativeSavingThrowInterpreter $savingThrowInterpreter,

        private NativeSkillInterpreter $skillInterpreter,

        private NativeAttackInterpreter $attackInterpreter,

        private NativeMultiAttackInterpreter $multiAttackInterpreter,

        private NativeEntryInterpreter $entryInterpreter,

        private NativeSectionInterpreter $sectionInterpreter,

    ) {
    }


    public function header(array $json): NativeHeaderData
    {
        return $this->headerInterpreter->interpret(
            $json['header'] ?? []
        );
    }


    public function combat(array $json): NativeCombatData
    {
        return $this->combatInterpreter->interpret(
            $json['combat'] ?? []
        );
    }


    public function speed(array $json): NativeSpeedData
    {
        return $this->speedInterpreter->interpret(
            $json['speed'] ?? []
        );
    }


    public function abilities(array $json): NativeAbilityData
    {
        return $this->abilityInterpreter->interpret(
            $json['abilities'] ?? []
        );
    }


    public function savingThrows(array $json): NativeSavingThrowData
    {
        return $this->savingThrowInterpreter->interpret(
            $json['savingThrows'] ?? []
        );
    }


    public function skills(array $json): NativeSkillData
    {
        return $this->skillInterpreter->interpret(
            $json['skills'] ?? []
        );
    }


    public function sections(array $json): array
    {
        return $this->sectionInterpreter->interpret(
            $json['sections'] ?? []
        );
    }


    public function attacks(array $json): array
    {
        return $this->attackInterpreter->interpret(
            $json['attacks'] ?? []
        );
    }


    public function multiAttacks(array $json): array
    {
        return $this->multiAttackInterpreter->interpret(
            $json['multiAttacks'] ?? []
        );
    }


    public function features(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['features'] ?? []
        );
    }


    public function actions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['actions'] ?? []
        );
    }


    public function bonusActions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['bonusActions'] ?? []
        );
    }


    public function reactions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['reactions'] ?? []
        );
    }


    public function legendaryActions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['legendaryActions'] ?? []
        );
    }


    public function mythicActions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['mythicActions'] ?? []
        );
    }


    public function lairActions(array $json): array
    {
        return $this->entryInterpreter->interpret(
            $json['lairActions'] ?? []
        );
    }


 public function interpret(array $json): NativeNpcData
{
    return new NativeNpcData(

        format:
            $json['format'] ?? 'npc-builder',

        version:
            (int) ($json['version'] ?? 1),

        header:
            $this->header($json),

        combat:
            $this->combat($json),

        speed:
            $this->speed($json),

        abilities:
            $this->abilities($json),

        savingThrows:
            $this->savingThrows($json),

        skills:
            $this->skills($json),

        sections:
            $this->sections($json),

        attacks:
            $this->attacks($json),

        multiAttacks:
            $this->multiAttacks($json),

        features:
            $this->features($json),

        actions:
            $this->actions($json),

        bonusActions:
            $this->bonusActions($json),

        reactions:
            $this->reactions($json),

        legendaryActions:
            $this->legendaryActions($json),

        lairActions:
            $this->lairActions($json),

        mythicActions:
            $this->mythicActions($json),

    );
}

}