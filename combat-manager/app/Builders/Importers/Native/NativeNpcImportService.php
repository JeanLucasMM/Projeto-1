<?php

namespace App\Builders\Importers\Native;


use App\Models\User;
use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;
use App\Services\Calculators\StatisticCalculator;


class NativeNpcImportService
{

    public function __construct(
        private NpcRepositoryInterface $repository,
        private StatisticCalculator $calculator
    ) {}



    public function import(
        User $user,
        array $json
    ): Npc
    {

        NativeNpcValidator::validate($json);


        $data = NativeNpcMapper::map($json);



        return $this->repository->create([

            'user_id' => $user->id,


            ...$data,


            'max_hp' =>
                $this->calculateHp($json),

        ]);

    }



    private function calculateHp(
        array $json
    ): int
    {

        $combat = $json['combat'] ?? [];


        if (
            ($combat['hp_mode'] ?? null)
            === 'custom'
        ) {

            return
                $combat['custom_hp']
                ?? 1;

        }


        /*
         * Depois podemos ligar isso ao
         * StatisticCalculator caso necessário.
         */

        return 1;

    }

}