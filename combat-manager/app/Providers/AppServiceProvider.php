<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\NpcRepositoryInterface;
use App\Repositories\Eloquent\NpcRepository;
use App\Repositories\Contracts\CombatRepositoryInterface;
use App\Repositories\Eloquent\CombatRepository;
use App\Repositories\Contracts\CombatNpcRepositoryInterface;
use App\Repositories\Eloquent\CombatNpcRepository;
use App\Repositories\Contracts\CombatPlayerRepositoryInterface;
use App\Repositories\Eloquent\CombatPlayerRepository;
use App\Repositories\Contracts\FolderRepositoryInterface;
use App\Repositories\Eloquent\FolderRepository;
use App\Models\Campaign;
use App\Models\Character;
use App\Policies\CampaignPolicy;
use App\Policies\CharacterPolicy;
use App\Http\Controllers\CharacterItemController;
use App\Http\Controllers\CharacterItemPlayerCurseController;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            NpcRepositoryInterface::class,
            NpcRepository::class
        );

        $this->app->bind(
            CombatRepositoryInterface::class,
            CombatRepository::class
        );

        $this->app->bind(
            CombatNpcRepositoryInterface::class,
            CombatNpcRepository::class
        );

        $this->app->bind(
            CombatPlayerRepositoryInterface::class,
            CombatPlayerRepository::class
        );

        $this->app->bind(
            FolderRepositoryInterface::class,
            FolderRepository::class
        );

        // V66.2.100 — usa o controller de itens já existente como base.
        // Apenas store()/update() recebem a regra de maldições do Player.
        $this->app->bind(
            CharacterItemController::class,
            CharacterItemPlayerCurseController::class
        );
    }

    public function boot(): void
    {
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Character::class, CharacterPolicy::class);
    }
}
