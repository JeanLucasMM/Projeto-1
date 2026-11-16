<?php



namespace App\Http\Controllers;



use App\Http\Requests\SaveMasterVaultItemRequest;

use App\Models\MasterVaultItem;
use App\Models\WorkspaceFolder;
use App\Models\WorkspaceFolderItem;
use App\Services\Folders\FolderContextRegistry;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

use Illuminate\View\View;

use Symfony\Component\HttpFoundation\BinaryFileResponse;



class MasterVaultController extends Controller

{

    public function index(Request $request): JsonResponse|View
    {
        $userId = (int) Auth::id();

        $items = MasterVaultItem::query()
            ->where('user_id', $userId)
            ->orderByDesc('favorite')
            ->orderBy('name')
            ->get();

        $folderSystemReady = $this->folderSystemReady();
        $folders = collect();
        $placements = collect();

        if ($folderSystemReady) {
            $folders = WorkspaceFolder::query()
                ->forContext(FolderContextRegistry::MASTER_VAULT, $userId)
                ->where('owner_user_id', $userId)
                ->withCount('placements')
                ->orderBy('position')
                ->orderBy('name')
                ->get();

            if ($items->isNotEmpty()) {
                $placements = WorkspaceFolderItem::query()
                    ->where('folderable_type', MasterVaultItem::class)
                    ->whereIn('folderable_id', $items->pluck('id'))
                    ->get()
                    ->keyBy('folderable_id');
            }
        }

        $payload = $items
            ->map(fn (MasterVaultItem $item) => $this->payload(
                $item,
                $placements->get($item->id)?->folder_id
            ))
            ->values();

        $folderPayload = $folders
            ->map(fn (WorkspaceFolder $folder) => $this->folderPayload($folder))
            ->values();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'items' => $payload,
                'folders' => $folderPayload,
                'folder_system_ready' => $folderSystemReady,
            ]);
        }

        return view('master.vault.index', [
            'vaultItems' => $payload,
            'vaultFolders' => $folderPayload,
            'vaultFolderSystemReady' => $folderSystemReady,
            'vaultScopeId' => $userId,
        ]);
    }

    public function store(SaveMasterVaultItemRequest $request): JsonResponse

    {

        $data = $this->deriveLegacyFields(

            $this->mergeWeaponRangeFields(

                $request->normalized(),

                $request

            )

        );

        $image = $request->file('image');



        if ($image) {

            $data['image_path'] = $image->store(

                'master-vault-items/' . Auth::id(),

                'public'

            );

        }



        $data['user_id'] = Auth::id();



        $item = MasterVaultItem::query()->create($data)->fresh();



        return response()->json([

            'success' => true,

            'message' => 'Item salvo no Vault.',

            'item' => $this->payload($item, null),

        ], 201);

    }



    public function update(

        SaveMasterVaultItemRequest $request,

        MasterVaultItem $vaultItem

    ): JsonResponse {

        $this->authorizeItem($vaultItem);



        $data = $this->deriveLegacyFields(

            $this->mergeWeaponRangeFields(

                $request->normalized(),

                $request

            )

        );

        $image = $request->file('image');

        $removeImage = $request->boolean('remove_image');



        if ($removeImage && $vaultItem->image_path) {

            Storage::disk('public')->delete($vaultItem->image_path);

            $data['image_path'] = null;

        }



        if ($image) {

            if ($vaultItem->image_path) {

                Storage::disk('public')->delete($vaultItem->image_path);

            }



            $data['image_path'] = $image->store(

                'master-vault-items/' . Auth::id(),

                'public'

            );

        }



        $vaultItem->update($data);

        $vaultItem->refresh();



        return response()->json([

            'success' => true,

            'message' => 'Item do Vault atualizado.',

            'item' => $this->payload($vaultItem, $this->folderIdForItem((int) $vaultItem->id)),

        ]);

    }



    public function duplicate(MasterVaultItem $vaultItem): JsonResponse

    {

        $this->authorizeItem($vaultItem);



        $copy = $vaultItem->replicate([

            'created_at',

            'updated_at',

            'deleted_at',

        ]);



        $copy->name = trim($vaultItem->name . ' — Cópia');

        $copy->favorite = false;

        $copy->image_path = $this->duplicateVaultImage(

            $vaultItem->image_path

        );

        $copy->save();

        $copy->refresh();

        $folderId = null;
        if ($this->folderSystemReady()) {
            $placement = WorkspaceFolderItem::query()
                ->where('folderable_type', MasterVaultItem::class)
                ->where('folderable_id', $vaultItem->id)
                ->first();

            if ($placement) {
                $folderId = (int) $placement->folder_id;
                $position = ((int) WorkspaceFolderItem::query()
                    ->where('folder_id', $folderId)
                    ->max('position')) + 1;

                WorkspaceFolderItem::query()->create([
                    'folder_id' => $folderId,
                    'folderable_type' => MasterVaultItem::class,
                    'folderable_id' => $copy->id,
                    'position' => $position,
                    'metadata' => [],
                ]);
            }
        }

        return response()->json([

            'success' => true,

            'message' => 'Item duplicado no Vault.',

            'item' => $this->payload($copy, $folderId),

        ], 201);

    }



    public function destroy(MasterVaultItem $vaultItem): JsonResponse

    {

        $this->authorizeItem($vaultItem);

        if ($this->folderSystemReady()) {
            WorkspaceFolderItem::query()
                ->where('folderable_type', MasterVaultItem::class)
                ->where('folderable_id', $vaultItem->id)
                ->delete();
        }

        $vaultItem->delete();



        return response()->json([

            'success' => true,

            'message' => 'Item removido do Vault.',

        ]);

    }



    public function image(MasterVaultItem $vaultItem): BinaryFileResponse

    {

        $this->authorizeItem($vaultItem);



        abort_unless(

            is_string($vaultItem->image_path)

            && trim($vaultItem->image_path) !== '',

            404

        );



        $relative = ltrim(

            str_replace('\\\\', '/', $vaultItem->image_path),

            '/'

        );



        abort_if(str_contains($relative, '../'), 404);



        $fullPath = storage_path('app/public/' . $relative);

        abort_unless(is_file($fullPath), 404);



        return response()->file($fullPath, [

            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',

            'Pragma' => 'no-cache',

            'Expires' => '0',

            'X-Content-Type-Options' => 'nosniff',

        ]);

    }


    /**

     * Mantém o Vault compatível com o CharacterItemController atual.

     * properties.* continua sendo a fonte mecânica real.

     */



    private function duplicateVaultImage(

        ?string $sourcePath

    ): ?string {

        if (

            !is_string($sourcePath)

            || trim($sourcePath) === ''

        ) {

            return null;

        }



        $disk = Storage::disk('public');



        if (!$disk->exists($sourcePath)) {

            return null;

        }



        $extension = pathinfo(

            $sourcePath,

            PATHINFO_EXTENSION

        );



        $filename =

            uniqid(

                'vault_copy_',

                true

            )

            . (

                $extension

                    ? '.' . $extension

                    : ''

            );



        $destination =

            'master-vault-items/'

            . Auth::id()

            . '/'

            . $filename;



        $disk->copy(

            $sourcePath,

            $destination

        );



        return $destination;

    }



    /**

     * SaveMasterVaultItemRequest pode normalizar apenas os campos que ele

     * conhece. Distância/Alcance foram adicionados depois, então preservamos

     * explicitamente os valores brutos enviados pelo editor.

     *

     * Também migra o formato temporário usado pela V10.5:

     * properties.weapon.range.{normal,max,unit}

     * properties.weapon.reach.{distance,unit}

     */

    private function mergeWeaponRangeFields(

        array $data,

        SaveMasterVaultItemRequest $request

    ): array {

        $properties = is_array($data['properties'] ?? null)

            ? $data['properties']

            : [];



        $weapon = is_array($properties['weapon'] ?? null)

            ? $properties['weapon']

            : [];



        /*

         * Habilidades do Vault chegam por FormData. Isso significa que

         * `usage.enabled = false` chega como string "0". Antes de persistir,

         * transformamos esse valor em boolean real para que o snapshot do

         * Vault, Espólio/Loja e Inventário mantenha Passiva como Passiva.

         */

        $rawFeatures = $request->input('properties.features');



        if (is_array($properties['features'] ?? null)) {

            $features = array_values($properties['features']);

            $rawFeatures = is_array($rawFeatures)

                ? array_values($rawFeatures)

                : [];



            foreach ($features as $index => $feature) {

                if (!is_array($feature)) {

                    continue;

                }



                $rawFeature = is_array($rawFeatures[$index] ?? null)

                    ? $rawFeatures[$index]

                    : [];



                $rawUsage = is_array($rawFeature['usage'] ?? null)

                    ? $rawFeature['usage']

                    : [];



                $usage = is_array($feature['usage'] ?? null)

                    ? $feature['usage']

                    : [];



                $enabledSource = array_key_exists('enabled', $rawUsage)

                    ? $rawUsage['enabled']

                    : ($usage['enabled'] ?? false);



                $enabled = filter_var(

                    $enabledSource,

                    FILTER_VALIDATE_BOOLEAN

                );



                if (!$enabled) {

                    /*

                     * Não carregamos defaults de 1/dia em habilidades

                     * passivas. O único estado relevante é enabled=false.

                     */

                    $feature['usage'] = [

                        'enabled' => false,

                    ];

                } else {

                    $usage['enabled'] = true;

                    $feature['usage'] = $usage;

                }



                $features[$index] = $feature;

            }



            $properties['features'] = $features;

        }



        /*
         * Vestígios: preserva Dormente, Desperta e Exaltada separadamente.
         * O estágio efetivo pertence ao inventário de cada personagem,
         * não à definição global do item no Cofre.
         */
        if (is_array($properties['vestige'] ?? null)) {
            $properties['vestige'] = $this->normalizeVestigeForStorage(
                $properties['vestige']
            );
        }

        $rawWeapon = $request->input('properties.weapon');



        if (!is_array($rawWeapon)) {

            $rawWeapon = [];

        }



        /*

         * FormData envia booleanos como "0" / "1". Em PHP essas strings

         * não devem ser persistidas como se fossem o tipo lógico da regra.

         * Canonicalizamos traits/masteries antes de salvar.

         */

        $normalizeRuleEntries = static function (mixed $entries): array {

            if (!is_array($entries)) {

                return [];

            }



            return collect($entries)

                ->filter(fn ($entry) => is_array($entry))

                ->map(function (array $entry): array {

                    $entry['custom'] = filter_var(

                        $entry['custom'] ?? false,

                        FILTER_VALIDATE_BOOLEAN

                    );



                    return $entry;

                })

                ->values()

                ->all();

        };



        if (array_key_exists('traits', $rawWeapon)) {

            $weapon['traits'] =

                $normalizeRuleEntries(

                    $rawWeapon['traits']

                );

        }



        if (array_key_exists('masteries', $rawWeapon)) {

            $weapon['masteries'] =

                $normalizeRuleEntries(

                    $rawWeapon['masteries']

                );

        }



        $traits = is_array($rawWeapon['traits'] ?? null)

            ? $rawWeapon['traits']

            : (

                is_array($weapon['traits'] ?? null)

                    ? $weapon['traits']

                    : []

            );



        $traitKeys = collect($traits)

            ->filter(fn ($trait) => is_array($trait))

            ->map(fn ($trait) => (string) ($trait['key'] ?? ''))

            ->filter()

            ->values()

            ->all();



        $hasRange = in_array('range', $traitKeys, true);

        $hasReach = in_array('reach', $traitKeys, true);



        if ($hasRange) {

            $normal = $rawWeapon['range_normal_ft'] ?? null;

            $long = $rawWeapon['range_long_ft'] ?? null;



            $legacyRange = is_array($rawWeapon['range'] ?? null)

                ? $rawWeapon['range']

                : [];



            if ($normal === null || $normal === '') {

                $normal = $legacyRange['normal'] ?? null;



                if (

                    $normal !== null

                    && $normal !== ''

                    && ($legacyRange['unit'] ?? 'ft') === 'm'

                ) {

                    $normal = round((float) $normal * 3.28084);

                }

            }



            if ($long === null || $long === '') {

                $long = $legacyRange['max'] ?? null;



                if (

                    $long !== null

                    && $long !== ''

                    && ($legacyRange['unit'] ?? 'ft') === 'm'

                ) {

                    $long = round((float) $long * 3.28084);

                }

            }



            $weapon['range_normal_ft'] =

                $this->nullableNonNegativeInteger($normal);



            $weapon['range_long_ft'] =

                $this->nullableNonNegativeInteger($long);

        } else {

            unset(

                $weapon['range_normal_ft'],

                $weapon['range_long_ft']

            );

        }



        if ($hasReach) {

            $reach = $rawWeapon['reach_ft'] ?? null;



            $legacyReach = is_array($rawWeapon['reach'] ?? null)

                ? $rawWeapon['reach']

                : [];



            if ($reach === null || $reach === '') {

                $reach = $legacyReach['distance'] ?? 10;



                if (

                    $reach !== null

                    && $reach !== ''

                    && ($legacyReach['unit'] ?? 'ft') === 'm'

                ) {

                    $reach = round((float) $reach * 3.28084);

                }

            }



            $weapon['reach_ft'] =

                $this->nullableNonNegativeInteger($reach)

                ?? 10;

        } else {

            unset($weapon['reach_ft']);

        }



        unset(

            $weapon['range'],

            $weapon['reach']

        );



        if ($weapon !== []) {

            $properties['weapon'] = $weapon;

        }



        $data['properties'] = $properties;



        return $data;

    }



    private function nullableNonNegativeInteger(mixed $value): ?int

    {

        if ($value === null || $value === '') {

            return null;

        }



        if (!is_numeric($value)) {

            return null;

        }



        return max(0, (int) round((float) $value));

    }



    /**
     * Persiste as três gavetas do Vestígio sem impor limite artificial de
     * habilidades e sem escolher uma etapa global para os personagens.
     *
     * current_stage é mantido SOMENTE por compatibilidade com itens antigos.
     */
    private function normalizeVestigeForStorage(array $source): array
    {
        $validStages = ['dormant', 'awakened', 'exalted'];
        $currentStage = $source['current_stage'] ?? 'dormant';

        if (!in_array($currentStage, $validStages, true)) {
            $currentStage = 'dormant';
        }

        $stages = [];

        foreach ($validStages as $stage) {
            $rawStage = is_array($source['stages'][$stage] ?? null)
                ? $source['stages'][$stage]
                : [];

            $features = [];
            $rawFeatures = is_array($rawStage['features'] ?? null)
                ? $rawStage['features']
                : [];

            foreach ($rawFeatures as $feature) {
                if (!is_array($feature)) {
                    continue;
                }

                $title = trim((string) ($feature['title'] ?? ''));
                $description = trim((string) ($feature['description'] ?? ''));

                if ($title === '' && $description === '') {
                    continue;
                }

                $usage = is_array($feature['usage'] ?? null)
                    ? $feature['usage']
                    : [];
                $usageEnabled = filter_var(
                    $usage['enabled'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                if (!$usageEnabled) {
                    $usage = ['enabled' => false];
                } else {
                    $max = max(1, (int) ($usage['max'] ?? 1));
                    $mode = in_array($usage['counter_mode'] ?? null, ['spend', 'build'], true)
                        ? $usage['counter_mode']
                        : 'spend';
                    $current = isset($usage['current'])
                        ? (int) $usage['current']
                        : ($mode === 'build' ? 0 : $max);
                    $recovery = (string) ($usage['recovery'] ?? 'day');

                    if (!in_array($recovery, [
                        'day', 'short_rest', 'long_rest', 'dawn', 'single_use', 'custom',
                    ], true)) {
                        $recovery = 'day';
                    }

                    $usage = [
                        'enabled' => true,
                        'counter_mode' => $mode,
                        'current' => max(0, min($max, $current)),
                        'max' => $max,
                        'recovery' => $recovery,
                        'recovery_custom' => trim((string) ($usage['recovery_custom'] ?? '')),
                    ];
                }

                $features[] = [
                    'title' => $title,
                    'description' => $description,
                    'usage' => $usage,
                ];
            }

            $stages[$stage] = [
                'lore' => trim((string) ($rawStage['lore'] ?? '')),
                'features' => $features,
            ];
        }

        return [
            'enabled' => filter_var($source['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'current_stage' => $currentStage,
            'stages' => $stages,
        ];
    }

    private function deriveLegacyFields(array $data): array

    {

        $properties = is_array($data['properties'] ?? null)

            ? $data['properties']

            : [];



        $nature = $properties['nature']

            ?? $data['type']

            ?? 'mundane';



        if (!in_array($nature, ['wonderful', 'mundane', 'technological'], true)) {

            $nature = 'mundane';

        }



        $properties['nature'] = $nature;

        $properties['inventory'] = is_array($properties['inventory'] ?? null)

            ? $properties['inventory']

            : [];



        $properties['inventory']['equippable'] =

            (bool) ($properties['inventory']['equippable'] ?? false);



        if (

            is_array($properties['armor'] ?? null)

            || is_array($properties['shield'] ?? null)

        ) {

            $properties['inventory']['equippable'] = true;

        }



        $isMagical = $nature === 'wonderful';



        $data['type'] = $nature;

        $data['properties'] = $properties;

        $data['is_magical'] = $isMagical;

        $data['requires_attunement'] =

            $isMagical && (bool) ($data['requires_attunement'] ?? false);



        // Toda natureza (Maravilhoso, Tecnológico e Mundano) possui raridade.
        // A natureza define se é mágico; a raridade não implica magia.
        $data['rarity'] = in_array(
            $data['rarity'] ?? 'common',
            ['common', 'uncommon', 'rare', 'very_rare', 'legendary', 'artifact'],
            true
        ) ? ($data['rarity'] ?? 'common') : 'common';



        $data['armor_class'] = null;

        $data['damage'] = null;

        $data['attack_bonus'] = null;

        $data['damage_bonus'] = null;



        $weapon = is_array($properties['weapon'] ?? null)

            ? $properties['weapon']

            : null;



        if ($weapon) {

            $magicBonus = (int) ($weapon['magic_bonus'] ?? 0);

            $damage = is_array($weapon['damage'] ?? null)

                ? $weapon['damage']

                : [];



            $count = max(1, (int) ($damage['count'] ?? 1));

            $die = trim((string) ($damage['die'] ?? ''));



            $data['damage'] = $die !== ''

                ? $count . $die

                : null;



            $data['attack_bonus'] = $magicBonus !== 0

                ? $magicBonus

                : null;



            $data['damage_bonus'] = $magicBonus !== 0

                ? $magicBonus

                : null;

        }



        $armor = is_array($properties['armor'] ?? null)

            ? $properties['armor']

            : null;



        if ($armor) {

            $baseAc = $armor['base_ac'] ?? null;

            $magicBonus = (int) ($armor['magic_bonus'] ?? 0);



            $data['armor_class'] = $baseAc !== null

                ? (int) $baseAc + $magicBonus

                : null;

        }



        return $data;

    }



    private function authorizeItem(MasterVaultItem $item): void

    {

        abort_unless(

            (int) $item->user_id === (int) Auth::id(),

            404

        );

    }



    private function folderSystemReady(): bool
    {
        return Schema::hasTable('workspace_folders')
            && Schema::hasTable('workspace_folder_items');
    }

    private function folderIdForItem(int $itemId): ?int
    {
        if (!$this->folderSystemReady()) {
            return null;
        }

        $folderId = WorkspaceFolderItem::query()
            ->where('folderable_type', MasterVaultItem::class)
            ->where('folderable_id', $itemId)
            ->value('folder_id');

        return $folderId ? (int) $folderId : null;
    }

    private function folderPayload(WorkspaceFolder $folder): array
    {
        return [
            'id' => (int) $folder->id,
            'name' => (string) $folder->name,
            'description' => $folder->description,
            'color' => $folder->color ?: '#8A4B20',
            'text_color' => $folder->text_color,
            'icon' => $folder->icon,
            'position' => (int) $folder->position,
            'items_count' => (int) (
                $folder->placements_count
                ?? $folder->placements()->count()
            ),
        ];
    }

    private function payload(MasterVaultItem $item, ?int $folderId = null): array

    {

        return [

            'id' => (int) $item->id,

            'name' => $item->name,

            'type' => $item->type,

            'description' => $item->description,

            'image_path' => $item->image_path,

            'image_url' => $item->image_path

                ? route(

                    'master.vault.items.image',

                    [

                        'vaultItem' => $item,

                        'v' => substr(sha1($item->image_path), 0, 12),

                    ],

                    false

                )

                : null,

            'default_quantity' => (int) $item->default_quantity,

            'weight' => $item->weight,

            'is_magical' => (bool) $item->is_magical,

            'rarity' => $item->rarity,

            'requires_attunement' => (bool) $item->requires_attunement,

            'is_cursed' => (bool) $item->is_cursed,

            'curse_name' => $item->curse_name,

            'curse_description' => $item->curse_description,

            'armor_class' => $item->armor_class,

            'damage' => $item->damage,

            'attack_bonus' => $item->attack_bonus,

            'damage_bonus' => $item->damage_bonus,

            'ability_bonuses' => $item->ability_bonuses ?? [],

            'properties' => $item->properties ?? [],

            'modifiers' => $item->modifiers ?? [],

            'notes' => $item->notes,

            'tags' => $item->tags ?? [],

            'favorite' => (bool) $item->favorite,

            'folder_id' => $folderId,

            'created_at' => $item->created_at?->toISOString(),

            'updated_at' => $item->updated_at?->toISOString(),

        ];

    }

}