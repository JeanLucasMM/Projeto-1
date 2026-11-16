<?php

namespace App\Services\Spells;

use App\Models\Spell;

/** Converte a duração publicada de uma magia em rodadas de seis segundos. */
class SpellDurationResolver
{
    /** @return array{total_rounds:?int,label:string} */
    public function resolve(?Spell $spell): array
    {
        if (!$spell) {
            return ['total_rounds' => null, 'label' => 'Não informada'];
        }

        $label = trim((string) ($spell->duration ?? ''));
        $total = $this->fromText($label);
        if ($total === null) {
            $raw = $spell->raw_data ?? null;
            if (is_string($raw)) {
                $raw = json_decode($raw, true);
            } elseif (is_object($raw)) {
                $raw = (array) $raw;
            }
            if (is_array($raw)) {
                // O registro oficial 5etools usa duration: [{type:timed, duration:{type:minute,amount:1}}].
                $total = $this->fromStructured($raw['duration'] ?? null);
                if ($total === null && isset($raw['spell']) && is_array($raw['spell'])) {
                    $total = $this->fromStructured($raw['spell']['duration'] ?? null);
                }
            }
        }

        return [
            'total_rounds' => $total,
            'label' => $label !== '' ? $label : ($total !== null ? 'Duração definida' : 'Não informada'),
        ];
    }

    private function fromStructured(mixed $data): ?int
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $this->fromStructured($decoded) : $this->fromText($data);
        }
        if (!is_array($data)) {
            return null;
        }
        if (array_is_list($data)) {
            foreach ($data as $entry) {
                $rounds = $this->fromStructured($entry);
                if ($rounds !== null) return $rounds;
            }
            return null;
        }
        $kind = strtolower((string) ($data['type'] ?? ''));
        if ($kind === 'timed' && isset($data['duration'])) {
            return $this->fromStructured($data['duration']);
        }
        $amount = $data['amount'] ?? null;
        if (!is_numeric($amount) || (float) $amount <= 0) return null;
        $seconds = $this->secondsForUnit($kind);
        return $seconds !== null ? (int) ceil(((float) $amount * $seconds) / 6) : null;
    }

    private function fromText(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') return null;
        if (str_starts_with($value, '[') || str_starts_with($value, '{')) {
            $json = json_decode($value, true);
            if (is_array($json)) return $this->fromStructured($json);
        }
        $text = function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
        // A duração deve ser um limite temporal explícito; "especial" e "até ser dissipada" não são limites.
        if (preg_match('/\b(instantaneous|instantânea|instantâneo|special|especial|permanent|permanente|until dispelled|até ser dissipada|indefinid[ao])\b/u', $text)) {
            return null;
        }
        if (!preg_match('/(\d+(?:[.,]\d+)?)\s*(seconds?|segundos?|secs?|minutes?|minutos?|mins?|hours?|horas?|days?|dias?|weeks?|semanas?|rounds?|rodadas?|turns?|turnos?)/iu', $text, $m)) {
            return null;
        }
        $amount = (float) str_replace(',', '.', $m[1]);
        $unit = function_exists('mb_strtolower') ? mb_strtolower($m[2]) : strtolower($m[2]);
        $seconds = $this->secondsForUnit($unit);
        return $seconds !== null && $amount > 0 ? max(1, (int) ceil($amount * $seconds / 6)) : null;
    }

    private function secondsForUnit(string $unit): ?int
    {
        return match (true) {
            preg_match('/^(second|segundo|sec)/u', $unit) === 1 => 1,
            preg_match('/^(minute|minuto|min)/u', $unit) === 1 => 60,
            preg_match('/^(hour|hora)/u', $unit) === 1 => 3600,
            preg_match('/^(day|dia)/u', $unit) === 1 => 86400,
            preg_match('/^(week|semana)/u', $unit) === 1 => 604800,
            preg_match('/^(round|rodada|turn|turno)/u', $unit) === 1 => 6,
            default => null,
        };
    }
}
