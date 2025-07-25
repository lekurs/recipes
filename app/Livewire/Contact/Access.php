<?php

namespace App\Livewire\Contact;

use App\Models\Contact;
use App\Models\Project;
use Flux\Flux;
use Livewire\Component;

class Access extends Component
{
    public Project $project;
    public Contact $contact;
    public int $days = 30;

    public function mount(Project $project, Contact $contact)
    {
        $this->project = $project;
        $this->contact = $contact;
    }

    /**
     * Donner accès à un contact
     */
    public function grantAccess()
    {
        try {
            // Vérifier que le contact appartient bien à un client du projet
            $isValidContact = $this->project->customers()
                ->whereHas('contacts', function($query) {
                    $query->where('contacts.id', $this->contact->id);
                })->exists();

            if (!$isValidContact) {
                Flux::toast(
                    'Contact non autorisé pour ce projet.',
                    variant: 'error'
                );
                return;
            }

            // Calculer la date d'expiration
            $expiresAt = now()->addDays($this->days);

            // Générer un nouveau token
            $accessToken = \Str::random(64);

            // Mettre à jour ou créer l'association contact-projet
            $this->project->contacts()->syncWithoutDetaching([
                $this->contact->id => [
                    'access_token' => $accessToken,
                    'expires_at' => $expiresAt,
                    'is_active' => true,
                    'updated_at' => now()
                ]
            ]);

            // Émettre un événement pour le composant parent (pas de rechargement local)
            $this->dispatch('contact-access-updated', [
                'contact_id' => $this->contact->id,
                'refresh_needed' => true
            ])->to('project.show');

            // Émettre un événement pour fermer le formulaire
            $this->dispatch('access-action-completed');

            Flux::toast(
                "Accès accordé à {$this->contact->name} pour {$this->days} jour(s).",
                variant: 'success'
            );

            // IMPORTANT: Ne pas recharger le composant ici pour éviter les wire:id

        } catch (\Exception $e) {
            Flux::toast(
                'Erreur lors de l\'attribution de l\'accès : ' . $e->getMessage(),
                variant: 'error'
            );
        }
    }

    /**
     * Révoquer l'accès d'un contact
     */
    public function revokeAccess()
    {
        try {
            // Mettre à jour l'association pour désactiver l'accès
            $this->project->contacts()->updateExistingPivot($this->contact->id, [
                'access_token' => null,
                'expires_at' => null,
                'is_active' => false,
                'updated_at' => now()
            ]);

            // Émettre un événement pour le composant parent (pas de rechargement local)
            $this->dispatch('contact-access-updated', [
                'contact_id' => $this->contact->id,
                'refresh_needed' => true
            ])->to('project.show');

            // Émettre un événement pour fermer le formulaire
            $this->dispatch('access-action-completed');

            Flux::toast(
                "Accès révoqué pour {$this->contact->name}.",
                variant: 'success'
            );

            // IMPORTANT: Ne pas recharger le composant ici pour éviter les wire:id

        } catch (\Exception $e) {
            Flux::toast(
                'Erreur lors de la révocation de l\'accès : ' . $e->getMessage(),
                variant: 'error'
            );
        }
    }

    /**
     * Valider le nombre de jours
     */
    public function updatedDays()
    {
        if ($this->days < 1) {
            $this->days = 1;
        } elseif ($this->days > 365) {
            $this->days = 365;
        }
    }

    public function render()
    {
        // Recharger les données fraîches à chaque render
        $this->project->load('contacts');

        return view('livewire.contact.access');
    }
}
