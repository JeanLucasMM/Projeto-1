<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\CharacterCombat;
use App\Models\Spell;
use App\Services\Characters\EquippedWeaponAttack;
use App\Services\Characters\CharacterConcentrationManager;
use App\Services\Spells\SpellDurationResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * V66.2.100 — permite ao jogador criar e manter suas próprias maldições.
 *
 * Extende o controller atual: todas as validações, rotas, permissões e
 * funcionalidades de itens continuam sendo processadas pelo código original.
 * Não há migração adicional: character_items já tem os campos de maldição.
 *
 * A marca _player_curse_created é colocada exclusivamente pelo servidor
 * no JSON properties. Nunca é aceita como autorização vinda do cliente.
 */
class CharacterItemPlayerCurseController extends CharacterItemController
{
    private const CURSE_FIELDS = [
        'is_cursed',
        'curse_name',
        'curse_description',
    ];

    public function store(Request $request, Character $character): JsonResponse
    {
        $this->authorizePlayerCharacter($character);
        $this->forbidClientRevelation($request);
        $requested = $this->validatedCurseFields($request);
        $this->stripCurseFields($request);

        // O controller original mantém todas as validações do item, imagem,
        // habilidades, equipamentos e integração com os cards da ficha.
        $incomingWeapon = (array) $request->input('properties.weapon', []);
        $response = parent::store($request, $character);
        $payload = $response->getData(true);
        $item = $character->items()->findOrFail($payload['item']['id']);
        $this->restoreWeaponFields($item, $incomingWeapon);

        $active = (bool) ($requested['is_cursed'] ?? false);
        $this->persistPlayerCurse($item, $requested, $active);
        return $this->refreshResponseItem($response, $item);
    }

    public function update(Request $request, Character $character, CharacterItem $item): JsonResponse
    {
        $this->authorizePlayerCharacter($character);
        abort_unless((int) $item->character_id === (int) $character->id, 404);
        $this->forbidClientRevelation($request);

        $originalProperties = is_array($item->properties) ? $item->properties : [];
        $originalWeapon = (array) ($originalProperties['weapon'] ?? []);
        $incomingWeapon = (array) $request->input('properties.weapon', []);
        $ownedByPlayer = !empty($originalProperties['_player_curse_created']);
        $wasCursed = (bool) $item->is_cursed;
        $changedCurse = $request->hasAny(self::CURSE_FIELDS);
        $requested = $this->validatedCurseFields($request);

        // Segredos do Mestre — inclusive maldições já reveladas — nunca
        // podem ser alterados pelo jogador. Não depende de flags do cliente.
        if ($changedCurse && $wasCursed && !$ownedByPlayer) {
            abort(403, 'Esta alteração não é permitida para este item.');
        }
        if ($changedCurse) {
            $active = array_key_exists('is_cursed', $requested)
                ? (bool) $requested['is_cursed']
                : $wasCursed;
            $this->validateCurseName($requested, $active, $item);
        }

        $this->stripCurseFields($request);
        $response = parent::update($request, $character, $item);
        $item->refresh();
        $this->restoreWeaponFields($item, $incomingWeapon, $originalWeapon);

        // O controller base normaliza properties. Mesmo se alguém enviar
        // essa marca manualmente, restaura a propriedade confiável do banco.
        $properties = is_array($item->properties) ? $item->properties : [];
        $properties['_player_curse_created'] = $ownedByPlayer;
        $item->properties = $properties;
        $item->save();

        if ($changedCurse) {
            $this->persistPlayerCurse($item, $requested, $active);
        }
        return $this->refreshResponseItem($response, $item);
    }

