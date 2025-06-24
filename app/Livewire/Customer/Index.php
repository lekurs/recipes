<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination; // ← Utiliser le trait

    // Propriété réactive pour la recherche
    public $search = '';

    // Propriétés pour le tri
    public $sortBy = 'name';        // Colonne de tri par défaut
    public $sortDirection = 'asc';  // Direction par défaut

    // Propriétés pour l'ajout
    public $showAddForm = false;    // Afficher/masquer le formulaire
    public $newCustomerName = '';   // Nom du nouveau customer

    // Nombre d'éléments par page
    public $perPage = 5;

    // Action : Afficher le formulaire d'ajout
    public function openAddForm()
    {
        $this->showAddForm = true;
        $this->newCustomerName = ''; // Reset du champ
    }

    // Action : Masquer le formulaire d'ajout
    public function closeAddForm()
    {
        $this->showAddForm = false;
        $this->newCustomerName = '';
    }

    // Action : Masquer le formulaire d'ajout
    public function hideAddForm()
    {
        $this->showAddForm = false;
        $this->newCustomerName = '';
    }

    // Action : Créer un nouveau customer
    public function createCustomer()
    {
        // Validation simple
        if (empty($this->newCustomerName)) {
            session()->flash('error', "Le nom du customer est obligatoire !");
            return;
        }

        // Créer le customer
        Customer::create([
            'name' => $this->newCustomerName,
            'is_active' => true, // Actif par défaut
        ]);

        // Réinitialiser et fermer le formulaire
        $this->hideAddForm();

        // Message de succès
        session()->flash('message', "Customer '{$this->newCustomerName}' créé avec succès !");
    }
    public function sort($column)
    {
        // Si on clique sur la même colonne, inverser la direction
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nouvelle colonne, commencer par ASC
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
    public function toggleStatus($customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            session()->flash('error', "Customer introuvable !");
            return;
        }

        // Basculer le statut
        if ($customer->is_active) {
            $customer->deactivate();
            session()->flash('message', "Customer '{$customer->name}' désactivé.");
        } else {
            $customer->activate();
            session()->flash('message', "Customer '{$customer->name}' activé.");
        }
    }

    // Action : Supprimer un customer
    public function delete($customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            session()->flash('error', "Customer introuvable !");
            return;
        }

        // Vérifier s'il a des contacts
        if ($customer->contacts()->count() > 0) {
            session()->flash('error', "Impossible de supprimer ce customer car il a des contacts liés.");
            return;
        }

        // Supprimer le customer
        $customer->delete();

        // Message de confirmation
        session()->flash('message', "Customer '{$customer->name}' supprimé avec succès !");
    }

    public function render()
    {
        // Filtrer et trier les customers
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Passer les customers à la vue
        return view('livewire.customer.index', [
            'customers' => $customers
        ]);
    }
}
