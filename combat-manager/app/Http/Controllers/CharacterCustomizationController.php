<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class CharacterCustomizationController extends Controller
{
    /**
     * Atualiza os dados gerais editáveis pelo modal da ficha.
     *
     * Nesta primeira etapa o controller cuida de:
     * - identidade básica;
     * - preferências visuais da ficha;
     * - espaço persistente para regras opcionais.
     *
     * O fluxo de subida de nível será conectado separadamente,
     * porque ele precisa alterar classe, nível total, HP, hit dice,
     * features e regras de multiclass de forma transacional.
     */
    public function update(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'background' => [
                'nullable',
                'string',
                'max:120',
            ],

            'species' => [
                'nullable',
                'string',
                'max:120',
            ],

            'sheet_settings' => [
                'nullable',
                'array',
            ],

            'sheet_settings.display' => [
                'nullable',
                'array',
            ],

            'sheet_settings.display.show_defenses' => [
                'nullable',
                'boolean',
            ],

            /*
             * Compatibilidade temporária com a primeira versão
             * da configuração.
             */
            'sheet_settings.display.show_empty_defenses' => [
                'nullable',
                'boolean',
            ],

            'sheet_settings.display.show_experience' => [
                'nullable',
                'boolean',
            ],

            'sheet_settings.optional_rules' => [
                'nullable',
                'array',
            ],

            'sheet_settings.optional_rules.morquen' => [
                'nullable',
                'boolean',
            ],

            'sheet_settings.optional_rules.exhaustion' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | IDENTIDADE
        |--------------------------------------------------------------------------
        */

        $character->name =
            trim(
                $validated['name']
            );

        $character->background =
            $this->nullableTrimmedString(
                $validated['background']
                ?? null
            );

        $character->species =
            $this->nullableTrimmedString(
                $validated['species']
                ?? null
            );


        /*
        |--------------------------------------------------------------------------
        | CONFIGURAÇÕES DA FICHA
        |--------------------------------------------------------------------------
        |
        | Fazemos merge recursivo para não apagar opções que venham a ser
        | adicionadas no futuro por outros componentes.
        |
        */

        $currentSettings =
            is_array(
                $character->sheet_settings
            )
                ? $character->sheet_settings
                : [];

        $incomingSettings =
            Arr::get(
                $validated,
                'sheet_settings',
                []
            );

        if (!is_array($incomingSettings)) {
            $incomingSettings = [];
        }

        $defaultSettings = [
            'display' => [
                /*
                 * A visibilidade do módulo passa a ser uma preferência
                 * explícita. O show.blade decide o fallback para registros
                 * antigos que ainda não possuem essa chave.
                 */
                'show_defenses' => true,

                /*
                 * O Hero já possui suporte visual a XP.
                 */
                'show_experience' => false,
            ],

            /*
             * Regras especiais/opcionais da ficha.
             *
             * A interface separa essas regras das simples opções visuais.
             */
            'optional_rules' => [
                'morquen' => false,
                'exhaustion' => false,
            ],
        ];

        $mergedSettings =
            array_replace_recursive(
                $defaultSettings,
                $currentSettings,
                $incomingSettings
            );

        /*
        |--------------------------------------------------------------------------
        | MIGRAÇÃO DA CHAVE ANTIGA
        |--------------------------------------------------------------------------
        |
        | A primeira versão usava show_empty_defenses. Ela significava
        | "mostrar quando estiver vazio", mas agora o controle deve significar
        | "mostrar/esconder o módulo inteiro".
        |
        */

        if (
            !array_key_exists(
                'show_defenses',
                $incomingSettings['display']
                ?? []
            )
            && array_key_exists(
                'show_empty_defenses',
                $incomingSettings['display']
                ?? []
            )
        ) {
            $mergedSettings['display']['show_defenses'] =
                (bool) $incomingSettings['display']['show_empty_defenses'];
        }

        unset(
            $mergedSettings['display']['show_empty_defenses']
        );

        $character->sheet_settings =
            $mergedSettings;

        $character->save();


        return response()->json([
            'success' => true,

            'message' =>
                'Configurações do personagem atualizadas.',

            'character' => [
                'id' =>
                    $character->getKey(),

                'name' =>
                    $character->name,

                'background' =>
                    $character->background,

                'species' =>
                    $character->species,

                'level' =>
                    (int) $character->level,

                'sheet_settings' =>
                    $character->sheet_settings,

                'image_path' =>
                    $character->image_path,

                'image_url' =>
                    $this->imageUrl(
                        $character->image_path
                    ),
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FOTO DO PERSONAGEM
    |--------------------------------------------------------------------------
    |
    | A imagem fica separada do PATCH de identidade porque upload precisa
    | usar multipart/form-data. O botão Salvar do modal chama os dois fluxos
    | em sequência e só recarrega a ficha depois que tudo terminar.
    |
    */

    public function updateImage(
        Request $request,
        Character $character
    ): JsonResponse {
        $this->authorizeCharacter(
            $request,
            $character
        );

        $validated = $request->validate([
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'remove_image' => [
                'nullable',
                'boolean',
            ],
        ]);

        $image =
            $request->file(
                'image'
            );

        $removeImage =
            (bool) (
                $validated['remove_image']
                ?? false
            );

        if (
            !$image
            && !$removeImage
        ) {
            return response()->json([
                'success' => true,

                'message' =>
                    'Nenhuma alteração de foto.',

                'character' => [
                    'id' =>
                        $character->getKey(),

                    'image_path' =>
                        $character->image_path,

                    'image_url' =>
                        $this->imageUrl(
                            $character->image_path
                        ),
                ],
            ]);
        }

        $oldPath =
            $character->image_path;

        $newPath =
            null;

        if ($image) {
            $newPath =
                $image->store(
                    'characters',
                    'public'
                );
        }

        try {
            if ($removeImage) {
                $character->image_path =
                    null;
            }

            if ($newPath) {
                $character->image_path =
                    $newPath;
            }

            $character->save();

        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk(
                    'public'
                )->delete(
                    $newPath
                );
            }

            throw $exception;
        }

        /*
         * Só apagamos a imagem anterior depois que o novo caminho foi
         * persistido com sucesso.
         */
        if (
            $oldPath
            && $oldPath
                !== $character->image_path
        ) {
            Storage::disk(
                'public'
            )->delete(
                $oldPath
            );
        }

        return response()->json([
            'success' => true,

            'message' =>
                $character->image_path
                    ? 'Foto do personagem atualizada.'
                    : 'Foto do personagem removida.',

            'character' => [
                'id' =>
                    $character->getKey(),

                'image_path' =>
                    $character->image_path,

                'image_url' =>
                    $this->imageUrl(
                        $character->image_path
                    ),
            ],
        ]);
    }


private function imageUrl(
    ?string $path
): ?string {
    if (!$path) {
        return null;
    }

    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk(
        'public'
    );

    return $disk->url(
        $path
    );
}


    private function authorizeCharacter(
        Request $request,
        Character $character
    ): void {
        abort_unless(
            $request->user()
            && (int) $character->user_id
                === (int) $request->user()->id,
            403
        );
    }


    /**
     * Normaliza campos opcionais de texto.
     */
    private function nullableTrimmedString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value =
            trim(
                (string) $value
            );

        return $value === ''
            ? null
            : $value;
    }
}