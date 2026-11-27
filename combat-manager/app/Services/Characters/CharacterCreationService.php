<?php

namespace App\Services\Characters;

use App\Models\Character;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Spells\SpellcastingProgressionService;
use App\Services\Spells\SpellcastingSheetSyncService;

class CharacterCreationService
{
    public function create(
        array $data,
        ?string $imagePath = null
    ): Character {
        return DB::transaction(function () use ($data, $imagePath) {

            /*
            |--------------------------------------------------------------------------
            | Classes
            |--------------------------------------------------------------------------
            */

            $classes = collect($data['classes'] ?? [])
                ->filter(fn (array $class) => !empty($class['class']))
                ->map(function (array $class) {
                    return [
                        'class' => trim($class['class']),
                        'subclass' => filled($class['subclass'] ?? null)
                            ? trim($class['subclass'])
                            : null,
                        'level' => max(
                            1,
                            min(
                                20,
                                (int) ($class['level'] ?? 1)
                            )
                        ),
                        // Metadados da criação. Não são colunas de character_classes.
                        'hit_die' => in_array((int) ($class['hit_die'] ?? 8), [6, 8, 10, 12], true)
                            ? (int) ($class['hit_die'] ?? 8) : 8,
                        'spellcaster' => (bool) ($class['spellcaster'] ?? false),
                        'spell_ability' => (string) ($class['spell_ability'] ?? 'wisdom'),
                        'caster_type' => (string) ($class['caster_type'] ?? 'full'),
                    ];
                })
                ->values();

            if ($classes->isEmpty()) {
                throw new \InvalidArgumentException(
                    'O personagem precisa possuir pelo menos uma classe.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Nível total
            |--------------------------------------------------------------------------
            */

            $totalLevel = $classes->sum('level');

            if ($totalLevel < 1 || $totalLevel > 20) {
                throw new \InvalidArgumentException(
                    'A soma dos níveis das classes deve estar entre 1 e 20.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            |
            | Só usa o valor enviado quando o jogador ativou a opção
            | "Proficiência personalizada".
            |
            */

            $customProficiency = (bool) (
                $data['custom_prof_enabled'] ?? false
            );

            $proficiencyBonus = $customProficiency
                && array_key_exists('proficiency_bonus', $data)
                ? (int) $data['proficiency_bonus']
                : $this->calculateDefaultProficiency($totalLevel);

            /*
            |--------------------------------------------------------------------------
            | Experiência
            |--------------------------------------------------------------------------
            |
            | A migration não possui uma coluna específica indicando se a
            | campanha utiliza XP. Guardamos essa preferência em overrides.
            |
            */

            $usesExperience = (bool) (
                $data['xp_enabled'] ?? false
            );

            $experiencePoints = $usesExperience
                ? max(
                    0,
                    (int) ($data['experience_points'] ?? 0)
                )
                : 0;

            /*
            |--------------------------------------------------------------------------
            | CHARACTER
            |--------------------------------------------------------------------------
            */

            $character = Character::create([
                'user_id' => $data['user_id'],

                'name' => $data['name'],

                'species' => $data['species'],

                'background' => $data['background'],

                'alignment' => $data['alignment'],

                'level' => $totalLevel,

                'proficiency_bonus' => $proficiencyBonus,

                'image_path' => $imagePath,
                'sheet_settings' => [
                    'creation_ability_breakdown' => [
                        'base' => $data['base_abilities'] ?? $data['abilities'],
                        'background' => $data['background_ability_bonuses'] ?? [],
                        'background_method' => $data['background_ability_method'] ?? 'none',
                        'background_options' => $data['background_ability_options'] ?? [],
                    ],
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | CLASSES
            |--------------------------------------------------------------------------
            |
            | A primeira classe é a principal na criação.
            |
            | Como a tabela atual não possui is_primary/sort_order, a relação
            | primary_class do Model continua usando a classe de maior nível.
            |
            */

            // Armazena apenas as colunas existentes; dados extras são usados abaixo.
            $createdClasses = $character->classes()->createMany(
                $classes->map(fn (array $class) => [
                    'class' => $class['class'],
                    'subclass' => $class['subclass'],
                    'level' => $class['level'],
                ])->all()
            );

            /*
            |--------------------------------------------------------------------------
            | ATRIBUTOS
            |--------------------------------------------------------------------------
            */

            // Mesmos valores exibidos e rolados na etapa Atributos.
            // A Constituição escolhida aqui também é usada no cálculo de PV.
            $abilityKeys = [
                'strength', 'dexterity', 'constitution',
                'intelligence', 'wisdom', 'charisma',
            ];

            $abilityScores = [];
            foreach ($abilityKeys as $abilityKey) {
                $abilityScores[$abilityKey] = max(
                    1,
                    min(20, (int) ($data['abilities'][$abilityKey] ?? 10))
                );
            }

            $character->abilities()->create($abilityScores);

            /*
            |--------------------------------------------------------------------------
            | DADOS DE VIDA E PV MÁXIMOS INICIAIS
            |--------------------------------------------------------------------------
            |
            | O primeiro nível da primeira classe recebe o máximo do dado.
            | Níveis adicionais usam o valor padrão ou as faces escolhidas.
            | Na criação, o personagem começa com os PV atuais iguais aos máximos.
            */
            $method = in_array(($data['hit_points_method'] ?? 'standard'), ['standard', 'rolled', 'manual'], true)
                ? $data['hit_points_method'] : 'standard';
            $submittedRolls = json_decode((string) ($data['hit_points_rolls'] ?? '{}'), true);
            $submittedRolls = is_array($submittedRolls) ? $submittedRolls : [];
            $acceptedRolls = [];
            $hitDiceGroups = [];
            $baseHitPoints = 0;

            foreach ($classes as $classIndex => $class) {
                $sides = (int) $class['hit_die'];
                $count = (int) $class['level'];
                $key = 'd' . $sides;
                if (!isset($hitDiceGroups[$key])) {
                    $hitDiceGroups[$key] = ['die' => $key, 'current' => 0, 'maximum' => 0];
                }
                $hitDiceGroups[$key]['current'] += $count;
                $hitDiceGroups[$key]['maximum'] += $count;

                for ($i = 0; $i < $count; $i++) {
                    if ($classIndex === 0 && $i === 0) {
                        $baseHitPoints += $sides;
                        continue;
                    }
                    $rollKey = $classIndex . '-' . ($classIndex === 0 ? $i - 1 : $i);
                    $standard = intdiv($sides, 2) + 1;
                    $provided = $submittedRolls[$rollKey] ?? null;
                    $rolled = filter_var($provided, FILTER_VALIDATE_INT);
                    if ($method === 'rolled' && (
                        $rolled === false || $rolled === null || $rolled < 1 || $rolled > $sides
                    )) {
                        throw ValidationException::withMessages([
                            'hit_points_rolls' => 'Conclua a rolagem de cada Dado de Vida antes de criar o personagem.',
                        ]);
                    }
                    $value = $method === 'rolled' && $rolled !== false
                        && $rolled !== null && $rolled >= 1 && $rolled <= $sides
                            ? $rolled : $standard;
                    $baseHitPoints += $value;
                    if ($method === 'rolled') {
                        $acceptedRolls[$rollKey] = $value;
                    }
                }
            }

            $constitution = $abilityScores['constitution'];
            $constitutionModifier = (int) floor(($constitution - 10) / 2);
            $calculatedMaxHp = max(1, $baseHitPoints + $constitutionModifier * $totalLevel);
            $maxHp = $method === 'manual'
                ? max(1, min(9999, (int) ($data['hit_points_manual'] ?? $calculatedMaxHp)))
                : $calculatedMaxHp;

            /*
            |--------------------------------------------------------------------------
            | COMBATE
            |--------------------------------------------------------------------------
            */

            $character->combat()->create([
                'experience_points' => $experiencePoints,

                'current_hp' => $maxHp,
                'max_hp' => $maxHp,
                'temporary_hp' => 0,
                'temporary_max_hp' => 0,

                'hit_dice' => array_values($hitDiceGroups),

                'armor_class' => 10,
                'speed' => 30,
                'initiative_bonus' => 0,

                'death_save_successes' => 0,
                'death_save_failures' => 0,

                'concentration_active' => false,
                'concentration_spell_id' => null,

                'exhaustion_level' => 0,

                'conditions' => [],
                'damage_resistances' => [],
                'damage_immunities' => [],
                'damage_vulnerabilities' => [],

                /*
                |------------------------------------------------------------------
                | Preferências / Overrides
                |------------------------------------------------------------------
                |
                | O Index usa isso para decidir se XP deve aparecer.
                |
                */

                'overrides' => [
                    'uses_experience' => $usesExperience,
                    'hp_creation_method' => $method,
                    'hp_creation_rolls' => $acceptedRolls,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | PERFIS DE CONJURAÇÃO VINCULADOS ÀS CLASSES
            |--------------------------------------------------------------------------
            | Usa CharacterSpellcasting e o sincronizador existentes do Grimório.
            | Atributo, progressão e limites continuam editáveis na ficha.
            */
            $createdSpellcaster = false;

            foreach ($classes as $index => $class) {
                if (!$class['spellcaster']) {
                    continue;
                }

                $createdClass = $createdClasses->get($index);
                if (!$createdClass) {
                    continue;
                }

                $detectedProfile = app(SpellcastingSheetSyncService::class)->detectForClass(
                    $class['class'], $class['subclass']
                );

                $character->spellcasting()->create([
                    'character_class_id' => $createdClass->id,
                    'sync_with_character_class' => true,
                    'class' => $class['class'],
                    'ability' => in_array($class['spell_ability'], [
                        'intelligence', 'wisdom', 'charisma',
                        'strength', 'dexterity', 'constitution',
                    ], true) ? $class['spell_ability'] : 'wisdom',
                    'caster_level' => $class['level'],
                    'caster_type' => in_array($class['caster_type'], [
                        'full', 'half', 'half_up', 'third', 'pact', 'custom',
                    ], true) ? $class['caster_type'] : 'full',
                    'slot_mode' => 'automatic',
                    'spell_list_codes' => $detectedProfile['spell_list_codes'] ?? [],
                    'spell_save_dc_bonus' => 0,
                    'spell_attack_bonus_extra' => 0,
                    'is_active' => true,
                    'sort_order' => $index,
                ]);
                $createdSpellcaster = true;
            }

            if ($createdSpellcaster) {
                app(SpellcastingProgressionService::class)->sync($character);
            }

            /*
            |--------------------------------------------------------------------------
            | SAVING THROWS
            |--------------------------------------------------------------------------
            */

            $abilities = [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ];

            $character->savingThrows()->createMany(
                collect($abilities)
                    ->map(fn (string $ability) => [
                        'ability' => $ability,
                        'proficient' => false,
                        'bonus_override' => null,
                    ])
                    ->all()
            );

            /*
            |--------------------------------------------------------------------------
            | TRAÇOS DE ESPÉCIE E TALENTO DE ORIGEM
            |--------------------------------------------------------------------------
            |
            | Adiciona à mesma tabela character_features já utilizada pelo
            | módulo Traços & Talentos. Nenhuma nova tabela é necessária.
            |
            | A transação garante que uma falha nestes registros desfaça a
            | criação do personagem, classes, atributos e combate.
            |
            */

            $initialFeatures = [];

            foreach (($data['species_traits'] ?? []) as $trait) {
                $name = trim((string) ($trait['name'] ?? ''));

                if ($name === '') {
                    continue;
                }

                $initialFeatures[] = $this->initialFeature(
                    'species_trait',
                    $name,
                    $data['species'],
                    $trait['description'] ?? null
                );
            }

            if (filled($data['origin_feat']['name'] ?? null)) {
                $initialFeatures[] = $this->initialFeature(
                    'feat',
                    trim((string) $data['origin_feat']['name']),
                    $data['background'],
                    $data['origin_feat']['description'] ?? null
                );
            }

            /*
            |------------------------------------------------------------------
            | Treinamentos do antecedente — fonte original do módulo de ficha
            |------------------------------------------------------------------
            */

            $allowedTrainingTypes = [
                'armor_training',
                'weapon_training',
                'tool_proficiency',
                'language',
                'vehicle_proficiency',
            ];

            $trainingSeen = [];
            // A mesma formação não é duplicada quando classe e antecedente a concedem.
            $addInitialTraining = function (string $type, string $name, string $source, ?string $rollAbility = null) use (
                &$initialFeatures, &$trainingSeen
            ): void {
                $name = trim($name);
                if ($name === '') return;
                $trainingKey = $type . ':' . mb_strtolower($name);
                if (isset($trainingSeen[$trainingKey])) return;
                $trainingSeen[$trainingKey] = true;

                $feature = $this->initialFeature($type, $name, $source, null);
                $feature['data']['roll_ability'] = $rollAbility ?: $this->initialTrainingAbility($type, $name);
                $initialFeatures[] = $feature;
            };

            $primaryClassName = trim((string) ($classes->first()['class'] ?? 'Classe'));
            foreach ([
                ['prefix' => 'class', 'source' => $primaryClassName],
                ['prefix' => 'background', 'source' => $data['background']],
            ] as $trainingOrigin) {
                $prefix = $trainingOrigin['prefix'];
                $source = $trainingOrigin['source'];
                foreach ($allowedTrainingTypes as $type) {
                    foreach (($data[$prefix . '_trainings'][$type] ?? []) as $name) {
                        $addInitialTraining($type, (string) $name, $source);
                    }
                }
                foreach (($data[$prefix . '_custom_trainings'] ?? []) as $training) {
                    $type = (string) ($training['type'] ?? '');
                    if (!in_array($type, $allowedTrainingTypes, true)) continue;
                    $rollAbility = (string) ($training['roll_ability'] ?? '');
                    $addInitialTraining($type, (string) ($training['name'] ?? ''), $source,
                        in_array($type, ['tool_proficiency','vehicle_proficiency'], true) && $rollAbility !== ''
                            ? $rollAbility : null);
                }
            }

            if ($initialFeatures !== []) {
                $character->features()->createMany($initialFeatures);
            }

            /*
            |------------------------------------------------------------------
            | Perícias do antecedente — a ficha usa character_skills
            |------------------------------------------------------------------
            */

            $initialSkills = array_values(array_unique(array_merge(
                $data['class_skills'] ?? [],
                $data['background_skills'] ?? []
            )));

            if ($initialSkills !== []) {
                $expertiseSkills = $data['skill_expertise'] ?? [];
                $character->skills()->createMany(
                    array_map(fn (string $skill) => [
                        'skill' => $skill,
                        'proficient' => true,
                        'expertise' => in_array($skill, $expertiseSkills, true),
                        'bonus_override' => null,
                        'temporary_bonus' => 0,
                    ], $initialSkills)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RETORNO
            |--------------------------------------------------------------------------
            */

            return $character->load([
                'classes',
                'abilities',
                'combat',
                'savingThrows',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos padronizados de novos traços e talentos
    |--------------------------------------------------------------------------
    */

    private function initialFeature(
        string $type,
        string $name,
        string $source,
        ?string $description
    ): array {
        return [
            'name' => $name,
            'type' => $type,
            'source' => mb_substr(trim($source), 0, 100),
            'level_acquired' => 1,
            'description' => filled($description) ? trim($description) : null,
            'uses_max' => null,
            'uses_current' => null,
            'recovery' => null,
            'data' => [
                'activation' => 'passive',
                'quick_text' => null,
                'counter_mode' => 'spend',
                'recovery_custom' => null,
                'column' => 'left',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos de rolagem usados pelo módulo de Treinamentos & Proficiências
    |--------------------------------------------------------------------------
    */

    private function initialTrainingAbility(
        string $type,
        string $name
    ): ?string {
        if ($type === 'vehicle_proficiency') {
            return 'wisdom';
        }

        if ($type !== 'tool_proficiency') {
            return null;
        }

        return match ($name) {
            'Ferramentas de Ladrão' => 'dexterity',
            'Kit de Herbalismo' => 'wisdom',
            'Kit de Disfarce' => 'charisma',
            'Kit de Falsificação' => 'intelligence',
            'Kit de Veneno' => 'intelligence',
            'Ferramentas de Navegador' => 'wisdom',
            'Suprimentos de Alquimista' => 'intelligence',
            'Ferramentas de Ferreiro' => 'strength',
            'Ferramentas de Carpinteiro' => 'strength',
            'Ferramentas de Artesão' => 'intelligence',
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Proficiência padrão
    |--------------------------------------------------------------------------
    */

    private function calculateDefaultProficiency(
        int $level
    ): int {
        return match (true) {
            $level >= 17 => 6,
            $level >= 13 => 5,
            $level >= 9 => 4,
            $level >= 5 => 3,
            default => 2,
        };
    }
}
