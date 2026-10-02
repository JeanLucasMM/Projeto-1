<?php

namespace App\Services\Transformations;

use App\Models\Npc;
use App\Support\Dictionaries\Alignments;
use App\Support\Dictionaries\Conditions;
use App\Support\Dictionaries\DamageTypes;
use App\Support\Dictionaries\Languages;
use App\Support\Dictionaries\NpcSizes;
use App\Support\Dictionaries\NpcTypes;
use App\Support\Dictionaries\Senses;
use App\Support\Dictionaries\SkillNames;
use Illuminate\Support\Collection;

class MonsterTransformationSheetResolver
{
    /*
    |--------------------------------------------------------------------------
    | SUPORTE DE FORMATO
    |--------------------------------------------------------------------------
    |
    | Transformações de criatura aceitam exclusivamente fichas criadas pelo
    | Construtor de NPC do Spellbound.
    |--------------------------------------------------------------------------
    */
    public function supports(
        Npc $npc
    ): bool {
        $json =
            $this->jsonArray(
                $npc->json_data
                ?? []
            );

        return (
            $json['format']
            ?? null
        )
        ===
        'npc-builder';
    }


    /*
    |--------------------------------------------------------------------------
    | API PÚBLICA
    |--------------------------------------------------------------------------
    */

    public function resolve(
        Npc $npc
    ): array {
        $json =
            $this->jsonArray(
                $npc->json_data
                ?? []
            );

        if (
            (
                $json['format']
                ?? null
            )
            !==
            'npc-builder'
        ) {
            throw new \InvalidArgumentException(
                'Transformações de criatura aceitam somente fichas criadas pelo Construtor de NPC do Spellbound.'
            );
        }

        return $this->resolveNative(
            $npc,
            $json
        );
    }


