<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case ONGOING = 'on_going';
    case COMPLETED = 'completed';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ONGOING => 'En cours',
            self::COMPLETED => 'Terminé',
            self::PAUSED => 'En pause',
            self::CANCELLED => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ONGOING => 'blue',
            self::COMPLETED => 'green',
            self::PAUSED => 'orange',
            self::CANCELLED => 'red',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::ONGOING => 1,
            self::PAUSED => 2,
            self::COMPLETED => 3,
            self::CANCELLED => 4,
        };
    }
}
