<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Services\Characters\CharacterCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class CharacterController extends Controller
{
    public function index(): View
    {
        $characters = Character::query()
            ->standalone()
            ->where('user_id', Auth::id())
            ->with([
                'classes',
                'combat',
            ])
            ->latest()
            ->get();

        return view('characters.index', [
            'characters' => $characters,
        ]);
    }

    public function store(
        Request $request,
        CharacterCreationService $characterCreationService
    ): RedirectResponse {
        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Identidade
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'species' => [
                'required',
                'string',
                'max:80',
            ],

            'background' => [
                'required',
                'string',
                'max:120',
            ],

            'alignment' => [
                'required',
                'string',
                'max:50',
            ],

            /*
            |--------------------------------------------------------------------------
            | Classes
            |--------------------------------------------------------------------------
            |
            | A primeira classe será criada como principal.
            | As demais serão multiclasses.
            |
            */

            'classes' => [
                'required',
                'array',
                'min:1',
                'max:6',
            ],

            'classes.*.class' => [
                'required',
                'string',
                'max:80',
            ],

            'classes.*.subclass' => [
                'nullable',
                'string',
                'max:80',
            ],

            'classes.*.level' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            /* Dado de vida e perfil de conjuração escolhidos na criação. */
            'classes.*.hit_die' => [
                'nullable',
                'integer',
                Rule::in([6, 8, 10, 12]),
            ],
            'classes.*.spellcaster' => ['sometimes', 'boolean'],
            'classes.*.spell_ability' => [
                'nullable',
                Rule::in(['intelligence', 'wisdom', 'charisma', 'strength', 'dexterity', 'constitution']),
            ],
            'classes.*.caster_type' => [
                'nullable',
                Rule::in(['full', 'half', 'half_up', 'third', 'pact', 'custom']),
            ],

            /* Atributos de criação: pontuação inicial limitada a 20. */
            'ability_generation_method' => ['required', Rule::in(['standard', 'rolled', 'manual'])],
            'abilities' => ['required', 'array', 'size:6'],
            'abilities.strength' => ['required', 'integer', 'min:1', 'max:20'],
            'abilities.dexterity' => ['required', 'integer', 'min:1', 'max:20'],
            'abilities.constitution' => ['required', 'integer', 'min:1', 'max:20'],
            'abilities.intelligence' => ['required', 'integer', 'min:1', 'max:20'],
            'abilities.wisdom' => ['required', 'integer', 'min:1', 'max:20'],
            'abilities.charisma' => ['required', 'integer', 'min:1', 'max:20'],

            /* Decomposição auditável: valor rolado, bônus de origem e aumentos de nível. */
            'base_abilities' => ['required', 'array', 'size:6'],
            'background_ability_method' => ['required', Rule::in(['none','two','three'])],
            'background_ability_options' => ['sometimes', 'array', 'max:3'],
            'background_ability_options.*' => ['nullable', Rule::in(['strength','dexterity','constitution','intelligence','wisdom','charisma'])],
            'background_ability_primary' => ['nullable', Rule::in(['strength','dexterity','constitution','intelligence','wisdom','charisma'])],
            'background_ability_secondary' => ['nullable', Rule::in(['strength','dexterity','constitution','intelligence','wisdom','charisma'])],
            'base_abilities.strength' => ['required', 'integer', 'min:1', 'max:20'],
            'base_abilities.dexterity' => ['required', 'integer', 'min:1', 'max:20'],
            'base_abilities.constitution' => ['required', 'integer', 'min:1', 'max:20'],
            'base_abilities.intelligence' => ['required', 'integer', 'min:1', 'max:20'],
            'base_abilities.wisdom' => ['required', 'integer', 'min:1', 'max:20'],
            'base_abilities.charisma' => ['required', 'integer', 'min:1', 'max:20'],

            /* Vida: padrão, rolagem com os dados do Spellbound ou ajuste manual. */
            'hit_points_method' => [
                'required',
                Rule::in(['standard', 'rolled', 'manual']),
            ],
            'hit_points_rolls' => ['nullable', 'string', 'max:4000'],
            'hit_points_manual' => ['nullable', 'integer', 'min:1', 'max:9999'],

            /*
            |--------------------------------------------------------------------------
            | Multiclasse
            |--------------------------------------------------------------------------
            */

            'multi_class_enabled' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Experiência
            |--------------------------------------------------------------------------
            |
            | XP é opcional porque nem toda campanha usa o sistema.
            |
            */

            'xp_enabled' => [
                'nullable',
                'boolean',
            ],

            'experience_points' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            */

            'custom_prof_enabled' => [
                'nullable',
                'boolean',
            ],

            'proficiency_bonus' => [
                'nullable', 'required_if:custom_prof_enabled,1',
                'integer', 'min:0', 'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Imagem
            |--------------------------------------------------------------------------
            */

            'image' => [
                'nullable',
                'image',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Traços de Espécie e Talento de Origem (opcionais)
            |--------------------------------------------------------------------------
            |
            | Usa a mesma tabela character_features e os mesmos tipos que
            | o módulo Traços & Talentos da ficha de personagem já reconhece.
            |
            */

            'species_traits' => [
                'sometimes',
                'array',
                'max:12',
            ],

            'species_traits.*.name' => [
                'required',
                'string',
                'max:120',
            ],

            'species_traits.*.description' => [
                'nullable',
                'string',
                'max:30000',
            ],

            'origin_feat' => [
                'sometimes',
                'array',
            ],

            'origin_feat.name' => [
                'required_with:origin_feat',
                'string',
                'max:120',
            ],

            'origin_feat.description' => [
                'nullable',
                'string',
                'max:30000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Proficiências concedidas pelo antecedente
            |--------------------------------------------------------------------------
            |
            | Perícias usam character_skills. Armas, armaduras, ferramentas,
            | idiomas e veículos usam os tipos de character_features já
            | reconhecidos pela página de Treinamento & Proficiências.
            |
            */

            'background_skills' => [
                'sometimes',
                'array',
                'max:18',
            ],

            'background_skills.*' => [
                'string',
                'distinct',
                Rule::in([
                    'acrobatics', 'animal_handling', 'arcana', 'athletics',
                    'deception', 'history', 'insight', 'intimidation',
                    'investigation', 'medicine', 'nature', 'perception',
                    'performance', 'persuasion', 'religion',
                    'sleight_of_hand', 'stealth', 'survival',
                ]),
            ],

            'background_trainings' => [
                'sometimes',
                'array',
            ],

            'background_trainings.armor_training' => ['sometimes', 'array', 'max:4'],
            'background_trainings.weapon_training' => ['sometimes', 'array', 'max:3'],
            'background_trainings.tool_proficiency' => ['sometimes', 'array', 'max:10'],
            'background_trainings.language' => ['sometimes', 'array', 'max:16'],
            'background_trainings.vehicle_proficiency' => ['sometimes', 'array', 'max:3'],
            'background_trainings.*.*' => [
                'string',
                'max:120',
            ],

            'background_custom_trainings' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'background_custom_trainings.*.type' => [
                'required',
                Rule::in([
                    'armor_training', 'weapon_training', 'tool_proficiency',
                    'language', 'vehicle_proficiency',
                ]),
            ],

            'background_custom_trainings.*.name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'background_custom_trainings.*.roll_ability' => ['nullable', Rule::in(['strength','dexterity','constitution','intelligence','wisdom','charisma'])],

            /* Expertise é um grau superior da mesma perícia, não uma segunda proficiência. */
            'skill_expertise' => ['sometimes', 'array', 'max:18'],
            'skill_expertise.*' => ['string', 'distinct', Rule::in([
                'acrobatics','animal_handling','arcana','athletics','deception',
                'history','insight','intimidation','investigation','medicine',
                'nature','perception','performance','persuasion','religion',
                'sleight_of_hand','stealth','survival',
            ])],

            /* Proficiências obtidas da classe principal. */
            'class_skills' => ['sometimes', 'array', 'max:18'],
            'class_skills.*' => [
                'string', 'distinct',
                Rule::in([
                    'acrobatics', 'animal_handling', 'arcana', 'athletics',
                    'deception', 'history', 'insight', 'intimidation',
                    'investigation', 'medicine', 'nature', 'perception',
                    'performance', 'persuasion', 'religion',
                    'sleight_of_hand', 'stealth', 'survival',
                ]),
            ],
            'class_trainings' => ['sometimes', 'array'],
            'class_trainings.armor_training' => ['sometimes', 'array', 'max:4'],
            'class_trainings.weapon_training' => ['sometimes', 'array', 'max:3'],
            'class_trainings.tool_proficiency' => ['sometimes', 'array', 'max:10'],
            'class_trainings.language' => ['sometimes', 'array', 'max:16'],
            'class_trainings.vehicle_proficiency' => ['sometimes', 'array', 'max:3'],
            'class_trainings.*.*' => ['string', 'max:120'],
            'class_custom_trainings' => ['sometimes', 'array', 'max:30'],
            'class_custom_trainings.*.type' => [
                'required', Rule::in([
                    'armor_training', 'weapon_training', 'tool_proficiency',
                    'language', 'vehicle_proficiency',
                ]),
            ],
            'class_custom_trainings.*.name' => ['nullable', 'string', 'max:120'],
            'class_custom_trainings.*.roll_ability' => ['nullable', Rule::in(['strength','dexterity','constitution','intelligence','wisdom','charisma'])],
        ]);

        // Atributos finais são derivados dos componentes validados no servidor;
        // não confie no valor oculto enviado pelo navegador.
        $abilityKeys = ['strength','dexterity','constitution','intelligence','wisdom','charisma'];
        $base = $validated['base_abilities'];
        $advanced = array_fill_keys($abilityKeys, 0); // Aumentos por nível/talento são editados posteriormente na ficha.
        $mode = $validated['background_ability_method'];
        $origin = array_fill_keys($abilityKeys, 0);
        $options = array_values(array_filter($validated['background_ability_options'] ?? []));
        $primary = $validated['background_ability_primary'] ?? null;
        $secondary = $validated['background_ability_secondary'] ?? null;

        if ($mode !== 'none') {
            if (count($options) !== 3 || count(array_unique($options)) !== 3) {
                throw ValidationException::withMessages([
                    'background_ability_options' => 'Escolha três atributos elegíveis diferentes para o antecedente.',
                ]);
            }
            if ($mode === 'three') {
                foreach ($options as $key) $origin[$key] = 1;
            } else {
                if (!$primary || !$secondary || $primary === $secondary
                    || !in_array($primary, $options, true) || !in_array($secondary, $options, true)) {
                    throw ValidationException::withMessages([
                        'background_ability_primary' => 'Escolha dois atributos distintos entre os três elegíveis para +2 e +1.',
                    ]);
                }
                $origin[$primary] = 2;
                $origin[$secondary] = 1;
            }
        }

        // Validação dos valores padrão ocorre ANTES dos bônus de origem ou nível.
        if (($validated['ability_generation_method'] ?? 'manual') === 'standard') {
            $chosen = array_map('intval', array_values($base));
            sort($chosen, SORT_NUMERIC);
            if ($chosen !== [8,10,12,13,14,15]) {
                throw ValidationException::withMessages([
                    'base_abilities' => 'No conjunto padrão, utilize 15, 14, 13, 12, 10 e 8 uma única vez.',
                ]);
            }
        }
        $validated['background_ability_bonuses'] = $origin;
        foreach ($abilityKeys as $key) {
            $final = (int) $base[$key] + $origin[$key] + (int) $advanced[$key];
            if ($final > 20) {
                throw ValidationException::withMessages([
                    'abilities.'.$key => 'O valor final de '. $key .' não pode ultrapassar 20 na criação.',
                ]);
            }
            if ((int) $validated['abilities'][$key] !== $final) {
                throw ValidationException::withMessages([
                    'abilities.'.$key => 'Os valores dos atributos mudaram. Revise os bônus antes de criar o personagem.',
                ]);
            }
        }
        $validSkills = array_unique(array_merge($validated['class_skills'] ?? [], $validated['background_skills'] ?? []));
        foreach (($validated['skill_expertise'] ?? []) as $expertSkill) {
            if (!in_array($expertSkill, $validSkills, true)) {
                throw ValidationException::withMessages([
                    'skill_expertise' => 'Expertise requer proficiência na perícia correspondente.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalização dos controles opcionais
        |--------------------------------------------------------------------------
        */

        $validated['multi_class_enabled'] = $request->boolean(
            'multi_class_enabled'
        );

        $validated['xp_enabled'] = $request->boolean(
            'xp_enabled'
        );

        $validated['custom_prof_enabled'] = $request->boolean(
            'custom_prof_enabled'
        );

        /*
        |--------------------------------------------------------------------------
        | XP
        |--------------------------------------------------------------------------
        |
        | Se o sistema de XP estiver desligado, não carregamos um valor
        | acidentalmente do formulário.
        |
        */

        if (! $validated['xp_enabled']) {
            $validated['experience_points'] = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Proficiência
        |--------------------------------------------------------------------------
        |
        | Se não houver override, o Service calcula automaticamente
        | com base no nível total.
        |
        */

        if (! $validated['custom_prof_enabled']) {
            $validated['proficiency_bonus'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Multiclasse desativada
        |--------------------------------------------------------------------------
        |
        | O formulário pode mandar apenas uma classe. Neste caso mantemos
        | apenas a primeira entrada.
        |
        */

        if (! $validated['multi_class_enabled']) {
            $validated['classes'] = [
                $validated['classes'][0],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Normalização dos nomes das classes
        |--------------------------------------------------------------------------
        |
        | Além de validar duplicação, limpamos espaços desnecessários.
        |
        */

        $validated['classes'] = collect($validated['classes'])
            ->map(function (array $class) {
                return [
                    'class' => trim($class['class']),
                    'subclass' => isset($class['subclass'])
                        ? trim((string) $class['subclass'])
                        : null,
                    'level' => (int) $class['level'],
                    'hit_die' => (int) ($class['hit_die'] ?? 8),
                    'spellcaster' => (bool) ($class['spellcaster'] ?? false),
                    'spell_ability' => (string) ($class['spell_ability'] ?? 'wisdom'),
                    'caster_type' => (string) ($class['caster_type'] ?? 'full'),
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Classes duplicadas
        |--------------------------------------------------------------------------
        |
        | A migration usa unique(character_id, class).
        |
        */

        $classNames = collect($validated['classes'])
            ->pluck('class')
            ->map(
                fn (string $class) =>
                    mb_strtolower(trim($class))
            )
            ->values();

        if ($classNames->duplicates()->isNotEmpty()) {
            return back()
                ->withErrors([
                    'classes' => 'Uma mesma classe não pode ser adicionada duas vezes.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Nível total
        |--------------------------------------------------------------------------
        |
        | Exemplo:
        | Clérigo 5 + Guerreiro 3 = nível 8
        |
        */

        $totalLevel = collect($validated['classes'])
            ->sum(
                fn (array $class) =>
                    (int) $class['level']
            );

        if ($totalLevel < 1 || $totalLevel > 20) {
            return back()
                ->withErrors([
                    'classes' => 'A soma dos níveis das classes deve estar entre 1 e 20.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Multiclasse coerente com o total
        |--------------------------------------------------------------------------
        |
        | Se houver mais de uma classe, o modo precisa estar ativo.
        |
        */

        if (
            count($validated['classes']) > 1 &&
            ! $validated['multi_class_enabled']
        ) {
            return back()
                ->withErrors([
                    'classes' => 'Ative a opção de multiclasse para adicionar mais de uma classe.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Imagem
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('characters', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Criação
        |--------------------------------------------------------------------------
        */

        try {
            $characterCreationService->create(
                [
                    ...$validated,
                    'user_id' => Auth::id(),
                    'level' => $totalLevel,
                ],
                $imagePath
            );
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('characters.index')
            ->with(
                'success',
                'Personagem criado com sucesso.'
            );
    }

    public function destroy(
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->user_id === Auth::id(),
            403
        );

        if ($character->image_path) {
            Storage::disk('public')->delete(
                $character->image_path
            );
        }

        $character->delete();

        return redirect()
            ->route('characters.index')
            ->with(
                'success',
                'Personagem removido.'
            );
    }
}
