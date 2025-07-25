<?php

namespace App\Enums;

enum Role: string
{
    case  ADMIN = 'admin';
    case CLIENT = 'client';
    case DEVELOPER = 'developer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::CLIENT => 'Client',
            self::DEVELOPER => 'Developpeur',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'blue',
            self::CLIENT => 'teal',
            self::DEVELOPER => 'purple',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'shield-check',
            self::CLIENT => 'user',
            self::DEVELOPER => 'code-bracket',
        };
    }

    public function getAnswerCardClasses(): string
    {
        return match ($this) {
            self::ADMIN => 'bg-blue-50 border-l-4 border-blue-400 dark:bg-blue-900/20 dark:border-blue-500',
            self::CLIENT => 'bg-yellow-50 border-l-4 border-yellow-400 dark:bg-teal-900/20 dark:border-yellow-500',
            self::DEVELOPER => 'bg-purple-50 border-l-4 border-purple-400 dark:bg-purple-900/20 dark:border-purple-500',
        };
    }

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::ADMIN => 'primary',
            self::CLIENT => 'success',
            self::DEVELOPER => 'purple',
        };
    }

    public function getAvatarClasses(): string
    {
        return match ($this) {
            self::ADMIN => 'w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center',
            self::CLIENT => 'w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center',
            self::DEVELOPER => 'w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center',
        };
    }

    public function getIconClasses(): string
    {
        return match ($this) {
            self::ADMIN => 'w-4 h-4 text-blue-600',
            self::CLIENT => 'w-4 h-4 text-yellow-600',
            self::DEVELOPER => 'w-4 h-4 text-purple-600',
        };
    }

    public function getAnswerBackground(): string
    {
        return match ($this) {
            self::ADMIN => 'bg-blue-200',
            self::CLIENT => 'bg-yellow-200',
            self::DEVELOPER => 'bg-purple-200',
        };
    }

    public function getBorderBackground(): string
    {
        return match ($this) {
            self::ADMIN => 'border-blue-300 bg-blue-200',
            self::CLIENT => 'border-yellow-300 bg-yellow-200',
            self::DEVELOPER => 'border-purple-300 bg-purple-200',
        };
    }
}
