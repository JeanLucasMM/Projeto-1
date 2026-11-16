<?php

namespace App\Services\Spells;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FiveEToolsHomebrewClient
{
    /**
     * @return array<int, string>
     */
    public function spellFiles(): array
    {
        $data = $this->getJson(
            (string) config('spells.fiveetools_homebrew.index_url')
        );

        $spellIndex = $data['spell'] ?? null;

        if (! is_array($spellIndex)) {
            throw new RuntimeException(
                'O index-props.json do homebrew não contém um índice válido em [spell].'
            );
        }

        $files = array_keys($spellIndex);

        $files = array_values(array_filter(
            $files,
            fn ($path) => is_string($path) && trim($path) !== ''
        ));

        $files = array_values(array_unique($files));

        natcasesort($files);

        return array_values($files);
    }

    /**
     * Retorna o documento completo porque precisamos tanto do array `spell`
     * quanto de `_meta.sources`.
     *
     * @return array<string, mixed>
     */
    public function documentFromFile(string $path): array
    {
        $url = rtrim(
            (string) config('spells.fiveetools_homebrew.base_url'),
            '/'
        ).'/'.$this->encodePath($path);

        $data = $this->getJson($url);

        if (! array_key_exists('spell', $data) || ! is_array($data['spell'])) {
            throw new RuntimeException(
                "O arquivo homebrew [{$path}] não contém um array válido em [spell]."
            );
        }

        return $data;
    }

    /**
     * Mantido por compatibilidade com código anterior.
     *
     * @return array<int, array<string, mixed>>
     */
    public function spellsFromFile(string $path): array
    {
        $document = $this->documentFromFile($path);

        return $document['spell'];
    }

    private function getJson(string $url): array
    {
        if ($url === '') {
            throw new RuntimeException(
                'A URL configurada para o homebrew do 5etools está vazia.'
            );
        }

        $attempts = max(
            1,
            (int) config('spells.fiveetools_homebrew.retries', 3)
        );

        $sleepMs = max(
            0,
            (int) config(
                'spells.fiveetools_homebrew.retry_sleep_ms',
                750
            )
        );

        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                /** @var Response $response */
                $response = Http::acceptJson()
                    ->withHeaders([
                        'User-Agent' => 'Spellbound/1.0',
                    ])
                    ->timeout(
                        (int) config(
                            'spells.fiveetools_homebrew.timeout',
                            60
                        )
                    )
                    ->get($url);

                $response->throw();

                $json = $response->json();

                if (! is_array($json)) {
                    throw new RuntimeException(
                        "A resposta de [{$url}] não é um JSON válido."
                    );
                }

                return $json;
            } catch (\Throwable $exception) {
                $lastException = $exception;

                if ($attempt < $attempts && $sleepMs > 0) {
                    usleep($sleepMs * 1000);
                }
            }
        }

        throw new RuntimeException(
            "Não foi possível baixar [{$url}] após {$attempts} tentativa(s).".
            ($lastException ? ' '.$lastException->getMessage() : ''),
            previous: $lastException
        );
    }

    private function encodePath(string $path): string
    {
        return implode(
            '/',
            array_map(
                fn (string $segment) => rawurlencode($segment),
                explode('/', str_replace('\\', '/', $path))
            )
        );
    }
}
