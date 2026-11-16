<?php

namespace App\Console\Commands;

use App\Services\Spells\SpellClassListService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Sincronização global, por proveniência, das listas de magia 5etools.
 *
 * A fonte data/spells/sources.json possui relações EXPLÍCITAS:
 *   <spell source> / <spell name> / class | classVariant.
 *
 * IMPORTANTE: nunca inferir uma classe a partir de nós genéricos do lookup
 * (isso transforma permissões de subclasses/variantes em listas-base).
 */
class SyncFiveEToolsSpellLists extends Command
{
    protected $signature = 'spells:sync-5etools-lists
        {--apply : Confirma a substituição atômica das listas importadas}
        {--skip-remote : Opção antiga desativada por segurança}';

    protected $description = 'Audita e reconstrói TODAS as listas 5etools por fonte, classe e tipo, sem alterar os feitiços dos personagens.';

    private const SOURCE_URL = 'https://raw.githubusercontent.com/5etools-mirror-3/5etools-src/main/data/spells/sources.json';
    private const STAGING = 'spell_class_lists_rebuild_stage';
    private const CHUNK_SIZE = 125;
    private const BUFFER_SIZE = 350;

    private array $buffer = [];
    private int $officialMatched = 0;
    private int $officialSpells = 0;
    private int $missingOfficial = 0;
    private int $egwWizardMapped = 0;
    private int $ggrWizardMapped = 0;
    private int $localRows = 0;
    private int $authoritativeRows = 0;
    private int $processed = 0;
    private array $unmappedExamples = [];

