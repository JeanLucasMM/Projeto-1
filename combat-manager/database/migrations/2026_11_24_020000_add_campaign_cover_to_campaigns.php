<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->string('cover_image_path')->nullable();
            $table->unsignedTinyInteger('cover_image_focus_y')->default(50);
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->dropColumn(['cover_image_path', 'cover_image_focus_y']);
        });
    }
};
