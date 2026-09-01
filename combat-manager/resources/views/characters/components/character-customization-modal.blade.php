@props([
    'character',
    'classes' => collect(),
])

@php
    /*
    |--------------------------------------------------------------------------
    | DADOS INICIAIS
    |--------------------------------------------------------------------------
    */

    $sortedClasses =
        collect($classes)
            ->sortByDesc(function ($class) {
                return [
                    (int) ($class->is_primary ?? false),
                    (int) ($class->level ?? 0),
                    -((int) ($class->sort_order ?? 0)),
                ];
            })
            ->values();

    $sheetSettings =
        is_array(
            $character->sheet_settings
            ?? null
        )
            ? $character->sheet_settings
            : [];

    $displaySettings =
        is_array(
            $sheetSettings['display']
            ?? null
        )
            ? $sheetSettings['display']
            : [];

    /*
     * Compatibilidade:
     * - show_defenses é a chave nova;
     * - show_empty_defenses é aceita somente para registros já salvos
     *   durante a primeira implementação.
     */
    $initialShowDefenses =
        array_key_exists(
            'show_defenses',
            $displaySettings
        )
            ? (bool) $displaySettings['show_defenses']
            : (
                array_key_exists(
                    'show_empty_defenses',
                    $displaySettings
                )
                    ? (bool) $displaySettings['show_empty_defenses']
                    : true
            );

    $progressionSettings =
        is_array(
            $sheetSettings['progression']
            ?? null
        )
            ? $sheetSettings['progression']
            : [];

    $proficiencyCustomEnabled =
        (bool) (
            $progressionSettings[
                'proficiency_custom_enabled'
            ]
            ?? false
        );

    $characterLevel =
        max(
            1,
            (int) (
                $character->level
                ?? 1
            )
        );

    $calculatedProficiency =
        match (true) {
            $characterLevel >= 17 => 6,
            $characterLevel >= 13 => 5,
            $characterLevel >= 9 => 4,
            $characterLevel >= 5 => 3,
            default => 2,
        };

    $currentProficiency =
        (int) (
            $character->proficiency_bonus
            ?? $calculatedProficiency
        );

    $initialSettings =
        array_replace_recursive(
            [
                'display' => [
                    'show_defenses' =>
                        $initialShowDefenses,

                    'show_experience' =>
                        false,
                ],

                'progression' => [
                    'proficiency_custom_enabled' =>
                        $proficiencyCustomEnabled,
                ],

                'optional_rules' => [
                    'morquen' => false,
                    'exhaustion' => false,
                ],
            ],
            $sheetSettings
        );

    unset(
        $initialSettings['display']['show_empty_defenses']
    );

    $initialImageUrl =
        $character->image_path
            ? \Illuminate\Support\Facades\Storage::disk(
                'public'
            )->url(
                $character->image_path
            )
            : null;

    $initialPayload = [
        'name' =>
            $character->name
            ?? '',

        'image_url' =>
            $initialImageUrl,

        'background' =>
            $character->background
            ?? '',

        'species' =>
            $character->species
            ?? '',

        'level' =>
            $characterLevel,

        'proficiency' => [
            'current' =>
                $currentProficiency,

            'calculated' =>
                $calculatedProficiency,

            'custom_enabled' =>
                $proficiencyCustomEnabled,
        ],

        'settings' =>
            $initialSettings,

        'classes' =>
            $sortedClasses
                ->map(function ($class) {
                    return [
                        'id' =>
                            $class->id
                            ?? null,

                        'name' =>
                            $class->class
                            ?? 'Classe',

                        'subclass' =>
                            $class->subclass
                            ?? null,

                        'level' =>
                            (int) (
                                $class->level
                                ?? 0
                            ),

                        'is_primary' =>
                            (bool) (
                                $class->is_primary
                                ?? false
                            ),
                    ];
                })
                ->all(),
    ];

    /*
     * O JSON fica em data-* e não dentro de x-data.
     *
     * Isso evita misturar um objeto JavaScript enorme com o parser
     * de atributos HTML/Blade.
     */
    $encodedInitialPayload =
        e(
            json_encode(
                $initialPayload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );
@endphp


{{-- ================================================================
     ALPINE
================================================================= --}}

<script>
    document.addEventListener(
        'alpine:init',
        () => {
            Alpine.data(
                'characterCustomizationModal',
                () => ({
                    open: false,
                    activeTab: 'identity',

                    saving: false,
                    saveError: null,

                    identityImageFile: null,
                    identityImagePreview: null,
                    identityImageRemove: false,
                    identityImageError: null,

                    levelingClassId: null,
                    levelingDirection: null,

                    levelConfirmOpen: false,
                    levelConfirmMode: null,
                    levelConfirmClass: null,

                    progressionError: null,
                    progressionMessage: null,

                    proficiencyDrawerOpen: false,
                    morquenRuleOpen: false,
                    exhaustionRuleOpen: false,

                    specialRulesUnlocked: false,
                    specialRulesCode: '',
                    specialRulesCodeError: null,

                    proficiencySaving: false,
                    proficiencyError: null,
                    proficiencyMessage: null,
                    customProficiencyValue: 2,

                    requiresPageRefresh: false,

                    initial: {
                        name: '',
                        background: '',
                        species: '',
                        image_url: null,
                        level: 1,

                        proficiency: {
                            current: 2,
                            calculated: 2,
                            custom_enabled: false,
                        },

                        settings: {
                            display: {
                                show_defenses: true,
                                show_experience: false,
                            },

                            progression: {
                                proficiency_custom_enabled: false,
                            },

                            optional_rules: {
                                morquen: false,
                                exhaustion: false,
                            },
                        },

                        classes: [],
                    },

                    /*
                     * IMPORTANTE:
                     *
                     * draft nunca é null.
                     * Os x-models abaixo são inicializados pelo Alpine
                     * mesmo quando o modal está escondido.
                     */
                    draft: {
                        name: '',
                        background: '',
                        species: '',
                        image_url: null,
                        level: 1,

                        proficiency: {
                            current: 2,
                            calculated: 2,
                            custom_enabled: false,
                        },

                        settings: {
                            display: {
                                show_defenses: true,
                                show_experience: false,
                            },

                            progression: {
                                proficiency_custom_enabled: false,
                            },

                            optional_rules: {
                                morquen: false,
                                exhaustion: false,
                            },
                        },

                        classes: [],
                    },

                    saveUrl: '',
                    imageSaveUrl: '',
                    levelUpUrl: '',
                    levelDownUrl: '',
                    proficiencyUrl: '',


                    /*
                    |--------------------------------------------------------------------------
                    | INIT
                    |--------------------------------------------------------------------------
                    */

                    init() {
                        this.saveUrl =
                            this.$el.dataset.saveUrl
                            ?? '';

                        this.imageSaveUrl =
                            this.$el.dataset.imageSaveUrl
                            ?? '';

                        this.levelUpUrl =
                            this.$el.dataset.levelUpUrl
                            ?? '';

                        this.levelDownUrl =
                            this.$el.dataset.levelDownUrl
                            ?? '';

                        this.proficiencyUrl =
                            this.$el.dataset.proficiencyUrl
                            ?? '';

                        try {
                            const parsed =
                                JSON.parse(
                                    this.$el.dataset.initial
                                    ?? '{}'
                                );

                            this.initial =
                                this.normalizeInitial(
                                    parsed
                                );

                        } catch (error) {
                            console.error(
                                'Erro ao carregar configuração inicial:',
                                error
                            );
                        }

                        this.resetDraft();

                        try {
                            this.specialRulesUnlocked =
                                sessionStorage.getItem(
                                    'spellbound.special_rules_unlocked'
                                ) === '1';
                        } catch (error) {
                            this.specialRulesUnlocked =
                                false;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | HELPERS
                    |--------------------------------------------------------------------------
                    */

                    clone(value) {
                        return JSON.parse(
                            JSON.stringify(
                                value
                            )
                        );
                    },

                    normalizeInitial(value) {
                        const source =
                            value
                            && typeof value === 'object'
                                ? value
                                : {};

                        const settings =
                            source.settings
                            && typeof source.settings === 'object'
                                ? source.settings
                                : {};

                        const display =
                            settings.display
                            && typeof settings.display === 'object'
                                ? settings.display
                                : {};

                        const progression =
                            settings.progression
                            && typeof settings.progression === 'object'
                                ? settings.progression
                                : {};

                        const optionalRules =
                            settings.optional_rules
                            && typeof settings.optional_rules === 'object'
                                ? settings.optional_rules
                                : {};

                        const proficiency =
                            source.proficiency
                            && typeof source.proficiency === 'object'
                                ? source.proficiency
                                : {};

                        return {
                            name:
                                String(
                                    source.name
                                    ?? ''
                                ),

                            image_url:
                                source.image_url
                                ? String(
                                    source.image_url
                                )
                                : null,

                            background:
                                String(
                                    source.background
                                    ?? ''
                                ),

                            species:
                                String(
                                    source.species
                                    ?? ''
                                ),

                            level:
                                Math.max(
                                    1,
                                    parseInt(
                                        source.level
                                    )
                                    || 1
                                ),

                            proficiency: {
                                current:
                                    parseInt(
                                        proficiency.current
                                    )
                                    || 0,

                                calculated:
                                    parseInt(
                                        proficiency.calculated
                                    )
                                    || 2,

                                custom_enabled:
                                    !!proficiency.custom_enabled,
                            },

                            settings: {
                                display: {
                                    show_defenses:
                                        Object.prototype.hasOwnProperty.call(
                                            display,
                                            'show_defenses'
                                        )
                                            ? !!display.show_defenses
                                            : true,

                                    show_experience:
                                        !!display.show_experience,
                                },

                                progression: {
                                    proficiency_custom_enabled:
                                        Object.prototype.hasOwnProperty.call(
                                            progression,
                                            'proficiency_custom_enabled'
                                        )
                                            ? !!progression.proficiency_custom_enabled
                                            : !!proficiency.custom_enabled,
                                },

                                optional_rules: {
                                    ...optionalRules,

                                    morquen:
                                        !!optionalRules.morquen,
                                },
                            },

                            classes:
                                Array.isArray(
                                    source.classes
                                )
                                    ? source.classes
                                    : [],
                        };
                    },

                    resetDraft() {
                        this.draft =
                            this.clone(
                                this.initial
                            );

                        this.identityImageFile =
                            null;

                        this.identityImagePreview =
                            this.draft.image_url
                            ?? null;

                        this.identityImageRemove =
                            false;

                        this.identityImageError =
                            null;

                        this.customProficiencyValue =
                            parseInt(
                                this.draft.proficiency.current
                            );

                        if (
                            Number.isNaN(
                                this.customProficiencyValue
                            )
                        ) {
                            this.customProficiencyValue =
                                this.draft.proficiency.calculated;
                        }
                    },

                    unlockSpecialRules() {
                        this.specialRulesCodeError =
                            null;

                        const code =
                            String(
                                this.specialRulesCode
                                ?? ''
                            )
                                .trim()
                                .toUpperCase();

                        if (code !== 'MORQUEN') {
                            this.specialRulesCodeError =
                                'Código inválido.';

                            return;
                        }

                        this.specialRulesUnlocked =
                            true;

                        this.specialRulesCode =
                            '';

                        try {
                            sessionStorage.setItem(
                                'spellbound.special_rules_unlocked',
                                '1'
                            );
                        } catch (error) {
                            // Mantém desbloqueado no componente.
                        }
                    },


                    lockSpecialRules() {
                        this.specialRulesUnlocked =
                            false;

                        this.specialRulesCode =
                            '';

                        this.specialRulesCodeError =
                            null;

                        this.morquenRuleOpen =
                            false;

                        try {
                            sessionStorage.removeItem(
                                'spellbound.special_rules_unlocked'
                            );
                        } catch (error) {
                            // Nada a fazer.
                        }
                    },


                    handleIdentityImage(event) {
                        const file =
                            event?.target?.files?.[0]
                            ?? null;

                        if (!file) {
                            return;
                        }

                        this.identityImageError =
                            null;

                        const allowedTypes = [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ];

                        if (
                            !allowedTypes.includes(
                                file.type
                            )
                        ) {
                            this.identityImageError =
                                'Use uma imagem JPG, PNG ou WEBP.';

                            event.target.value =
                                '';

                            return;
                        }

                        if (
                            file.size
                            > 5 * 1024 * 1024
                        ) {
                            this.identityImageError =
                                'A imagem deve ter no máximo 5 MB.';

                            event.target.value =
                                '';

                            return;
                        }

                        this.identityImageFile =
                            file;

                        this.identityImageRemove =
                            false;

                        if (
                            this.identityImagePreview
                            && this.identityImagePreview.startsWith(
                                'blob:'
                            )
                        ) {
                            URL.revokeObjectURL(
                                this.identityImagePreview
                            );
                        }

                        this.identityImagePreview =
                            URL.createObjectURL(
                                file
                            );
                    },


                    removeIdentityImage() {
                        if (
                            this.identityImagePreview
                            && this.identityImagePreview.startsWith(
                                'blob:'
                            )
                        ) {
                            URL.revokeObjectURL(
                                this.identityImagePreview
                            );
                        }

                        this.identityImageFile =
                            null;

                        this.identityImagePreview =
                            null;

                        this.identityImageRemove =
                            true;

                        this.identityImageError =
                            null;
                    },


                    async saveIdentityImage() {
                        if (
                            !this.imageSaveUrl
                            || (
                                !this.identityImageFile
                                && !this.identityImageRemove
                            )
                        ) {
                            return;
                        }

                        const formData =
                            new FormData();

                        if (this.identityImageFile) {
                            formData.append(
                                'image',
                                this.identityImageFile
                            );
                        }

                        formData.append(
                            'remove_image',
                            this.identityImageRemove
                                ? '1'
                                : '0'
                        );

                        const response =
                            await fetch(
                                this.imageSaveUrl,
                                {
                                    method:
                                        'POST',

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute(
                                                    'content'
                                                )
                                            ?? '',

                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                    },

                                    body:
                                        formData,
                                }
                            );

                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (!response.ok) {
                            const messages =
                                data?.errors
                                    ? Object.values(
                                        data.errors
                                    )
                                        .flat()
                                        .filter(
                                            Boolean
                                        )
                                    : [];

                            throw new Error(
                                messages.length
                                    ? messages.join(
                                        ' '
                                    )
                                    : (
                                        data?.message
                                        ?? 'Não foi possível atualizar a foto.'
                                    )
                            );
                        }

                        this.draft.image_url =
                            data
                                ?.character
                                ?.image_url
                            ?? null;

                        this.initial.image_url =
                            this.draft.image_url;
                    },


                    signed(value) {
                        const number =
                            parseInt(
                                value
                            );

                        if (
                            Number.isNaN(
                                number
                            )
                        ) {
                            return '—';
                        }

                        return number >= 0
                            ? `+${number}`
                            : `${number}`;
                    },

                    calculatedProficiency(level) {
                        const value =
                            Math.max(
                                1,
                                parseInt(
                                    level
                                )
                                || 1
                            );

                        if (value >= 17) return 6;
                        if (value >= 13) return 5;
                        if (value >= 9) return 4;
                        if (value >= 5) return 3;

                        return 2;
                    },

                    csrfToken() {
                        return document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            ?.getAttribute(
                                'content'
                            )
                            ?? '';
                    },

                    async jsonResponse(response) {
                        const data =
                            await response
                                .json()
                                .catch(
                                    () => ({})
                                );

                        if (!response.ok) {
                            const messages =
                                data
                                    && data.errors
                                        ? Object.values(
                                            data.errors
                                        )
                                            .flat()
                                            .filter(
                                                Boolean
                                            )
                                        : [];

                            throw new Error(
                                messages.length
                                    ? messages.join(
                                        ' '
                                    )
                                    : (
                                        data.message
                                        ?? 'Não foi possível concluir a operação.'
                                    )
                            );
                        }

                        return data;
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | MODAL
                    |--------------------------------------------------------------------------
                    */

                    openModal(tab = 'identity') {
                        this.resetDraft();

                        this.activeTab =
                            [
                                'identity',
                                'progression',
                                'rules',
                            ].includes(tab)
                                ? tab
                                : 'identity';

                        this.saveError =
                            null;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        this.proficiencyDrawerOpen =
                            false;

                        this.morquenRuleOpen =
                            false;

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;

                        this.open =
                            true;
                    },

                    closeModal() {
                        if (
                            this.saving
                            || this.levelingClassId !== null
                            || this.proficiencySaving
                        ) {
                            return;
                        }

                        if (
                            this.requiresPageRefresh
                        ) {
                            window.location.reload();
                            return;
                        }

                        this.open =
                            false;

                        this.activeTab =
                            'identity';

                        this.saveError =
                            null;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;

                        this.resetDraft();
                    },

                    setTab(tab) {
                        this.activeTab =
                            tab;

                        this.saveError =
                            null;
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | PROGRESSÃO
                    |--------------------------------------------------------------------------
                    */

                    requestLevelChange(
                        item,
                        mode
                    ) {
                        if (
                            this.levelingClassId !== null
                            || !item
                            || !item.id
                            || !['up', 'down'].includes(
                                mode
                            )
                        ) {
                            return;
                        }

                        const currentLevel =
                            parseInt(
                                item.level
                            )
                            || 1;

                        if (
                            mode === 'up'
                            && (
                                this.draft.level >= 20
                                || currentLevel >= 20
                            )
                        ) {
                            return;
                        }

                        if (
                            mode === 'down'
                            && currentLevel <= 1
                        ) {
                            return;
                        }

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        this.levelConfirmMode =
                            mode;

                        this.levelConfirmClass =
                            this.clone(
                                item
                            );

                        this.levelConfirmOpen =
                            true;
                    },


                    cancelLevelConfirmation() {
                        if (
                            this.levelingClassId !== null
                        ) {
                            return;
                        }

                        this.levelConfirmOpen =
                            false;

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;
                    },


                    async confirmLevelChange() {
                        if (
                            !this.levelConfirmOpen
                            || !this.levelConfirmClass
                            || !this.levelConfirmMode
                        ) {
                            return;
                        }

                        const item =
                            this.clone(
                                this.levelConfirmClass
                            );

                        const mode =
                            this.levelConfirmMode;

                        this.levelConfirmOpen =
                            false;

                        await this.changeLevel(
                            item,
                            mode
                        );

                        this.levelConfirmMode =
                            null;

                        this.levelConfirmClass =
                            null;
                    },


                    async changeLevel(
                        item,
                        mode
                    ) {
                        const isUp =
                            mode === 'up';

                        const url =
                            isUp
                                ? this.levelUpUrl
                                : this.levelDownUrl;

                        if (
                            this.levelingClassId !== null
                            || !url
                            || !item
                            || !item.id
                        ) {
                            return;
                        }

                        this.levelingClassId =
                            item.id;

                        this.levelingDirection =
                            mode;

                        this.progressionError =
                            null;

                        this.progressionMessage =
                            null;

                        try {
                            const response =
                                await fetch(
                                    url,
                                    {
                                        method:
                                            'PATCH',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                this.csrfToken(),

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify({
                                                class_id:
                                                    item.id,
                                            }),
                                    }
                                );

                            const data =
                                await this.jsonResponse(
                                    response
                                );

                            this.draft.level =
                                parseInt(
                                    data?.character?.level
                                )
                                || this.draft.level;

                            this.draft.classes =
                                Array.isArray(
                                    data?.classes
                                )
                                    ? data.classes.map(
                                        classItem => ({
                                            id:
                                                classItem.id,

                                            name:
                                                classItem.class
                                                ?? 'Classe',

                                            subclass:
                                                classItem.subclass
                                                ?? null,

                                            level:
                                                parseInt(
                                                    classItem.level
                                                )
                                                || 1,

                                            is_primary:
                                                !!classItem.is_primary,
                                        })
                                    )
                                    : this.draft.classes;

                            const currentProficiency =
                                parseInt(
                                    data
                                        ?.character
                                        ?.proficiency_bonus
                                );

                            if (
                                !Number.isNaN(
                                    currentProficiency
                                )
                            ) {
                                this.draft.proficiency.current =
                                    currentProficiency;
                            }

                            const calculated =
                                parseInt(
                                    data
                                        ?.progression
                                        ?.calculated_proficiency
                                );

                            this.draft.proficiency.calculated =
                                Number.isNaN(
                                    calculated
                                )
                                    ? this.calculatedProficiency(
                                        this.draft.level
                                    )
                                    : calculated;

                            this.draft.proficiency.custom_enabled =
                                !!data
                                    ?.progression
                                    ?.proficiency_custom_enabled;

                            this.draft
                                .settings
                                .progression
                                .proficiency_custom_enabled =
                                    this.draft
                                        .proficiency
                                        .custom_enabled;

                            this.customProficiencyValue =
                                this.draft.proficiency.current;

                            this.initial =
                                this.clone(
                                    this.draft
                                );

                            this.progressionMessage =
                                data?.message
                                ?? (
                                    isUp
                                        ? 'Nível aumentado.'
                                        : 'Nível reduzido.'
                                );

                            this.requiresPageRefresh =
                                true;

                        } catch (error) {
                            console.error(
                                isUp
                                    ? 'Erro ao subir de nível:'
                                    : 'Erro ao reduzir nível:',
                                error
                            );

                            this.progressionError =
                                error?.message
                                ?? (
                                    isUp
                                        ? 'Não foi possível subir de nível.'
                                        : 'Não foi possível reduzir o nível.'
                                );

                        } finally {
                            this.levelingClassId =
                                null;

                            this.levelingDirection =
                                null;
                        }
                    },


                    async saveCustomProficiency() {
                        if (
                            this.proficiencySaving
                            || !this.proficiencyUrl
                        ) {
                            return;
                        }

                        const value =
                            parseInt(
                                this.customProficiencyValue
                            );

                        if (
                            Number.isNaN(
                                value
                            )
                        ) {
                            this.proficiencyError =
                                'Informe um número inteiro para a proficiência.';

                            return;
                        }

                        await this.persistProficiency(
                            true,
                            value
                        );
                    },


                    async restoreAutomaticProficiency() {
                        if (
                            this.proficiencySaving
                            || !this.proficiencyUrl
                        ) {
                            return;
                        }

                        await this.persistProficiency(
                            false,
                            null
                        );
                    },


                    async persistProficiency(
                        enabled,
                        value
                    ) {
                        this.proficiencySaving =
                            true;

                        this.proficiencyError =
                            null;

                        this.proficiencyMessage =
                            null;

                        try {
                            const response =
                                await fetch(
                                    this.proficiencyUrl,
                                    {
                                        method:
                                            'PATCH',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                this.csrfToken(),

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify({
                                                enabled:
                                                    !!enabled,

                                                value:
                                                    enabled
                                                        ? value
                                                        : null,
                                            }),
                                    }
                                );

                            const data =
                                await this.jsonResponse(
                                    response
                                );

                            const current =
                                parseInt(
                                    data
                                        ?.character
                                        ?.proficiency_bonus
                                );

                            if (
                                !Number.isNaN(
                                    current
                                )
                            ) {
                                this.draft.proficiency.current =
                                    current;
                            }

                            const calculated =
                                parseInt(
                                    data
                                        ?.progression
                                        ?.calculated_proficiency
                                );

                            if (
                                !Number.isNaN(
                                    calculated
                                )
                            ) {
                                this.draft.proficiency.calculated =
                                    calculated;
                            }

                            this.draft.proficiency.custom_enabled =
                                !!data
                                    ?.progression
                                    ?.proficiency_custom_enabled;

                            this.draft
                                .settings
                                .progression
                                .proficiency_custom_enabled =
                                    this.draft
                                        .proficiency
                                        .custom_enabled;

                            this.customProficiencyValue =
                                this.draft.proficiency.current;

                            this.initial =
                                this.clone(
                                    this.draft
                                );

                            this.proficiencyMessage =
                                data?.message
                                ?? (
                                    enabled
                                        ? 'Proficiência customizada aplicada.'
                                        : 'Proficiência automática restaurada.'
                                );

                            this.requiresPageRefresh =
                                true;

                        } catch (error) {
                            console.error(
                                'Erro ao atualizar proficiência:',
                                error
                            );

                            this.proficiencyError =
                                error?.message
                                ?? 'Não foi possível atualizar a proficiência.';

                        } finally {
                            this.proficiencySaving =
                                false;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | SALVAR
                    |--------------------------------------------------------------------------
                    */

                    async save() {
                        if (
                            this.saving
                            || !this.saveUrl
                        ) {
                            return;
                        }

                        const name =
                            String(
                                this.draft.name
                                ?? ''
                            ).trim();

                        if (!name) {
                            this.saveError =
                                'Informe o nome do personagem.';

                            this.activeTab =
                                'identity';

                            return;
                        }

                        this.saving =
                            true;

                        this.saveError =
                            null;

                        const payload = {
                            name,

                            background:
                                String(
                                    this.draft.background
                                    ?? ''
                                ).trim(),

                            species:
                                String(
                                    this.draft.species
                                    ?? ''
                                ).trim(),

                            sheet_settings:
                                this.draft.settings,
                        };

                        try {
                            const response =
                                await fetch(
                                    this.saveUrl,
                                    {
                                        method:
                                            'PATCH',

                                        headers: {
                                            'Content-Type':
                                                'application/json',

                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                document
                                                    .querySelector(
                                                        'meta[name="csrf-token"]'
                                                    )
                                                    ?.getAttribute(
                                                        'content'
                                                    )
                                                ?? '',

                                            'X-Requested-With':
                                                'XMLHttpRequest',
                                        },

                                        body:
                                            JSON.stringify(
                                                payload
                                            ),
                                    }
                                );

                            const data =
                                await response
                                    .json()
                                    .catch(
                                        () => ({})
                                    );

                            if (!response.ok) {
                                const messages =
                                    data
                                        && data.errors
                                            ? Object.values(
                                                data.errors
                                            )
                                                .flat()
                                                .filter(
                                                    Boolean
                                                )
                                            : [];

                                throw new Error(
                                    messages.length
                                        ? messages.join(
                                            ' '
                                        )
                                        : (
                                            data.message
                                            ?? 'Não foi possível salvar.'
                                        )
                                );
                            }

                            /*
                             * A identidade textual já foi salva.
                             * Se houver troca/remoção de foto, concluímos o
                             * multipart antes do reload.
                             */
                            await this.saveIdentityImage();


                            /*
                             * Nesta etapa usamos reload propositalmente.
                             *
                             * Isso mantém Hero, Defesas, XP, foto e futuras
                             * regras totalmente sincronizados com o servidor
                             * sem duplicar estado entre vários componentes.
                             */
                            window.location.reload();

                        } catch (error) {
                            console.error(
                                'Erro ao salvar customização:',
                                error
                            );

                            this.saveError =
                                error
                                && error.message
                                    ? error.message
                                    : 'Não foi possível salvar.';

                        } finally {
                            this.saving =
                                false;
                        }
                    },
                })
            );
        }
    );
</script>


{{-- ================================================================
     ROOT
================================================================= --}}

<div
    x-data="characterCustomizationModal"

    data-initial="{!! $encodedInitialPayload !!}"

    data-save-url="{{ route(
        'characters.customization.update',
        $character
    ) }}"

    data-image-save-url="{{ route(
        'characters.customization.image',
        $character
    ) }}"

    data-level-up-url="{{ route(
        'characters.progression.level-up',
        $character
    ) }}"

    data-level-down-url="{{ route(
        'characters.progression.level-down',
        $character
    ) }}"

    data-proficiency-url="{{ route(
        'characters.progression.proficiency',
        $character
    ) }}"

    @open-character-customization.window="
        openModal(
            $event.detail && $event.detail.tab
                ? $event.detail.tab
                : 'identity'
        )
    "

    @keydown.escape.window="
        if (levelConfirmOpen) {
            cancelLevelConfirmation()
        } else if (open) {
            closeModal()
        }
    "
