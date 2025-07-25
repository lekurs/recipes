<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class SignedUrlGenerator
{
    /**
     * Générer une URL signée pour l'accès d'un contact au dashboard
     */
    public function generateDashboardAccess(Contact $contact, int $daysValid = 30): string
    {
        $expiration = now()->addDays($daysValid);

        return URL::temporarySignedRoute(
            'client.dashboard', // Route vers le dashboard client
            $expiration,
            ['contact_id' => $contact->id]
        );
    }

    /**
     * Générer une URL signée pour l'accès d'un contact à un projet spécifique
     */
    public function generateProjectAccess(Contact $contact, int $projectId, int $daysValid = 30): string
    {
        $expiration = now()->addDays($daysValid);

        return URL::temporarySignedRoute(
            'client.project.show',
            $expiration,
            [
                'contact_id' => $contact->id,
                'project_id' => $projectId
            ]
        );
    }

    /**
     * Générer une URL signée générique avec paramètres personnalisés
     */
    public function generateCustomAccess(string $routeName, array $parameters, int $daysValid = 30): string
    {
        $expiration = now()->addDays($daysValid);

        return URL::temporarySignedRoute(
            $routeName,
            $expiration,
            $parameters
        );
    }

    /**
     * Vérifier si une URL signée est encore valide
     */
    public function isUrlValid(string $signedUrl): bool
    {
        try {
            // Extraire les paramètres de l'URL
            $parsedUrl = parse_url($signedUrl);
            parse_str($parsedUrl['query'] ?? '', $queryParams);

            // Vérifier l'expiration
            if (isset($queryParams['expires'])) {
                $expiresAt = Carbon::createFromTimestamp($queryParams['expires']);
                return now()->isBefore($expiresAt);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Générer plusieurs URLs pour tous les projets d'un contact
     */
    public function generateAllProjectAccess(Contact $contact, int $daysValid = 30): array
    {
        $urls = [];

        // URL dashboard principal
        $urls['dashboard'] = $this->generateDashboardAccess($contact, $daysValid);

        // URLs pour chaque projet accessible
        $contact->validProjects->each(function ($project) use ($contact, $daysValid, &$urls) {
            $urls['project_' . $project->id] = $this->generateProjectAccess($contact, $project->id, $daysValid);
        });

        return $urls;
    }

    /**
     * Générer un email avec les URLs d'accès pour un contact
     */
    public function generateAccessEmail(Contact $contact, int $daysValid = 30): array
    {
        $dashboardUrl = $this->generateDashboardAccess($contact, $daysValid);
        $projectCount = $contact->validProjects->count();

        return [
            'contact' => $contact,
            'dashboard_url' => $dashboardUrl,
            'project_count' => $projectCount,
            'expires_at' => now()->addDays($daysValid),
            'days_valid' => $daysValid,
        ];
    }
}
