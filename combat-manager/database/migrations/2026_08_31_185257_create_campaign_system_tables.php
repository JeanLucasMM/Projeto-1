<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CAMPANHAS
        |--------------------------------------------------------------------------
        |
        | owner_user_id é o Mestre/proprietário da campanha.
        |
        */

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name', 120);

            $table->text('description')
                ->nullable();

            $table->timestamps();

            $table->index([
                'owner_user_id',
                'created_at',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | CONVITES
        |--------------------------------------------------------------------------
        |
        | Nesta primeira versão o convite é para um usuário já cadastrado.
        | Guardamos também o e-mail como snapshot e para facilitar evolução
        | futura para convites externos.
        |
        */

        Schema::create('campaign_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->foreignId('invited_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('invited_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('email');

            $table->string('token', 64)
                ->unique();

            $table->string('status', 20)
                ->default('pending');

            $table->timestamp('expires_at')
                ->nullable();

            $table->timestamp('responded_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'campaign_id',
                'status',
            ]);

            $table->index([
                'invited_user_id',
                'status',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | MEMBROS
        |--------------------------------------------------------------------------
        |
        | O proprietário não precisa de uma entrada aqui.
        | Esta tabela representa participantes aceitos.
        |
        */

        Schema::create('campaign_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('role', 30)
                ->default('player');

            $table->timestamp('joined_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'campaign_id',
                'user_id',
            ]);

            $table->index([
                'user_id',
                'role',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | PERSONAGENS DA CAMPANHA
        |--------------------------------------------------------------------------
        |
        | Aceitar o convite NÃO compartilha todas as fichas.
        | Somente personagens explicitamente vinculados aparecem ao Mestre.
        |
        */

        Schema::create('campaign_characters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->foreignId('character_id')
                ->constrained('characters')
                ->cascadeOnDelete();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'campaign_id',
                'character_id',
            ]);

            $table->index([
                'campaign_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_characters');
        Schema::dropIfExists('campaign_members');
        Schema::dropIfExists('campaign_invitations');
        Schema::dropIfExists('campaigns');
    }
};