    public function maxHp(
        Npc $npc
    ): int {
        return max(
            1,
            (int) (
                $this->resolve(
                    $npc
                )['max_hp']
                ?? 1
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NATIVE / NPC BUILDER
    |--------------------------------------------------------------------------
    |
    | O formato Native NÃO passa pelo NpcViewModelFactory.
    |
    | Ele é interpretado a partir do mesmo contrato salvo pelo npc-builder.js:
    | header, combat, speed, abilities, savingThrows, skills, features,
    | attacks, multiAttacks, actions, bonusActions, reactions etc.
    |--------------------------------------------------------------------------
    */

    private function resolveNative(
        Npc $npc,
        array $json
    ): array {
        $header =
            $this->arrayValue(
                $json,
                'header'
            );

        $combat =
            $this->arrayValue(
                $json,
                'combat'
            );

        $speed =
            $this->arrayValue(
                $json,
                'speed'
            );

        $abilities =
            $this->arrayValue(
                $json,
                'abilities'
            );

        $savingThrows =
            $this->arrayValue(
                $json,
                'savingThrows'
            );

        $skills =
            collect(
                $this->arrayValue(
                    $json,
                    'skills'
                )
            );

        $name =
            $this->cleanText(
                $header['name']
                ?? $npc->name
                ?? 'Forma de Monstro'
            );

        $challengeRating =
            $this->cleanText(
                $header['challengeRating']
                ?? $header['cr']
                ?? $header['nd']
                ?? $npc->challenge_rating
                ?? ''
            );

        $proficiencyBonus =
            $this->proficiencyFromCr(
                $challengeRating
            );

        /*
         * Caso uma versão do Builder tenha persistido explicitamente o PB,
         * respeitamos o valor salvo.
         */
        if (
            isset(
                $header['proficiencyBonus']
            )
            && is_numeric(
                $header['proficiencyBonus']
            )
        ) {
            $proficiencyBonus =
                (int) $header['proficiencyBonus'];
        }

        $abilityDefinitions = [
            'str' => [
                'short' => 'STR',
                'label' => 'Força',
            ],

            'dex' => [
                'short' => 'DEX',
                'label' => 'Destreza',
            ],

            'con' => [
                'short' => 'CON',
                'label' => 'Constituição',
            ],

            'int' => [
                'short' => 'INT',
                'label' => 'Inteligência',
            ],

            'wis' => [
                'short' => 'WIS',
                'label' => 'Sabedoria',
            ],

            'cha' => [
                'short' => 'CHA',
                'label' => 'Carisma',
            ],
        ];

        $resolvedAbilities =
            collect(
                $abilityDefinitions
            )
                ->map(
                    function (
                        array $definition,
                        string $key
                    ) use (
                        $abilities,
                        $savingThrows,
                        $proficiencyBonus
                    ): array {
                        $score =
                            (int) (
                                $abilities[$key]
                                ?? 10
                            );

                        $modifier =
                            $this->abilityModifier(
                                $score
                            );

                        $saveConfig =
                            isset(
                                $savingThrows[$key]
                            )
                            && is_array(
                                $savingThrows[$key]
                            )
                                ? $savingThrows[$key]
                                : [];

                        $proficient =
                            (bool) (
                                $saveConfig['proficient']
                                ?? false
                            );

                        $save =
                            $modifier
                            +
                            (
                                $proficient
                                    ? $proficiencyBonus
                                    : 0
                            )
                            +
                            (
                                (int) (
                                    $saveConfig['bonus']
                                    ?? 0
                                )
                            );

                        return [
                            'key' =>
                                $key,

                            'short' =>
                                $definition['short'],

                            'label' =>
                                $definition['label'],

                            'score' =>
                                $score,

                            'modifier' =>
                                $modifier,

                            'save' =>
                                $save,

                            'save_proficient' =>
                                $proficient,
                        ];
                    }
                )
                ->values()
                ->all();

        $abilityByKey =
            collect(
                $resolvedAbilities
            )
                ->keyBy('key');

        /*
        |--------------------------------------------------------------------------
        | Classe de Armadura
        |--------------------------------------------------------------------------
        |
        | Espelha exatamente o getter armorClass do npc-builder.js:
        |
        | ac_base + modificador de DEX + ac_bonus
        |--------------------------------------------------------------------------
        */
        $armorClass =
            (
                (int) (
                    $combat['ac_base']
                    ?? $combat['ac']
                    ?? 10
                )
            )
            +
            (
                (int) data_get(
                    $abilityByKey->get(
                        'dex'
                    ),
                    'modifier',
                    0
                )
            )
            +
            (
                (int) (
                    $combat['ac_bonus']
                    ?? 0
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Pontos de Vida
        |--------------------------------------------------------------------------
        */
        $maxHp =
            $this->nativeMaxHp(
                $combat,
                $abilities
            );

        /*
        |--------------------------------------------------------------------------
        | Iniciativa
        |--------------------------------------------------------------------------
        */
        $initiative =
            (int) data_get(
                $abilityByKey->get(
                    'dex'
                ),
                'modifier',
                0
            );

        $initiativeSkill =
            $skills
                ->first(
                    function ($skill): bool {
                        if (!is_array($skill)) {
                            return false;
                        }

                        return in_array(
                            mb_strtolower(
                                trim(
                                    (string) (
                                        $skill['key']
                                        ?? ''
                                    )
                                )
                            ),
                            [
                                'initiative',
                                'iniciativa',
                            ],
                            true
                        );
                    }
                );

        if (
            is_array(
                $initiativeSkill
            )
        ) {
            $initiative +=
                $this->nativeSkillProficiencyBonus(
                    $initiativeSkill,
                    $proficiencyBonus
                );

            $initiative +=
                (int) (
                    $initiativeSkill['bonus']
                    ?? 0
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Perícias
        |--------------------------------------------------------------------------
        |
        | O statblock do Builder mostra as perícias realmente treinadas.
        | "enabled" sozinho não é tratado como proficiência.
        |--------------------------------------------------------------------------
        */
        $resolvedSkills =
            $skills
                ->filter(
                    function ($skill): bool {
                        if (!is_array($skill)) {
                            return false;
                        }

                        $key =
                            mb_strtolower(
                                trim(
                                    (string) (
                                        $skill['key']
                                        ?? ''
                                    )
                                )
                            );

                        if (
                            in_array(
                                $key,
                                [
                                    'initiative',
                                    'iniciativa',
                                ],
                                true
                            )
                        ) {
                            return false;
                        }

                        return
                            !empty(
                                $skill['proficient']
                            )
                            || !empty(
                                $skill['expertise']
                            );
                    }
                )
                ->map(
                    function (
                        array $skill
                    ) use (
                        $abilities,
                        $proficiencyBonus
                    ): array {
                        $abilityKey =
                            mb_strtolower(
                                trim(
                                    (string) (
                                        $skill['ability']
                                        ?? 'str'
                                    )
                                )
                            );

                        $score =
                            (int) (
                                $abilities[$abilityKey]
                                ?? 10
                            );

                        $value =
                            $this->abilityModifier(
                                $score
                            );

                        $value +=
                            $this->nativeSkillProficiencyBonus(
                                $skill,
                                $proficiencyBonus
                            );

                        $value +=
                            (int) (
                                $skill['bonus']
                                ?? 0
                            );

                        $key =
                            trim(
                                (string) (
                                    $skill['key']
                                    ?? ''
                                )
                            );

                        $label =
                            $this->dictionaryLabel(
                                SkillNames::class,
                                $key,
                                (
                                    $skill['label']
                                    ?? $key
                                )
                            );

                        return [
                            'name' =>
                                $label,

                            'value' =>
                                $value,
                        ];
                    }
                )
                ->values()
                ->all();

        /*
        |--------------------------------------------------------------------------
        | Percepção Passiva
        |--------------------------------------------------------------------------
        |
        | Espelha:
        | 10 + getSkillValue(perception) + passivePerceptionBonus
        |--------------------------------------------------------------------------
        */
        $wisdomModifier =
            (int) data_get(
                $abilityByKey->get(
                    'wis'
                ),
                'modifier',
                0
            );

        $perceptionSkill =
            $skills
                ->first(
                    function ($skill): bool {
                        if (!is_array($skill)) {
                            return false;
                        }

                        return in_array(
                            mb_strtolower(
                                trim(
                                    (string) (
                                        $skill['key']
                                        ?? ''
                                    )
                                )
                            ),
                            [
                                'perception',
                                'percepcao',
                                'percepção',
                            ],
                            true
                        );
                    }
                );

        $perceptionValue =
            $wisdomModifier;

        if (
            is_array(
                $perceptionSkill
            )
        ) {
            $perceptionValue +=
                $this->nativeSkillProficiencyBonus(
                    $perceptionSkill,
                    $proficiencyBonus
                );

            $perceptionValue +=
                (int) (
                    $perceptionSkill['bonus']
                    ?? 0
                );
        }

        $passivePerception =
            10
            +
            $perceptionValue
            +
            (
                (int) (
                    $combat['passivePerceptionBonus']
                    ?? $combat['passive_perception_bonus']
                    ?? 0
                )
            );

        $details =
            collect();

        if (!empty($resolvedSkills)) {
            $details->push([
                'label' =>
                    'Perícias',

                'value' =>
                    collect(
                        $resolvedSkills
                    )
                        ->map(
                            fn (array $skill): string =>
                                $skill['name']
                                . ' '
                                . $this->signed(
                                    $skill['value']
                                )
                        )
                        ->implode(', '),
            ]);
        }

        $resistances =
            $this->nativeDictionaryDetail(
                $combat['resistances']
                ?? [],
                DamageTypes::class,
                $combat['resistanceCustom']
                ?? ''
            );

        $immunities =
            $this->nativeDictionaryDetail(
                $combat['immunities']
                ?? [],
                DamageTypes::class,
                $combat['immunityCustom']
                ?? ''
            );

        $conditionImmunities =
            $this->nativeDictionaryDetail(
                $combat['conditionImmunities']
                ?? [],
                Conditions::class,
                $combat['conditionImmunityCustom']
                ?? ''
            );

        $vulnerabilities =
            $this->nativeDictionaryDetail(
                $combat['vulnerabilities']
                ?? [],
                DamageTypes::class,
                $combat['vulnerabilityCustom']
                ?? ''
            );

        foreach (
            [
                'Resistências a Dano' =>
                    $resistances,

                'Imunidades a Dano' =>
                    $immunities,

                'Imunidades a Condição' =>
                    $conditionImmunities,

                'Vulnerabilidades a Dano' =>
                    $vulnerabilities,
            ]
            as $label => $value
        ) {
            if (
                !$this->isEmptyDisplayValue(
                    $value
                )
            ) {
                $details->push([
                    'label' =>
                        $label,

                    'value' =>
                        $value,
                ]);
            }
        }

        $senses =
            $this->nativeSenses(
                $combat,
                $passivePerception
            );

        if (
            !$this->isEmptyDisplayValue(
                $senses
            )
        ) {
            $details->push([
                'label' =>
                    'Sentidos',

                'value' =>
                    $senses,
            ]);
        }

        $languages =
            collect([
                $this->dictionaryList(
                    $header['languages']
                    ?? [],
                    Languages::class
                ),

                $this->dictionaryList(
                    $combat['languages']
                    ?? [],
                    Languages::class
                ),

                $this->cleanText(
                    $header['languageCustom']
                    ?? ''
                ),
            ])
                ->reject(
                    fn ($value): bool =>
                        $this->isEmptyDisplayValue(
                            $value
                        )
                )
                ->unique(
                    fn ($value): string =>
                        mb_strtolower(
                            (string) $value
                        )
                )
                ->implode(', ');

        if (
            !$this->isEmptyDisplayValue(
                $languages
            )
        ) {
            $details->push([
                'label' =>
                    'Idiomas',

                'value' =>
                    $languages,
            ]);
        }

        return [
            'source' =>
                'native',

            'name' =>
                $name,

            'subtitle' =>
                $this->nativeSubtitle(
                    $header
                ),

            'image_path' =>
                $this->nativeImagePath(
                    $npc,
                    $json
                ),

            'armor_class' =>
                $armorClass,

            'max_hp' =>
                $maxHp,

            'initiative' =>
                $initiative,

            'speed' =>
                $this->nativeSpeed(
                    $speed
                ),

            'jump' =>
                $this->nativeJump(
                    $speed,
                    $abilities
                ),

            'challenge_rating' =>
                $challengeRating,

            'proficiency_bonus' =>
                $proficiencyBonus,

            'abilities' =>
                $resolvedAbilities,

            'details' =>
                $details
                    ->values()
                    ->all(),

            'sections' =>
                $this->nativeSections(
                    $json,
                    $abilities,
                    $proficiencyBonus,
                    $name
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATO EXTERNO — NÃO UTILIZADO POR TRANSFORMAÇÕES
    |--------------------------------------------------------------------------
    |
    | Mantido temporariamente apenas como código legado interno do resolver.
    | resolve() nunca encaminha uma transformação para este caminho.
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | NATIVE — SEÇÕES
    |--------------------------------------------------------------------------
    */

    private function nativeSections(
        array $json,
        array $abilities,
        int $proficiencyBonus,
        string $monsterName
    ): array {
        $sections =
            collect();

        $features =
            $this->normalizeEntries(
                $json['features']
                ?? []
            );

        if ($features->isNotEmpty()) {
            $sections->push([
                'title' =>
                    'Habilidades',

                'entries' =>
                    $features->all(),
            ]);
        }

        $attacks =
            collect(
                is_array(
                    $json['attacks']
                    ?? null
                )
                    ? $json['attacks']
                    : []
            );

        $actions =
            collect(
                is_array(
                    $json['actions']
                    ?? null
                )
                    ? $json['actions']
                    : []
            );

        $actionEntries =
            collect();

        $attackMap =
            $attacks
                ->filter(
                    fn ($attack): bool =>
                        is_array($attack)
                        && trim(
                            (string) (
                                $attack['id']
                                ?? ''
                            )
                        )
                        !== ''
                )
                ->keyBy(
                    fn (array $attack): string =>
                        (string) $attack['id']
                );

        $actionMap =
            $actions
                ->filter(
                    fn ($action): bool =>
                        is_array($action)
                        && trim(
                            (string) (
                                $action['id']
                                ?? ''
                            )
                        )
                        !== ''
                )
                ->keyBy(
                    fn (array $action): string =>
                        (string) $action['id']
                );

        foreach (
            is_array(
                $json['multiAttacks']
                ?? null
            )
                ? $json['multiAttacks']
                : []
            as $multiAttack
        ) {
            if (!is_array($multiAttack)) {
                continue;
            }

            $entry =
                $this->nativeMultiAttack(
                    $multiAttack,
                    $attackMap,
                    $actionMap,
                    $monsterName
                );

            if ($entry !== null) {
                $actionEntries->push(
                    $entry
                );
            }
        }

        foreach ($attacks as $attack) {
            if (!is_array($attack)) {
                continue;
            }

            $entry =
                $this->nativeAttack(
                    $attack,
                    $abilities,
                    $proficiencyBonus
                );

            if ($entry !== null) {
                $actionEntries->push(
                    $entry
                );
            }
        }

        $actionEntries =
            $actionEntries
                ->concat(
                    $this->normalizeEntries(
                        $json['actions']
                        ?? []
                    )
                )
                ->values();

        if ($actionEntries->isNotEmpty()) {
            $sections->push([
                'title' =>
                    'Ações',

                'entries' =>
                    $actionEntries->all(),
            ]);
        }

        foreach (
            [
                'bonusActions' =>
                    'Ações Bônus',

                'reactions' =>
                    'Reações',

                'legendaryActions' =>
                    'Ações Lendárias',

                'lairActions' =>
                    'Ações de Covil',

                'mythicActions' =>
                    'Ações Míticas',
            ]
            as $sourceKey => $title
        ) {
            $entries =
                $this->normalizeEntries(
                    $json[$sourceKey]
                    ?? []
                );

            if ($entries->isNotEmpty()) {
                $sections->push([
                    'title' =>
                        $title,

                    'entries' =>
                        $entries->all(),
                ]);
            }
        }

        return $sections
            ->values()
            ->all();
    }


    private function nativeMultiAttack(
        array $multiAttack,
        Collection $attackMap,
        Collection $actionMap,
        string $monsterName
    ): ?array {
        $title =
            $this->cleanText(
                $multiAttack['title']
                ?? 'Multiataque'
            );

        $mode =
            $multiAttack['mode']
            ?? 'automatic';

        $tracker =
            $this->normalizeTracker(
                $multiAttack['tracker']
                ?? null
            );

        $entryId =
            $this->entryId(
                $multiAttack,
                'multiattack',
                $title
            );

        if ($mode === 'custom') {
            $html =
                $this->sanitizeHtml(
                    $multiAttack['customText']
                    ?? ''
                );

            if (
                $title === ''
                && $this->htmlIsEmpty(
                    $html
                )
            ) {
                return null;
            }

            return [
                'id' =>
                    $entryId,

                'title' =>
                    $title,

                'html' =>
                    $html,

                'variant' =>
                    'multiattack',

                'tracker' =>
                    $tracker,
            ];
        }

        $numberWords = [
            1 => 'um',
            2 => 'dois',
            3 => 'três',
            4 => 'quatro',
            5 => 'cinco',
            6 => 'seis',
            7 => 'sete',
            8 => 'oito',
            9 => 'nove',
            10 => 'dez',
        ];

        $parts =
            [];

        foreach (
            is_array(
                $multiAttack['entries']
                ?? null
            )
                ? $multiAttack['entries']
                : []
            as $entry
        ) {
            if (!is_array($entry)) {
                continue;
            }

            $source =
                $entry['source']
                ?? 'attack';

            $sourceId =
                (string) (
                    $entry['sourceId']
                    ?? ''
                );

            $quantity =
                max(
                    1,
                    (int) (
                        $entry['quantity']
                        ?? 1
                    )
                );

            $sourceEntry =
                $source === 'action'
                    ? $actionMap->get(
                        $sourceId
                    )
                    : $attackMap->get(
                        $sourceId
                    );

            if (!is_array($sourceEntry)) {
                continue;
            }

            $entryName =
                $this->cleanText(
                    $sourceEntry['title']
                    ?? $sourceEntry['name']
                    ?? (
                        $source === 'action'
                            ? 'Ação'
                            : 'Ataque'
                    )
                );

            if ($entryName === '') {
                continue;
            }

            $quantityText =
                $numberWords[$quantity]
                ?? (string) $quantity;

            if ($source === 'action') {
                $parts[] =
                    $quantity <= 1
                        ? 'uma vez a ação '
                            . $entryName
                        : $quantityText
                            . ' ('
                            . $quantity
                            . ') vezes a ação '
                            . $entryName;

                continue;
            }

            $parts[] =
                $quantity <= 1
                    ? 'um ataque usando '
                        . $entryName
                    : $quantityText
                        . ' ('
                        . $quantity
                        . ') ataques usando '
                        . $entryName;
        }

        $body =
            '';

        if (!empty($parts)) {
            if (count($parts) === 1) {
                $partsText =
                    $parts[0];

            } else {
                $last =
                    array_pop(
                        $parts
                    );

                $partsText =
                    implode(
                        ', ',
                        $parts
                    )
                    . ' e '
                    . $last;
            }

            $body =
                $monsterName
                . ' realiza '
                . $partsText
                . '.';
        }

        if (
            $title === ''
            && $body === ''
        ) {
            return null;
        }

        return [
            'id' =>
                $entryId,

            'title' =>
                $title,

            'html' =>
                $this->sanitizeHtml(
                    $body
                ),

            'variant' =>
                'multiattack',

            'tracker' =>
                $tracker,
        ];
    }


    private function nativeAttack(
        array $attack,
        array $abilities,
        int $proficiencyBonus
    ): ?array {
        $title =
            $this->cleanText(
                $attack['title']
                ?? $attack['name']
                ?? 'Ataque'
            );

        $tracker =
            $this->normalizeTracker(
                $attack['tracker']
                ?? null
            );

        $entryId =
            $this->entryId(
                $attack,
                'attack',
                $title
            );

        if (
            (
                $attack['mode']
                ?? null
            )
            ===
            'text'
        ) {
            $html =
                $this->sanitizeHtml(
                    $attack['content']
                    ?? ''
                );

            if (
                $title === ''
                && $this->htmlIsEmpty(
                    $html
                )
            ) {
                return null;
            }

            return [
                'id' =>
                    $entryId,

                'title' =>
                    $title,

                'html' =>
                    $html,

                'variant' =>
                    'attack',

                'tracker' =>
                    $tracker,
            ];
        }

        $builder =
            isset(
                $attack['builder']
            )
            && is_array(
                $attack['builder']
            )
                ? $attack['builder']
                : [];

        if (empty($builder)) {
            $html =
                $this->sanitizeHtml(
                    $attack['content']
                    ?? ''
                );

            if (
                $title === ''
                && $this->htmlIsEmpty(
                    $html
                )
            ) {
                return null;
            }

            return [
                'id' =>
                    $entryId,

                'title' =>
                    $title,

                'html' =>
                    $html,

                'variant' =>
                    'attack',

                'tracker' =>
                    $tracker,
            ];
        }

        $attackType =
            match (
                $builder['attackType']
                ?? 'weapon'
            ) {
                'weapon' =>
                    'Ataque com Arma',

                'spell' =>
                    'Ataque Mágico',

                'feature' =>
                    'Ataque Especial',

                default =>
                    'Ataque',
            };

        $attackMode =
            (
                $builder['range']
                ?? 'melee'
            )
            ===
            'melee'
                ? 'Corpo a Corpo'
                : 'À Distância';

        $hitBonus =
            0;

        $attackAbility =
            trim(
                (string) (
                    $builder['attackAbility']
                    ?? ''
                )
            );

        if (
            $attackAbility !== ''
            && array_key_exists(
                $attackAbility,
                $abilities
            )
        ) {
            $hitBonus +=
                $this->abilityModifier(
                    (int) $abilities[
                        $attackAbility
                    ]
                );
        }

        if (
            filter_var(
                $builder['proficiency']
                ?? false,
                FILTER_VALIDATE_BOOLEAN
            )
        ) {
            $hitBonus +=
                $proficiencyBonus;
        }

        $hitBonus +=
            (int) (
                $builder['extraHitBonus']
                ?? 0
            );

        $reach =
            max(
                0,
                (int) (
                    $builder['reach']
                    ?? 0
                )
            );

        $targets =
            $this->cleanText(
                $builder['targets']
                ?? 'Um alvo'
            );

        $damageText =
            $this->nativeAttackDamage(
                $builder['damages']
                ?? [],
                $abilities
            );

        /*
         * Efeitos do Builder são rich text.
         * Não usamos cleanText(), porque isso apagaria o negrito/itálico
         * escolhido no editor.
         */
        $effects =
            collect(
                is_array(
                    $builder['effects']
                    ?? null
                )
                    ? $builder['effects']
                    : []
            )
                ->map(
                    fn ($effect): string =>
                        $this->sanitizeHtml(
                            is_array($effect)
                                ? (
                                    $effect['content']
                                    ?? $effect['text']
                                    ?? ''
                                )
                                : $effect
                        )
                )
                ->filter(
                    fn ($effect): bool =>
                        !$this->htmlIsEmpty(
                            $effect
                        )
                )
                ->implode(' ');

        $body =
            $attackType
            . ' '
            . $attackMode
            . ': '
            . e(
                $this->signed(
                    $hitBonus
                )
            )
            . ' para atingir, alcance '
            . e(
                (string) (
                    $reach > 0
                        ? $reach
                        : 5
                )
            )
            . ' pés, '
            . e(
                $targets !== ''
                    ? $targets
                    : 'Um alvo'
            )
            . '.';

        if ($damageText !== '') {
            /*
             * "Acerto:" permanece texto normal.
             * Somente os valores mecânicos serão realçados.
             */
            $body .=
                ' Acerto: '
                . $damageText
                . '.';
        }

        if ($effects !== '') {
            $body .=
                ' '
                . $effects;
        }

        return [
            'id' =>
                $entryId,

            'title' =>
                $title,

            'html' =>
                $this->sanitizeHtml(
                    $body
                ),

            'variant' =>
                'attack',

            'tracker' =>
                $tracker,
        ];
    }


    private function nativeAttackDamage(
        mixed $damages,
        array $abilities
    ): string {
        if (!is_array($damages)) {
            return '';
        }

        $parts =
            [];

        foreach ($damages as $damage) {
            if (!is_array($damage)) {
                continue;
            }

            $count =
                max(
                    1,
                    (int) (
                        $damage['count']
                        ?? 1
                    )
                );

            $dieText =
                strtolower(
                    trim(
                        (string) (
                            $damage['die']
                            ?? 'd6'
                        )
                    )
                );

            $dieSides =
                max(
                    2,
                    (int) str_replace(
                        'd',
                        '',
                        $dieText
                    )
                );

            $modifier =
                0;

            $damageAbility =
                trim(
                    (string) (
                        $damage['ability']
                        ?? ''
                    )
                );

            if (
                $damageAbility !== ''
                && $damageAbility !== 'none'
                && array_key_exists(
                    $damageAbility,
                    $abilities
                )
            ) {
                $modifier +=
                    $this->abilityModifier(
                        (int) $abilities[
                            $damageAbility
                        ]
                    );
            }

            $modifier +=
                (int) (
                    $damage['extra']
                    ?? 0
                );

            $average =
                ($dieSides / 2)
                + .5;

            $total =
                (int) floor(
                    (
                        $count
                        * $average
                    )
                    + $modifier
                );

            $modifierText =
                $modifier === 0
                    ? ''
                    : (
                        $modifier > 0
                            ? ' + '
                                . $modifier
                            : ' - '
                                . abs(
                                    $modifier
                                )
                    );

            $typeKey =
                trim(
                    (string) (
                        $damage['type']
                        ?? ''
                    )
                );

            $type =
                $this->dictionaryLabel(
                    DamageTypes::class,
                    $typeKey,
                    $typeKey
                );

            $part =
                $total
                . ' ('
                . $count
                . 'd'
                . $dieSides
                . $modifierText
                . ')';

            if (
                !$this->isEmptyDisplayValue(
                    $type
                )
            ) {
                $part .=
                    ' de '
                    . $type;
            }

            $parts[] =
                e(
                    $part
                );
        }

        return implode(
            ', mais ',
            $parts
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NORMAL — SEÇÕES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | HELPERS — NATIVE
    |--------------------------------------------------------------------------
    */

    private function nativeMaxHp(
        array $combat,
        array $abilities
    ): int {
        $mode =
            $combat['hp_mode']
            ?? 'average';

        if (
            $mode === 'custom'
            && isset(
                $combat['custom_hp']
            )
        ) {
            return max(
                1,
                (int) $combat['custom_hp']
            );
        }

        $count =
            max(
                1,
                (int) (
                    $combat['hit_dice_count']
                    ?? 1
                )
            );

        $die =
            max(
                2,
                (int) str_replace(
                    'd',
                    '',
                    strtolower(
                        (string) (
                            $combat['hit_die']
                            ?? 'd8'
                        )
                    )
                )
            );

        $constitution =
            (int) (
                $abilities['con']
                ?? 10
            );

        $constitutionModifier =
            $this->abilityModifier(
                $constitution
            );

        $extra =
            (int) (
                $combat['hp_mod_extra']
                ?? 0
            );

        return max(
            1,
            (int) floor(
                (
                    $count
                    * (
                        ($die / 2)
                        + .5
                    )
                )
                +
                (
                    $count
                    * $constitutionModifier
                )
                +
                $extra
            )
        );
    }


    private function nativeSkillProficiencyBonus(
        array $skill,
        int $proficiencyBonus
    ): int {
        if (
            !empty(
                $skill['expertise']
            )
        ) {
            return
                $proficiencyBonus
                * 2;
        }

        if (
            !empty(
                $skill['proficient']
            )
        ) {
            return
                $proficiencyBonus;
        }

        return 0;
    }


    private function nativeSubtitle(
        array $header
    ): string {
        $size =
            $this->dictionaryLabel(
                NpcSizes::class,
                $header['size']
                ?? '',
                $header['size']
                ?? ''
            );

        $types =
            $this->labelList(
                $header['types']
                ?? [],
                NpcTypes::class
            );

        $alignments =
            $this->labelList(
                $header['alignments']
                ?? [],
                Alignments::class
            );

        $subtitle =
            collect([
                $size,
                $types,
            ])
                ->reject(
                    fn ($value): bool =>
                        $this->isEmptyDisplayValue(
                            $value
                        )
                )
                ->implode(' ');

        if (
            !$this->isEmptyDisplayValue(
                $alignments
            )
        ) {
            $subtitle .=
                (
                    $subtitle !== ''
                        ? ', '
                        : ''
                )
                . $alignments;
        }

        return trim(
            $subtitle
        );
    }


    private function nativeImagePath(
        Npc $npc,
        array $json
    ): ?string {
        $candidate =
            $json['image_path']
            ?? $json['image']
            ?? $npc->image_path
            ?? null;

        if (
            !is_string(
                $candidate
            )
        ) {
            return
                $npc->image_path
                ?? null;
        }

        $candidate =
            trim(
                $candidate
            );

        return
            $candidate !== ''
                ? $candidate
                : null;
    }


    private function nativeSpeed(
        array $speed
    ): string {
        $parts =
            [];

        $definitions = [
            'walk' =>
                null,

            'climb' =>
                'escalada',

            'swim' =>
                'natação',

            'burrow' =>
                'escavação',

            'fly' =>
                'voo',
        ];

        foreach (
            $definitions
            as $key => $label
        ) {
            $distance =
                (int) (
                    $speed[$key]
                    ?? 0
                );

            if ($distance <= 0) {
                continue;
            }

            $value =
                (
                    $label !== null
                        ? $label
                            . ' '
                        : ''
                )
                . $distance
                . ' ft.';

            if (
                $key === 'fly'
                && !empty(
                    $speed['hover']
                )
            ) {
                $value .=
                    ' (pairar)';
            }

            $parts[] =
                $value;
        }

        return
            !empty($parts)
                ? implode(
                    ', ',
                    $parts
                )
                : '0 ft.';
    }


    /*
    |--------------------------------------------------------------------------
    | SALTO
    |--------------------------------------------------------------------------
    |
    | O Builder possui:
    | - hasJumps
    | - jumpHorizontalBonus
    | - jumpVerticalBonus
    |
    | Alguns JSONs antigos mantêm os bônus de salto preenchidos mesmo quando
    | hasJumps ficou false. Para não perder informação já criada no Spellbound,
    | consideramos salto configurado quando:
    |
    | - hasJumps === true; OU
    | - algum dos bônus de salto é diferente de zero.
    |--------------------------------------------------------------------------
    */
    private function nativeJump(
        array $speed,
        array $abilities
    ): ?array {
        $horizontalBonus =
            (int) (
                $speed['jumpHorizontalBonus']
                ?? 0
            );

        $verticalBonus =
            (int) (
                $speed['jumpVerticalBonus']
                ?? 0
            );

        $hasJumpData =
            !empty(
                $speed['hasJumps']
            )
            || $horizontalBonus !== 0
            || $verticalBonus !== 0;

        if (!$hasJumpData) {
            return null;
        }

        $strength =
            (int) (
                $abilities['str']
                ?? 10
            );

        $horizontal =
            max(
                0,
                $strength
                + $horizontalBonus
            );

        $vertical =
            max(
                0,
                3
                + $this->abilityModifier(
                    $strength
                )
                + $verticalBonus
            );

        return [
            'horizontal' =>
                $horizontal,

            'vertical' =>
                $vertical,

            'text' =>
                'Horizontal '
                . $horizontal
                . ' ft. · Vertical '
                . $vertical
                . ' ft.',
        ];
    }

    private function nativeSenses(
        array $combat,
        int $passivePerception
    ): string {
        $parts =
            [];

        $senses =
            isset(
                $combat['senses']
            )
            && is_array(
                $combat['senses']
            )
                ? $combat['senses']
                : [];

        foreach (
            $senses
            as $key => $distance
        ) {
            $distance =
                (int) $distance;

            if ($distance <= 0) {
                continue;
            }

            $label =
                $this->dictionaryLabel(
                    Senses::class,
                    $key,
                    match ($key) {
                        'blindsight' =>
                            'Visão às cegas',

                        'darkvision' =>
                            'Visão no escuro',

                        'truesight' =>
                            'Visão verdadeira',

                        default =>
                            ucfirst(
                                (string) $key
                            ),
                    }
                );

            $parts[] =
                $label
                . ' '
                . $distance
                . ' ft.';
        }

        foreach (
            is_array(
                $combat['customSenses']
                ?? null
            )
                ? $combat['customSenses']
                : []
            as $custom
        ) {
            if (is_string($custom)) {
                $custom =
                    $this->cleanText(
                        $custom
                    );

                if (
                    !$this->isEmptyDisplayValue(
                        $custom
                    )
                ) {
                    $parts[] =
                        $custom;
                }

                continue;
            }

            if (!is_array($custom)) {
                continue;
            }

            $name =
                $this->cleanText(
                    $custom['name']
                    ?? $custom['label']
                    ?? ''
                );

            $distance =
                (int) (
                    $custom['distance']
                    ?? $custom['value']
                    ?? 0
                );

            if ($name === '') {
                continue;
            }

            $parts[] =
                $name
                . (
                    $distance > 0
                        ? ' '
                            . $distance
                            . ' ft.'
                        : ''
                );
        }

        $parts[] =
            'Percepção passiva '
            . $passivePerception;

        return collect(
            $parts
        )
            ->reject(
                fn ($value): bool =>
                    $this->isEmptyDisplayValue(
                        $value
                    )
            )
            ->unique(
                fn ($value): string =>
                    mb_strtolower(
                        (string) $value
                    )
            )
            ->implode(', ');
    }


    private function nativeDictionaryDetail(
        mixed $values,
        string $dictionary,
        mixed $custom
    ): string {
        $base =
            $this->dictionaryList(
                $values,
                $dictionary
            );

        $custom =
            $this->cleanText(
                $custom
            );

        if (
            $this->isEmptyDisplayValue(
                $custom
            )
        ) {
            return $base;
        }

        return trim(
            $base
            . (
                $base !== ''
                    ? ', '
                    : ''
            )
            . $custom
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS — NORMALIZAÇÃO DE ENTRADAS
    |--------------------------------------------------------------------------
    */

    private function normalizeEntries(
        mixed $entries
    ): Collection {
        return collect(
            $entries
            ?? []
        )
            ->map(
                function ($entry): ?array {
                    $title =
                        $this->cleanText(
                            $this->value(
                                $entry,
                                [
                                    'title',
                                    'name',
                                    'label',
                                    'header',
                                ],
                                ''
                            )
                        );

                    $body =
                        $this->value(
                            $entry,
                            [
                                'content',
                                'description',
                                'text',
                                'body',
                                'details',
                                'entry',
                                'html',
                            ],
                            ''
                        );

                    if (
                        is_array($body)
                        || is_object($body)
                    ) {
                        $body =
                            '';
                    }

                    $html =
                        $this->sanitizeHtml(
                            $body
                        );

                    if (
                        $title === ''
                        && $this->htmlIsEmpty(
                            $html
                        )
                    ) {
                        return null;
                    }

                    $entryArray =
                        is_array(
                            $entry
                        )
                            ? $entry
                            : [];

                    return [
                        'id' =>
                            $this->entryId(
                                $entryArray,
                                'entry',
                                $title
                            ),

                        'title' =>
                            $title,

                        'html' =>
                            $html,

                        'variant' =>
                            'entry',

                        'tracker' =>
                            $this->normalizeTracker(
                                $this->value(
                                    $entry,
                                    ['tracker'],
                                    null
                                )
                            ),
                    ];
                }
            )
            ->filter()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS — GENÉRICOS
    |--------------------------------------------------------------------------
    */

    private function jsonArray(
        mixed $value
    ): array {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded =
                json_decode(
                    $value,
                    true
                );

            return
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (is_object($value)) {
            return
                json_decode(
                    json_encode(
                        $value
                    ),
                    true
                )
                ?: [];
        }

        return [];
    }


    private function arrayValue(
        array $source,
        string $key
    ): array {
        $value =
            $source[$key]
            ?? [];

        return
            is_array($value)
                ? $value
                : [];
    }


    private function value(
        mixed $source,
        array|string $keys,
        mixed $default = null
    ): mixed {
        $keys =
            is_array($keys)
                ? $keys
                : [$keys];

        foreach ($keys as $key) {
            if (is_array($source)) {
                if (
                    array_key_exists(
                        $key,
                        $source
                    )
                    && $source[$key] !== null
                ) {
                    return $source[$key];
                }

                continue;
            }

            if (is_object($source)) {
                if (
                    isset(
                        $source->{$key}
                    )
                ) {
                    return $source->{$key};
                }

                if (
                    property_exists(
                        $source,
                        $key
                    )
                ) {
                    return $source->{$key};
                }
            }
        }

        return $default;
    }


    private function abilityModifier(
        int $score
    ): int {
        return
            (int) floor(
                (
                    $score
                    - 10
                )
                / 2
            );
    }


    private function signed(
        int $value
    ): string {
        return
            (
                $value >= 0
                    ? '+'
                    : ''
            )
            . $value;
    }


    private function normalizeList(
        mixed $value
    ): Collection {
        if ($value === null) {
            return collect();
        }

        if (
            $value instanceof
                Collection
        ) {
            $value =
                $value->all();
        }

        if (is_array($value)) {
            return collect(
                $value
            )
                ->map(
                    function ($entry): string {
                        if (
                            is_scalar($entry)
                            || $entry === null
                        ) {
                            return
                                $this->cleanText(
                                    $entry
                                );
                        }

                        if (
                            is_array($entry)
                            || is_object($entry)
                        ) {
                            return
                                $this->cleanText(
                                    $this->value(
                                        $entry,
                                        [
                                            'label',
                                            'name',
                                            'value',
                                            'title',
                                            'type',
                                        ],
                                        ''
                                    )
                                );
                        }

                        return '';
                    }
                )
                ->reject(
                    fn ($entry): bool =>
                        $this->isEmptyDisplayValue(
                            $entry
                        )
                )
                ->values();
        }

        if (is_object($value)) {
            if (
                method_exists(
                    $value,
                    '__toString'
                )
            ) {
                $text =
                    $this->cleanText(
                        (string) $value
                    );

                return
                    $this->isEmptyDisplayValue(
                        $text
                    )
                        ? collect()
                        : collect([
                            $text,
                        ]);
            }

            return $this->normalizeList(
                get_object_vars(
                    $value
                )
            );
        }

        $text =
            $this->cleanText(
                $value
            );

        if (
            $this->isEmptyDisplayValue(
                $text
            )
        ) {
            return collect();
        }

        /*
         * Algumas importações persistem listas como string separada por vírgula.
         */
        if (
            str_contains(
                $text,
                ','
            )
        ) {
            return collect(
                explode(
                    ',',
                    $text
                )
            )
                ->map(
                    fn ($entry): string =>
                        trim(
                            $entry
                        )
                )
                ->reject(
                    fn ($entry): bool =>
                        $this->isEmptyDisplayValue(
                            $entry
                        )
                )
                ->values();
        }

        return collect([
            $text,
        ]);
    }


    private function labelList(
        mixed $value,
        string $dictionary
    ): string {
        return $this->normalizeList(
            $value
        )
            ->map(
                fn ($entry): string =>
                    $this->dictionaryLabel(
                        $dictionary,
                        $entry,
                        $entry
                    )
            )
            ->reject(
                fn ($entry): bool =>
                    $this->isEmptyDisplayValue(
                        $entry
                    )
            )
            ->implode(', ');
    }


    private function dictionaryList(
        mixed $value,
        string $dictionary
    ): string {
        return $this->labelList(
            $value,
            $dictionary
        );
    }


    private function dictionaryLabel(
        string $dictionary,
        mixed $value,
        mixed $fallback = ''
    ): string {
        $value =
            $this->cleanText(
                $value
            );

        if (
            $this->isEmptyDisplayValue(
                $value
            )
        ) {
            return
                $this->cleanText(
                    $fallback
                );
        }

        try {
            return
                $dictionary::label(
                    $value
                );
        } catch (\Throwable $exception) {
            return
                $this->cleanText(
                    $fallback
                )
                ?: $value;
        }
    }


    private function proficiencyFromCr(
        mixed $challengeRating
    ): int {
        $value =
            trim(
                (string) $challengeRating
            );

        if (
            $value === ''
        ) {
            return 2;
        }

        if (
            str_contains(
                $value,
                '/'
            )
        ) {
            [
                $numerator,
                $denominator,
            ] =
                array_pad(
                    explode(
                        '/',
                        $value,
                        2
                    ),
                    2,
                    '1'
                );

            $cr =
                (float) $numerator
                /
                max(
                    1.0,
                    (float) $denominator
                );

        } else {
            $cr =
                is_numeric(
                    $value
                )
                    ? (float) $value
                    : 0.0;
        }

        return match (true) {
            $cr >= 29 =>
                9,

            $cr >= 25 =>
                8,

            $cr >= 21 =>
                7,

            $cr >= 17 =>
                6,

            $cr >= 13 =>
                5,

            $cr >= 9 =>
                4,

            $cr >= 5 =>
                3,

            default =>
                2,
        };
    }


    private function cleanText(
        mixed $value
    ): string {
        if (
            $value === null
            || is_array($value)
            || is_object($value)
        ) {
            return '';
        }

        $text =
            html_entity_decode(
                (string) $value,
                ENT_QUOTES
                | ENT_HTML5,
                'UTF-8'
            );

        $text =
            strip_tags(
                $text
            );

        $text =
            preg_replace(
                '/\x{00A0}/u',
                ' ',
                $text
            );

        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                $text
            )
        );
    }


    private function sanitizeHtml(
        mixed $value
    ): string {
        if (
            $value === null
            || is_array($value)
            || is_object($value)
        ) {
            return '';
        }

        $html =
            (string) $value;

        $html =
            str_replace(
                [
                    "\r\n",
                    "\r",
                ],
                "\n",
                $html
            );

        $hasStructuralHtml =
            preg_match(
                '/<\s*\/?\s*(?:p|br|ul|ol|li|blockquote|h[1-6]|pre|hr)\b/iu',
                $html
            )
            === 1;

        $html =
            html_entity_decode(
                $html,
                ENT_QUOTES
                | ENT_HTML5,
                'UTF-8'
            );

        $html =
            strip_tags(
                $html,
                '<strong><b><em><i><br><p><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><hr><pre><code>'
            );

        $html =
            preg_replace_callback(
                '/<\s*(\/?)\s*(strong|b|em|i|br|p|ul|ol|li|blockquote|h1|h2|h3|h4|h5|h6|hr|pre|code)\b[^>]*>/iu',
                function (array $matches): string {
                    $closing =
                        $matches[1]
                        ?? '';

                    $tag =
                        mb_strtolower(
                            $matches[2]
                            ?? ''
                        );

                    if (
                        in_array(
                            $tag,
                            [
                                'br',
                                'hr',
                            ],
                            true
                        )
                    ) {
                        return
                            '<'
                            . $tag
                            . '>';
                    }

                    return
                        '<'
                        . $closing
                        . $tag
                        . '>';
                },
                $html
            );

        if (!$hasStructuralHtml) {
            $html =
                str_replace(
                    "\n",
                    '<br>',
                    $html
                );
        }

        /*
        |--------------------------------------------------------------------------
        | REALCE MECÂNICO
        |--------------------------------------------------------------------------
        |
        | Automaticamente em negrito:
        |
        | +18
        | -3
        | (+2)
        | (-1)
        | 9 (2d8)
        | 34 (3d12 + 15)
        |
        | Não alteramos texto que já está dentro de <strong>/<b>, nem código.
        |--------------------------------------------------------------------------
        */
        $html =
            $this->highlightMechanicalValues(
                trim(
                    $html
                )
            );

        return trim(
            $html
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ID ESTÁVEL DA ENTRADA
    |--------------------------------------------------------------------------
    */
    private function entryId(
        array $entry,
        string $prefix,
        string $title
    ): string {
        $id =
            trim(
                (string) (
                    $entry['id']
                    ?? ''
                )
            );

        if ($id !== '') {
            return $id;
        }

        return
            $prefix
            . '-'
            . substr(
                sha1(
                    $prefix
                    . '|'
                    . $title
                    . '|'
                    . json_encode(
                        $entry,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                ),
                0,
                20
            );
    }


    /*
    |--------------------------------------------------------------------------
    | TRACKER DO NPC BUILDER
    |--------------------------------------------------------------------------
    |
    | O JSON original usa:
    |
    | enabled
    | uses
    | reset
    | min / max
    | customReset
    |
    | A transformação não persiste isso no banco. Estes dados são apenas a
    | configuração inicial que a Blade colocará no localStorage do navegador.
    |--------------------------------------------------------------------------
    */
    private function normalizeTracker(
        mixed $tracker
    ): ?array {
        if (
            !is_array(
                $tracker
            )
            || !(
                (bool) (
                    $tracker['enabled']
                    ?? false
                )
            )
        ) {
            return null;
        }

        $uses =
            max(
                0,
                (int) (
                    $tracker['uses']
                    ?? 0
                )
            );

        $reset =
            trim(
                (string) (
                    $tracker['reset']
                    ?? ''
                )
            );

        $min =
            max(
                1,
                (int) (
                    $tracker['min']
                    ?? 4
                )
            );

        $max =
            max(
                $min,
                (int) (
                    $tracker['max']
                    ?? 6
                )
            );

        $customReset =
            $this->cleanText(
                $tracker['customReset']
                ?? ''
            );

        $label =
            match ($reset) {
                'day' =>
                    'Dia',

                'short_rest' =>
                    'Desc. Curto',

                'long_rest' =>
                    'Desc. Longo',

                'turn' =>
                    'Turno',

                'recharge' =>
                    'Recarga '
                    . $min
                    . '–'
                    . $max,

                'custom' =>
                    $customReset !== ''
                        ? $customReset
                        : 'Usos',

                default =>
                    'Usos',
            };

        /*
         * Recarga funciona como disponível/usado.
         * Os demais trackers usam o número de usos salvo no Builder.
         */
        $capacity =
            $reset === 'recharge'
                ? 1
                : max(
                    1,
                    $uses
                );

        $initial =
            $reset === 'recharge'
                ? 1
                : min(
                    $capacity,
                    $uses
                );

        return [
            'enabled' =>
                true,

            'title' =>
                $this->cleanText(
                    $tracker['title']
                    ?? ''
                ),

            'uses' =>
                $uses,

            'initial' =>
                $initial,

            'capacity' =>
                $capacity,

            'reset' =>
                $reset,

            'min' =>
                $min,

            'max' =>
                $max,

            'custom_reset' =>
                $customReset,

            'label' =>
                $label,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NEGRITO AUTOMÁTICO DE VALORES MECÂNICOS
    |--------------------------------------------------------------------------
    */
    private function highlightMechanicalValues(
        string $html
    ): string {
        if ($html === '') {
            return '';
        }

        $parts =
            preg_split(
                '/(<[^>]+>)/u',
                $html,
                -1,
                PREG_SPLIT_DELIM_CAPTURE
            );

        if (!is_array($parts)) {
            return $html;
        }

        $strongDepth =
            0;

        $codeDepth =
            0;

        foreach (
            $parts
            as $index => $part
        ) {
            if (
                $part === ''
            ) {
                continue;
            }

            if (
                str_starts_with(
                    $part,
                    '<'
                )
            ) {
                if (
                    preg_match(
                        '/^<\s*(strong|b)\b/iu',
                        $part
                    )
                ) {
                    $strongDepth++;
                } elseif (
                    preg_match(
                        '/^<\s*\/\s*(strong|b)\s*>/iu',
                        $part
                    )
                ) {
                    $strongDepth =
                        max(
                            0,
                            $strongDepth - 1
                        );
                }

                if (
                    preg_match(
                        '/^<\s*(code|pre)\b/iu',
                        $part
                    )
                ) {
                    $codeDepth++;
                } elseif (
                    preg_match(
                        '/^<\s*\/\s*(code|pre)\s*>/iu',
                        $part
                    )
                ) {
                    $codeDepth =
                        max(
                            0,
                            $codeDepth - 1
                        );
                }

                continue;
            }

            if (
                $strongDepth > 0
                || $codeDepth > 0
            ) {
                continue;
            }

            /*
             * Uma única expressão trata:
             *
             * 34 (3d12 + 15)
             * 9 (2d8)
             * (+2)
             * (-1)
             * +18
             * -5
             *
             * O padrão de dano vem primeiro para não criar <strong> aninhado
             * dentro da fórmula.
             */
            $parts[$index] =
                preg_replace_callback(
                    '/(?<![\pL\pN_])('
                    . '\d+\s*\(\s*\d+d\d+(?:\s*[+\-]\s*\d+)?\s*\)'
                    . '|\([+\-]\d+(?:[.,]\d+)?\)'
                    . '|[+\-]\d+(?:[.,]\d+)?'
                    . ')/iu',
                    fn (array $matches): string =>
                        '<strong>'
                        . $matches[1]
                        . '</strong>',
                    $part
                )
                ?? $part;
        }

        return implode(
            '',
            $parts
        );
    }


    private function htmlIsEmpty(
        string $html
    ): bool {
        return $this->isEmptyDisplayValue(
            $this->cleanText(
                $html
            )
        );
    }


    private function isEmptyDisplayValue(
        mixed $value
    ): bool {
        $text =
            mb_strtolower(
                $this->cleanText(
                    $value
                )
            );

        return in_array(
            $text,
            [
                '',
                '-',
                '—',
                '–',
                'none',
                'nenhum',
                'nenhuma',
                'n/a',
                'na',
                'null',
            ],
            true
        );
    }


}