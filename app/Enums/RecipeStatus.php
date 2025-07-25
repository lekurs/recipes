<?php

namespace App\Enums;

enum RecipeStatus: string
{
    case PENDING = 'pending';           // État initial (pas encore de réponse)
    case IN_PROGRESS = 'in_progress';   // Client a vu/répondu
    case UPDATED = 'updated';           // Dev/Admin a donné une mise à jour
    case QUESTION = 'question';         // Dev/Admin pose une question
    case COMPLETED = 'completed';       // SEUL le client peut mettre ça
    case REJECTED = 'rejected';         // Client rejette la solution

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::IN_PROGRESS => 'En cours',
            self::UPDATED => 'Mis à jour',
            self::QUESTION => 'Question posée',
            self::COMPLETED => 'Terminé',
            self::REJECTED => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-gray-100 text-gray-800',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::UPDATED => 'bg-yellow-100 text-yellow-800',
            self::QUESTION => 'bg-orange-100 text-orange-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::REJECTED => 'bg-red-100 text-red-800',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'blue',
            self::UPDATED => 'yellow',
            self::QUESTION => 'orange',
            self::COMPLETED => 'green',
            self::REJECTED => 'red'
        };
    }

    /**
     * Retourne les statuts visibles selon le rôle de l'utilisateur
     */
    public static function getVisibleCasesForRole(string $role): array
    {
        $allCases = self::cases();

        // Normalisation du rôle en lowercase pour éviter les problèmes de casse
        $normalizedRole = strtolower(trim($role));

        return array_filter($allCases, function ($status) use ($normalizedRole, $allCases) {
            return match ($normalizedRole) {
                'admin', 'developer' => $status->value !== 'completed',
                'client' => $status->value !== 'rejected',
                default => $allCases,
            };
        });
    }
}
