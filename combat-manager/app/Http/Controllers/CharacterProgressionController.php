<?php

namespace App\Http\Controllers;

use App\Models\Character;

use App\Models\CampaignCharacter;

use App\Support\CharacterExperience;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;

class CharacterProgressionController extends Controller

{

    /*

    |--------------------------------------------------------------------------

    | SUBIR DE NÍVEL

    |--------------------------------------------------------------------------

    */

    public function levelUp(

        Request $request,

        Character $character

    ): JsonResponse {

        return $this->changeLevel(

            $request,

            $character,

            +1

        );

    }

    /*

    |--------------------------------------------------------------------------

    | VOLTAR UM NÍVEL

    |--------------------------------------------------------------------------

    |

    | A classe nunca é reduzida abaixo do nível 1.

    |

    | Caso no futuro seja necessário "remover" uma multiclass, isso deve ser

    | uma ação separada, porque envolve apagar a entrada de character_classes.

    |

    */

    public function levelDown(

        Request $request,

        Character $character

    ): JsonResponse {

        return $this->changeLevel(

            $request,

            $character,

            -1

        );

    }

    /*

    |--------------------------------------------------------------------------

    | ALTERAÇÃO DE NÍVEL

    |--------------------------------------------------------------------------

    */

    private function changeLevel(

        Request $request,

        Character $character,

        int $direction

    ): JsonResponse {

        $this->authorizeCharacter(

            $request,

            $character

        );

        if (

            !in_array(

                $direction,

                [-1, 1],

                true

            )

        ) {

            abort(422);

        }

        $validated = $request->validate([

            'class_id' => [

                'required',

                'integer',

            ],

                'campaign_id' => [

                    'nullable',

                    'integer',

                    'min:1',

                ],

        ]);

        $result = DB::transaction(

            function () use (

                $character,

                $validated,

                $direction

            ): array {

                $lockedCharacter =

                    Character::query()

                        ->whereKey(

                            $character->getKey()

                        )

                        ->lockForUpdate()

                        ->firstOrFail();

                $classes =

                    $lockedCharacter

                        ->classes()

                        ->lockForUpdate()

                        ->orderBy('sort_order')

                        ->orderBy('id')

                        ->get();

                $selectedClass =

                    $classes->first(

                        fn ($class) =>

                            (int) $class->id

                            === (int) $validated['class_id']

                    );

                if (!$selectedClass) {

                    throw ValidationException::withMessages([

                        'class_id' =>

                            'A classe selecionada não pertence a este personagem.',

                    ]);

                }

                $currentTotal =

                    (int) $classes->sum(

                        fn ($class) =>

                            (int) $class->level

                    );

                /*

                |--------------------------------------------------------------------------

                | CAMPANHA COM XP — AUTORIZAÇÃO DE SUBIDA

                |--------------------------------------------------------------------------

                |

                | A interface não é uma barreira de segurança. Mesmo requisições

                | manuais passam por esta checagem, com o Character bloqueado.

                | Para fichas fora de campanhas com XP, preserva-se o modo livre.

                |

                */

                if ($direction > 0) {

                    $links = CampaignCharacter::query()

                        ->with('campaign')

                        ->where('character_id', $lockedCharacter->id)

                        ->whereHas('campaign')

                        ->orderByDesc('is_active')

                        ->orderByDesc('updated_at')

                        ->lockForUpdate()

                        ->get();

                    $requestedCampaignId = (int) (

                        $validated['campaign_id'] ?? 0

                    );

                    if ($requestedCampaignId > 0) {

                        $experienceLink = $links->first(

                            fn (CampaignCharacter $link) =>

                                (int) $link->campaign_id === $requestedCampaignId

                        );

                        if (!$experienceLink) {

                            throw ValidationException::withMessages([

                                'campaign_id' =>

                                    'A campanha selecionada não pertence a esta ficha.',

                            ]);

                        }

                    } else {

                        $experienceLink = $links->first(

                            fn (CampaignCharacter $link) =>

                                (bool) ($link->campaign?->experience_enabled ?? false)

                        );

                    }

                    // Uma campanha sem XP não pode liberar a subida se a ficha
                    // também está vinculada a uma campanha que exige XP.
                    if (!(bool) ($experienceLink?->campaign?->experience_enabled ?? false)) {
                        $experienceLink = $links->first(
                            fn (CampaignCharacter $link) =>
                                (bool) ($link->campaign?->experience_enabled ?? false)
                        );
                    }

                    if ((bool) ($experienceLink?->campaign?->experience_enabled ?? false)) {

                        $nextThreshold = CharacterExperience::nextThreshold(

                            $currentTotal

                        );

                        $effectiveXP = CharacterExperience::effectiveTotal(

                            $currentTotal,

                            (int) ($experienceLink->experience_points ?? 0)

                        );

                        if ($nextThreshold === null || $effectiveXP < $nextThreshold) {

                            $remainingXP = $nextThreshold === null

                                ? 0

                                : max(0, $nextThreshold - $effectiveXP);

                            throw ValidationException::withMessages([

                                'class_id' => $nextThreshold === null

                                    ? 'O personagem já chegou ao nível 20.'

                                    : 'Você ainda precisa de '

                                        . number_format($remainingXP, 0, ',', '.')

                                        . ' XP para subir de nível.',

                            ]);

                        }

                    }

                }

                /*

                |--------------------------------------------------------------------------

                | LIMITES

                |--------------------------------------------------------------------------

                */

                if (

                    $direction > 0

                    && $currentTotal >= 20

                ) {

                    throw ValidationException::withMessages([

                        'class_id' =>

                            'O personagem já atingiu o nível total 20.',

                    ]);

                }

                if (

                    $direction > 0

                    && (int) $selectedClass->level >= 20

                ) {

                    throw ValidationException::withMessages([

                        'class_id' =>

                            'Esta classe já atingiu o nível 20.',

                    ]);

                }

                if (

                    $direction < 0

                    && (int) $selectedClass->level <= 1

                ) {

                    throw ValidationException::withMessages([

                        'class_id' =>

                            'Uma classe não pode ser reduzida abaixo do nível 1.',

                    ]);

                }

                /*

                |--------------------------------------------------------------------------

                | CLASSE ESCOLHIDA

                |--------------------------------------------------------------------------

                */

                $selectedClass->level =

                    (int) $selectedClass->level

                    + $direction;

                $selectedClass->save();

                /*

                |--------------------------------------------------------------------------

                | NÍVEL TOTAL

                |--------------------------------------------------------------------------

                */

                $newTotal =

                    $currentTotal

                    + $direction;

                $newTotal =

                    max(

                        1,

                        min(

                            20,

                            $newTotal

                        )

                    );

                $lockedCharacter->level =

                    $newTotal;

                /*

                |--------------------------------------------------------------------------

                | PROFICIÊNCIA AUTOMÁTICA

                |--------------------------------------------------------------------------

                |

                | Proficiência Custom ligada:

                |     preserva o valor manual.

                |

                | Proficiência Custom desligada:

                |     recalcula pelo novo nível total.

                |

                */

                $settings =

                    is_array(

                        $lockedCharacter->sheet_settings

                        ?? null

                    )

                        ? $lockedCharacter->sheet_settings

                        : [];

                $progressionSettings =

                    is_array(

                        $settings['progression']

                        ?? null

                    )

                        ? $settings['progression']

                        : [];

                $customEnabled =

                    (bool) (

                        $progressionSettings[

                            'proficiency_custom_enabled'

                        ]

                        ?? false

                    );

                $calculatedProficiency =

                    $this->calculatedProficiency(

                        $newTotal

                    );

                if (!$customEnabled) {

                    $lockedCharacter->proficiency_bonus =

                        $calculatedProficiency;

                }

                $lockedCharacter->save();

                /*

                |--------------------------------------------------------------------------

                | RETORNO

                |--------------------------------------------------------------------------

                */

                $classes =

                    $lockedCharacter

                        ->classes()

                        ->orderByDesc('is_primary')

                        ->orderByDesc('level')

                        ->orderBy('sort_order')

                        ->orderBy('id')

                        ->get();

                return [

                    'level' =>

                        $newTotal,

                    'proficiency_bonus' =>

                        (int) $lockedCharacter->proficiency_bonus,

                    'calculated_proficiency' =>

                        $calculatedProficiency,

                    'proficiency_custom_enabled' =>

                        $customEnabled,

                    'classes' =>

                        $classes

                            ->map(

                                fn ($class) => [

                                    'id' =>

                                        (int) $class->id,

                                    'class' =>

                                        (string) $class->class,

                                    'subclass' =>

                                        $class->subclass,

                                    'level' =>

                                        (int) $class->level,

                                    'is_primary' =>

                                        (bool) (

                                            $class->is_primary

                                            ?? false

                                        ),

                                ]

                            )

                            ->values()

                            ->all(),

                ];

            }

        );

        $isLevelUp =

            $direction > 0;

        return response()->json([

            'success' =>

                true,

            'message' =>

                $isLevelUp

                    ? 'Nível aumentado com sucesso.'

                    : 'Nível reduzido com sucesso.',

            'character' => [

                'id' =>

                    (int) $character->getKey(),

                'level' =>

                    $result['level'],

                'proficiency_bonus' =>

                    $result['proficiency_bonus'],

            ],

            'progression' => [

                'direction' =>

                    $isLevelUp

                        ? 'up'

                        : 'down',

                'calculated_proficiency' =>

                    $result['calculated_proficiency'],

                'proficiency_custom_enabled' =>

                    $result['proficiency_custom_enabled'],

            ],

            'classes' =>

                $result['classes'],

        ]);

    }

