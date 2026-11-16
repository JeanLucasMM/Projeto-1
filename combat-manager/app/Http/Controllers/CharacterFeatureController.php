<?php
namespace App\Http\Controllers;
use App\Models\Character;
use App\Models\CharacterFeature;
use App\Models\CharacterFeatureEffectState;
use App\Services\Characters\CharacterFeatureEffectService;
use App\Services\Characters\CharacterRoundTrackerService;
use App\Services\Characters\CharacterConcentrationManager;
use App\Services\Characters\ConcentrationCheckService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
        DB::transaction(function () use ($character, $feature): void {
            $character = app(CharacterConcentrationManager::class)->lockCharacter($character);
            app(CharacterFeatureEffectService::class)->stop($character, $feature);
            $feature->delete();
        });
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
    | EFEITOS TEMPORÁRIOS (rodadas / concentração)
    |--------------------------------------------------------------------------
    */

    public function effectState(Request $request, Character $character): JsonResponse
    {
        $this->authorizeCharacter($request, $character);
        return response()->json([
            'success' => true,
            'effects' => app(CharacterFeatureEffectService::class)->activeFor($character),
            'concentration' => app(CharacterConcentrationManager::class)->currentConcentration($character),
        ]);
    }

    public function activateEffect(Request $request, Character $character, CharacterFeature $feature): JsonResponse
    {
        $this->authorizeFeature($request, $character, $feature);
        $validated = $request->validate([
            'confirm_replace_concentration' => ['sometimes', 'boolean'],
        ]);
        $result = DB::transaction(function () use ($character, $feature, $validated): array {
            $character = app(CharacterConcentrationManager::class)->lockCharacter($character);
            $feature = CharacterFeature::query()->where('character_id', $character->id)
                ->whereKey($feature->id)->lockForUpdate()->firstOrFail();
            return app(CharacterFeatureEffectService::class)->activate(
                $character, $feature, (bool) ($validated['confirm_replace_concentration'] ?? false)
            );
        });
        return response()->json(['success' => true, ...$result, 'message' => 'Habilidade ativada.']);
    }

    public function stopEffect(Request $request, Character $character, CharacterFeature $feature): JsonResponse
    {
        $this->authorizeFeature($request, $character, $feature);
        DB::transaction(function () use ($character, $feature): void {
            $character = app(CharacterConcentrationManager::class)->lockCharacter($character);
            app(CharacterFeatureEffectService::class)->stop($character, $feature);
        });
        return response()->json(['success' => true, 'message' => 'Efeito encerrado.']);
    }

    /** Avanço manual apenas fora de combate; não interfere na iniciativa. */
    public function advanceEffectsRound(Request $request, Character $character): JsonResponse
    {
        $this->authorizeCharacter($request, $character);
        $validated = $request->validate([
            'rounds' => ['required', 'integer', Rule::in([1, 3, 10])],
        ]);
        return response()->json(
            app(CharacterRoundTrackerService::class)->advanceOutsideCombat(
                $character, (int) $validated['rounds']
            )
        );
    }

    public function effectConcentrationCheck(
        Request $request, Character $character, CharacterFeature $feature
    ): JsonResponse {
        $this->authorizeFeature($request, $character, $feature);
        $validated = $request->validate([
            'damage' => ['required', 'integer', 'min:1', 'max:1000000'],
            'roll_total' => ['sometimes', 'nullable', 'integer', 'min:-1000', 'max:1000'],
        ]);
        $active = CharacterFeatureEffectState::query()
            ->where('character_id', $character->id)
            ->where('character_feature_id', $feature->id)
            ->where('is_active', true)->where('requires_concentration', true)->exists();
        $concentration = app(CharacterConcentrationManager::class)->currentConcentration($character);
        if (!$active || ($concentration['source'] ?? null) !== 'feature'
            || (int) ($concentration['id'] ?? 0) !== (int) $feature->id) {
            throw ValidationException::withMessages(['concentration' => 'A habilidade não está concentrada.']);
        }
        $combat = $character->combat()->first();
        $rule = (bool) data_get($character->sheet_settings, 'optional_rules.exhaustion', false);
        $exhaustion = $rule ? max(0, min(6, (int) ($combat?->exhaustion_level ?? 0))) * 2 : 0;
        $check = app(ConcentrationCheckService::class)->forDamage(
            (int) $validated['damage'], $this->constitutionSaveModifier($character), $exhaustion,
            ['source' => 'feature', 'feature_id' => (int) $feature->id,
             'feature_name' => (string) $feature->name, 'character_id' => (int) $character->id]
        );
        if (array_key_exists('roll_total', $validated) && $validated['roll_total'] !== null) {
            $result = app(CharacterConcentrationManager::class)->resolveFeature(
                $character, (int) $feature->id, (int) $validated['damage'], (int) $validated['roll_total']
            );
            return response()->json(['success' => true, 'resolved' => true, 'result' => $result]);
        }
        return response()->json(['success' => true, 'concentration_check' => $check]);
    }

    private function constitutionSaveModifier(Character $character): int
    {
        $abilities = $character->abilities()->first();
        $bonuses = is_array($abilities?->temporary_bonuses) ? $abilities->temporary_bonuses : [];
        $overrides = is_array($abilities?->overrides) ? $abilities->overrides : [];
        $score = array_key_exists('constitution', $overrides)
            ? (int) $overrides['constitution']
            : (int) ($abilities?->constitution ?? 10)
                + (int) ($bonuses['constitution'] ?? $bonuses['con'] ?? 0);
        $modifier = (int) floor(($score - 10) / 2);
        $save = $character->savingThrows()->where('ability', 'constitution')->first();
        $temporary = (int) ($save?->temporary_bonus ?? 0);
        if ($save && $save->bonus_override !== null) {
            return (int) $save->bonus_override + $temporary;
        }
        $level = max(1, (int) ($character->level ?? 1));
        $proficiency = (int) ($character->proficiency_bonus ?? match (true) {
            $level >= 17 => 6, $level >= 13 => 5, $level >= 9 => 4, $level >= 5 => 3, default => 2,
        });
        return $modifier + ((bool) ($save?->proficient ?? false) ? $proficiency : 0) + $temporary;
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
            'data.effect_enabled' => ['sometimes', 'boolean'],
            'data.effect_requires_concentration' => ['sometimes', 'boolean'],
            'data.effect_duration_mode' => ['sometimes', 'nullable', Rule::in(['fixed', 'dice'])],
            'data.effect_duration_value' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10000'],
            'data.effect_duration_dice' => ['sometimes', 'nullable', 'regex:/^([1-9]|10)[dD]([2-9]|[1-9][0-9]|100)([+-]\d{1,3})?$/'],

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
        $data['effect_enabled'] = (bool) ($data['effect_enabled'] ?? false);
        $data['effect_requires_concentration'] = $data['effect_enabled']
            && (bool) ($data['effect_requires_concentration'] ?? false);
        $data['effect_duration_mode'] = ($data['effect_duration_mode'] ?? '') === 'dice'
            ? 'dice' : 'fixed';
        $data['effect_duration_value'] = max(1, min(10000, (int) ($data['effect_duration_value'] ?? 1)));
        $data['effect_duration_dice'] = strtolower(trim((string) ($data['effect_duration_dice'] ?? '1d4')));
        if ($data['effect_enabled'] && $data['effect_duration_mode'] === 'dice'
            && !preg_match('/^([1-9]|10)d([2-9]|[1-9][0-9]|100)([+-]\d{1,3})?$/', $data['effect_duration_dice'])) {
            throw ValidationException::withMessages(['data.effect_duration_dice' => 'Use dados como 1d4 ou 2d6+1.']);
        }
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