    public function __construct(private readonly SpellClassListService $lists)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('skip-remote')) {
            $this->error('A opção --skip-remote foi desativada: sem sources.json não é possível reconstruir as listas-base com segurança.');
            return self::FAILURE;
        }

        try {
            $response = Http::timeout(100)->retry(3, 1000)->get(self::SOURCE_URL);
            $response->throw();
            $sourceMap = $response->json();
        } catch (Throwable $e) {
            $this->error('Não foi possível obter a fonte oficial. Nenhuma lista foi alterada: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (! is_array($sourceMap)
            || empty($sourceMap['XPHB']['Acid Splash']['class'])
            || empty($sourceMap['XPHB']['Magic Missile']['class'])) {
            $this->error('sources.json não possui a estrutura esperada. Nenhuma lista foi alterada.');
            return self::FAILURE;
        }

        $lookup = $this->makeLookup($sourceMap);
        unset($sourceMap, $response);

        $this->line('Fonte: data/spells/sources.json (relações class e classVariant explícitas).');
        $this->line('Processando feitiços em lotes. A tabela original não será modificada durante a preparação.');

        try {
            // MySQL (banco do Spellbound): tabela temporária na mesma conexão.
            DB::statement('DROP TEMPORARY TABLE IF EXISTS `' . self::STAGING . '`');
            DB::statement('CREATE TEMPORARY TABLE `' . self::STAGING . '` LIKE `spell_class_lists`');

            DB::table('spells')
                ->where('active', true)
                ->where('upstream_missing', false)
                ->whereIn('provider', ['5etools', '5etools-homebrew'])
                ->select(['id', 'name', 'source', 'provider', 'raw_data'])
                ->orderBy('id')
                ->chunkById(self::CHUNK_SIZE, function ($spells) use ($lookup): void {
                    foreach ($spells as $spell) {
                        $this->processed++;
                        $raw = $this->decode($spell->raw_data);
                        $classes = is_array($raw) ? ($raw['classes'] ?? null) : null;
                        $isOfficial = $spell->provider === '5etools';
                        $source = strtoupper(trim((string) $spell->source));
                        $name = $this->lists->normalize((string) $spell->name);

                        if ($isOfficial) {
                            $this->officialSpells++;
                        }

                        if ($isOfficial && isset($lookup[$source][$name])) {
                            $this->officialMatched++;
                            foreach ($this->rowsFromSourceMap($spell, $lookup[$source][$name]) as $row) {
                                $this->queueRow($row);
                                $this->authoritativeRows++;
                            }
                            // Entradas por subclasse explícitas no registro local continuam válidas.
                            foreach ($this->rowsFromRaw($spell, $classes, true) as $row) {
                                $this->queueRow($row);
                                $this->localRows++;
                            }
                        } elseif ($isOfficial && (
                            $source === 'EGW'
                            || ($source === 'GGR' && $name === $this->lists->normalize('Encode Thoughts'))
                        )) {
                            // Política de catálogo do Spellbound: os oficiais EGW
                            // sem vínculo na fonte e Encode Thoughts (GGR) são
                            // feitiços de Mago comuns. Sem restrições adicionais.
                            // Mantemos a fonte real (EGW/GGR) para proveniência.
                            $this->queueRow($this->row(
                                $spell, 'wizard', $source, null, null, 'class'
                            ));
                            if ($source === 'EGW') {
                                $this->egwWizardMapped++;
                            } else {
                                $this->ggrWizardMapped++;
                            }

                            // Concessões explícitas de subclasse continuam
                            // separadas; não promovemos variantes a lista-base.
                            foreach ($this->rowsFromRaw($spell, $classes, true) as $row) {
                                $this->queueRow($row);
                                $this->localRows++;
                            }
                        } else {
                            if ($isOfficial) {
                                $this->missingOfficial++;
                                if (count($this->unmappedExamples) < 30) {
                                    $this->unmappedExamples[] = $spell->source . ' · ' . $spell->name;
                                }
                            }
                            // Homebrew e oficiais não mapeados: apenas classes EXPLÍCITAS.
                            foreach ($this->rowsFromRaw($spell, $classes, false) as $row) {
                                $this->queueRow($row);
                                $this->localRows++;
                            }
                        }
                    }
                    $this->flushRows();
                }, 'id');
            $this->flushRows();

            $existingCount = DB::table('spell_class_lists')
                ->whereIn('provider', ['5etools', '5etools-homebrew'])->count();
            $newCount = DB::table(self::STAGING)->count();
            $baseCount = DB::table(self::STAGING)->where('list_type', 'class')->count();
            $variantCount = DB::table(self::STAGING)->where('list_type', 'variant')->count();
            $subclassCount = DB::table(self::STAGING)->where('list_type', 'subclass')->count();

            $this->table(['Métrica', 'Quantidade'], [
                ['Feitiços processados', $this->processed],
                ['Feitiços oficiais encontrados em sources.json', $this->officialMatched],
                ['Feitiços EGW vinculados ao Mago (regra Spellbound)', $this->egwWizardMapped],
                ['Encode Thoughts (GGR) vinculado ao Mago', $this->ggrWizardMapped],
                ['Oficiais ainda não mapeados', $this->missingOfficial],
                ['Relações da fonte principal (brutas)', $this->authoritativeRows],
                ['Relações adicionais do raw_data (brutas)', $this->localRows],
                ['Relações-base únicas', $baseCount],
                ['Relações de variantes únicas', $variantCount],
                ['Relações explícitas de subclasses únicas', $subclassCount],
                ['Total anterior (importados)', $existingCount],
                ['Novo total (importados)', $newCount],
            ]);

            if ($this->unmappedExamples !== []) {
                $this->warn('Oficiais ainda sem lista (verificar antes de aplicar):');
                foreach ($this->unmappedExamples as $exampleMissing) {
                    $this->line('  - ' . $exampleMissing);
                }
            }

            // Auditoria do fallback: cada magia EGW deve aparecer como Mago
            // na lista-base, com a fonte do suplemento preservada.
            $egwRows = DB::table(self::STAGING . ' as sc')
                ->join('spells as s', 's.id', '=', 'sc.spell_id')
                ->where('s.provider', '5etools')
                ->where('s.source', 'EGW')
                ->where('sc.list_type', 'class')
                ->where('sc.class_code', 'wizard')
                ->where('sc.class_source', 'EGW')
                ->count();
            $this->line("  EGW / lista-base Mago (fonte EGW): {$egwRows} vínculos");

            $ggrRows = DB::table(self::STAGING . ' as sc')
                ->join('spells as s', 's.id', '=', 'sc.spell_id')
                ->where('s.provider', '5etools')
                ->where('s.source', 'GGR')
                ->where('s.name', 'Encode Thoughts')
                ->where('sc.list_type', 'class')
                ->where('sc.class_code', 'wizard')
                ->where('sc.class_source', 'GGR')
                ->count();
            $this->line("  GGR / Encode Thoughts / lista-base Mago: {$ggrRows} vínculo(s)");

            foreach (['Acid Splash', 'Magic Missile'] as $example) {
                $rows = DB::table(self::STAGING . ' as sc')
                    ->join('spells as s', 's.id', '=', 'sc.spell_id')
                    ->where('s.name', $example)
                    ->where('s.source', 'XPHB')
                    ->where('sc.list_type', 'class')
                    ->where('sc.class_source', 'XPHB')
                    ->orderBy('sc.class_name')
                    ->pluck('sc.class_name')->unique()->values()->all();
                $this->line("  XPHB / {$example} / lista-base: " . implode(', ', $rows));
            }

            if ($newCount < 1 || $baseCount < 1
                || ($this->officialSpells >= 100 && $this->officialMatched < 50)) {
                $this->error('Mapeamento insuficiente ou incompleto. A tabela anterior continua intacta.');
                return self::FAILURE;
            }

            if ($this->missingOfficial > 0) {
                $this->error('Ainda existem feitiços oficiais sem lista segura. Nenhuma alteração foi realizada.');
                return self::FAILURE;
            }

            if (! $this->option('apply')) {
                $this->info('PRÉVIA concluída. Nenhuma lista foi modificada. Para aplicar: php artisan spells:sync-5etools-lists --apply');
                return self::SUCCESS;
            }

            DB::transaction(function (): void {
                DB::table('spell_class_lists')
                    ->whereIn('provider', ['5etools', '5etools-homebrew'])
                    ->delete();

                $cols = ['spell_id', 'provider', 'class_code', 'class_name', 'class_source',
                    'subclass_name', 'subclass_source', 'list_type', 'normalized_key', 'created_at', 'updated_at'];

                DB::table('spell_class_lists')->insertUsing(
                    $cols,
                    DB::table(self::STAGING)->select($cols)
                );
            }, 3);

            $this->info('Reconstrução global concluída. Nenhum character_spell foi alterado.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Reconstrução abortada; confira backup e logs: ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            $this->buffer = [];
            DB::statement('DROP TEMPORARY TABLE IF EXISTS `' . self::STAGING . '`');
        }
    }

    private function makeLookup(array $sourceMap): array
    {
        $map = [];
        foreach ($sourceMap as $source => $spells) {
            if (! is_array($spells)) continue;
            foreach ($spells as $name => $mapping) {
                if (is_string($name) && is_array($mapping)) {
                    $map[strtoupper(trim((string) $source))][$this->lists->normalize($name)] = $mapping;
                }
            }
        }
        return $map;
    }

    private function rowsFromSourceMap(object $spell, array $mapping): array
    {
        $rows = [];
        foreach (['class' => 'class', 'classVariant' => 'variant'] as $field => $type) {
            foreach (($mapping[$field] ?? []) as $entry) {
                if (! is_array($entry)) continue;
                $code = $this->lists->canonicalCode((string) ($entry['name'] ?? ''));
                if (! $code) continue;
                $rows[] = $this->row($spell, $code, $entry['source'] ?? null,
                    null, null, $type);
            }
        }
        return $rows;
    }

    private function rowsFromRaw(object $spell, mixed $classes, bool $onlySubclasses): array
    {
        if (! is_array($classes)) return [];
        $rows = [];
        if (! $onlySubclasses) {
            foreach (['fromClassList' => 'class', 'fromClassListVariant' => 'variant'] as $field => $type) {
                foreach (($classes[$field] ?? []) as $entry) {
                    if (! is_array($entry)) continue;
                    $code = $this->lists->canonicalCode((string) ($entry['name'] ?? ''));
                    if (! $code) continue;
                    $rows[] = $this->row($spell, $code, $entry['source'] ?? null,
                        null, null, $type);
                }
            }
        }
        foreach (($classes['fromSubclass'] ?? []) as $entry) {
            if (! is_array($entry)) continue;
            $class = $entry['class'] ?? null;
            $sub = $entry['subclass'] ?? null;
            if (! is_array($class) || ! is_array($sub)) continue;
            $code = $this->lists->canonicalCode((string) ($class['name'] ?? ''));
            if (! $code) continue;
            $subclassName = trim((string) ($sub['name'] ?? $sub['shortName'] ?? ''));
            if ($subclassName === '') continue;
            $rows[] = $this->row($spell, $code, $class['source'] ?? null,
                $subclassName, $sub['source'] ?? null, 'subclass');
        }
        return $rows;
    }

    private function row(
        object $spell,
        string $classCode,
        ?string $classSource,
        ?string $subclass,
        ?string $subclassSource,
        string $type
    ): array {
        $key = implode('|', [(int) $spell->id, (string) $spell->provider, $classCode,
            (string) $classSource, (string) $subclass, (string) $subclassSource, $type]);
        return [
            'spell_id' => (int) $spell->id,
            'provider' => (string) $spell->provider,
            'class_code' => $classCode,
            'class_name' => $this->lists->canonicalName($classCode),
            'class_source' => $classSource,
            'subclass_name' => $subclass,
            'subclass_source' => $subclassSource,
            'list_type' => $type,
            'normalized_key' => hash('sha256', $key),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function queueRow(array $row): void
    {
        $this->buffer[$row['normalized_key']] = $row;
        if (count($this->buffer) >= self::BUFFER_SIZE) $this->flushRows();
    }

    private function flushRows(): void
    {
        if ($this->buffer === []) return;
        DB::table(self::STAGING)->insertOrIgnore(array_values($this->buffer));
        $this->buffer = [];
    }

    private function decode(mixed $raw): mixed
    {
        if (is_array($raw)) return $raw;
        if (! is_string($raw)) return null;
        return json_decode($raw, true);
    }
}