    /*

    |--------------------------------------------------------------------------

    | PROFICIÊNCIA CUSTOM

    |--------------------------------------------------------------------------

    */

    public function updateProficiency(

        Request $request,

        Character $character

    ): JsonResponse {

        $this->authorizeCharacter(

            $request,

            $character

        );

        $validated = $request->validate([

            'enabled' => [

                'required',

                'boolean',

            ],

            /*

             * Sem min/max de propósito.

             * O jogador pode escolher qualquer inteiro.

             */

            'value' => [

                'nullable',

                'integer',

            ],

        ]);

        $result = DB::transaction(

            function () use (

                $character,

                $validated

            ): array {

                $lockedCharacter =

                    Character::query()

                        ->whereKey(

                            $character->getKey()

                        )

                        ->lockForUpdate()

                        ->firstOrFail();

                $enabled =

                    (bool) $validated['enabled'];

                if (

                    $enabled

                    && !array_key_exists(

                        'value',

                        $validated

                    )

                ) {

                    throw ValidationException::withMessages([

                        'value' =>

                            'Informe o valor da proficiência customizada.',

                    ]);

                }

                $level =

                    max(

                        1,

                        (int) (

                            $lockedCharacter->level

                            ?? 1

                        )

                    );

                $calculated =

                    $this->calculatedProficiency(

                        $level

                    );

                $settings =

                    is_array(

                        $lockedCharacter->sheet_settings

                        ?? null

                    )

                        ? $lockedCharacter->sheet_settings

                        : [];

                if (

                    !isset(

                        $settings['progression']

                    )

                    || !is_array(

                        $settings['progression']

                    )

                ) {

                    $settings['progression'] =

                        [];

                }

                $settings['progression'][

                    'proficiency_custom_enabled'

                ] =

                    $enabled;

                if ($enabled) {

                    $lockedCharacter->proficiency_bonus =

                        (int) $validated['value'];

                } else {

                    $lockedCharacter->proficiency_bonus =

                        $calculated;

                }

                $lockedCharacter->sheet_settings =

                    $settings;

                $lockedCharacter->save();

                return [

                    'proficiency_bonus' =>

                        (int) $lockedCharacter->proficiency_bonus,

                    'calculated_proficiency' =>

                        $calculated,

                    'proficiency_custom_enabled' =>

                        $enabled,

                    'sheet_settings' =>

                        $settings,

                ];

            }

        );

        return response()->json([

            'success' =>

                true,

            'message' =>

                $result['proficiency_custom_enabled']

                    ? 'Proficiência customizada aplicada.'

                    : 'Proficiência automática restaurada.',

            'character' => [

                'id' =>

                    (int) $character->getKey(),

                'proficiency_bonus' =>

                    $result['proficiency_bonus'],

                'sheet_settings' =>

                    $result['sheet_settings'],

            ],

            'progression' => [

                'calculated_proficiency' =>

                    $result['calculated_proficiency'],

                'proficiency_custom_enabled' =>

                    $result['proficiency_custom_enabled'],

            ],

        ]);

    }

