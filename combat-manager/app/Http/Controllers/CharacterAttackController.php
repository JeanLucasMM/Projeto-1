<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterAttack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CharacterAttackController extends Controller
{
    private const ABILITIES = [
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
    ];

    private const RECOVERIES = [
        'none',
        'short_rest',
        'long_rest',
    ];

    private const COUNTER_MODES = [
        'spend',
        'build',
    ];

    /*
    |--------------------------------------------------------------------------
    | Criar
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $character
        );

        $validated =
            $this->validateAttack(
                $request
            );

        $payload =
            $this->normalizePayload(
                $validated
            );

        if (
            !array_key_exists(
                'sort_order',
                $payload
            )
        ) {
            $payload['sort_order'] =
                (int) (
                    $character
                        ->attacks()
                        ->max('sort_order')
                    ?? -1
                ) + 1;
        }

        $attack =
            $character
                ->attacks()
                ->create(
                    $payload
                );

        return response()->json([
            'success' => true,
            'attack' => $attack->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Character $character,
        CharacterAttack $attack
    ): JsonResponse {
        $this->authorizeAttack(
            $character,
            $attack
        );

        $validated =
            $this->validateAttack(
                $request
            );

        $payload =
            $this->normalizePayload(
                $validated,
                $attack
            );

        $attack->update(
            $payload
        );

        return response()->json([
            'success' => true,
            'attack' => $attack->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Excluir
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Character $character,
        CharacterAttack $attack
    ): JsonResponse {
        $this->authorizeAttack(
            $character,
            $attack
        );

        $attack->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar somente o rastreador
    |--------------------------------------------------------------------------
    |
    | Funciona tanto para:
    |
    | spend  → 5/5, 4/5, 3/5...
    | build  → 0/4, 1/4, 2/4...
    |
    */

    public function updateUses(
        Request $request,
        Character $character,
        CharacterAttack $attack
    ): JsonResponse {
        $this->authorizeAttack(
            $character,
            $attack
        );

        abort_unless(
            $attack->uses_max !== null,
            422,
            'Este ataque não possui rastreador.'
        );

        $validated =
            $request->validate([
                'uses_current' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ]);

        $maximum =
            max(
                0,
                (int) $attack->uses_max
            );

        $current =
            min(
                $maximum,
                max(
                    0,
                    (int) $validated[
                        'uses_current'
                    ]
                )
            );

        $attack->update([
            'uses_current' =>
                $current,
        ]);

        return response()->json([
            'success' => true,

            'uses_current' =>
                $current,

            'uses_max' =>
                $maximum,

            'attack' =>
                $attack->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validação
    |--------------------------------------------------------------------------
    */

    private function validateAttack(
        Request $request
    ): array {
        return $request->validate([
            /*
            | Identificação
            */

            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'effect' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            /*
            | Acerto
            */

            'attack_ability' => [
                'nullable',
                'string',
                Rule::in(
                    self::ABILITIES
                ),
            ],

            'use_proficiency' => [
                'required',
                'boolean',
            ],

            'attack_bonus' => [
                'required',
                'integer',
                'between:-999,999',
            ],

            /*
            | Compatibilidade de dano antigo
            |--------------------------------------------------------------------------
            |
            | Continuam existindo para não quebrar registros antigos.
            | Novos ataques usam data.damage_parts.
            |
            */

            'damage' => [
                'nullable',
                'string',
                'max:100',
            ],

            'damage_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'damage_abilities' => [
                'nullable',
                'array',
            ],

            'damage_abilities.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(
                    self::ABILITIES
                ),
            ],

            'damage_bonus' => [
                'required',
                'integer',
                'between:-999,999',
            ],

            /*
            | Rastreador
            */

            'uses_current' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'uses_max' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'recovery' => [
                'nullable',
                'string',
                Rule::in(
                    self::RECOVERIES
                ),
            ],

            /*
            | Organização
            */

            'visible' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
            | Dados extras
            */

            'data' => [
                'nullable',
                'array',
            ],

            /*
            | Alcance
            */

            'data.range' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            | Maestrias
            */

            'data.masteries' => [
                'nullable',
                'array',
                'max:20',
            ],

            'data.masteries.*' => [
                'required',
                'string',
                'max:50',
                'distinct',
            ],

            /*
            | Tipo do rastreador
            */

            'data.counter_mode' => [
                'nullable',
                'string',
                Rule::in(
                    self::COUNTER_MODES
                ),
            ],

            /*
            | Partes de dano
            |
            | Exemplo:
            |
            | [
            |   {
            |       "expression": "1d8",
            |       "type": "piercing",
            |       "abilities": ["dexterity"],
            |       "bonus": 0
            |   },
            |   {
            |       "expression": "1d8",
            |       "type": "radiant",
            |       "abilities": [],
            |       "bonus": 0
            |   }
            | ]
            |
            */

            'data.damage_parts' => [
                'nullable',
                'array',
                'max:20',
            ],

            'data.damage_parts.*.id' => [
                'nullable',
                'string',
                'max:80',
            ],

            'data.damage_parts.*.expression' => [
                'nullable',
                'string',
                'max:100',
            ],

            'data.damage_parts.*.type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'data.damage_parts.*.abilities' => [
                'nullable',
                'array',
            ],

            'data.damage_parts.*.abilities.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(
                    self::ABILITIES
                ),
            ],

            'data.damage_parts.*.bonus' => [
                'nullable',
                'integer',
                'between:-999,999',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Normalização
    |--------------------------------------------------------------------------
    */

    private function normalizePayload(
        array $validated,
        ?CharacterAttack $attack = null
    ): array {
        $validated['attack_ability'] =
            $validated['attack_ability']
            ?: null;

        $validated['damage_abilities'] =
            array_values(
                array_unique(
                    $validated[
                        'damage_abilities'
                    ]
                    ?? []
                )
            );

        $data =
            is_array(
                $validated['data']
                ?? null
            )
                ? $validated['data']
                : [];

        /*
        |--------------------------------------------------------------------------
        | Alcance
        |--------------------------------------------------------------------------
        */

        $range =
            trim(
                (string) (
                    $data['range']
                    ?? ''
                )
            );

        $data['range'] =
            $range !== ''
                ? $range
                : null;

        /*
        |--------------------------------------------------------------------------
        | Maestrias
        |--------------------------------------------------------------------------
        */

        $masteries =
            $data['masteries']
            ?? [];

        $data['masteries'] =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            static fn ($value) =>
                                trim(
                                    (string) $value
                                ),
                            is_array($masteries)
                                ? $masteries
                                : []
                        ),
                        static fn ($value) =>
                            $value !== ''
                    )
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Tipo de contador
        |--------------------------------------------------------------------------
        */

        $counterMode =
            (string) (
                $data['counter_mode']
                ?? 'spend'
            );

        if (
            !in_array(
                $counterMode,
                self::COUNTER_MODES,
                true
            )
        ) {
            $counterMode =
                'spend';
        }

        $data['counter_mode'] =
            $counterMode;

        /*
        |--------------------------------------------------------------------------
        | Danos separados
        |--------------------------------------------------------------------------
        */

        $damageParts =
            $data['damage_parts']
            ?? [];

        /*
        | Compatibilidade:
        | se não vier damage_parts, converte os campos antigos em uma parte.
        */

        if (
            !is_array(
                $damageParts
            )
            ||
            count(
                $damageParts
            ) === 0
        ) {
            $legacyDamage =
                trim(
                    (string) (
                        $validated['damage']
                        ?? ''
                    )
                );

            $legacyType =
                trim(
                    (string) (
                        $validated[
                            'damage_type'
                        ]
                        ?? ''
                    )
                );

            $legacyAbilities =
                $validated[
                    'damage_abilities'
                ]
                ?? [];

            $legacyBonus =
                (int) (
                    $validated[
                        'damage_bonus'
                    ]
                    ?? 0
                );

            if (
                $legacyDamage !== ''
                ||
                $legacyType !== ''
                ||
                count(
                    $legacyAbilities
                ) > 0
                ||
                $legacyBonus !== 0
            ) {
                $damageParts = [
                    [
                        'id' =>
                            (string) Str::uuid(),

                        'expression' =>
                            $legacyDamage,

                        'type' =>
                            $legacyType !== ''
                                ? $legacyType
                                : null,

                        'abilities' =>
                            $legacyAbilities,

                        'bonus' =>
                            $legacyBonus,
                    ],
                ];
            } else {
                $damageParts = [];
            }
        }

        $normalizedParts = [];

        foreach (
            $damageParts as
            $part
        ) {
            if (
                !is_array(
                    $part
                )
            ) {
                continue;
            }

            $expression =
                trim(
                    (string) (
                        $part['expression']
                        ?? ''
                    )
                );

            $type =
                trim(
                    (string) (
                        $part['type']
                        ?? ''
                    )
                );

            $abilities =
                array_values(
                    array_unique(
                        array_filter(
                            $part['abilities']
                            ?? [],
                            static fn ($ability) =>
                                in_array(
                                    $ability,
                                    self::ABILITIES,
                                    true
                                )
                        )
                    )
                );

            $bonus =
                (int) (
                    $part['bonus']
                    ?? 0
                );

            /*
            | Não salva linhas completamente vazias.
            */

            if (
                $expression === ''
                &&
                $type === ''
                &&
                count(
                    $abilities
                ) === 0
                &&
                $bonus === 0
            ) {
                continue;
            }

            $normalizedParts[] = [
                'id' =>
                    trim(
                        (string) (
                            $part['id']
                            ?? ''
                        )
                    )
                    ?: (string) Str::uuid(),

                'expression' =>
                    $expression,

                'type' =>
                    $type !== ''
                        ? $type
                        : null,

                'abilities' =>
                    $abilities,

                'bonus' =>
                    $bonus,
            ];
        }

        $data['damage_parts'] =
            $normalizedParts;

        /*
        | Mantemos a primeira parte nos campos antigos.
        | Isso garante compatibilidade com qualquer código legado.
        */

        $firstDamage =
            $normalizedParts[0]
            ?? null;

        $validated['damage'] =
            $firstDamage[
                'expression'
            ]
            ?? null;

        $validated['damage_type'] =
            $firstDamage[
                'type'
            ]
            ?? null;

        $validated['damage_abilities'] =
            $firstDamage[
                'abilities'
            ]
            ?? [];

        $validated['damage_bonus'] =
            (int) (
                $firstDamage[
                    'bonus'
                ]
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | Rastreador
        |--------------------------------------------------------------------------
        */

        if (
            $validated['uses_max'] === null
        ) {
            $validated['uses_current'] =
                null;

            $validated['recovery'] =
                null;

            $validated['data'] =
                $data;

            return $validated;
        }

        $maximum =
            max(
                1,
                (int) $validated[
                    'uses_max'
                ]
            );

        if (
            $validated['uses_current'] ===
                null
        ) {
            if (
                $attack &&
                $attack->uses_current !== null
            ) {
                $current =
                    (int) $attack
                        ->uses_current;
            } else {
                /*
                | Tracker de gasto começa cheio.
                | Contador progressivo começa em zero.
                */

                $current =
                    $counterMode === 'build'
                        ? 0
                        : $maximum;
            }
        } else {
            $current =
                (int) $validated[
                    'uses_current'
                ];
        }

        $validated['uses_max'] =
            $maximum;

        $validated['uses_current'] =
            min(
                $maximum,
                max(
                    0,
                    $current
                )
            );

        $validated['recovery'] =
            $validated['recovery']
            ?: 'none';

        $validated['data'] =
            $data;

        return $validated;
    }

    /*
    |--------------------------------------------------------------------------
    | Autorização
    |--------------------------------------------------------------------------
    */

    private function authorizeCharacter(
        Character $character
    ): void {
        Gate::authorize(
            'update',
            $character
        );
    }

    private function authorizeAttack(
        Character $character,
        CharacterAttack $attack
    ): void {
        $this->authorizeCharacter(
            $character
        );

        abort_unless(
            (int) $attack->character_id ===
                (int) $character->id,
            404
        );
    }
}