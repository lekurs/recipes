<?php

namespace App\Http\Middleware;

use App\Models\Contact;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HybridAuth
{
    /**
     * Handle an incoming request.
     *
     * Gère 3 types d'authentification :
     * 1. User connecté normalement (session classique)
     * 2. URL signée valide (accès temporaire direct)
     * 3. Session temporaire active (navigation après URL signée)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // === MÉTHODE 1 : User connecté normalement ===
        if (Auth::check()) {
            // L'utilisateur est connecté, on continue
            return $next($request);
        }

        // === MÉTHODE 2 : URL signée valide ===
        if ($this->hasValidSignedUrl($request)) {
            $contact = $this->getContactFromSignedUrl($request);

            if ($contact) {
                // Créer une session temporaire pour ce contact
                session(['temp_contact_id' => $contact->id, 'temp_contact_expires' => now()->addHours(2)]);

                // Ajouter le contact à la requête pour y accéder facilement
                $request->attributes->set('contact', $contact);
                $request->attributes->set('auth_method', 'signed_url');

                return $next($request);
            }
        }

        // === MÉTHODE 3 : Session temporaire active ===
        if ($this->hasTempSession()) {
            $contact = $this->getContactFromTempSession();

            if ($contact) {
                // Ajouter le contact à la requête
                $request->attributes->set('contact', $contact);
                $request->attributes->set('auth_method', 'temp_session');

                return $next($request);
            } else {
                // Session temporaire expirée ou contact inexistant
                $this->clearTempSession();
            }
        }

        // === AUCUNE AUTHENTIFICATION VALIDE ===
        // Redirect vers login ou page d'erreur selon le contexte
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        // Pour les routes web, rediriger vers login avec message explicatif
        return redirect()->route('login')->with('error', 'Accès non autorisé. Connectez-vous ou utilisez votre lien d\'accès.');
    }

    /**
     * Vérifier si la requête a une URL signée valide
     */
    private function hasValidSignedUrl(Request $request): bool
    {
        return $request->hasValidSignature() && !$request->signatureHasExpired();
    }

    /**
     * Récupérer le contact depuis l'URL signée
     */
    private function getContactFromSignedUrl(Request $request): ?Contact
    {
        // On s'attend à avoir contact_id dans l'URL ou les paramètres
        $contactId = $request->route('contact_id') ?? $request->get('contact_id');

        if (!$contactId) {
            return null;
        }

        return Contact::find($contactId);
    }

    /**
     * Vérifier si on a une session temporaire active
     */
    private function hasTempSession(): bool
    {
        if (!session()->has('temp_contact_id') || !session()->has('temp_contact_expires')) {
            return false;
        }

        // Vérifier que la session n'a pas expiré
        $expiresAt = session('temp_contact_expires');
        return now()->isBefore($expiresAt);
    }

    /**
     * Récupérer le contact depuis la session temporaire
     */
    private function getContactFromTempSession(): ?Contact
    {
        $contactId = session('temp_contact_id');

        if (!$contactId) {
            return null;
        }

        return Contact::find($contactId);
    }

    /**
     * Nettoyer la session temporaire
     */
    private function clearTempSession(): void
    {
        session()->forget(['temp_contact_id', 'temp_contact_expires']);
    }
}
