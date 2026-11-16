<?php

namespace App\Services\Spells;

use Illuminate\Support\Str;
use InvalidArgumentException;

class FiveEToolsSpellNormalizer
{
    private const NORMALIZER_VERSION = 'spellbound-spell-normalizer-v4';

    private const SCHOOL_NAMES = [
        'A' => 'Abjuration',
        'C' => 'Conjuration',
        'D' => 'Divination',
        'E' => 'Enchantment',
        'V' => 'Evocation',
        'I' => 'Illusion',
        'N' => 'Necromancy',
        'T' => 'Transmutation',
    ];

    public function __construct(
        private readonly FiveEToolsTextFormatter $textFormatter
    ) {
    }

    public function normalize(
        array $spell,
        string $provider = '5etools'
    ): array {
        $name = trim((string) ($spell['name'] ?? ''));
        $source = trim((string) ($spell['source'] ?? ''));

        if ($name === '' || $source === '') {
            throw new InvalidArgumentException(
                'Um feitiço do 5etools está sem [name] ou [source].'
            );
        }

        $descriptionData = $spell['entries'] ?? [];
        $higherLevelData = $spell['entriesHigherLevel'] ?? null;
        $scalingLevelDice = $spell['scalingLevelDice'] ?? null;

        $damageTypes = $this->stringList($spell['damageInflict'] ?? []);
        $savingThrows = $this->stringList($spell['savingThrow'] ?? []);
        $areaTags = $this->stringList($spell['areaTags'] ?? []);
        $miscTags = $this->stringList($spell['miscTags'] ?? []);

        $effects = array_filter([
            'damage_types' => $damageTypes ?: null,
            'saving_throws' => $savingThrows ?: null,
            'conditions_inflicted' => $this->stringList(
                $spell['conditionInflict'] ?? []
            ) ?: null,
            'area_tags' => $areaTags ?: null,
        ], fn ($value) => $value !== null);

        return [
            'name' => $name,
            'level' => (int) ($spell['level'] ?? 0),
            'school' => $this->schoolName($spell['school'] ?? null),
            'casting_time' => $this->formatCastingTime($spell['time'] ?? []),
            'range' => $this->formatRange($spell['range'] ?? null),
            'components' => $spell['components'] ?? null,
            'duration' => $this->formatDuration($spell['duration'] ?? []),
            'ritual' => (bool) data_get($spell, 'meta.ritual', false),
            'concentration' => $this->hasConcentration($spell['duration'] ?? []),
            'description' => $this->textFormatter->entriesToText($descriptionData),
            'effects' => $effects ?: null,
            'saving_throw' => $savingThrows
                ? strtoupper(implode(', ', $savingThrows))
                : null,
            'attack_type' => $this->formatAttackType($spell['spellAttack'] ?? []),
            'upcast' => $higherLevelData,
            'source' => $source,
            'slug' => Str::slug($name).'-'.Str::lower($source),
            'active' => true,

            'provider' => $provider,
            'external_key' => $this->externalKey($name, $source),
            'rules_version' => $this->rulesVersion($spell),
            'page' => isset($spell['page']) ? (int) $spell['page'] : null,
            'other_sources' => $spell['otherSources'] ?? null,

            'casting_time_data' => $spell['time'] ?? null,
            'range_data' => $spell['range'] ?? null,
            'duration_data' => $spell['duration'] ?? null,
            'description_data' => $descriptionData ?: null,
            'higher_level_data' => $higherLevelData,
            'higher_level_text' =>
                $this->normalizeHigherLevelText(
                    $this->textFormatter
                        ->entriesToText(
                            $higherLevelData
                        )
                ),
            'scaling_level_dice' => $scalingLevelDice,
            'reprinted_as' => $spell['reprintedAs'] ?? null,

            'damage_types' => $damageTypes ?: null,
            'saving_throws' => $savingThrows ?: null,
            'area_tags' => $areaTags ?: null,
            'misc_tags' => $miscTags ?: null,

            'raw_data' => $spell,
            'upstream_hash' => $this->hash($spell),
            'upstream_missing' => false,
        ];
    }

