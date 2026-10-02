@php
    /*
    |--------------------------------------------------------------------------
    | CHARACTER ORIGINAL x FICHA MECÂNICA
    |--------------------------------------------------------------------------
    |
    | $character      = identidade + sistemas compartilhados.
    | $sheetCharacter = ficha mecânica renderizada.
    |--------------------------------------------------------------------------
    */
    $sheetCharacter =
        $character;

    $sheetTransformation =
        null;

    $editingTransformationSheet =
        false;

    $requestedTransformationId =
        request()->integer(
            'transformation_sheet'
        );

    $activeSheetState =
        null;

    if (
        method_exists(
            $character,
            'transformationState'
        )
    ) {
        $activeSheetState =
            $character
                ->transformationState()
                ->with([
                    'transformation.formCharacter',
                ])
                ->first();

        if (
            $activeSheetState
            && $activeSheetState->is_active
            && $activeSheetState->transformation
            && $activeSheetState->transformation->type
                ===
                \App\Models\CharacterTransformation::TYPE_CHARACTER_SHEET
            && $activeSheetState->transformation->formCharacter
        ) {
            $sheetTransformation =
                $activeSheetState
                    ->transformation;

            $sheetCharacter =
                $sheetTransformation
                    ->formCharacter;
        }
    }

    if (
        $requestedTransformationId > 0
        && method_exists(
            $character,
            'transformations'
        )
    ) {
        $requestedTransformation =
            $character
                ->transformations()
                ->with(
                    'formCharacter'
                )
                ->whereKey(
                    $requestedTransformationId
                )
                ->where(
                    'type',
                    \App\Models\CharacterTransformation::TYPE_CHARACTER_SHEET
                )
                ->first();

        abort_unless(
            $requestedTransformation
            && $requestedTransformation->formCharacter,
            404
        );

        if (
            $sheetTransformation
            && (int) $sheetTransformation->id
                !==
                (int) $requestedTransformation->id
        ) {
            abort(
                409,
                'Volte à Forma Normal antes de editar outra transformação.'
            );
        }

        $sheetTransformation =
            $requestedTransformation;

        $sheetCharacter =
            $requestedTransformation
                ->formCharacter;

        $editingTransformationSheet =
            !(
                $activeSheetState
                && $activeSheetState->is_active
                && (int) $activeSheetState->character_transformation_id
                    ===
                    (int) $requestedTransformation->id
            );
    }

    $sheetCharacter->loadMissing([
        'classes',
        'abilities',
        'skills',
        'savingThrows',
        'features',
        'resources',
        'combat',
        'attacks',
    ]);

    $sheetTransformationImageUrl =
        null;

    if (
        $sheetTransformation
        && $sheetTransformation->image_path
    ) {
        $normalizedTransformationImagePath =
            str_replace(
                '\\',
                '/',
                trim(
                    (string) $sheetTransformation->image_path
                )
            );

        foreach (
            [
                'storage/app/public/',
                'public/storage/',
                'storage/',
                'public/',
            ]
            as $prefix
        ) {
            $position =
                stripos(
                    $normalizedTransformationImagePath,
                    $prefix
                );

            if ($position !== false) {
                $normalizedTransformationImagePath =
                    substr(
                        $normalizedTransformationImagePath,
                        $position
                        + strlen(
                            $prefix
                        )
                    );
            }
        }

        $normalizedTransformationImagePath =
            ltrim(
                $normalizedTransformationImagePath,
                '/'
            );

        if ($normalizedTransformationImagePath !== '') {
            $sheetTransformationImageUrl =
                '/storage/'
                . $normalizedTransformationImagePath;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VISIBILIDADE DO MÓDULO DE DEFESAS
    |--------------------------------------------------------------------------
    |
    | Agora a opção é uma chave de VISIBILIDADE REAL:
    |
    | show_defenses = true
    |     -> renderiza o hero-defenses inteiro.
    |
    | show_defenses = false
    |     -> não renderiza wrapper, componente, Alpine ou espaço.
    |
    | Para personagens antigos que ainda não possuem a chave:
    |     -> mostra automaticamente somente quando existem defesas.
    |
    */

    $normalizeDefenseList = static function ($value): array {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_string($value)) {
            $decoded =
                json_decode(
                    $value,
                    true
                );

            $value =
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(
                fn ($entry) =>
                    trim(
                        (string) $entry
                    )
            )
            ->filter()
            ->unique(
                fn (string $entry) =>
                    mb_strtolower(
                        $entry
                    )
            )
            ->values()
            ->all();
    };

    $characterCombat =
        $sheetCharacter->combat;

    $hasHeroDefenses =
        count(
            $normalizeDefenseList(
                $characterCombat?->damage_resistances
            )
        ) > 0
        || count(
            $normalizeDefenseList(
                $characterCombat?->damage_immunities
            )
        ) > 0
        || count(
            $normalizeDefenseList(
                $characterCombat?->damage_vulnerabilities
            )
        ) > 0;

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
     * NOVA REGRA:
     * se show_defenses foi configurado, ele manda independentemente
     * de o personagem possuir ou não resistências salvas.
     */
    if (
        array_key_exists(
            'show_defenses',
            $displaySettings
        )
    ) {
        $showHeroDefenses =
            (bool) $displaySettings['show_defenses'];

    /*
     * Compatibilidade com a primeira implementação.
     * Se você já desligou o toggle antigo, false passa a esconder de fato.
     */
    } elseif (
        array_key_exists(
            'show_empty_defenses',
            $displaySettings
        )
    ) {
        $showHeroDefenses =
            (bool) $displaySettings['show_empty_defenses'];

    /*
     * Registro antigo sem configuração:
     * mantém o comportamento automático.
     */
    } else {
        $showHeroDefenses =
            $hasHeroDefenses;
    }

    /*
    |--------------------------------------------------------------------------
    | PARTY DRAWER
    |--------------------------------------------------------------------------
    */

    $showPartyDrawer =
        auth()->check()
        &&
        (int) $character->user_id
            ===
            (int) auth()->id()
        &&
        session('dashboard_mode')
            ===
            'player';


    /*
    |--------------------------------------------------------------------------
    | TEMA VISUAL DA FICHA
    |--------------------------------------------------------------------------
    |
    | O tema pertence ao Character original.
    | A Nova Ficha usa a mesma aparência do Player.
    |
    | Neste primeiro estágio só existe o tema visual padrão,
    | mas o atributo data-theme já deixa o Show preparado para
    | Emerald, Arcane, Midnight, Infernal, Royal etc.
    |--------------------------------------------------------------------------
    */

    $characterTheme =
        trim(
            (string) data_get(
                $character->sheet_settings,
                'appearance.theme',
                'spellbound'
            )
        );

    if ($characterTheme === '') {
        $characterTheme =
            'spellbound';
    }

    $characterTheme =
        preg_replace(
            '/[^a-z0-9_-]/i',
            '',
            $characterTheme
        )
        ?: 'spellbound';
@endphp

<x-app-layout>

    <style>

        /*

        |--------------------------------------------------------------------------

        | FOLHA — V6 SUPERFÍCIE ÚNICA

        |--------------------------------------------------------------------------

        |

        | A moldura existe somente no contorno da ficha.

        | Os blocos internos deixam de parecer cards independentes.

        |

        */

        .character-sheet-page {

            min-height: 100%;

            background:

                radial-gradient(

                    circle at 18% 3%,

                    rgba(255,255,255,.30),

                    transparent 28%

                ),

                linear-gradient(

                    180deg,

                    #e8e0d3 0%,

                    #ddd2c2 100%

                );

        }

        .character-sheet-canvas {

            position: relative;

            overflow: hidden;

            border:

                1px solid

                rgba(174,145,105,.58);

            border-radius: 20px;

            background-color: #eee5d6;

            background-image:

                linear-gradient(

                    rgba(140,98,57,.024) 1px,

                    transparent 1px

                ),

                linear-gradient(

                    90deg,

                    rgba(140,98,57,.018) 1px,

                    transparent 1px

                ),

                radial-gradient(

                    circle at 28% 6%,

                    rgba(255,255,255,.28),

                    transparent 34%

                );

            background-size:

                25px 25px,

                25px 25px,

                auto;

            padding: 0;

            box-shadow:

                inset 0 1px 0 rgba(255,255,255,.46),

                0 12px 28px rgba(83,21,15,.07);

        }

        /*

        | Um único filete interno reforça a ideia de folha física,

        | sem criar outra caixa ao redor de cada componente.

        */

        .character-sheet-canvas::after {

            content: '';

            pointer-events: none;

            position: absolute;

            inset: 8px;

            /*

            | O filete interno é decorativo.

            | Antes estava em z-index 90 e podia ser desenhado por cima

            | de drawers e modais.

            */

            z-index: 1;

            border:

                1px solid

                rgba(190,161,119,.26);

            border-radius: 13px;

        }

        /*

        |--------------------------------------------------------------------------

        | HERO

        |--------------------------------------------------------------------------

        */

        .character-sheet-hero {

            position: relative;

            z-index: 20;

        }

        .character-sheet-hero > * {

            margin-bottom: 0 !important;

            border: 0 !important;

            border-radius: 0 !important;

            background:

                transparent !important;

            box-shadow:

                none !important;

        }

        /*
        |--------------------------------------------------------------------------
        | DEFESAS DO HEADER — ALINHADAS À COLUNA PRINCIPAL
        |--------------------------------------------------------------------------
        |
        | No desktop, as defesas começam exatamente na região da direita,
        | deixando livre a largura ocupada pelos Atributos.
        |
        | O show é responsável pelo posicionamento.
        | O partial hero-defenses cuida apenas da apresentação/conteúdo.
        |
        */

        .character-sheet-hero-defenses {
            position: relative;
            z-index: 30;
            width: 100%;
            padding:
                0 14px 10px;
        }

        .character-sheet-hero-defenses > * {
            width: 100%;
            min-width: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFESAS — DESKTOP
        |--------------------------------------------------------------------------
        |
        | A grade principal usa 322px para a coluna de atributos em >= 1024px.
        | Os 17px adicionais reproduzem o recuo interno usado por Ataques,
        | alinhando o início visual das defesas com o conteúdo da coluna direita.
        |
        */

        @media (min-width: 1024px) {
            .character-sheet-hero-defenses {
                display: grid;
                grid-template-columns:
                    calc(322px + 17px)
                    minmax(0, 1fr);
                width: 100%;
                padding:
                    0 17px 10px 0;
            }

            .character-sheet-hero-defenses > * {
                grid-column: 2;
                width: 100% !important;
                min-width: 0;
                margin-left: 0 !important;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DEFESAS — DESKTOP GRANDE
        |--------------------------------------------------------------------------
        |
        | A partir de 1280px, a coluna dos Atributos passa para 316px.
        |
        */

        @media (min-width: 1280px) {
            .character-sheet-hero-defenses {
                grid-template-columns:
                    calc(316px + 17px)
                    minmax(0, 1fr);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DEFESAS — MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 640px) {
            .character-sheet-hero-defenses {
                padding:
                    0 8px 8px;
            }
        }

        /*

        |--------------------------------------------------------------------------

        | CORPO

        |--------------------------------------------------------------------------

        */

        .character-sheet-body {

            position: relative;

            z-index: 10;

            margin-top: 0;

            overflow: visible;

            border: 0;

            border-radius: 0;

            background: transparent;

            box-shadow: none;

        }

        .character-sheet-body::before {

            content: '';

            position: absolute;

            top: 0;

            right: 14px;

            left: 14px;

            height: 1px;

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    rgba(151,116,77,.44) 8%,

                    rgba(151,116,77,.44) 92%,

                    transparent

                );

        }

        .character-sheet-main-grid {

            display: grid;

            grid-template-columns:

                minmax(0, 1fr);

            align-items: stretch;

            gap: 0;

        }

        .character-sheet-abilities,

        .character-sheet-main-column,

        .character-sheet-attacks,

        .character-sheet-features,

        .character-sheet-traits-feats {

            min-width: 0;

        }

        /*

        |--------------------------------------------------------------------------

        | ATRIBUTOS

        |--------------------------------------------------------------------------

        */

        .character-sheet-abilities {

            position: relative;

            padding:

                17px 14px 20px 17px;

            background:

                linear-gradient(

                    180deg,

                    rgba(239,228,210,.58) 0%,

                    rgba(233,219,198,.42) 100%

                );

        }

        .character-sheet-abilities::before {

            content: '';

            position: absolute;

            top: 21px;

            bottom: 21px;

            left: 7px;

            width: 2px;

            border-radius: 999px;

            background:

                linear-gradient(

                    180deg,

                    transparent,

                    rgba(107,29,20,.18) 10%,

                    rgba(107,29,20,.18) 90%,

                    transparent

                );

        }

        /*

        |--------------------------------------------------------------------------

        | COLUNA PRINCIPAL

        |--------------------------------------------------------------------------

        */

        .character-sheet-main-column {

            display: flex;

            min-height: 100%;

            flex-direction: column;

            gap: 0;

            padding: 0;

            background:

                rgba(244,237,225,.16);

        }

        /*

        |--------------------------------------------------------------------------

        | SEÇÕES — NÃO SÃO CARDS

        |--------------------------------------------------------------------------

        */

        .character-sheet-block {

            position: relative;

            min-width: 0;

            overflow: visible;

            border: 0;

            border-radius: 0;

            box-shadow: none;

        }

        .character-sheet-block::after {

            display: none;

        }

        .character-sheet-attacks {

            background:

                rgba(247,241,230,.42);

            padding:

                15px 17px 13px;

        }

        .character-sheet-features {

            position: relative;

            background:

                rgba(235,222,202,.42);

            padding:

                17px 18px 16px;

        }

        .character-sheet-features::before {

            content: '';

            position: absolute;

            top: 0;

            right: 18px;

            left: 18px;

            height: 1px;

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    rgba(151,116,77,.34),

                    transparent

                );

        }

        /*

        | O partial desenha conteúdo; o show desenha a folha.

        */

        .character-sheet-abilities > *,

        .character-sheet-attacks > *,

        .character-sheet-traits-feats > * {

            width: 100%;

            border: 0 !important;

            border-radius: 0 !important;

            background: transparent !important;

            box-shadow: none !important;

        }

        .character-sheet-attacks > * {

            min-height: 0 !important;

            height: auto !important;

        }

        /*

        |--------------------------------------------------------------------------

        | RECURSOS + PROGRESSÃO

        |--------------------------------------------------------------------------

        */

        .character-sheet-bottom-grid {

            display: grid;

            grid-template-columns:

                minmax(0, 1fr);

            gap: 0;

            margin: 0;

            border-top:

                1px solid

                rgba(151,116,77,.24);

            background:

                rgba(229,215,194,.34);

        }

        .character-sheet-bottom-cell {

            position: relative;

            min-width: 0;

            overflow: visible;

            border: 0;

            border-radius: 0;

            background:

                transparent;

            padding:

                17px 18px 18px;

            box-shadow: none;

        }

        /*

        |--------------------------------------------------------------------------

        | DESKTOP

        |--------------------------------------------------------------------------

        */

        @media (min-width: 1024px) {

            .character-sheet-main-grid {

                grid-template-columns:

                    322px

                    minmax(0, 1fr);

            }

            .character-sheet-abilities {

                border-right:

                    1px solid

                    rgba(151,116,77,.30);

            }

            .character-sheet-main-column {

                border-left:

                    1px solid

                    rgba(255,255,255,.20);

            }

            .character-sheet-bottom-grid {

                grid-template-columns:

                    repeat(

                        2,

                        minmax(0, 1fr)

                    );

            }

            .character-sheet-bottom-cell + .character-sheet-bottom-cell {

                border-left:

                    1px solid

                    rgba(151,116,77,.24);

            }

        }

        @media (min-width: 1280px) {

            .character-sheet-main-grid {

                grid-template-columns:

                    316px

                    minmax(0, 1fr);

            }

        }

        /*

        |--------------------------------------------------------------------------

        | MOBILE / TABLET

        |--------------------------------------------------------------------------

        */

        @media (max-width: 1023px) {

            .character-sheet-abilities {

                border-bottom:

                    1px solid

                    rgba(151,116,77,.26);

            }

            .character-sheet-attacks {

                padding-top: 13px;

            }

        }

        @media (max-width: 640px) {

            .character-sheet-canvas::after {

                inset: 4px;

                border-radius: 16px;

            }

            .character-sheet-abilities {

                padding:

                    14px 10px 16px;

            }

            .character-sheet-attacks,

            .character-sheet-features {

                padding:

                    14px 12px;

            }

        }

        /*

        |--------------------------------------------------------------------------

        | ABAS LATERAIS

        |--------------------------------------------------------------------------

        */

        .character-side-tab {

            min-width: 42px;

            border:

                1px solid

                rgba(205,187,159,.88);

            border-right: 0;

            background:

                var(--theme-side-tab-background);

            color:

                var(--theme-side-tab-text);

            box-shadow:

                0 7px 18px rgba(83,21,15,.16);

            transition:

                background .16s ease,

                padding .16s ease,

                transform .16s ease;

        }

        .character-side-tab:hover,

        .character-side-tab.active {

            background:

                var(--theme-side-tab-hover-background);

        }

        .character-side-tab:hover {

            padding-right: 1rem;

        }

        .character-drawer {

            background:

                var(--theme-drawer-background);

        }













        /*

        |--------------------------------------------------------------------------

        | V7 — PALETA DO HEADER / PAPEL BRANCO + BEGE AVERMELHADO

        |--------------------------------------------------------------------------

        |

        | A ficha continua sendo uma única superfície.

        | A hierarquia agora vem de:

        |

        | - papel base bege quente;

        | - regiões secundárias em bege levemente avermelhado;

        | - informações importantes em papel quase branco;

        | - linhas marrom/douradas discretas.

        |

        */

        .character-sheet-page {

            background:

                var(--theme-page-background) !important;

        }

        .character-sheet-canvas {

            border-color:

                var(--theme-canvas-border) !important;

            background:

                var(--theme-canvas-background) !important;

            box-shadow:

                var(--theme-shadow) !important;

        }

        .character-sheet-canvas::after {

            border-color:

                var(--theme-canvas-inner-border) !important;

        }

        /*

        | Linha entre header e corpo semelhante às linhas da identidade.

        */

        .character-sheet-body::before {

            right: 18px !important;

            left: 18px !important;

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    color-mix(in srgb, var(--theme-accent) 42%, transparent) 8%,

                    color-mix(in srgb, var(--theme-accent) 42%, transparent) 92%,

                    transparent

                ) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | ATRIBUTOS

        |--------------------------------------------------------------------------

        */

        .character-sheet-abilities {

            background:

                linear-gradient(

                    180deg,

                    rgba(234,218,197,.70) 0%,

                    rgba(229,210,187,.54) 100%

                ) !important;

        }

        .character-sheet-abilities::before {

            background:

                linear-gradient(

                    180deg,

                    transparent,

                    rgba(107,29,20,.16) 10%,

                    rgba(107,29,20,.16) 90%,

                    transparent

                ) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | COLUNA PRINCIPAL

        |--------------------------------------------------------------------------

        */

        .character-sheet-main-column {

            background:

                rgba(245,239,229,.34) !important;

        }

        /*

        | Ataques é uma área de consulta importante:

        | fica mais clara, próxima do branco quente do header.

        */

        .character-sheet-attacks {

            background:

                linear-gradient(

                    180deg,

                    rgba(249,245,237,.88) 0%,

                    rgba(246,239,229,.82) 100%

                ) !important;

        }

        /*

        | Habilidades usam o bege avermelhado mais forte.

        | Os registros importantes serão brancos no próprio partial.

        */

        .character-sheet-features {

            background:

                linear-gradient(

                    180deg,

                    rgba(232,212,190,.72) 0%,

                    rgba(227,204,180,.60) 100%

                ) !important;

        }

        .character-sheet-features::before {

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    rgba(151,112,72,.30),

                    transparent

                ) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | BLOCOS INFERIORES

        |--------------------------------------------------------------------------

        */

        .character-sheet-bottom-grid {

            border-top-color:

                rgba(151,112,72,.28) !important;

            background:

                linear-gradient(

                    180deg,

                    rgba(231,214,193,.54) 0%,

                    rgba(226,207,184,.46) 100%

                ) !important;

        }

        .character-sheet-bottom-cell {

            background:

                rgba(248,243,235,.34) !important;

        }

        @media (min-width: 1024px) {

            .character-sheet-abilities {

                border-right-color:

                    rgba(151,112,72,.32) !important;

            }

            .character-sheet-main-column {

                border-left-color:

                    rgba(255,255,255,.28) !important;

            }

            .character-sheet-bottom-cell + .character-sheet-bottom-cell {

                border-left-color:

                    rgba(151,112,72,.26) !important;

            }

        }

        /*

        |--------------------------------------------------------------------------

        | V8 — AJUSTE EXCLUSIVO DE HABILIDADES

        |--------------------------------------------------------------------------

        |

        | Ataques permanece exatamente como está.

        | A área de habilidades perde o tom marrom saturado e passa a usar

        | um bege avermelhado mais leve, próximo das superfícies do header.

        |

        */

        .character-sheet-features {

            background:

                linear-gradient(

                    180deg,

                    rgba(239,227,212,.78) 0%,

                    rgba(234,218,199,.68) 100%

                ) !important;

        }

        .character-sheet-features::before {

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    rgba(151,112,72,.26),

                    transparent

                ) !important;

        }













        /*

        |--------------------------------------------------------------------------

        | V9 — PALETA UNIFICADA A PARTIR DE ATTACK

        |--------------------------------------------------------------------------

        |

        | Attack é a referência e NÃO é alterado aqui.

        |

        | Tokens extraídos da linguagem visual já usada nele:

        | papel principal  : #fbf8f1

        | papel claro      : #fffdf8

        | papel alternado  : #f7f0e6

        | faixa bege       : #eadbc8

        | hover            : #f4e8d8

        |

        */

        .character-sheet-page {

            --sheet-paper:
                var(--theme-paper);

            --sheet-paper-strong:
                var(--theme-paper-strong);

            --sheet-paper-alt:
                var(--theme-paper-alt);

            --sheet-band:
                var(--theme-band);

            --sheet-band-soft:
                var(--theme-band-soft);

            --sheet-hover:
                var(--theme-hover);

            --sheet-border:
                var(--theme-border);

            --sheet-border-strong:
                var(--theme-border-strong);

            --sheet-heading:
                var(--theme-title);

            --sheet-text:
                var(--theme-text);

            --sheet-muted:
                var(--theme-muted);

            --sheet-label:
                var(--theme-label);

        }

        /*

        | Mantém o fundo geral um pouco mais escuro que a ficha,

        | mas remove a diferença excessiva de cor entre os partials.

        */

        .character-sheet-canvas {

            background-color:

                var(--theme-canvas-bg) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | ATRIBUTOS

        |--------------------------------------------------------------------------

        |

        | Mesma superfície clara usada ao redor do Attack.

        | Os cards internos continuam dando hierarquia.

        |

        */

        .character-sheet-abilities {

            background:

                linear-gradient(

                    180deg,

                    rgba(249,245,237,.88) 0%,

                    rgba(246,239,229,.82) 100%

                ) !important;

        }

        .character-sheet-abilities::before {

            background:

                linear-gradient(

                    180deg,

                    transparent,

                    rgba(140,98,57,.15) 10%,

                    rgba(140,98,57,.15) 90%,

                    transparent

                ) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | HABILIDADES

        |--------------------------------------------------------------------------

        |

        | O design V6 permanece; somente a superfície da seção passa

        | a seguir a mesma família cromática do Attack.

        |

        */

        .character-sheet-features {

            background:

                linear-gradient(

                    180deg,

                    rgba(249,245,237,.88) 0%,

                    rgba(246,239,229,.82) 100%

                ) !important;

        }

        .character-sheet-features::before {

            background:

                linear-gradient(

                    90deg,

                    transparent,

                    rgba(160,119,77,.24),

                    transparent

                ) !important;

        }

        /*

        |--------------------------------------------------------------------------

        | PARTE INFERIOR

        |--------------------------------------------------------------------------

        */

        .character-sheet-bottom-grid {

            background:

                linear-gradient(

                    180deg,

                    rgba(247,240,230,.72) 0%,

                    rgba(243,233,220,.70) 100%

                ) !important;

            border-top-color:

                rgba(176,140,98,.26) !important;

        }

        .character-sheet-bottom-cell {

            background:

                transparent !important;

        }

        @media (min-width: 1024px) {

            .character-sheet-abilities {

                border-right-color:

                    rgba(176,140,98,.28) !important;

            }

            .character-sheet-bottom-cell + .character-sheet-bottom-cell {

                border-left-color:

                    rgba(176,140,98,.25) !important;

            }

        }

        /*

        |--------------------------------------------------------------------------

        | V10 — ALINHAMENTO ENTRE ATTACK E FEATURES

        |--------------------------------------------------------------------------

        |

        | Attack permanece intocado.

        | Apenas a região que recebe Features passa a usar a mesma geometria

        | externa de padding usada na região de Attack.

        |

        */

        .character-sheet-features {

            padding:

                15px 17px 13px !important;

        }

        /*

        | Remove o filete adicional específico de Features.

        | O próprio componente já possui o mesmo cabeçalho do Attack.

        */

        .character-sheet-features::before {

            display: none !important;

        }





        /*

        |--------------------------------------------------------------------------

        | V11 — TRAÇOS DE ESPÉCIE + TALENTOS

        |--------------------------------------------------------------------------

        |

        | Bloco compacto abaixo de Habilidades.

        |

        */

        .character-sheet-traits-feats {

            position: relative;

            min-width: 0;

            border-top:

                1px solid

                rgba(151,116,77,.24);

            background:

                rgba(247,241,230,.42);

            padding:

                13px 17px 15px;

        }

        .character-sheet-traits-feats > * {

            width: 100%;

        }

















        /*

        |--------------------------------------------------------------------------

        | V13 — TREINAMENTO ABAIXO DOS ATRIBUTOS

        |--------------------------------------------------------------------------

        */

        .character-sheet-training-under-abilities {

            margin-top: 12px;

            border-top:

                1px solid

                rgba(151,116,77,.24);

            padding-top: 12px;

        }

        .character-sheet-training-under-abilities > * {

            width: 100%;

        }

    
        /*
        |--------------------------------------------------------------------------
        | V14 — FUNDO QUADRICULADO + MÓDULOS SOBRE A FOLHA
        |--------------------------------------------------------------------------
        |
        | O canvas continua sendo a folha quadriculada.
        | O corpo fica transparente e deixa esse papel aparecer.
        |
        | Atributos, Ataques, Habilidades e os demais blocos passam a funcionar
        | como módulos de papel posicionados sobre essa base.
        |
        | Este bloco fica por último de propósito: ele consolida a linguagem
        | visual final e sobrescreve as versões anteriores sem alterar os partials.
        |
        */

        /*
        |--------------------------------------------------------------------------
        | CORPO — DEIXA O QUADRICULADO DO CANVAS APARECER
        |--------------------------------------------------------------------------
        */

        .character-sheet-body {
            position: relative;
            z-index: 10;

            margin-top: 0;

            overflow: visible;

            border: 0;
            border-radius: 0;

            background: transparent !important;

            padding:
                8px 14px 16px;

            box-shadow: none;
        }

        .character-sheet-body::before {
            display: none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | GRID PRINCIPAL
        |--------------------------------------------------------------------------
        */

        .character-sheet-main-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr);

            align-items: start !important;

            gap:
                12px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | COLUNA PRINCIPAL
        |--------------------------------------------------------------------------
        */

        .character-sheet-main-column {
            display: flex;

            min-width: 0;
            min-height: 100%;

            flex-direction: column;

            gap:
                12px !important;

            padding: 0;

            border: 0 !important;
            border-left: 0 !important;

            background:
                transparent !important;
        }


        /*
        |--------------------------------------------------------------------------
        | MÓDULO BASE
        |--------------------------------------------------------------------------
        */

        .character-sheet-abilities,
        .character-sheet-attacks,
        .character-sheet-features,
        .character-sheet-traits-feats,
        .character-sheet-bottom-cell {
            position: relative;

            min-width: 0;

            overflow: visible;

            border:
                1px solid
                rgba(176,140,98,.34) !important;

            border-radius:
                12px !important;

            background:
                linear-gradient(
                    180deg,
                    rgba(255,253,248,.965) 0%,
                    rgba(248,242,233,.94) 100%
                ) !important;

            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.78),
                0 3px 10px rgba(83,21,15,.040) !important;
        }


        /*
        |--------------------------------------------------------------------------
        | ATRIBUTOS
        |--------------------------------------------------------------------------
        */

        .character-sheet-abilities {
            z-index: 32;

            padding:
                15px 14px 18px 16px !important;
        }

        .character-sheet-abilities::before {
            content: '';

            position: absolute;

            top: 18px;
            bottom: 18px;
            left: 7px;

            width: 2px;

            border-radius: 999px;

            background:
                linear-gradient(
                    180deg,
                    transparent,
                    rgba(107,29,20,.14) 10%,
                    rgba(107,29,20,.14) 90%,
                    transparent
                ) !important;
        }

        /*
        | O character-abilities recebeu anteriormente um -mt próprio.
        | Agora o SHOW move o módulo completo, portanto neutralizamos
        | qualquer margem negativa aplicada ao partial.
        */

        .character-sheet-abilities > .character-stats-sheet {
            margin-top:
                0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | ATAQUES
        |--------------------------------------------------------------------------
        */

        .character-sheet-attacks {
            padding:
                15px 17px 14px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | HABILIDADES
        |--------------------------------------------------------------------------
        */

        .character-sheet-features {
            padding:
                15px 17px 14px !important;
        }

        .character-sheet-features::before {
            display:
                none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | TRAÇOS + TALENTOS
        |--------------------------------------------------------------------------
        */

        .character-sheet-traits-feats {
            padding:
                13px 17px 15px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTEÚDO DOS PARTIALS
        |--------------------------------------------------------------------------
        */

        .character-sheet-abilities > *,
        .character-sheet-attacks > *,
        .character-sheet-features > *,
        .character-sheet-traits-feats > * {
            width:
                100%;

            border:
                0 !important;

            border-radius:
                0 !important;

            background:
                transparent !important;

            box-shadow:
                none !important;
        }

        .character-sheet-attacks > *,
        .character-sheet-features > *,
        .character-sheet-traits-feats > * {
            min-height:
                0 !important;

            height:
                auto !important;
        }


        /*
        |--------------------------------------------------------------------------
        | TREINAMENTO ABAIXO DOS ATRIBUTOS
        |--------------------------------------------------------------------------
        */

        .character-sheet-training-under-abilities {
            margin-top:
                12px;

            border-top:
                1px solid
                rgba(151,116,77,.22);

            padding-top:
                12px;
        }

        .character-sheet-training-under-abilities > * {
            width:
                100%;
        }


        /*
        |--------------------------------------------------------------------------
        | PARTE INFERIOR
        |--------------------------------------------------------------------------
        */

        .character-sheet-bottom-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr);

            gap:
                12px !important;

            margin:
                12px 14px 18px;

            border-top:
                0 !important;

            background:
                transparent !important;
        }

        .character-sheet-bottom-cell {
            padding:
                17px 18px 18px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | DESKTOP
        |--------------------------------------------------------------------------
        */

        @media (min-width: 1024px) {

            .character-sheet-main-grid {
                grid-template-columns:
                    322px
                    minmax(0, 1fr);
            }

            /*
            | A faixa de defesas ocupa aproximadamente essa altura.
            | O módulo de Atributos sobe inteiro e aproveita o espaço esquerdo.
            */

            .character-sheet-abilities {
                margin-top:
                    -110px !important;
            }

            .character-sheet-bottom-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }

        }


        @media (min-width: 1280px) {

            .character-sheet-main-grid {
                grid-template-columns:
                    316px
                    minmax(0, 1fr);
            }

        }


        /*
        |--------------------------------------------------------------------------
        | TABLET
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1023px) {

            .character-sheet-body {
                padding:
                    10px 10px 14px;
            }

            .character-sheet-abilities {
                margin-top:
                    0 !important;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 640px) {

            .character-sheet-body {
                padding:
                    8px 8px 12px;
            }

            .character-sheet-main-grid,
            .character-sheet-bottom-grid {
                gap:
                    10px !important;
            }

            .character-sheet-abilities {
                padding:
                    13px 10px 15px 12px !important;
            }

            .character-sheet-attacks,
            .character-sheet-features,
            .character-sheet-traits-feats,
            .character-sheet-bottom-cell {
                padding:
                    13px 12px !important;
            }

            .character-sheet-bottom-grid {
                margin:
                    10px 8px 14px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | V15 — RITMO DOS MÓDULOS
        |--------------------------------------------------------------------------
        |
        | Uma única medida governa os vazios externos da ficha:
        |
        | - Hero -> Defesas / Atributos
        | - Atributos -> coluna direita
        | - Defesas -> Ataques
        | - Ataques -> Habilidades
        | - Habilidades -> próximos módulos
        |
        | Assim, o quadriculado aparece sempre com o mesmo "respiro".
        |
        */

        .character-sheet-page {
            --sheet-module-gap:
                12px;

            --sheet-module-gutter:
                14px;

            --sheet-defenses-height:
                108px;
        }


        /*
        |--------------------------------------------------------------------------
        | DEFESAS — MESMA MALHA DO CORPO
        |--------------------------------------------------------------------------
        */

        .character-sheet-hero-defenses {
            width:
                100%;

            padding:
                var(--sheet-module-gap)
                var(--sheet-module-gutter)
                0 !important;
        }

        .character-sheet-hero-defenses > * {
            width:
                100% !important;

            min-width:
                0;

            margin:
                0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | CORPO
        |--------------------------------------------------------------------------
        */

        .character-sheet-body {
            padding:
                var(--sheet-module-gap)
                var(--sheet-module-gutter)
                16px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | GAPS INTERNOS ENTRE MÓDULOS
        |--------------------------------------------------------------------------
        */

        .character-sheet-main-grid,
        .character-sheet-main-column {
            gap:
                var(--sheet-module-gap) !important;
        }

        .character-sheet-bottom-grid {
            gap:
                var(--sheet-module-gap) !important;

            margin:
                var(--sheet-module-gap)
                var(--sheet-module-gutter)
                18px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | DESKTOP
        |--------------------------------------------------------------------------
        */

        @media (min-width: 1024px) {

            /*
            | A coluna esquerda mede 322px.
            |
            | Início da coluna direita:
            | gutter 14 + atributos 322 + gap 12.
            */

            .character-sheet-hero-defenses {
                display:
                    grid;

                grid-template-columns:
                    calc(
                        var(--sheet-module-gutter)
                        + 322px
                        + var(--sheet-module-gap)
                    )
                    minmax(0, 1fr);

                padding:
                    var(--sheet-module-gap)
                    var(--sheet-module-gutter)
                    0
                    0 !important;
            }

            .character-sheet-hero-defenses > * {
                grid-column:
                    2;
            }

            /*
            | Defesas tem altura estável de 92px.
            |
            | Atributos sobe a altura de Defesas + o gap inferior de 12px,
            | ficando exatamente alinhado ao topo de Defesas.
            */

            .character-sheet-abilities {
                margin-top:
                    calc(
                        -1
                        * (
                            var(--sheet-defenses-height)
                            + var(--sheet-module-gap)
                        )
                    ) !important;
            }

        }


        @media (min-width: 1280px) {

            /*
            | Em telas grandes a coluna de Atributos mede 316px.
            */

            .character-sheet-hero-defenses {
                grid-template-columns:
                    calc(
                        var(--sheet-module-gutter)
                        + 316px
                        + var(--sheet-module-gap)
                    )
                    minmax(0, 1fr);
            }

        }


        /*
        |--------------------------------------------------------------------------
        | TABLET / MOBILE
        |--------------------------------------------------------------------------
        |
        | Não existe sobreposição entre Defesas e Atributos.
        | Mantemos apenas o mesmo ritmo de 10px em telas menores.
        |
        */

        @media (max-width: 1023px) {

            .character-sheet-page {
                --sheet-module-gap:
                    10px;

                --sheet-module-gutter:
                    10px;
            }

            .character-sheet-hero-defenses {
                padding:
                    var(--sheet-module-gap)
                    var(--sheet-module-gutter)
                    0 !important;
            }

            .character-sheet-body {
                padding:
                    var(--sheet-module-gap)
                    var(--sheet-module-gutter)
                    14px !important;
            }

            .character-sheet-abilities {
                margin-top:
                    0 !important;
            }

        }



        /*
        |--------------------------------------------------------------------------
        | V16 — DEFESAS OPCIONAIS
        |--------------------------------------------------------------------------
        |
        | Quando o módulo não é renderizado, removemos o deslocamento usado
        | para alinhar Atributos ao topo de Defesas.
        |
        | Resultado:
        |
        | COM DEFESAS:
        | Atributos | Defesas
        |           | Ataques
        |
        | SEM DEFESAS:
        | Atributos | Ataques
        |
        | Em ambos os casos o primeiro módulo começa 12px abaixo do Hero.
        |
        */

        @media (min-width: 1024px) {

            .character-sheet-page--without-defenses
            .character-sheet-abilities {
                margin-top:
                    0 !important;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | V20 — CORREÇÃO DA CAMADA HERO / DEFESAS SOBRE ATRIBUTOS
        |--------------------------------------------------------------------------
        |
        | No desktop, Atributos sobe com margin-top negativo para ocupar a coluna
        | esquerda ao lado de Defesas. Porém o wrapper do Hero continua cobrindo
        | toda essa faixa e, como o Hero está em uma camada acima do corpo,
        | a área transparente intercepta os cliques.
        |
        | O Hero deixa de capturar eventos por si só. Reativamos eventos apenas
        | nos seus conteúdos reais:
        | - header principal;
        | - card real de Defesas.
        |
        | A área vazia à esquerda de Defesas passa a deixar o clique chegar nos
        | atributos que estão visualmente abaixo dela.
        |--------------------------------------------------------------------------
        */

        .character-sheet-hero {
            pointer-events: none;
        }

        .character-sheet-hero
        > :not(.character-sheet-hero-defenses) {
            pointer-events: auto;
        }

        .character-sheet-hero-defenses {
            pointer-events: none !important;
        }

        .character-sheet-hero-defenses > * {
            pointer-events: auto !important;
        }


        /* FIM V14 */

    

        /*
        |--------------------------------------------------------------------------
        | V21 — AJUSTES FINAIS DE ATRIBUTOS / SCROLL
        |--------------------------------------------------------------------------
        |
        | 1. Aproxima a linha do título "Atributos" dos cards.
        | 2. Remove o filete vertical decorativo à esquerda.
        | 3. Esconde a barra de rolagem da página sem bloquear wheel/touch.
        |
        */

        .character-sheet-abilities
        .character-stats-v2-header {
            margin-bottom:
                0 !important;

            padding-bottom:
                4px !important;
        }

        .character-sheet-abilities
        .character-stats-v2-grid {
            margin-top:
                -4px !important;
        }

        .character-sheet-abilities::before {
            display:
                none !important;

            content:
                none !important;
        }

        html,
        body {
            scrollbar-width:
                none;

            -ms-overflow-style:
                none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width:
                0;

            height:
                0;

            display:
                none;
        }



        /*
        |--------------------------------------------------------------------------
        | V22 — AJUSTE FINO: LINHA DOS ATRIBUTOS + SCROLLBAR
        |--------------------------------------------------------------------------
        */

        /* Aproxima mais os cards da linha inferior do título. */
        .character-sheet-abilities
        .character-stats-v2-header {
            margin-bottom:
                0 !important;

            padding-bottom:
                3px !important;
        }

        .character-sheet-abilities
        .character-stats-v2-grid {
            margin-top:
                -7px !important;
        }

        /* Remove definitivamente o filete vertical esquerdo. */
        .character-sheet-abilities::before {
            display:
                none !important;

            content:
                none !important;
        }

        /*
         * A barra visível da captura não está necessariamente no body;
         * pode ser um wrapper rolável do layout.
         *
         * Nesta página escondemos a scrollbar de qualquer elemento,
         * sem bloquear wheel, touchpad, teclado ou touch.
         */
        * {
            scrollbar-width:
                none !important;

            -ms-overflow-style:
                none !important;
        }

        *::-webkit-scrollbar {
            width:
                0 !important;

            height:
                0 !important;

            display:
                none !important;
        }



        /*
        |--------------------------------------------------------------------------
        | V23 — DEFESAS UNIFICADAS
        |--------------------------------------------------------------------------
        |
        | O partial de Defesas passou de 92px para 108px para comportar
        | cabeçalho + quadro interno no mesmo padrão visual de Ataques/Habilidades.
        | A variável acima mantém o alinhamento vertical dos Atributos.
        |
        */



        /*
        |--------------------------------------------------------------------------
        | V24 — RESPIRO PARA AS ABAS LATERAIS
        |--------------------------------------------------------------------------
        |
        | Reserva uma faixa própria para Party / Inventário / Grimório no desktop.
        | As abas deixam de encostar na moldura da ficha e passam a flutuar com
        | uma pequena distância visual do canvas.
        |
        */

        .character-side-tabs {
            right: 0;
            align-items: flex-end;
        }

        .character-side-tab {
            border-right:
                1px solid
                rgba(205,187,159,.88) !important;

            border-radius:
                14px !important;
        }

        .character-side-tab:hover {
            padding-right:
                .75rem;

            transform:
                translateX(-2px);
        }

        @media (min-width: 1024px) {
            .character-sheet-page {
                /*
                 * Reserva apenas o suficiente para o trilho lateral.
                 * O card da Loja fica solto da moldura, sem criar um corredor
                 * vazio exagerado entre a ficha e as abas.
                 */
                padding-right:
                    88px !important;
            }

            .character-side-tabs {
                right:
                    8px !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | V30 — SUPERFÍCIES CONTROLADAS PELO THEME CORE
        |--------------------------------------------------------------------------
        |
        | O Show acumulou versões visuais antigas (V7/V8/V9) que ainda
        | forçavam gradientes claros com !important. Isso fazia temas escuros
        | ficarem divididos: Hero escuro + corpo claro.
        |
        | Este bloco fica por último de propósito e passa a autoridade das
        | superfícies estruturais para resources/css/character-sheet.css.
        |--------------------------------------------------------------------------
        */

        .character-sheet-theme .character-sheet-main-column {
            background:
                transparent !important;
        }

        .character-sheet-theme .character-sheet-abilities {
            background:
                var(
                    --theme-module-alt-background
                ) !important;

            border-color:
                var(
                    --theme-border
                ) !important;
        }

        .character-sheet-theme .character-sheet-abilities::before {
            background:
                linear-gradient(
                    180deg,
                    transparent,
                    color-mix(
                        in srgb,
                        var(--theme-accent) 25%,
                        transparent
                    ) 10%,
                    color-mix(
                        in srgb,
                        var(--theme-accent) 25%,
                        transparent
                    ) 90%,
                    transparent
                ) !important;
        }

        .character-sheet-theme .character-sheet-attacks {
            background:
                var(
                    --theme-module-background
                ) !important;
        }

        .character-sheet-theme .character-sheet-features {
            background:
                var(
                    --theme-module-alt-background
                ) !important;
        }

        .character-sheet-theme .character-sheet-bottom-grid {
            border-top-color:
                var(
                    --theme-border
                ) !important;

            background:
                var(
                    --theme-module-alt-background
                ) !important;
        }

        .character-sheet-theme .character-sheet-bottom-cell {
            background:
                transparent !important;
        }

        .character-sheet-theme
        .character-sheet-training-under-abilities {
            border-top-color:
                var(
                    --theme-border
                ) !important;
        }

        @media (min-width: 1024px) {
            .character-sheet-theme .character-sheet-abilities {
                border-right-color:
                    var(
                        --theme-border
                    ) !important;
            }

            .character-sheet-theme
            .character-sheet-bottom-cell
            +
            .character-sheet-bottom-cell {
                border-left-color:
                    var(
                        --theme-border
                    ) !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | V33 — ALTURA GUIADA PELOS ATRIBUTOS / SLOTS VISUAIS SINCRONIZADOS
        |--------------------------------------------------------------------------
        |
        | A coluna esquerda é a referência real de altura da ficha.
        | Ela nunca recebe scroll interno nem altura artificial.
        |
        | Na coluna direita:
        | - Ataques fica no topo;
        | - Habilidades reserva 4 linhas por coluna;
        | - Traços & Talentos fica ancorado no rodapé;
        | - Traços e Talentos reservam 3 linhas por coluna;
        | - somente o conteúdo que ultrapassar esses limites rola internamente.
        |
        | As duas colunas de Habilidades rolam de forma independente. Isso evita
        | que uma lista longa mova a outra e mantém o divisor central exatamente
        | alinhado com a faixa ESQUERDA / DIREITA.
        |
        */

        .character-sheet-page {
            --sheet-feature-row-height:
                92px;

            --sheet-features-window-height:
                368px;

            --sheet-traits-row-height:
                92px;

            --sheet-traits-window-height:
                276px;
        }


        @media (min-width: 1024px) {

            /* ================================================================
               COLUNA ESQUERDA = LIMITADOR REAL DA FICHA
            ================================================================= */

            .character-sheet-main-grid {
                height:
                    auto !important;

                min-height:
                    0 !important;

                align-items:
                    stretch !important;
            }


            .character-sheet-abilities {
                height:
                    auto !important;

                min-height:
                    0 !important;

                max-height:
                    none !important;

                overflow:
                    visible !important;
            }


            .character-sheet-main-column {
                display:
                    flex !important;

                min-width:
                    0;

                min-height:
                    100% !important;

                height:
                    auto !important;

                align-self:
                    stretch !important;

                flex-direction:
                    column !important;

                overflow:
                    visible !important;
            }


            .character-sheet-attacks,
            .character-sheet-features,
            .character-sheet-traits-feats {
                flex:
                    0 0 auto;
            }


            /*
             * Tudo que sobrar entre Habilidades e o fim da coluna esquerda
             * vira respiro acima do rodapé. Traços & Talentos permanece sempre
             * encostado na base definida por Atributos.
             */
            .character-sheet-traits-feats {
                margin-top:
                    auto !important;
            }


            /* ================================================================
               HABILIDADES — 4 SLOTS POR COLUNA
            ================================================================= */

            /*
             * O wrapper geral deixa de rolar. Ele apenas define a janela.
             * Quem rola agora é cada coluna individualmente.
             */
            .character-sheet-features
            .character-features-v12-scroll {
                position:
                    relative;

                height:
                    var(--sheet-features-window-height) !important;

                min-height:
                    var(--sheet-features-window-height) !important;

                max-height:
                    var(--sheet-features-window-height) !important;

                overflow:
                    hidden !important;

                scrollbar-gutter:
                    auto !important;
            }


            .character-sheet-features
            .character-features-v12-scroll
            > .character-features-v9-columns {
                position:
                    relative;

                height:
                    100% !important;

                min-height:
                    0 !important;

                align-items:
                    stretch !important;
            }


            /*
             * Divisor central independente do conteúdo e das scrollbars.
             * Assim ele começa e termina exatamente no centro da janela.
             */
            .character-sheet-features
            .character-features-v12-scroll::after {
                content:
                    '';

                position:
                    absolute;

                z-index:
                    8;

                top:
                    0;

                bottom:
                    0;

                left:
                    50%;

                width:
                    1px;

                pointer-events:
                    none;

                background:
                    var(--theme-border) !important;
            }


            /*
             * O divisor antigo dependia da altura do conteúdo da coluna direita.
             * Agora a linha central acima é a única autoridade.
             */
            .character-sheet-features
            .character-features-v9-column
            +
            .character-features-v9-column {
                border-left:
                    0 !important;
            }


            .character-sheet-features
            .character-features-v9-column {
                position:
                    relative;

                height:
                    100% !important;

                min-height:
                    0 !important;

                max-height:
                    100% !important;

                overflow-x:
                    hidden !important;

                overflow-y:
                    auto !important;

                overscroll-behavior:
                    contain;

                scrollbar-gutter:
                    auto;

                scrollbar-width:
                    thin !important;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 48%,
                        transparent
                    )
                    color-mix(
                        in srgb,
                        var(--theme-border) 28%,
                        transparent
                    ) !important;

                /*
                 * As linhas permanecem visíveis mesmo quando existem menos de
                 * quatro registros, transformando o vazio em slots da ficha.
                 */
                background:
                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent calc(
                            var(--sheet-feature-row-height)
                            - 1px
                        ),
                        var(--theme-border-soft) calc(
                            var(--sheet-feature-row-height)
                            - 1px
                        ),
                        var(--theme-border-soft)
                        var(--sheet-feature-row-height)
                    ),
                    var(--theme-card-background) !important;
            }


            .character-sheet-features
            .character-feature-v9-card,
            .character-sheet-features
            .character-features-v9-empty {
                box-sizing:
                    border-box !important;

                height:
                    var(--sheet-feature-row-height) !important;

                min-height:
                    var(--sheet-feature-row-height) !important;

                max-height:
                    var(--sheet-feature-row-height) !important;
            }


            /*
             * O estado vazio do partial não deve ocupar as quatro linhas.
             * Ele representa somente o primeiro slot; os demais permanecem
             * desenhados pelo fundo quadriculado da coluna.
             */
            .character-sheet-features
            .character-features-v9-empty {
                align-items:
                    center !important;

                justify-content:
                    center !important;
            }


            /* Scrollbar independente de cada lado. */
            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar {
                display:
                    block !important;

                width:
                    6px !important;

                height:
                    6px !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-track {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-border) 18%,
                        transparent
                    ) !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-thumb {
                border:
                    1px solid transparent;

                border-radius:
                    999px;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 50%,
                        transparent
                    ) !important;

                background-clip:
                    padding-box !important;
            }


            /* ================================================================
               TRAÇOS & TALENTOS — 3 SLOTS POR COLUNA
            ================================================================= */

            .character-sheet-traits-feats
            .character-traits-feats-v6-columns {
                position:
                    relative;

                align-items:
                    stretch !important;
            }


            /* Linha central única, seguindo a mesma regra de Habilidades. */
            .character-sheet-traits-feats
            .character-traits-feats-v6-columns::after {
                content:
                    '';

                position:
                    absolute;

                z-index:
                    8;

                top:
                    0;

                bottom:
                    0;

                left:
                    50%;

                width:
                    1px;

                pointer-events:
                    none;

                background:
                    var(--theme-border) !important;
            }


            .character-sheet-traits-feats
            .character-traits-feats-v6-column
            +
            .character-traits-feats-v6-column {
                border-left:
                    0 !important;
            }


            .character-sheet-traits-feats
            .character-traits-feats-v6-list {
                height:
                    var(--sheet-traits-window-height) !important;

                min-height:
                    var(--sheet-traits-window-height) !important;

                max-height:
                    var(--sheet-traits-window-height) !important;

                overflow-x:
                    hidden !important;

                overflow-y:
                    auto !important;

                overscroll-behavior:
                    contain;

                scrollbar-gutter:
                    auto;

                scrollbar-width:
                    thin !important;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 48%,
                        transparent
                    )
                    color-mix(
                        in srgb,
                        var(--theme-border) 28%,
                        transparent
                    ) !important;

                /* Três slots visíveis mesmo com poucos registros. */
                background:
                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent calc(
                            var(--sheet-traits-row-height)
                            - 1px
                        ),
                        var(--theme-border-soft) calc(
                            var(--sheet-traits-row-height)
                            - 1px
                        ),
                        var(--theme-border-soft)
                        var(--sheet-traits-row-height)
                    ),
                    var(--theme-card-background) !important;
            }


            .character-sheet-traits-feats
            .character-mini-feature-row,
            .character-sheet-traits-feats
            .character-traits-feats-v6-empty {
                box-sizing:
                    border-box !important;

                height:
                    var(--sheet-traits-row-height) !important;

                min-height:
                    var(--sheet-traits-row-height) !important;

                max-height:
                    var(--sheet-traits-row-height) !important;
            }


            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar {
                display:
                    block !important;

                width:
                    6px !important;

                height:
                    6px !important;
            }


            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-track {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-border) 18%,
                        transparent
                    ) !important;
            }


            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-thumb {
                border:
                    1px solid transparent;

                border-radius:
                    999px;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 50%,
                        transparent
                    ) !important;

                background-clip:
                    padding-box !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | V34 — LARGURA FLUIDA + TRAÇOS COESOS + SCROLL DISCRETO
        |--------------------------------------------------------------------------
        |
        | Este bloco é a autoridade final do layout da coluna direita no desktop.
        |
        | - módulos ocupam toda a largura disponível;
        | - Traços & Talentos usa a mesma superfície de Habilidades;
        | - scroll não cria faixa branca nem botões nativos nas extremidades;
        | - thumb permanece muito discreto e aparece com mais contraste no hover;
        | - nenhuma largura fixa é criada dentro das duas colunas.
        |
        */

        @media (min-width: 1024px) {

            /* ================================================================
               LARGURA FLUIDA — COLUNA DIREITA
            ================================================================= */

            .character-sheet-main-column,
            .character-sheet-attacks,
            .character-sheet-features,
            .character-sheet-traits-feats {
                width:
                    100% !important;

                max-width:
                    none !important;

                min-width:
                    0 !important;
            }


            .character-sheet-features
            > .character-features-v11,
            .character-sheet-features
            .character-features-v11,
            .character-sheet-features
            .character-features-v11-table-wrap,
            .character-sheet-features
            .character-features-v12-scroll,
            .character-sheet-features
            .character-features-v9-columns,
            .character-sheet-traits-feats
            > .character-traits-feats-v6,
            .character-sheet-traits-feats
            .character-traits-feats-v6-table,
            .character-sheet-traits-feats
            .character-traits-feats-v6-columns {
                width:
                    100% !important;

                max-width:
                    none !important;

                min-width:
                    0 !important;

                margin-inline:
                    0 !important;
            }


            .character-sheet-features
            .character-features-v9-columns,
            .character-sheet-traits-feats
            .character-traits-feats-v6-columns {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
            }


            .character-sheet-features
            .character-features-v9-column,
            .character-sheet-traits-feats
            .character-traits-feats-v6-column {
                width:
                    auto !important;

                max-width:
                    none !important;

                min-width:
                    0 !important;
            }


            .character-sheet-features
            .character-features-v10-column-bar {
                width:
                    100% !important;

                max-width:
                    none !important;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
            }


            /* ================================================================
               TRAÇOS & TALENTOS — MESMA SUPERFÍCIE DE HABILIDADES
            ================================================================= */

            .character-sheet-theme
            .character-sheet-traits-feats {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-module-alt-background) !important;

                box-shadow:
                    var(--theme-module-shadow) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6-table {
                border-color:
                    var(--theme-border) !important;

                background:
                    var(--theme-card-background) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6-column-head {
                border-bottom-color:
                    var(--theme-border-strong) !important;

                background:
                    var(--theme-band-background) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6-list {
                background-color:
                    var(--theme-card-background) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6
            .character-mini-feature-row {
                background:
                    var(--theme-card-background) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6
            .character-mini-feature-row:nth-child(even) {
                background:
                    var(--theme-card-alt-background) !important;
            }


            .character-sheet-theme
            .character-traits-feats-v6
            .character-mini-feature-quick {
                border-left-color:
                    var(--theme-accent) !important;

                background:
                    var(--theme-inset-background) !important;
            }


            /* ================================================================
               SCROLL INTERNO — FINO, SEM SETAS E SEM FAIXA VISÍVEL
            ================================================================= */

            .character-sheet-features
            .character-features-v9-column,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list {
                scrollbar-gutter:
                    auto !important;

                scrollbar-width:
                    thin !important;

                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 20%,
                        transparent
                    )
                    transparent !important;
            }


            .character-sheet-features
            .character-features-v9-column:hover,
            .character-sheet-features
            .character-features-v9-column:focus-within,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list:hover,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list:focus-within {
                scrollbar-color:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 34%,
                        transparent
                    )
                    transparent !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar {
                display:
                    block !important;

                width:
                    3px !important;

                height:
                    3px !important;

                background:
                    transparent !important;
            }


            /* Windows/Chromium pode desenhar setas nas pontas do scrollbar. */
            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-button,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-button {
                display:
                    none !important;

                width:
                    0 !important;

                height:
                    0 !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-track,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-track,
            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-track-piece,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-track-piece,
            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-corner,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-corner {
                background:
                    transparent !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-thumb,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-thumb {
                border:
                    0 !important;

                border-radius:
                    999px !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 18%,
                        transparent
                    ) !important;
            }


            .character-sheet-features
            .character-features-v9-column:hover::-webkit-scrollbar-thumb,
            .character-sheet-features
            .character-features-v9-column:focus-within::-webkit-scrollbar-thumb,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list:hover::-webkit-scrollbar-thumb,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list:focus-within::-webkit-scrollbar-thumb {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 34%,
                        transparent
                    ) !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar-thumb:hover,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar-thumb:hover {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 48%,
                        transparent
                    ) !important;
            }
        }



        /*
        |--------------------------------------------------------------------------
        | V35 — SCROLL INVISÍVEL + INDICADOR DISCRETO DE CONTINUIDADE
        |--------------------------------------------------------------------------
        |
        | A rolagem continua funcionando normalmente, mas nenhuma barra fica
        | visível. Quando ainda existe conteúdo abaixo da janela, o JavaScript
        | adiciona uma pequena seta temática no rodapé da respectiva coluna.
        |
        */

        @media (min-width: 1024px) {

            .character-sheet-features
            .character-features-v9-column,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list {
                scrollbar-width:
                    none !important;

                -ms-overflow-style:
                    none !important;

                scrollbar-gutter:
                    auto !important;
            }


            .character-sheet-features
            .character-features-v9-column::-webkit-scrollbar,
            .character-sheet-traits-feats
            .character-traits-feats-v6-list::-webkit-scrollbar {
                display:
                    none !important;

                width:
                    0 !important;

                height:
                    0 !important;

                background:
                    transparent !important;
            }


            /*
             * O indicador é inserido fora do elemento rolável. Assim ele não
             * ocupa largura, não cria faixa lateral e não interfere no divisor.
             */
            .sheet-scroll-more-hint {
                position:
                    absolute;

                z-index:
                    40;

                bottom:
                    5px;

                display:
                    flex;

                width:
                    18px;

                height:
                    14px;

                align-items:
                    center;

                justify-content:
                    center;

                pointer-events:
                    none;

                opacity:
                    0;

                transform:
                    translate(-50%, 2px);

                transition:
                    opacity .16s ease,
                    transform .16s ease;
            }


            .sheet-scroll-more-hint::before {
                content:
                    '';

                width:
                    6px;

                height:
                    6px;

                border-right:
                    1.5px solid
                    var(--theme-label);

                border-bottom:
                    1.5px solid
                    var(--theme-label);

                transform:
                    translateY(-2px)
                    rotate(45deg);

                opacity:
                    .52;

                filter:
                    drop-shadow(
                        0 1px 0
                        color-mix(
                            in srgb,
                            var(--theme-paper-strong) 72%,
                            transparent
                        )
                    );
            }


            .sheet-scroll-more-hint.is-visible {
                opacity:
                    1;

                transform:
                    translate(-50%, 0);
            }


            .sheet-scroll-more-hint--left {
                left:
                    25%;
            }


            .sheet-scroll-more-hint--right {
                left:
                    75%;
            }


            /*
             * Suave halo de papel apenas atrás da seta. Ele serve para a seta
             * permanecer legível sobre texto sem virar um botão ou um scrollbar.
             */
            .sheet-scroll-more-hint.is-visible::after {
                content:
                    '';

                position:
                    absolute;

                z-index:
                    -1;

                inset:
                    -2px -4px;

                border-radius:
                    999px;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-card-background) 74%,
                        transparent
                    );

                box-shadow:
                    0 0 7px
                    color-mix(
                        in srgb,
                        var(--theme-card-background) 82%,
                        transparent
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | V36 — HABILIDADES: LEITURA, TRACKERS E VAZIO LIMPO
        |--------------------------------------------------------------------------
        |
        | - compacta o cabeçalho para a descrição rápida respirar dentro dos 92px;
        | - centraliza verticalmente o texto da descrição rápida;
        | - dá presença visual aos rastreadores sem fugir do tema;
        | - mantém o espaço reservado, mas remove as linhas dos slots vazios;
        | - aplica o mesmo vazio limpo em Traços & Talentos.
        |
        */

        @media (min-width: 1024px) {

            /* ================================================================
               HABILIDADES — DESCRIÇÃO RÁPIDA CENTRALIZADA
            ================================================================= */

            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-head {
                padding:
                    8px 10px 4px !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-meta {
                margin-top:
                    2px !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-quick-wrap {
                display:
                    flex !important;

                min-height:
                    0 !important;

                flex:
                    1 1 auto !important;

                align-items:
                    stretch !important;

                padding:
                    0 10px 8px !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-quick,
            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-quick-empty {
                display:
                    flex !important;

                width:
                    100% !important;

                min-height:
                    31px !important;

                align-items:
                    center !important;

                margin:
                    0 !important;

                padding:
                    5px 9px !important;

                overflow:
                    hidden !important;

                line-height:
                    1.28 !important;
            }


            /* ================================================================
               HABILIDADES — TRACKER COM COR E PROFUNDIDADE
            ================================================================= */

            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-tracker {
                height:
                    31px !important;

                overflow:
                    hidden !important;

                border:
                    1px solid
                    var(--theme-border-strong) !important;

                border-radius:
                    8px !important;

                background:
                    var(--theme-paper-strong) !important;

                box-shadow:
                    inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 76%,
                        white 24%
                    ),
                    0 2px 5px
                    color-mix(
                        in srgb,
                        var(--theme-text-strong) 13%,
                        transparent
                    ) !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-tracker
            button {
                width:
                    29px !important;

                min-width:
                    29px !important;

                height:
                    100% !important;

                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent-soft) 72%,
                        var(--theme-paper-strong) 28%
                    ) !important;

                color:
                    var(--theme-accent-strong) !important;

                text-shadow:
                    0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 80%,
                        transparent
                    );

                box-shadow:
                    inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 75%,
                        transparent
                    ) !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-tracker
            button:hover:not(:disabled) {
                background:
                    color-mix(
                        in srgb,
                        var(--theme-accent) 25%,
                        var(--theme-paper-strong) 75%
                    ) !important;

                color:
                    var(--theme-text-strong) !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-tracker
            button:disabled {
                opacity:
                    .38 !important;
            }


            .character-sheet-theme
            .character-sheet-features
            .character-feature-v9-tracker
            strong {
                min-width:
                    43px !important;

                height:
                    100% !important;

                background:
                    var(--theme-surface-raised) !important;

                color:
                    var(--theme-text-strong) !important;

                box-shadow:
                    inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 62%,
                        transparent
                    ) !important;
            }


            /* ================================================================
               ESPAÇO RESERVADO — SEM RISCOS NOS SLOTS VAZIOS
            ================================================================= */

            .character-sheet-theme
            .character-sheet-features
            .character-features-v9-column {
                background:
                    var(--theme-card-background) !important;
            }


            .character-sheet-theme
            .character-sheet-traits-feats
            .character-traits-feats-v6-list {
                background:
                    var(--theme-card-background) !important;
            }


            /*
             * Se uma coluna estiver totalmente vazia, o placeholder do partial
             * continua centralizado, mas sem desenhar uma linha de "slot".
             */
            .character-sheet-theme
            .character-sheet-features
            .character-features-v9-empty,
            .character-sheet-theme
            .character-sheet-traits-feats
            .character-traits-feats-v6-empty {
                border-bottom:
                    0 !important;

                background:
                    transparent !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | V31 — DARK THEMES / MÓDULOS SEM REFLEXO BRANCO
        |--------------------------------------------------------------------------
        |
        | A V7/V8 do Show ainda aplica em todos os módulos:
        | inset 0 1px 0 rgba(255,255,255,.78) !important.
        |
        | Como esse CSS vive dentro do próprio Show, ele pode vencer ajustes do
        | arquivo global. Nos temas escuros removemos o inset e usamos somente
        | sombra escura externa.
        |
        */

        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-abilities,
        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-attacks,
        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-features,
        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-traits-feats,
        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-bottom-cell {
            box-shadow:
                0 5px 14px rgba(0,0,0,.22) !important;
        }

        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-abilities,
        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-attacks,
        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-features,
        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-traits-feats,
        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-bottom-cell {
            border-color:
                rgba(151,162,173,.24) !important;
        }

        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-abilities,
        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-attacks,
        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-features,
        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-traits-feats,
        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-bottom-cell {
            border-color:
                rgba(154,122,102,.24) !important;
        }

        /* O canvas também perde o reflexo superior branco nos temas escuros. */
        .character-sheet-theme:is(
            [data-theme="midnight"],
            [data-theme="obsidian"]
        )
        .character-sheet-canvas {
            box-shadow:
                0 12px 28px rgba(0,0,0,.24) !important;
        }

        /* Remove o glow branco radial do papel apenas nos dark themes. */
        .character-sheet-theme[data-theme="midnight"]
        .character-sheet-page {
            background:
                radial-gradient(
                    circle at 18% 3%,
                    rgba(111,140,166,.045),
                    transparent 28%
                ),
                linear-gradient(
                    180deg,
                    var(--theme-page-bg-start) 0%,
                    var(--theme-page-bg-end) 100%
                ) !important;
        }

        .character-sheet-theme[data-theme="obsidian"]
        .character-sheet-page {
            background:
                radial-gradient(
                    circle at 18% 3%,
                    rgba(183,136,105,.035),
                    transparent 28%
                ),
                linear-gradient(
                    180deg,
                    var(--theme-page-bg-start) 0%,
                    var(--theme-page-bg-end) 100%
                ) !important;
        }


        /*
        |--------------------------------------------------------------------------
        | V34 — LOJA DO PLAYER / ATALHO LATERAL
        |--------------------------------------------------------------------------
        |
        | A loja deixa de ocupar o Hero e passa a viver no mesmo trilho de
        | Party / Inventário / Grimório. O cartão usa exclusivamente tokens
        | do Theme Core para acompanhar Spellbound, Emerald, Obsidian etc.
        |
        */

        .player-shop-side-wrap {
            position: relative;
            display: flex;
            width: auto;
            justify-content: center;
            margin-bottom: 8px;
            transform:
                translate(2px, -16px);
        }

        .player-shop-side-tab {
            position: relative;
            display: flex;
            width: 70px;
            height: 74px;
            flex: 0 0 70px;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            border:
                1px solid
                color-mix(
                    in srgb,
                    var(--theme-accent) 44%,
                    var(--theme-border-strong)
                );
            border-radius: 16px;
            background:
                var(--theme-side-tab-background);
            padding: 8px 7px 8px;
            color: var(--theme-side-tab-text);
            box-shadow:
                0 8px 20px
                    color-mix(
                        in srgb,
                        var(--theme-accent) 16%,
                        transparent
                    ),
                inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 38%,
                        transparent
                    );
            cursor: pointer;
            isolation: isolate;
            animation:
                playerShopSidePulse
                2.35s
                ease-in-out
                infinite;
            transition:
                transform .16s ease,
                border-color .16s ease,
                background .16s ease,
                box-shadow .16s ease;
        }

        .player-shop-side-tab::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 4px;
            z-index: 0;
            background:
                var(--theme-accent);
            opacity: .78;
            pointer-events: none;
        }

        .player-shop-side-tab:hover {
            transform:
                translateX(-2px)
                translateY(-1px);
            border-color:
                color-mix(
                    in srgb,
                    var(--theme-accent) 62%,
                    var(--theme-border-strong)
                );
            background:
                var(--theme-side-tab-hover-background);
            box-shadow:
                0 11px 25px
                    color-mix(
                        in srgb,
                        var(--theme-accent) 23%,
                        transparent
                    ),
                inset 0 1px 0
                    color-mix(
                        in srgb,
                        var(--theme-paper-strong) 44%,
                        transparent
                    );
        }

        .player-shop-side-tab__top {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .player-shop-side-tab__icon {
            position: relative;
            display: grid;
            width: 26px;
            height: 26px;
            flex: 0 0 26px;
            place-items: center;
            border:
                1px solid
                color-mix(
                    in srgb,
                    var(--theme-accent) 42%,
                    var(--theme-border)
                );
            border-radius: 9px;
            background:
                var(--theme-inset-background);
            color:
                var(--theme-accent);
            box-shadow:
                inset 0 1px 0
                color-mix(
                    in srgb,
                    var(--theme-paper-strong) 38%,
                    transparent
                );
        }

        .player-shop-side-tab__icon svg {
            width: 14px;
            height: 14px;
        }

        .player-shop-side-tab__dot {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 7px;
            height: 7px;
            border:
                2px solid
                var(--theme-side-tab-background);
            border-radius: 999px;
            background: #10b981;
            box-sizing: content-box;
            box-shadow:
                0 0 0 2px
                color-mix(
                    in srgb,
                    #10b981 15%,
                    transparent
                );
        }

        .player-shop-side-tab__label {
            color:
                var(--theme-side-tab-text);
            font-size: 7px;
            font-weight: 900;
            letter-spacing: .08em;
            line-height: 1.04;
            text-align: center;
            text-transform: uppercase;
            opacity: .88;
        }

        .player-shop-side-tab__name {
            position: relative;
            z-index: 1;
            display: -webkit-box;
            width: 100%;
            overflow: hidden;
            margin-top: 5px;
            color:
                var(--theme-side-tab-text);
            font-family: Georgia, serif;
            font-size: 10.5px;
            font-weight: 900;
            line-height: 1.03;
            text-align: center;
            overflow-wrap: anywhere;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        @keyframes playerShopSidePulse {
            0%,
            100% {
                box-shadow:
                    0 8px 20px
                        color-mix(
                            in srgb,
                            var(--theme-accent) 13%,
                            transparent
                        ),
                    0 0 0 0
                        color-mix(
                            in srgb,
                            var(--theme-accent) 17%,
                            transparent
                        ),
                    inset 0 1px 0
                        color-mix(
                            in srgb,
                            var(--theme-paper-strong) 36%,
                            transparent
                        );
            }

            50% {
                box-shadow:
                    0 10px 24px
                        color-mix(
                            in srgb,
                            var(--theme-accent) 22%,
                            transparent
                        ),
                    0 0 0 4px
                        color-mix(
                            in srgb,
                            var(--theme-accent) 7%,
                            transparent
                        ),
                    inset 0 1px 0
                        color-mix(
                            in srgb,
                            var(--theme-paper-strong) 40%,
                            transparent
                        );
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .player-shop-side-tab {
                animation: none;
            }
        }

        @media (max-width: 1023px) {
            .player-shop-side-wrap {
                transform:
                    translate(1px, -10px);
            }

            .player-shop-side-tab {
                width: 68px;
                height: 70px;
                flex-basis: 68px;
                border-radius: 14px;
            }

            .player-shop-side-tab__name {
                font-size: 9.8px;
            }
        }

        @media (max-width: 640px) {
            .player-shop-side-wrap {
                transform:
                    translate(0, -6px);
            }

            .player-shop-side-tab {
                width: 64px;
                height: 62px;
                flex-basis: 64px;
                padding: 6px;
                border-radius: 13px;
            }

            .player-shop-side-tab__icon {
                width: 24px;
                height: 24px;
                flex-basis: 24px;
            }

            .player-shop-side-tab__label {
                font-size: 5.8px;
            }

            .player-shop-side-tab__name {
                margin-top: 4px;
                font-size: 8.6px;
                -webkit-line-clamp: 2;
            }
        }

</style>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const configurations = [
            {
                rootSelector:
                    '.character-sheet-features .character-features-v12-scroll',

                scrollerSelector:
                    '.character-features-v9-column',
            },
            {
                rootSelector:
                    '.character-sheet-traits-feats .character-traits-feats-v6-columns',

                scrollerSelector:
                    '.character-traits-feats-v6-list',
            },
        ];


        const bindScrollHints = (root, scrollerSelector) => {
            if (root.dataset.scrollHintsBound === '1') {
                return;
            }

            const scrollers = Array.from(
                root.querySelectorAll(scrollerSelector)
            ).slice(0, 2);

            if (scrollers.length === 0) {
                return;
            }

            root.dataset.scrollHintsBound = '1';

            const hints = [0, 1].map((index) => {
                const hint = document.createElement('span');

                hint.className = [
                    'sheet-scroll-more-hint',
                    index === 0
                        ? 'sheet-scroll-more-hint--left'
                        : 'sheet-scroll-more-hint--right',
                ].join(' ');

                hint.setAttribute('aria-hidden', 'true');

                root.appendChild(hint);

                return hint;
            });


            const update = () => {
                scrollers.forEach((scroller, index) => {
                    const remaining =
                        scroller.scrollHeight
                        - scroller.scrollTop
                        - scroller.clientHeight;

                    const canContinue =
                        scroller.scrollHeight
                        > scroller.clientHeight + 2
                        && remaining > 3;

                    hints[index]
                        ?.classList
                        .toggle(
                            'is-visible',
                            canContinue
                        );
                });
            };


            scrollers.forEach((scroller) => {
                scroller.addEventListener(
                    'scroll',
                    update,
                    {
                        passive: true,
                    }
                );
            });


            const resizeObserver =
                typeof ResizeObserver !== 'undefined'
                    ? new ResizeObserver(update)
                    : null;

            resizeObserver?.observe(root);

            scrollers.forEach((scroller) => {
                resizeObserver?.observe(scroller);
            });


            const mutationObserver =
                new MutationObserver(() => {
                    requestAnimationFrame(update);
                });

            mutationObserver.observe(
                root,
                {
                    childList: true,
                    subtree: true,
                    characterData: true,
                }
            );


            requestAnimationFrame(() => {
                requestAnimationFrame(update);
            });
        };


        const scan = () => {
            configurations.forEach((configuration) => {
                document
                    .querySelectorAll(
                        configuration.rootSelector
                    )
                    .forEach((root) => {
                        bindScrollHints(
                            root,
                            configuration.scrollerSelector
                        );
                    });
            });
        };


        scan();

        const pageObserver =
            new MutationObserver(() => {
                scan();
            });

        pageObserver.observe(
            document.body,
            {
                childList: true,
                subtree: true,
            }
        );

        window.addEventListener(
            'resize',
            scan,
            {
                passive: true,
            }
        );
    });
</script>





    <div

        x-data="{

            drawer: null,

            shopPollUrl:
                @js(
                    $showPartyDrawer
                        ? route(
                            'characters.shops.index',
                            ['character' => $character]
                        )
                        : null
                ),

            openShopCount: 0,

            openShopName: '',

            shopPollBusy: false,

            shopPollTimer: null,

            init() {

                if (!this.shopPollUrl) {
                    return;
                }

                this.refreshOpenShops();

                this.shopPollTimer =
                    window.setInterval(
                        () => {
                            this.refreshOpenShops();
                        },
                        7000
                    );

            },

            destroy() {

                if (this.shopPollTimer) {
                    window.clearInterval(
                        this.shopPollTimer
                    );
                }

            },

            async refreshOpenShops() {

                if (
                    !this.shopPollUrl
                    ||
                    this.shopPollBusy
                    ||
                    document.hidden
                ) {
                    return;
                }

                this.shopPollBusy = true;

                try {
                    const response =
                        await fetch(
                            this.shopPollUrl,
                            {
                                headers: {
                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                cache:
                                    'no-store',
                            }
                        );

                    if (!response.ok) {
                        return;
                    }

                    const data =
                        await response.json();

                    const shops =
                        Array.isArray(
                            data?.shops
                        )
                            ? data.shops
                            : [];

                    this.openShopCount =
                        Math.max(
                            0,
                            parseInt(
                                data?.shop_count
                            )
                            || shops.length
                        );

                    this.openShopName =
                        shops[0]?.name
                        ?? '';

                } catch (error) {
                    /*
                    | A ficha continua funcionando normalmente
                    | mesmo que a consulta da Loja falhe.
                    */
                } finally {
                    this.shopPollBusy =
                        false;
                }

            },

            openDrawer(name) {

                this.drawer = name;

            },

            closeDrawer() {

                this.drawer = null;

            },

            openShopFromIndicator() {

                this.drawer =
                    null;

                this.$nextTick(
                    () => {
                        window.dispatchEvent(
                            new CustomEvent(
                                'open-character-shop'
                            )
                        );
                    }
                );

            }

        }"

        x-effect="

            document.body.classList.toggle(

                'overflow-hidden',

                drawer !== null

            )

        "

        @keydown.escape.window="

            closeDrawer()

        "

        data-theme="{{ $characterTheme }}"

        class="

            character-sheet-page
            character-sheet-theme

            {{ $showHeroDefenses
                ? 'character-sheet-page--with-defenses'
                : 'character-sheet-page--without-defenses'
            }}

            min-h-full

            px-3

            py-5

            sm:px-6

            md:px-8

        "

    >

        @if($showPartyDrawer)

            <div
                x-data="{
                    pokesUrl: @js(route('characters.party.pokes', $character)),
                    queue: [],
                    currentPoke: null,
                    visible: false,
                    polling: false,
                    timer: null,
                    hideTimer: null,

                    init() {
                        this.checkPokes();

                        this.timer = window.setInterval(
                            () => {
                                this.checkPokes();
                            },
                            2000
                        );
                    },

                    destroy() {
                        if (this.timer) {
                            clearInterval(this.timer);
                        }

                        if (this.hideTimer) {
                            clearTimeout(this.hideTimer);
                        }
                    },

                    async checkPokes() {
                        if (this.polling || document.hidden) {
                            return;
                        }

                        this.polling = true;

                        try {
                            const response = await fetch(
                                this.pokesUrl,
                                {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    cache: 'no-store',
                                }
                            );

                            if (!response.ok) {
                                return;
                            }

                            const data = await response.json();

                            const pokes = Array.isArray(data?.pokes)
                                ? data.pokes
                                : [];

                            if (pokes.length > 0) {
                                this.queue.push(...pokes);

                                if (!this.visible) {
                                    this.showNext();
                                }
                            }
                        } finally {
                            this.polling = false;
                        }
                    },

                    showNext() {
                        if (this.queue.length === 0) {
                            this.visible = false;
                            this.currentPoke = null;
                            return;
                        }

                        this.currentPoke = this.queue.shift();
                        this.visible = true;

                        if (this.hideTimer) {
                            clearTimeout(this.hideTimer);
                        }

                        this.hideTimer = window.setTimeout(
                            () => {
                                this.visible = false;

                                window.setTimeout(
                                    () => {
                                        this.showNext();
                                    },
                                    180
                                );
                            },
                            4800
                        );
                    },
                }"
                class="pointer-events-none fixed left-1/2 top-6 z-[260] -translate-x-1/2"
            >
                <div
                    x-show="visible && currentPoke"
                    x-cloak
                    x-transition
                    class="pointer-events-auto flex min-w-[280px] max-w-[420px] items-center gap-3 rounded-2xl border border-[#cdbb9f] bg-[#fffdf8] px-4 py-3 shadow-2xl"
                >
                    <div
                        class="
                            flex
                            h-12
                            w-12
                            shrink-0
                            items-center
                            justify-center
                            rounded-full
                            bg-[#eadbc8]
                            text-[26px]
                            shadow-inner
                        "
                        x-text="
                            currentPoke?.emoji
                            ?? '👉'
                        "
                    ></div>

                    <div class="min-w-0 flex-1">
                        <p class="text-[8px] font-black uppercase tracking-[0.18em] text-[#8c6239]">
                            Party
                        </p>

                        <p
                            class="
                                mt-0.5
                                max-w-[310px]
                                font-serif
                                text-[15px]
                                font-black
                                leading-snug
                                text-[#53150f]
                            "
                            x-text="
                                currentPoke?.message
                                ?? 'Alguém te cutucou.'
                            "
                        ></p>

                        <p
                            class="
                                mt-1
                                truncate
                                text-[9px]
                                font-bold
                                text-[#7d604d]
                            "
                        >
                            <span
                                x-text="
                                    currentPoke?.sender_name
                                    ?? 'Alguém'
                                "
                            ></span>

                            <span
                                aria-hidden="true"
                            >
                                ·
                            </span>

                            <span
                                x-text="
                                    currentPoke?.campaign_name
                                    ?? 'Party'
                                "
                            ></span>
                        </p>
                    </div>
                </div>
            </div>

        @endif


        <div

            class="

                character-sheet-canvas

                mx-auto

                max-w-[1400px]

            "

        >

            {{-- ============================================================

                 FOLHA PRINCIPAL

            ============================================================= --}}

            <div

                class="

                    relative

                    w-full

                "

            >
                @if (
                    $sheetTransformation
                    && $sheetTransformation->type === 'character_sheet'
                )
                    <div
                        class="
                            relative
                            z-40
                            flex
                            flex-wrap
                            items-center
                            justify-between
                            gap-2
                            border-b
                            border-[#b08c62]/35
                            bg-[#eadbc8]/72
                            px-4
                            py-2
                        "
                    >
                        <div class="min-w-0">
                            <span
                                class="
                                    text-[7px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#93623d]
                                "
                            >
                                {{ $editingTransformationSheet ? 'Editando Nova Ficha' : 'Transformação ativa' }}
                            </span>

                            <strong
                                class="
                                    ml-2
                                    font-serif
                                    text-[12px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                {{ $sheetTransformation->name }}
                            </strong>
                        </div>

                        @if ($editingTransformationSheet)
                            <a
                                href="{{ route('characters.show', $character) }}"
                                class="
                                    rounded-lg
                                    border
                                    border-[#93623d]/30
                                    bg-[#fffdf8]
                                    px-3
                                    py-1.5
                                    text-[8px]
                                    font-black
                                    uppercase
                                    tracking-[0.08em]
                                    text-[#6b1d14]
                                    transition
                                    hover:bg-[#f3e7d8]
                                "
                            >
                                Voltar à ficha original
                            </a>
                        @endif
                    </div>
                @endif



                {{-- ========================================================

                     HERO

                ========================================================= --}}

                <div

                    class="

                        character-sheet-hero

                        relative

                        z-20

                        w-full

                    "

                >

                    {{-- ====================================================

                         HEADER PRINCIPAL

                    ===================================================== --}}

                    @include(

                        'characters.components.hero-header',

                        [

                            'character' =>

                                $sheetCharacter,

                            'identityCharacter' =>
                                $character,

                            'inventoryCharacter' =>
                                $character,

                            'imageOverrideUrl' =>
                                $sheetTransformationImageUrl,


                        ]

                    )



                    {{-- ====================================================

                         RESISTÊNCIAS / IMUNIDADES / VULNERABILIDADES

                    ===================================================== --}}

                    @if ($showHeroDefenses)

                        <div

                            class="

                                character-sheet-hero-defenses

                            "

                        >

                            @include(

                                'characters.components.hero-defenses',

                                [

                                    'character' =>

                                        $sheetCharacter,

                                ]

                            )

                        </div>

                    @endif

                </div>





                {{-- ========================================================

                     CORPO

                ========================================================= --}}

                <div

                    class="

                        character-sheet-body

                        relative

                        z-10

                    "

                >

                    {{-- ====================================================

                         CONTEÚDO PRINCIPAL

                    ===================================================== --}}

                    <div class="character-sheet-main-grid">

                        {{-- =================================================

                             ATRIBUTOS

                        ================================================== --}}

                        <aside class="character-sheet-abilities">

                            @include(
                                'characters.components.character-transformations',
                                [
                                    'character' =>
                                        $character,

                                ]
                            )

                            @include(

                                'characters.components.character-abilities',

                                [

                                    'character' =>

                                        $sheetCharacter,

                                ]

                            )

                            <div class="character-sheet-training-under-abilities">

                                @include(

                                    'characters.components.training-proficiencies',

                                    [

                                        'character' =>

                                            $sheetCharacter,

                                    ]

                                )

                            </div>

                        </aside>





                        {{-- =================================================

                             COLUNA PRINCIPAL

                        ================================================== --}}

                        <main class="character-sheet-main-column">

                            {{-- ATAQUES --}}

                            <section

                                class="

                                    character-sheet-block

                                    character-sheet-attacks

                                "

                            >

                                @include(

                                    'characters.components.attacks',

                                    [

                                        'character' =>

                                            $sheetCharacter,

                                    ]

                                )

                            </section>





                            {{-- HABILIDADES --}}

                            <section

                                class="

                                    character-sheet-block

                                    character-sheet-features

                                "

                            >

                                @include(

                                    'characters.components.features',

                                    [

                                        'character' =>

                                            $sheetCharacter,

                                    ]

                                )

                            </section>





                            {{-- TRAÇOS DE ESPÉCIE + TALENTOS --}}

                            <section

                                class="

                                    character-sheet-block

                                    character-sheet-traits-feats

                                "

                            >

                                @include(

                                    'characters.components.traits-feats',

                                    [

                                        'character' =>

                                            $sheetCharacter,

                                    ]

                                )

                            </section>









</main>

                    </div>

                </div>

            </div>

        </div>





        {{-- ================================================================

             ABAS LATERAIS

        ================================================================= --}}

        <div

            class="

                character-side-tabs

                fixed

                right-0

                top-1/2

                z-40

                flex

                -translate-y-1/2

                flex-col

                gap-2

            "

        >

            @if($showPartyDrawer)

                {{-- LOJA ABERTA --}}
                <div
                    x-show="openShopCount > 0"
                    x-cloak
                    x-transition.opacity
                    class="player-shop-side-wrap"
                >
                    <button
                        type="button"
                        @click="openShopFromIndicator()"
                        class="player-shop-side-tab"
                        :title="
                            openShopCount === 1
                                ? (
                                    'Abrir loja '
                                    + (
                                        openShopName
                                        || ''
                                    )
                                )
                                : (
                                    'Abrir '
                                    + openShopCount
                                    + ' lojas'
                                )
                        "
                        :aria-label="
                            openShopCount === 1
                                ? (
                                    'Abrir loja '
                                    + (
                                        openShopName
                                        || 'disponível'
                                    )
                                )
                                : (
                                    'Abrir '
                                    + openShopCount
                                    + ' lojas disponíveis'
                                )
                        "
                    >
                        <span
                            class="player-shop-side-tab__top"
                        >
                            <span
                                class="player-shop-side-tab__icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M4 10h16"></path>
                                    <path d="M5 10 6.5 5h11L19 10"></path>
                                    <path d="M6 10v8.5h12V10"></path>
                                    <path d="M9 18.5v-5h6v5"></path>
                                </svg>

                                <span
                                    class="player-shop-side-tab__dot"
                                ></span>
                            </span>

                            <span
                                class="player-shop-side-tab__label"
                            >
                                Loja<br>aberta
                            </span>
                        </span>

                        <strong
                            class="player-shop-side-tab__name"
                            x-text="
                                openShopCount === 1
                                    ? (
                                        openShopName
                                        || 'Visitar loja'
                                    )
                                    : (
                                        openShopCount
                                        + ' lojas'
                                    )
                            "
                        ></strong>
                    </button>
                </div>


                {{-- PARTY --}}

                <button
                    type="button"
                    @click="
                        drawer === 'party'
                            ? closeDrawer()
                            : openDrawer('party')
                    "
                    class="
                        character-side-tab
                        rounded-l-2xl
                        px-3
                        py-4
                    "
                    :class="{
                        'active': drawer === 'party'
                    }"
                    title="Abrir Party"
                >
                    <div class="flex flex-col items-center gap-2">
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <circle cx="8" cy="9" r="3" stroke-width="1.7" />
                            <circle cx="16" cy="9" r="3" stroke-width="1.7" />
                            <path
                                d="M3.5 19c.6-3 2.1-4.5 4.5-4.5S11.9 16 12.5 19M11.5 19c.6-3 2.1-4.5 4.5-4.5s3.9 1.5 4.5 4.5"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span
                            class="text-[8px] font-black uppercase tracking-[0.25em]"
                            style="writing-mode: vertical-rl;"
                        >
                            Party
                        </span>
                    </div>
                </button>

            @endif


            {{-- INVENTÁRIO --}}

            <button

                type="button"

                @click="

                    drawer === 'inventory'

                        ? closeDrawer()

                        : openDrawer('inventory')

                "
                class="

                    character-side-tab

                    rounded-l-2xl

                    px-3

                    py-4

                "

                :class="{

                    'active':

                        drawer ===

                            'inventory'

                }"

                title="Abrir Inventário"

            >

                <div

                    class="

                        flex

                        flex-col

                        items-center

                        gap-2

                    "

                >

                    <svg

                        class="

                            h-5

                            w-5

                        "

                        fill="none"

                        viewBox="0 0 24 24"

                        stroke="currentColor"

                    >

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M5 8.5h14v11H5v-11z"

                        />

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M8 8.5V6a4 4 0 018 0v2.5"

                        />

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M9 13h6"

                        />

                    </svg>

                    <span

                        class="

                            text-[8px]

                            font-black

                            uppercase

                            tracking-[0.22em]

                        "

                        style="

                            writing-mode:

                                vertical-rl;

                        "

                    >

                        Inventário

                    </span>

                </div>

            </button>





            {{-- GRIMÓRIO --}}

            <button

                type="button"

                @click="

                    drawer === 'spellbook'

                        ? closeDrawer()

                        : openDrawer('spellbook')

                "
                class="

                    character-side-tab

                    rounded-l-2xl

                    px-3

                    py-4

                "

                :class="{

                    'active':

                        drawer ===

                            'spellbook'

                }"

                title="Abrir Grimório"

            >

                <div

                    class="

                        flex

                        flex-col

                        items-center

                        gap-2

                    "

                >

                    <svg

                        class="

                            h-5

                            w-5

                        "

                        fill="none"

                        viewBox="0 0 24 24"

                        stroke="currentColor"

                    >

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M4 5.5A2.5 2.5 0 016.5 3H20v16H6.5A2.5 2.5 0 014 16.5v-11z"

                        />

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M4 16.5A2.5 2.5 0 016.5 14H20"

                        />

                        <path

                            stroke-linecap="round"

                            stroke-linejoin="round"

                            stroke-width="1.7"

                            d="M8 7h7M8 10h5"

                        />

                    </svg>

                    <span

                        class="

                            text-[8px]

                            font-black

                            uppercase

                            tracking-[0.25em]

                        "

                        style="

                            writing-mode:

                                vertical-rl;

                        "

                    >

                        Grimório

                    </span>

                </div>

            </button>

        </div>





        {{-- ================================================================

             DRAWER COMPARTILHADO

        ================================================================= --}}

        <div

            x-show="

                drawer !== null

            "

            x-cloak

            class="

                fixed

                inset-0

                z-50

            "

        >

            {{-- BACKDROP --}}

            <div

                x-show="

                    drawer !== null

                "

                x-transition:enter="

                    transition-opacity

                    duration-200

                "

                x-transition:enter-start="

                    opacity-0

                "

                x-transition:enter-end="

                    opacity-100

                "

                x-transition:leave="

                    transition-opacity

                    duration-150

                "

                x-transition:leave-start="

                    opacity-100

                "

                x-transition:leave-end="

                    opacity-0

                "

                @click="

                    closeDrawer()

                "
                class="

                    absolute

                    inset-0

                    bg-[#2a1712]/60

                    backdrop-blur-[2px]

                "

            ></div>





            {{-- DRAWER --}}

            <aside

                x-show="

                    drawer !== null

                "

                x-transition:enter="

                    transform

                    transition

                    ease-out

                    duration-300

                "

                x-transition:enter-start="

                    translate-x-full

                "

                x-transition:enter-end="

                    translate-x-0

                "

                x-transition:leave="

                    transform

                    transition

                    ease-in

                    duration-200

                "

                x-transition:leave-start="

                    translate-x-0

                "

                x-transition:leave-end="

                    translate-x-full

                "

                class="

                    character-drawer

                    absolute

                    inset-y-0

                    right-0

                    flex

                    w-full

                    max-w-3xl

                    flex-col

                    border-l

                    border-[#cdbb9f]

                    shadow-2xl

                "

            >

                {{-- ========================================================

                     HEADER — GRIMÓRIO

                ========================================================= --}}

                <div

                    x-show="

                        drawer ===

                            'spellbook'

                    "

                    x-cloak

                    class="

                        shrink-0

                        border-b

                        border-[#cdbb9f]/60

                        bg-[#53150f]

                        px-5

                        py-5

                        text-[#f4f1e8]

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

                        <div>

                            <p

                                class="

                                    text-[9px]

                                    font-black

                                    uppercase

                                    tracking-[0.3em]

                                    text-[#cdbb9f]

                                "

                            >

                                Grimório de Aventureiro

                            </p>

                            <h2

                                class="

                                    mt-1

                                    font-serif

                                    text-2xl

                                    font-black

                                "

                            >

                                Magias

                            </h2>

                            <p

                                class="

                                    mt-1

                                    text-xs

                                    text-[#eadfce]/70

                                "

                            >

                                {{ $character->name }}

                            </p>

                        </div>





                        <button

                            type="button"

                            @click="

                                closeDrawer()

                            "
                            class="

                                flex

                                h-9

                                w-9

                                shrink-0

                                items-center

                                justify-center

                                rounded-lg

                                border

                                border-[#cdbb9f]/30

                                bg-[#f4f1e8]/10

                                text-[#f4f1e8]

                                transition

                                hover:bg-[#f4f1e8]/20

                            "

                            title="Fechar Grimório"

                        >

                            <svg

                                class="

                                    h-5

                                    w-5

                                "

                                fill="none"

                                viewBox="0 0 24 24"

                                stroke="currentColor"

                            >

                                <path

                                    stroke-linecap="round"

                                    stroke-linejoin="round"

                                    stroke-width="1.8"

                                    d="M6 6l12 12M18 6L6 18"

                                />

                            </svg>

                        </button>

                    </div>

                </div>





                @if($showPartyDrawer)

                    {{-- ========================================================

                         PARTY

                    ========================================================= --}}

                    <div
                        x-show="drawer === 'party'"
                        x-cloak
                        class="min-h-0 flex-1"
                    >
                        @include(
                            'characters.components.party-drawer',
                            [
                                'character' => $character,
                            ]
                        )
                    </div>

                @endif


                {{-- ========================================================

                     INVENTÁRIO

                ========================================================= --}}

                <div

                    x-show="

                        drawer ===

                            'inventory'

                    "

                    x-cloak

                    class="

                        min-h-0

                        flex-1

                        overflow-y-auto

                        p-4

                        sm:p-5

                    "

                >

                    @include(

                        'characters.components.inventory',

                        [

                            'character' =>

                                $character,

                        ]

                    )

                </div>





                {{-- ========================================================

                     GRIMÓRIO

                ========================================================= --}}

                <div

                    x-show="

                        drawer ===

                            'spellbook'

                    "

                    x-cloak

                    class="

                        min-h-0

                        flex-1

                        overflow-y-auto

                        p-4

                        sm:p-6

                    "

                >

                    @include(

                        'characters.components.spells',

                        [

                            'character' =>

                                $character,

                        ]

                    )

                </div>

            </aside>

        </div>

        @if($showPartyDrawer)
            @include(
                'characters.components.shop',
                [
                    'character' => $character,
                    'characterTheme' => $characterTheme,
                ]
            )
        @endif

    </div>

</x-app-layout>