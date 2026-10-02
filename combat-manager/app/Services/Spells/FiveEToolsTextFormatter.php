<?php

namespace App\Services\Spells;

class FiveEToolsTextFormatter
{
    public function entriesToText(mixed $entries): string
    {
        $parts = $this->extract($entries);

        $parts = array_values(array_filter(
            array_map(
                fn (string $part) => trim($part),
                $parts
            ),
            fn (string $part) => $part !== ''
        ));

        return trim(implode("\n\n", $parts));
    }

    public function cleanInlineTags(string $text): string
    {
        $previous = null;

        for ($i = 0; $i < 8 && $previous !== $text; $i++) {
            $previous = $text;

            $text = preg_replace_callback(
                '/\{@([a-zA-Z][a-zA-Z0-9]*)\s+([^{}]+)\}/u',
                function (array $matches): string {
                    $payload = $matches[2];
                    $parts = explode('|', $payload);

                    return trim($parts[0] ?? '');
                },
                $text
            ) ?? $text;
        }

        $text = str_replace(
            ['{@h}', '{@hitYourSpellAttack}', '{@dcYourSpellSave}'],
            ['', 'seu modificador de ataque mágico', 'sua CD de magia'],
            $text
        );

        return trim(
            preg_replace('/[ \t]+/u', ' ', $text) ?? $text
        );
    }

    private function extract(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (is_string($value)) {
            return [$this->cleanInlineTags($value)];
        }

        if (is_int($value) || is_float($value)) {
            return [(string) $value];
        }

        if (! is_array($value)) {
            return [];
        }

        if (array_is_list($value)) {
            $result = [];

            foreach ($value as $item) {
                array_push($result, ...$this->extract($item));
            }

            return $result;
        }

        $type = $value['type'] ?? null;
        $result = [];

        if (isset($value['name']) && is_string($value['name'])) {
            $name = $this->cleanInlineTags($value['name']);

            if ($name !== '') {
                $result[] = $name;
            }
        }

        if ($type === 'table') {
            if (isset($value['caption']) && is_string($value['caption'])) {
                $result[] = $this->cleanInlineTags($value['caption']);
            }

            if (isset($value['colLabels']) && is_array($value['colLabels'])) {
                $result[] = implode(
                    ' | ',
                    array_map(
                        fn ($item) => $this->cleanInlineTags((string) $item),
                        $value['colLabels']
                    )
                );
            }

            if (isset($value['rows']) && is_array($value['rows'])) {
                foreach ($value['rows'] as $row) {
                    if (! is_array($row)) {
                        continue;
                    }

                    $cells = [];

                    foreach ($row as $cell) {
                        $cellText = $this->entriesToText($cell);

                        if ($cellText !== '') {
                            $cells[] = str_replace("\n", ' ', $cellText);
                        }
                    }

                    if ($cells !== []) {
                        $result[] = implode(' | ', $cells);
                    }
                }
            }

            return $result;
        }

        foreach (['entry', 'entries', 'items'] as $key) {
            if (array_key_exists($key, $value)) {
                array_push($result, ...$this->extract($value[$key]));
            }
        }

        return $result;
    }
}
