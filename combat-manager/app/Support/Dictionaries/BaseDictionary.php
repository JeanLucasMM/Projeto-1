<?php

namespace App\Support\Dictionaries;

abstract class BaseDictionary
{
    protected const ITEMS = [];

    public static function options(): array
    {
        return static::ITEMS;
    }

    public static function label(?string $key): string
    {
        if ($key === null) {
            return '';
        }

        return static::ITEMS[$key] ?? ucfirst($key);
    }

    public static function exists(string $key): bool
    {
        return array_key_exists($key, static::ITEMS);
    }

    public static function keys(): array
    {
        return array_keys(static::ITEMS);
    }
}