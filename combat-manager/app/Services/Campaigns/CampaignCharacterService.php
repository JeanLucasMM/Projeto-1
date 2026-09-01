<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\CampaignCharacter;
use App\Models\Character;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CampaignCharacterService
{
    public function attach(
        Campaign $campaign,
        Character $character,
        User $user
    ): CampaignCharacter {
        /*
        |--------------------------------------------------------------------------
        | Autorização
        |--------------------------------------------------------------------------
        |
        | Cada usuário compartilha somente as próprias fichas.
        | O usuário precisa participar da campanha ou ser o Mestre dela.
        |
        */

        abort_unless(
            (int) $character->user_id
                === (int) $user->id,
            403
        );

        abort_unless(
            $campaign->isOwner($user)
                || $campaign->hasMember($user),
            403
        );

        return DB::transaction(
            function () use (
                $campaign,
                $character,
                $user
            ): CampaignCharacter {
                /*
                | Travamos a campanha para serializar mudanças de personagem
                | ativo. Assim duas requisições simultâneas não deixam dois
                | personagens do mesmo Player em jogo.
                */

                Campaign::query()
                    ->whereKey($campaign->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $existingLink =
                    CampaignCharacter::query()
                        ->where(
                            'campaign_id',
                            $campaign->id
                        )
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->lockForUpdate()
                        ->first();

                if ($existingLink) {
                    return $existingLink;
                }

                /*
                | O primeiro personagem compartilhado entra em jogo.
                | Personagens adicionais entram descansando.
                */

                $hasActiveCharacter =
                    CampaignCharacter::query()
                        ->where(
                            'campaign_id',
                            $campaign->id
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->whereHas(
                            'character',
                            function ($query) use ($user) {
                                $query->where(
                                    'user_id',
                                    $user->id
                                );
                            }
                        )
                        ->exists();

                return CampaignCharacter::query()
                    ->create([
                        'campaign_id' =>
                            $campaign->id,

                        'character_id' =>
                            $character->id,

                        'is_active' =>
                            !$hasActiveCharacter,
                    ]);
            }
        );
    }


    public function detach(
        Campaign $campaign,
        Character $character,
        User $user
    ): void {
        DB::transaction(
            function () use (
                $campaign,
                $character,
                $user
            ): void {
                Campaign::query()
                    ->whereKey($campaign->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $link =
                    CampaignCharacter::query()
                        ->where(
                            'campaign_id',
                            $campaign->id
                        )
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->lockForUpdate()
                        ->first();

                if (!$link) {
                    return;
                }

                /*
                | O dono da ficha pode retirar o próprio personagem.
                | O Mestre continua podendo remover fichas da campanha.
                */

                $canDetach =
                    (int) $character->user_id
                        === (int) $user->id
                    ||
                    $campaign->isOwner($user);

                abort_unless(
                    $canDetach,
                    403
                );

                $link->delete();
            }
        );
    }


    public function setActive(
        Campaign $campaign,
        Character $character,
        User $user,
        bool $active
    ): CampaignCharacter {
        /*
        |--------------------------------------------------------------------------
        | Quem escolhe o personagem em jogo é o Player
        |--------------------------------------------------------------------------
        |
        | O Mestre consegue visualizar e remover fichas, mas não escolhe qual
        | personagem outro usuário está interpretando naquele momento.
        |
        */

        abort_unless(
            (int) $character->user_id
                === (int) $user->id,
            403
        );

        abort_unless(
            $campaign->isOwner($user)
                || $campaign->hasMember($user),
            403
        );

        return DB::transaction(
            function () use (
                $campaign,
                $character,
                $user,
                $active
            ): CampaignCharacter {
                Campaign::query()
                    ->whereKey($campaign->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $links =
                    CampaignCharacter::query()
                        ->where(
                            'campaign_id',
                            $campaign->id
                        )
                        ->whereHas(
                            'character',
                            function ($query) use ($user) {
                                $query->where(
                                    'user_id',
                                    $user->id
                                );
                            }
                        )
                        ->lockForUpdate()
                        ->get();

                $link =
                    $links->first(
                        fn (CampaignCharacter $current) =>
                            (int) $current->character_id
                            ===
                            (int) $character->id
                    );

                if (!$link) {
                    throw ValidationException::withMessages([
                        'character' =>
                            'Esse personagem não pertence à campanha.',
                    ]);
                }

                /*
                | Ao entrar em jogo, todos os outros personagens do mesmo
                | usuário passam automaticamente para Descansando.
                */

                if ($active) {
                    foreach ($links as $current) {
                        $shouldBeActive =
                            (int) $current->id
                            ===
                            (int) $link->id;

                        if (
                            (bool) $current->is_active
                            !==
                            $shouldBeActive
                        ) {
                            $current->update([
                                'is_active' =>
                                    $shouldBeActive,
                            ]);
                        }
                    }
                } else {
                    $link->update([
                        'is_active' =>
                            false,
                    ]);
                }

                return $link->fresh();
            }
        );
    }
}