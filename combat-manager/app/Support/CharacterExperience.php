<?php

namespace App\Support;

final class CharacterExperience
{
    public const MAX_LEVEL = 20;

    /*
    |--------------------------------------------------------------------------
    | Experiência cumulativa por nível — D&D 5e
    |--------------------------------------------------------------------------
    |
    | XP e nível são independentes no Spellbound. Esta classe calcula somente
    | marcos e progresso; ela nunca altera o nível do Character.
    |
    */

    private const THRESHOLDS = [
        1 => 0,
        2 => 300,
        3 => 900,
        4 => 2700,
        5 => 6500,
        6 => 14000,
        7 => 23000,
        8 => 34000,
        9 => 48000,
        10 => 64000,
        11 => 85000,
        12 => 100000,
        13 => 120000,
        14 => 140000,
        15 => 165000,
        16 => 195000,
        17 => 225000,
        18 => 265000,
        19 => 305000,
        20 => 355000,
    ];

    public static function threshold(
        int $level
    ): int {
        $level = self::normalizeLevel(
            $level
        );

        return self::THRESHOLDS[
            $level
        ];
    }

    public static function nextThreshold(
        int $level
    ): ?int {
        $level = self::normalizeLevel(
            $level
        );

        if ($level >= self::MAX_LEVEL) {
            return null;
        }

        return self::THRESHOLDS[
            $level + 1
        ];
    }

    public static function effectiveTotal(
        int $level,
        int $storedExperience
    ): int {
        /*
         * Personagens já existentes começam, no mínimo, no marco cumulativo
         * correspondente ao nível atual.
         */
        return max(
            self::threshold(
                $level
            ),
            max(
                0,
                $storedExperience
            )
        );
    }

    public static function remaining(
        int $level,
        int $experience
    ): int {
        $next = self::nextThreshold(
            $level
        );

        if ($next === null) {
            return 0;
        }

        return max(
            0,
            $next - max(
                0,
                $experience
            )
        );
    }

    public static function progress(
        int $level,
        int $experience
    ): array {
        $level = self::normalizeLevel(
            $level
        );

        $experience = self::effectiveTotal(
            $level,
            $experience
        );

        $current = self::threshold(
            $level
        );

        $next = self::nextThreshold(
            $level
        );

        if ($next === null) {
            return [
                'level' => $level,
                'experience' => $experience,
                'current_threshold' => $current,
                'next_threshold' => null,
                'earned_in_level' => max(
                    0,
                    $experience - $current
                ),
                'required_in_level' => 0,
                'remaining' => 0,
                'percent' => 100.0,
                'ready_for_next_level' => false,
                'max_level' => true,
            ];
        }

        $requiredInLevel = max(
            1,
            $next - $current
        );

        $earnedInLevel = max(
            0,
            $experience - $current
        );

        $percent = min(
            100,
            max(
                0,
                (
                    $earnedInLevel
                    /
                    $requiredInLevel
                )
                *
                100
            )
        );

        return [
            'level' => $level,
            'experience' => $experience,
            'current_threshold' => $current,
            'next_threshold' => $next,
            'earned_in_level' => $earnedInLevel,
            'required_in_level' => $requiredInLevel,
            'remaining' => max(
                0,
                $next - $experience
            ),
            'percent' => round(
                $percent,
                2
            ),
            'ready_for_next_level' =>
                $experience >= $next,
            'max_level' => false,
        ];
    }

    public static function table(): array
    {
        return self::THRESHOLDS;
    }

    private static function normalizeLevel(
        int $level
    ): int {
        return max(
            1,
            min(
                self::MAX_LEVEL,
                $level
            )
        );
    }
}