    /**
     * O normalizador legado de CharacterItem descarta alguns campos novos.
     * Restaura apenas propriedades conhecidas do editor, após sua validação.
     * Mantém munição e escolha de atributo quando a arma é editada.
     */
    private function restoreWeaponFields(
        CharacterItem $item, array $incoming, array $previous = []
    ): void {
        $properties = is_array($item->properties) ? $item->properties : [];
        if (!is_array($properties['weapon'] ?? null)) return;
        $weapon = $properties['weapon'];
        $capacity = array_key_exists('loading_capacity', $incoming)
            ? $incoming['loading_capacity'] : ($previous['loading_capacity'] ?? null);
        $capacity = is_numeric($capacity) && (int) $capacity > 0
            ? min(999999, (int) $capacity) : null;
        if ($capacity !== null) {
            $weapon['loading_capacity'] = $capacity;
            $weapon['loading_current'] = max(0, min(
                $capacity, (int) ($previous['loading_current'] ?? $capacity)
            ));
        }
        $choice = $previous['attack_ability_choice'] ?? null;
        if (in_array($choice, ['strength','dexterity'], true)) {
            $weapon['attack_ability_choice'] = $choice;
        }
        foreach (['range_normal_ft','range_long_ft','reach_ft'] as $key) {
            $value = array_key_exists($key, $incoming)
                ? $incoming[$key] : ($previous[$key] ?? null);
            if (is_numeric($value)) $weapon[$key] = max(0, min(99999, (int) $value));
        }
        $damage = (array) ($weapon['damage'] ?? []);
        $originalDamage = (array) ($previous['damage'] ?? []);
        $inputDamage = (array) ($incoming['damage'] ?? []);
        $damage['flat_bonus'] = max(-999, min(999, (int) ($inputDamage['flat_bonus'] ?? $originalDamage['flat_bonus'] ?? 0)));
        $damage['include_ability'] = filter_var(
            $inputDamage['include_ability'] ?? $originalDamage['include_ability'] ?? true,
            FILTER_VALIDATE_BOOLEAN
        );
        $weapon['damage'] = $damage;
        // A normalização antiga descartava os atributos permitidos pelo editor.
        // Preserve o valor validado no cadastro do item, sem permitir opções extras.
        $abilities = array_key_exists('allowed_abilities', $incoming)
            ? $incoming['allowed_abilities'] : ($previous['allowed_abilities'] ?? null);
        if (is_array($abilities)) {
            $abilities = array_values(array_unique(array_filter($abilities,
                static fn ($value) => in_array($value, ['strength','dexterity','none'], true))));
            if ($abilities !== []) $weapon['allowed_abilities'] = $abilities;
        }
        // Nunca aceite attack_config vindo do editor genérico de itens.
        if (isset($previous['attack_config']) && is_array($previous['attack_config'])) {
            $weapon['attack_config'] = $previous['attack_config'];
        }
        $allowed = app(EquippedWeaponAttack::class)->allowedAbilities($weapon);
        if (!in_array($weapon['attack_ability_choice'] ?? '', $allowed, true)) {
            $weapon['attack_ability_choice'] = $allowed[0];
        }
        $properties['weapon'] = $weapon;
        $item->properties = $properties;
        $item->save();
        $item->refresh();
    }

