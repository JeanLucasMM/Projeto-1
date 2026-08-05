<?php

namespace App\Builders\Exporter;

use App\Builders\NpcBuilder;
use App\Builders\Objects\Feature;
use App\Builders\Objects\Section;
use App\DTOs\AbilityData;
use App\DTOs\CombatData;
use App\DTOs\HeaderData;
use App\DTOs\SavingThrowData;
use App\DTOs\SectionData;
use App\DTOs\SkillData;
use App\ViewModels\NpcViewModel;
use ReflectionClass;

class NpcViewModelExporter
{
    public static function export(NpcBuilder $builder): NpcViewModel
    {
        return new NpcViewModel(
            header: self::header($builder),
            combat: self::combat($builder),
            abilities: self::abilities($builder),
            savingThrows: self::savingThrows($builder),
            skills: self::skills($builder),
            sections: self::sections($builder),
            imagePath: null,
        );
    }

    private static function header(NpcBuilder $builder): HeaderData
    {
        return self::hydrate(HeaderData::class, [

            'name' => $builder->name,

            'size' => $builder->size,

            'type' => implode(', ', $builder->types),

            'alignment' => $builder->alignment,

            'challengeRating' => $builder->challengeRating,

            'cr' => $builder->challengeRating,

            'xp' => $builder->xp(),

            'proficiencyBonus' => $builder->proficiencyBonus(),

            'imagePath' => null,

        ]);
    }

    private static function combat(NpcBuilder $builder): CombatData
    {
        $combat = $builder->combat();

        return self::hydrate(CombatData::class, [

            'armorClass' => $combat->armorClass,
            'ac' => $combat->armorClass,

            'hitPoints' => $combat->hitPoints,
            'hp' => $combat->hitPoints,

            'hitDice' => $combat->hitDice,

            'speed' => $combat->speed,

            'senses' => $combat->senses,

            'languages' => $combat->languages,

            'resistances' => $combat->resistances,

            'immunities' => $combat->immunities,

            'conditionImmunities' => $combat->conditionImmunities,

            'conditions' => $combat->conditionImmunities,

            'vulnerabilities' => $combat->vulnerabilities,

        ]);
    }

    private static function abilities(NpcBuilder $builder): array
    {
        $abilities = [];

        foreach ($builder->abilities as $ability) {

            $abilities[] = self::hydrate(AbilityData::class, [

                'name' => $ability->name,

                'value' => $ability->score,

                'modifier' => $ability->modifier(),

                'savingThrow' => $builder->savingThrows[$ability->name] ?? null,

                'save' => $builder->savingThrows[$ability->name] ?? null,

            ]);

        }

        return $abilities;
    }

    private static function savingThrows(NpcBuilder $builder): array
    {
        $savingThrows = [];

        foreach ($builder->savingThrows as $ability => $value) {

            $savingThrows[] = self::hydrate(

                SavingThrowData::class,

                [

                    'ability' => $ability,

                    'value' => $value,

                ]

            );

        }

        return $savingThrows;
    }

    private static function skills(NpcBuilder $builder): array
    {
        $skills = [];

        foreach ($builder->skills as $skill) {

            $ability = $builder->ability($skill->ability);

            $skills[] = self::hydrate(SkillData::class, [

                'name' => $skill->name,

                'value' => $skill->modifier(

                    $ability,

                    $builder->proficiencyBonus()

                ),

                'proficient' => $skill->proficient,

                'expertise' => $skill->expertise,

                'miscBonus' => $skill->miscBonus,

                'ability' => $skill->ability,

            ]);

        }

        return $skills;
    }

    private static function sections(NpcBuilder $builder): array
    {
        $sections = [];

        foreach ($builder->sections as $section) {

            $sections[] = self::hydrate(

                SectionData::class,

                [

                    'title' => $section->title,

                    'items' => self::sectionItems($section),

                    'features' => self::sectionItems($section),

                ]

            );

        }

        return $sections;
    }

    private static function sectionItems(Section $section): array
    {
        $items = [];

        foreach ($section->features as $feature) {

            $items[] = self::featurePayload($feature);

        }

        return $items;
    }

    private static function featurePayload(Feature $feature): object
    {
        return (object) [

            'title' => $feature->title,

            'text' => $feature->text,

            'trackers' => $feature->trackers,

            'tags' => $feature->tags,

        ];
    }

    private static function hydrate(string $class, array $data): object
    {
        $reflection = new ReflectionClass($class);

        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {

            if (property_exists($object, $key)) {

                $object->{$key} = $value;

            }

        }

        return $object;
    }
}