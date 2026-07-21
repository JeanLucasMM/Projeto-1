<?php

namespace App\Factories;

use App\Models\Npc;
use App\Services\Interpreters\NpcInterpreter;
use App\ViewModels\NpcViewModel;


class NpcViewModelFactory
{
    public function __construct(
        private NpcInterpreter $interpreter
    ) {
    }

    public function make(Npc $npc): NpcViewModel
    {
        $json = $npc->json_data;
            

        return new NpcViewModel(

            header: $this->interpreter->header($json),

            imagePath: $npc->image_path,

            combat: $this->interpreter->combat($json),

            abilities: $this->interpreter->abilities($json),

            savingThrows: $this->interpreter->savingThrows($json),

            skills: $this->interpreter->skills($json),

            sections: $this->interpreter->sections($json),

            
            

);
    }
}