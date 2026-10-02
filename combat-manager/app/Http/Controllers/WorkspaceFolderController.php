<?php



namespace App\Http\Controllers;



use App\Models\WorkspaceFolder;

use App\Models\WorkspaceFolderItem;

use App\Services\Folders\FolderContextRegistry;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;

use Illuminate\Validation\ValidationException;

use Symfony\Component\HttpFoundation\BinaryFileResponse;



class WorkspaceFolderController extends Controller

{

    public function __construct(

        private FolderContextRegistry $registry

    ) {

    }



    public function index(Request $request): JsonResponse

    {

        $data = $request->validate([

            'scope_type' => ['required', 'string', 'max:80'],

            'scope_id' => ['required', 'integer', 'min:1'],

        ]);



        $scopeType = trim($data['scope_type']);

        $scopeId = (int) $data['scope_id'];



        $this->registry->authorizeScope(

            $scopeType,

            $scopeId

        );



        $folders = WorkspaceFolder::query()

            ->forContext($scopeType, $scopeId)

            ->where('owner_user_id', Auth::id())

            ->withCount('placements')

            ->orderBy('position')

            ->orderBy('name')

            ->get()

            ->map(

                fn (WorkspaceFolder $folder) =>

                    $this->folderPayload($folder)

            )

            ->values();



        return response()->json([

            'success' => true,

            'folders' => $folders,

        ]);

    }



    public function store(Request $request): JsonResponse

    {

        $data = $this->validateFolder(

            $request

        );



        $scopeType = trim($data['scope_type']);

        $scopeId = (int) $data['scope_id'];



        $this->registry->authorizeScope(

            $scopeType,

            $scopeId

        );



        $position = WorkspaceFolder::query()

            ->forContext($scopeType, $scopeId)

            ->where('owner_user_id', Auth::id())

            ->max('position');



        $folder = WorkspaceFolder::query()->create([

            'owner_user_id' => Auth::id(),

            'scope_type' => $scopeType,

            'scope_id' => $scopeId,

            'parent_id' => $this->validatedParentId(

                $data['parent_id'] ?? null,

                $scopeType,

                $scopeId

            ),

            'name' => trim($data['name']),

            'description' => $this->nullableText(

                $data['description'] ?? null

            ),

            'content' => $this->nullableText(

                $data['content'] ?? null

            ),

            'color' => $this->normalizeColor(

                $data['color'] ?? null

            ),

            'text_color' => $this->normalizeColor(

                $data['text_color'] ?? null

            ),

            'icon' => $this->nullableText(

                $data['icon'] ?? null

            ),

            'position' => ((int) $position) + 1,

            'metadata' => [],

            'is_system' => false,

        ]);



        if ($request->hasFile('image')) {

            $folder->image_path =

                $this->storeImage(

                    $request,

                    $folder

                );



            $folder->save();

        }



        $folder->loadCount('placements');



        return response()->json([

            'success' => true,

            'message' => 'Pasta criada.',

            'folder' => $this->folderPayload($folder),

        ], 201);

    }



    public function update(

        Request $request,

        WorkspaceFolder $workspaceFolder

    ): JsonResponse {

        $this->authorizeFolder(

            $workspaceFolder

        );



        $data = $request->validate([

            'name' => ['sometimes', 'required', 'string', 'max:120'],

            'description' => ['nullable', 'string', 'max:10000'],

            'content' => ['nullable', 'string', 'max:50000'],

            'color' => [

                'nullable',

                'string',

                'max:20',

                'regex:/^#[0-9A-Fa-f]{6}$/',

            ],

            'text_color' => [

                'nullable',

                'string',

                'max:20',

                'regex:/^#[0-9A-Fa-f]{6}$/',

            ],

            'icon' => ['nullable', 'string', 'max:80'],

            'parent_id' => ['nullable', 'integer', 'min:1'],

            'position' => ['nullable', 'integer', 'min:0'],

            'remove_image' => ['nullable', 'boolean'],

            'image' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:8192',

            ],

        ]);



