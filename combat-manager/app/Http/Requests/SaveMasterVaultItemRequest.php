<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaveMasterVaultItemRequest extends FormRequest
{
    private const NATURES = [
        'wonderful',
        'mundane',
        'technological',
    ];

    private const RARITIES = [
        'common',
        'uncommon',
        'rare',
        'very_rare',
        'legendary',
        'artifact',
    ];

    private const VESTIGE_STAGES = ['dormant', 'awakened', 'exalted'];

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', Rule::in(self::NATURES)],
            'description' => ['nullable', 'string', 'max:30000'],
            'image' => ['nullable', 'image', 'max:8192'],
            'remove_image' => ['sometimes', 'boolean'],
            'default_quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'is_magical' => ['sometimes', 'boolean'],
            'rarity' => ['nullable', 'string', Rule::in(self::RARITIES)],
            'requires_attunement' => ['sometimes', 'boolean'],
            'is_cursed' => ['sometimes', 'boolean'],
            'curse_name' => ['nullable', 'string', 'max:160'],
            'curse_description' => ['nullable', 'string', 'max:30000'],
            'armor_class' => ['nullable', 'integer', 'min:-99', 'max:999'],
            'damage' => ['nullable', 'string', 'max:120'],
            'attack_bonus' => ['nullable', 'integer', 'min:-99', 'max:999'],
            'damage_bonus' => ['nullable', 'integer', 'min:-99', 'max:999'],
            'ability_bonuses' => ['nullable', 'array'],
            'properties' => ['nullable', 'array'],
            'modifiers' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:30000'],
            'tags' => ['nullable', 'array', 'max:30'],
            'tags.*' => ['string', 'max:60'],
            'favorite' => ['sometimes', 'boolean'],
            'properties.vestige' => ['sometimes', 'array'],
            'properties.vestige.enabled' => ['sometimes', 'boolean'],
            // Campo legado: compatível com itens já existentes, não define o grau do Player.
            'properties.vestige.current_stage' => ['sometimes', 'string', Rule::in(self::VESTIGE_STAGES)],
            'properties.vestige.stages' => ['sometimes', 'array'],
        ];

        foreach (self::VESTIGE_STAGES as $stage) {
            $prefix = "properties.vestige.stages.{$stage}";
            $rules[$prefix] = ['sometimes', 'array'];
            $rules["{$prefix}.lore"] = ['sometimes', 'nullable', 'string', 'max:30000'];
            $rules["{$prefix}.features"] = ['sometimes', 'array'];
            $rules["{$prefix}.features.*"] = ['array'];
            $rules["{$prefix}.features.*.title"] = ['sometimes', 'nullable', 'string', 'max:120'];
            $rules["{$prefix}.features.*.description"] = ['sometimes', 'nullable', 'string', 'max:10000'];
            $rules["{$prefix}.features.*.usage"] = ['sometimes', 'array'];
            $rules["{$prefix}.features.*.usage.enabled"] = ['sometimes', 'boolean'];
            $rules["{$prefix}.features.*.usage.counter_mode"] = ['sometimes', 'string', Rule::in(['spend', 'build'])];
            $rules["{$prefix}.features.*.usage.max"] = ['sometimes', 'integer', 'min:1'];
            $rules["{$prefix}.features.*.usage.current"] = ['sometimes', 'integer', 'min:0'];
            $rules["{$prefix}.features.*.usage.recovery"] = ['sometimes', 'string', Rule::in([
                'day', 'short_rest', 'long_rest', 'dawn', 'single_use', 'custom',
            ])];
            $rules["{$prefix}.features.*.usage.recovery_custom"] = ['sometimes', 'nullable', 'string', 'max:120'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (!$this->boolean('properties.vestige.enabled')) {
                return;
            }

            if ($this->input('type') !== 'wonderful' || $this->input('rarity') !== 'artifact') {
                $validator->errors()->add(
                    'properties.vestige.enabled',
                    'Um Vestígio precisa ser um Item Maravilhoso de raridade Artefato.'
                );
            }
        });
    }

    public function normalized(): array
    {
        $data = $this->validated();

        unset($data['image'], $data['remove_image']);

        $data['is_magical'] = (bool) ($data['is_magical'] ?? false);
        $data['requires_attunement'] = (bool) ($data['requires_attunement'] ?? false);
        $data['is_cursed'] = (bool) ($data['is_cursed'] ?? false);
        $data['favorite'] = (bool) ($data['favorite'] ?? false);

        $data['ability_bonuses'] = is_array($data['ability_bonuses'] ?? null)
            ? $data['ability_bonuses']
            : [];

        // O editor envia todo o contrato mecânico em `properties` (armas,
        // armaduras, escudos, habilidades comuns, Vestígios e regras opcionais).
        // Como as etapas dos Vestígios têm validação aninhada própria, o
        // `validated()` do Laravel pode omitir os outros ramos não detalhados
        // por regras individuais. Restauramos o array completo já validado
        // quanto ao tipo; os campos do Vestígio continuam sujeitos às
        // regras específicas e ao withValidator() acima.
        $submittedProperties = $this->input('properties', []);
        $data['properties'] = is_array($submittedProperties)
            ? $submittedProperties
            : [];

        // Mantém compatibilidade direta com o inventory atual.
        $data['properties']['nature'] = $data['type'];

        $data['modifiers'] = is_array($data['modifiers'] ?? null)
            ? $data['modifiers']
            : [];

        $data['tags'] = collect($data['tags'] ?? [])
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($data['is_cursed']) {
            $curseName = trim((string) ($data['curse_name'] ?? ''));
            $data['curse_name'] = $curseName !== '' ? $curseName : null;
        } else {
            $data['curse_name'] = null;
            $data['curse_description'] = null;
        }

        return $data;
    }
}
