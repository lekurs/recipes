<?php

namespace App\Livewire\Contact;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SelectByClient extends Component
{
    use WithPagination;

    public Project $project;
// Listener pour rafraîchir quand un accès est mis à jour
    protected $listeners = [
        'contact-access-updated' => 'refreshContacts',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Rafraîchir les données des contacts
     */
    public function refreshContacts($data = null)
    {
        if ($data && $data['refresh_needed']) {
            // Recharger le projet avec ses relations
            $this->project->load('contacts');
        }
    }


    public function render()
    {
//        $contacts = Contact::query()
//            ->whereHas('customer', function ($query) {
//                $query->whereHas('projects', function ($query) {
//                    $query->where('projects.id', $this->project->id);
//                });
//            })
//            // Exclure les contacts qui ont déjà un accès ACTIF
//            ->whereDoesntHave('projects', function ($query) {
//                $query->where('projects.id', $this->project->id)
//                    ->where('contact_project.is_active', 1);
//            })
//            ->paginate(3);

        // Récupérer tous les contacts des clients de ce projet
        $contacts = \App\Models\Contact::whereHas('customer.projects', function($query) {
            $query->where('projects.id', $this->project->id);
        })->paginate(5);

        return view('livewire.contact.select-by-client', [
            'contacts' => $contacts,
        ]);
    }
}