        $oldImage = trim(

            (string) ($workspaceFolder->image_path ?? '')

        );



        $updates = [];



        foreach (

            [

                'name',

                'description',

                'content',

                'icon',

                'position',

            ] as $field

        ) {

            if (array_key_exists($field, $data)) {

                $updates[$field] =

                    in_array(

                        $field,

                        ['description', 'content', 'icon'],

                        true

                    )

                        ? $this->nullableText($data[$field])

                        : $data[$field];

            }

        }



        if (array_key_exists('name', $updates)) {

            $updates['name'] = trim(

                (string) $updates['name']

            );

        }



        if (array_key_exists('color', $data)) {

            $updates['color'] =

                $this->normalizeColor(

                    $data['color']

                );

        }



        if (array_key_exists('text_color', $data)) {

            $updates['text_color'] =

                $this->normalizeColor(

                    $data['text_color']

                );

        }



        if (array_key_exists('parent_id', $data)) {

            $updates['parent_id'] =

                $this->validatedParentId(

                    $data['parent_id'],

                    $workspaceFolder->scope_type,

                    (int) $workspaceFolder->scope_id,

                    (int) $workspaceFolder->id

                );

        }



        $newImage = $oldImage !== ''

            ? $oldImage

            : null;



        if ((bool) ($data['remove_image'] ?? false)) {

            $newImage = null;

        }



        if ($request->hasFile('image')) {

            $newImage =

                $this->storeImage(

                    $request,

                    $workspaceFolder

                );

        }



        $updates['image_path'] = $newImage;



        $workspaceFolder->update($updates);



        if (

            $oldImage !== ''

            && $oldImage !== ($newImage ?? '')

            && Storage::disk('public')->exists($oldImage)

        ) {

            Storage::disk('public')->delete($oldImage);

        }



        $workspaceFolder->loadCount('placements');



