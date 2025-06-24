<?php

namespace App\Livewire\Contact;

use App\Models\Contact;
use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;
    public ?Contact $selectedContact = null;
    public bool $showContactModal = false;
    public bool $showDeleteModal = false;
    public bool $showEditModal = false;

    public string $search = '';

    // Propriétés pour le formulaire
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $job_area = '';

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function render()
    {
        $contacts = Contact::where('customer_id', $this->customer->id)  // ← Contacts du client uniquement
        ->when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
//                ->orWhere('email', 'like', '%' . $this->search . '%')  // ← Bonus: recherche aussi par email
//                ->orWhere('job_area', 'like', '%' . $this->search . '%'); // ← Et par poste
        })
        ->get();

        dump($this->customer->id);
        dump($contacts);

        return view('livewire.contact.show', [
            'contacts' => $contacts
        ]);
    }

    // Méthode pour afficher un contact
    public function showContact($contactId)
    {
        $this->selectedContact = Contact::find($contactId);
        $this->showContactModal = true;
    }

    public function closeModal()
    {
        // Ferme toutes les modales
        $this->showContactModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;

        // Nettoie tout
        $this->selectedContact = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->job_area = '';
    }

    public function createContact()
    {
        // On vide le formulaire
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->job_area = '';

        // On remet selectedContact à null (mode création)
        $this->selectedContact = null;

        // On ouvre la modale d'édition
        $this->showEditModal = true;
    }

    public function editContact($contactId)
    {
        $contact = Contact::find($contactId);

        //on remplit les propriétés du formulaire avec les données du contact
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->phone = $contact->phone;
        $this->job_area = $contact->job_area;

        // On stocke l'ID du contact à modifier
        $this->selectedContact = $contact;

        // On affiche le modal d'édition
        $this->showEditModal = true;
    }

    public function deleteContact($contactId)
    {
        $contact = Contact::find($contactId);
        $contactName = $contact->name;
        $contact->delete();

        session()->flash('message', "Contact '{$contactName}' supprimé avec succès !");

    }

    public function saveContact()
    {
        // Validation des données
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'job_area' => 'nullable|string|max:255',
        ]);

        if ($this->selectedContact) {
            // MODE ÉDITION : On modifie le contact existant
            $this->selectedContact->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'job_area' => $this->job_area ?: null,
            ]);

            session()->flash('message', 'Contact modifié avec succès !');
        } else {
            // MODE CRÉATION : On crée un nouveau contact
            $this->customer->contacts()->create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'job_area' => $this->job_area ?: null,
            ]);

            session()->flash('message', 'Contact créé avec succès !');
        }

        // On ferme la modale et nettoie
        $this->closeModal();
    }
}
