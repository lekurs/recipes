<?php

namespace App\Livewire\Contact;

use App\Models\Customer;
use App\Models\Project;
use Flux\Flux;
use Livewire\Component;

class Create extends Component
{
    public Project $project;
    public Customer $customer;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $job_area = '';

    public function createContactWithProject()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'job_area' => 'nullable|string|max:255',
        ]);

        $contact = $this->customer->contacts()->create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'job_area' => $this->job_area,
            'customer_id' => $this->customer->id,
        ]);

        // Associer le contact au projet
        $this->project->contacts()->attach($contact->id, ['is_active' => true]);

        // Réinitialiser les champs du formulaire
        $this->reset(['name', 'email', 'phone', 'job_area']);

        // Émettre un événement pour rafraîchir la liste des contacts
        $this->dispatch('contact-created', ['contact' => $contact, 'refresh_needed' => true]);
        session()->flash('message', 'Contact créé avec succès et associé au projet.');

        // Fermer le modal si nécessaire
        Flux::modal('create-contact')->close();
    }

    public function render()
    {
        return view('livewire.contact.create');
    }
}
