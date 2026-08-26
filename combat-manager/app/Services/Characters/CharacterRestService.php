<?php

namespace App\Services\Characters;

use App\Repositories\Contracts\Characters\RestHandler;
use App\Enums\RestType;
use App\Models\Character;
use Illuminate\Support\Facades\DB;
use Throwable;

class CharacterRestService
{
    /**
     * @var array<int, RestHandler>
     */
    protected array $handlers = [];

    /**
     * Registra um módulo participante do descanso.
     */
    public function register(
        RestHandler $handler
    ): static {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Descanso curto.
     *
     * @throws Throwable
     */
    public function shortRest(
        Character $character
    ): Character {
        return $this->rest(
            $character,
            RestType::SHORT
        );
    }

    /**
     * Descanso longo.
     *
     * @throws Throwable
     */
    public function longRest(
        Character $character
    ): Character {
        return $this->rest(
            $character,
            RestType::LONG
        );
    }

    /**
     * Executa o descanso inteiro dentro de uma transação.
     *
     * @throws Throwable
     */
    public function rest(
        Character $character,
        RestType $restType
    ): Character {
        return DB::transaction(
            function () use (
                $character,
                $restType
            ) {
                /*
                |--------------------------------------------------------------------------
                | ESTADO ATUAL
                |--------------------------------------------------------------------------
                */

                $character->refresh();

                $character->loadMissing(
                    'combat'
                );

                /*
                |--------------------------------------------------------------------------
                | HANDLERS
                |--------------------------------------------------------------------------
                */

                foreach ($this->handlers as $handler) {
                    $handler->handle(
                        $character,
                        $restType
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | RECARREGA SOMENTE O QUE O DESCANSO PRECISA
                |--------------------------------------------------------------------------
                |
                | Não precisamos carregar abilities/classes aqui.
                |
                */

                $character->unsetRelation(
                    'combat'
                );

                $character->load(
                    'combat'
                );

                return $character;
            }
        );
    }
}