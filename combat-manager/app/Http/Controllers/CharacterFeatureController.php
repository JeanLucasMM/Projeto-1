<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CharacterFeatureController extends Controller
{
    private const TYPES = [
        'class_feature',
        'species_trait',
        'feat',

        /*
        |--------------------------------------------------------------------------
        | Treinamentos & Proficiências
        |--------------------------------------------------------------------------
        */

        'armor_training',
        'weapon_training',
        'tool_proficiency',
        'language',
        'vehicle_proficiency',

        'custom',
    ];

    private const ACTIVATIONS = [
        'passive',
        'action',
        'bonus_action',
        'reaction',
        'special',
    ];

    private const COUNTER_MODES = [
        'spend',
        'build',
    ];

    private const COLUMNS = [
        'left',
        'right',
    ];

    /*
    |--------------------------------------------------------------------------
    | Atributos para testes de ferramentas / veículos
    |--------------------------------------------------------------------------
    */

    private const ROLL_ABILITIES = [
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
        'day',
        'dawn',
        'single_use',
        'custom',
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
            $request,
            $character
        );

        $validated = $request->validate(
            $this->validationRules(
                creating: true
            )
        );

        $payload = $this->normalizePayload(
            $validated
        );

        $feature = $character
            ->features()
            ->create($payload);

        $feature->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Habilidade adicionada.',
            'feature' => $this->featurePayload($feature),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Editar
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Character $character,
        CharacterFeature $feature
    ): JsonResponse {
        $this->authorizeFeature(
            $request,
            $character,
            $feature
        );

        $validated = $request->validate(
            $this->validationRules(
                creating: false
            )
        );

        $payload = $this->normalizePayload(
            $validated,
            $feature
        );

        $feature->update($payload);
        $feature->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Habilidade atualizada.',
            'feature' => $this->featurePayload($feature),
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
        CharacterFeature $feature
    ): JsonResponse {
        $this->authorizeFeature(
            $request,
            $character,
            $feature
        );

        $id = $feature->id;

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Habilidade removida.',
            'deleted_id' => $id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Atualizar apenas o rastreador
    |--------------------------------------------------------------------------
    */

    public function updateUses(
        Request $request,
        Character $character,
        CharacterFeature $feature
    ): JsonResponse {
        $this->authorizeFeature(
            $request,
            $character,
            $feature
        );

        abort_unless(
            $feature->uses_max !== null,
            422,
            'Esta habilidade não possui Rastreador.'
        );

        $validated = $request->validate([
            'current' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $maximum = max(
            1,
            (int) $feature->uses_max
        );

        $current = min(
            $maximum,
            max(
                0,
                (int) $validated['current']
            )
        );

        $feature->update([
            'uses_current' => $current,
        ]);

        $feature->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Usos atualizados.',
            'feature' => $this->featurePayload($feature),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validação
    |--------------------------------------------------------------------------
    */

    private function validationRules(
        bool $creating
    ): array {
        return [
            'name' => [
                $creating
                    ? 'required'
                    : 'sometimes',
                'string',
                'max:120',
            ],

            'type' => [
                'sometimes',
                'nullable',
                Rule::in(self::TYPES),
            ],

            'source' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'level_acquired' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:30000',
            ],

            'uses_max' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],

            'uses_current' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],

            'recovery' => [
                'sometimes',
                'nullable',
                Rule::in(self::RECOVERIES),
            ],

            'data' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'data.activation' => [
                'sometimes',
                'nullable',
                Rule::in(self::ACTIVATIONS),
            ],

            'data.quick_text' => [
                'sometimes',
                'nullable',
                'string',
                'max:180',
            ],

            'data.counter_mode' => [
                'sometimes',
                'nullable',
                Rule::in(self::COUNTER_MODES),
            ],

            'data.column' => [
                'sometimes',
                'nullable',
                Rule::in(self::COLUMNS),
            ],

            'data.recovery_custom' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Ferramentas / veículos
            |--------------------------------------------------------------------------
            |
            | Validamos explicitamente a chave para que ela faça parte do
            | payload validado e seja persistida em data sem ambiguidade.
            |
            */

            'data.roll_ability' => [
                'sometimes',
                'nullable',
                Rule::in(self::ROLL_ABILITIES),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Normalização
    |--------------------------------------------------------------------------
    */

    private function normalizePayload(
        array $validated,
        ?CharacterFeature $existing = null
    ): array {
        $existingData = is_array(
            $existing?->data
        )
            ? $existing->data
            : [];

        $incomingData = array_key_exists(
            'data',
            $validated
        )
            && is_array($validated['data'])
                ? $validated['data']
                : [];

        /*
        | Preservamos chaves futuras/customizadas que já existam em data.
        */
        $data = array_merge(
            $existingData,
            $incomingData
        );

        $type = $validated['type']
            ?? $existing?->type
            ?? 'class_feature';

        if (
            !in_array(
                $type,
                self::TYPES,
                true
            )
        ) {
            $type = 'class_feature';
        }

        $activation = $data['activation']
            ?? 'passive';

        if (
            !in_array(
                $activation,
                self::ACTIVATIONS,
                true
            )
        ) {
            $activation = 'passive';
        }

        $counterMode = (
            $data['counter_mode']
            ?? 'spend'
        ) === 'build'
            ? 'build'
            : 'spend';

        $quickText = trim(
            (string) (
                $data['quick_text']
                ?? ''
            )
        );

        $column = (
            $data['column']
            ?? 'left'
        ) === 'right'
            ? 'right'
            : 'left';

        $data['activation'] = $activation;
        $data['counter_mode'] = $counterMode;
        $data['column'] = $column;
        $data['quick_text'] = $quickText !== ''
            ? $quickText
            : null;

        /*
        |--------------------------------------------------------------------------
        | Atributo de rolagem
        |--------------------------------------------------------------------------
        |
        | Mantemos a chave apenas quando ela existir no registro.
        | Isso evita poluir Habilidades, Talentos e Traços com roll_ability.
        |
        */

        if (
            array_key_exists(
                'roll_ability',
                $data
            )
        ) {
            $rollAbility =
                $data['roll_ability'];

            $data['roll_ability'] =
                in_array(
                    $rollAbility,
                    self::ROLL_ABILITIES,
                    true
                )
                    ? $rollAbility
                    : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Rastreador
        |--------------------------------------------------------------------------
        */

        $usesMax = array_key_exists(
            'uses_max',
            $validated
        )
            ? $validated['uses_max']
            : $existing?->uses_max;

        if ($usesMax === null) {
            $usesCurrent = null;
            $recovery = null;
            $data['recovery_custom'] = null;
        } else {
            $usesMax = max(
                1,
                (int) $usesMax
            );

            if (
                array_key_exists(
                    'uses_current',
                    $validated
                )
                && $validated['uses_current'] !== null
            ) {
                $usesCurrent = (int) $validated['uses_current'];
            } elseif (
                $existing
                && $existing->uses_current !== null
            ) {
                $usesCurrent = (int) $existing->uses_current;
            } else {
                $usesCurrent = $counterMode === 'build'
                    ? 0
                    : $usesMax;
            }

            $usesCurrent = min(
                $usesMax,
                max(
                    0,
                    $usesCurrent
                )
            );

            $recovery = $validated['recovery']
                ?? $existing?->recovery
                ?? 'none';

            if (
                !in_array(
                    $recovery,
                    self::RECOVERIES,
                    true
                )
            ) {
                $recovery = 'none';
            }

            if ($recovery === 'custom') {
                $recoveryCustom = trim(
                    (string) (
                        $data['recovery_custom']
                        ?? ''
                    )
                );

                $data['recovery_custom'] =
                    $recoveryCustom !== ''
                        ? $recoveryCustom
                        : null;
            } else {
                $data['recovery_custom'] = null;
            }
        }

        return [
            'name' => trim(
                (string) (
                    $validated['name']
                    ?? $existing?->name
                    ?? ''
                )
            ),

            'type' => $type,

            'source' => $this->nullableTrimmed(
                $validated['source']
                ?? $existing?->source
            ),

            'level_acquired' => array_key_exists(
                'level_acquired',
                $validated
            )
                ? (
                    $validated['level_acquired'] !== null
                        ? (int) $validated['level_acquired']
                        : null
                )
                : $existing?->level_acquired,

            'description' => $this->nullableTrimmed(
                $validated['description']
                ?? $existing?->description
            ),

            'uses_max' => $usesMax,
            'uses_current' => $usesCurrent,
            'recovery' => $recovery,

            'data' => $data,
        ];
    }

    private function nullableTrimmed(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value !== ''
            ? $value
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Payload
    |--------------------------------------------------------------------------
    */

    private function featurePayload(
        CharacterFeature $feature
    ): array {
        return [
            'id' => $feature->id,
            'character_id' => $feature->character_id,

            'name' => $feature->name,
            'type' => $feature->type,
            'source' => $feature->source,
            'level_acquired' => $feature->level_acquired,
            'description' => $feature->description,

            'uses_max' => $feature->uses_max,
            'uses_current' => $feature->uses_current,
            'recovery' => $feature->recovery,

            'data' => is_array($feature->data)
                ? $feature->data
                : [],

            'created_at' => $feature->created_at?->toISOString(),
            'updated_at' => $feature->updated_at?->toISOString(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Autorização
    |--------------------------------------------------------------------------
    */

    private function authorizeCharacter(
        Request $request,
        Character $character
    ): void {
        abort_unless(
            $request->user()
            && (int) $character->user_id
                === (int) $request->user()->id,
            403
        );
    }

    private function authorizeFeature(
        Request $request,
        Character $character,
        CharacterFeature $feature
    ): void {
        $this->authorizeCharacter(
            $request,
            $character
        );

        abort_unless(
            (int) $feature->character_id
                === (int) $character->id,
            404
        );
    }
}