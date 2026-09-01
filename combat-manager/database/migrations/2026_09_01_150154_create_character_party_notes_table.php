<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'character_party_notes',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId(
                    'campaign_id'
                )
                    ->constrained(
                        'campaigns'
                    )
                    ->cascadeOnDelete();

                $table->foreignId(
                    'character_id'
                )
                    ->constrained(
                        'characters'
                    )
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Diário
                |--------------------------------------------------------------------------
                |
                | Pode armazenar o JSON versionado das páginas do Diário.
                |
                */

                $table->longText(
                    'notes'
                )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Cutucadas da Party
                |--------------------------------------------------------------------------
                |
                | Armazena uma fila JSON de interações recebidas/enviadas.
                |
                | Exemplo:
                |
                | [
                |     {
                |         "id": "uuid",
                |         "from_character_id": 10,
                |         "emoji": "👉",
                |         "created_at": "2026-09-01T12:00:00-03:00",
                |         "seen": false
                |     }
                | ]
                |
                */

                $table->json(
                    'pokes'
                )
                    ->nullable();

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Uma anotação/estado da Party por Character em cada Campaign
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'campaign_id',
                        'character_id',
                    ],
                    'character_party_notes_campaign_character_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'character_party_notes'
        );
    }
};