    /**
     * V66.2.112 — Equipamento e munição da arma usam o mesmo item do Inventário.
     * Reaproveita a rota de equipar; não cria CharacterAttack duplicados.
     */
    public function updateEquipped(
        Request $request,
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->authorizePlayerCharacter($character);
        abort_unless((int) $item->character_id === (int) $character->id, 404);

        $input = $request->validate([
            'equipped' => ['required', 'boolean'],
            'weapon_attack_ability' => ['sometimes', 'string', 'in:strength,dexterity,none'],
            'ammo_action' => ['sometimes', 'string', 'in:decrement,increment,reload'],
            'attack_config' => ['sometimes','array'],
            'attack_config.name' => ['sometimes','string','max:120'],
            'attack_config.attack_ability' => ['required_with:attack_config','string','in:strength,dexterity,none'],
            'attack_config.effect' => ['nullable','string','max:255'],
            'attack_config.description' => ['nullable','string','max:20000'],
            'attack_config.attack_bonus' => ['sometimes','integer','between:-999,999'],
            'attack_config.range' => ['nullable','string','max:120'],
            'attack_config.masteries' => ['sometimes','array','max:20'],
            'attack_config.masteries.*' => ['string','max:60'],
            'attack_config.damage_parts' => ['sometimes','array','min:1','max:15'],
            'attack_config.damage_parts.*.id' => ['nullable','string','max:80'],
            'attack_config.damage_parts.*.expression' => ['required','string','regex:/^\d{1,2}d(?:4|6|8|10|12|20|100)$/i'],
            'attack_config.damage_parts.*.type' => ['nullable','string','max:80'],
            'attack_config.damage_parts.*.bonus' => ['required','integer','between:-999,999'],
            'attack_config.damage_parts.*.abilities' => ['sometimes','array','max:6'],
            'attack_config.damage_parts.*.abilities.*' => ['string','in:strength,dexterity,constitution,intelligence,wisdom,charisma'],
        ]);
        return DB::transaction(function () use ($request, $character, $item, $input): JsonResponse {
            $locked = $character->items()->lockForUpdate()->findOrFail($item->id);
            $service = app(EquippedWeaponAttack::class);
            $properties = is_array($locked->properties) ? $locked->properties : [];
            $attackConfig = $input['attack_config'] ?? null;
            $choice = $attackConfig['attack_ability'] ?? $input['weapon_attack_ability'] ?? null;
            $action = $input['ammo_action'] ?? null;

            if (($choice !== null || $action !== null || $attackConfig !== null) && !$service->isWeapon($locked)) {
                throw ValidationException::withMessages([
                    'weapon' => 'Somente armas configuradas aceitam atributo ou carregamento.',
                ]);
            }
            if ($action !== null && (!$locked->equipped || !(bool) $input['equipped'])) {
                throw ValidationException::withMessages([
                    'ammo_action' => 'Equipe esta arma antes de alterar a munição.',
                ]);
            }
            if ($service->isWeapon($locked)) {
                $weapon = (array) ($properties['weapon'] ?? []);
                $allowed = $service->allowedAbilities($weapon);
                if ($choice !== null && !in_array($choice, $allowed, true)) {
                    throw ValidationException::withMessages([
                        'weapon_attack_ability' => 'Este atributo não está habilitado para esta arma.',
                    ]);
                }
                if ($attackConfig !== null && !(bool) $input['equipped']) {
                    throw ValidationException::withMessages([
                        'attack_config' => 'Configure o ataque apenas ao equipar a arma.',
                    ]);
                }
                if ($choice !== null) {
                    $weapon['attack_ability_choice'] = $choice;
                } elseif (!in_array($weapon['attack_ability_choice'] ?? '', $allowed, true)) {
                    $weapon['attack_ability_choice'] = $allowed[0];
                }
                if ($attackConfig !== null) {
                    // Somente campos previstos; o ataque continua derivado do mesmo item.
                    $adjusted = ['attack_ability' => $choice];
                    if (array_key_exists('name', $attackConfig)) {
                        $name = trim((string) $attackConfig['name']);
                        if ($name !== '') $adjusted['name'] = $name;
                    }
                    if (array_key_exists('attack_bonus', $attackConfig)) {
                        $adjusted['attack_bonus'] = (int) $attackConfig['attack_bonus'];
                    }
                    foreach (['effect','description','range'] as $field) {
                        if (array_key_exists($field, $attackConfig)) {
                            $adjusted[$field] = trim((string) ($attackConfig[$field] ?? ''));
                        }
                    }
                    if (array_key_exists('masteries', $attackConfig)) {
                        $adjusted['masteries'] = array_values($attackConfig['masteries'] ?? []);
                    }
                    if (array_key_exists('damage_parts', $attackConfig)) {
                        $adjusted['damage_parts'] = array_values(array_map(
                            static fn (array $part) => [
                                'id' => (string) ($part['id'] ?? ''),
                                'expression' => strtolower((string) $part['expression']),
                                'type' => (string) ($part['type'] ?? ''),
                                'bonus' => (int) ($part['bonus'] ?? 0),
                                'abilities' => array_values(array_unique($part['abilities'] ?? [])),
                            ],
                            $attackConfig['damage_parts']
                        ));
                    }
                    $weapon['attack_config'] = $adjusted;
                }
                $capacity = $service->loadingCapacity($weapon);
                if ($capacity !== null) {
                    $current = max(0, min($capacity, (int) ($weapon['loading_current'] ?? $capacity)));
                    if ($action === 'decrement') $current = max(0, $current - 1);
                    if ($action === 'increment') $current = min($capacity, $current + 1);
                    if ($action === 'reload') $current = $capacity;
                    $weapon['loading_current'] = $current;
                } elseif ($action !== null) {
                    throw ValidationException::withMessages([
                        'ammo_action' => 'Esta arma não possui Carregamento configurado.',
                    ]);
                }
                $properties['weapon'] = $weapon;
                $locked->properties = $properties;
                $locked->save();
            }
            $response = parent::updateEquipped($request, $character, $locked);
            $locked->refresh();
            $payload = $response->getData(true);
            if (isset($payload['item']) && is_array($payload['item'])) {
                $payload['item']['properties'] = $locked->properties;
            }
            $payload['weapon_attack'] = $service->row($character, $locked);
            if ($action !== null) $payload['message'] = 'Munição atualizada.';
            return $response->setData($payload);
        }, 3);
    }

