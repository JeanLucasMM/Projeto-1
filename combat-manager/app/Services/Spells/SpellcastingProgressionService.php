<?php

namespace App\Services\Spells;

use App\Models\Character;
use App\Models\CharacterSpellSlot;
use App\Models\CharacterSpellcasting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SpellcastingProgressionService
{
    private const SLOT_TABLE = [
        1  => [2,0,0,0,0,0,0,0,0],
        2  => [3,0,0,0,0,0,0,0,0],
        3  => [4,2,0,0,0,0,0,0,0],
        4  => [4,3,0,0,0,0,0,0,0],
        5  => [4,3,2,0,0,0,0,0,0],
        6  => [4,3,3,0,0,0,0,0,0],
        7  => [4,3,3,1,0,0,0,0,0],
        8  => [4,3,3,2,0,0,0,0,0],
        9  => [4,3,3,3,1,0,0,0,0],
        10 => [4,3,3,3,2,0,0,0,0],
        11 => [4,3,3,3,2,1,0,0,0],
        12 => [4,3,3,3,2,1,0,0,0],
        13 => [4,3,3,3,2,1,1,0,0],
        14 => [4,3,3,3,2,1,1,0,0],
        15 => [4,3,3,3,2,1,1,1,0],
        16 => [4,3,3,3,2,1,1,1,0],
        17 => [4,3,3,3,2,1,1,1,1],
        18 => [4,3,3,3,3,1,1,1,1],
        19 => [4,3,3,3,3,2,1,1,1],
        20 => [4,3,3,3,3,2,2,1,1],
    ];

    public function sync(Character $character): void
    {
        DB::transaction(function () use ($character): void {
            $profiles = CharacterSpellcasting::query()
                ->where('character_id', $character->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $this->syncSharedSlots($character, $profiles);
            $this->syncPactSlots($character, $profiles);
        });
    }

    public function sharedEffectiveCasterLevel(
        Collection $profiles
    ): int {
        $total = 0;

        foreach ($profiles as $profile) {
            if (
                !$profile instanceof CharacterSpellcasting
                || !$profile->is_active
                || $profile->caster_type === 'pact'
            ) {
                continue;
            }

            if (
                $profile->effective_caster_level_override
                !== null
            ) {
                $total += max(
                    0,
                    (int) $profile
                        ->effective_caster_level_override
                );

                continue;
            }

            $level = max(
                0,
                (int) $profile->caster_level
            );

            $total += match ($profile->caster_type) {
                'full' => $level,
                'half' => (int) floor($level / 2),
                'half_up' => (int) ceil($level / 2),
                'third' => (int) floor($level / 3),
                'custom' => 0,
                default => 0,
            };
        }

        return max(0, min(20, $total));
    }

    public function slotsForCasterLevel(int $level): array
    {
        $level = max(0, min(20, $level));

        if ($level < 1) {
            return array_fill(0, 9, 0);
        }

        return self::SLOT_TABLE[$level];
    }

    public function pactProgression(int $level): array
    {
        $level = max(1, min(20, $level));

        $slotLevel = match (true) {
            $level >= 9 => 5,
            $level >= 7 => 4,
            $level >= 5 => 3,
            $level >= 3 => 2,
            default => 1,
        };

        $count = match (true) {
            $level >= 17 => 4,
            $level >= 11 => 3,
            $level >= 2 => 2,
            default => 1,
        };

        return [
            'slot_level' => $slotLevel,
            'count' => $count,
        ];
    }

    public function updateSlot(
        Character $character,
        string $slotType,
        int $slotLevel,
        ?int $manualMaximum,
        int $current
    ): CharacterSpellSlot {
        $slot = CharacterSpellSlot::query()
            ->where('character_id', $character->id)
            ->where('slot_type', $slotType)
            ->where('slot_level', $slotLevel)
            ->firstOrFail();

        $slot->manual_maximum = $manualMaximum;

        $effectiveMaximum =
            $manualMaximum
            ?? $slot->calculated_maximum
            ?? $slot->maximum
            ?? 0;

        $slot->maximum = max(
            0,
            (int) $effectiveMaximum
        );

        $slot->current = max(
            0,
            min(
                (int) $current,
                (int) $slot->maximum
            )
        );

        $slot->save();

        return $slot;
    }

    private function syncSharedSlots(
        Character $character,
        Collection $profiles
    ): void {
        $effectiveLevel =
            $this->sharedEffectiveCasterLevel($profiles);

        $calculated =
            $this->slotsForCasterLevel($effectiveLevel);

        foreach (range(1, 9) as $slotLevel) {
            $newCalculated = (int) (
                $calculated[$slotLevel - 1] ?? 0
            );

            $slot = CharacterSpellSlot::query()
                ->firstOrNew([
                    'character_id' => $character->id,
                    'slot_type' => 'spell',
                    'slot_level' => $slotLevel,
                ]);

            $wasNew = !$slot->exists;
            $oldMaximum = (int) ($slot->maximum ?? 0);

            $slot->calculated_maximum = $newCalculated;
            $slot->recovery = 'long_rest';

            $slot->maximum = max(
                0,
                (int) (
                    $slot->manual_maximum
                    ?? $newCalculated
                )
            );

            if ($wasNew) {
                $slot->current = (int) $slot->maximum;
            } else {
                $increase = max(
                    0,
                    (int) $slot->maximum - $oldMaximum
                );

                $slot->current = min(
                    (int) $slot->maximum,
                    max(0, (int) $slot->current)
                    + $increase
                );
            }

            $slot->save();
        }
    }

    private function syncPactSlots(
        Character $character,
        Collection $profiles
    ): void {
        foreach ($profiles as $profile) {
            if (
                !$profile instanceof CharacterSpellcasting
                || $profile->caster_type !== 'pact'
            ) {
                continue;
            }

            $slotType = 'pact:' . $profile->id;
            $progression =
                $this->pactProgression(
                    (int) $profile->caster_level
                );

            foreach (range(1, 5) as $slotLevel) {
                $newCalculated =
                    $slotLevel
                    === (int) $progression['slot_level']
                        ? (int) $progression['count']
                        : 0;

                $slot = CharacterSpellSlot::query()
                    ->firstOrNew([
                        'character_id' => $character->id,
                        'slot_type' => $slotType,
                        'slot_level' => $slotLevel,
                    ]);

                $wasNew = !$slot->exists;
                $oldMaximum = (int) ($slot->maximum ?? 0);

                $slot->calculated_maximum =
                    $newCalculated;
                $slot->recovery = 'short_rest';

                $slot->maximum = max(
                    0,
                    (int) (
                        $slot->manual_maximum
                        ?? $newCalculated
                    )
                );

                if ($wasNew) {
                    $slot->current =
                        (int) $slot->maximum;
                } else {
                    $increase = max(
                        0,
                        (int) $slot->maximum
                        - $oldMaximum
                    );

                    $slot->current = min(
                        (int) $slot->maximum,
                        max(0, (int) $slot->current)
                        + $increase
                    );
                }

                $slot->save();
            }
        }

    }
}
