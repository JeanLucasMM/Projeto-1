<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->decimal('image_zoom', 4, 2)->default(1.00);
            $table->string('image_position', 12)->default('auto');
        });
    }

    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn(['image_zoom', 'image_position']);
        });
    }
};
