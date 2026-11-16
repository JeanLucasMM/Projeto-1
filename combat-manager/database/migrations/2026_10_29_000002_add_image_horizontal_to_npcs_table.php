<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->unsignedTinyInteger('image_horizontal')->default(50);
        });
    }

    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn('image_horizontal');
        });
    }
};
