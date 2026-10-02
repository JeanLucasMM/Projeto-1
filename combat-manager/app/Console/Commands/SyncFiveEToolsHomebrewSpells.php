<?php

namespace App\Console\Commands;

use App\Services\Spells\FiveEToolsHomebrewSpellImporter;
use Illuminate\Console\Command;
use Throwable;

class SyncFiveEToolsHomebrewSpells extends Command
{
    protected $signature = 'spells:sync-5etools-homebrew
                            {--match= : Importa apenas arquivos cujo caminho contenha este texto}';

    protected $description = 'Importa somente feitiços do catálogo third-party/homebrew compatível com 5etools.';

    public function handle(
        FiveEToolsHomebrewSpellImporter $importer
    ): int {
        $match = $this->option('match');
        $match = is_string($match) && trim($match) !== ''
            ? trim($match)
            : null;

        $this->newLine();
        $this->info(
            'Spellbound — sincronização de feitiços third-party/homebrew'
        );

        if ($match !== null) {
            $this->line('Filtro de arquivo: '.$match);
        } else {
            $this->line(
                'Modo: todos os arquivos homebrew que possuem a propriedade spell'
            );
        }

        $this->newLine();

        try {
            $report = $importer->sync(
                $match,
                function (string $event, array $data): void {
                    if ($event === 'file:start') {
                        $this->line('→ '.$data['path']);
                    }

                    if ($event === 'file:done') {
                        $this->line(
                            '  ✓ '.number_format(
                                (int) $data['spell_count'],
                                0,
                                ',',
                                '.'
                            ).' feitiço(s)'
                        );
                    }

                    if ($event === 'file:error') {
                        $this->warn(
                            '  ! arquivo ignorado: '.$data['message']
                        );
                    }
                }
            );
        } catch (Throwable $exception) {
            $this->newLine();
            $this->error('A sincronização homebrew falhou.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            ['Campo', 'Resultado'],
            [
                ['Execução', '#'.$report['run_id']],
                ['Status', $report['status']],
                ['Arquivos encontrados', $report['source_count']],
                ['Arquivos processados', $report['files_processed']],
                ['Feitiços encontrados', $report['spells_seen']],
                ['Criados', $report['created_count']],
                ['Atualizados', $report['updated_count']],
                ['Inalterados', $report['unchanged_count']],
                ['Ausentes no upstream', $report['missing_count']],
                ['Erros de arquivo', $report['error_count']],
            ]
        );

        $this->newLine();

        if ($report['status'] === 'partial') {
            $this->warn(
                'Sincronização concluída parcialmente. Alguns arquivos homebrew foram ignorados; consulte spell_import_runs.errors.'
            );

            return self::SUCCESS;
        }

        $this->info('Sincronização homebrew concluída.');

        return self::SUCCESS;
    }
}
