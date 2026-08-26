<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CharacterSheetStatsController extends Controller
{
    private const ABILITIES = [
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
    ];

    private const SKILLS = [
        'acrobatics' => 'dexterity',
        'animal_handling' => 'wisdom',
        'arcana' => 'intelligence',
        'athletics' => 'strength',
        'deception' => 'charisma',
        'history' => 'intelligence',
        'insight' => 'wisdom',
        'intimidation' => 'charisma',
        'investigation' => 'intelligence',
        'medicine' => 'wisdom',
        'nature' => 'intelligence',
        'perception' => 'wisdom',
        'performance' => 'charisma',
        'persuasion' => 'charisma',
        'religion' => 'intelligence',
        'sleight_of_hand' => 'dexterity',
        'stealth' => 'dexterity',
        'survival' => 'wisdom',
    ];

    public function updateAbility(
        Request $request,
        Character $character,
        string $ability
    ): JsonResponse {
        abort_unless(
            $character->user_id === $request->user()->id,
            403
        );

        abort_unless(
            in_array(
                $ability,
                self::ABILITIES,
                true
            ),
            404
        );

        $allowedSkills = collect(self::SKILLS)
            ->filter(
                fn (string $skillAbility) =>
                    $skillAbility === $ability
            )
            ->keys()
            ->values()
            ->all();

        $validated = $request->validate([
            'ability' => [
                'required',
                'array',
            ],

            'ability.score' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],

            'ability.temporary_bonus' => [
                'required',
                'integer',
                'between:-999,999',
            ],

            'ability.override' => [
                'nullable',
                'integer',
                'min:1',
                'max:999',
            ],

            'saving_throw' => [
                'required',
                'array',
            ],

            'saving_throw.proficient' => [
                'required',
                'boolean',
            ],

            'saving_throw.bonus_override' => [
                'nullable',
                'integer',
                'between:-999,999',
            ],

            'saving_throw.temporary_bonus' => [
                'required',
                'integer',
                'between:-999,999',
            ],

            'skills' => [
                'present',
                'array',
            ],

            'skills.*.skill' => [
                'required',
                'string',
                Rule::in($allowedSkills),
            ],

            'skills.*.proficient' => [
                'required',
                'boolean',
            ],

            'skills.*.expertise' => [
                'required',
                'boolean',
            ],

            'skills.*.bonus_override' => [
                'nullable',
                'integer',
                'between:-999,999',
            ],

            'skills.*.temporary_bonus' => [
                'required',
                'integer',
                'between:-999,999',
            ],
        ]);

        $result = DB::transaction(
            function () use (
                $character,
                $ability,
                $allowedSkills,
                $validated
            ) {
                /*
                |--------------------------------------------------------------------------
                | ATRIBUTO
                |--------------------------------------------------------------------------
                */

                $abilities = $character
                    ->abilities()
                    ->firstOrCreate(
                        [],
                        [
                            'strength' => 10,
                            'dexterity' => 10,
                            'constitution' => 10,
                            'intelligence' => 10,
                            'wisdom' => 10,
                            'charisma' => 10,
                            'temporary_bonuses' => [],
                            'overrides' => [],
                        ]
                    );

                $temporaryBonuses =
                    is_array($abilities->temporary_bonuses)
                        ? $abilities->temporary_bonuses
                        : [];

                $overrides =
                    is_array($abilities->overrides)
                        ? $abilities->overrides
                        : [];

                $temporaryBonuses[$ability] =
                    (int) $validated['ability']['temporary_bonus'];

                if (
                    $validated['ability']['override'] === null
                ) {
                    unset(
                        $overrides[$ability]
                    );
                } else {
                    $overrides[$ability] =
                        (int) $validated['ability']['override'];
                }

                $abilities->forceFill([
                    $ability =>
                        (int) $validated['ability']['score'],

                    'temporary_bonuses' =>
                        $temporaryBonuses,

                    'overrides' =>
                        $overrides,
                ]);

                $abilities->save();

                /*
                |--------------------------------------------------------------------------
                | SALVAGUARDA
                |--------------------------------------------------------------------------
                */

                $savingThrow = $character
                    ->savingThrows()
                    ->updateOrCreate(
                        [
                            'ability' =>
                                $ability,
                        ],
                        [
                            'proficient' =>
                                (bool) $validated[
                                    'saving_throw'
                                ]['proficient'],

                            'bonus_override' =>
                                $validated[
                                    'saving_throw'
                                ]['bonus_override'],

                            'temporary_bonus' =>
                                (int) $validated[
                                    'saving_throw'
                                ]['temporary_bonus'],
                        ]
                    );

                /*
                |--------------------------------------------------------------------------
                | PERÍCIAS DO ATRIBUTO
                |--------------------------------------------------------------------------
                */

                $submittedSkills = collect(
                    $validated['skills'] ?? []
                )->keyBy('skill');

                $skills = collect(
                    $allowedSkills
                )->map(
                    function (
                        string $skillKey
                    ) use (
                        $character,
                        $submittedSkills
                    ) {
                        $data =
                            $submittedSkills->get(
                                $skillKey,
                                [
                                    'skill' =>
                                        $skillKey,

                                    'proficient' =>
                                        false,

                                    'expertise' =>
                                        false,

                                    'bonus_override' =>
                                        null,

                                    'temporary_bonus' =>
                                        0,
                                ]
                            );

                        $expertise =
                            (bool) (
                                $data['expertise']
                                ?? false
                            );

                        return $character
                            ->skills()
                            ->updateOrCreate(
                                [
                                    'skill' =>
                                        $skillKey,
                                ],
                                [
                                    'proficient' =>
                                        $expertise
                                            ? true
                                            : (bool) (
                                                $data[
                                                    'proficient'
                                                ]
                                                ?? false
                                            ),

                                    'expertise' =>
                                        $expertise,

                                    'bonus_override' =>
                                        $data[
                                            'bonus_override'
                                        ]
                                        ?? null,

                                    'temporary_bonus' =>
                                        (int) (
                                            $data[
                                                'temporary_bonus'
                                            ]
                                            ?? 0
                                        ),
                                ]
                            );
                    }
                );

                return [
                    'ability' =>
                        $abilities->fresh(),

                    'saving_throw' =>
                        $savingThrow->fresh(),

                    'skills' =>
                        $skills
                            ->map(
                                fn ($skill) =>
                                    $skill->fresh()
                            )
                            ->values(),
                ];
            }
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}