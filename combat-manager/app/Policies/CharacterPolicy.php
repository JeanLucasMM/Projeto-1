<?php

namespace App\Policies;

use App\Models\Character;
use App\Models\User;

class CharacterPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Visualizar
    |--------------------------------------------------------------------------
    |
    | 1. O dono sempre pode visualizar.
    | 2. Um Mestre pode visualizar SOMENTE quando a ficha foi explicitamente
    |    vinculada a uma campanha que pertence a ele.
    |
    */

    public function view(
        User $user,
        Character $character
    ): bool {
        if (
            (int) $character->user_id
            === (int) $user->id
        ) {
            return true;
        }

        return $character
            ->campaigns()
            ->where(
                'campaigns.owner_user_id',
                $user->id
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Alterar
    |--------------------------------------------------------------------------
    |
    | Compartilhar uma ficha com a campanha concede ao Mestre somente
    | visualização. Alterações normais da ficha continuam exclusivas do dono.
    |
    */

    public function update(
        User $user,
        Character $character
    ): bool {
        return (int) $character->user_id
            === (int) $user->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Excluir
    |--------------------------------------------------------------------------
    |
    | Compartilhar a ficha nunca concede ao Mestre permissão de apagá-la.
    |
    */

    public function delete(
        User $user,
        Character $character
    ): bool {
        return (int) $character->user_id
            === (int) $user->id;
    }
}