<?php

namespace App\Console\Commands;

use App\Services\Spells\FiveEToolsSpellImporter;
use Illuminate\Console\Command;
use Throwable;

class SyncFiveEToolsSpells extends Command
{
    protected $signature = 'spells:sync-5etools
                            {--source=* : Sincroniza apenas fontes específicas, por exemplo --source=PHB --source=XPHB}';

    protected $description = 'Importa e sincroniza o catálogo local de feitiços usando os dados do 5etools.';

    public function handle(FiveEToolsSpellImporter $importer): int
    {
        $sources = array_values(array_filter(
            array_map(
                fn ($source) => trim((string) $source),
                (array) $this->option('source')
            )
        ));

        $this->newLine();
        $this->info('Spellbound — sincronização de feitiços do 5etools');

        if ($sources !== []) {
            $this->line(
                'Fontes selecionadas: '.implode(', ', $sources)
            );
        } else {
            $this->line(
                'Modo: catálogo completo (todas as fontes presentes no index.json)'
            );
        }

        $this->newLine();

        try {
            $report = $importer->sync(
                $sources,
                function (string $event, array $data): void {
                    if ($event === 'source:start') {
                        $this->line(
                            sprintf(
                                '→ %-12s %s',
                                $data['source'],
                                $data['filename']
                            )
                        );
                    }

                    if ($event === 'source:done') {
                        $this->line(
                            sprintf(
                                '  ✓ %s feitiço(s)',
                                number_format(
                                    (int) $data['spell_count'],
                                    0,
                                    ',',
                                    '.'
                                )
                            )
                        );
                    }
                }
            );
        } catch (Throwable $exception) {
            $this->newLine();
            $this->error('A sincronização falhou.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            ['Campo', 'Resultado'],
            [
                ['Execução', '#'.$report['run_id']],
                ['Status', $report['status']],
                ['Fontes', $report['source_count']],
                ['Arquivos processados', $report['files_processed']],
                ['Feitiços encontrados', $report['spells_seen']],
                ['Criados', $report['created_count']],
                ['Atualizados', $report['updated_count']],
                ['Inalterados', $report['unchanged_count']],
                ['Ausentes no upstream', $report['missing_count']],
                ['Erros', $report['error_count']],
            ]
        );

        $this->newLine();
        $this->info('Sincronização concluída.');

        return self::SUCCESS;
    }
}
