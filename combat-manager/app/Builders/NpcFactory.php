<?php

namespace App\Builders;

use App\Builders\Importers\NpcImporter;
use App\Builders\Importers\AbilityImporter;
use App\Builders\Importers\SkillImporter;
use App\Builders\Importers\CombatImporter;
use App\Builders\Importers\SpellcastingImporter;
use App\Builders\Importers\FeatureImporter;
use App\Builders\Importers\SavingThrowImporter;

class NpcFactory
{
    public static function create(): NpcBuilder
    {
        return new NpcBuilder();
    }

    public static function fromBuilderState(array $state): NpcBuilder
    {
        $npc = static::create();

        $npc
    ->name($state['name'] ?? '')
    ->size($state['size'] ?? '')
    ->alignment($state['alignment'] ?? '')
    ->challengeRating($state['challengeRating'] ?? 0);

if (!empty($state['types']) && is_array($state['types'])) {

    $npc->types($state['types']);

} elseif (!empty($state['type'])) {

    $npc->addType($state['type']);

}

        if (isset($state['combat']) && is_array($state['combat'])) {
            $combat = $state['combat'];

            $npc->combat()
                ->armorClass((int) ($combat['armorClass'] ?? 10))
                ->hitPoints((int) ($combat['hitPoints'] ?? 1))
                ->hitDice($combat['hitDice'] ?? '')
                ->speed($combat['speed'] ?? '')
                ->senses($combat['senses'] ?? '')
                ->languages($combat['languages'] ?? '')
                ->resistances($combat['resistances'] ?? '')
                ->immunities($combat['immunities'] ?? '')
                ->conditionImmunities($combat['conditionImmunities'] ?? '')
                ->vulnerabilities($combat['vulnerabilities'] ?? '');
        }

        if (isset($state['abilities']) && is_array($state['abilities'])) {
            foreach ($state['abilities'] as $key => $value) {
                $npc->ability($key)->value((int) $value);
            }
        }

        if (isset($state['savingThrows']) && is_array($state['savingThrows'])) {
            foreach ($state['savingThrows'] as $key => $value) {
                $npc->savingThrows[$key] = (int) $value;
            }
        }

        if (isset($state['skills']) && is_array($state['skills'])) {

            foreach ($state['skills'] as $key => $data) {

                $npc->skill(
                    $key,
                    $data['ability'] ?? 'dex'
                )

                ->proficient($data['proficient'] ?? false)

                ->expertise($data['expertise'] ?? false)

                ->miscBonus((int)($data['miscBonus'] ?? 0));

            }

        }


        return $npc;
    }

    public static function fromArray(array $data): NpcBuilder
    {
        $npc = static::create();

        foreach (static::importers() as $importer) {
            $payload = static::payloadFor($importer, $data);

            if (!empty($payload)) {
                $importer::import($npc, $payload);
            }
        }

        return $npc;
    }

    public static function from5eMM(array $json): NpcBuilder
    {
        return static::fromArray($json);
    }

    protected static function importers(): array
    {
        return [
            NpcImporter::class,
            AbilityImporter::class,
            SavingThrowImporter::class,
            SkillImporter::class,
            CombatImporter::class,
            SpellcastingImporter::class,
            FeatureImporter::class,
        ];
    }

    protected static function payloadFor(string $importer, array $data): array
    {
        return match ($importer) {
            NpcImporter::class => $data,
            AbilityImporter::class => $data['abilities'] ?? [],
            SavingThrowImporter::class => $data['savingThrows'] ?? [],
            SkillImporter::class => $data['skills'] ?? [],
            CombatImporter::class => $data['combat'] ?? [],
            SpellcastingImporter::class => $data['spellcasting'] ?? [],
            FeatureImporter::class => $data['sections'] ?? [],
            default => [],
        };
    }
}