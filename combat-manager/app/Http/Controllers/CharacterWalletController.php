<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CharacterWalletController extends Controller
{
    public function update(
        Request $request,
        Character $character
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | AutorizaÃ§ÃĢo
        |--------------------------------------------------------------------------
        |
        | A carteira altera estado persistente da ficha.
        | CharacterPolicy::update permanece exclusiva do dono.
        |
        */

        Gate::authorize(
            'update',
            $character
        );

        $data = $request->validate([
            'copper' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999999999',
            ],

            'silver' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999999999',
            ],

            'electrum' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999999999',
            ],

            'gold' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999999999',
            ],

            'platinum' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999999999',
            ],
        ]);

        $wallet = CharacterWallet::query()
            ->firstOrCreate(
                [
                    'character_id' =>
                        $character->id,
                ],
                [
                    'copper' => 0,
                    'silver' => 0,
                    'electrum' => 0,
                    'gold' => 0,
                    'platinum' => 0,
                ]
            );

        if ($data !== []) {
            $wallet->update(
                $data
            );
        }

        $wallet->refresh();

        return response()->json([
            'success' => true,

            'message' =>
                'Moedas atualizadas.',

            'wallet' => [
                'copper' =>
                    (int) $wallet->copper,

                'silver' =>
                    (int) $wallet->silver,

                'electrum' =>
                    (int) $wallet->electrum,

                'gold' =>
                    (int) $wallet->gold,

                'platinum' =>
                    (int) $wallet->platinum,
            ],
        ]);
    }
}