<?php

namespace App\Services\Spells;

use App\Models\HomebrewSpellSource;
use Illuminate\Support\Facades\DB;

class HomebrewSpellSourceRegistry
{
    public const PROVIDER = '5etools-homebrew';

    /**
     * Registra/enriquece as fontes declaradas no `_meta.sources` de um arquivo.
     *
     * @param  array<int, mixed>  $sources
     */
    public function registerMetadataSources(
        array $sources,
        string $originFile
    ): void {
        foreach ($sources as $source) {
            if (! is_array($source)) {
                continue;
            }

            $code = trim((string) (
                $source['json']
                ?? $source['abbreviation']
                ?? ''
            ));

            if ($code === '') {
                continue;
            }

            $this->upsert(
                sourceCode: $code,
                name: $this->sourceName($source, $code),
                originFile: $originFile,
                metadata: $source
            );
        }
    }

    public function resolveForSpell(
        string $sourceCode,
        string $originFile
    ): HomebrewSpellSource {
        $sourceCode = trim($sourceCode);

        return $this->upsert(
            sourceCode: $sourceCode,
            name: $sourceCode,
            originFile: $originFile,
            metadata: null
        );
    }

    public function refreshCounts(): void
    {
        $sources = HomebrewSpellSource::query()
            ->where('provider', self::PROVIDER)
            ->get();

        foreach ($sources as $source) {
            $count = DB::table('spells')
                ->where('provider', self::PROVIDER)
                ->where('homebrew_spell_source_id', $source->id)
                ->where('upstream_missing', false)
                ->count();

            $source->forceFill([
                'spell_count' => $count,
            ])->save();
        }
    }

    private function upsert(
        string $sourceCode,
        string $name,
        string $originFile,
        ?array $metadata
    ): HomebrewSpellSource {
        $source = HomebrewSpellSource::query()->firstOrNew([
            'provider' => self::PROVIDER,
            'source_code' => $sourceCode,
        ]);

        $currentFiles = is_array($source->origin_files)
            ? $source->origin_files
            : [];

        if (! in_array($originFile, $currentFiles, true)) {
            $currentFiles[] = $originFile;
            sort($currentFiles, SORT_NATURAL | SORT_FLAG_CASE);
        }

        $payload = [
            'is_available' => true,
            'last_seen_at' => now(),
            'origin_files' => array_values($currentFiles),
        ];

        /*
         * Não sobrescrevemos um nome rico por um código cru.
         */
        $currentName = trim((string) $source->name);

        if (
            $currentName === ''
            || $currentName === $source->source_code
            || $name !== $sourceCode
        ) {
            $payload['name'] = $name;
        }

        if ($metadata !== null) {
            $payload['metadata'] = $metadata;

            if (isset($metadata['authors']) && is_array($metadata['authors'])) {
                $payload['authors'] = array_values($metadata['authors']);
            }

            $url = $metadata['url'] ?? null;

            if (is_string($url) && trim($url) !== '') {
                $payload['url'] = trim($url);
            }
        }

        $source->forceFill($payload)->save();

        return $source;
    }

    private function sourceName(
        array $metadata,
        string $fallback
    ): string {
        foreach (['full', 'name', 'abbreviation'] as $field) {
            $value = $metadata[$field] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return $fallback;
    }
}
