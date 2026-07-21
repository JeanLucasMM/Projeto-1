<?php

namespace App\Repositories\Contracts;

use App\Models\Combat;
use Illuminate\Database\Eloquent\Collection;

interface CombatRepositoryInterface
{
    public function create(array $data): Combat;

    public function findById(int $id): ?Combat;

    public function findByUser(int $userId): Collection;

    public function delete(Combat $combat): bool;

    public function save(Combat $combat): bool;
}