    private function normalizeHigherLevelText(
        ?string $text
    ): ?string {
        $text = trim(
            (string) $text
        );

        if ($text === '') {
            return null;
        }

        $text = preg_replace(
            '/(^|\R)\s*(?:At Higher Levels|Cantrip Upgrade)\.?\s*(?=\R|$)/iu',
            '$1',
            $text
        ) ?? $text;

        $text = preg_replace(
            '/^(?:At Higher Levels|Cantrip Upgrade)\.?\s+/iu',
            '',
            $text
        ) ?? $text;

        $text = preg_replace(
            '/\R{3,}/u',
            "\n\n",
            $text
        ) ?? $text;

        $text = trim($text);

        return $text !== ''
            ? $text
            : null;
    }

    private function externalKey(string $name, string $source): string
    {
        $normalizedName = preg_replace(
            '/\s+/u',
            ' ',
            mb_strtolower(trim($name), 'UTF-8')
        ) ?? mb_strtolower(trim($name), 'UTF-8');

        return mb_strtolower($source, 'UTF-8').'|'.$normalizedName;
    }

    private function schoolName(mixed $school): string
    {
        $code = strtoupper(trim((string) $school));

        return self::SCHOOL_NAMES[$code] ?? ($code !== '' ? $code : 'Unknown');
    }

    private function formatCastingTime(mixed $times): string
    {
        if (! is_array($times) || $times === []) {
            return 'Special';
        }

        $result = [];

        foreach ($times as $time) {
            if (! is_array($time)) {
                continue;
            }

            $number = $time['number'] ?? 1;
            $unit = strtolower((string) ($time['unit'] ?? 'special'));

            $unitText = match ($unit) {
                'action' => ((int) $number === 1 ? 'Action' : 'Actions'),
                'bonus' => 'Bonus Action',
                'reaction' => 'Reaction',
                'round' => ((int) $number === 1 ? 'Round' : 'Rounds'),
                'minute' => ((int) $number === 1 ? 'Minute' : 'Minutes'),
                'hour' => ((int) $number === 1 ? 'Hour' : 'Hours'),
                default => Str::headline($unit),
            };

            $text = "{$number} {$unitText}";

            if (isset($time['condition']) && is_string($time['condition'])) {
                $condition = $this->textFormatter->cleanInlineTags(
                    $time['condition']
                );

                if ($condition !== '') {
                    $text .= ' — '.$condition;
                }
            }

            $result[] = $text;
        }

        return $result !== [] ? implode(' / ', $result) : 'Special';
    }

    private function formatRange(mixed $range): string
    {
        if (! is_array($range)) {
            return 'Special';
        }

        $type = strtolower((string) ($range['type'] ?? 'special'));
        $distance = $range['distance'] ?? null;

        if ($type === 'special') {
            return 'Special';
        }

        if (is_array($distance)) {
            $distanceType = strtolower((string) ($distance['type'] ?? ''));
            $amount = $distance['amount'] ?? null;

            $specialDistance = match ($distanceType) {
                'self' => 'Self',
                'touch' => 'Touch',
                'sight' => 'Sight',
                'unlimited' => 'Unlimited',
                'plane' => 'Same Plane',
                default => null,
            };

            if ($specialDistance !== null) {
                return $specialDistance;
            }

            if ($amount !== null) {
                $unit = match ($distanceType) {
                    'feet' => 'ft.',
                    'miles' => ((float) $amount === 1.0 ? 'mile' : 'miles'),
                    default => $distanceType,
                };

                if ($type === 'point') {
                    return trim("{$amount} {$unit}");
                }

                return Str::headline($type).' '.trim("{$amount} {$unit}");
            }
        }

        return Str::headline($type);
    }

