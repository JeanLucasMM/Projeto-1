<?php

namespace App\Services\Spells;

use App\Models\Character;
use App\Models\CharacterSpell;
use App\Models\CharacterSpellcasting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SpellcastingSheetSyncService
{
    /**
     * Sincroniza apenas os dados que pertencem à classe da ficha.
     *
     * O jogador continua livre para sobrescrever atributo, progressão,
     * CD, ataque e slots no perfil de conjuração.
     */
    public function syncLinkedProfiles(
        Character $character
    ): void {
        $character->loadMissing('classes');

        $classes = $character
            ->classes
            ->keyBy(
                fn ($row) => (int) $row->id
            );

        $profiles = CharacterSpellcasting::query()
            ->where(
                'character_id',
                $character->id
            )
            ->where(
                'sync_with_character_class',
                true
            )
            ->whereNotNull(
                'character_class_id'
            )
            ->get();

        foreach ($profiles as $profile) {
            $class = $classes->get(
                (int) $profile->character_class_id
            );

            if (! $class) {
                continue;
            }

            $className = trim(
                (string) $class->class
            );

            $hasCollision = CharacterSpellcasting::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'id',
                    '<>',
                    $profile->id
                )
                ->where(
                    'class',
                    $className
                )
                ->exists();

            if (! $hasCollision) {
                $profile->class = $className;
            }

            $profile->caster_level = max(
                1,
                min(
                    20,
                    (int) $class->level
                )
            );

            if ($profile->isDirty()) {
                $profile->save();

                CharacterSpell::query()
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->where(
                        'spellcasting_id',
                        $profile->id
                    )
                    ->update([
                        'source_class' =>
                            $profile->class,
                    ]);
            }
        }
    }

    public function context(
        Character $character,
        Collection $profiles
    ): array {
        $character->loadMissing('classes');

        return [
            'level' =>
                max(
                    1,
                    (int) $character->level
                ),

            'proficiency_bonus' =>
                (int) $character->proficiency_bonus,

            'classes' =>
                $character
                    ->classes
                    ->sortBy('sort_order')
                    ->values()
                    ->map(
                        function ($class): array {
                            $detected = $this->detect(
                                (string) $class->class,
                                (string) ($class->subclass ?? '')
                            );

                            return [
                                'id' =>
                                    (int) $class->id,

                                'class' =>
                                    (string) $class->class,

                                'subclass' =>
                                    $class->subclass
                                    ?: null,

                                'level' =>
                                    max(
                                        1,
                                        (int) $class->level
                                    ),

                                'is_primary' =>
                                    (bool) $class->is_primary,

                                'spellcasting_default' =>
                                    $detected,
                            ];
                        }
                    )
                    ->all(),

            'spellcasting_suggestions' =>
                $this->suggestions(
                    $character,
                    $profiles
                ),
        ];
    }

    public function suggestions(
        Character $character,
        Collection $profiles
    ): array {
        $character->loadMissing('classes');

        $linkedClassIds = $profiles
            ->pluck('character_class_id')
            ->filter()
            ->map(
                fn ($id) => (int) $id
            )
            ->all();

        $existingNames = $profiles
            ->pluck('class')
            ->filter()
            ->map(
                fn ($name) =>
                    $this->normalize(
                        (string) $name
                    )
            )
            ->all();

        $result = [];

        foreach (
            $character
                ->classes
                ->sortBy('sort_order')
            as $class
        ) {
            if (
                in_array(
                    (int) $class->id,
                    $linkedClassIds,
                    true
                )
            ) {
                continue;
            }

            if (
                in_array(
                    $this->normalize(
                        (string) $class->class
                    ),
                    $existingNames,
                    true
                )
            ) {
                continue;
            }

            $detected = $this->detect(
                (string) $class->class,
                (string) ($class->subclass ?? '')
            );

            if (! $detected) {
                continue;
            }

            $result[] = [
                'character_class_id' =>
                    (int) $class->id,

                'class' =>
                    (string) $class->class,

                'subclass' =>
                    $class->subclass
                    ?: null,

                'level' =>
                    max(
                        1,
                        (int) $class->level
                    ),

                'ability' =>
                    $detected['ability'],

                'caster_type' =>
                    $detected['caster_type'],

                'label' =>
                    $detected['label'],

                'spell_list_codes' =>
                    $detected['spell_list_codes'] ?? [],
            ];
        }

        return $result;
    }

    public function importDetectedProfiles(
        Character $character
    ): int {
        $character->loadMissing('classes');

        $profiles = CharacterSpellcasting::query()
            ->where(
                'character_id',
                $character->id
            )
            ->get();

        $suggestions = $this->suggestions(
            $character,
            $profiles
        );

        $created = 0;

        foreach ($suggestions as $index => $suggestion) {
            CharacterSpellcasting::query()
                ->create([
                    'character_id' =>
                        $character->id,

                    'character_class_id' =>
                        $suggestion[
                            'character_class_id'
                        ],

                    'sync_with_character_class' =>
                        true,

                    'class' =>
                        $suggestion['class'],

                    'ability' =>
                        $suggestion['ability'],

                    'caster_level' =>
                        $suggestion['level'],

                    'caster_type' =>
                        $suggestion['caster_type'],

                    'spell_list_codes' =>
                        $suggestion['spell_list_codes'] ?? [],

                    'slot_mode' =>
                        'automatic',

                    'is_active' =>
                        true,

                    'sort_order' =>
                        $profiles->count()
                        + $index,
                ]);

            $created++;
        }

        return $created;
    }

    /**
     * Quando o perfil está vinculado à ficha, nome e nível vêm da classe.
     * Os demais campos continuam sob controle do jogador.
     */
    public function applyLinkedClass(
        Character $character,
        array $payload
    ): array {
        $classId = $payload[
            'character_class_id'
        ] ?? null;

        $sync = (bool) (
            $payload[
                'sync_with_character_class'
            ] ?? false
        );

        if (! $classId || ! $sync) {
            $payload[
                'character_class_id'
            ] = $classId
                ? (int) $classId
                : null;

            $payload[
                'sync_with_character_class'
            ] = false;

            return $payload;
        }

        $class = $character
            ->classes()
            ->whereKey(
                (int) $classId
            )
            ->firstOrFail();

        $payload['class'] = trim(
            (string) $class->class
        );

        $payload['caster_level'] = max(
            1,
            min(
                20,
                (int) $class->level
            )
        );

        $payload[
            'character_class_id'
        ] = (int) $class->id;

        $payload[
            'sync_with_character_class'
        ] = true;

        return $payload;
    }

    public function detectForClass(
        string $class,
        ?string $subclass = null
    ): ?array {
        return $this->detect(
            $class,
            $subclass ?? ''
        );
    }

    private function detect(
        string $class,
        string $subclass
    ): ?array {
        $classKey = $this->normalize(
            $class
        );

        $subclassKey = $this->normalize(
            $subclass
        );

        if (
            in_array(
                $classKey,
                [
                    'fighter',
                    'guerreiro',
                ],
                true
            )
            &&
            in_array(
                $subclassKey,
                [
                    'eldritch knight',
                    'cavaleiro arcano',
                ],
                true
            )
        ) {
            return [
                'caster_type' => 'third',
                'ability' => 'intelligence',
                'label' => '1/3 conjurador',
                'spell_list_codes' => ['wizard'],
            ];
        }

        if (
            in_array(
                $classKey,
                [
                    'rogue',
                    'ladino',
                ],
                true
            )
            &&
            in_array(
                $subclassKey,
                [
                    'arcane trickster',
                    'trapaceiro arcano',
                ],
                true
            )
        ) {
            return [
                'caster_type' => 'third',
                'ability' => 'intelligence',
                'label' => '1/3 conjurador',
                'spell_list_codes' => ['wizard'],
            ];
        }

        $map = [
            'bard' => ['full', 'charisma', 'Conjurador completo', 'bard'],

            'bardo' => ['full', 'charisma', 'Conjurador completo', 'bard'],

            'cleric' => ['full', 'wisdom', 'Conjurador completo', 'cleric'],

            'clerigo' => ['full', 'wisdom', 'Conjurador completo', 'cleric'],

            'druid' => ['full', 'wisdom', 'Conjurador completo', 'druid'],

            'druida' => ['full', 'wisdom', 'Conjurador completo', 'druid'],

            'sorcerer' => ['full', 'charisma', 'Conjurador completo', 'sorcerer'],

            'feiticeiro' => ['full', 'charisma', 'Conjurador completo', 'sorcerer'],

            'wizard' => ['full', 'intelligence', 'Conjurador completo', 'wizard'],

            'mago' => ['full', 'intelligence', 'Conjurador completo', 'wizard'],

            'paladin' => ['half_up', 'charisma', 'Meio-conjurador 2024', 'paladin'],

            'paladino' => ['half_up', 'charisma', 'Meio-conjurador 2024', 'paladin'],

            'ranger' => ['half_up', 'wisdom', 'Meio-conjurador 2024', 'ranger'],

            'patrulheiro' => ['half_up', 'wisdom', 'Meio-conjurador 2024', 'ranger'],

            'warlock' => ['pact', 'charisma', 'Pact Magic', 'warlock'],

            'bruxo' => ['pact', 'charisma', 'Pact Magic', 'warlock'],

            'artificer' => ['half_up', 'intelligence', 'Meio-conjurador', 'artificer'],

            'artifice' => ['half_up', 'intelligence', 'Meio-conjurador', 'artificer'],
        ];

        if (! isset($map[$classKey])) {
            return null;
        }

        [
            $casterType,
            $ability,
            $label,
            $spellListCode,
        ] = $map[$classKey];

        return [
            'caster_type' => $casterType,
            'ability' => $ability,
            'label' => $label,
            'spell_list_codes' => [$spellListCode],
        ];
    }

    private function normalize(
        string $value
    ): string {
        return strtolower(
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    Str::ascii($value)
                )
                ?? Str::ascii($value)
            )
        );
    }
}
