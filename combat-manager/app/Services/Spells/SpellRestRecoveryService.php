<?php

namespace App\Services\Spells;

use App\Enums\RestType;
use App\Models\Character;
use Illuminate\Support\Facades\DB;

class SpellRestRecoveryService
{
    public function recover(
        Character $character,
        RestType $restType
    ): array {
        $recoveries = match ($restType) {
            RestType::SHORT => [
                'short_rest',
            ],

            RestType::LONG => [
                'short_rest',
                'long_rest',
                'day',
            ],
        };

        $slots = DB::table(
            'character_spell_slots'
        )
            ->where(
                'character_id',
                $character->id
            )
            ->whereIn(
                'recovery',
                $recoveries
            )
            ->get([
                'id',
                'slot_type',
                'slot_level',
                'maximum',
                'current',
                'recovery',
            ]);

        foreach ($slots as $slot) {
            DB::table(
                'character_spell_slots'
            )
                ->where(
                    'id',
                    $slot->id
                )
                ->update([
                    'current' =>
                        max(
                            0,
                            (int)
                            $slot->maximum
                        ),

                    'updated_at' =>
                        now(),
                ]);
        }

        $freeCasts = DB::table(
            'character_spells'
        )
            ->where(
                'character_id',
                $character->id
            )
            ->whereNotNull(
                'free_casts_max'
            )
            ->whereIn(
                'free_casts_recovery',
                $recoveries
            )
            ->get([
                'id',
                'spell_id',
                'free_casts_max',
                'free_casts_recovery',
            ]);

        foreach (
            $freeCasts
            as $characterSpell
        ) {
            DB::table(
                'character_spells'
            )
                ->where(
                    'id',
                    $characterSpell->id
                )
                ->update([
                    'free_casts_current' =>
                        max(
                            0,
                            (int)
                            $characterSpell
                                ->free_casts_max
                        ),

                    'updated_at' =>
                        now(),
                ]);
        }

        return [
            'slots' =>
                $slots
                    ->map(
                        fn (
                            object $slot
                        ) => [
                            'id' =>
                                (int)
                                $slot->id,

                            'slot_type' =>
                                $slot->slot_type,

                            'slot_level' =>
                                (int)
                                $slot->slot_level,

                            'current' =>
                                max(
                                    0,
                                    (int)
                                    $slot->maximum
                                ),

                            'maximum' =>
                                max(
                                    0,
                                    (int)
                                    $slot->maximum
                                ),

                            'recovery' =>
                                $slot->recovery,
                        ]
                    )
                    ->values()
                    ->all(),

            'free_casts' =>
                $freeCasts
                    ->map(
                        fn (
                            object $row
                        ) => [
                            'character_spell_id' =>
                                (int)
                                $row->id,

                            'spell_id' =>
                                (int)
                                $row->spell_id,

                            'current' =>
                                max(
                                    0,
                                    (int)
                                    $row
                                        ->free_casts_max
                                ),

                            'maximum' =>
                                max(
                                    0,
                                    (int)
                                    $row
                                        ->free_casts_max
                                ),

                            'recovery' =>
                                $row
                                    ->free_casts_recovery,
                        ]
                    )
                    ->values()
                    ->all(),
        ];
    }
}
