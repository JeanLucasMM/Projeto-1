<?php

namespace App\Services\Spells;

use App\Models\SpellImportRun;
use Closure;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FiveEToolsSpellImporter
{
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
        'damage_types',
        'saving_throws',
        'area_tags',
        'misc_tags',
        'raw_data',
    ];

    public function __construct(
        private readonly FiveEToolsClient $client,
        private readonly FiveEToolsSpellNormalizer $normalizer
    ) {
    }

    public function sync(
        array $onlySources = [],
        ?Closure $progress = null
    ): array {
        $run = SpellImportRun::query()->create([
            'provider' => '5etools',
            'status' => SpellImportRun::STATUS_RUNNING,
            'index_url' => (string) config('spells.fiveetools.index_url'),
            'started_at' => now(),
        ]);

        $errors = [];
        $seenKeys = [];

        try {
            $manifest = $this->client->manifest();
            $manifest = $this->filterManifest($manifest, $onlySources);

            if ($manifest === []) {
                throw new RuntimeException(
                    'Nenhuma fonte do 5etools corresponde ao filtro informado.'
                );
            }

            $run->update([
                'source_count' => count($manifest),
                'manifest' => $manifest,
            ]);

            foreach ($manifest as $source => $filename) {
                if ($progress) {
                    $progress('source:start', [
                        'source' => $source,
                        'filename' => $filename,
                    ]);
                }

                try {
                    $spells = $this->client->spellsFromFile($filename);

                    DB::transaction(function () use (
                        $spells,
                        $source,
                        &$seenKeys,
                        $run
                    ): void {
                        foreach ($spells as $spell) {
                            if (! is_array($spell)) {
                                throw new RuntimeException(
                                    "A fonte [{$source}] contém um feitiço inválido."
                                );
                            }

                            $normalized = $this->normalizer->normalize($spell);
                            $externalKey = $normalized['external_key'];
                            $seenKeys[] = $externalKey;

                            $existing = DB::table('spells')
                                ->where('provider', '5etools')
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
                            } elseif (
                                $existing->upstream_hash !== null
                                && hash_equals(
                                    (string) $existing->upstream_hash,
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
                            } else {
                                $payload = $this->prepareForDatabase($normalized);
                                $payload['last_seen_at'] = $now;
                                $payload['updated_at'] = $now;

                                DB::table('spells')
                                    ->where('id', $existing->id)
                                    ->update($payload);

                                $run->updated_count++;
                            }
                        }
                    });

                    $run->files_processed++;
                    $run->save();

                    if ($progress) {
                        $progress('source:done', [
                            'source' => $source,
                            'filename' => $filename,
                            'spell_count' => count($spells),
                        ]);
                    }
                } catch (\Throwable $exception) {
                    $errors[] = [
                        'source' => $source,
                        'filename' => $filename,
                        'message' => $exception->getMessage(),
                    ];

                    throw $exception;
                }
            }

            if ($onlySources === []) {
                $seenKeys = array_values(array_unique($seenKeys));

                if ($seenKeys !== []) {
                    $missingQuery = DB::table('spells')
                        ->where('provider', '5etools')
                        ->whereNotIn('external_key', $seenKeys)
                        ->where('upstream_missing', false);

                    $run->missing_count = (clone $missingQuery)->count();

                    $missingQuery->update([
                        'upstream_missing' => true,
                        'updated_at' => now(),
                    ]);
                }
            }

            $run->status = SpellImportRun::STATUS_SUCCESS;
            $run->error_count = 0;
            $run->errors = [];
            $run->finished_at = now();
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

    private function filterManifest(
        array $manifest,
        array $onlySources
    ): array {
        if ($onlySources === []) {
            return $manifest;
        }

        $wanted = array_map(
            fn (string $source) => strtoupper(trim($source)),
            $onlySources
        );

        return array_filter(
            $manifest,
            fn (string $filename, string $source) => in_array(
                strtoupper($source),
                $wanted,
                true
            ),
            ARRAY_FILTER_USE_BOTH
        );
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

    private function report(SpellImportRun $run): array
    {
        return [
            'run_id' => $run->id,
            'status' => $run->status,
            'source_count' => $run->source_count,
            'files_processed' => $run->files_processed,
            'spells_seen' => $run->spells_seen,
            'created_count' => $run->created_count,
            'updated_count' => $run->updated_count,
            'unchanged_count' => $run->unchanged_count,
            'missing_count' => $run->missing_count,
            'error_count' => $run->error_count,
        ];
    }
}
