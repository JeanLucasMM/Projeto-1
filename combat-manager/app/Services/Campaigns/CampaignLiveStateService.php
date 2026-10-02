<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Support\Facades\Storage;

class CampaignLiveStateService
{

    public function masterShield(Campaign $campaign): array
    {
        $campaign->load([
            'members.user',
            'characters' => function ($query) {
                $query
                    ->with(['abilities', 'combat', 'classes', 'skills', 'savingThrows', 'items', 'user'])
                    ->orderBy('characters.name');
            },
        ]);

        $charactersByUser = $campaign->characters->groupBy(
            fn (Character $character) => (int) $character->user_id
        );

        $players = $campaign->members
            ->map(function ($member) use ($campaign, $charactersByUser): array {
                $characters = $charactersByUser->get(
                    (int) $member->user_id,
                    collect()
                );

                $activeCharacter = $characters->first(
                    fn (Character $character) => (bool) $character->pivot->is_active
                );

                return [
                    'user_id' => (int) $member->user_id,
                    'player_name' => $member->user?->name ?? 'Player',
                    'player_email' => $member->user?->email,
                    'character' => $activeCharacter
                        ? $this->characterPayload($campaign, $activeCharacter)
                        : null,
                    'resting_count' => $characters
                        ->reject(fn (Character $character) => (bool) $character->pivot->is_active)
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

    private function characterPayload(Campaign $campaign, Character $character): array
    {
        $combat = $character->combat;

        $baseMaxHp = max(1, (int) ($combat?->max_hp ?? 1));
        $temporaryMaxHp = max(0, (int) ($combat?->temporary_max_hp ?? 0));
        $effectiveMaxHp = max(1, $baseMaxHp + $temporaryMaxHp);
        $currentHp = max(0, (int) ($combat?->current_hp ?? $effectiveMaxHp));
        $temporaryHp = max(0, (int) ($combat?->temporary_hp ?? 0));

        $hpPercent = max(
            0,
            min(100, round(($currentHp / $effectiveMaxHp) * 100, 2))
        );

        $settings = $this->normalizedSettings($character->sheet_settings ?? []);
        $morquenRuleActive = (bool) data_get($settings, 'optional_rules.morquen', false);

        $deathSuccessLimit = $morquenRuleActive ? 2 : 3;
        $deathFailureLimit = $morquenRuleActive ? 6 : 3;

        $deathSuccesses = min(
            $deathSuccessLimit,
            max(0, (int) ($combat?->death_save_successes ?? 0))
        );

        $deathFailures = min(
            $deathFailureLimit,
            max(0, (int) ($combat?->death_save_failures ?? 0))
        );

        $deathSaveDead = $currentHp <= 0
            && $deathFailures >= $deathFailureLimit;

        $deathSaveStabilized = $currentHp <= 0
            && !$deathSaveDead
            && $deathSuccesses >= $deathSuccessLimit;

        $deathSavesActive = $currentHp <= 0
            && !$deathSaveDead
            && !$deathSaveStabilized;

        /*
        |--------------------------------------------------------------------------
        | Histórico persistente de morte
        |--------------------------------------------------------------------------
        |
        | CharacterCombatController registra cada transição real para morte em
        | overrides.death. O Escudo do Mestre lê esse snapshot para continuar
        | mostrando o registro mesmo após cura ou ressurreição.
        |
        */
        $combatOverrides =
            $this->normalizedSettings(
                $combat?->overrides
                ?? []
            );

        $deathHistory =
            data_get(
                $combatOverrides,
                'death.history',
                []
            );

        if (!is_array($deathHistory)) {
            $deathHistory = [];
        }

        $deathHistory =
            collect($deathHistory)
                ->filter(
                    fn ($entry) =>
                        is_array($entry)
                        &&
                        !empty(
                            $entry['occurred_at']
                            ?? null
                        )
                )
                ->values()
                ->all();

        $deathCount =
            max(
                count($deathHistory),
                max(
                    0,
                    (int) data_get(
                        $combatOverrides,
                        'death.count',
                        0
                    )
                )
            );

        $lastDeathAt =
            data_get(
                $combatOverrides,
                'death.last_at'
            );

        if (
            !$lastDeathAt
            &&
            count($deathHistory) > 0
        ) {
            $lastEntry =
                $deathHistory[
                    array_key_last(
                        $deathHistory
                    )
                ];

            $lastDeathAt =
                $lastEntry['occurred_at']
                ?? null;
        }

        /*
         * Compatibilidade: se a ficha já estava morta antes desta versão e
         * ainda não possui history, o Mestre continua vendo um registro.
         */
        if (
            $deathSaveDead
            &&
            $deathCount === 0
        ) {
            $deathCount = 1;
            $lastDeathAt =
                $combat?->updated_at?->toISOString();
        }

        $hasDied =
            $deathCount > 0
            ||
            $deathSaveDead;

        $healthState = match (true) {
            $deathSaveDead => 'dead',
            $currentHp <= 0 => 'down',
            $hpPercent <= 25 => 'critical',
            $hpPercent <= 50 => 'wounded',
            default => 'healthy',
        };

        $exhaustionEnabled = (bool) data_get(
            $settings,
            'optional_rules.exhaustion',
            false
        );

        $exhaustion = $exhaustionEnabled
            ? min(6, max(0, (int) ($combat?->exhaustion_level ?? 0)))
            : 0;

        $conditions = collect(is_array($combat?->conditions) ? $combat->conditions : [])
            ->map(function ($condition) {
                if (is_string($condition)) {
                    return trim($condition);
                }

                if (is_array($condition)) {
                    return trim((string) (
                        $condition['name']
                        ?? $condition['label']
                        ?? $condition['key']
                        ?? ''
                    ));
                }

                return '';
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $primaryClass = $character->classes
            ->sortByDesc(fn ($class) => [
                (int) ($class->is_primary ?? false),
                (int) ($class->level ?? 0),
            ])
            ->first();

        $imageUrl =
            $this->publicImageUrl(
                $character->image_path
            );

        $abilities = $this->abilitySummary($character);
        $armorClass = $this->sheetArmorClass(
            $character,
            $abilities
        );
        $passivePerception = $this->passivePerception($character);
        $savingThrows = $this->savingThrowSummary(
            $character,
            $abilities
        );

        $inventory =
            $this->inventoryPayload(
                $character
            );

        /*
        | O inventário é carregado junto do estado ao vivo para permitir
        | consulta somente-leitura pelo Mestre.
        */
        /*
        $inventory =
            $character->items
                ->sortBy([
                    [
                        'equipped',
                        'desc',
                    ],
                    [
                        'name',
                        'asc',
                    ],
                ])
                ->map(
                    fn ($item) => [
                        'id' =>
                            (int) $item->id,

                        'name' =>
                            (string) $item->name,

                        'type' =>
                            (string) (
                                $item->type
                                ?? 'mundane'
                            ),

                        'quantity' =>
                            max(
                                1,
                                (int) (
                                    $item->quantity
                                    ?? 1
                                )
                            ),

                        'equipped' =>
                            (bool) (
                                $item->equipped
                                ?? false
                            ),

                        'is_magical' =>
                            (bool) (
                                $item->is_magical
                                ?? false
                            ),

                        'rarity' =>
                            $item->rarity
                                ? (string) $item->rarity
                                : null,

                        'weight' =>
                            $item->weight
                                !==
                                null
                                    ? (float) $item->weight
                                    : null,
                    ]
                )
                ->values()
                ->all();
        */

        return [
            'id' => (int) $character->id,
            'name' => $character->name,
            'level' => max(1, (int) ($character->level ?? 1)),
            'class_name' => $primaryClass?->class ?? 'Personagem',
            'image_url' => $imageUrl,
            'sheet_url' => route('characters.show', $character, false),

            'current_hp' => $currentHp,
            'base_max_hp' => $baseMaxHp,
            'temporary_max_hp' => $temporaryMaxHp,
            'max_hp' => $effectiveMaxHp,
            'temporary_hp' => $temporaryHp,
            'hp_percent' => $hpPercent,
            'health_state' => $healthState,

            'exhaustion_enabled' => $exhaustionEnabled,
            'exhaustion' => $exhaustion,

            'armor_class' => $armorClass,
            'passive_perception' => $passivePerception,
            'abilities' => $abilities,
            'saving_throws' => $savingThrows,
            'inventory' => $inventory,
            'initiative_bonus' => (int) ($combat?->initiative_bonus ?? 0),
            'speed' => (int) ($combat?->speed ?? 30),
            'concentration_active' => (bool) ($combat?->concentration_active ?? false),
            'conditions' => $conditions,

            'death_save_successes' => $deathSuccesses,
            'death_save_failures' => $deathFailures,
            'death_save_success_limit' => $deathSuccessLimit,
            'death_save_failure_limit' => $deathFailureLimit,
            'death_saves_active' => $deathSavesActive,
            'death_save_stabilized' => $deathSaveStabilized,
            'death_save_dead' => $deathSaveDead,

            'has_died' => $hasDied,
            'death_count' => $deathCount,
            'last_death_at' => $lastDeathAt,
            'death_history' => $deathHistory,

            'last_roll' => null,
            'combat_updated_at' => $combat?->updated_at?->toISOString(),
        ];
    }





    private function publicImageUrl(
        ?string $path
    ): ?string {
        if (
            !is_string($path)
            ||
            trim($path) === ''
        ) {
            return null;
        }

        $relativePath =
            ltrim(
                str_replace(
                    '\\',
                    '/',
                    trim($path)
                ),
                '/'
            );

        foreach (
            [
                'storage/',
                'public/',
            ]
            as $prefix
        ) {
            if (
                str_starts_with(
                    $relativePath,
                    $prefix
                )
            ) {
                $relativePath =
                    substr(
                        $relativePath,
                        strlen($prefix)
                    );
            }
        }

        if (
            $relativePath === ''
            ||
            str_contains(
                $relativePath,
                '../'
            )
        ) {
            return null;
        }

        if (
            !Storage::disk('public')
                ->exists(
                    $relativePath
                )
        ) {
            return null;
        }

        return
            '/storage/'
            .
            implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode(
                        '/',
                        $relativePath
                    )
                )
            );
    }


    private function abilitySummary(
        Character $character
    ): array {
        $abilities =
            $character->abilities;

        $temporaryBonuses =
            $this->normalizedSettings(
                $abilities?->temporary_bonuses
                ?? []
            );

        $overrides =
            $this->normalizedSettings(
                $abilities?->overrides
                ?? []
            );

        $definitions = [
            'strength' => [
                'short' => 'FOR',
                'label' => 'Força',
            ],
            'dexterity' => [
                'short' => 'DES',
                'label' => 'Destreza',
            ],
            'constitution' => [
                'short' => 'CON',
                'label' => 'Constituição',
            ],
            'intelligence' => [
                'short' => 'INT',
                'label' => 'Inteligência',
            ],
            'wisdom' => [
                'short' => 'SAB',
                'label' => 'Sabedoria',
            ],
            'charisma' => [
                'short' => 'CAR',
                'label' => 'Carisma',
            ],
        ];

        $summary = [];

        foreach (
            $definitions as
            $key => $definition
        ) {
            $baseScore =
                (int) (
                    $abilities?->{$key}
                    ?? 10
                );

            $temporaryBonus =
                (int) (
                    $temporaryBonuses[$key]
                    ?? 0
                );

            $hasOverride =
                array_key_exists(
                    $key,
                    $overrides
                )
                &&
                $overrides[$key] !== null
                &&
                $overrides[$key] !== '';

            $score =
                $hasOverride
                    ? (int) $overrides[$key]
                    : (
                        $baseScore
                        +
                        $temporaryBonus
                    );

            $score =
                max(
                    1,
                    $score
                );

            $summary[] = [
                'key' =>
                    $key,

                'short' =>
                    $definition['short'],

                'label' =>
                    $definition['label'],

                'score' =>
                    $score,

                'modifier' =>
                    (int) floor(
                        (
                            $score
                            -
                            10
                        )
                        /
                        2
                    ),
            ];
        }

        return $summary;
    }


    private function savingThrowSummary(
        Character $character,
        array $abilities
    ): array {
        $savingThrows =
            $character->savingThrows
                ->keyBy(
                    'ability'
                );

        $proficiencyBonus =
            max(
                0,
                (int) (
                    $character->proficiency_bonus
                    ?? 0
                )
            );

        return collect(
            $abilities
        )
            ->map(
                function (
                    array $ability
                ) use (
                    $savingThrows,
                    $proficiencyBonus
                ): array {
                    $save =
                        $savingThrows->get(
                            $ability['key']
                        );

                    $temporaryBonus =
                        (int) (
                            $save?->temporary_bonus
                            ?? 0
                        );

                    $proficient =
                        (bool) (
                            $save?->proficient
                            ?? false
                        );

                    if (
                        $save
                        &&
                        $save->bonus_override !== null
                    ) {
                        $value =
                            (int) $save->bonus_override
                            +
                            $temporaryBonus;
                    } else {
                        $value =
                            (int) $ability['modifier']
                            +
                            (
                                $proficient
                                    ? $proficiencyBonus
                                    : 0
                            )
                            +
                            $temporaryBonus;
                    }

                    return [
                        'key' =>
                            $ability['key'],

                        'short' =>
                            $ability['short'],

                        'label' =>
                            $ability['label'],

                        'value' =>
                            $value,

                        'proficient' =>
                            $proficient,

                        'temporary_bonus' =>
                            $temporaryBonus,
                    ];
                }
            )
            ->values()
            ->all();
    }


    private function passivePerception(
        Character $character
    ): int {
        /*
        |--------------------------------------------------------------------------
        | Percepção Passiva
        |--------------------------------------------------------------------------
        |
        | Segue a mesma estrutura da ficha:
        | 10 + modificador de Percepção + bônus adicional configurado.
        |
        */

        $wisdomScore =
            (int) (
                $character->abilities?->wisdom
                ?? 10
            );

        $wisdomModifier =
            (int) floor(
                (
                    $wisdomScore
                    -
                    10
                )
                /
                2
            );

        $perception =
            $character->skills
                ->first(
                    fn ($skill) =>
                        in_array(
                            mb_strtolower(
                                trim(
                                    (string) (
                                        $skill->skill
                                        ?? ''
                                    )
                                )
                            ),
                            [
                                'perception',
                                'percepção',
                                'percepcao',
                            ],
                            true
                        )
                );

        $temporaryBonus =
            (int) (
                $perception?->temporary_bonus
                ?? 0
            );

        if (
            $perception
            &&
            $perception->bonus_override
                !==
                null
        ) {
            $perceptionModifier =
                (int) $perception->bonus_override
                +
                $temporaryBonus;
        } else {
            $proficiencyBonus =
                max(
                    0,
                    (int) (
                        $character->proficiency_bonus
                        ?? 0
                    )
                );

            $trainingBonus =
                $perception?->expertise
                    ? (
                        $proficiencyBonus
                        *
                        2
                    )
                    : (
                        $perception?->proficient
                            ? $proficiencyBonus
                            : 0
                    );

            $perceptionModifier =
                $wisdomModifier
                +
                $trainingBonus
                +
                $temporaryBonus;
        }

        $overrides =
            $this->normalizedSettings(
                $character->combat?->overrides
                ?? []
            );

        $passiveBonus =
            (int) data_get(
                $overrides,
                'quick_stats.passive_perception.bonus',
                0
            );

        return max(
            0,
            10
            +
            $perceptionModifier
            +
            $passiveBonus
        );
    }


    private function sheetArmorClass(
        Character $character,
        array $abilities
    ): int {
        /*
        |--------------------------------------------------------------------------
        | Classe de Armadura
        |--------------------------------------------------------------------------
        |
        | A defesa pode vir da configuração base da ficha OU de uma armadura
        | equipada. Escudos equipados entram como bônus separado.
        |
        | Estrutura dos itens:
        | properties.armor  -> base_ac, magic_bonus, dexterity_mode,
        |                      dexterity_cap, ability_modifiers
        | properties.shield -> ac_bonus, magic_bonus
        |
        */

        $modifierByLongKey =
            collect(
                $abilities
            )
                ->mapWithKeys(
                    fn (array $ability) => [
                        (string) $ability['key'] =>
                            (int) $ability['modifier'],
                    ]
                )
                ->all();

        $longToShort = [
            'strength' =>
                'str',

            'dexterity' =>
                'dex',

            'constitution' =>
                'con',

            'intelligence' =>
                'int',

            'wisdom' =>
                'wis',

            'charisma' =>
                'cha',
        ];

        $modifierByShortKey =
            collect(
                $longToShort
            )
                ->mapWithKeys(
                    fn ($short, $long) => [
                        $short =>
                            (int) (
                                $modifierByLongKey[$long]
                                ?? 0
                            ),
                    ]
                )
                ->all();

        $combatOverrides =
            $this->normalizedSettings(
                $character->combat?->overrides
                ?? []
            );

        $armorConfig =
            data_get(
                $combatOverrides,
                'armor',
                []
            );

        if (!is_array($armorConfig)) {
            $armorConfig =
                [];
        }

        /*
        | Defesa base da ficha quando não há armadura melhor equipada.
        */
        $storedArmorMode =
            $armorConfig['mode']
            ?? ['dex'];

        $armorMode =
            is_array($storedArmorMode)
                ? array_values(
                    $storedArmorMode
                )
                : [
                    $storedArmorMode,
                ];

        if (
            $armorMode === []
            &&
            !array_key_exists(
                'mode',
                $armorConfig
            )
        ) {
            $armorMode =
                ['dex'];
        }

        $baseBodyAc =
            10
            +
            collect(
                $armorMode
            )
                ->sum(
                    fn ($ability) =>
                        (int) (
                            $modifierByShortKey[
                                (string) $ability
                            ]
                            ?? 0
                        )
                );

        /*
        | Armaduras equipadas substituem a fórmula corporal.
        | Se houver mais de uma por inconsistência de dados, usamos a melhor.
        */
        $equippedArmorValues =
            $character->items
                ->filter(
                    function ($item) {
                        if (!(bool) $item->equipped) {
                            return false;
                        }

                        $properties =
                            is_array(
                                $item->properties
                            )
                                ? $item->properties
                                : [];

                        return is_array(
                            $properties['armor']
                            ?? null
                        );
                    }
                )
                ->map(
                    function ($item) use (
                        $modifierByLongKey
                    ): int {
                        $properties =
                            is_array(
                                $item->properties
                            )
                                ? $item->properties
                                : [];

                        $armor =
                            is_array(
                                $properties['armor']
                                ?? null
                            )
                                ? $properties['armor']
                                : [];

                        $baseAc =
                            (int) (
                                $armor['base_ac']
                                ?? 10
                            );

                        $magicBonus =
                            (int) (
                                $armor['magic_bonus']
                                ?? 0
                            );

                        $dexterityMode =
                            (string) (
                                $armor['dexterity_mode']
                                ?? match (
                                    $armor['category']
                                    ?? null
                                ) {
                                    'light' =>
                                        'full',

                                    'medium' =>
                                        'capped',

                                    default =>
                                        'none',
                                }
                            );

                        $dexterityModifier =
                            (int) (
                                $modifierByLongKey[
                                    'dexterity'
                                ]
                                ?? 0
                            );

                        $dexterityContribution =
                            match ($dexterityMode) {
                                'full' =>
                                    $dexterityModifier,

                                'capped' =>
                                    min(
                                        $dexterityModifier,
                                        max(
                                            0,
                                            (int) (
                                                $armor[
                                                    'dexterity_cap'
                                                ]
                                                ?? 2
                                            )
                                        )
                                    ),

                                default =>
                                    0,
                            };

                        $extraAbilityBonus =
                            collect(
                                is_array(
                                    $armor[
                                        'ability_modifiers'
                                    ]
                                    ?? null
                                )
                                    ? $armor[
                                        'ability_modifiers'
                                    ]
                                    : []
                            )
                                ->sum(
                                    fn ($ability) =>
                                        (int) (
                                            $modifierByLongKey[
                                                (string) $ability
                                            ]
                                            ?? 0
                                        )
                                );

                        return
                            $baseAc
                            +
                            $dexterityContribution
                            +
                            $extraAbilityBonus
                            +
                            $magicBonus;
                    }
                )
                ->values();

        $bodyAc =
            max(
                $baseBodyAc,
                (int) (
                    $equippedArmorValues
                        ->max()
                    ?? 0
                )
            );

        /*
        | Escudo vem dos itens equipados.
        | Não empilhamos vários escudos: usamos o maior bônus equipado.
        */
        $equippedShieldBonus =
            (int) (
                $character->items
                    ->filter(
                        function ($item) {
                            if (!(bool) $item->equipped) {
                                return false;
                            }

                            $properties =
                                is_array(
                                    $item->properties
                                )
                                    ? $item->properties
                                    : [];

                            return is_array(
                                $properties['shield']
                                ?? null
                            );
                        }
                    )
                    ->map(
                        function ($item): int {
                            $properties =
                                is_array(
                                    $item->properties
                                )
                                    ? $item->properties
                                    : [];

                            $shield =
                                is_array(
                                    $properties['shield']
                                    ?? null
                                )
                                    ? $properties['shield']
                                    : [];

                            return
                                (int) (
                                    $shield['ac_bonus']
                                    ?? 2
                                )
                                +
                                (int) (
                                    $shield['magic_bonus']
                                    ?? 0
                                );
                        }
                    )
                    ->max()
                ?? 0
            );

        /*
        | Compatibilidade com fichas antigas que ainda usam o escudo manual.
        | Só entra quando não há escudo-item equipado, evitando dupla contagem.
        */
        $legacyShieldBonus =
            $equippedShieldBonus > 0
                ? 0
                : (
                    (bool) (
                        $armorConfig[
                            'shield_enabled'
                        ]
                        ?? false
                    )
                        ? (int) (
                            $armorConfig[
                                'shield_bonus'
                            ]
                            ?? 2
                        )
                        : 0
                );

        $namedBonusTotal =
            collect(
                $armorConfig['bonuses']
                ?? []
            )
                ->sum(
                    fn ($bonus) =>
                        (int) (
                            $bonus['value']
                            ?? 0
                        )
                );

        return
            $bodyAc
            +
            $equippedShieldBonus
            +
            $legacyShieldBonus
            +
            $namedBonusTotal;
    }


    private function inventoryPayload(
        Character $character
    ): array {
        return $character->items
            ->sortBy([
                [
                    'equipped',
                    'desc',
                ],
                [
                    'name',
                    'asc',
                ],
            ])
            ->map(
                function ($item) use (
                    $character
                ): array {
                    $properties =
                        is_array(
                            $item->properties
                        )
                            ? $item->properties
                            : [];

                    $armor =
                        is_array(
                            $properties['armor']
                            ?? null
                        )
                            ? $properties['armor']
                            : null;

                    $shield =
                        is_array(
                            $properties['shield']
                            ?? null
                        )
                            ? $properties['shield']
                            : null;

                    $weapon =
                        is_array(
                            $properties['weapon']
                            ?? null
                        )
                            ? $properties['weapon']
                            : null;

                    $kind =
                        match (true) {
                            $armor !== null =>
                                trim(
                                    (string) (
                                        $armor['armor_type']
                                        ?? ''
                                    )
                                )
                                ?: 'Armadura',

                            $shield !== null =>
                                trim(
                                    (string) (
                                        $shield['label']
                                        ?? ''
                                    )
                                )
                                ?: 'Escudo',

                            $weapon !== null =>
                                trim(
                                    (string) (
                                        $weapon['weapon_type']
                                        ?? ''
                                    )
                                )
                                ?: 'Arma',

                            default =>
                                match (
                                    (string) (
                                        $item->type
                                        ?? 'mundane'
                                    )
                                ) {
                                    'wonderful' =>
                                        'Item Maravilhoso',

                                    'technological' =>
                                        'Item Tecnológico',

                                    default =>
                                        'Item',
                                },
                        };

                    $rarityLabel =
                        match (
                            (string) (
                                $item->rarity
                                ?? ''
                            )
                        ) {
                            'common' =>
                                'Comum',

                            'uncommon' =>
                                'Incomum',

                            'rare' =>
                                'Raro',

                            'very_rare' =>
                                'Muito Raro',

                            'legendary' =>
                                'Lendário',

                            'artifact' =>
                                'Artefato',

                            default =>
                                null,
                        };

                    $attunementLabel =
                        (bool) (
                            $item->requires_attunement
                            ?? false
                        )
                            ? (
                                (bool) (
                                    $item->attuned
                                    ?? false
                                )
                                    ? 'Sintonizado'
                                    : 'Sintonizável'
                            )
                            : null;

                    $imageUrl =
                        is_string(
                            $item->image_path
                        )
                        &&
                        trim(
                            $item->image_path
                        )
                        !==
                        ''
                            ? route(
                                'characters.items.image',
                                [
                                    'character' =>
                                        $character->id,

                                    'item' =>
                                        $item->id,
                                ],
                                false
                            )
                            : null;

                    return [
                        'id' =>
                            (int) $item->id,

                        'name' =>
                            (string) $item->name,

                        'type' =>
                            (string) (
                                $item->type
                                ?? 'mundane'
                            ),

                        'kind' =>
                            $kind,

                        'quantity' =>
                            max(
                                1,
                                (int) (
                                    $item->quantity
                                    ?? 1
                                )
                            ),

                        'equipped' =>
                            (bool) (
                                $item->equipped
                                ?? false
                            ),

                        'is_magical' =>
                            (bool) (
                                $item->is_magical
                                ?? false
                            ),

                        'rarity' =>
                            $item->rarity
                                ? (string) $item->rarity
                                : null,

                        'rarity_label' =>
                            $rarityLabel,

                        'requires_attunement' =>
                            (bool) (
                                $item->requires_attunement
                                ?? false
                            ),

                        'attuned' =>
                            (bool) (
                                $item->attuned
                                ?? false
                            ),

                        'attunement_label' =>
                            $attunementLabel,

                        'weight' =>
                            $item->weight
                                !==
                                null
                                    ? (float) $item->weight
                                    : null,

                        'description' =>
                            trim(
                                (string) (
                                    $item->description
                                    ?? ''
                                )
                            ),

                        'image_url' =>
                            $imageUrl,
                    ];
                }
            )
            ->values()
            ->all();
    }


    private function normalizedSettings(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}