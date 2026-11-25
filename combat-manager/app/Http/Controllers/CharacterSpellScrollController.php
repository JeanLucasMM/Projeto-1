<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\Spell;
use App\Services\Characters\CharacterConcentrationManager;
use App\Services\Spells\SpellDisplayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * V66.2.77 — conjuração de um feitiço por pergaminho.
 *
 * A origem da autorização é o próprio item: o personagem não precisa conhecer
 * nem preparar o feitiço, e um livro não habilitado pela campanha não é
 * permanentemente liberado no Grimório. Não consome espaços de magia.
 */
class CharacterSpellScrollController extends Controller
{
    private function requireScroll(Character $character, CharacterItem $item): Spell
    {
        Gate::authorize('update', $character);
        abort_unless((int) $item->character_id === (int) $character->id, 404);

        $properties = is_array($item->properties) ? $item->properties : [];
        $scroll = is_array($properties['spell_scroll'] ?? null)
            ? $properties['spell_scroll'] : [];
        $spellId = (int) ($scroll['spell_id'] ?? 0);

        if ($spellId <= 0) {
            throw ValidationException::withMessages([
                'spell' => 'O pergaminho não possui um feitiço válido vinculado.',
            ]);
        }

        // Fontes não liberadas à campanha continuam válidas quando um item
        // legítimo já foi concedido; feitiços excluídos/inativos não são lançados.
        return Spell::query()
            ->where('active', true)
            ->where('upstream_missing', false)
            ->findOrFail($spellId);
    }

    public function show(
        Character $character,
        CharacterItem $item,
        SpellDisplayService $display
    ): JsonResponse {
        $spell = $this->requireScroll($character, $item);

        // Carregamento pontual: nenhuma descrição do catálogo inteiro na ficha.
        return response()->json([
            'success' => true,
            'spell' => $display->payload($spell),
        ]);
    }

    public function cast(
        Request $request,
        Character $character,
        CharacterItem $item
    ): JsonResponse {
        $this->requireScroll($character, $item);

        $input = $request->validate([
            'confirm_replace_concentration' => ['sometimes', 'boolean'],
        ]);

        $result = DB::transaction(function () use ($character, $item, $input): array {
            $manager = app(CharacterConcentrationManager::class);
            $character = $manager->lockCharacter($character);

            // Bloqueio exclusivo: duas abas não podem gastar a mesma carga.
            $locked = CharacterItem::query()
                ->where('character_id', $character->id)
                ->lockForUpdate()
                ->findOrFail($item->id);

            $spell = $this->requireScroll($character, $locked);
            $properties = is_array($locked->properties) ? $locked->properties : [];
            $scroll = is_array($properties['spell_scroll'] ?? null)
                ? $properties['spell_scroll'] : [];
            $features = is_array($properties['features'] ?? null)
                ? array_values($properties['features']) : [];

            // Usos pertencem à primeira habilidade de regras, não à habilidade
            // textual de conjuração. Compatível com itens legados sem system_key.
            $rulesIndex = null;
            foreach ($features as $index => $feature) {
                if (is_array($feature)
                    && ($feature['system_key'] ?? '') === 'spell_scroll_rules') {
                    $rulesIndex = $index;
                    break;
                }
            }
            if ($rulesIndex === null) {
                foreach ($features as $index => $feature) {
                    if (is_array($feature)
                        && mb_strtolower(trim((string) ($feature['title'] ?? ''))) ===
                           mb_strtolower('Uso e Conjuração de Pergaminhos')) {
                        $rulesIndex = $index;
                        break;
                    }
                }
            }
            if ($rulesIndex === null && count($features) > 0
                && is_array($features[0])
                && (bool) ($features[0]['usage']['enabled'] ?? false)) {
                $rulesIndex = 0;
            }

            if ($rulesIndex === null) {
                throw ValidationException::withMessages([
                    'item' => 'O pergaminho não possui uma habilidade de usos válida.',
                ]);
            }

            $usage = is_array($features[$rulesIndex]['usage'] ?? null)
                ? $features[$rulesIndex]['usage'] : [];
            $track = (bool) ($usage['enabled'] ?? false);
            $remaining = null;

            if ($track) {
                if (($usage['counter_mode'] ?? 'spend') !== 'spend') {
                    throw ValidationException::withMessages([
                        'item' => 'O rastreador do pergaminho deve gastar cargas.',
                    ]);
                }
                $current = max(0, (int) ($usage['current'] ?? 0));
                if ($current < 1) {
                    throw ValidationException::withMessages([
                        'item' => 'Este pergaminho não possui usos disponíveis.',
                    ]);
                }
                $remaining = $current - 1;
            }

            $castLevel = (int) ($scroll['spell_level'] ?? $spell->level);
            if ($castLevel < (int) $spell->level || $castLevel > 9) {
                throw ValidationException::withMessages([
                    'spell' => 'O nível do pergaminho não corresponde ao feitiço.',
                ]);
            }

            if ((bool) $spell->concentration
                && $manager->hasActiveConcentration($character)
                && !($input['confirm_replace_concentration'] ?? false)) {
                throw ValidationException::withMessages([
                    'concentration' => 'Uma concentração já está ativa. Confirme a substituição antes de conjurar.',
                ]);
            }

            $stats = [
                [13, 5], [13, 5], [13, 5], [15, 7], [15, 7],
                [17, 9], [17, 9], [18, 10], [18, 10], [19, 11],
            ][$castLevel];

            $dc = isset($scroll['spell_dc']) && is_numeric($scroll['spell_dc'])
                ? (int) $scroll['spell_dc'] : $stats[0];
            $attack = isset($scroll['spell_attack_bonus']) && is_numeric($scroll['spell_attack_bonus'])
                ? (int) $scroll['spell_attack_bonus'] : $stats[1];

            // A concentração é global, igual à usada pelo Grimório, por
            // Transformações e por outras habilidades.
            $endedFeature = null;
            $endedTransformation = null;
            if ((bool) $spell->concentration) {
                $endedFeature = $manager->replaceFeatureWithSpell($character);
                $endedTransformation = $manager->replaceTransformationWithSpell($character);
                $manager->startSpell($character, (int) $spell->id);
            }

            // Consumo e concentração são atômicos; um erro reverte ambos.
            if ($track) {
                $features[$rulesIndex]['usage']['current'] = $remaining;
                $properties['features'] = $features;
                $locked->properties = $properties;
                $locked->save();
            }

            return [
                'spell_id' => (int) $spell->id,
                'spell_name' => (string) $spell->name,
                'cast_level' => $castLevel,
                'concentration' => (bool) $spell->concentration,
                'spell_save_dc' => $dc,
                'spell_attack_bonus' => $attack,
                'resource' => [
                    'type' => 'spell_scroll',
                    'item_id' => (int) $locked->id,
                    'current' => $remaining,
                    'feature_index' => $rulesIndex,
                ],
                'ended_feature' => $endedFeature,
                'ended_transformation' => $endedTransformation,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $result['spell_name'] . ' conjurado pelo pergaminho.',
            'cast' => $result,
        ]);
    }
}
