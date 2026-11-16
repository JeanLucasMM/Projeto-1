<?php

namespace App\Services\Spells;

use App\Models\Character;
use App\Models\CharacterSpell;
use App\Models\CharacterSpellSlot;
use App\Models\CharacterSpellcasting;
use App\Models\Spell;
use App\Services\Characters\CharacterConcentrationManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SpellCastService
{
    public function cast(
        Character $character,
        CharacterSpell $characterSpell,
        Spell $spell,
        array $options
    ): array {
        $resourceType = (string) (
            $options['resource_type']
            ?? ((int) $spell->level === 0 ? 'none' : 'slot')
        );

        $castLevel = max(
            (int) $spell->level,
            (int) ($options['cast_level'] ?? $spell->level)
        );

        return DB::transaction(function () use (
            $character,
            $characterSpell,
            $spell,
            $resourceType,
            $castLevel,
            $options
        ): array {
            // Mesma ordem de lock usada na ativação das transformações.
            $manager = app(CharacterConcentrationManager::class);
            $character = $manager->lockCharacter($character);
            $resource = null;

            // A magia continua única na ficha, mas o perfil escolhido deve
            // corresponder a uma origem efetivamente vinculada ao personagem.
            $origins = DB::table('character_spell_profiles')
                ->where('character_spell_id', $characterSpell->id)
                ->get()->keyBy('spellcasting_id');
            $selectedProfileId = isset($options['spellcasting_id'])
                ? (int) $options['spellcasting_id'] : 0;
            if ($selectedProfileId === 0 && $origins->count() === 1) {
                $selectedProfileId = (int) $origins->keys()->first();
            }
            if ($selectedProfileId === 0 && $origins->count() > 1) {
                throw ValidationException::withMessages([
                    'spellcasting_id' => 'Escolha o perfil de conjuração.',
                ]);
            }
            if ($selectedProfileId === 0 && $characterSpell->spellcasting_id) {
                $selectedProfileId = (int) $characterSpell->spellcasting_id;
            }

            $selectedProfile = null;
            if ($selectedProfileId > 0) {
                $selectedProfile = CharacterSpellcasting::query()
                    ->where('character_id', $character->id)
                    ->where('is_active', true)
                    ->with('character.abilities')
                    ->find($selectedProfileId);
                if (!$selectedProfile) {
                    throw ValidationException::withMessages([
                        'spellcasting_id' => 'Perfil inválido ou desativado.',
                    ]);
                }
                if ($origins->isNotEmpty() && !$origins->has($selectedProfileId)) {
                    throw ValidationException::withMessages([
                        'spellcasting_id' => 'Esta magia não pertence ao perfil selecionado.',
                    ]);
                }
                $origin = $origins->get($selectedProfileId);
                $prepared = $origin
                    ? ((bool) $origin->prepared || (bool) $origin->always_prepared)
                    : $characterSpell->isPrepared();
                if ((int) $spell->level > 0 && !$prepared
                    && !($characterSpell->cast_without_slot && $resourceType === 'none')
                    && $resourceType !== 'free') {
                    throw ValidationException::withMessages([
                        'spellcasting_id' => 'Prepare o feitiço neste perfil antes de conjurá-lo.',
                    ]);
                }
            }

            if ($selectedProfileId === 0 && (int) $spell->level > 0
                && $resourceType === 'slot' && !$characterSpell->isPrepared()) {
                throw ValidationException::withMessages([
                    'spell' => 'O feitiço precisa estar preparado antes de usar um espaço.',
                ]);
            }

            if ((int) $spell->level === 0 && ($resourceType !== 'none' || $castLevel !== 0)) {
                throw ValidationException::withMessages([
                    'resource_type' => 'Truques não consomem espaços de magia.',
                ]);
            }
            if ((int) $spell->level > 0 && $resourceType === 'none'
                && !$characterSpell->cast_without_slot) {
                throw ValidationException::withMessages([
                    'resource_type' => 'Esta magia exige um espaço ou uso gratuito autorizado.',
                ]);
            }
            if ($castLevel > 9 || $castLevel < (int) $spell->level) {
                throw ValidationException::withMessages([
                    'cast_level' => 'Círculo de conjuração inválido.',
                ]);
            }

            // A confirmação é validada no servidor antes de consumir slots/usos.
            if ((bool) $spell->concentration
                && $manager->hasActiveConcentration($character)
                && !($options['confirm_replace_concentration'] ?? false)
            ) {
                throw ValidationException::withMessages([
                    'concentration' => 'Uma concentração já está ativa. Confirme a substituição antes de conjurar.',
                ]);
            }

            if ($resourceType === 'slot') {
                $slotType = trim((string) ($options['slot_type'] ?? 'spell'));
                if ($slotType !== 'spell') {
                    if (!preg_match('/^pact:([1-9][0-9]*)$/D', $slotType, $matches)) {
                        throw ValidationException::withMessages(['slot_type' => 'Tipo de espaço inválido.']);
                    }
                    $pactProfile = CharacterSpellcasting::query()
                        ->where('character_id', $character->id)
                        ->where('caster_type', 'pact')
                        ->where('is_active', true)
                        ->find((int) $matches[1]);
                    if (!$pactProfile || $castLevel > 5) {
                        throw ValidationException::withMessages(['slot_type' => 'Espaço de pacto inválido.']);
                    }
                }
                $slot = CharacterSpellSlot::query()
                    ->where('character_id', $character->id)
                    ->where('slot_type', $slotType)
                    ->where('slot_level', $castLevel)
                    ->lockForUpdate()->first();

                if (!$slot || (int) $slot->current < 1) {
                    throw ValidationException::withMessages([
                        'slot' => 'Não há um slot disponível nesse nível.',
                    ]);
                }
                $slot->current = (int) $slot->current - 1;
                $slot->save();
                $resource = [
                    'type' => 'slot',
                    'slot_type' => $slot->slot_type,
                    'slot_level' => (int) $slot->slot_level,
                    'current' => (int) $slot->current,
                    'maximum' => (int) $slot->maximum,
                ];
            } elseif ($resourceType === 'free') {
                $locked = CharacterSpell::query()
                    ->whereKey($characterSpell->id)
                    ->lockForUpdate()->firstOrFail();
                if ($locked->free_casts_current === null
                    || (int) $locked->free_casts_current < 1
                ) {
                    throw ValidationException::withMessages([
                        'free_cast' => 'Não há conjurações gratuitas disponíveis.',
                    ]);
                }
                $locked->free_casts_current = (int) $locked->free_casts_current - 1;
                $locked->save();
                $resource = [
                    'type' => 'free',
                    'current' => (int) $locked->free_casts_current,
                    'maximum' => (int) ($locked->free_casts_max ?? 0),
                ];
            } elseif ($resourceType !== 'none') {
                throw ValidationException::withMessages([
                    'resource_type' => 'Recurso de conjuração inválido.',
                ]);
            }

            $endedTransformation = null;
            $endedFeature = null;
            if ((bool) $spell->concentration) {
                $endedFeature = $manager->replaceFeatureWithSpell($character);
                // Uma nova concentração substitui a da forma ativa.
                // A reversão da forma e a exaustão usam a mesma transação do slot.
                $endedTransformation = $manager->replaceTransformationWithSpell($character);

                $manager->startSpell($character, (int) $spell->id);
            }

            return [
                'spell_id' => (int) $spell->id,
                'spell_name' => $spell->name,
                'cast_level' => $castLevel,
                'concentration' => (bool) $spell->concentration,
                'resource' => $resource,
                'spellcasting_id' => $selectedProfile?->id,
                'casting_class' => $selectedProfile?->class,
                'spell_save_dc' => $selectedProfile?->spell_save_dc,
                'spell_attack_bonus' => $selectedProfile?->spell_attack_bonus,
                'ended_transformation' => $endedTransformation,
                'ended_feature' => $endedFeature,
            ];
        });
    }

    public function stopConcentration(Character $character): void
    {
        app(CharacterConcentrationManager::class)->stop($character);
    }

}
