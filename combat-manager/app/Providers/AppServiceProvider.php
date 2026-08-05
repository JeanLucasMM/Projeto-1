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
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
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
        
    }

    


public function boot(): void
{
    Blade::anonymousComponentPath(
        resource_path('views/npc-builder/components'),
        'builder'
    );
}
}
