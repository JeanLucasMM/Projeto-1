<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
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
        ];
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

        $data['properties'] = is_array($data['properties'] ?? null)
            ? $data['properties']
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
