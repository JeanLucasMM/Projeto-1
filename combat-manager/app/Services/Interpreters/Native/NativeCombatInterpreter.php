<?php

namespace App\Services\Interpreters\Native;


use App\DTOs\Native\NativeCombatData;


class NativeCombatInterpreter
{

    public function interpret(array $json): NativeCombatData
    {

        $combat = $json['combat'] ?? [];


        return new NativeCombatData(

            acBase:
                (int)($combat['ac_base'] ?? 10),


            acBonus:
                (int)($combat['ac_bonus'] ?? 0),


            acType:
                $combat['ac_type'] ?? '',



            hpMode:
                $combat['hp_mode'] ?? 'average',


            hitDiceCount:
                (int)($combat['hit_dice_count'] ?? 1),


            hitDie:
                $combat['hit_die'] ?? 'd8',


            hpModifierExtra:
                (int)($combat['hp_mod_extra'] ?? 0),


            customHp:
                (int)($combat['custom_hp'] ?? 0),



            senses:
                $combat['senses'] ?? [],


            customSenses:
                $combat['customSenses'] ?? [],



            languages:
                $combat['languages'] ?? [],



            resistances:
                $combat['resistances'] ?? [],


            immunities:
                $combat['immunities'] ?? [],


            conditionImmunities:
                $combat['conditionImmunities'] ?? [],


            vulnerabilities:
                $combat['vulnerabilities'] ?? [],



            passivePerceptionBonus:
                (int)($combat['passivePerceptionBonus'] ?? 0),

        );

    }

}