<?php

namespace App\Services\Folders;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\LootDrop;
use App\Models\MasterVaultItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FolderContextRegistry
{
    public const CAMPAIGN_SHOPS = 'campaign.shops';
    public const CAMPAIGN_LOOT = 'campaign.loot';
    public const CHARACTER_INVENTORY = 'character.inventory';
    public const MASTER_VAULT = 'master.vault';

    public function authorizeScope(string $scopeType, int $scopeId): void
    {
        $userId = (int) Auth::id();
        abort_if($userId <= 0, 401);

        if (in_array($scopeType, [self::CAMPAIGN_SHOPS, self::CAMPAIGN_LOOT], true)) {
            $campaign = Campaign::query()->findOrFail($scopeId);
            abort_unless($campaign->isOwner($userId), 403);
            return;
        }

        if ($scopeType === self::CHARACTER_INVENTORY) {
            $character = Character::query()->findOrFail($scopeId);
            abort_unless((int) $character->user_id === $userId, 403);
            return;
        }

        if ($scopeType === self::MASTER_VAULT) {
            abort_unless($scopeId === $userId, 403);
            return;
        }

        throw new HttpException(422, 'Este contexto ainda não foi registrado no sistema de pastas.');
    }

    public function morphClassForItemType(string $itemType): string
    {
        return match ($itemType) {
            'shop', 'loot' => LootDrop::class,
            'character_item' => CharacterItem::class,
            'vault_item' => MasterVaultItem::class,
            default => throw new HttpException(422, 'Este tipo de conteúdo não pode ser organizado em pastas.'),
        };
    }

    public function resolveItem(
        string $scopeType,
        int $scopeId,
        string $itemType,
        int $itemId
    ): Model {
        $this->authorizeScope($scopeType, $scopeId);

        if ($scopeType === self::CAMPAIGN_SHOPS && $itemType === 'shop') {
            return LootDrop::query()
                ->whereKey($itemId)
                ->where('campaign_id', $scopeId)
                ->where('is_shop', true)
                ->firstOrFail();
        }

        if ($scopeType === self::CAMPAIGN_LOOT && $itemType === 'loot') {
            return LootDrop::query()
                ->whereKey($itemId)
                ->where('campaign_id', $scopeId)
                ->where('is_shop', false)
                ->where('is_quick_access', false)
                ->firstOrFail();
        }

        if ($scopeType === self::CHARACTER_INVENTORY && $itemType === 'character_item') {
            return CharacterItem::query()
                ->whereKey($itemId)
                ->where('character_id', $scopeId)
                ->firstOrFail();
        }

        if ($scopeType === self::MASTER_VAULT && $itemType === 'vault_item') {
            return MasterVaultItem::query()
                ->whereKey($itemId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        throw new HttpException(422, 'Este conteúdo não pertence ao contexto informado.');
    }
}
