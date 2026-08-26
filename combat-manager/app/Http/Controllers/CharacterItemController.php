<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CharacterItemController extends Controller
{
    private const RARITIES = [
        'common',
        'uncommon',
        'rare',
        'very_rare',
        'legendary',
        'artifact',
    ];

    private const NATURES = [
        'wonderful',
        'mundane',
        'technological',
    ];

    private const FEATURE_RECOVERIES = [
        'day',
        'short_rest',
        'long_rest',
        'dawn',
        'single_use',
        'custom',
    ];

    /**
     * Cria um item no inventário do personagem.
     */
    public function store(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter($character);
        $this->forbidCurseMutation($request);

        $data = $request->validate(
            $this->validationRules(creating: true)
        );

        $image = $request->file('image');

        unset(
            $data['image'],
            $data['remove_image']
        );

        $data = $this->normalizePayload($data);

        if ($image) {
            $data['image_path'] = $image->store(
                'character-items/' . $character->id,
                'public'
            );
        }

        $item = $character
            ->items()
            ->create($data);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado ao inventário.',
            'item' => $this->itemPayload($item),
            'counts' => $this->inventoryCounts($character),
        ], 201);
    }

    /**
     * Edita os dados estruturais e editoriais do item.
     */
    public function update(
        Request $request,
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->authorizeItem($character, $item);
        $this->forbidCurseMutation($request);

        $data = $request->validate(
            $this->validationRules(creating: false)
        );

        $image = $request->file('image');
        $removeImage = (bool) ($data['remove_image'] ?? false);

        unset(
            $data['image'],
            $data['remove_image']
        );

        $data = $this->normalizePayload(
            $data,
            $item
        );

        if ($removeImage && $item->image_path) {
            Storage::disk('public')->delete(
                $item->image_path
            );

            $data['image_path'] = null;
        }

        if ($image) {
            if ($item->image_path) {
                Storage::disk('public')->delete(
                    $item->image_path
                );
            }

            $data['image_path'] = $image->store(
                'character-items/' . $character->id,
                'public'
            );
        }

        $item->update($data);
        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Item atualizado.',
            'item' => $this->itemPayload($item),
            'counts' => $this->inventoryCounts($character),
        ]);
    }

    /**
     * Entrega a imagem do item diretamente pelo Laravel.
     *
     * Não depende de public/storage, APP_URL ou de symlink/junction.
     */
    public function image(
        Character $character,
        CharacterItem $item
    ): BinaryFileResponse {
        $this->authorizeItem($character, $item);

        abort_unless(
            is_string($item->image_path)
            && trim($item->image_path) !== '',
            404
        );

        $relativePath = ltrim(
            str_replace('\\', '/', trim($item->image_path)),
            '/'
        );

        abort_if(
            str_contains($relativePath, '../')
            || str_contains($relativePath, '..\\'),
            404
        );

        $fullPath = storage_path(
            'app/public/' . $relativePath
        );

        abort_unless(
            is_file($fullPath),
            404
        );

        return response()->file(
            $fullPath,
            [
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Remove o item e sua imagem pública, se existir.
     */
    public function destroy(
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->authorizeItem($character, $item);

        $itemId = $item->id;

        if ($item->image_path) {
            Storage::disk('public')->delete(
                $item->image_path
            );
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removido do inventário.',
            'deleted_id' => $itemId,
            'counts' => $this->inventoryCounts($character),
        ]);
    }

    /**
     * Equipar/guardar é uma ação de jogo e não exige abrir o editor.
     */
    public function updateEquipped(
        Request $request,
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->authorizeItem($character, $item);

        $data = $request->validate([
            'equipped' => [
                'required',
                'boolean',
            ],
        ]);

        $properties = is_array($item->properties)
            ? $item->properties
            : [];

        $equippable = (bool) (
            $properties['inventory']['equippable']
            ?? false
        );

        if (!$equippable && (bool) $data['equipped']) {
            throw ValidationException::withMessages([
                'equipped' => [
                    'Este item não está configurado como equipável.',
                ],
            ]);
        }

        $item->update([
            'equipped' => $equippable
                ? (bool) $data['equipped']
                : false,
        ]);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => $item->equipped
                ? 'Item equipado.'
                : 'Item guardado.',
            'item' => $this->itemPayload($item),
            'counts' => $this->inventoryCounts($character),
        ]);
    }

    /**
     * Sintoniza ou remove a sintonização.
     * O limite padrão de 3 é tratado como confirmação na interface,
     * não como bloqueio rígido no backend.
     */
    public function updateAttuned(
        Request $request,
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->authorizeItem($character, $item);

        $data = $request->validate([
            'attuned' => [
                'required',
                'boolean',
            ],
        ]);

        if (
            !$item->is_magical
            || !$item->requires_attunement
        ) {
            throw ValidationException::withMessages([
                'attuned' => [
                    'Este item não requer sintonização.',
                ],
            ]);
        }

        $item->update([
            'attuned' => (bool) $data['attuned'],
        ]);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => $item->attuned
                ? 'Item sintonizado.'
                : 'Sintonização removida.',
            'item' => $this->itemPayload($item),
            'counts' => $this->inventoryCounts($character),
        ]);
    }

    /**
     * Atualiza apenas os usos de UMA habilidade do item.
     *
     * O rastreador pertence à habilidade, e não ao item inteiro.
     * A habilidade é localizada pelo índice atual em properties.features.
     */
    public function updateFeatureUses(
        Request $request,
        Character $character,
        CharacterItem $item,
        string $feature
    ): JsonResponse {
        $this->authorizeItem($character, $item);

        $data = $request->validate([
            'current' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        if (!ctype_digit($feature)) {
            abort(404);
        }

        $featureIndex = (int) $feature;

        $properties = is_array($item->properties)
            ? $item->properties
            : [];

        $features = is_array($properties['features'] ?? null)
            ? array_values($properties['features'])
            : [];

        if (!array_key_exists($featureIndex, $features)) {
            abort(404);
        }

        $featureData = is_array($features[$featureIndex])
            ? $features[$featureIndex]
            : [];

        $usage = is_array($featureData['usage'] ?? null)
            ? $featureData['usage']
            : [];

        if (!(bool) ($usage['enabled'] ?? false)) {
            throw ValidationException::withMessages([
                'current' => [
                    'Esta habilidade não possui Rastreador de usos.',
                ],
            ]);
        }

        $max = max(
            1,
            (int) ($usage['max'] ?? 1)
        );

        $current = min(
            $max,
            max(
                0,
                (int) $data['current']
            )
        );

        $usage['current'] = $current;
        $usage['max'] = $max;

        $featureData['usage'] = $usage;
        $features[$featureIndex] = $featureData;
        $properties['features'] = $features;

        $item->update([
            'properties' => $properties,
        ]);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Usos da habilidade atualizados.',
            'item' => $this->itemPayload($item),
            'feature_index' => $featureIndex,
            'counts' => $this->inventoryCounts($character),
        ]);
    }

    /**
     * Regras do criador modular.
     */
    private function validationRules(bool $creating): array
    {
        return [
            'name' => [
                $creating ? 'required' : 'sometimes',
                'string',
                'max:120',
            ],

            'type' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(self::NATURES),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:30000',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'remove_image' => [
                'sometimes',
                'boolean',
            ],

            'equipped' => [
                'sometimes',
                'boolean',
            ],

            'is_magical' => [
                'sometimes',
                'boolean',
            ],

            'rarity' => [
                'sometimes',
                'nullable',
                Rule::in(self::RARITIES),
            ],

            'requires_attunement' => [
                'sometimes',
                'boolean',
            ],

            'attuned' => [
                'sometimes',
                'boolean',
            ],

            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:20000',
            ],

            'ability_bonuses' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'modifiers' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.nature' => [
                'sometimes',
                'nullable',
                Rule::in(self::NATURES),
            ],

            'properties.inventory' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.inventory.equippable' => [
                'sometimes',
                'boolean',
            ],

            'properties.weapon' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.weapon.category' => [
                'sometimes',
                'nullable',
                Rule::in([
                    'simple',
                    'martial',
                    'custom',
                ]),
            ],

            'properties.weapon.category_custom' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.weapon_type' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.magic_bonus' => [
                'sometimes',
                'nullable',
                'integer',
                'between:-99,99',
            ],

            'properties.weapon.traits' => [
                'sometimes',
                'nullable',
                'array',
                'max:40',
            ],

            'properties.weapon.traits.*.key' => [
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.traits.*.name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.traits.*.description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'properties.weapon.traits.*.custom' => [
                'sometimes',
                'boolean',
            ],

            'properties.weapon.damage' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.weapon.damage.count' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:99',
            ],

            'properties.weapon.damage.die' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'properties.weapon.damage.ability' => [
                'sometimes',
                'nullable',
                'string',
                'max:40',
            ],

            'properties.weapon.damage.ability_custom' => [
                'sometimes',
                'nullable',
                'string',
                'max:80',
            ],

            'properties.weapon.damage.type' => [
                'sometimes',
                'nullable',
                'string',
                'max:80',
            ],

            'properties.weapon.extra_damage' => [
                'sometimes',
                'nullable',
                'array',
                'max:20',
            ],

            'properties.weapon.extra_damage.*.count' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:99',
            ],

            'properties.weapon.extra_damage.*.die' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'properties.weapon.extra_damage.*.type' => [
                'sometimes',
                'nullable',
                'string',
                'max:80',
            ],

            'properties.weapon.masteries' => [
                'sometimes',
                'nullable',
                'array',
                'max:20',
            ],

            'properties.weapon.masteries.*.key' => [
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.masteries.*.name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'properties.weapon.masteries.*.description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'properties.weapon.masteries.*.custom' => [
                'sometimes',
                'boolean',
            ],

            'properties.armor' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.armor.category' => [
                'sometimes',
                'nullable',
                Rule::in([
                    'light',
                    'medium',
                    'heavy',
                    'custom',
                ]),
            ],

            'properties.armor.category_custom' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.armor.armor_type' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.armor.base_ac' => [
                'sometimes',
                'nullable',
                'integer',
                'between:0,99',
            ],

            'properties.armor.magic_bonus' => [
                'sometimes',
                'nullable',
                'integer',
                'between:-99,99',
            ],

            'properties.shield' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.shield.label' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.shield.ac_bonus' => [
                'sometimes',
                'nullable',
                'integer',
                'between:0,99',
            ],

            'properties.shield.magic_bonus' => [
                'sometimes',
                'nullable',
                'integer',
                'between:-99,99',
            ],

            'properties.custom_properties' => [
                'sometimes',
                'nullable',
                'array',
                'max:40',
            ],

            'properties.custom_properties.*.title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'properties.custom_properties.*.value' => [
                'nullable',
                'string',
                'max:250',
            ],

            'properties.custom_properties.*.description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'properties.features' => [
                'sometimes',
                'nullable',
                'array',
                'max:60',
            ],

            'properties.features.*.title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'properties.features.*.description' => [
                'nullable',
                'string',
                'max:15000',
            ],

            'properties.features.*.usage' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'properties.features.*.usage.enabled' => [
                'sometimes',
                'boolean',
            ],

            'properties.features.*.usage.current' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],

            'properties.features.*.usage.max' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],

            'properties.features.*.usage.recovery' => [
                'sometimes',
                'nullable',
                Rule::in(self::FEATURE_RECOVERIES),
            ],

            'properties.features.*.usage.recovery_custom' => [
                'sometimes',
                'nullable',
                'string',
                'max:120',
            ],

            'properties.optional_rules' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }

    /**
     * Converte os campos editoriais em um formato previsível.
     */
    private function normalizePayload(
        array $data,
        ?CharacterItem $existing = null
    ): array {
        $existingProperties = is_array($existing?->properties)
            ? $existing->properties
            : [];

        $properties = array_key_exists('properties', $data)
            ? (is_array($data['properties']) ? $data['properties'] : [])
            : $existingProperties;

        $properties = $this->normalizeProperties($properties);

        $nature = $properties['nature'];
        $isMagical = $nature === 'wonderful';

        $rarity = array_key_exists('rarity', $data)
            ? $data['rarity']
            : $existing?->rarity;

        $requiresAttunement = $isMagical
            && (
                array_key_exists('requires_attunement', $data)
                    ? (bool) $data['requires_attunement']
                    : (bool) ($existing?->requires_attunement ?? false)
            );

        $attuned = $requiresAttunement
            && (
                array_key_exists('attuned', $data)
                    ? (bool) $data['attuned']
                    : (bool) ($existing?->attuned ?? false)
            );

        $equippable = (bool) (
            $properties['inventory']['equippable']
            ?? false
        );

        $equipped = $equippable
            && (
                array_key_exists('equipped', $data)
                    ? (bool) $data['equipped']
                    : (bool) ($existing?->equipped ?? false)
            );

        $data['type'] = $nature;
        $data['properties'] = $properties;
        $data['quantity'] = 1;
        $data['weight'] = null;
        $data['equipped'] = $equipped;
        $data['is_magical'] = $isMagical;
        $data['rarity'] = $rarity;
        $data['requires_attunement'] = $requiresAttunement;
        $data['attuned'] = $attuned;

        /*
        | Campos legados de combate continuam preenchidos quando possível.
        | Isso evita quebrar integrações existentes antes de migrarmos tudo
        | para properties.*.
        */
        $data['armor_class'] = null;
        $data['damage'] = null;
        $data['attack_bonus'] = null;
        $data['damage_bonus'] = null;

        $weapon = is_array($properties['weapon'] ?? null)
            ? $properties['weapon']
            : null;

        if ($weapon) {
            $magicBonus = (int) ($weapon['magic_bonus'] ?? 0);
            $damage = is_array($weapon['damage'] ?? null)
                ? $weapon['damage']
                : [];

            $count = max(1, (int) ($damage['count'] ?? 1));
            $die = trim((string) ($damage['die'] ?? ''));

            $data['damage'] = $die !== ''
                ? $count . $die
                : null;

            $data['attack_bonus'] = $magicBonus !== 0
                ? $magicBonus
                : null;

            $data['damage_bonus'] = $magicBonus !== 0
                ? $magicBonus
                : null;
        }

        $armor = is_array($properties['armor'] ?? null)
            ? $properties['armor']
            : null;

        if ($armor) {
            $baseAc = $armor['base_ac'] ?? null;
            $magicBonus = (int) ($armor['magic_bonus'] ?? 0);

            $data['armor_class'] = $baseAc !== null
                ? (int) $baseAc + $magicBonus
                : null;
        }

        return $data;
    }

    private function normalizeProperties(array $properties): array
    {
        $nature = $properties['nature'] ?? 'wonderful';

        if (!in_array($nature, self::NATURES, true)) {
            $nature = 'wonderful';
        }

        $properties['nature'] = $nature;

        $properties['inventory'] = is_array(
            $properties['inventory'] ?? null
        )
            ? $properties['inventory']
            : [];

        $properties['inventory']['equippable'] = (bool) (
            $properties['inventory']['equippable']
            ?? false
        );

        if (isset($properties['weapon']) && is_array($properties['weapon'])) {
            $properties['weapon'] = $this->normalizeWeapon(
                $properties['weapon']
            );
        } else {
            unset($properties['weapon']);
        }

        if (isset($properties['armor']) && is_array($properties['armor'])) {
            $properties['armor'] = $this->normalizeArmor(
                $properties['armor']
            );
        } else {
            unset($properties['armor']);
        }

        if (isset($properties['shield']) && is_array($properties['shield'])) {
            $properties['shield'] = $this->normalizeShield(
                $properties['shield']
            );
        } else {
            unset($properties['shield']);
        }

        $properties['custom_properties'] = collect(
            $properties['custom_properties'] ?? []
        )
            ->filter(fn ($entry) => is_array($entry))
            ->map(function (array $entry) {
                return [
                    'title' => trim((string) ($entry['title'] ?? '')),
                    'value' => trim((string) ($entry['value'] ?? '')),
                    'description' => trim((string) ($entry['description'] ?? '')),
                ];
            })
            ->filter(function (array $entry) {
                return $entry['title'] !== ''
                    || $entry['value'] !== ''
                    || $entry['description'] !== '';
            })
            ->values()
            ->all();

        $properties['features'] = collect(
            $properties['features'] ?? []
        )
            ->filter(fn ($feature) => is_array($feature))
            ->map(function (array $feature) {
                $title = trim((string) ($feature['title'] ?? ''));
                $description = trim((string) ($feature['description'] ?? ''));

                $usage = is_array($feature['usage'] ?? null)
                    ? $feature['usage']
                    : [];

                $enabled = (bool) ($usage['enabled'] ?? false);

                if (!$enabled) {
                    $usage = [
                        'enabled' => false,
                    ];
                } else {
                    $max = max(
                        1,
                        (int) ($usage['max'] ?? 1)
                    );

                    $current = min(
                        $max,
                        max(
                            0,
                            (int) ($usage['current'] ?? $max)
                        )
                    );

                    $recovery = $usage['recovery'] ?? 'day';

                    if (!in_array($recovery, self::FEATURE_RECOVERIES, true)) {
                        $recovery = 'day';
                    }

                    $usage = [
                        'enabled' => true,
                        'current' => $current,
                        'max' => $max,
                        'recovery' => $recovery,
                        'recovery_custom' => $recovery === 'custom'
                            ? trim((string) ($usage['recovery_custom'] ?? ''))
                            : null,
                    ];
                }

                return [
                    'title' => $title,
                    'description' => $description,
                    'usage' => $usage,
                ];
            })
            ->filter(function (array $feature) {
                return $feature['title'] !== ''
                    || $feature['description'] !== '';
            })
            ->values()
            ->all();

        return $properties;
    }

    private function normalizeWeapon(array $weapon): array
    {
        $category = $weapon['category'] ?? 'simple';

        if (!in_array($category, ['simple', 'martial', 'custom'], true)) {
            $category = 'custom';
        }

        $damage = is_array($weapon['damage'] ?? null)
            ? $weapon['damage']
            : [];

        $ability = trim((string) ($damage['ability'] ?? 'strength'));

        $traits = collect($weapon['traits'] ?? [])
            ->filter(fn ($entry) => is_array($entry))
            ->map(function (array $entry) {
                return [
                    'key' => trim((string) ($entry['key'] ?? '')),
                    'name' => trim((string) ($entry['name'] ?? '')),
                    'description' => trim((string) ($entry['description'] ?? '')),
                    'custom' => (bool) ($entry['custom'] ?? false),
                ];
            })
            ->filter(fn (array $entry) => $entry['name'] !== '')
            ->values()
            ->all();

        $masteries = collect($weapon['masteries'] ?? [])
            ->filter(fn ($entry) => is_array($entry))
            ->map(function (array $entry) {
                return [
                    'key' => trim((string) ($entry['key'] ?? '')),
                    'name' => trim((string) ($entry['name'] ?? '')),
                    'description' => trim((string) ($entry['description'] ?? '')),
                    'custom' => (bool) ($entry['custom'] ?? false),
                ];
            })
            ->filter(fn (array $entry) => $entry['name'] !== '')
            ->values()
            ->all();

        $extraDamage = collect($weapon['extra_damage'] ?? [])
            ->filter(fn ($entry) => is_array($entry))
            ->map(function (array $entry) {
                return [
                    'count' => max(1, (int) ($entry['count'] ?? 1)),
                    'die' => trim((string) ($entry['die'] ?? 'd6')),
                    'type' => trim((string) ($entry['type'] ?? '')),
                ];
            })
            ->values()
            ->all();

        return [
            'category' => $category,
            'category_custom' => $category === 'custom'
                ? trim((string) ($weapon['category_custom'] ?? ''))
                : null,
            'weapon_type' => trim((string) ($weapon['weapon_type'] ?? '')),
            'magic_bonus' => (int) ($weapon['magic_bonus'] ?? 0),
            'traits' => $traits,
            'damage' => [
                'count' => max(1, (int) ($damage['count'] ?? 1)),
                'die' => trim((string) ($damage['die'] ?? 'd4')),
                'ability' => $ability,
                'ability_custom' => $ability === 'custom'
                    ? trim((string) ($damage['ability_custom'] ?? ''))
                    : null,
                'type' => trim((string) ($damage['type'] ?? '')),
            ],
            'extra_damage' => $extraDamage,
            'masteries' => $masteries,
        ];
    }

    private function normalizeArmor(array $armor): array
    {
        $category = $armor['category'] ?? 'light';

        if (!in_array($category, ['light', 'medium', 'heavy', 'custom'], true)) {
            $category = 'custom';
        }

        return [
            'category' => $category,
            'category_custom' => $category === 'custom'
                ? trim((string) ($armor['category_custom'] ?? ''))
                : null,
            'armor_type' => trim((string) ($armor['armor_type'] ?? '')),
            'base_ac' => isset($armor['base_ac'])
                ? (int) $armor['base_ac']
                : null,
            'magic_bonus' => (int) ($armor['magic_bonus'] ?? 0),
        ];
    }

    private function normalizeShield(array $shield): array
    {
        return [
            'label' => trim((string) ($shield['label'] ?? 'Escudo')) ?: 'Escudo',
            'ac_bonus' => (int) ($shield['ac_bonus'] ?? 2),
            'magic_bonus' => (int) ($shield['magic_bonus'] ?? 0),
        ];
    }

    /**
     * O jogador não recebe nem envia campos ocultos de maldição.
     */
    private function forbidCurseMutation(Request $request): void
    {
        if (
            $request->hasAny([
                'is_cursed',
                'curse_description',
                'curse_revealed',
            ])
        ) {
            abort(
                403,
                'Campos de maldição só podem ser alterados pelo Mestre.'
            );
        }
    }

    private function authorizeCharacter(Character $character): void
    {
        abort_unless(
            (int) $character->user_id === (int) Auth::id(),
            403
        );
    }

    private function authorizeItem(
        Character $character,
        CharacterItem $item
    ): void {
        $this->authorizeCharacter($character);

        abort_unless(
            (int) $item->character_id === (int) $character->id,
            404
        );
    }

    private function itemPayload(CharacterItem $item): array
    {
        $curseVisible = (bool) $item->is_cursed
            && (bool) $item->curse_revealed;

        return [
            'id' => $item->id,
            'character_id' => $item->character_id,
            'name' => $item->name,
            'type' => $item->type,
            'description' => $item->description,

            'image_path' => $item->image_path,
            'image_url' => $this->imageUrl($item),

            'quantity' => (int) $item->quantity,
            'weight' => $item->weight,
            'equipped' => (bool) $item->equipped,
            'is_magical' => (bool) $item->is_magical,
            'rarity' => $item->rarity,
            'rarity_label' => $this->rarityLabel($item->rarity),
            'requires_attunement' => (bool) $item->requires_attunement,
            'attuned' => (bool) $item->attuned,
            'armor_class' => $item->armor_class,
            'damage' => $item->damage,
            'attack_bonus' => $item->attack_bonus,
            'damage_bonus' => $item->damage_bonus,
            'ability_bonuses' => $item->ability_bonuses ?? [],
            'properties' => $item->properties ?? [],
            'modifiers' => $item->modifiers ?? [],
            'notes' => $item->notes,

            'is_cursed' => $curseVisible,
            'curse_revealed' => $curseVisible,
            'curse_description' => $curseVisible
                ? $item->curse_description
                : null,

            'created_at' => $item->created_at?->toISOString(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }



    private function imageUrl(CharacterItem $item): ?string
    {
        if (
            !is_string($item->image_path)
            || trim($item->image_path) === ''
        ) {
            return null;
        }

        return route(
            'characters.items.image',
            [
                'character' => $item->character_id,
                'item' => $item->id,
                'v' => substr(
                    sha1($item->image_path),
                    0,
                    12
                ),
            ],
            false
        );
    }

    private function inventoryCounts(Character $character): array
    {
        $items = $character->items()->get([
            'id',
            'equipped',
            'attuned',
            'type',
        ]);

        return [
            'total' => $items->count(),
            'equipped' => $items->where('equipped', true)->count(),
            'attuned' => $items->where('attuned', true)->count(),
            'wonderful' => $items->where('type', 'wonderful')->count(),
            'technological' => $items->where('type', 'technological')->count(),
            'mundane' => $items->where('type', 'mundane')->count(),
        ];
    }

    private function rarityLabel(?string $rarity): ?string
    {
        if (!$rarity) {
            return null;
        }

        return match ($rarity) {
            'common' => 'Comum',
            'uncommon' => 'Incomum',
            'rare' => 'Raro',
            'very_rare' => 'Muito Raro',
            'legendary' => 'Lendário',
            'artifact' => 'Artefato',
            default => ucfirst(str_replace('_', ' ', $rarity)),
        };
    }
}