    /**
     * V66.2.101 — pergaminhos não exigem feitiço preparado nem gastam slots.
     * Reutiliza o endpoint do rastreador e a transação do sistema existente.
     * Outros itens continuam usando o método original, sem alterações.
     */
    public function updateFeatureUses(
        Request $request,
        Character $character,
        CharacterItem $item,
        string $feature
    ): JsonResponse {
        if (!$request->boolean('scroll_cast')) {
            return parent::updateFeatureUses($request, $character, $item, $feature);
        }

        $this->authorizePlayerCharacter($character);
        abort_unless((int) $item->character_id === (int) $character->id, 404);
        if (!ctype_digit($feature)) abort(404);
        $input = $request->validate([
            'scroll_cast' => ['required', 'accepted'],
            'confirm_replace_concentration' => ['sometimes', 'boolean'],
        ]);
        $index = (int) $feature;

        return DB::transaction(function () use (
            $request, $character, $item, $feature, $index, $input
        ): JsonResponse {
            $manager = app(CharacterConcentrationManager::class);
            $character = $manager->lockCharacter($character);
            $locked = CharacterItem::query()
                ->where('character_id', $character->id)
                ->lockForUpdate()->findOrFail($item->id);
            $properties = is_array($locked->properties) ? $locked->properties : [];
            $scroll = is_array($properties['spell_scroll'] ?? null)
                ? $properties['spell_scroll'] : [];
            $spellId = (int) ($scroll['spell_id'] ?? 0);
            if ($spellId <= 0) {
                throw ValidationException::withMessages([
                    'spell' => 'Vincule um feitiço do Grimório ao pergaminho antes de conjurar.',
                ]);
            }
            $spell = Spell::query()->find($spellId);
            if (!$spell) {
                throw ValidationException::withMessages([
                    'spell' => 'O feitiço deste pergaminho não está disponível no Grimório.',
                ]);
            }
            if (!empty($scroll['consumed'])) {
                throw ValidationException::withMessages([
                    'scroll' => 'Este pergaminho já foi consumido.',
                ]);
            }
            $features = is_array($properties['features'] ?? null)
                ? array_values($properties['features']) : [];
            $selected = $features[$index] ?? null;
            $rules = is_array($selected) && (
                ($selected['system_key'] ?? null) === 'spell_scroll_rules'
                || trim((string) ($selected['title'] ?? '')) === 'Uso e Conjuração de Pergaminhos'
            );
            if (!$rules) {
                throw ValidationException::withMessages([
                    'feature' => 'Selecione a habilidade de uso do pergaminho.',
                ]);
            }
            $usage = is_array($selected['usage'] ?? null) ? $selected['usage'] : [];
            if (empty($usage['enabled'])) {
                // Pergaminho antigo: inicializa UM uso consumível, não uma nova habilidade.
                $usage = [
                    'enabled' => true, 'counter_mode' => 'spend',
                    'current' => 1, 'max' => 1, 'recovery' => 'single_use',
                ];
                $features[$index]['usage'] = $usage;
                $properties['features'] = $features;
                $locked->properties = $properties;
                $locked->save();
            }
            $remaining = (int) ($usage['current'] ?? 0);
            if ($remaining < 1) {
                throw ValidationException::withMessages([
                    'scroll' => 'Este pergaminho não possui conjurações restantes.',
                ]);
            }
            if ((bool) $spell->concentration && $manager->hasActiveConcentration($character)
                && !($input['confirm_replace_concentration'] ?? false)) {
                throw ValidationException::withMessages([
                    'concentration' => 'Existe uma concentração ativa. Confirme a substituição para conjurar.',
                ]);
            }

            // O rastreador e a concentração são atualizados atomicamente.
            $request->merge(['current' => $remaining - 1]);
            $response = parent::updateFeatureUses($request, $character, $locked, $feature);
            $locked->refresh();
            $properties = is_array($locked->properties) ? $locked->properties : [];
            $scroll = is_array($properties['spell_scroll'] ?? null)
                ? $properties['spell_scroll'] : [];
            $isSingleUse = ($usage['recovery'] ?? null) === 'single_use';
            if ($isSingleUse) {
                $scroll['consumed'] = true;
                $properties['spell_scroll'] = $scroll;
                $locked->properties = $properties;
                $locked->save();
            }

            $duration = app(SpellDurationResolver::class)->resolve($spell);
            if ((bool) $spell->concentration) {
                $manager->replaceFeatureWithSpell($character);
                $manager->replaceTransformationWithSpell($character);
                $manager->startSpell($character, (int) $spell->id);
                $combat = $character->combat()->lockForUpdate()->first();
                if ($combat) {
                    $overrides = is_array($combat->overrides) ? $combat->overrides : [];
                    $meta = is_array($overrides['concentration'] ?? null)
                        ? $overrides['concentration'] : [];
                    $meta['duration_total_rounds'] = $duration['total_rounds'];
                    $meta['source_item_id'] = (int) $locked->id;
                    $meta['source_item_name'] = (string) $locked->name;
                    $overrides['concentration'] = $meta;
                    $combat->overrides = $overrides;
                    $combat->save();
                }
            } elseif (($duration['total_rounds'] ?? null) !== null) {
                $this->registerTimedScroll($character, $locked, $spell, (int) $duration['total_rounds']);
            }

            $level = max(0, min(9, (int) $spell->level));
            // Valores iguais aos padrões do editor; personalizados têm precedência.
            $defaults = [
                [13,5], [13,5], [13,5], [15,7], [15,7],
                [17,9], [17,9], [18,10], [18,10], [19,11],
            ];
            $dc = filter_var($scroll['spell_dc'] ?? null, FILTER_VALIDATE_INT);
            $attack = filter_var($scroll['spell_attack_bonus'] ?? null, FILTER_VALIDATE_INT);
            $dc = $dc !== false && $dc >= 1 && $dc <= 99 ? $dc : $defaults[$level][0];
            $attack = $attack !== false && $attack >= -30 && $attack <= 99
                ? $attack : $defaults[$level][1];
            $payload = $response->getData(true);
            $payload['item']['properties'] = $locked->fresh()->properties;
            $payload['message'] = "{$spell->name} conjurado pelo pergaminho.";
            $payload['cast'] = [
                'spell_id' => (int) $spell->id,
                'spell_name' => (string) $spell->name,
                'cast_level' => $level,
                'spell_save_dc' => $dc,
                'spell_attack_bonus' => $attack,
                'concentration' => (bool) $spell->concentration,
                'duration_label' => $duration['label'],
                'duration_total_rounds' => $duration['total_rounds'],
                'resource' => [
                    'type' => 'scroll', 'item_id' => (int) $locked->id,
                    'remaining' => $remaining - 1,
                ],
            ];
            $payload['scroll_timers'] = $this->scrollTimers($character);
            return $response->setData($payload);
        }, 3);
    }

