<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_import_runs', function (Blueprint $table) {
            $table->id();

            $table->string('provider', 30)
                ->default('5etools');

            $table->string('status', 30)
                ->default('running');

            $table->text('index_url')
                ->nullable();

            $table->unsignedInteger('source_count')
                ->default(0);

            $table->unsignedInteger('files_processed')
                ->default(0);

            $table->unsignedInteger('spells_seen')
                ->default(0);

            $table->unsignedInteger('created_count')
                ->default(0);

            $table->unsignedInteger('updated_count')
                ->default(0);

            $table->unsignedInteger('unchanged_count')
                ->default(0);

            $table->unsignedInteger('missing_count')
                ->default(0);

            $table->unsignedInteger('error_count')
                ->default(0);

            $table->json('manifest')
                ->nullable();

            $table->json('errors')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('finished_at')
                ->nullable();

            $table->timestamps();

            $table->index(['provider', 'status']);
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_import_runs');
    }
};
