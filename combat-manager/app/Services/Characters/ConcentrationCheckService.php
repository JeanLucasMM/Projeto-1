<?php

namespace App\Services\Characters;

class ConcentrationCheckService
{
    /*
    |--------------------------------------------------------------------------
    | TESTE DE CONCENTRAÇÃO POR DANO
    |--------------------------------------------------------------------------
    |
    | Regra-base:
    |
    | CD = maior entre 10 e metade do dano recebido.
    |
    | O serviço não decide o que acontece em uma falha. Ele apenas produz um
    | payload estável para qualquer sistema que dependa de concentração:
    |
    | - Transformações;
    | - futuros Feitiços;
    | - habilidades;
    | - outros efeitos sustentados.
    |--------------------------------------------------------------------------
    */

    public function forDamage(
        int $damage,
        int $constitutionSaveModifier,
        int $exhaustionPenalty = 0,
        array $context = []
    ): array {
        $damage =
            max(
                1,
                $damage
            );

        $exhaustionPenalty =
            max(
                0,
                $exhaustionPenalty
            );

        $effectiveModifier =
            $constitutionSaveModifier
            - $exhaustionPenalty;

        $difficultyClass =
            max(
                10,
                intdiv(
                    $damage,
                    2
                )
            );

        $expression =
            match (true) {
                $effectiveModifier > 0 =>
                    '1d20+'
                    . $effectiveModifier,

                $effectiveModifier < 0 =>
                    '1d20'
                    . $effectiveModifier,

                default =>
                    '1d20',
            };

        return [
            'required' =>
                true,

            'reason' =>
                'damage',

            'label' =>
                'Salvamento de Constituição',

            'dc' =>
                $difficultyClass,

            'damage' =>
                $damage,

            'base_modifier' =>
                $constitutionSaveModifier,

            'exhaustion_penalty' =>
                $exhaustionPenalty,

            'modifier' =>
                $effectiveModifier,

            'expression' =>
                $expression,

            'context' =>
                $context,
        ];
    }
}