    /** Sincronização já existente, com durações de pergaminhos não concentrados. */
    public function sync(Request $request, Character $character): JsonResponse
    {
        $this->authorizePlayerCharacter($character);
        // O HUD consulta somente contadores: não consome notificações de
        // transferência que pertencem ao polling normal do Inventário.
        if ($request->boolean('scroll_timers_only')) {
            return response()->json([
                'success' => true,
                'scroll_timers' => $this->scrollTimers($character),
            ])->header('Cache-Control', 'private, no-store');
        }
        $response = parent::sync($request, $character);
        $payload = $response->getData(true);
        $payload['scroll_timers'] = $this->scrollTimers($character);
        return $response->setData($payload);
    }

    private function registerTimedScroll(
        Character $character, CharacterItem $item, Spell $spell, int $total
    ): void {
        $combat = $character->combat()->lockForUpdate()->first();
        if (!$combat) {
            $combat = $character->combat()->create([
                'experience_points' => 0, 'current_hp' => 0, 'max_hp' => 1,
                'temporary_hp' => 0, 'temporary_max_hp' => 0, 'hit_dice' => [],
                'armor_class' => 10, 'speed' => 30, 'initiative_bonus' => 0,
                'death_save_successes' => 0, 'death_save_failures' => 0,
                'concentration_active' => false, 'concentration_spell_id' => null,
                'exhaustion_level' => 0, 'conditions' => [],
                'damage_resistances' => [], 'damage_immunities' => [],
                'damage_vulnerabilities' => [], 'overrides' => [],
            ]);
        }
        $encounter = DB::table('combat_players as cp')
            ->join('combats as c', 'c.id', '=', 'cp.combat_id')
            ->where('cp.character_id', $character->id)
            ->where('c.is_active', true)
            ->orderByDesc('c.id')
            ->first(['c.id', 'c.current_round']);
        $overrides = is_array($combat->overrides) ? $combat->overrides : [];
        $effects = is_array($overrides['scroll_timers'] ?? null)
            ? $overrides['scroll_timers'] : [];
        $effects = array_values(array_filter($effects, fn ($effect) =>
            is_array($effect) && !empty($effect['id'])));
        $effects[] = [
            'id' => (string) Str::uuid(),
            'spell_id' => (int) $spell->id,
            'spell_name' => (string) $spell->name,
            'item_id' => (int) $item->id,
            'duration_total_rounds' => $total,
            'combat_id' => $encounter ? (int) $encounter->id : null,
            'started_round' => $encounter ? max(1, (int) $encounter->current_round) : null,
            'started_at' => now()->toIso8601String(),
        ];
        $overrides['scroll_timers'] = array_slice($effects, -30);
        $combat->overrides = $overrides;
        $combat->save();
    }

