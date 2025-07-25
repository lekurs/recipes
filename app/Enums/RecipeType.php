<?php

namespace App\Enums;

enum RecipeType: string
{
    case DESKTOP = 'desktop';
    case MOBILE = 'mobile';
    case ALL = 'all';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'Tous',
            self::DESKTOP => 'Desktop',
            self::MOBILE => 'Mobile',
        };
    }
}
