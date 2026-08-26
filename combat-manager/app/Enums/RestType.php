<?php

namespace App\Enums;

enum RestType: string
{
    case SHORT = 'short';
    case LONG = 'long';

    public function isShort(): bool
    {
        return $this === self::SHORT;
    }

    public function isLong(): bool
    {
        return $this === self::LONG;
    }
}