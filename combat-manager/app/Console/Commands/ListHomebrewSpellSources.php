<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListHomebrewSpellSources extends Command
{
    protected $signature = 'spells:homebrew-sources
                            {--search= : Filtra por código ou nome da fonte}';

    protected $description = 'Lista as fontes homebrew/third-party cadastradas e a quantidade de feitiços de cada uma.';

    public function handle(): int
    {
        $search = trim((string) $this->option('search'));

        $query = DB::table('homebrew_spell_sources')
            ->where('provider', '5etools-homebrew')
            ->orderBy('name')
            ->orderBy('source_code');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('source_code', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        $sources = $query->get([
            'id',
            'source_code',
            'name',
            'spell_count',
            'is_available',
        ]);

        if ($sources->isEmpty()) {
            $this->warn('Nenhuma fonte homebrew encontrada.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Código', 'Fonte', 'Feitiços', 'Disponível'],
            $sources->map(fn ($source) => [
                $source->id,
                $source->source_code,
                $source->name ?: $source->source_code,
                (int) $source->spell_count,
                $source->is_available ? 'sim' : 'não',
            ])->all()
        );

        $this->newLine();
        $this->line(
            'Total de fontes: '.$sources->count()
        );

        return self::SUCCESS;
    }
}
