<?php

namespace App\Enum;

enum Role: string
{
    case  ADMIN = 'admin';
    case CLIENT = 'client';
    case DEVELOPER = 'developer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::CLIENT => 'Client',
            self::DEVELOPER => 'Developer',
        };
    }
}
