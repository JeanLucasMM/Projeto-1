<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Ficha mecânica de transformação
            |--------------------------------------------------------------------------
            |
            | Uma transformação do tipo "Nova Ficha" reutiliza a estrutura de
            | Character para armazenar apenas a camada mecânica alternativa.
            |
            | Este marcador permite que essas fichas auxiliares sejam excluídas
            | do Index, campanhas e outros seletores de personagens normais.
            |
            */
            $table->boolean('is_transformation_form')
                ->default(false)
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('is_transformation_form');
        });
    }
};