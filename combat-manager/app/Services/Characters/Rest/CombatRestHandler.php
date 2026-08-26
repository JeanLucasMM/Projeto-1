<?php

namespace App\Services\Characters\Rest;

use App\Repositories\Contracts\Characters\RestHandler;
use App\Enums\RestType;
use App\Models\Character;
use Illuminate\Support\Collection;

class CombatRestHandler implements RestHandler
{
    public function handle(
        Character $character,
        RestType $restType
    ): void {
        /*
        |--------------------------------------------------------------------------
        | DESCANSO CURTO
        |--------------------------------------------------------------------------
        |
        | O estado universal de combate não possui nada para restaurar
        | automaticamente em descanso curto.
        |
        | Recursos específicos entrarão nos seus próprios handlers.
        |
        */

        if ($restType->isShort()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DESCANSO LONGO
        |--------------------------------------------------------------------------
        */

        $combat = $character->combat;

        if (!$combat) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VIDA MÁXIMA
        |--------------------------------------------------------------------------
        |
        | Esta é a única vida permanente e NÃO deve ser alterada.
        |
        */

        $maxHp = max(
            1,
            (int) ($combat->max_hp ?? 1)
        );

        /*
        |--------------------------------------------------------------------------
        | HIT DICE
        |--------------------------------------------------------------------------
        |
        | Aceitamos:
        |
        | - array
        | - Collection
        | - JSON string
        |
        */

        $rawHitDice =
            $combat->hit_dice ?? [];

        if ($rawHitDice instanceof Collection) {
            $rawHitDice =
                $rawHitDice->all();
        }

        if (is_string($rawHitDice)) {
            $decoded =
                json_decode(
                    $rawHitDice,
                    true
                );

            $rawHitDice =
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (!is_array($rawHitDice)) {
            $rawHitDice = [];
        }

        /*
        |--------------------------------------------------------------------------
        | RECUPERA TODOS OS USOS
        |--------------------------------------------------------------------------
        */

        $hitDice = collect(
            $rawHitDice
        )
            ->filter(
                fn ($die) =>
                    is_array($die)
            )
            ->map(
                function ($die) {
                    $maximum = max(
                        0,
                        (int) (
                            $die['maximum']
                            ?? 0
                        )
                    );

                    return [
                        'die' => strtolower(
                            (string) (
                                $die['die']
                                ?? 'd8'
                            )
                        ),

                        'current' =>
                            $maximum,

                        'maximum' =>
                            $maximum,
                    ];
                }
            )
            ->filter(
                fn ($die) =>
                    $die['maximum'] > 0
            )
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | VERIFICA CAST DO MODEL
        |--------------------------------------------------------------------------
        |
        | Se CharacterCombat possuir:
        |
        | protected $casts = [
        |     'hit_dice' => 'array',
        | ];
        |
        | enviamos array.
        |
        | Caso contrário, armazenamos JSON.
        |
        */

        $casts =
            method_exists(
                $combat,
                'getCasts'
            )
                ? $combat->getCasts()
                : [];

        $hasHitDiceCast =
            array_key_exists(
                'hit_dice',
                $casts
            );

        $hitDiceForDatabase =
            $hasHitDiceCast
                ? $hitDice
                : json_encode(
                    $hitDice,
                    JSON_UNESCAPED_UNICODE
                );

        /*
        |--------------------------------------------------------------------------
        | DESCANSO LONGO
        |--------------------------------------------------------------------------
        |
        | max_hp:
        |     NÃO ALTERA.
        |
        | current_hp:
        |     recupera até max_hp.
        |
        | temporary_hp:
        |     desaparece.
        |
        | temporary_max_hp:
        |     desaparece.
        |
        | death saves:
        |     zerados.
        |
        | hit dice:
        |     todos recuperados.
        |
        */

        $combat->forceFill([
            'current_hp' =>
                $maxHp,

            'temporary_hp' =>
                0,

            'temporary_max_hp' =>
                0,

            'death_save_successes' =>
                0,

            'death_save_failures' =>
                0,

            'hit_dice' =>
                $hitDiceForDatabase,
        ]);

        $combat->save();

        /*
        |--------------------------------------------------------------------------
        | SINCRONIZA RELAÇÃO EM MEMÓRIA
        |--------------------------------------------------------------------------
        */

        $character->setRelation(
            'combat',
            $combat
        );
    }
}