    /** Retorna o contador real das rodadas do combate ativo, sem usar timers do browser. */
    private function scrollTimers(Character $character): array
    {
        $combat = $character->combat()->first();
        $overrides = is_array($combat?->overrides) ? $combat->overrides : [];
        $effects = is_array($overrides['scroll_timers'] ?? null)
            ? $overrides['scroll_timers'] : [];
        $combatIds = collect($effects)->pluck('combat_id')->filter()->unique()->all();
        $rounds = DB::table('combats')->whereIn('id', $combatIds)
            ->get(['id','current_round','is_active'])->keyBy('id');
        $result = [];
        foreach ($effects as $effect) {
            if (!is_array($effect)) continue;
            $total = max(1, (int) ($effect['duration_total_rounds'] ?? 0));
            $encounter = $rounds->get((int) ($effect['combat_id'] ?? 0));
            $elapsed = $encounter && isset($effect['started_round'])
                ? max(0, (int) $encounter->current_round - (int) $effect['started_round']) : 0;
            $remaining = max(0, $total - $elapsed);
            if ($remaining === 0) continue;
            $result[] = [
                'id' => $effect['id'],
                'spell_name' => (string) ($effect['spell_name'] ?? 'Feitiço'),
                'item_id' => (int) ($effect['item_id'] ?? 0),
                'total_rounds' => $total,
                'elapsed_rounds' => $elapsed,
                'remaining_rounds' => $remaining,
                'combat_active' => (bool) ($encounter?->is_active ?? false),
            ];
        }
        return $result;
    }

