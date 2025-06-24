<?php

namespace App\Enums;

enum AnswerStatus: string
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

    // Méthodes utiles pour les règles métier
    public function canBeSetByClient(): bool
    {
        return in_array($this, [
            self::IN_PROGRESS,
            self::COMPLETED,
            self::REJECTED
        ]);
    }

    public function canBeSetByAdmin(): bool
    {
        return in_array($this, [
            self::UPDATED,
            self::QUESTION
        ]);
    }

}
