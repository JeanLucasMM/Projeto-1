<?php

namespace App\Services\Interpreters;

use App\ViewModels\TrackerViewModel;
use App\Models\CombatNpc;

class SpellSlotParser
{
    public static function parse(
        string $text,
        CombatNpc $combatNpc,
        string $resourcePrefix
    ): array
    {
        $trackers = [];

        // 1ª Passagem: Captura o formato antigo (ex: ___/3)
        $text = preg_replace_callback(
            '/_{3,}\/(\d+)/u',
            function ($matches) use (&$trackers, $combatNpc, $resourcePrefix) {
                $max = intval($matches[1]);
                $resourceKey = $resourcePrefix . '-' . $matches[0];
                
                $current = $combatNpc->getResource($resourceKey, $max);
                
                $trackers[] = new TrackerViewModel(
                    type: 'counter',
                    resourceKey: $resourceKey,
                    current: $current,
                    max: $max,
                    label: $matches[0]
                );
                
                return '__TRACKER__' . (count($trackers) - 1) . '__';
            },
            $text
        );

        // 2ª Passagem: Captura o novo formato apagando o (3/Dia) (ex: (3/Dia)____)
        $text = preg_replace_callback(
            '/\(\s*(\d+)\s*\/([^)]+)\)\s*_{3,}/u',
            function ($matches) use (&$trackers, $combatNpc, $resourcePrefix) {
                $max = intval($matches[1]);
                
                $cleanLabel = '(' . $max . '/' . trim($matches[2]) . ')';
                $resourceKey = $resourcePrefix . '-' . $cleanLabel;
                
                $current = $combatNpc->getResource($resourceKey, $max);
                
                $trackers[] = new TrackerViewModel(
                    type: 'counter',
                    resourceKey: $resourceKey,
                    current: $current,
                    max: $max,
                    label: $cleanLabel
                );
                
                // Retornamos APENAS o placeholder, fazendo o texto original desaparecer
                return '__TRACKER__' . (count($trackers) - 1) . '__';
            },
            $text
        );

        return [
            'text' => $text,
            'trackers' => $trackers,
        ];
    }
}