    /*

    |--------------------------------------------------------------------------

    | HELPERS

    |--------------------------------------------------------------------------

    */

    /*
    |--------------------------------------------------------------------------
    | IDENTIDADE DE CLASSE / SUBCLASSE — NÃO CONSOME XP
    |--------------------------------------------------------------------------
    */
    public function updateClass(
        Request $request,
        Character $character,
        int $classId
    ): JsonResponse {
        $this->authorizeCharacter($request, $character);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'subclass' => ['nullable', 'string', 'max:80'],
        ]);

        $name = trim($validated['name']);
        $subclass = trim((string) ($validated['subclass'] ?? ''));

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => 'Informe o nome da classe.',
            ]);
        }

        $entry = DB::transaction(function () use ($character, $classId, $name, $subclass): array {
            $lockedCharacter = Character::query()
                ->whereKey($character->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $classes = $lockedCharacter->classes()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $selected = $classes->first(
                fn ($item) => (int) $item->getKey() === $classId
            );

            if (!$selected) {
                throw ValidationException::withMessages([
                    'class_id' => 'Esta classe não pertence ao personagem.',
                ]);
            }

            if ($classes->contains(fn ($item) =>
                (int) $item->getKey() !== $classId
                && mb_strtolower(trim((string) $item->class)) === mb_strtolower($name)
            )) {
                throw ValidationException::withMessages([
                    'name' => 'O personagem já possui uma classe com esse nome.',
                ]);
            }

            $selected->class = $name;
            $selected->subclass = $subclass === '' ? null : $subclass;
            $selected->save();

            return [
                'id' => (int) $selected->getKey(),
                'name' => (string) $selected->class,
                'subclass' => $selected->subclass,
                'level' => (int) $selected->level,
                'is_primary' => (bool) ($selected->is_primary ?? false),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Classe e subclasse atualizadas.',
            'class' => $entry,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | INICIAR MULTICLASSE COMO UMA SUBIDA DE NÍVEL
    |--------------------------------------------------------------------------
    |
    | Criar uma nova classe consome exatamente um nível e exige XP quando a
    | campanha usa progressão por experiência.
    */
    public function storeMulticlass(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter($request, $character);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'subclass' => ['nullable', 'string', 'max:80'],
            'campaign_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $name = trim($validated['name']);
        $subclass = trim((string) ($validated['subclass'] ?? ''));
        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => 'Informe o nome da nova classe.',
            ]);
        }

        $result = DB::transaction(function () use ($character, $validated, $name, $subclass): array {
            $lockedCharacter = Character::query()
                ->whereKey($character->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $classes = $lockedCharacter->classes()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($classes->isEmpty()) {
                throw ValidationException::withMessages([
                    'name' => 'O personagem precisa ter uma classe inicial.',
                ]);
            }

            if ($classes->count() >= 6) {
                throw ValidationException::withMessages([
                    'name' => 'O limite é de seis classes por personagem.',
                ]);
            }

            $level = (int) $classes->sum(fn ($item) => (int) $item->level);
            if ($level >= CharacterExperience::MAX_LEVEL) {
                throw ValidationException::withMessages([
                    'name' => 'O personagem já atingiu o nível máximo (20).',
                ]);
            }

            if ($classes->contains(fn ($item) =>
                mb_strtolower(trim((string) $item->class)) === mb_strtolower($name)
            )) {
                throw ValidationException::withMessages([
                    'name' => 'Essa classe já existe. Aumente o nível dela.',
                ]);
            }

            $links = CampaignCharacter::query()
                ->with('campaign')
                ->where('character_id', $lockedCharacter->getKey())
                ->whereHas('campaign')
                ->orderByDesc('is_active')
                ->orderByDesc('updated_at')
                ->lockForUpdate()
                ->get();

            $requestedCampaignId = (int) ($validated['campaign_id'] ?? 0);
            $requestedLink = $requestedCampaignId > 0
                ? $links->first(fn ($link) => (int) $link->campaign_id === $requestedCampaignId)
                : null;

            if ($requestedCampaignId > 0 && !$requestedLink) {
                throw ValidationException::withMessages([
                    'campaign_id' => 'Esta campanha não pertence ao personagem.',
                ]);
            }

            // Não permitir bypass da regra de XP indicando uma campanha sem XP.
            $xpLink = $links->first(
                fn ($link) => (bool) ($link->campaign?->experience_enabled ?? false)
            );
            if ($requestedLink?->campaign?->experience_enabled) {
                $xpLink = $requestedLink;
            }

            if ($xpLink) {
                $threshold = CharacterExperience::nextThreshold($level);
                $effective = CharacterExperience::effectiveTotal(
                    $level,
                    (int) ($xpLink->experience_points ?? 0)
                );

                if ($threshold === null || $effective < $threshold) {
                    throw ValidationException::withMessages([
                        'name' => $threshold === null
                            ? 'O personagem já atingiu o nível máximo.'
                            : 'Faltam '
                                . number_format(max(0, $threshold - $effective), 0, ',', '.')
                                . ' XP para subir de nível.',
                    ]);
                }
            }

            $newClass = $lockedCharacter->classes()->make();
            $newClass->class = $name;
            $newClass->subclass = $subclass === '' ? null : $subclass;
            $newClass->level = 1;
            $newClass->sort_order = min(255, (int) $classes->max('sort_order') + 1);
            $newClass->is_primary = false;
            $newClass->save();

            $newLevel = $level + 1;
            $settings = is_array($lockedCharacter->sheet_settings ?? null)
                ? $lockedCharacter->sheet_settings
                : [];
            $customEnabled = (bool) data_get(
                $settings,
                'progression.proficiency_custom_enabled',
                false
            );
            $calculated = $this->calculatedProficiency($newLevel);

            $lockedCharacter->level = $newLevel;
            if (!$customEnabled) {
                $lockedCharacter->proficiency_bonus = $calculated;
            }
            $lockedCharacter->save();

            return [
                'level' => $newLevel,
                'proficiency_bonus' => (int) $lockedCharacter->proficiency_bonus,
                'calculated_proficiency' => $calculated,
                'proficiency_custom_enabled' => $customEnabled,
                'classes' => $lockedCharacter->classes()
                    ->orderByDesc('is_primary')
                    ->orderByDesc('level')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($item) => [
                        'id' => (int) $item->getKey(),
                        'class' => (string) $item->class,
                        'subclass' => $item->subclass,
                        'level' => (int) $item->level,
                        'is_primary' => (bool) ($item->is_primary ?? false),
                    ])->values()->all(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Multiclasse adicionada e nível aumentado com sucesso.',
            'character' => [
                'id' => (int) $character->getKey(),
                'level' => $result['level'],
                'proficiency_bonus' => $result['proficiency_bonus'],
            ],
            'progression' => [
                'calculated_proficiency' => $result['calculated_proficiency'],
                'proficiency_custom_enabled' => $result['proficiency_custom_enabled'],
            ],
            'classes' => $result['classes'],
        ]);
    }

    private function calculatedProficiency(

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

    private function authorizeCharacter(

        Request $request,

        Character $character

    ): void {

        if (

            $request->user()

            && isset(

                $character->user_id

            )

        ) {

            abort_unless(

                (int) $character->user_id

                === (int) $request->user()->id,

                403

            );

        }

    }

}