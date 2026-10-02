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
        | Workspace Folders
        |--------------------------------------------------------------------------
        |
        | Sistema genérico de pastas do Spellbound.
        |
        | scope_type + scope_id definem "onde" a pasta existe:
        | - campaign.shops      + campaign_id
        | - character.inventory + character_id
        | - master.vault       + user_id
        |
        | A tabela é deliberadamente genérica para novos módulos poderem
        | reutilizá-la sem novas migrations de estrutura.
        |
        */
        Schema::create('workspace_folders', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('scope_type', 80);
            $table->unsignedBigInteger('scope_id');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('workspace_folders')
                ->nullOnDelete();

            $table->string('name', 120);
            $table->text('description')->nullable();

            /*
            | Texto livre maior para futuras áreas:
            | notas, lore, instruções, observações etc.
            */
            $table->longText('content')->nullable();

            $table->string('color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->string('icon', 80)->nullable();
            $table->string('image_path', 2048)->nullable();

            $table->unsignedInteger('position')->default(0);

            /*
            | Espaço genérico para preferências futuras sem migration:
            | layout, capa, flags, visualização, customização adicional etc.
            */
            $table->json('metadata')->nullable();

            $table->boolean('is_system')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->index(
                ['owner_user_id', 'scope_type', 'scope_id'],
                'workspace_folders_owner_scope_index'
            );

            $table->index(
                ['scope_type', 'scope_id', 'position'],
                'workspace_folders_scope_position_index'
            );

            $table->index(
                ['parent_id', 'position'],
                'workspace_folders_parent_position_index'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Conteúdo das pastas
        |--------------------------------------------------------------------------
        |
        | Relação polimórfica. Uma pasta pode conter Loja, CharacterItem,
        | MasterVaultItem ou qualquer outro Model liberado pelo registry.
        |
        | Não é necessário adicionar folder_id nas tabelas de cada recurso.
        |
        */
        Schema::create('workspace_folder_items', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('folder_id')
                ->constrained('workspace_folders')
                ->cascadeOnDelete();

            $table->string('folderable_type', 191);
            $table->unsignedBigInteger('folderable_id');

            $table->unsignedInteger('position')->default(0);
            $table->json('metadata')->nullable();

            $table->timestamps();

            /*
            | Um recurso pertence a uma pasta por vez.
            | "Sem pasta" é representado pela ausência desta linha.
            */
            $table->unique(
                ['folderable_type', 'folderable_id'],
                'workspace_folder_items_resource_unique'
            );

            $table->index(
                ['folder_id', 'position'],
                'workspace_folder_items_folder_position_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_folder_items');
        Schema::dropIfExists('workspace_folders');
    }
};