    private function formatDuration(mixed $durations): string
    {
        if (! is_array($durations) || $durations === []) {
            return 'Special';
        }

        $result = [];

        foreach ($durations as $duration) {
            if (! is_array($duration)) {
                continue;
            }

            $type = strtolower((string) ($duration['type'] ?? 'special'));

            if ($type === 'instant') {
                $result[] = 'Instantaneous';
                continue;
            }

            if ($type === 'special') {
                $result[] = 'Special';
                continue;
            }

            if ($type === 'permanent') {
                $text = 'Permanent';

                if (! empty($duration['ends']) && is_array($duration['ends'])) {
                    $text .= ' ('.implode(', ', array_map(
                        fn ($item) => Str::headline((string) $item),
                        $duration['ends']
                    )).')';
                }

                $result[] = $text;
                continue;
            }

            if ($type === 'timed' && is_array($duration['duration'] ?? null)) {
                $timed = $duration['duration'];
                $amount = $timed['amount'] ?? 1;
                $unit = strtolower((string) ($timed['type'] ?? 'round'));

                $unitText = match ($unit) {
                    'turn' => ((int) $amount === 1 ? 'turn' : 'turns'),
                    'round' => ((int) $amount === 1 ? 'round' : 'rounds'),
                    'minute' => ((int) $amount === 1 ? 'minute' : 'minutes'),
                    'hour' => ((int) $amount === 1 ? 'hour' : 'hours'),
                    'day' => ((int) $amount === 1 ? 'day' : 'days'),
                    'week' => ((int) $amount === 1 ? 'week' : 'weeks'),
                    'year' => ((int) $amount === 1 ? 'year' : 'years'),
                    default => $unit,
                };

                $text = "{$amount} {$unitText}";

                if (! empty($duration['concentration'])) {
                    $text = 'Concentration, up to '.$text;
                }

                $result[] = $text;
                continue;
            }

            $result[] = Str::headline($type);
        }

        return $result !== [] ? implode(' / ', $result) : 'Special';
    }

    private function hasConcentration(mixed $durations): bool
    {
        if (! is_array($durations)) {
            return false;
        }

        foreach ($durations as $duration) {
            if (is_array($duration) && ! empty($duration['concentration'])) {
                return true;
            }
        }

        return false;
    }

    private function formatAttackType(mixed $attackTypes): ?string
    {
        if (! is_array($attackTypes) || $attackTypes === []) {
            return null;
        }

        $mapped = array_map(
            fn ($type) => match (strtoupper((string) $type)) {
                'M' => 'Melee Spell Attack',
                'R' => 'Ranged Spell Attack',
                'O' => 'Other Spell Attack',
                default => (string) $type,
            },
            $attackTypes
        );

        return implode(', ', $mapped);
    }

    private function rulesVersion(array $spell): ?string
    {
        $edition = strtolower((string) ($spell['edition'] ?? ''));
        $source = strtoupper((string) ($spell['source'] ?? ''));

        if (
            $edition === 'one'
            || ! empty($spell['basicRules2024'])
            || ! empty($spell['srd52'])
            || $source === 'XPHB'
        ) {
            return '2024';
        }

        if (
            $edition === 'classic'
            || ! empty($spell['basicRules'])
            || ! empty($spell['srd'])
            || $source === 'PHB'
        ) {
            return '2014';
        }

        return null;
    }

    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(
            array_map(
                fn ($item) => trim((string) $item),
                $value
            ),
            fn ($item) => $item !== ''
        ));
    }

    private function hash(array $spell): string
    {
        $canonical = $this->canonicalize($spell);

        return hash(
            'sha256',
            self::NORMALIZER_VERSION.'|'.(
                json_encode(
                    $canonical,
                    JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_PRESERVE_ZERO_FRACTION
                ) ?: ''
            )
        );
    }

    private function canonicalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(
                fn ($item) => $this->canonicalize($item),
                $value
            );
        }

        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = $this->canonicalize($item);
        }

        return $value;
    }
}
