<?php

namespace App\Services\Interpreters\Native;

use App\DTOs\Native\NativeEntryData;

class NativeEntryInterpreter
{
    public function interpret(array $entries): array
    {
        if (!is_array($entries)) {
            return [];
        }

        return array_map(
            fn(array $entry) => NativeEntryData::fromArray(
                $this->normalize($entry)
            ),
            $entries
        );
    }

    private function normalize(array $entry): array
    {
        return [
            'id' => (string) (
                $entry['id'] ?? ''
            ),

            'title' =>
                $entry['title']
                ?? $entry['name']
                ?? '',

            'content' =>
                $entry['content']
                ?? $entry['description']
                ?? $entry['text']
                ?? '',

            'type' =>
                $entry['type']
                ?? 'normal',

            /*
            |--------------------------------------------------------------------------
            | Tracker
            |--------------------------------------------------------------------------
            */
            'tracker' => [
                'enabled' => (bool) (
                    $entry['tracker']['enabled']
                    ?? false
                ),

                'title' =>
                    $entry['tracker']['title']
                    ?? '',

                'uses' => (int) (
                    $entry['tracker']['uses']
                    ?? 0
                ),

                'min' => (int) (
                    $entry['tracker']['min']
                    ?? 4
                ),

                'max' => (int) (
                    $entry['tracker']['max']
                    ?? 6
                ),

                'reset' =>
                    $entry['tracker']['reset']
                    ?? '',

                'customReset' =>
                    $entry['tracker']['customReset']
                    ?? '',
            ],

            /*
            |--------------------------------------------------------------------------
            | Legendary
            |--------------------------------------------------------------------------
            */
            'legendary' => [
                'enabled' => (bool) (
                    $entry['legendary']['enabled']
                    ?? false
                ),

                'totalActions' => (int) (
                    $entry['legendary']['totalActions']
                    ?? 3
                ),

                'intro' =>
                    $entry['legendary']['intro']
                    ?? '',
            ],

            /*
            |--------------------------------------------------------------------------
            | Lair
            |--------------------------------------------------------------------------
            */
            'lair' => [
                'enabled' => (bool) (
                    $entry['lair']['enabled']
                    ?? false
                ),

                'intro' =>
                    $entry['lair']['intro']
                    ?? '',
            ],

            /*
            |--------------------------------------------------------------------------
            | Spellcasting
            |--------------------------------------------------------------------------
            */
            'spellcasting' => [
                'enabled' => (bool) (
                    $entry['spellcasting']['enabled']
                    ?? false
                ),

                'casterLevel' => (int) (
                    $entry['spellcasting']['casterLevel']
                    ?? 1
                ),

                'ability' =>
                    $entry['spellcasting']['ability']
                    ?? 'cha',

                'attackBonusExtra' => (int) (
                    $entry['spellcasting']['attackBonusExtra']
                    ?? 0
                ),

                'saveDCExtra' => (int) (
                    $entry['spellcasting']['saveDCExtra']
                    ?? 0
                ),

                /*
                |--------------------------------------------------------------------------
                | Spell Slots
                |--------------------------------------------------------------------------
                */
                'slots' => $this->normalizeSpellSlots(
                    $entry['spellcasting']['slots']
                    ?? []
                ),
            ],
        ];
    }

    private function normalizeSpellSlots(array $slots): array
    {
        if (!is_array($slots)) {
            return [];
        }

        return array_map(
            function (array $slot): array {
                return [
                    'id' => (string) (
                        $slot['id']
                        ?? ''
                    ),

                    'level' => (int) (
                        $slot['level']
                        ?? 0
                    ),

                    'uses' => (int) (
                        $slot['uses']
                        ?? 0
                    ),

                    'reset' =>
                        $slot['reset']
                        ?? 'day',

                    'customReset' =>
                        $slot['customReset']
                        ?? '',

                    'spells' =>
                        $slot['spells']
                        ?? '',
                ];
            },
            $slots
        );
    }
}