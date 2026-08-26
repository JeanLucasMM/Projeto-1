<?php

namespace App\Repositories\Contracts\Characters;

use App\Enums\RestType;
use App\Models\Character;

interface RestHandler
{
    public function handle(
        Character $character,
        RestType $restType
    ): void;
}