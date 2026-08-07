<?php

namespace App\Builders\Importers\Native;

use Exception;

class NativeNpcValidator
{
    public static function validate(array $json): void
    {
        if (!isset($json['version'])) {
            throw new Exception(
                'JSON do NPC Builder sem versão.'
            );
        }


        if (!isset($json['header'])) {
            throw new Exception(
                'JSON do NPC Builder sem header.'
            );
        }


        if (!isset($json['combat'])) {
            throw new Exception(
                'JSON do NPC Builder sem dados de combate.'
            );
        }
    }
}