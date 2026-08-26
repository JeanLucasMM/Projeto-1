<?php

namespace App\Services\Characters;

use App\Models\Character;
use Illuminate\Support\Facades\DB;

class CharacterCreationService
{
    public function create(
        array $data,
        ?string $imagePath = null
    ): Character {
        return DB::transaction(function () use ($data, $imagePath) {

            /*
            |--------------------------------------------------------------------------
            | Classes
            |--------------------------------------------------------------------------
            */

            $classes = collect($data['classes'] ?? [])
                ->filter(fn (array $class) => !empty($class['class']))
                ->map(function (array $class) {
                    return [
                        'class' => trim($class['class']),
                        'subclass' => filled($class['subclass'] ?? null)
                            ? trim($class['subclass'])
                            : null,
                        'level' => max(
                            1,
                            min(
                                20,
                                (int) ($class['level'] ?? 1)
                            )
                        ),
                    ];
                })
                ->values();

            if ($classes->isEmpty()) {
                throw new \InvalidArgumentException(
                    'O personagem precisa possuir pelo menos uma classe.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Nível total
            |--------------------------------------------------------------------------
            */

            $totalLevel = $classes->sum('level');

            if ($totalLevel < 1 || $totalLevel > 20) {
                throw new \InvalidArgumentException(
                    'A soma dos níveis das classes deve estar entre 1 e 20.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            |
            | Só usa o valor enviado quando o jogador ativou a opção
            | "Proficiência personalizada".
            |
            */

            $customProficiency = (bool) (
                $data['custom_prof_enabled'] ?? false
            );

            $proficiencyBonus = $customProficiency
                && array_key_exists('proficiency_bonus', $data)
                ? (int) $data['proficiency_bonus']
                : $this->calculateDefaultProficiency($totalLevel);

            /*
            |--------------------------------------------------------------------------
            | Experiência
            |--------------------------------------------------------------------------
            |
            | A migration não possui uma coluna específica indicando se a
            | campanha utiliza XP. Guardamos essa preferência em overrides.
            |
            */

            $usesExperience = (bool) (
                $data['xp_enabled'] ?? false
            );

            $experiencePoints = $usesExperience
                ? max(
                    0,
                    (int) ($data['experience_points'] ?? 0)
                )
                : 0;

            /*
            |--------------------------------------------------------------------------
            | CHARACTER
            |--------------------------------------------------------------------------
            */

            $character = Character::create([
                'user_id' => $data['user_id'],

                'name' => $data['name'],

                'species' => $data['species'],

                'background' => $data['background'],

                'alignment' => $data['alignment'],

                'level' => $totalLevel,

                'proficiency_bonus' => $proficiencyBonus,

                'image_path' => $imagePath,
            ]);

            /*
            |--------------------------------------------------------------------------
            | CLASSES
            |--------------------------------------------------------------------------
            |
            | A primeira classe é a principal na criação.
            |
            | Como a tabela atual não possui is_primary/sort_order, a relação
            | primary_class do Model continua usando a classe de maior nível.
            |
            */

            $character->classes()->createMany(
                $classes->all()
            );

            /*
            |--------------------------------------------------------------------------
            | ATRIBUTOS
            |--------------------------------------------------------------------------
            */

            $character->abilities()->create([
                'strength' => 10,
                'dexterity' => 10,
                'constitution' => 10,
                'intelligence' => 10,
                'wisdom' => 10,
                'charisma' => 10,
            ]);

            /*
            |--------------------------------------------------------------------------
            | COMBATE
            |--------------------------------------------------------------------------
            */

            $character->combat()->create([
                'experience_points' => $experiencePoints,

                'current_hp' => 0,
                'max_hp' => 0,
                'temporary_hp' => 0,
                'temporary_max_hp' => 0,

                'hit_dice' => [],

                'armor_class' => 10,
                'speed' => 30,
                'initiative_bonus' => 0,

                'death_save_successes' => 0,
                'death_save_failures' => 0,

                'concentration_active' => false,
                'concentration_spell_id' => null,

                'exhaustion_level' => 0,

                'conditions' => [],
                'damage_resistances' => [],
                'damage_immunities' => [],
                'damage_vulnerabilities' => [],

                /*
                |------------------------------------------------------------------
                | Preferências / Overrides
                |------------------------------------------------------------------
                |
                | O Index usa isso para decidir se XP deve aparecer.
                |
                */

                'overrides' => [
                    'uses_experience' => $usesExperience,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | SAVING THROWS
            |--------------------------------------------------------------------------
            */

            $abilities = [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ];

            $character->savingThrows()->createMany(
                collect($abilities)
                    ->map(fn (string $ability) => [
                        'ability' => $ability,
                        'proficient' => false,
                        'bonus_override' => null,
                    ])
                    ->all()
            );

            /*
            |--------------------------------------------------------------------------
            | RETORNO
            |--------------------------------------------------------------------------
            */

            return $character->load([
                'classes',
                'abilities',
                'combat',
                'savingThrows',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Proficiência padrão
    |--------------------------------------------------------------------------
    */

    private function calculateDefaultProficiency(
        int $level
    ): int {
        return match (true) {
            $level >= 17 => 6,
            $level >= 13 => 5,
            $level >= 9 => 4,
            $level >= 5 => 3,
            default => 2,
        };
    }
}