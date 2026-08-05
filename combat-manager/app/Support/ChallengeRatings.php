<?php

namespace App\Support;

class ChallengeRatings
{
    protected const DATA = [

        '0' => ['xp' => 10, 'pb' => 2],
        '1/8' => ['xp' => 25, 'pb' => 2],
        '1/4' => ['xp' => 50, 'pb' => 2],
        '1/2' => ['xp' => 100, 'pb' => 2],

        '1' => ['xp' => 200, 'pb' => 2],
        '2' => ['xp' => 450, 'pb' => 2],
        '3' => ['xp' => 700, 'pb' => 2],
        '4' => ['xp' => 1100, 'pb' => 2],

        '5' => ['xp' => 1800, 'pb' => 3],
        '6' => ['xp' => 2300, 'pb' => 3],
        '7' => ['xp' => 2900, 'pb' => 3],
        '8' => ['xp' => 3900, 'pb' => 3],

        '9' => ['xp' => 5000, 'pb' => 4],
        '10' => ['xp' => 5900, 'pb' => 4],
        '11' => ['xp' => 7200, 'pb' => 4],
        '12' => ['xp' => 8400, 'pb' => 4],

        '13' => ['xp' => 10000, 'pb' => 5],
        '14' => ['xp' => 11500, 'pb' => 5],
        '15' => ['xp' => 13000, 'pb' => 5],
        '16' => ['xp' => 15000, 'pb' => 5],

        '17' => ['xp' => 18000, 'pb' => 6],
        '18' => ['xp' => 20000, 'pb' => 6],
        '19' => ['xp' => 22000, 'pb' => 6],
        '20' => ['xp' => 25000, 'pb' => 6],

        '21' => ['xp' => 33000, 'pb' => 7],
        '22' => ['xp' => 41000, 'pb' => 7],
        '23' => ['xp' => 50000, 'pb' => 7],
        '24' => ['xp' => 62000, 'pb' => 7],

        '25' => ['xp' => 75000, 'pb' => 8],
        '26' => ['xp' => 90000, 'pb' => 8],
        '27' => ['xp' => 105000, 'pb' => 8],
        '28' => ['xp' => 120000, 'pb' => 8],

        '29' => ['xp' => 135000, 'pb' => 9],
        '30' => ['xp' => 155000, 'pb' => 9],

    ];

    public static function all(): array
    {
        return array_keys(static::DATA);
    }

    public static function exists(string|int|float $cr): bool
    {
        return array_key_exists((string) $cr, static::DATA);
    }

    public static function xp(string|int|float $cr): int
    {
        return static::DATA[(string) $cr]['xp'] ?? 0;
    }

    public static function proficiency(string|int|float $cr): int
    {
        return static::DATA[(string) $cr]['pb'] ?? 2;
    }

    public static function label(string|int|float $cr): string
    {
        return (string) $cr;
    }
}