    private function authorizePlayerCharacter(Character $character): void
    {
        abort_unless((int) $character->user_id === (int) Auth::id(), 403);
    }

    private function forbidClientRevelation(Request $request): void
    {
        // A revelação de maldições do Mestre continua sob controle do Mestre.
        abort_if($request->has('curse_revealed'), 403,
            'A revelação de uma maldição não pode ser alterada pelo jogador.');
    }

    private function validatedCurseFields(Request $request): array
    {
        return Validator::make($request->only(self::CURSE_FIELDS), [
            'is_cursed' => ['sometimes', 'boolean'],
            'curse_name' => ['sometimes', 'nullable', 'string', 'max:160'],
            'curse_description' => ['sometimes', 'nullable', 'string', 'max:30000'],
        ])->validate();
    }

    private function validateCurseName(
        array $requested,
        bool $active,
        ?CharacterItem $existing = null
    ): void {
        if (!$active) return;
        $name = trim((string) ($requested['curse_name'] ?? $existing?->curse_name ?? ''));
        if ($name === '') {
            throw ValidationException::withMessages([
                'curse_name' => 'Informe um nome para a maldição.',
            ]);
        }
    }

    private function stripCurseFields(Request $request): void
    {
        // FormData chega por request->request. Mantém suporte a JSON também.
        foreach (self::CURSE_FIELDS as $field) {
            $request->request->remove($field);
            $request->query->remove($field);
            if ($request->isJson()) {
                $request->json()->remove($field);
            }
        }
    }

    private function persistPlayerCurse(
        CharacterItem $item,
        array $requested,
        bool $active
    ): void {
        $this->validateCurseName($requested, $active, $item);
        $properties = is_array($item->properties) ? $item->properties : [];
        $properties['_player_curse_created'] = $active;

        $item->forceFill([
            'is_cursed' => $active,
            'curse_revealed' => $active,
            'curse_name' => $active
                ? trim((string) ($requested['curse_name'] ?? $item->curse_name ?? '')) : null,
            'curse_description' => $active
                ? (string) ($requested['curse_description'] ?? $item->curse_description ?? '') : null,
            'properties' => $properties,
        ])->save();
        $item->refresh();
    }

    private function refreshResponseItem(JsonResponse $response, CharacterItem $item): JsonResponse
    {
        $payload = $response->getData(true);
        $visible = (bool) $item->is_cursed && (bool) $item->curse_revealed;
        $payload['item']['properties'] = $item->properties ?? [];
        $payload['item']['is_cursed'] = $visible;
        $payload['item']['curse_revealed'] = $visible;
        $payload['item']['curse_name'] = $visible ? $item->curse_name : null;
        $payload['item']['curse_description'] = $visible ? $item->curse_description : null;
        return $response->setData($payload);
    }
}
