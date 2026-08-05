<?php

namespace App\Builders\Importers;

use App\Builders\NpcBuilder;

class SavingThrowImporter
{
    public static function import(
        NpcBuilder $npc,
        array $savingThrows
    ): void {

        foreach ($savingThrows as $save) {

            $npc->savingThrows[
                $save['ability']
            ] = (int) $save['value'];

        }

    }
}