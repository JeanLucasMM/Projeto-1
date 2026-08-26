<?php

namespace App\Providers;

use App\Services\Characters\CharacterRestService;
use App\Services\Characters\Rest\CombatRestHandler;
use Illuminate\Support\ServiceProvider;

class CharacterRestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            CharacterRestService::class,
            function ($app) {
                $service =
                    new CharacterRestService();

                /*
                |--------------------------------------------------------------------------
                | COMBATE
                |--------------------------------------------------------------------------
                */
                $service->register(
                    $app->make(
                        CombatRestHandler::class
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | OUTROS MÓDULOS
                |--------------------------------------------------------------------------
                |
                | Depois simplesmente teremos:
                |
                | $service->register(
                |     $app->make(
                |         CharacterResourceRestHandler::class
                |     )
                | );
                |
                | $service->register(
                |     $app->make(
                |         SpellSlotRestHandler::class
                |     )
                | );
                |
                */

                return $service;
            }
        );
    }

    public function boot(): void
    {
        //
    }
}