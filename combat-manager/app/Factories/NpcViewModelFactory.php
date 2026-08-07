<?php

namespace App\Factories;


use App\Models\Npc;

use App\Services\Interpreters\NpcInterpreter;
use App\Services\Interpreters\Native\NativeNpcInterpreter;

use App\ViewModels\NpcViewModel;
use App\ViewModels\NativeNpcViewModel;



class NpcViewModelFactory
{

    public function __construct(

        private NpcInterpreter $legacyInterpreter,

        private NativeNpcInterpreter $nativeInterpreter,

    ) {
    }



 public function make(Npc $npc): NpcViewModel|NativeNpcViewModel
{
    $json = $npc->json_data;

    if (!is_array($json)) {
        $json = json_decode($json, true) ?? [];
    }


    if (
        ($json['format'] ?? null) === 'npc-builder'
    ) {

        return $this->makeNative(
            $npc,
            $json
        );

    }


    return $this->makeLegacy(
        $npc,
        $json
    );
}





    private function makeLegacy(
        Npc $npc,
        array $json
    ): NpcViewModel {


        return new NpcViewModel(

            header:
                $this->legacyInterpreter->header($json),


            combat:
                $this->legacyInterpreter->combat($json),


            abilities:
                $this->legacyInterpreter->abilities($json),


            savingThrows:
                $this->legacyInterpreter->savingThrows($json),


            skills:
                $this->legacyInterpreter->skills($json),


            sections:
                $this->legacyInterpreter->sections($json),


            imagePath:
                $npc->image_path,

        );

    }







    private function makeNative(
        Npc $npc,
        array $json
    ): NativeNpcViewModel {


        return new NativeNpcViewModel(

            header:
                $this->nativeInterpreter->header($json),


            combat:
                $this->nativeInterpreter->combat($json),


            speed:
                $this->nativeInterpreter->speed($json),


            abilities:
                $this->nativeInterpreter->abilities($json),


            savingThrows:
                $this->nativeInterpreter->savingThrows($json),


            skills:
                $this->nativeInterpreter->skills($json),


            sections:
                $this->nativeInterpreter->sections($json),


            attacks:
                $this->nativeInterpreter->attacks($json),


            multiAttacks:
                $this->nativeInterpreter->multiAttacks($json),


            features:
                $this->nativeInterpreter->features($json),


            actions:
                $this->nativeInterpreter->actions($json),


            bonusActions:
                $this->nativeInterpreter->bonusActions($json),


            reactions:
                $this->nativeInterpreter->reactions($json),


            legendaryActions:
                $this->nativeInterpreter->legendaryActions($json),


            lairActions:
                $this->nativeInterpreter->lairActions($json),


            mythicActions:
                $this->nativeInterpreter->mythicActions($json),


            imagePath:
                $npc->image_path,

        );

    }

}