<?php

namespace App\Services\Spells;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FiveEToolsClient
{
    public function manifest(): array
    {
        $data = $this->getJson(
            (string) config('spells.fiveetools.index_url')
        );

        if ($data === []) {
            throw new RuntimeException(
                'O índice de feitiços do 5etools retornou vazio.'
            );
        }

        foreach ($data as $source => $filename) {
            if (! is_string($source) || ! is_string($filename) || $filename === '') {
                throw new RuntimeException(
                    'O índice de feitiços do 5etools possui um registro inválido.'
                );
            }
        }

        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);

        return $data;
    }

    public function spellsFromFile(string $filename): array
    {
        $url = rtrim(
            (string) config('spells.fiveetools.base_url'),
            '/'
        ).'/'.ltrim($filename, '/');

        $data = $this->getJson($url);

        if (! array_key_exists('spell', $data) || ! is_array($data['spell'])) {
            throw new RuntimeException(
                "O arquivo [{$filename}] não contém um array válido em [spell]."
            );
        }

        return $data['spell'];
    }

    private function getJson(string $url): array
    {
        if ($url === '') {
            throw new RuntimeException(
                'A URL configurada para o 5etools está vazia.'
            );
        }

        $attempts = max(
            1,
            (int) config('spells.fiveetools.retries', 3)
        );

        $sleepMs = max(
            0,
            (int) config('spells.fiveetools.retry_sleep_ms', 750)
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
                        (int) config('spells.fiveetools.timeout', 45)
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
}
