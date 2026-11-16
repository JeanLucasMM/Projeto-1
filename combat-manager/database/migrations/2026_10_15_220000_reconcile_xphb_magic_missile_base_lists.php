<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Correção pontual da proveniência da lista-base de Magic Missile (XPHB).
 * NÃO apaga subclasses, variantes, outras magias nem entradas de outras fontes.
 * Execute após fazer backup do banco de dados.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('spells') || !Schema::hasTable('spell_class_lists')) {
            return;
        }

        DB::transaction(function (): void {
            $spells = DB::table('spells')
                ->whereRaw('LOWER(source) = ?', ['xphb'])
                ->whereRaw('LOWER(name) = ?', ['magic missile'])
                ->get(['id', 'provider']);

            foreach ($spells as $spell) {
                $spellId = (int) $spell->id;

                // Somente vinculações BASE incorretas. Subclasses e variantes ficam intactas.
                DB::table('spell_class_lists')
                    ->where('spell_id', $spellId)
                    ->where('list_type', 'class')
                    ->whereNotIn('class_code', ['sorcerer', 'wizard'])
                    ->delete();

                foreach (['sorcerer' => 'Sorcerer', 'wizard' => 'Wizard'] as $code => $name) {
                    $alreadyExists = DB::table('spell_class_lists')
                        ->where('spell_id', $spellId)
                        ->where('list_type', 'class')
                        ->where('class_code', $code)
                        ->exists();
                    if ($alreadyExists) {
                        continue;
                    }

                    $normalizedKey = hash('sha256', implode('|', [
                        $spellId, $code, 'XPHB', '', '', 'class',
                    ]));
                    DB::table('spell_class_lists')->insertOrIgnore([
                        'spell_id' => $spellId,
                        'provider' => (string) $spell->provider,
                        'class_code' => $code,
                        'class_name' => $name,
                        'class_source' => 'XPHB',
                        'subclass_name' => null,
                        'subclass_source' => null,
                        'list_type' => 'class',
                        'normalized_key' => $normalizedKey,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Migração de saneamento de dados: não recria vínculos incorretos.
    }
};
