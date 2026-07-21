<?php

namespace App\Services;

use App\Models\User;
use App\Models\Npc;
use App\Repositories\Contracts\NpcRepositoryInterface;
use App\Services\Calculators\StatisticCalculator;
use Illuminate\Http\UploadedFile;

class ImportNpcService
{
    public function __construct(
        private StatisticCalculator $calculator,
        private NpcRepositoryInterface $repository
    ) {}

    public function import(
        User $user,
        string $jsonContent,
        ?UploadedFile $image = null
        
    ): Npc
    {
        $json = json_decode($jsonContent, true);

        if (!$json) {
            throw new \Exception('JSON inválido.');
        }

        $imagePath = null;

        if ($image) {

            $imagePath = $image->store(
                'npcs',
                'public'
            );

        }

        return $this->repository->create([

            'user_id' => $user->id,

            'name' => $json['name'] ?? 'Sem nome',

            'nickname' => $json['nickname'] ?? null,

            'creature_type' => $json['type'] ?? null,

            'size' => $json['size'] ?? null,

            'alignment' => $json['alignment'] ?? null,

            'armor_class' => $json['AC'] ?? 10,

            'challenge_rating' => $json['CR'] ?? 0,

            'max_hp' => $this->calculator->hitPoints($json['HP']),

            'json_data' => $json,

            'image_path' => $imagePath,

        ]);
    }
}