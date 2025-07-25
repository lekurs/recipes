<?php

namespace App\Traits;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait AuthHelper
{
    /**
     * Récupérer le contact actuel (connecté ou via session temporaire)
     */
    protected function getCurrentContact(): ?Contact
    {
        // Si l'utilisateur est connecté et a un contact associé
        if (Auth::check()) {
            return Auth::user()->contact;
        }

        // Sinon, récupérer depuis les attributs de la requête (middleware HybridAuth)
        return request()->attributes->get('contact');
    }

    /**
     * Récupérer l'utilisateur actuel (seulement si connecté normalement)
     */
    protected function getCurrentUser(): ?User
    {
        return Auth::check() ? Auth::user() : null;
    }

    /**
     * Vérifier le type d'authentification actuel
     */
    protected function getAuthMethod(): string
    {
        if (Auth::check()) {
            return 'user_login';
        }

        return request()->attributes->get('auth_method', 'none');
    }

    /**
     * Vérifier si l'utilisateur actuel est staff (admin/dev)
     */
    protected function isStaff(): bool
    {
        $user = $this->getCurrentUser();
        return $user && $user->isStaff();
    }

    /**
     * Vérifier si c'est un accès client (connecté ou temporaire)
     */
    protected function isClientAccess(): bool
    {
        return !$this->isStaff() && $this->getCurrentContact() !== null;
    }

    /**
     * Vérifier si c'est un accès temporaire (via URL signée)
     */
    protected function isTempAccess(): bool
    {
        return in_array($this->getAuthMethod(), ['signed_url', 'temp_session']);
    }

    /**
     * Récupérer les projets accessibles pour le contact/utilisateur actuel
     */
    protected function getAccessibleProjects()
    {
        if ($this->isStaff()) {
            // Staff a accès à tous les projets
            return \App\Models\Project::all();
        }

        $contact = $this->getCurrentContact();
        if (!$contact) {
            return collect([]);
        }

        // Pour les clients, seulement les projets avec accès valide
        return $contact->validProjects;
    }
}
