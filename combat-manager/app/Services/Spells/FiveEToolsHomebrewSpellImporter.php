<?php

namespace App\Services\Spells;

use App\Models\SpellImportRun;
use Closure;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FiveEToolsHomebrewSpellImporter
{
    private const PROVIDER = '5etools-homebrew';

    private const JSON_COLUMNS = [
        'components',
        'effects',
        'upcast',
        'other_sources',
        'casting_time_data',
        'range_data',
        'duration_data',
        'description_data',
        'higher_level_data',
        'reprinted_as',
        'scaling_level_dice',
        'damage_types',
        'saving_throws',
        'area_tags',
        'misc_tags',
        'raw_data',
    ];

    public function __construct(
        private readonly FiveEToolsHomebrewClient $client,
        private readonly FiveEToolsSpellNormalizer $normalizer
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function sync(
        ?string $match = null,
        ?Closure $progress = null
    ): array {
        $run = SpellImportRun::query()->create([
            'provider' => self::PROVIDER,
            'status' => SpellImportRun::STATUS_RUNNING,
            'index_url' => (string) config(
                'spells.fiveetools_homebrew.index_url'
            ),
            'started_at' => now(),
        ]);

        $errors = [];
        $seenKeys = [];

        try {
            $files = $this->client->spellFiles();

            if ($match !== null && trim($match) !== '') {
                $needle = mb_strtolower(trim($match), 'UTF-8');

                $files = array_values(array_filter(
                    $files,
                    fn (string $path) => str_contains(
                        mb_strtolower($path, 'UTF-8'),
                        $needle
                    )
                ));
            }

            if ($files === []) {
                throw new RuntimeException(
                    'Nenhum arquivo homebrew com spells corresponde ao filtro informado.'
                );
            }

            $run->update([
                'source_count' => count($files),
                'manifest' => $files,
            ]);

            foreach ($files as $path) {
                $progress?->__invoke('file:start', [
                    'path' => $path,
                ]);

                try {
                    $spells = $this->client->spellsFromFile($path);

                    DB::transaction(function () use (
                        $spells,
                        $path,
                        &$seenKeys,
                        $run
                    ): void {
                        foreach ($spells as $spell) {
                            if (! is_array($spell)) {
                                throw new RuntimeException(
                                    "O arquivo [{$path}] contém um feitiço inválido."
                                );
                            }

                            $normalized = $this->normalizer->normalize(
                                $spell,
                                self::PROVIDER
                            );

                            $externalKey = $normalized['external_key'];
                            $seenKeys[] = $externalKey;

                            $existing = DB::table('spells')
                                ->where('provider', self::PROVIDER)
                                ->where('external_key', $externalKey)
                                ->first([
                                    'id',
                                    'upstream_hash',
                                    'imported_at',
                                ]);

                            $now = now();
                            $run->spells_seen++;

                            if ($existing === null) {
                                $payload = $this->prepareForDatabase($normalized);

                                $payload['imported_at'] = $now;
                                $payload['last_seen_at'] = $now;
                                $payload['created_at'] = $now;
                                $payload['updated_at'] = $now;

                                DB::table('spells')->insert($payload);

                                $run->created_count++;
                                continue;
                            }

                            if (
                                is_string($existing->upstream_hash)
                                && $existing->upstream_hash !== ''
                                && hash_equals(
                                    $existing->upstream_hash,
                                    (string) $normalized['upstream_hash']
                                )
                            ) {
                                DB::table('spells')
                                    ->where('id', $existing->id)
                                    ->update([
                                        'last_seen_at' => $now,
                                        'upstream_missing' => false,
                                    ]);

                                $run->unchanged_count++;
                                continue;
                            }

                            $payload = $this->prepareForDatabase($normalized);

                            $payload['last_seen_at'] = $now;
                            $payload['updated_at'] = $now;

                            DB::table('spells')
                                ->where('id', $existing->id)
                                ->update($payload);

                            $run->updated_count++;
                        }
                    });

                    $run->files_processed++;
                    $run->save();

                    $progress?->__invoke('file:done', [
                        'path' => $path,
                        'spell_count' => count($spells),
                    ]);
                } catch (\Throwable $exception) {
                    $errors[] = [
                        'file' => $path,
                        'message' => $exception->getMessage(),
                    ];

                    $progress?->__invoke('file:error', [
                        'path' => $path,
                        'message' => $exception->getMessage(),
                    ]);

                    // Homebrew é muito heterogêneo. Um arquivo quebrado não deve
                    // impedir a importação dos demais arquivos.
                    continue;
                }
            }

            /*
             * Só marcamos "missing" quando a sincronização foi completa.
             * Um --match parcial nunca deve marcar o restante do catálogo como
             * desaparecido.
             */
            if ($match === null || trim($match) === '') {
                $seenKeys = array_values(array_unique($seenKeys));

                if ($seenKeys !== []) {
                    $missingQuery = DB::table('spells')
                        ->where('provider', self::PROVIDER)
                        ->whereNotIn('external_key', $seenKeys)
                        ->where('upstream_missing', false);

                    $run->missing_count = (clone $missingQuery)->count();

                    $missingQuery->update([
                        'upstream_missing' => true,
                        'updated_at' => now(),
                    ]);
                }
            }

            $run->error_count = count($errors);
            $run->errors = $errors;
            $run->finished_at = now();

            if ($run->files_processed === 0 && $errors !== []) {
                $run->status = SpellImportRun::STATUS_FAILED;
            } elseif ($errors !== []) {
                $run->status = SpellImportRun::STATUS_PARTIAL;
            } else {
                $run->status = SpellImportRun::STATUS_SUCCESS;
            }

            $run->save();

            return $this->report($run);
        } catch (\Throwable $exception) {
            $run->status = SpellImportRun::STATUS_FAILED;
            $run->error_count = max(1, count($errors));
            $run->errors = $errors !== []
                ? $errors
                : [['message' => $exception->getMessage()]];
            $run->finished_at = now();
            $run->save();

            throw $exception;
        }
    }

    private function prepareForDatabase(array $data): array
    {
        foreach (self::JSON_COLUMNS as $column) {
            if (! array_key_exists($column, $data)) {
                continue;
            }

            if ($data[$column] === null) {
                continue;
            }

            $data[$column] = json_encode(
                $data[$column],
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_PRESERVE_ZERO_FRACTION
            );
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function report(SpellImportRun $run): array
    {
        return [
            'run_id' => $run->id,
            'status' => $run->status,
            'source_count' => (int) $run->source_count,
            'files_processed' => (int) $run->files_processed,
            'spells_seen' => (int) $run->spells_seen,
            'created_count' => (int) $run->created_count,
            'updated_count' => (int) $run->updated_count,
            'unchanged_count' => (int) $run->unchanged_count,
            'missing_count' => (int) $run->missing_count,
            'error_count' => (int) $run->error_count,
        ];
    }
}