>

    <template x-teleport="body">

        <div
            x-show="open"
            x-cloak

            class="
                fixed
                inset-0
                z-[190]

                flex
                items-center
                justify-center

                p-3
                sm:p-6
            "

            role="dialog"
            aria-modal="true"
            aria-label="Configurações do personagem"
        >

            {{-- BACKDROP --}}

            <div
                class="
                    absolute
                    inset-0

                    bg-[#2a1712]/60
                    backdrop-blur-[2px]
                "

                @click="closeModal()"
            ></div>


            {{-- PAINEL --}}

            <div
                @click.stop

                x-transition:enter="
                    transition
                    ease-out
                    duration-150
                "

                x-transition:enter-start="
                    opacity-0
                    translate-y-1
                    scale-[.99]
                "

                x-transition:enter-end="
                    opacity-100
                    translate-y-0
                    scale-100
                "

                x-transition:leave="
                    transition
                    ease-in
                    duration-100
                "

                x-transition:leave-start="
                    opacity-100
                    translate-y-0
                    scale-100
                "

                x-transition:leave-end="
                    opacity-0
                    translate-y-1
                    scale-[.99]
                "

                class="
                    relative
                    z-10

                    flex
                    max-h-[90vh]
                    w-full
                    max-w-[820px]
                    flex-col

                    overflow-hidden

                    rounded-2xl
                    border
                    border-[#b08c62]/70

                    bg-[#fbf8f1]

                    shadow-[0_28px_80px_rgba(42,23,18,.30)]
                "
            >

                {{-- ====================================================
                     HEADER
                ===================================================== --}}

                <header
                    class="
                        shrink-0

                        border-b
                        border-[#a0774d]/30

                        bg-[#eadbc8]

                        px-4
                        pb-3
                        pt-4

                        sm:px-5
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                        "
                    >

                        <div class="min-w-0">

                            <p
                                class="
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]

                                    text-[#8c6239]
                                "
                            >
                                Ficha do personagem
                            </p>

                            <h2
                                class="
                                    mt-1

                                    truncate

                                    font-serif
                                    text-[23px]
                                    font-black
                                    leading-none

                                    text-[#53150f]
                                "
                            >
                                Configurações
                            </h2>

                            <p
                                class="
                                    mt-1.5

                                    text-[12px]
                                    leading-relaxed

                                    text-[#6f4c36]
                                "
                            >
                                Identidade, progressão e regras opcionais da ficha.
                            </p>

                        </div>


                        <button
                            type="button"

                            @click="closeModal()"

                            :disabled="
                                saving
                                || levelingClassId !== null
                                || proficiencySaving
                            "

                            class="
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center

                                rounded-lg

                                text-[#8c6239]

                                transition

                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]

                                disabled:opacity-40
                            "

                            title="Fechar"
                            aria-label="Fechar configurações"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            >
                                <path d="M6 6l12 12M18 6 6 18" />
                            </svg>
                        </button>

                    </div>


                    {{-- ABAS --}}

                    <nav
                        class="
                            mt-4

                            grid
                            grid-cols-3

                            overflow-hidden

                            rounded-lg
                            border
                            border-[#a0774d]/28

                            bg-[#f7eee2]
                        "
                        aria-label="Seções das configurações"
                    >

                        <button
                            type="button"

                            @click="setTab('identity')"

                            class="
                                border-r
                                border-[#a0774d]/22

                                px-2
                                py-3

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'identity'
                                    ? 'bg-[#fffdf8] text-[#53150f]'
                                    : 'text-[#8c6239] hover:bg-[#fffdf8]/50'
                            "
                        >
                            Identidade
                        </button>


                        <button
                            type="button"

                            @click="setTab('progression')"

                            class="
                                border-r
                                border-[#a0774d]/22

                                px-2
                                py-3

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'progression'
                                    ? 'bg-[#fffdf8] text-[#53150f]'
                                    : 'text-[#8c6239] hover:bg-[#fffdf8]/50'
                            "
                        >
                            Progressão
                        </button>


                        <button
                            type="button"

                            @click="setTab('rules')"

                            class="
                                px-2
                                py-3

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.12em]

                                transition
                            "

                            :class="
                                activeTab === 'rules'
                                    ? 'bg-[#fffdf8] text-[#53150f]'
                                    : 'text-[#8c6239] hover:bg-[#fffdf8]/50'
                            "
                        >
                            Regras Opcionais
                        </button>

                    </nav>

                </header>


                {{-- ====================================================
                     BODY
                ===================================================== --}}

                <div
                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto
                        [scrollbar-width:none]
                        [&::-webkit-scrollbar]:hidden

                        p-4
                        sm:p-5
                    "
                >

                    {{-- =================================================
                         IDENTIDADE
                    ================================================== --}}

                    <section
                        x-show="
                            activeTab === 'identity'
                        "
                    >

                        <div
                            class="
                                mb-4

                                border-b
                                border-[#b08c62]/28

                                pb-3
                            "
                        >
                            <h3
                                class="
                                    font-serif
                                    text-[19px]
                                    font-black

                                    text-[#53150f]
                                "
                            >
                                Identidade do personagem
                            </h3>

                            <p
                                class="
                                    mt-1

                                    text-[11px]
                                    leading-relaxed

                                    text-[#7d604d]
                                "
                            >
                                Estes dados aparecem diretamente no cabeçalho da ficha.
                            </p>
                        </div>


                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-4

                                sm:grid-cols-[150px_minmax(0,1fr)]
                            "
                        >

                            {{-- FOTO --}}

                            <div>

                                <span
                                    class="
                                        mb-1.5
                                        block

                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.14em]

                                        text-[#8c6239]
                                    "
                                >
                                    Foto
                                </span>


                                <label
                                    class="
                                        group
                                        relative

                                        flex
                                        aspect-[4/5]
                                        w-full

                                        cursor-pointer
                                        items-center
                                        justify-center

                                        overflow-hidden

                                        rounded-xl
                                        border
                                        border-dashed
                                        border-[#cdbb9f]

                                        bg-[#efe9dc]/45

                                        shadow-[inset_0_1px_0_rgba(255,255,255,.72)]

                                        transition

                                        hover:border-[#a0774d]
                                        hover:bg-[#efe9dc]/70
                                    "
                                >

                                    <template
                                        x-if="
                                            identityImagePreview
                                        "
                                    >
                                        <img
                                            :src="
                                                identityImagePreview
                                            "

                                            alt="Prévia da foto do personagem"

                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                                object-top
                                            "
                                        >
                                    </template>


                                    <template
                                        x-if="
                                            !identityImagePreview
                                        "
                                    >
                                        <div
                                            class="
                                                px-3
                                                text-center
                                            "
                                        >
                                            <svg
                                                class="
                                                    mx-auto
                                                    h-8
                                                    w-8

                                                    text-[#8c6239]/45
                                                "

                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 5h16v14H4zM8 13l2.5-2.5L14 14l2-2 4 4M8 9h.01"
                                                />
                                            </svg>

                                            <span
                                                class="
                                                    mt-2
                                                    block

                                                    text-[9px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.10em]

                                                    text-[#8c6239]
                                                "
                                            >
                                                Adicionar foto
                                            </span>

                                            <span
                                                class="
                                                    mt-1
                                                    block

                                                    text-[8px]

                                                    text-[#8c6239]/65
                                                "
                                            >
                                                JPG, PNG ou WEBP
                                            </span>
                                        </div>
                                    </template>


                                    <div
                                        class="
                                            pointer-events-none
                                            absolute
                                            inset-x-2
                                            bottom-2

                                            translate-y-2
                                            rounded-lg

                                            bg-[#2b1d17]/72

                                            px-2
                                            py-1.5

                                            text-center
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.09em]

                                            text-white

                                            opacity-0

                                            backdrop-blur-sm

                                            transition

                                            group-hover:translate-y-0
                                            group-hover:opacity-100
                                        "
                                    >
                                        Trocar foto
                                    </div>


                                    <input
                                        type="file"

                                        accept="image/jpeg,image/png,image/webp"

                                        @click="
                                            $event.target.value =
                                                null
                                        "

                                        @change="
                                            handleIdentityImage(
                                                $event
                                            )
                                        "

                                        class="hidden"
                                    >

                                </label>


                                <button
                                    x-show="
                                        identityImagePreview
                                    "
                                    x-cloak

                                    type="button"

                                    @click="
                                        removeIdentityImage()
                                    "

                                    class="
                                        mt-2
                                        w-full

                                        rounded-lg

                                        px-2
                                        py-1.5

                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.08em]

                                        text-red-700

                                        transition

                                        hover:bg-red-50
                                    "
                                >
                                    Remover foto
                                </button>


                                <p
                                    x-show="
                                        identityImageError
                                    "
                                    x-cloak

                                    class="
                                        mt-2

                                        text-[9px]
                                        font-bold
                                        leading-relaxed

                                        text-red-700
                                    "

                                    x-text="
                                        identityImageError
                                    "
                                ></p>

                            </div>


                            {{-- DADOS DE IDENTIDADE --}}

                            <div
                                class="
                                    grid
                                    min-w-0
                                    grid-cols-1
                                    gap-3

                                    sm:grid-cols-2
                                "
                            >

                                {{-- NOME --}}

                                <label
                                    class="
                                        block
                                        min-w-0

                                        sm:col-span-2
                                    "
                                >
                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Nome
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.name"

                                        maxlength="120"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[15px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >
                                </label>


                                {{-- ANTECEDENTE --}}

                                <label class="block min-w-0">

                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Antecedente
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.background"

                                        maxlength="120"

                                        placeholder="—"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[13px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >

                                </label>


                                {{-- ESPÉCIE --}}

                                <label class="block min-w-0">

                                    <span
                                        class="
                                            mb-1.5
                                            block

                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Espécie
                                    </span>

                                    <input
                                        type="text"

                                        x-model="draft.species"

                                        maxlength="120"

                                        placeholder="—"

                                        class="
                                            h-11
                                            w-full

                                            rounded-lg
                                            border
                                            border-[#cdbb9f]

                                            bg-[#fffdf8]

                                            px-3

                                            font-serif
                                            text-[13px]
                                            font-bold

                                            text-[#53150f]

                                            outline-none

                                            transition

                                            focus:border-[#8c6239]
                                            focus:ring-2
                                            focus:ring-[#8c6239]/10
                                        "
                                    >

                                </label>

                            </div>

                        </div>


                    </section>


                    {{-- =================================================
                         PROGRESSÃO
                    ================================================== --}}

                    <section
                        x-show="
                            activeTab === 'progression'
                        "
                    >

                        {{-- CABEÇALHO --}}

                        <div
                            class="
                                flex
                                items-end
                                justify-between
                                gap-4

                                border-b
                                border-[#b08c62]/28

                                pb-3
                            "
                        >

                            <div>
                                <h3
                                    class="
                                        font-serif
                                        text-[19px]
                                        font-black

                                        text-[#53150f]
                                    "
                                >
                                    Progressão
                                </h3>

                                <p
                                    class="
                                        mt-1

                                        text-[11px]
                                        leading-relaxed

                                        text-[#7d604d]
                                    "
                                >
                                    Escolha qual classe recebe o próximo nível.
                                </p>
                            </div>


                            <div
                                class="
                                    shrink-0

                                    rounded-lg
                                    border
                                    border-[#cdbb9f]

                                    bg-[#fffdf8]

                                    px-3
                                    py-2.5

                                    text-center
                                "
                            >
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.13em]

                                        text-[#8c6239]
                                    "
                                >
                                    Nível total
                                </p>

                                <p
                                    class="
                                        mt-0.5

                                        font-serif
                                        text-[22px]
                                        font-black
                                        leading-none

                                        text-[#6b1d14]
                                    "

                                    x-text="
                                        draft.level
                                    "
                                ></p>
                            </div>

                        </div>


                        {{-- =================================================
                             ESCOLHER CLASSE
                        ================================================== --}}

                        <div class="mt-4">

                            <div
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-3
                                "
                            >
                                <div>
                                    <p
                                        class="
                                            text-[10px]
                                            font-black
                                            uppercase
                                            tracking-[0.14em]

                                            text-[#8c6239]
                                        "
                                    >
                                        Próximo nível
                                    </p>

                                    <p
                                        class="
                                            mt-0.5

                                            text-[11px]
                                            text-[#7d604d]
                                        "
                                    >
                                        O aumento é aplicado imediatamente à classe escolhida.
                                    </p>
                                </div>

                                <span
                                    x-show="
                                        draft.level >= 20
                                    "

                                    class="
                                        shrink-0

                                        rounded-full

                                        bg-[#eadbc8]

                                        px-2.5
                                        py-1

                                        text-[10px]
                                        font-black
                                        uppercase
                                        tracking-[0.10em]

                                        text-[#6b1d14]
                                    "
                                >
                                    Nível máximo
                                </span>
                            </div>


                            <div
                                class="
                                    mt-2.5

                                    grid
                                    grid-cols-1
                                    gap-2

                                    sm:grid-cols-2
                                "
                            >

                                <template
                                    x-for="
                                        item in draft.classes
                                    "
                                    :key="
                                        item.id
                                        || item.name
                                    "
                                >
                                    <article
                                        class="
                                            relative

                                            flex
                                            min-w-0
                                            items-center
                                            justify-between
                                            gap-3

                                            overflow-hidden

                                            rounded-xl
                                            border
                                            border-[#d8c7ab]/75

                                            bg-[#fffdf8]

                                            px-3
                                            py-3

                                            shadow-[inset_0_1px_0_rgba(255,255,255,.72)]
                                        "
                                    >

                                        <div class="min-w-0">

                                            <div
                                                class="
                                                    flex
                                                    min-w-0
                                                    items-center
                                                    gap-2
                                                "
                                            >
                                                <p
                                                    class="
                                                        truncate

                                                        font-serif
                                                        text-[14px]
                                                        font-black

                                                        text-[#53150f]
                                                    "

                                                    x-text="
                                                        item.name
                                                    "
                                                ></p>

                                                <span
                                                    x-show="
                                                        item.is_primary
                                                    "

                                                    class="
                                                        shrink-0

                                                        rounded-full

                                                        bg-[#eadbc8]

                                                        px-2
                                                        py-0.5

                                                        text-[9px]
                                                        font-black
                                                        uppercase
                                                        tracking-[0.10em]

                                                        text-[#6f472f]
                                                    "
                                                >
                                                    Principal
                                                </span>
                                            </div>


                                            <p
                                                x-show="
                                                    item.subclass
                                                "

                                                class="
                                                    mt-0.5

                                                    truncate

                                                    text-[11px]
                                                    font-semibold

                                                    text-[#8c6239]
                                                "

                                                x-text="
                                                    item.subclass
                                                "
                                            ></p>


                                            <p
                                                class="
                                                    mt-1.5

                                                    text-[11px]
                                                    font-semibold

                                                    text-[#7d604d]
                                                "
                                            >
                                                Nível da classe:

                                                <strong
                                                    class="
                                                        ml-1

                                                        font-serif
                                                        text-[13px]

                                                        text-[#53150f]
                                                    "

                                                    x-text="
                                                        item.level
                                                    "
                                                ></strong>
                                            </p>

                                        </div>


                                        <div
                                            class="
                                                flex
                                                shrink-0
                                                items-center
                                                gap-1.5
                                            "
                                        >

                                            {{-- VOLTAR -1 --}}

                                            <button
                                                type="button"

                                                @click="
                                                    requestLevelChange(
                                                        item,
                                                        'down'
                                                    )
                                                "

                                                :disabled="
                                                    item.level <= 1
                                                    || levelingClassId !== null
                                                "

                                                class="
                                                    min-w-[70px]

                                                    rounded-lg
                                                    border
                                                    border-[#cdbb9f]

                                                    bg-[#f4f1e8]

                                                    px-2.5
                                                    py-2.5

                                                    text-[10px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.08em]

                                                    text-[#6b1d14]

                                                    transition

                                                    hover:border-[#a0774d]/55
                                                    hover:bg-[#eadbc8]

                                                    disabled:cursor-not-allowed
                                                    disabled:opacity-40
                                                "
                                            >
                                                <span
                                                    x-show="
                                                        !(
                                                            levelingClassId
                                                            === item.id
                                                            && levelingDirection
                                                            === 'down'
                                                        )
                                                    "
                                                >
                                                    Voltar -1
                                                </span>

                                                <span
                                                    x-show="
                                                        levelingClassId
                                                        === item.id
                                                        && levelingDirection
                                                        === 'down'
                                                    "
                                                    x-cloak
                                                >
                                                    Salvando...
                                                </span>
                                            </button>


                                            {{-- SUBIR +1 --}}

                                            <button
                                                type="button"

                                                @click="
                                                    requestLevelChange(
                                                        item,
                                                        'up'
                                                    )
                                                "

                                                :disabled="
                                                    draft.level >= 20
                                                    || item.level >= 20
                                                    || levelingClassId !== null
                                                "

                                                class="
                                                    min-w-[68px]

                                                    rounded-lg
                                                    border
                                                    border-[#6b1d14]/30

                                                    bg-[#6b1d14]

                                                    px-2.5
                                                    py-2.5

                                                    text-[10px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.08em]

                                                    text-[#fffaf2]

                                                    transition

                                                    hover:bg-[#53150f]

                                                    disabled:cursor-not-allowed
                                                    disabled:border-[#cdbb9f]/60
                                                    disabled:bg-[#eadbc8]/70
                                                    disabled:text-[#8c6239]/55
                                                "
                                            >
                                                <span
                                                    x-show="
                                                        !(
                                                            levelingClassId
                                                            === item.id
                                                            && levelingDirection
                                                            === 'up'
                                                        )
                                                    "
                                                >
                                                    Subir +1
                                                </span>

                                                <span
                                                    x-show="
                                                        levelingClassId
                                                        === item.id
                                                        && levelingDirection
                                                        === 'up'
                                                    "
                                                    x-cloak
                                                >
                                                    Salvando...
                                                </span>
                                            </button>

                                        </div>

                                    </article>
                                </template>


                                <div
                                    x-show="
                                        !draft.classes.length
                                    "

                                    class="
                                        rounded-xl
                                        border
                                        border-dashed
                                        border-[#cdbb9f]/75

                                        px-4
                                        py-5

                                        text-center
                                        text-[11px]

                                        text-[#8c6239]

                                        sm:col-span-2
                                    "
                                >
                                    Nenhuma classe encontrada.
                                </div>

                            </div>


                            {{-- FEEDBACK LEVEL-UP --}}

                            <div
                                x-show="
                                    progressionError
                                "

                                class="
                                    mt-2.5

                                    rounded-lg
                                    border
                                    border-red-200

                                    bg-red-50

                                    px-3
                                    py-2.5

                                    text-[11px]
                                    font-bold

                                    text-red-700
                                "

                                x-text="
                                    progressionError
                                "
                            ></div>


                            <div
                                x-show="
                                    progressionMessage
                                "

                                class="
                                    mt-2.5

                                    rounded-lg
                                    border
                                    border-emerald-200

                                    bg-emerald-50

                                    px-3
                                    py-2.5

                                    text-[11px]
                                    font-bold

                                    text-emerald-800
                                "

                                x-text="
                                    progressionMessage
                                "
                            ></div>

                        </div>


                        {{-- =================================================
                             PROFICIÊNCIA CUSTOM — GAVETA
                        ================================================== --}}

                        <div
                            class="
                                mt-4

                                overflow-hidden

                                rounded-xl
                                border
                                border-[#cdbb9f]/70

                                bg-[#f7eee2]/45
                            "
                        >

                            {{-- CABEÇALHO DA GAVETA --}}

                            <button
                                type="button"

                                @click="
                                    proficiencyDrawerOpen =
                                        !proficiencyDrawerOpen
                                "

                                class="
                                    flex
                                    w-full
                                    items-center
                                    justify-between
                                    gap-3

                                    px-3.5
                                    py-3

                                    text-left

                                    transition

                                    hover:bg-[#eadbc8]/38
                                "
                            >

                                <span class="min-w-0">

                                    <span
                                        class="
                                            flex
                                            items-center
                                            gap-2
                                        "
                                    >
                                        <span
                                            class="
                                                font-serif
                                                text-[13px]
                                                font-black

                                                text-[#53150f]
                                            "
                                        >
                                            Proficiência Custom
                                        </span>

                                        <span
                                            class="
                                                rounded-full

                                                px-2
                                                py-0.5

                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-[0.10em]
                                            "

                                            :class="
                                                draft.proficiency.custom_enabled
                                                    ? 'bg-[#6b1d14] text-[#fffaf2]'
                                                    : 'bg-[#eadbc8] text-[#8c6239]'
                                            "

                                            x-text="
                                                draft.proficiency.custom_enabled
                                                    ? 'Custom'
                                                    : 'Automática'
                                            "
                                        ></span>
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block

                                            text-[11px]

                                            text-[#7d604d]
                                        "
                                    >
                                        Valor atual:
                                        <strong
                                            class="
                                                font-serif
                                                text-[12px]

                                                text-[#53150f]
                                            "

                                            x-text="
                                                signed(
                                                    draft.proficiency.current
                                                )
                                            "
                                        ></strong>

                                        <span
                                            x-show="
                                                draft.proficiency.custom_enabled
                                            "
                                        >
                                            · automático seria
                                            <strong
                                                class="
                                                    font-serif
                                                    text-[12px]

                                                    text-[#8c6239]
                                                "

                                                x-text="
                                                    signed(
                                                        draft.proficiency.calculated
                                                    )
                                                "
                                            ></strong>
                                        </span>
                                    </span>

                                </span>


                                <svg
                                    class="
                                        h-4
                                        w-4
                                        shrink-0

                                        text-[#8c6239]

                                        transition-transform
                                    "

                                    :class="
                                        proficiencyDrawerOpen
                                            ? 'rotate-180'
                                            : ''
                                    "

                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m6 9 6 6 6-6" />
                                </svg>

                            </button>


                            {{-- CONTEÚDO DA GAVETA --}}

                            <div
                                x-show="
                                    proficiencyDrawerOpen
                                "
                                x-transition

                                class="
                                    border-t
                                    border-[#cdbb9f]/55

                                    bg-[#fffdf8]/70
                                "
                            >

                                <div class="p-3.5">

                                    {{-- AVISO --}}

                                    <div
                                        class="
                                            rounded-lg
                                            border
                                            border-amber-300/75

                                            bg-amber-50

                                            px-3
                                            py-3
                                        "
                                    >
                                        <p
                                            class="
                                                text-[10px]
                                                font-black
                                                uppercase
                                                tracking-[0.12em]

                                                text-amber-900
                                            "
                                        >
                                            Atenção
                                        </p>

                                        <p
                                            class="
                                                mt-1

                                                text-[11px]
                                                leading-relaxed

                                                text-amber-900/85
                                            "
                                        >
                                            Este valor substitui a proficiência calculada pelo nível. Perícias, salvaguardas, ataques, CDs e qualquer cálculo que use proficiência passarão a usar o valor escolhido.
                                        </p>
                                    </div>


                                    {{-- CONTROLES --}}

                                    <div
                                        class="
                                            mt-3

                                            grid
                                            grid-cols-1
                                            gap-3

                                            sm:grid-cols-[minmax(0,1fr)_auto]
                                            sm:items-end
                                        "
                                    >

                                        <label class="block min-w-0">

                                            <span
                                                class="
                                                    mb-1.5
                                                    block

                                                    text-[10px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.13em]

                                                    text-[#8c6239]
                                                "
                                            >
                                                Valor da proficiência
                                            </span>

                                            <div
                                                class="
                                                    flex
                                                    h-11
                                                    items-center

                                                    overflow-hidden

                                                    rounded-lg
                                                    border
                                                    border-[#cdbb9f]

                                                    bg-[#fffdf8]
                                                "
                                            >
                                                <span
                                                    class="
                                                        flex
                                                        h-full
                                                        items-center

                                                        border-r
                                                        border-[#d8c7ab]/65

                                                        bg-[#f4f1e8]

                                                        px-3

                                                        font-serif
                                                        text-[15px]
                                                        font-black

                                                        text-[#8c6239]
                                                    "
                                                >
                                                    ±
                                                </span>

                                                <input
                                                    type="number"

                                                    step="1"

                                                    x-model.number="
                                                        customProficiencyValue
                                                    "

                                                    class="
                                                        h-full
                                                        min-w-0
                                                        flex-1

                                                        border-0
                                                        bg-transparent

                                                        px-3

                                                        font-serif
                                                        text-[16px]
                                                        font-black

                                                        text-[#53150f]

                                                        outline-none
                                                        ring-0
                                                    "
                                                >
                                            </div>

                                        </label>


                                        <button
                                            type="button"

                                            @click="
                                                saveCustomProficiency()
                                            "

                                            :disabled="
                                                proficiencySaving
                                            "

                                            class="
                                                h-11

                                                rounded-lg

                                                bg-[#6b1d14]

                                                px-4

                                                text-[10px]
                                                font-black
                                                uppercase
                                                tracking-[0.11em]

                                                text-[#fffaf2]

                                                transition

                                                hover:bg-[#53150f]

                                                disabled:cursor-wait
                                                disabled:opacity-55
                                            "
                                        >
                                            <span
                                                x-show="
                                                    !proficiencySaving
                                                "
                                            >
                                                Usar valor custom
                                            </span>

                                            <span
                                                x-show="
                                                    proficiencySaving
                                                "
                                                x-cloak
                                            >
                                                Salvando...
                                            </span>
                                        </button>

                                    </div>


                                    {{-- VOLTAR AO AUTOMÁTICO --}}

                                    <div
                                        class="
                                            mt-3

                                            flex
                                            flex-wrap
                                            items-center
                                            justify-between
                                            gap-2

                                            border-t
                                            border-[#d8c7ab]/50

                                            pt-3
                                        "
                                    >

                                        <p
                                            class="
                                                text-[11px]

                                                text-[#7d604d]
                                            "
                                        >
                                            Pelo nível
                                            <strong
                                                class="
                                                    font-serif
                                                    text-[12px]

                                                    text-[#53150f]
                                                "

                                                x-text="
                                                    draft.level
                                                "
                                            ></strong>,
                                            o valor automático é

                                            <strong
                                                class="
                                                    font-serif
                                                    text-[12px]

                                                    text-[#6b1d14]
                                                "

                                                x-text="
                                                    signed(
                                                        draft.proficiency.calculated
                                                    )
                                                "
                                            ></strong>.
                                        </p>


                                        <button
                                            type="button"

                                            @click="
                                                restoreAutomaticProficiency()
                                            "

                                            :disabled="
                                                proficiencySaving
                                                || !draft.proficiency.custom_enabled
                                            "

                                            class="
                                                rounded-lg
                                                border
                                                border-[#cdbb9f]

                                                bg-[#f4f1e8]

                                                px-3
                                                py-2.5

                                                text-[10px]
                                                font-black
                                                uppercase
                                                tracking-[0.10em]

                                                text-[#6b1d14]

                                                transition

                                                hover:bg-[#eadbc8]

                                                disabled:cursor-not-allowed
                                                disabled:opacity-40
                                            "
                                        >
                                            Voltar ao automático
                                        </button>

                                    </div>


                                    {{-- FEEDBACK --}}

                                    <div
                                        x-show="
                                            proficiencyError
                                        "

                                        class="
                                            mt-3

                                            rounded-lg
                                            border
                                            border-red-200

                                            bg-red-50

                                            px-3
                                            py-2.5

                                            text-[11px]
                                            font-bold

                                            text-red-700
                                        "

                                        x-text="
                                            proficiencyError
                                        "
                                    ></div>


                                    <div
                                        x-show="
                                            proficiencyMessage
                                        "

                                        class="
                                            mt-3

                                            rounded-lg
                                            border
                                            border-emerald-200

                                            bg-emerald-50

                                            px-3
                                            py-2.5

                                            text-[11px]
                                            font-bold

                                            text-emerald-800
                                        "

                                        x-text="
                                            proficiencyMessage
                                        "
                                    ></div>

                                </div>

                            </div>

                        </div>


                        {{-- NOTA DE ESCOPO --}}

                        <p
                            class="
                                mt-3

                                text-[10px]
                                leading-relaxed

                                text-[#8c6239]/75
                            "
                        >
                            Subir ou voltar um nível altera imediatamente a classe escolhida, o nível total e, quando estiver automática, a proficiência. HP, características, recursos e escolhas específicas da classe não são adicionados nem removidos automaticamente.
                        </p>

                    </section>


                    {{-- =================================================
                         REGRAS
                    ================================================== --}}

                    <section
                        x-show="
                            activeTab === 'rules'
                        "
                    >
                        {{-- =================================================
                             REGRAS OPCIONAIS — VERSÃO LIMPA
                        ================================================== --}}

                        <div
                            class="
                                mb-4
                                border-b
                                border-[#b08c62]/28
                                pb-3
                            "
                        >
                            <h3
                                class="
                                    font-serif
                                    text-[19px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Regras Opcionais
                            </h3>

                            <p
                                class="
                                    mt-1
                                    text-[11px]
                                    leading-relaxed
                                    text-[#7d604d]
                                "
                            >
                                Personalize o que aparece na ficha e ative regras especiais do personagem.
                            </p>
                        </div>


                        <div class="space-y-2">

                            {{-- DEFESAS --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Exibir Defesas
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Mostra ou esconde completamente Resistências, Imunidades e Vulnerabilidades.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.display.show_defenses
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.display.show_defenses
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.display.show_defenses
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- EXPERIÊNCIA --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Exibir experiência
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Mostra a experiência junto ao nível quando esse dado estiver disponível.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.display.show_experience
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.display.show_experience
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.display.show_experience
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- EXAUSTÃO --}}
                            <label
                                class="
                                    flex
                                    cursor-pointer
                                    items-center
                                    justify-between
                                    gap-4

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    px-3.5
                                    py-3

                                    transition
                                    hover:bg-[#f7eee2]/55
                                "
                            >
                                <span class="min-w-0">
                                    <span
                                        class="
                                            block
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Exaustão
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#7d604d]
                                        "
                                    >
                                        Ativa níveis de Exaustão e suas penalidades na ficha.
                                    </span>
                                </span>

                                <span
                                    class="
                                        relative
                                        h-5
                                        w-9
                                        shrink-0
                                        rounded-full
                                        transition
                                    "

                                    :class="
                                        draft.settings.optional_rules.exhaustion
                                            ? 'bg-[#6b1d14]'
                                            : 'bg-[#d8c7ab]'
                                    "
                                >
                                    <input
                                        type="checkbox"

                                        x-model="
                                            draft.settings.optional_rules.exhaustion
                                        "

                                        class="sr-only"
                                    >

                                    <span
                                        class="
                                            absolute
                                            top-0.5

                                            h-4
                                            w-4

                                            rounded-full
                                            bg-[#fffdf8]
                                            shadow-sm

                                            transition
                                        "

                                        :class="
                                            draft.settings.optional_rules.exhaustion
                                                ? 'translate-x-[18px]'
                                                : 'translate-x-0.5'
                                        "
                                    ></span>
                                </span>
                            </label>


                            {{-- =================================================
                                 REGRAS ESPECIAIS — ACESSO COMPACTO
                            ================================================== --}}

                            <form
                                x-show="
                                    !specialRulesUnlocked
                                "

                                x-cloak

                                @submit.prevent="
                                    unlockSpecialRules()
                                "

                                class="
                                    flex
                                    items-center
                                    gap-2

                                    rounded-xl
                                    border
                                    border-[#d8c7ab]/75

                                    bg-[#fffdf8]

                                    p-2
                                "
                            >
                                <input
                                    type="password"

                                    x-model="
                                        specialRulesCode
                                    "

                                    autocomplete="off"

                                    placeholder="Código de regra especial"

                                    class="
                                        h-9
                                        min-w-0
                                        flex-1

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]

                                        bg-[#fbf8f1]

                                        px-3

                                        text-[11px]
                                        font-semibold
                                        text-[#53150f]

                                        outline-none

                                        transition

                                        focus:border-[#8c6239]
                                        focus:ring-2
                                        focus:ring-[#8c6239]/10
                                    "
                                >

                                <button
                                    type="submit"

                                    class="
                                        h-9
                                        shrink-0

                                        rounded-lg
                                        bg-[#6b1d14]

                                        px-3.5

                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.08em]
                                        text-[#fffaf2]

                                        transition
                                        hover:bg-[#53150f]
                                    "
                                >
                                    Liberar
                                </button>
                            </form>


                            <p
                                x-show="
                                    !specialRulesUnlocked
                                    && specialRulesCodeError
                                "

                                x-cloak

                                class="
                                    px-1
                                    text-[10px]
                                    font-bold
                                    text-red-700
                                "

                                x-text="
                                    specialRulesCodeError
                                "
                            ></p>


                            {{-- MORQUEN --}}
                            <div
                                x-show="
                                    specialRulesUnlocked
                                "

                                x-cloak

                                class="
                                    flex
                                    items-center
                                    gap-2
                                "
                            >
                                <label
                                    class="
                                        flex
                                        min-w-0
                                        flex-1
                                        cursor-pointer
                                        items-center
                                        justify-between
                                        gap-4

                                        rounded-xl
                                        border
                                        border-[#9b6d4c]/45

                                        bg-[#fffdf8]

                                        px-3.5
                                        py-3

                                        transition
                                        hover:bg-[#f7eee2]/55
                                    "
                                >
                                    <span
                                        class="
                                            font-serif
                                            text-[13px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Regra Morquen
                                    </span>

                                    <span
                                        class="
                                            relative
                                            h-5
                                            w-9
                                            shrink-0
                                            rounded-full
                                            transition
                                        "

                                        :class="
                                            draft.settings.optional_rules.morquen
                                                ? 'bg-[#6b1d14]'
                                                : 'bg-[#d8c7ab]'
                                        "
                                    >
                                        <input
                                            type="checkbox"

                                            x-model="
                                                draft.settings.optional_rules.morquen
                                            "

                                            class="sr-only"
                                        >

                                        <span
                                            class="
                                                absolute
                                                top-0.5

                                                h-4
                                                w-4

                                                rounded-full
                                                bg-[#fffdf8]
                                                shadow-sm

                                                transition
                                            "

                                            :class="
                                                draft.settings.optional_rules.morquen
                                                    ? 'translate-x-[18px]'
                                                    : 'translate-x-0.5'
                                            "
                                        ></span>
                                    </span>
                                </label>

                                <button
                                    type="button"

                                    @click="
                                        lockSpecialRules()
                                    "

                                    class="
                                        flex
                                        h-10
                                        w-10
                                        shrink-0
                                        items-center
                                        justify-center

                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/70

                                        bg-[#f4f1e8]

                                        text-[#8c6239]

                                        transition

                                        hover:bg-[#eadbc8]
                                        hover:text-[#53150f]
                                    "

                                    title="Ocultar regra especial"
                                    aria-label="Ocultar regra especial"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M6 6l12 12M18 6 6 18" />
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </section>


                    {{-- ERRO --}}

                    <div
                        x-show="
                            saveError
                        "

                        class="
                            mt-4

                            rounded-lg
                            border
                            border-red-200

                            bg-red-50

                            px-3
                            py-3

                            text-[11px]
                            font-bold

                            text-red-700
                        "

                        x-text="
                            saveError
                        "
                    ></div>

                </div>


                {{-- ====================================================
                     FOOTER
                ===================================================== --}}

                <footer
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        gap-3

                        border-t
                        border-[#a0774d]/28

                        bg-[#eadbc8]/72

                        px-4
                        py-3

                        sm:px-5
                    "
                >

                    <p
                        class="
                            hidden

                            text-[11px]
                            text-[#7d604d]

                            sm:block
                        "
                    >
                        Identidade, opções da ficha e regras especiais são salvas juntas.
                    </p>


                    <div
                        class="
                            ml-auto

                            flex
                            items-center
                            gap-2
                        "
                    >

                        <button
                            type="button"

                            @click="closeModal()"

                            :disabled="
                                saving
                                || levelingClassId !== null
                                || proficiencySaving
                            "

                            class="
                                rounded-lg

                                px-3.5
                                py-2.5

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.11em]

                                text-[#8c6239]

                                transition

                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]

                                disabled:opacity-50
                            "
                        >
                            Fechar
                        </button>


                        <button
                            type="button"

                            @click="save()"

                            :disabled="
                                saving
                                || !String(
                                    draft.name
                                    || ''
                                ).trim()
                            "

                            class="
                                min-w-[118px]

                                rounded-lg

                                bg-[#6b1d14]

                                px-4
                                py-2.5

                                text-[11px]
                                font-black
                                uppercase
                                tracking-[0.11em]

                                text-[#fffaf2]

                                shadow-[0_1px_2px_rgba(83,21,15,.14)]

                                transition

                                hover:bg-[#53150f]

                                disabled:cursor-wait
                                disabled:opacity-50
                            "
                        >
                            <span
                                x-show="
                                    !saving
                                "
                            >
                                Salvar alterações
                            </span>

                            <span
                                x-show="
                                    saving
                                "
                                x-cloak
                            >
                                Salvando...
                            </span>
                        </button>

                    </div>

                </footer>

            </div>


            {{-- ========================================================
                 CONFIRMAÇÃO DE ALTERAÇÃO DE NÍVEL
            ========================================================= --}}

            <div
                x-show="
                    levelConfirmOpen
                "
                x-cloak

                class="
                    absolute
                    inset-0
                    z-30

                    flex
                    items-center
                    justify-center

                    p-4
                "
            >

                <div
                    class="
                        absolute
                        inset-0

                        bg-[#2a1712]/38
                        backdrop-blur-[1px]
                    "

                    @click="
                        cancelLevelConfirmation()
                    "
                ></div>


                <div
                    @click.stop

                    x-transition:enter="
                        transition
                        ease-out
                        duration-150
                    "

                    x-transition:enter-start="
                        opacity-0
                        scale-[.98]
                    "

                    x-transition:enter-end="
                        opacity-100
                        scale-100
                    "

                    class="
                        relative
                        z-10

                        w-full
                        max-w-[430px]

                        overflow-hidden

                        rounded-2xl
                        border
                        border-[#b08c62]/75

                        bg-[#fbf8f1]

                        shadow-[0_24px_70px_rgba(42,23,18,.32)]
                    "
                >

                    {{-- CABEÇALHO --}}

                    <div
                        class="
                            border-b
                            border-[#a0774d]/28

                            bg-[#eadbc8]

                            px-4
                            py-3.5
                        "
                    >
                        <p
                            class="
                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.15em]

                                text-[#8c6239]
                            "
                        >
                            Alteração de progressão
                        </p>

                        <h4
                            class="
                                mt-1

                                font-serif
                                text-[20px]
                                font-black
                                leading-none

                                text-[#53150f]
                            "

                            x-text="
                                levelConfirmMode === 'up'
                                    ? 'Confirmar subida de nível'
                                    : 'Confirmar redução de nível'
                            "
                        ></h4>
                    </div>


                    {{-- CONTEÚDO --}}

                    <div class="p-4">

                        <p
                            class="
                                text-[12px]
                                leading-relaxed

                                text-[#5f4031]
                            "
                        >
                            Você está prestes a

                            <strong
                                class="
                                    font-serif
                                    text-[#53150f]
                                "

                                x-text="
                                    levelConfirmMode === 'up'
                                        ? 'subir'
                                        : 'voltar'
                                "
                            ></strong>

                            um nível em

                            <strong
                                class="
                                    font-serif
                                    text-[#53150f]
                                "

                                x-text="
                                    levelConfirmClass?.name
                                    || 'Classe'
                                "
                            ></strong>.
                        </p>


                        {{-- RESUMO --}}

                        <div
                            class="
                                mt-3

                                grid
                                grid-cols-[1fr_auto_1fr]

                                items-center

                                rounded-xl
                                border
                                border-[#d8c7ab]/75

                                bg-[#fffdf8]

                                px-3
                                py-3
                            "
                        >

                            <div class="text-center">
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]

                                        text-[#8c6239]
                                    "
                                >
                                    Atual
                                </p>

                                <p
                                    class="
                                        mt-1

                                        font-serif
                                        text-[22px]
                                        font-black
                                        leading-none

                                        text-[#53150f]
                                    "

                                    x-text="
                                        levelConfirmClass?.level
                                        ?? '—'
                                    "
                                ></p>
                            </div>


                            <span
                                class="
                                    px-3

                                    font-serif
                                    text-[20px]
                                    font-black

                                    text-[#b08c62]
                                "
                                aria-hidden="true"
                            >
                                →
                            </span>


                            <div class="text-center">
                                <p
                                    class="
                                        text-[9px]
                                        font-black
                                        uppercase
                                        tracking-[0.12em]

                                        text-[#8c6239]
                                    "
                                >
                                    Novo
                                </p>

                                <p
                                    class="
                                        mt-1

                                        font-serif
                                        text-[22px]
                                        font-black
                                        leading-none

                                        text-[#6b1d14]
                                    "

                                    x-text="
                                        levelConfirmClass
                                            ? (
                                                parseInt(
                                                    levelConfirmClass.level
                                                )
                                                + (
                                                    levelConfirmMode === 'up'
                                                        ? 1
                                                        : -1
                                                )
                                            )
                                            : '—'
                                    "
                                ></p>
                            </div>

                        </div>


                        {{-- AVISO --}}

                        <div
                            class="
                                mt-3

                                rounded-xl
                                border
                                border-amber-300/75

                                bg-amber-50

                                px-3
                                py-3
                            "
                        >
                            <p
                                class="
                                    text-[10px]
                                    font-black
                                    uppercase
                                    tracking-[0.12em]

                                    text-amber-900
                                "
                            >
                                Atenção
                            </p>

                            <p
                                class="
                                    mt-1

                                    text-[11px]
                                    leading-relaxed

                                    text-amber-900/85
                                "
                            >
                                Esta ação altera imediatamente o nível da classe e o nível total da ficha.

                                <span
                                    x-show="
                                        levelConfirmMode === 'up'
                                    "
                                >
                                    HP, características, recursos e escolhas concedidas pelo novo nível não serão adicionados automaticamente.
                                </span>

                                <span
                                    x-show="
                                        levelConfirmMode === 'down'
                                    "
                                >
                                    HP, características, recursos e escolhas obtidas nesse nível não serão removidos automaticamente.
                                </span>

                                Se a proficiência estiver em modo automático, ela será recalculada pelo novo nível total.
                            </p>
                        </div>

                    </div>


                    {{-- AÇÕES --}}

                    <div
                        class="
                            flex
                            items-center
                            justify-end
                            gap-2

                            border-t
                            border-[#a0774d]/25

                            bg-[#f4f1e8]/75

                            px-4
                            py-3
                        "
                    >

                        <button
                            type="button"

                            @click="
                                cancelLevelConfirmation()
                            "

                            class="
                                rounded-lg

                                px-3.5
                                py-2.5

                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.10em]

                                text-[#8c6239]

                                transition

                                hover:bg-[#eadbc8]
                                hover:text-[#53150f]
                            "
                        >
                            Cancelar
                        </button>


                        <button
                            type="button"

                            @click="
                                confirmLevelChange()
                            "

                            class="
                                min-w-[126px]

                                rounded-lg
                                border

                                px-4
                                py-2.5

                                text-[10px]
                                font-black
                                uppercase
                                tracking-[0.10em]

                                transition
                            "

                            :class="
                                levelConfirmMode === 'up'
                                    ? 'border-[#6b1d14]/30 bg-[#6b1d14] text-[#fffaf2] hover:bg-[#53150f]'
                                    : 'border-[#9a4f42]/35 bg-[#7b2a21] text-white hover:bg-[#651f18]'
                            "

                            x-text="
                                levelConfirmMode === 'up'
                                    ? 'Sim, subir nível'
                                    : 'Sim, voltar nível'
                            "
                        ></button>

                    </div>

                </div>

            </div>

        </div>

    </template>

</div>