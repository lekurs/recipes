<?php

namespace App\Enums;

enum RecipeType: string
{
    case DESKTOP = 'desktop';
    case MOBILE = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::DESKTOP => 'Desktop',
            self::MOBILE => 'Mobile',
        };
    }
}