        return response()->json([

            'success' => true,

            'message' => 'Pasta atualizada.',

            'folder' => $this->folderPayload(

                $workspaceFolder->fresh()

                    ->loadCount('placements')

            ),

        ]);

    }



    public function destroy(

        WorkspaceFolder $workspaceFolder

    ): JsonResponse {

        $this->authorizeFolder(

            $workspaceFolder

        );



        abort_if(

            (bool) $workspaceFolder->is_system,

            422,

            'Esta pasta do sistema não pode ser excluída.'

        );



        $imagePath = trim(

            (string) ($workspaceFolder->image_path ?? '')

        );



        /*

        | WorkspaceFolder usa SoftDeletes; portanto o cascade do banco não

        | dispara aqui. Removemos explicitamente os vínculos para que o

        | conteúdo continue existindo e passe imediatamente a "Sem pasta".

        */

        DB::transaction(

            function () use ($workspaceFolder): void {

                WorkspaceFolderItem::query()

                    ->where('folder_id', $workspaceFolder->id)

                    ->delete();



                WorkspaceFolder::query()

                    ->where('parent_id', $workspaceFolder->id)

                    ->update([

                        'parent_id' => null,

                    ]);



                $workspaceFolder->delete();

            }

        );



        if (

            $imagePath !== ''

            && Storage::disk('public')->exists($imagePath)

        ) {

            Storage::disk('public')->delete($imagePath);

        }



        return response()->json([

            'success' => true,

            'message' => 'Pasta excluída. O conteúdo foi movido para Sem pasta.',

        ]);

    }



    public function image(

        WorkspaceFolder $workspaceFolder

    ): BinaryFileResponse {

        $this->authorizeFolder(

            $workspaceFolder

        );



        $path = trim(

            (string) ($workspaceFolder->image_path ?? '')

        );



        abort_if(

            $path === ''

            || !Storage::disk('public')->exists($path),

            404

        );



        return response()->file(

            Storage::disk('public')->path($path)

        );

    }



    public function moveItem(

        Request $request

    ): JsonResponse {

        $data = $request->validate([

            'scope_type' => ['required', 'string', 'max:80'],

            'scope_id' => ['required', 'integer', 'min:1'],

            'item_type' => [

                'required',

                'string',

                Rule::in([

                    'shop',

                    'loot',

                    'character_item',

                    'vault_item',

                ]),

            ],

            'item_id' => ['required', 'integer', 'min:1'],

            'folder_id' => ['nullable', 'integer', 'min:1'],

            'position' => ['nullable', 'integer', 'min:0'],

        ]);



        $scopeType = trim($data['scope_type']);

        $scopeId = (int) $data['scope_id'];

        $itemType = trim($data['item_type']);

        $itemId = (int) $data['item_id'];



        $item = $this->registry->resolveItem(

            $scopeType,

            $scopeId,

            $itemType,

            $itemId

        );



        $morphClass = get_class($item);



        $folder = null;



        if (!empty($data['folder_id'])) {

            $folder = WorkspaceFolder::query()

                ->whereKey((int) $data['folder_id'])

                ->forContext($scopeType, $scopeId)

                ->where('owner_user_id', Auth::id())

                ->firstOrFail();

        }



        DB::transaction(function () use (

            $folder,

            $morphClass,

            $itemId,

            $data

        ): void {

            WorkspaceFolderItem::query()

                ->where('folderable_type', $morphClass)

                ->where('folderable_id', $itemId)

                ->delete();



            if (!$folder) {

                return;

            }



            $position = array_key_exists('position', $data)

                ? max(0, (int) $data['position'])

                : (

                    (

                        (int) WorkspaceFolderItem::query()

                            ->where('folder_id', $folder->id)

                            ->max('position')

                    ) + 1

                );



            WorkspaceFolderItem::query()->create([

                'folder_id' => $folder->id,

                'folderable_type' => $morphClass,

                'folderable_id' => $itemId,

                'position' => $position,

                'metadata' => [],

            ]);

        });



        return response()->json([

            'success' => true,

            'message' => $folder

                ? 'Item movido para a pasta.'

                : 'Item movido para Sem pasta.',

            'folder_id' => $folder?->id,

            'position' => $folder

                ? WorkspaceFolderItem::query()

                    ->where('folderable_type', $morphClass)

                    ->where('folderable_id', $itemId)

                    ->value('position')

                : null,

        ]);

    }



    public function reorder(

        Request $request

    ): JsonResponse {

        $data = $request->validate([

            'scope_type' => ['required', 'string', 'max:80'],

            'scope_id' => ['required', 'integer', 'min:1'],

            'folder_ids' => ['required', 'array', 'max:500'],

            'folder_ids.*' => ['integer', 'min:1'],

        ]);



        $scopeType = trim($data['scope_type']);

        $scopeId = (int) $data['scope_id'];



        $this->registry->authorizeScope(

            $scopeType,

            $scopeId

        );



        $ids = array_values(

            array_unique(

                array_map(

                    'intval',

                    $data['folder_ids']

                )

            )

        );



        $ownedCount = WorkspaceFolder::query()

            ->forContext($scopeType, $scopeId)

            ->where('owner_user_id', Auth::id())

            ->whereIn('id', $ids)

            ->count();



        abort_unless(

            $ownedCount === count($ids),

            422,

            'A ordem contém uma pasta inválida.'

        );



        DB::transaction(

            function () use ($ids): void {

                foreach ($ids as $index => $id) {

                    WorkspaceFolder::query()

                        ->whereKey($id)

                        ->update([

                            'position' => $index,

                        ]);

                }

            }

        );



        return response()->json([

            'success' => true,

            'message' => 'Ordem das pastas atualizada.',

        ]);

    }



    private function authorizeFolder(

        WorkspaceFolder $folder

    ): void {

        abort_unless(

            (int) $folder->owner_user_id === (int) Auth::id(),

            403

        );



        $this->registry->authorizeScope(

            (string) $folder->scope_type,

            (int) $folder->scope_id

        );

    }



    private function validateFolder(

        Request $request

    ): array {

        return $request->validate([

            'scope_type' => ['required', 'string', 'max:80'],

            'scope_id' => ['required', 'integer', 'min:1'],

            'parent_id' => ['nullable', 'integer', 'min:1'],

            'name' => ['required', 'string', 'max:120'],

            'description' => ['nullable', 'string', 'max:10000'],

            'content' => ['nullable', 'string', 'max:50000'],

            'color' => [

                'nullable',

                'string',

                'max:20',

                'regex:/^#[0-9A-Fa-f]{6}$/',

            ],

            'text_color' => [

                'nullable',

                'string',

                'max:20',

                'regex:/^#[0-9A-Fa-f]{6}$/',

            ],

            'icon' => ['nullable', 'string', 'max:80'],

            'image' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:8192',

            ],

        ]);

    }



    private function validatedParentId(

        mixed $parentId,

        string $scopeType,

        int $scopeId,

        ?int $currentFolderId = null

    ): ?int {

        if (!$parentId) {

            return null;

        }



        $parentId = (int) $parentId;



        if (

            $currentFolderId

            && $parentId === $currentFolderId

        ) {

            throw ValidationException::withMessages([

                'parent_id' =>

                    'Uma pasta não pode ser pai dela mesma.',

            ]);

        }



        WorkspaceFolder::query()

            ->whereKey($parentId)

            ->forContext($scopeType, $scopeId)

            ->where('owner_user_id', Auth::id())

            ->firstOrFail();



        return $parentId;

    }



    private function storeImage(

        Request $request,

        WorkspaceFolder $folder

    ): string {

        $file = $request->file('image');



        abort_unless($file, 422);



        $extension = strtolower(

            (string) $file->getClientOriginalExtension()

        );



        $filename = uniqid('folder_', true)

            . ($extension !== '' ? '.' . $extension : '');



        $path = Storage::disk('public')->putFileAs(

            'workspace-folders/' . $folder->id,

            $file,

            $filename

        );



        if (!is_string($path) || trim($path) === '') {

            throw ValidationException::withMessages([

                'image' =>

                    'Não foi possível salvar a imagem da pasta.',

            ]);

        }



        return $path;

    }



    private function folderPayload(

        WorkspaceFolder $folder

    ): array {

        return [

            'id' => (int) $folder->id,

            'scope_type' => (string) $folder->scope_type,

            'scope_id' => (int) $folder->scope_id,

            'parent_id' => $folder->parent_id

                ? (int) $folder->parent_id

                : null,

            'name' => (string) $folder->name,

            'description' => $folder->description,

            'content' => $folder->content,

            'color' => $folder->color ?: '#8A4B20',

            'text_color' => $folder->text_color,

            'icon' => $folder->icon,

            'image_url' => $folder->image_path

                ? route(

                    'workspace-folders.image',

                    $folder,

                    false

                )

                : null,

            'position' => (int) $folder->position,

            'metadata' => $folder->metadata ?? [],

            'is_system' => (bool) $folder->is_system,

            'items_count' => (int) (

                $folder->placements_count

                ?? $folder->placements()->count()

            ),

        ];

    }



    private function normalizeColor(

        mixed $value

    ): ?string {

        $value = strtoupper(

            trim((string) ($value ?? ''))

        );



        return preg_match(

            '/^#[0-9A-F]{6}$/',

            $value

        )

            ? $value

            : null;

    }



    private function nullableText(

        mixed $value

    ): ?string {

        $value = trim(

            (string) ($value ?? '')

        );



        return $value !== ''

            ? $value

            : null;

    }

}