<?php

namespace App\Livewire\Customer;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;
    public $activeTab = 'infos'; // Onglet actif par défaut

    public function mount($customerId)
    {
        $this->customer = Customer::with(['contacts', 'projects'])
            ->findOrFail($customerId);
    }

    // Action : Changer d'onglet
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    // Action : Toggle statut customer
    public function toggleStatus()
    {
        if ($this->customer->is_active) {
            $this->customer->deactivate();
            session()->flash('message', "Customer '{$this->customer->name}' désactivé.");
        } else {
            $this->customer->activate();
            session()->flash('message', "Customer '{$this->customer->name}' activé.");
        }

        // Recharger le customer
        $this->customer->refresh();
    }

    // Getter pour les projets en cours
    public function getOngoingProjectsProperty()
    {
        return $this->customer->ongoingProjects;
    }

    // Getter pour les projets terminés
    public function getCompletedProjectsProperty()
    {
        return $this->customer->completedProjects;
    }

    public function render()
    {
        return view('livewire.customer.show');
    }
}
