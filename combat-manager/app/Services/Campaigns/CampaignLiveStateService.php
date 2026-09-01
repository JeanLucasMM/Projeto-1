<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\Character;

class CampaignLiveStateService
{
    public function masterShield(Campaign $campaign): array
    {
        $campaign->loadMissing([
            'members.user',
            'characters' => function ($query) {
                $query
                    ->with(['combat', 'classes', 'user'])
                    ->orderBy('characters.name');
            },
        ]);

        $charactersByUser = $campaign->characters->groupBy(
            fn (Character $character) => (int) $character->user_id
        );

        $players = $campaign->members
            ->map(function ($member) use (
                $campaign,
                $charactersByUser
            ): array {
                $characters = $charactersByUser->get(
                    (int) $member->user_id,
                    collect()
                );

                $activeCharacter = $characters->first(
                    fn (Character $character) =>
                        (bool) $character->pivot->is_active
                );

                return [
                    'user_id' => (int) $member->user_id,
                    'player_name' => $member->user?->name ?? 'Player',
                    'player_email' => $member->user?->email,
                    'character' => $activeCharacter
                        ? $this->characterPayload(
                            $campaign,
                            $activeCharacter
                        )
                        : null,
                    'resting_count' => $characters
                        ->reject(
                            fn (Character $character) =>
                                (bool) $character->pivot->is_active
                        )
                        ->count(),
                ];
            })
            ->values();

        return [
            'campaign_id' => (int) $campaign->id,
            'updated_at' => now()->toISOString(),
            'players' => $players->all(),
        ];
    }

    private function characterPayload(
        Campaign $campaign,
        Character $character
    ): array
    {
        $combat = $character->combat;

        $baseMaxHp = max(1, (int) ($combat?->max_hp ?? 1));
        $temporaryMaxHp = max(0, (int) ($combat?->temporary_max_hp ?? 0));
        $effectiveMaxHp = max(1, $baseMaxHp + $temporaryMaxHp);
        $currentHp = max(0, (int) ($combat?->current_hp ?? $effectiveMaxHp));
        $temporaryHp = max(0, (int) ($combat?->temporary_hp ?? 0));

        $hpPercent = max(
            0,
            min(
                100,
                round(($currentHp / $effectiveMaxHp) * 100, 2)
            )
        );

        $healthState = match (true) {
            $currentHp <= 0 => 'down',
            $hpPercent <= 25 => 'critical',
            $hpPercent <= 50 => 'wounded',
            default => 'healthy',
        };

        $settings = $this->normalizedSettings(
            $character->sheet_settings ?? []
        );

        $exhaustionEnabled = (bool) data_get(
            $settings,
            'optional_rules.exhaustion',
            false
        );

        $exhaustion = $exhaustionEnabled
            ? min(6, max(0, (int) ($combat?->exhaustion_level ?? 0)))
            : 0;

        $conditions = collect(
            is_array($combat?->conditions)
                ? $combat->conditions
                : []
        )
            ->map(function ($condition) {
                if (is_string($condition)) {
                    return trim($condition);
                }

                if (is_array($condition)) {
                    return trim(
                        (string) (
                            $condition['name']
                            ?? $condition['label']
                            ?? $condition['key']
                            ?? ''
                        )
                    );
                }

                return '';
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $primaryClass = $character->classes
            ->sortByDesc(
                fn ($class) => [
                    (int) ($class->is_primary ?? false),
                    (int) ($class->level ?? 0),
                ]
            )
            ->first();

        $imageUrl = (
            is_string($character->image_path)
            && trim($character->image_path) !== ''
        )
            ? route(
                'campaigns.master.characters.image',
                [
                    'campaign' =>
                        $campaign->id,

                    'character' =>
                        $character->id,
                ],
                false
            )
            : null;

        return [
            'id' => (int) $character->id,
            'name' => $character->name,
            'level' => max(1, (int) ($character->level ?? 1)),
            'class_name' => $primaryClass?->class ?? 'Personagem',
            'image_url' => $imageUrl,
            'sheet_url' => route(
                'characters.show',
                $character,
                false
            ),

            'current_hp' => $currentHp,
            'base_max_hp' => $baseMaxHp,
            'temporary_max_hp' => $temporaryMaxHp,
            'max_hp' => $effectiveMaxHp,
            'temporary_hp' => $temporaryHp,
            'hp_percent' => $hpPercent,
            'health_state' => $healthState,

            'exhaustion_enabled' => $exhaustionEnabled,
            'exhaustion' => $exhaustion,

            'armor_class' => (int) ($combat?->armor_class ?? 10),
            'initiative_bonus' => (int) ($combat?->initiative_bonus ?? 0),
            'speed' => (int) ($combat?->speed ?? 30),
            'concentration_active' => (bool) ($combat?->concentration_active ?? false),
            'conditions' => $conditions,

            // Reservado para o feed de rolamentos.
            'last_roll' => null,

            'combat_updated_at' => $combat?->updated_at?->toISOString(),
        ];
    }

    private function normalizedSettings(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}