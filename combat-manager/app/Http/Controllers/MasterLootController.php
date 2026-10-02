<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\LootCurrencyEntry;
use App\Models\LootCurrencyRecipient;
use App\Models\LootDrop;
use App\Models\LootDropItem;
use App\Models\LootItemRecipient;
use App\Models\LootClaim;
use App\Models\MasterVaultItem;
use App\Models\Npc;
use App\Models\WorkspaceFolder;
use App\Models\WorkspaceFolderItem;
use App\Services\Folders\FolderContextRegistry;
use App\Services\Loot\LootClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MasterLootController extends Controller
{
    private const COINS = [
        'copper',
        'silver',
        'electrum',
        'gold',
        'platinum',
    ];

    public function __construct(
        private LootClaimService $lootClaimService
    ) {
    }

    public function index(Campaign $campaign): JsonResponse
    {
        $this->authorizeCampaign($campaign);

        /*
        | Mantém sessões antigas consistentes com a regra de encerramento
        | automático. Isso também cobre Espólios que já estavam esgotados
        | antes desta versão ser instalada.
        */
        LootDrop::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_quick_access', false)
            ->where('is_shop', false)
            ->where('status', '!=', LootDrop::STATUS_CLOSED)
            ->pluck('id')
            ->each(
                fn ($dropId) =>
                    $this->lootClaimService
                        ->syncDropCompletion((int) $dropId)
            );

        $relations = $this->workspaceRelations();

        $drops = LootDrop::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_quick_access', false)
            ->where('is_shop', false)
            ->with($relations)
            ->latest()
            ->get();

        $quickDrop = LootDrop::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_quick_access', true)
            ->with($relations)
            ->latest()
            ->first();

        $shops = LootDrop::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_shop', true)
            ->with($relations)
            ->latest()
            ->get();

        $folderSystemReady =
            $this->workspaceFolderSystemReady();

        $lootFolders =
            collect();

        $lootPlacements =
            collect();

        $shopFolders =
            collect();

        $shopPlacements =
            collect();

        if ($folderSystemReady) {
            $lootFolders = WorkspaceFolder::query()
                ->forContext(
                    FolderContextRegistry::CAMPAIGN_LOOT,
                    (int) $campaign->id
                )
                ->where('owner_user_id', Auth::id())
                ->withCount('placements')
                ->orderBy('position')
                ->orderBy('name')
                ->get();

            $lootPlacements = WorkspaceFolderItem::query()
                ->where(
                    'folderable_type',
                    LootDrop::class
                )
                ->whereIn(
                    'folderable_id',
                    $drops->pluck('id')
                )
                ->get()
                ->keyBy('folderable_id');

            $shopFolders = WorkspaceFolder::query()
                ->forContext(
                    FolderContextRegistry::CAMPAIGN_SHOPS,
                    (int) $campaign->id
                )
                ->where('owner_user_id', Auth::id())
                ->withCount('placements')
                ->orderBy('position')
                ->orderBy('name')
                ->get();

            $shopPlacements = WorkspaceFolderItem::query()
                ->where(
                    'folderable_type',
                    LootDrop::class
                )
                ->whereIn(
                    'folderable_id',
                    $shops->pluck('id')
                )
                ->get()
                ->keyBy('folderable_id');
        }

        $drops->each(
            function (LootDrop $drop) use (
                $campaign,
                $lootPlacements,
                $folderSystemReady
            ): void {
                $this->decorateLootDropForMaster(
                    $campaign,
                    $drop,
                    $lootPlacements->get($drop->id),
                    $folderSystemReady
                );
            }
        );

        $shops->each(
            function (LootDrop $shop) use (
                $campaign,
                $shopPlacements,
                $folderSystemReady
            ): void {
                $this->decorateShopForMaster(
                    $campaign,
                    $shop,
                    $shopPlacements->get($shop->id),
                    $folderSystemReady
                );
            }
        );

        $shopkeepers = Npc::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'image_path'])
            ->map(function (Npc $npc) use ($campaign): array {
                $hasImage =
                    is_string($npc->image_path)
                    && trim($npc->image_path) !== '';

                return [
                    'id' => (int) $npc->id,
                    'name' => (string) $npc->name,
                    'image_url' => $hasImage
                        ? route(
                            'master.shops.npcs.image',
                            [
                                'campaign' => $campaign,
                                'npc' => $npc,
                            ],
                            false
                        )
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'drops' => $drops,
            'loot_folders' => $lootFolders
                ->map(
                    fn (WorkspaceFolder $folder) =>
                        $this->workspaceFolderPayload(
                            $folder
                        )
                )
                ->values(),
            'quick_drop' => $quickDrop,
            'shops' => $shops,
            'shop_folders' => $shopFolders
                ->map(
                    fn (WorkspaceFolder $folder) =>
                        $this->workspaceFolderPayload(
                            $folder
                        )
                )
                ->values(),
            'folder_system_ready' =>
                $folderSystemReady,
            'folder_system_message' =>
                $folderSystemReady
                    ? null
                    : 'O sistema de pastas ainda não foi migrado. Rode php artisan migrate.',
            'shopkeepers' => $shopkeepers,
        ]);
    }

    public function storeDrop(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        $this->authorizeCampaign($campaign);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'combat_id' => ['nullable', 'integer'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if (!empty($data['combat_id'])) {
            $combatBelongs = $campaign->combats()
                ->whereKey($data['combat_id'])
                ->exists();

            abort_unless($combatBelongs, 422);
        }

        $folder = null;

        if (!empty($data['folder_id'])) {
            abort_unless(
                $this->workspaceFolderSystemReady(),
                503,
                'O sistema de pastas ainda não foi migrado. Rode php artisan migrate.'
            );

            $folder = WorkspaceFolder::query()
                ->whereKey((int) $data['folder_id'])
                ->forContext(
                    FolderContextRegistry::CAMPAIGN_LOOT,
                    (int) $campaign->id
                )
                ->where('owner_user_id', Auth::id())
                ->firstOrFail();
        }

        $drop = LootDrop::query()->create([
            'campaign_id' => $campaign->id,
            'combat_id' => $data['combat_id'] ?? null,
            'created_by_user_id' => Auth::id(),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'status' => LootDrop::STATUS_DRAFT,
            'closed_reason' => null,
            'is_quick_access' => false,
            'is_shop' => false,
        ]);

        $placement = null;

        if ($folder) {
            $nextPosition = (
                (int) WorkspaceFolderItem::query()
                    ->where('folder_id', $folder->id)
                    ->max('position')
            ) + 1;

            $placement = WorkspaceFolderItem::query()->create([
                'folder_id' => $folder->id,
                'folderable_type' => LootDrop::class,
                'folderable_id' => $drop->id,
                'position' => $nextPosition,
                'metadata' => [],
            ]);
        }

        $this->decorateLootDropForMaster(
            $campaign,
            $drop,
            $placement,
            true
        );

        return response()->json([
            'success' => true,
            'message' => 'Espólio criado.',
            'drop' => $drop,
        ], 201);
    }


    public function updateDropPresentation(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $this->assertNotShopDrop($lootDrop);

        abort_if(
            (bool) $lootDrop->is_quick_access,
            404
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'remove_image' => ['nullable', 'boolean'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:8192',
            ],
        ]);

        $oldImagePath =
            is_string($lootDrop->image_path)
                ? trim($lootDrop->image_path)
                : '';

        $newImagePath =
            $oldImagePath !== ''
                ? $oldImagePath
                : null;

        if ((bool) ($data['remove_image'] ?? false)) {
            $newImagePath = null;
        }

        if ($request->hasFile('image')) {
            $newImagePath =
                $this->storeLootDropImage(
                    $request->file('image'),
                    $lootDrop
                );
        }

        $lootDrop->forceFill([
            'name' => trim((string) $data['name']),
            'description' => isset($data['description'])
                && trim((string) $data['description']) !== ''
                    ? trim((string) $data['description'])
                    : null,
            'image_path' => $newImagePath,
        ])->save();

        if (
            $oldImagePath !== ''
            && $oldImagePath !== $newImagePath
            && Storage::disk('public')->exists($oldImagePath)
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $lootDrop->load(
            $this->workspaceRelations()
        );

        $this->decorateLootDropForMaster(
            $campaign,
            $lootDrop
        );

        return response()->json([
            'success' => true,
            'message' => 'Espólio atualizado.',
            'drop' => $lootDrop,
        ]);
    }


    public function dropImage(
        Campaign $campaign,
        LootDrop $lootDrop
    ): BinaryFileResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $this->assertNotShopDrop($lootDrop);

        return $this->serveImage(
            $lootDrop->image_path
        );
    }


    public function destroyDrop(
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        /*
        | O acesso rápido é uma estrutura permanente da campanha e não pode
        | ser removido como um Espólio/Loja comum.
        */
        abort_if(
            (bool) $lootDrop->is_quick_access,
            404
        );

        $isShop =
            (bool) $lootDrop->is_shop;

        /*
        | Guardamos os caminhos antes da exclusão. Os registros relacionados
        | a itens, resgates, vendas, moedas e destinatários são removidos pelas
        | FKs com cascadeOnDelete já existentes no sistema de Espólios.
        |
        | Isso NÃO remove CharacterItem nem desfaz carteira de personagem:
        | o que já foi entregue/comprado pelo Player continua com ele.
        */
        $lootDropImagePath =
            is_string($lootDrop->image_path)
                ? trim($lootDrop->image_path)
                : '';

        $shopImagePath =
            is_string($lootDrop->shop_image_path)
                ? trim($lootDrop->shop_image_path)
                : '';

        $shopBackgroundPath =
            is_string($lootDrop->shop_background_path)
                ? trim($lootDrop->shop_background_path)
                : '';

        $imagePaths = LootDropItem::query()
            ->where(
                'loot_drop_id',
                $lootDrop->id
            )
            ->pluck('image_path')
            ->filter(
                fn ($path) =>
                    is_string($path)
                    && trim($path) !== ''
            )
            ->map(
                fn ($path) =>
                    trim($path)
            )
            ->values();

        DB::transaction(
            function () use ($lootDrop): void {
                /*
                | WorkspaceFolderItem é uma associação polimórfica e não
                | depende da FK de loot_drops. Removemos a posição da loja
                | antes de apagar a sessão.
                */
                if (
                    $this->workspaceFolderSystemReady()
                ) {
                    WorkspaceFolderItem::query()
                        ->where(
                            'folderable_type',
                            LootDrop::class
                        )
                        ->where(
                            'folderable_id',
                            $lootDrop->id
                        )
                        ->delete();
                }

                /*
                | A exclusão do LootDrop derruba em cascata:
                | - loot_drop_items
                | - loot_item_recipients
                | - loot_claims (inclusive vendas de loja)
                | - loot_currency_entries
                | - loot_currency_recipients
                | - loot_currency_claims
                */
                $lootDrop->delete();
            }
        );

        foreach ($imagePaths as $imagePath) {
            if (
                Storage::disk('public')
                    ->exists($imagePath)
            ) {
                Storage::disk('public')
                    ->delete($imagePath);
            }
        }

        if (
            $lootDropImagePath !== ''
            && Storage::disk('public')
                ->exists($lootDropImagePath)
        ) {
            Storage::disk('public')
                ->delete($lootDropImagePath);
        }

        if (
            $shopImagePath !== ''
            && Storage::disk('public')
                ->exists($shopImagePath)
        ) {
            Storage::disk('public')
                ->delete($shopImagePath);
        }

        if (
            $shopBackgroundPath !== ''
            && Storage::disk('public')
                ->exists($shopBackgroundPath)
        ) {
            Storage::disk('public')
                ->delete($shopBackgroundPath);
        }

        return response()->json([
            'success' => true,
            'message' =>
                $isShop
                    ? 'Loja excluída.'
                    : 'Espólio excluído.',
        ]);
    }

    public function storeShop(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        $this->authorizeCampaign($campaign);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'shopkeeper_npc_id' => ['nullable', 'integer'],
            'shop_attendant_name' => ['nullable', 'string', 'max:150'],
            'shop_has_counter' => ['nullable', 'boolean'],
            'shop_counter_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'shop_background_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'shop_image_zoom' => [
                'nullable',
                'integer',
                'min:70',
                'max:180',
            ],
            'shop_image_position' => [
                'nullable',
                'string',
                Rule::in(['top', 'center', 'bottom']),
            ],
            'shop_background_zoom' => [
                'nullable',
                'integer',
                'min:100',
                'max:180',
            ],
            'shop_background_position' => [
                'nullable',
                'string',
                Rule::in(['top', 'center', 'bottom']),
            ],
            'shop_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
                'dimensions:max_width=6000,max_height=6000',
            ],
            'shop_background' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:12288',
                'dimensions:max_width=8192,max_height=8192',
            ],
        ], [
            'shop_image.uploaded' =>
                'O retrato não pôde ser enviado. O arquivo pode ultrapassar o limite configurado no PHP.',
            'shop_image.image' =>
                'O retrato precisa ser uma imagem válida.',
            'shop_image.mimes' =>
                'O retrato deve estar em JPG, JPEG, PNG ou WEBP.',
            'shop_image.max' =>
                'O retrato é pesado demais para a loja.',
            'shop_image.dimensions' =>
                'O retrato possui dimensões grandes demais. Use no máximo 6000×6000 px.',
            'shop_background.uploaded' =>
                'O cenário não pôde ser enviado. O arquivo pode ultrapassar o limite configurado no PHP.',
            'shop_background.image' =>
                'O cenário precisa ser uma imagem válida.',
            'shop_background.mimes' =>
                'O cenário deve estar em JPG, JPEG, PNG ou WEBP.',
            'shop_background.max' =>
                'O cenário é pesado demais para a loja.',
            'shop_background.dimensions' =>
                'O cenário possui dimensões grandes demais. Use no máximo 8192×8192 px.',
        ]);

        $folder = null;

        if (
            !empty($data['folder_id'])
            && !$this->workspaceFolderSystemReady()
        ) {
            abort(
                503,
                'O sistema de pastas ainda não foi migrado. Rode php artisan migrate.'
            );
        }

        if (!empty($data['folder_id'])) {
            $folder = WorkspaceFolder::query()
                ->whereKey((int) $data['folder_id'])
                ->forContext(
                    FolderContextRegistry::CAMPAIGN_SHOPS,
                    (int) $campaign->id
                )
                ->where('owner_user_id', Auth::id())
                ->firstOrFail();
        }

        $shopkeeperId = isset($data['shopkeeper_npc_id'])
            ? (int) $data['shopkeeper_npc_id']
            : null;

        if ($shopkeeperId) {
            $this->assertOwnedShopkeeperNpc($shopkeeperId);
        }

        $shopAttendantName = trim(
            (string) ($data['shop_attendant_name'] ?? '')
        );

        $shopAttendantName = $shopkeeperId
            ? null
            : ($shopAttendantName !== '' ? $shopAttendantName : null);

        $shop = LootDrop::query()->create([
            'campaign_id' => $campaign->id,
            'combat_id' => null,
            'created_by_user_id' => Auth::id(),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'status' => LootDrop::STATUS_DRAFT,
            'closed_reason' => null,
            'is_quick_access' => false,
            'is_shop' => true,
            'shopkeeper_npc_id' => $shopkeeperId,
            'shop_attendant_name' => $shopAttendantName,
            'shop_image_path' => null,
            'shop_background_path' => null,
            'shop_has_counter' => (bool) ($data['shop_has_counter'] ?? true),
            'shop_counter_color' => strtoupper(
                (string) ($data['shop_counter_color'] ?? '#6B3A24')
            ),
            'shop_background_color' => strtoupper(
                (string) ($data['shop_background_color'] ?? '#6F4A33')
            ),
            'shop_image_zoom' => (int) (
                $data['shop_image_zoom'] ?? 100
            ),
            'shop_image_position' => (string) (
                $data['shop_image_position'] ?? 'center'
            ),
            'shop_background_zoom' => (int) (
                $data['shop_background_zoom'] ?? 100
            ),
            'shop_background_position' => (string) (
                $data['shop_background_position'] ?? 'center'
            ),
            'released_at' => null,
            'closed_at' => null,
        ]);

        if ($request->hasFile('shop_image')) {
            $shop->shop_image_path =
                $this->storeShopPresentationImage(
                    $request->file('shop_image'),
                    $shop
                );
        }

        if ($request->hasFile('shop_background')) {
            $shop->shop_background_path =
                $this->storeShopBackgroundImage(
                    $request->file('shop_background'),
                    $shop
                );
        }

        $shop->save();

        $placement = null;

        if ($folder) {
            $position =
                ((int) WorkspaceFolderItem::query()
                    ->where('folder_id', $folder->id)
                    ->max('position'))
                + 1;

            $placement = WorkspaceFolderItem::query()
                ->create([
                    'folder_id' => $folder->id,
                    'folderable_type' => LootDrop::class,
                    'folderable_id' => $shop->id,
                    'position' => $position,
                    'metadata' => [],
                ]);
        }

        $shop->loadMissing(
            'shopkeeperNpc:id,name,image_path'
        );

        $this->decorateShopForMaster(
            $campaign,
            $shop,
            $placement,
            true
        );

        return response()->json([
            'success' => true,
            'message' => 'Loja criada. Ela permanece fechada até você clicar em Abrir Loja.',
            'shop' => $shop,
        ], 201);
    }

    public function updateShopPresentation(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        abort_unless((bool) $lootDrop->is_shop, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'shopkeeper_npc_id' => ['nullable', 'integer'],
            'shop_attendant_name' => ['nullable', 'string', 'max:150'],
            'shop_has_counter' => ['nullable', 'boolean'],
            'shop_counter_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'shop_background_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'shop_image_zoom' => [
                'nullable',
                'integer',
                'min:70',
                'max:180',
            ],
            'shop_image_position' => [
                'nullable',
                'string',
                Rule::in(['top', 'center', 'bottom']),
            ],
            'shop_background_zoom' => [
                'nullable',
                'integer',
                'min:100',
                'max:180',
            ],
            'shop_background_position' => [
                'nullable',
                'string',
                Rule::in(['top', 'center', 'bottom']),
            ],
            'remove_shop_image' => ['nullable', 'boolean'],
            'remove_shop_background' => ['nullable', 'boolean'],
            'shop_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
                'dimensions:max_width=6000,max_height=6000',
            ],
            'shop_background' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:12288',
                'dimensions:max_width=8192,max_height=8192',
            ],
        ], [
            'shop_image.uploaded' =>
                'O retrato não pôde ser enviado. O arquivo pode ultrapassar o limite configurado no PHP.',
            'shop_image.image' =>
                'O retrato precisa ser uma imagem válida.',
            'shop_image.mimes' =>
                'O retrato deve estar em JPG, JPEG, PNG ou WEBP.',
            'shop_image.max' =>
                'O retrato é pesado demais para a loja.',
            'shop_image.dimensions' =>
                'O retrato possui dimensões grandes demais. Use no máximo 6000×6000 px.',
            'shop_background.uploaded' =>
                'O cenário não pôde ser enviado. O arquivo pode ultrapassar o limite configurado no PHP.',
            'shop_background.image' =>
                'O cenário precisa ser uma imagem válida.',
            'shop_background.mimes' =>
                'O cenário deve estar em JPG, JPEG, PNG ou WEBP.',
            'shop_background.max' =>
                'O cenário é pesado demais para a loja.',
            'shop_background.dimensions' =>
                'O cenário possui dimensões grandes demais. Use no máximo 8192×8192 px.',
        ]);

        $shopkeeperId = isset($data['shopkeeper_npc_id'])
            ? (int) $data['shopkeeper_npc_id']
            : null;

        if ($shopkeeperId) {
            $this->assertOwnedShopkeeperNpc($shopkeeperId);
        }

        $shopAttendantName = trim(
            (string) ($data['shop_attendant_name'] ?? '')
        );

        $shopAttendantName = $shopkeeperId
            ? null
            : ($shopAttendantName !== '' ? $shopAttendantName : null);

        $oldImagePath =
            is_string($lootDrop->shop_image_path)
                ? trim($lootDrop->shop_image_path)
                : '';

        $oldBackgroundPath =
            is_string($lootDrop->shop_background_path)
                ? trim($lootDrop->shop_background_path)
                : '';

        $newImagePath = $oldImagePath !== ''
            ? $oldImagePath
            : null;

        $newBackgroundPath = $oldBackgroundPath !== ''
            ? $oldBackgroundPath
            : null;

        if ((bool) ($data['remove_shop_image'] ?? false)) {
            $newImagePath = null;
        }

        if ((bool) ($data['remove_shop_background'] ?? false)) {
            $newBackgroundPath = null;
        }

        if ($request->hasFile('shop_image')) {
            $newImagePath =
                $this->storeShopPresentationImage(
                    $request->file('shop_image'),
                    $lootDrop
                );
        }

        if ($request->hasFile('shop_background')) {
            $newBackgroundPath =
                $this->storeShopBackgroundImage(
                    $request->file('shop_background'),
                    $lootDrop
                );
        }

        $lootDrop->update([
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'shopkeeper_npc_id' => $shopkeeperId,
            'shop_attendant_name' => $shopAttendantName,
            'shop_image_path' => $newImagePath,
            'shop_background_path' => $newBackgroundPath,
            'shop_has_counter' => (bool) (
                $data['shop_has_counter']
                ?? $lootDrop->shop_has_counter
                ?? true
            ),
            'shop_counter_color' => strtoupper(
                (string) (
                    $data['shop_counter_color']
                    ?? $lootDrop->shop_counter_color
                    ?? '#6B3A24'
                )
            ),
            'shop_background_color' => strtoupper(
                (string) (
                    $data['shop_background_color']
                    ?? $lootDrop->shop_background_color
                    ?? '#6F4A33'
                )
            ),
            'shop_image_zoom' => (int) (
                $data['shop_image_zoom']
                ?? $lootDrop->shop_image_zoom
                ?? 100
            ),
            'shop_image_position' => (string) (
                $data['shop_image_position']
                ?? $lootDrop->shop_image_position
                ?? 'center'
            ),
            'shop_background_zoom' => (int) (
                $data['shop_background_zoom']
                ?? $lootDrop->shop_background_zoom
                ?? 100
            ),
            'shop_background_position' => (string) (
                $data['shop_background_position']
                ?? $lootDrop->shop_background_position
                ?? 'center'
            ),
        ]);

        foreach (
            [
                [$oldImagePath, $newImagePath],
                [$oldBackgroundPath, $newBackgroundPath],
            ] as [$oldPath, $newPath]
        ) {
            if (
                $oldPath !== ''
                && $oldPath !== ($newPath ?? '')
                && Storage::disk('public')->exists($oldPath)
            ) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $lootDrop->load(
            'shopkeeperNpc:id,name,image_path'
        );

        $this->decorateShopForMaster(
            $campaign,
            $lootDrop
        );

        return response()->json([
            'success' => true,
            'message' => 'Apresentação da loja atualizada.',
            'shop' => $lootDrop,
        ]);
    }

    public function openShop(
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);
        abort_unless((bool) $lootDrop->is_shop, 404);

        $lootDrop->forceFill([
            'status' => LootDrop::STATUS_RELEASED,
            'closed_reason' => null,
            'released_at' => now(),
            'closed_at' => null,
        ])->save();

        $lootDrop->loadMissing(
            'shopkeeperNpc:id,name,image_path'
        );

        $this->decorateShopForMaster(
            $campaign,
            $lootDrop
        );

        return response()->json([
            'success' => true,
            'message' => 'Loja aberta para a Party.',
            'shop' => $lootDrop,
        ]);
    }

    public function shopImage(
        Campaign $campaign,
        LootDrop $lootDrop
    ): BinaryFileResponse {
        $this->authorizeDrop($campaign, $lootDrop);
        abort_unless((bool) $lootDrop->is_shop, 404);

        $lootDrop->loadMissing(
            'shopkeeperNpc:id,name,image_path'
        );

        return $this->serveImage(
            $this->shopPresentationImagePath($lootDrop)
        );
    }

    public function shopBackgroundImage(
        Campaign $campaign,
        LootDrop $lootDrop
    ): BinaryFileResponse {
        $this->authorizeDrop($campaign, $lootDrop);
        abort_unless((bool) $lootDrop->is_shop, 404);

        return $this->serveImage(
            $lootDrop->shop_background_path
        );
    }

    public function shopkeeperImage(
        Campaign $campaign,
        Npc $npc
    ): BinaryFileResponse {
        $this->authorizeCampaign($campaign);

        abort_unless(
            (int) $npc->user_id === (int) Auth::id(),
            404
        );

        return $this->serveImage($npc->image_path);
    }

    public function addShopItem(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        abort_unless((bool) $lootDrop->is_shop, 404);

        $rules = [
            'vault_item_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ];

        foreach (self::COINS as $coin) {
            $rules['price_' . $coin] = [
                'nullable',
                'integer',
                'min:0',
                'max:999999999',
            ];
        }

        $data = $request->validate($rules);

        $hasPrice = collect(self::COINS)
            ->contains(
                fn ($coin) =>
                    (int) ($data['price_' . $coin] ?? 0) > 0
            );

        if (!$hasPrice) {
            throw ValidationException::withMessages([
                'price' => 'Informe um preço para o item.',
            ]);
        }

        $vaultItem = MasterVaultItem::query()
            ->where('user_id', Auth::id())
            ->whereKey($data['vault_item_id'])
            ->firstOrFail();

        $lootItem = LootDropItem::query()->create([
            'loot_drop_id' => $lootDrop->id,
            'vault_item_id' => $vaultItem->id,
            'name' => $vaultItem->name,
            'type' => $vaultItem->type,
            'description' => $vaultItem->description,
            'image_path' => $this->copyVaultImageToLoot(
                $vaultItem->image_path,
                $lootDrop
            ),
            'weight' => $vaultItem->weight,
            'is_magical' => (bool) $vaultItem->is_magical,
            'rarity' => $vaultItem->rarity,
            'requires_attunement' => (bool) $vaultItem->requires_attunement,
            'is_cursed' => (bool) $vaultItem->is_cursed,
            'curse_name' => $vaultItem->curse_name,
            'curse_description' => $vaultItem->curse_description,
            'armor_class' => $vaultItem->armor_class,
            'damage' => $vaultItem->damage,
            'attack_bonus' => $vaultItem->attack_bonus,
            'damage_bonus' => $vaultItem->damage_bonus,
            'ability_bonuses' => $vaultItem->ability_bonuses ?? [],
            'properties' => $vaultItem->properties ?? [],
            'modifiers' => $vaultItem->modifiers ?? [],
            'notes' => $vaultItem->notes,
            'quantity_total' => (int) $data['quantity'],
            'quantity_remaining' => (int) $data['quantity'],
            'release_status' => LootDropItem::RELEASED,
            'visibility_mode' => LootDropItem::VISIBILITY_PARTY,
            'released_at' => now(),
            'price_copper' => (int) ($data['price_copper'] ?? 0),
            'price_silver' => (int) ($data['price_silver'] ?? 0),
            'price_electrum' => (int) ($data['price_electrum'] ?? 0),
            'price_gold' => (int) ($data['price_gold'] ?? 0),
            'price_platinum' => (int) ($data['price_platinum'] ?? 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado à loja.',
            'item' => $lootItem->fresh(),
        ], 201);
    }

    public function updateShopItem(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): JsonResponse {
        $this->authorizeLootItem($campaign, $lootDrop, $lootItem);

        abort_unless((bool) $lootDrop->is_shop, 404);

        $rules = [];

        foreach (self::COINS as $coin) {
            $rules['price_' . $coin] = [
                'required',
                'integer',
                'min:0',
                'max:999999999',
            ];
        }

        $data = $request->validate($rules);

        $hasPrice = collect(self::COINS)
            ->contains(
                fn ($coin) =>
                    (int) ($data['price_' . $coin] ?? 0) > 0
            );

        if (!$hasPrice) {
            throw ValidationException::withMessages([
                'price' => 'Informe um preço para o item.',
            ]);
        }

        $lootItem->update([
            'price_copper' => (int) $data['price_copper'],
            'price_silver' => (int) $data['price_silver'],
            'price_electrum' => (int) $data['price_electrum'],
            'price_gold' => (int) $data['price_gold'],
            'price_platinum' => (int) $data['price_platinum'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preço atualizado.',
            'item' => $lootItem->fresh(),
        ]);
    }

    public function quickReleaseItem(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        $this->authorizeCampaign($campaign);

        $data = $request->validate([
            'vault_item_id' => [
                'required',
                'integer',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
            'visibility_mode' => [
                'required',
                Rule::in([
                    LootDropItem::VISIBILITY_PARTY,
                    LootDropItem::VISIBILITY_RESTRICTED,
                ]),
            ],
            'character_ids' => [
                'nullable',
                'array',
            ],
            'character_ids.*' => [
                'integer',
            ],
        ]);

        $vaultItem = MasterVaultItem::query()
            ->where('user_id', Auth::id())
            ->whereKey($data['vault_item_id'])
            ->firstOrFail();

        $characterIds = collect(
            $data['character_ids']
            ?? []
        )
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->unique()
            ->values();

        if (
            $data['visibility_mode']
            ===
            LootDropItem::VISIBILITY_RESTRICTED
            &&
            $characterIds->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'character_ids' =>
                    'Selecione ao menos um personagem.',
            ]);
        }

        $this->assertCharactersBelongToCampaign(
            $campaign,
            $characterIds
        );

        $quantity = max(
            1,
            (int) $data['quantity']
        );

        $lootItem = DB::transaction(
            function () use (
                $campaign,
                $vaultItem,
                $quantity,
                $data,
                $characterIds
            ): LootDropItem {
                Campaign::query()
                    ->whereKey($campaign->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $quickDrop = LootDrop::query()
                    ->where(
                        'campaign_id',
                        $campaign->id
                    )
                    ->where(
                        'is_quick_access',
                        true
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$quickDrop) {
                    $quickDrop = LootDrop::query()
                        ->create([
                            'campaign_id' =>
                                $campaign->id,

                            'combat_id' =>
                                null,

                            'created_by_user_id' =>
                                Auth::id(),

                            'name' =>
                                'Acesso Rápido',

                            'description' =>
                                'Itens e moedas liberados diretamente pelo Mestre.',

                            'status' =>
                                LootDrop::STATUS_RELEASED,

                            'is_quick_access' =>
                                true,

                            'released_at' =>
                                now(),
                        ]);
                } else {
                    $quickDrop->forceFill([
                        'status' =>
                            LootDrop::STATUS_RELEASED,

                        'closed_at' =>
                            null,

                        'released_at' =>
                            $quickDrop->released_at
                            ?? now(),
                    ]);

                    $quickDrop->save();
                }

                $lootItem = LootDropItem::query()
                    ->create([
                        'loot_drop_id' =>
                            $quickDrop->id,

                        'vault_item_id' =>
                            $vaultItem->id,

                        'name' =>
                            $vaultItem->name,

                        'type' =>
                            $vaultItem->type,

                        'description' =>
                            $vaultItem->description,

                        'image_path' =>
                            $this->copyVaultImageToLoot(
                                $vaultItem->image_path,
                                $quickDrop
                            ),

                        'weight' =>
                            $vaultItem->weight,

                        'is_magical' =>
                            (bool) $vaultItem->is_magical,

                        'rarity' =>
                            $vaultItem->rarity,

                        'requires_attunement' =>
                            (bool) $vaultItem->requires_attunement,

                        'is_cursed' =>
                            (bool) $vaultItem->is_cursed,

                        'curse_name' =>
                            $vaultItem->curse_name,

                        'curse_description' =>
                            $vaultItem->curse_description,

                        'armor_class' =>
                            $vaultItem->armor_class,

                        'damage' =>
                            $vaultItem->damage,

                        'attack_bonus' =>
                            $vaultItem->attack_bonus,

                        'damage_bonus' =>
                            $vaultItem->damage_bonus,

                        'ability_bonuses' =>
                            $vaultItem->ability_bonuses
                            ?? [],

                        'properties' =>
                            $vaultItem->properties
                            ?? [],

                        'modifiers' =>
                            $vaultItem->modifiers
                            ?? [],

                        'notes' =>
                            $vaultItem->notes,

                        'quantity_total' =>
                            $quantity,

                        'quantity_remaining' =>
                            $quantity,

                        'release_status' =>
                            LootDropItem::RELEASED,

                        'visibility_mode' =>
                            $data['visibility_mode'],

                        'released_at' =>
                            now(),
                    ]);

                if (
                    $data['visibility_mode']
                    ===
                    LootDropItem::VISIBILITY_RESTRICTED
                ) {
                    foreach (
                        $characterIds
                        as $characterId
                    ) {
                        LootItemRecipient::query()
                            ->create([
                                'loot_drop_item_id' =>
                                    $lootItem->id,

                                'character_id' =>
                                    $characterId,
                            ]);
                    }
                }

                return $lootItem;
            }
        );

        return response()->json([
            'success' => true,
            'message' =>
                "{$quantity}× {$vaultItem->name} liberado(s) agora.",
            'item' =>
                $lootItem->fresh([
                    'recipients.character:id,name,user_id',
                ]),
        ], 201);
    }

    public function quickReleaseCurrency(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        $this->authorizeCampaign($campaign);

        $rules = [
            'label' => ['nullable', 'string', 'max:120'],
            'distribution_mode' => [
                'required',
                Rule::in([
                    'open',
                    LootCurrencyEntry::DISTRIBUTION_SPLIT,
                    LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM,
                ]),
            ],
            'character_ids' => ['nullable', 'array'],
            'character_ids.*' => ['integer'],
        ];

        foreach (self::COINS as $coin) {
            $rules[$coin] = [
                'nullable',
                'integer',
                'min:0',
                'max:999999999',
            ];
        }

        $data = $request->validate($rules);

        $hasCurrency = collect(self::COINS)
            ->contains(
                fn ($coin) =>
                    (int) ($data[$coin] ?? 0) > 0
            );

        if (!$hasCurrency) {
            throw ValidationException::withMessages([
                'currency' => 'Informe ao menos uma moeda.',
            ]);
        }

        $mode = (string) $data['distribution_mode'];

        $characterIds = collect(
            $data['character_ids']
            ?? []
        )
            ->map(
                fn ($id) =>
                    (int) $id
            )
            ->unique()
            ->values();

        if (
            $mode
            ===
            LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM
            &&
            $characterIds->count() !== 1
        ) {
            throw ValidationException::withMessages([
                'character_ids' =>
                    'Escolha exatamente um personagem para receber estas moedas.',
            ]);
        }

        if (
            $mode
            ===
            LootCurrencyEntry::DISTRIBUTION_SPLIT
            &&
            $characterIds->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'character_ids' =>
                    'Selecione ao menos um personagem para dividir as moedas.',
            ]);
        }

        if ($mode === 'open') {
            $characterIds = collect();
        }

        $this->assertCharactersBelongToCampaign(
            $campaign,
            $characterIds
        );

        $currency = DB::transaction(
            function () use (
                $campaign,
                $data,
                $mode,
                $characterIds
            ): LootCurrencyEntry {
                Campaign::query()
                    ->whereKey($campaign->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $quickDrop = LootDrop::query()
                    ->where(
                        'campaign_id',
                        $campaign->id
                    )
                    ->where(
                        'is_quick_access',
                        true
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$quickDrop) {
                    $quickDrop = LootDrop::query()
                        ->create([
                            'campaign_id' =>
                                $campaign->id,

                            'combat_id' =>
                                null,

                            'created_by_user_id' =>
                                Auth::id(),

                            'name' =>
                                'Acesso Rápido',

                            'description' =>
                                'Itens e moedas liberados diretamente pelo Mestre.',

                            'status' =>
                                LootDrop::STATUS_RELEASED,

                            'is_quick_access' =>
                                true,

                            'is_shop' =>
                                false,

                            'released_at' =>
                                now(),
                        ]);
                } else {
                    $quickDrop->forceFill([
                        'status' =>
                            LootDrop::STATUS_RELEASED,

                        'closed_reason' =>
                            null,

                        'closed_at' =>
                            null,

                        'released_at' =>
                            $quickDrop->released_at
                            ?? now(),
                    ]);

                    $quickDrop->save();
                }

                $visibility =
                    $mode === 'open'
                        ? LootCurrencyEntry::VISIBILITY_PARTY
                        : LootCurrencyEntry::VISIBILITY_RESTRICTED;

                $payload = [
                    'loot_drop_id' =>
                        $quickDrop->id,

                    'label' =>
                        trim(
                            (string) ($data['label'] ?? '')
                        ) ?: null,

                    'release_status' =>
                        LootCurrencyEntry::RELEASED,

                    'visibility_mode' =>
                        $visibility,

                    'distribution_mode' =>
                        $mode,

                    'released_at' =>
                        now(),

                    'withdrawn_at' =>
                        null,
                ];

                foreach (self::COINS as $coin) {
                    $value =
                        max(
                            0,
                            (int) ($data[$coin] ?? 0)
                        );

                    $payload[$coin . '_total'] =
                        $value;

                    $payload[$coin . '_remaining'] =
                        $value;
                }

                $currency =
                    LootCurrencyEntry::query()
                        ->create($payload);

                if (
                    $mode
                    ===
                    LootCurrencyEntry::DISTRIBUTION_SPLIT
                ) {
                    $allocations =
                        $this->splitCurrency(
                            $currency,
                            $characterIds
                        );

                    foreach (
                        $characterIds as $index => $characterId
                    ) {
                        LootCurrencyRecipient::query()
                            ->create([
                                'loot_currency_entry_id' =>
                                    $currency->id,

                                'character_id' =>
                                    $characterId,

                                ...$allocations[$index],
                            ]);
                    }
                } elseif (
                    $mode
                    ===
                    LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM
                ) {
                    LootCurrencyRecipient::query()
                        ->create([
                            'loot_currency_entry_id' =>
                                $currency->id,

                            'character_id' =>
                                $characterIds->first(),
                        ]);
                }

                return $currency;
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Moedas rápidas disponibilizadas.',
            'currency' =>
                $currency->fresh([
                    'recipients.character:id,name,user_id',
                    'claims.character:id,name,user_id',
                ]),
        ], 201);
    }

    public function addVaultItem(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $this->assertNotShopDrop($lootDrop);

        $data = $request->validate([
            'vault_item_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999999'],
        ]);

        $vaultItem = MasterVaultItem::query()
            ->where('user_id', Auth::id())
            ->whereKey($data['vault_item_id'])
            ->firstOrFail();

        $quantity = max(
            1,
            (int) ($data['quantity'] ?? $vaultItem->default_quantity)
        );

        $lootItem = LootDropItem::query()->create([
            'loot_drop_id' => $lootDrop->id,
            'vault_item_id' => $vaultItem->id,
            'name' => $vaultItem->name,
            'type' => $vaultItem->type,
            'description' => $vaultItem->description,
            'image_path' => $this->copyVaultImageToLoot(
                $vaultItem->image_path,
                $lootDrop
            ),
            'weight' => $vaultItem->weight,
            'is_magical' => (bool) $vaultItem->is_magical,
            'rarity' => $vaultItem->rarity,
            'requires_attunement' => (bool) $vaultItem->requires_attunement,
            'is_cursed' => (bool) $vaultItem->is_cursed,
            'curse_name' => $vaultItem->curse_name,
            'curse_description' => $vaultItem->curse_description,
            'armor_class' => $vaultItem->armor_class,
            'damage' => $vaultItem->damage,
            'attack_bonus' => $vaultItem->attack_bonus,
            'damage_bonus' => $vaultItem->damage_bonus,
            'ability_bonuses' => $vaultItem->ability_bonuses ?? [],
            'properties' => $vaultItem->properties ?? [],
            'modifiers' => $vaultItem->modifiers ?? [],
            'notes' => $vaultItem->notes,
            'quantity_total' => $quantity,
            'quantity_remaining' => $quantity,
            'release_status' => LootDropItem::RELEASE_HIDDEN,
            'visibility_mode' => LootDropItem::VISIBILITY_PARTY,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado ao Espólio.',
            'item' => $lootItem,
        ], 201);
    }

    public function releaseItem(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): JsonResponse {
        $this->authorizeLootItem($campaign, $lootDrop, $lootItem);

        $this->assertNotShopDrop($lootDrop);

        $data = $request->validate([
            'visibility_mode' => [
                'required',
                Rule::in([
                    LootDropItem::VISIBILITY_PARTY,
                    LootDropItem::VISIBILITY_RESTRICTED,
                ]),
            ],
            'character_ids' => ['nullable', 'array'],
            'character_ids.*' => ['integer'],
        ]);

        $characterIds = collect($data['character_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if (
            $data['visibility_mode'] === LootDropItem::VISIBILITY_RESTRICTED
            && $characterIds->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'character_ids' => 'Selecione ao menos um personagem.',
            ]);
        }

        $this->assertCharactersBelongToCampaign($campaign, $characterIds);

        DB::transaction(function () use ($lootItem, $data, $characterIds, $lootDrop) {
            LootItemRecipient::query()
                ->where('loot_drop_item_id', $lootItem->id)
                ->delete();

            if ($data['visibility_mode'] === LootDropItem::VISIBILITY_RESTRICTED) {
                foreach ($characterIds as $characterId) {
                    LootItemRecipient::query()->create([
                        'loot_drop_item_id' => $lootItem->id,
                        'character_id' => $characterId,
                    ]);
                }
            }

            $lootItem->update([
                'visibility_mode' => $data['visibility_mode'],
                'release_status' => LootDropItem::RELEASED,
                'released_at' => now(),
                'withdrawn_at' => null,
            ]);

            if ($lootDrop->status === LootDrop::STATUS_DRAFT) {
                $lootDrop->update([
                    'status' => LootDrop::STATUS_RELEASED,
                    'released_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Item liberado.',
            'item' => $lootItem->fresh('recipients.character'),
        ]);
    }

    public function withdrawItem(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): JsonResponse {
        $this->authorizeLootItem($campaign, $lootDrop, $lootItem);

        $this->assertNotShopDrop($lootDrop);

        $lootItem->update([
            'release_status' => LootDropItem::RELEASE_WITHDRAWN,
            'withdrawn_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item recolhido pelo Mestre.',
        ]);
    }

    public function releaseAllItems(
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $this->assertNotShopDrop($lootDrop);

        abort_if(
            $lootDrop->status === LootDrop::STATUS_CLOSED,
            422,
            'Este Espólio já foi encerrado.'
        );

        $releasedCount = DB::transaction(
            function () use ($lootDrop): int {
                $items = LootDropItem::query()
                    ->where('loot_drop_id', $lootDrop->id)
                    ->where('quantity_remaining', '>', 0)
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    return 0;
                }

                $itemIds = $items
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                LootItemRecipient::query()
                    ->whereIn('loot_drop_item_id', $itemIds)
                    ->delete();

                $releasedAt = now();

                foreach ($items as $item) {
                    $item->forceFill([
                        'visibility_mode' =>
                            LootDropItem::VISIBILITY_PARTY,

                        'release_status' =>
                            LootDropItem::RELEASED,

                        'released_at' =>
                            $releasedAt,

                        'withdrawn_at' =>
                            null,
                    ]);

                    $item->save();
                }

                if ($lootDrop->status === LootDrop::STATUS_DRAFT) {
                    $lootDrop->forceFill([
                        'status' =>
                            LootDrop::STATUS_RELEASED,

                        'released_at' =>
                            $lootDrop->released_at
                            ?? $releasedAt,
                    ]);

                    $lootDrop->save();
                }

                return $items->count();
            }
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                $releasedCount > 0
                    ? "{$releasedCount} item(ns) liberado(s) para a Party."
                    : 'Não há itens disponíveis para liberar.',

            'released_count' =>
                $releasedCount,
        ]);
    }


    public function destroyItem(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): JsonResponse {
        $this->authorizeLootItem(
            $campaign,
            $lootDrop,
            $lootItem
        );

        $hasClaimHistory =
            LootClaim::query()
                ->where(
                    'loot_drop_item_id',
                    $lootItem->id
                )
                ->exists();

        if ($hasClaimHistory) {
            throw ValidationException::withMessages([
                'item' =>
                    (bool) $lootDrop->is_shop
                        ? 'Este produto já possui histórico de vendas. Mantenha o registro da loja.'
                        : 'Este item já possui histórico de resgate. Recolha os resgates ativos e mantenha o registro no Espólio.',
            ]);
        }

        $imagePath =
            is_string($lootItem->image_path)
                ? trim($lootItem->image_path)
                : '';

        DB::transaction(
            function () use ($lootItem): void {
                LootItemRecipient::query()
                    ->where(
                        'loot_drop_item_id',
                        $lootItem->id
                    )
                    ->delete();

                $lootItem->delete();
            }
        );

        if (
            $imagePath !== ''
            &&
            Storage::disk('public')
                ->exists($imagePath)
        ) {
            Storage::disk('public')
                ->delete($imagePath);
        }

        if (!(bool) $lootDrop->is_shop) {
            $this->lootClaimService
                ->syncDropCompletion((int) $lootDrop->id);
        }

        return response()->json([
            'success' =>
                true,

            'message' =>
                (bool) $lootDrop->is_shop
                    ? 'Produto removido da loja.'
                    : 'Item excluído do Espólio.',
        ]);
    }


    public function reverseItemClaim(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem,
        LootClaim $claim
    ): JsonResponse {
        $this->authorizeLootItem(
            $campaign,
            $lootDrop,
            $lootItem
        );

        $this->assertNotShopDrop($lootDrop);

        abort_unless(
            (int) $claim->loot_drop_item_id
            ===
            (int) $lootItem->id,
            404
        );

        $result =
            $this->lootClaimService
                ->reverseItemClaim(
                    $claim,
                    (int) Auth::id()
                );

        return response()->json([
            'success' =>
                true,

            'message' =>
                $result['holder_name']
                    ? "Item recolhido de {$result['holder_name']} e devolvido ao Espólio."
                    : 'Item devolvido ao Espólio.',

            'quantity_restored' =>
                $result['quantity_restored'],

            'claim_id' =>
                (int) $result['claim']->id,
        ]);
    }

    public function addCurrency(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $this->assertNotShopDrop($lootDrop);

        $rules = [
            'label' => ['nullable', 'string', 'max:120'],
        ];

        foreach (self::COINS as $coin) {
            $rules[$coin] = ['nullable', 'integer', 'min:0', 'max:999999999'];
        }

        $data = $request->validate($rules);

        $hasCurrency = collect(self::COINS)
            ->contains(fn ($coin) => (int) ($data[$coin] ?? 0) > 0);

        if (!$hasCurrency) {
            throw ValidationException::withMessages([
                'currency' => 'Informe ao menos uma moeda.',
            ]);
        }

        $payload = [
            'loot_drop_id' => $lootDrop->id,
            'label' => $data['label'] ?? null,
            'release_status' => LootCurrencyEntry::RELEASE_HIDDEN,
            'visibility_mode' => LootCurrencyEntry::VISIBILITY_PARTY,
            'distribution_mode' => LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM,
        ];

        foreach (self::COINS as $coin) {
            $value = (int) ($data[$coin] ?? 0);
            $payload[$coin . '_total'] = $value;
            $payload[$coin . '_remaining'] = $value;
        }

        $entry = LootCurrencyEntry::query()->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Moedas adicionadas ao Espólio.',
            'currency' => $entry,
        ], 201);
    }

    public function releaseCurrency(
        Request $request,
        Campaign $campaign,
        LootDrop $lootDrop,
        LootCurrencyEntry $currency
    ): JsonResponse {
        $this->authorizeCurrency($campaign, $lootDrop, $currency);

        $this->assertNotShopDrop($lootDrop);

        $data = $request->validate([
            'visibility_mode' => [
                'required',
                Rule::in([
                    LootCurrencyEntry::VISIBILITY_PARTY,
                    LootCurrencyEntry::VISIBILITY_RESTRICTED,
                ]),
            ],
            'distribution_mode' => [
                'required',
                Rule::in([
                    LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM,
                    LootCurrencyEntry::DISTRIBUTION_SPLIT,
                    'open',
                ]),
            ],
            'character_ids' => ['nullable', 'array'],
            'character_ids.*' => ['integer'],
        ]);

        $mode = (string) $data['distribution_mode'];

        $characterIds = collect($data['character_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($mode === 'open') {
            $data['visibility_mode'] =
                LootCurrencyEntry::VISIBILITY_PARTY;

            $characterIds = collect();
        }

        if (
            $mode === LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM
        ) {
            if ($characterIds->count() !== 1) {
                throw ValidationException::withMessages([
                    'character_ids' =>
                        'Escolha exatamente um personagem para receber estas moedas.',
                ]);
            }

            $data['visibility_mode'] =
                LootCurrencyEntry::VISIBILITY_RESTRICTED;
        }

        if (
            $mode === LootCurrencyEntry::DISTRIBUTION_SPLIT
            && $characterIds->isEmpty()
        ) {
            $characterIds = DB::table('campaign_characters')
                ->where('campaign_id', $campaign->id)
                ->where('is_active', true)
                ->pluck('character_id')
                ->map(fn ($id) => (int) $id)
                ->values();

            $data['visibility_mode'] =
                LootCurrencyEntry::VISIBILITY_PARTY;
        } elseif (
            $mode === LootCurrencyEntry::DISTRIBUTION_SPLIT
        ) {
            $data['visibility_mode'] =
                LootCurrencyEntry::VISIBILITY_RESTRICTED;
        }

        if (
            $mode === LootCurrencyEntry::DISTRIBUTION_SPLIT
            && $characterIds->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'character_ids' =>
                    'Não há personagens ativos para dividir as moedas.',
            ]);
        }

        $this->assertCharactersBelongToCampaign(
            $campaign,
            $characterIds
        );

        DB::transaction(function () use (
            $currency,
            $data,
            $mode,
            $characterIds,
            $lootDrop
        ) {
            LootCurrencyRecipient::query()
                ->where(
                    'loot_currency_entry_id',
                    $currency->id
                )
                ->delete();

            if (
                $mode
                === LootCurrencyEntry::DISTRIBUTION_SPLIT
            ) {
                $allocations =
                    $this->splitCurrency(
                        $currency,
                        $characterIds
                    );

                foreach (
                    $characterIds as $index => $characterId
                ) {
                    LootCurrencyRecipient::query()->create([
                        'loot_currency_entry_id' => $currency->id,
                        'character_id' => $characterId,
                        ...$allocations[$index],
                    ]);
                }
            } elseif (
                $mode
                === LootCurrencyEntry::DISTRIBUTION_FIRST_CLAIM
            ) {
                LootCurrencyRecipient::query()->create([
                    'loot_currency_entry_id' => $currency->id,
                    'character_id' => $characterIds->first(),
                ]);
            }

            $currency->update([
                'visibility_mode' => $data['visibility_mode'],
                'distribution_mode' => $mode,
                'release_status' => LootCurrencyEntry::RELEASED,
                'released_at' => now(),
                'withdrawn_at' => null,
            ]);

            if ($lootDrop->status === LootDrop::STATUS_DRAFT) {
                $lootDrop->update([
                    'status' => LootDrop::STATUS_RELEASED,
                    'released_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Moedas liberadas.',
            'currency' => $currency->fresh([
                'recipients.character',
                'claims.character',
            ]),
        ]);
    }

    public function withdrawCurrency(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootCurrencyEntry $currency
    ): JsonResponse {
        $this->authorizeCurrency($campaign, $lootDrop, $currency);

        $this->assertNotShopDrop($lootDrop);

        $currency->update([
            'release_status' => LootCurrencyEntry::RELEASE_WITHDRAWN,
            'withdrawn_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Moedas recolhidas pelo Mestre.',
        ]);
    }

    public function closeDrop(
        Campaign $campaign,
        LootDrop $lootDrop
    ): JsonResponse {
        $this->authorizeDrop($campaign, $lootDrop);

        $lootDrop->update([
            'status' => LootDrop::STATUS_CLOSED,
            'closed_reason' => 'manual',
            'closed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => (bool) $lootDrop->is_shop
                ? 'Sessão de loja encerrada.'
                : 'Sessão de Espólio encerrada.',
        ]);
    }

    public function itemImage(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): BinaryFileResponse {
        $this->authorizeLootItem($campaign, $lootDrop, $lootItem);
        return $this->serveImage($lootItem->image_path);
    }

    private function workspaceRelations(): array
    {
        return [
            'shopkeeperNpc:id,name,image_path',
            'items.recipients.character:id,name,user_id,image_path',

            'items.claims' => function ($query) {
                $query
                    ->with([
                        'character:id,name,user_id,image_path',
                        'user:id,name,email',
                        'characterItem:id,character_id,name,quantity,image_path',
                        'characterItem.character:id,name,user_id,image_path',
                        'reversedBy:id,name',
                    ])
                    ->orderByDesc('claimed_at');
            },

            'currencies.recipients.character:id,name,user_id',
            'currencies.claims.character:id,name',
        ];
    }


    private function decorateLootFolderPlacement(
        LootDrop $drop,
        ?WorkspaceFolderItem $folderPlacement = null,
        bool $folderPlacementResolved = false
    ): LootDrop {
        if (
            !$folderPlacementResolved
            && $this->workspaceFolderSystemReady()
        ) {
            $folderPlacement = WorkspaceFolderItem::query()
                ->where(
                    'folderable_type',
                    LootDrop::class
                )
                ->where(
                    'folderable_id',
                    $drop->id
                )
                ->first();
        }

        $drop->setAttribute(
            'folder_id',
            $folderPlacement
                ? (int) $folderPlacement->folder_id
                : null
        );

        $drop->setAttribute(
            'folder_position',
            $folderPlacement
                ? (int) $folderPlacement->position
                : null
        );

        return $drop;
    }


    private function decorateLootDropForMaster(
        Campaign $campaign,
        LootDrop $drop,
        ?WorkspaceFolderItem $folderPlacement = null,
        bool $folderPlacementResolved = false
    ): LootDrop {
        $this->decorateLootFolderPlacement(
            $drop,
            $folderPlacement,
            $folderPlacementResolved
        );

        $hasImage =
            is_string($drop->image_path)
            && trim($drop->image_path) !== '';

        $drop->setAttribute(
            'image_url',
            $hasImage
                ? route(
                    'master.loot.image',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => $drop,
                    ],
                    false
                )
                : null
        );

        $drop->setAttribute(
            'has_custom_image',
            $hasImage
        );

        return $drop;
    }


    private function decorateShopForMaster(
        Campaign $campaign,
        LootDrop $shop,
        ?WorkspaceFolderItem $folderPlacement = null,
        bool $folderPlacementResolved = false
    ): LootDrop {
        $shop->loadMissing(
            'shopkeeperNpc:id,name,image_path'
        );

        if (
            !$folderPlacementResolved
            && $this->workspaceFolderSystemReady()
        ) {
            $folderPlacement = WorkspaceFolderItem::query()
                ->where(
                    'folderable_type',
                    LootDrop::class
                )
                ->where(
                    'folderable_id',
                    $shop->id
                )
                ->first();
        }

        $shop->setAttribute(
            'folder_id',
            $folderPlacement
                ? (int) $folderPlacement->folder_id
                : null
        );

        $shop->setAttribute(
            'folder_position',
            $folderPlacement
                ? (int) $folderPlacement->position
                : null
        );

        $path = $this->shopPresentationImagePath($shop);

        $shopkeeperName = trim(
            (string) (
                $shop->shopkeeperNpc?->name
                ?? $shop->shop_attendant_name
                ?? ''
            )
        );

        $shop->setAttribute(
            'shopkeeper_name',
            $shopkeeperName !== ''
                ? $shopkeeperName
                : null
        );

        $shop->setAttribute(
            'presentation_image_url',
            $path
                ? route(
                    'master.shops.image',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => $shop,
                    ],
                    false
                )
                : null
        );

        $shop->setAttribute(
            'has_custom_shop_image',
            is_string($shop->shop_image_path)
            && trim($shop->shop_image_path) !== ''
        );

        $hasBackground =
            is_string($shop->shop_background_path)
            && trim($shop->shop_background_path) !== '';

        $shop->setAttribute(
            'background_image_url',
            $hasBackground
                ? route(
                    'master.shops.background',
                    [
                        'campaign' => $campaign,
                        'lootDrop' => $shop,
                    ],
                    false
                )
                : null
        );

        $shop->setAttribute(
            'has_custom_shop_background',
            $hasBackground
        );

        return $shop;
    }

    private function workspaceFolderSystemReady(): bool
    {
        return Schema::hasTable(
            'workspace_folders'
        )
            && Schema::hasTable(
                'workspace_folder_items'
            );
    }


    private function workspaceFolderPayload(
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
                ?? 0
            ),
        ];
    }


    private function shopPresentationImagePath(
        LootDrop $shop
    ): ?string {
        if (
            is_string($shop->shop_image_path)
            && trim($shop->shop_image_path) !== ''
        ) {
            return trim($shop->shop_image_path);
        }

        $characterPath =
            $shop->shopkeeperNpc?->image_path;

        return is_string($characterPath)
            && trim($characterPath) !== ''
                ? trim($characterPath)
                : null;
    }

    private function storeLootDropImage(
        $file,
        LootDrop $lootDrop
    ): string {
        $extension = strtolower(
            (string) $file->getClientOriginalExtension()
        );

        $filename = uniqid('loot_', true)
            . ($extension !== '' ? '.' . $extension : '');

        $directory = 'loot-drops/' . $lootDrop->id;

        $path = Storage::disk('public')
            ->putFileAs(
                $directory,
                $file,
                $filename
            );

        if (!is_string($path) || trim($path) === '') {
            throw ValidationException::withMessages([
                'image' =>
                    'Não foi possível salvar a foto do Espólio.',
            ]);
        }

        return $path;
    }


    private function storeShopPresentationImage(
        $file,
        LootDrop $shop
    ): string {
        $extension = strtolower(
            (string) $file->getClientOriginalExtension()
        );

        $filename = uniqid('shop_', true)
            . ($extension !== '' ? '.' . $extension : '');

        $directory = 'shop-images/' . $shop->id;

        $path = Storage::disk('public')
            ->putFileAs(
                $directory,
                $file,
                $filename
            );

        if (!is_string($path) || trim($path) === '') {
            throw ValidationException::withMessages([
                'shop_image' =>
                    'Não foi possível salvar a imagem da loja.',
            ]);
        }

        return $path;
    }


    private function storeShopBackgroundImage(
        $file,
        LootDrop $shop
    ): string {
        $extension = strtolower(
            (string) $file->getClientOriginalExtension()
        );

        $filename = uniqid('background_', true)
            . ($extension !== '' ? '.' . $extension : '');

        $directory = 'shop-images/' . $shop->id;

        $path = Storage::disk('public')
            ->putFileAs(
                $directory,
                $file,
                $filename
            );

        if (!is_string($path) || trim($path) === '') {
            throw ValidationException::withMessages([
                'shop_background' =>
                    'Não foi possível salvar o fundo da loja.',
            ]);
        }

        return $path;
    }

    private function assertOwnedShopkeeperNpc(int $npcId): void
    {
        $exists = Npc::query()
            ->whereKey($npcId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$exists) {
            throw ValidationException::withMessages([
                'shopkeeper_npc_id' =>
                    'O NPC escolhido não pertence ao seu Cofre.',
            ]);
        }
    }

    private function authorizeCampaign(Campaign $campaign): void
    {
        abort_unless($campaign->isOwner(Auth::id()), 404);
    }

    private function authorizeDrop(Campaign $campaign, LootDrop $lootDrop): void
    {
        $this->authorizeCampaign($campaign);
        abort_unless((int) $lootDrop->campaign_id === (int) $campaign->id, 404);
    }


    private function assertNotShopDrop(LootDrop $lootDrop): void
    {
        abort_if(
            (bool) $lootDrop->is_shop,
            404
        );
    }

    private function authorizeLootItem(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootDropItem $lootItem
    ): void {
        $this->authorizeDrop($campaign, $lootDrop);
        abort_unless((int) $lootItem->loot_drop_id === (int) $lootDrop->id, 404);
    }

    private function authorizeCurrency(
        Campaign $campaign,
        LootDrop $lootDrop,
        LootCurrencyEntry $currency
    ): void {
        $this->authorizeDrop($campaign, $lootDrop);
        abort_unless((int) $currency->loot_drop_id === (int) $lootDrop->id, 404);
    }

    private function assertCharactersBelongToCampaign(
        Campaign $campaign,
        $characterIds
    ): void {
        if ($characterIds->isEmpty()) {
            return;
        }

        $validCount = DB::table('campaign_characters')
            ->where('campaign_id', $campaign->id)
            ->where('is_active', true)
            ->whereIn('character_id', $characterIds)
            ->distinct()
            ->count('character_id');

        if ($validCount !== $characterIds->count()) {
            throw ValidationException::withMessages([
                'character_ids' => 'Há personagem selecionado que não está ativo nesta campanha.',
            ]);
        }
    }

    private function copyVaultImageToLoot(
        ?string $sourcePath,
        LootDrop $drop
    ): ?string {
        if (!is_string($sourcePath) || trim($sourcePath) === '') {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($sourcePath)) {
            return null;
        }

        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $filename = uniqid('vault_', true)
            . ($extension ? '.' . $extension : '');

        $destination = 'loot-items/' . $drop->id . '/' . $filename;
        $disk->copy($sourcePath, $destination);

        return $destination;
    }

    private function splitCurrency(
        LootCurrencyEntry $entry,
        $characterIds
    ): array {
        $count = $characterIds->count();

        return $characterIds
            ->values()
            ->map(function ($characterId, $index) use ($entry, $count) {
                $allocation = [];

                foreach (self::COINS as $coin) {
                    $total = (int) $entry->{$coin . '_total'};
                    $base = intdiv($total, $count);
                    $remainder = $total % $count;

                    $allocation[$coin . '_allocated'] =
                        $base + ($index < $remainder ? 1 : 0);
                }

                return $allocation;
            })
            ->all();
    }

    private function serveImage(?string $path): BinaryFileResponse
    {
        abort_unless(is_string($path) && trim($path) !== '', 404);

        $relative = ltrim(str_replace('\\', '/', $path), '/');
        abort_if(str_contains($relative, '../'), 404);

        $fullPath = storage_path('app/public/' . $relative);
        abort_unless(is_file($fullPath), 404);

        return response()->file($fullPath, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}