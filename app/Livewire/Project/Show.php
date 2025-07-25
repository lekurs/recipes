<?php

namespace App\Livewire\Project;

use App\Enums\ProjectStatus;
use App\Enums\RecipeStatus;
use App\Enums\RecipeType;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Recipe;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Show extends Component
{
    use WithFileUploads;
    use WithPagination;

    public Project $project;
    public string $activeTab = 'informations';
    public string $selectedType = 'all';
    public string $selectedStatus = 'all';
    public string $projectStatus = '';
    public ?int $selectedRecipeId = null;

    // Écouteurs d'événements optimisés
    protected $listeners = [
        'recipe-created' => 'refreshProject',
        'contact-created' => 'refreshProjectContacts',
        'contact-access-updated' => 'handleContactAccessUpdate',
    ];

    public function mount($project)
    {
        // Logique de chargement du projet optimisée
        if (is_numeric($project)) {
            $this->project = Project::with([
                'customers.contacts',
                'contacts.customer',
                'recipes.answers',
                'recipes' => function ($query) {
                    $query->with(['answers'])
                        ->orderBy('created_at', 'asc');
                }
            ])->findOrFail($project);
        } else {
            $this->project = Project::with([
                'customers.contacts',
                'contacts.customer',
                'recipes.answers',
                'recipes' => function ($query) {
                    $query->with(['answers'])
                        ->orderBy('created_at', 'asc');
                }
            ])->findOrFail($project->id ?? $project);
        }

        $this->projectStatus = $this->project->status->value;
    }

    public function updatedProjectStatus()
    {
        try {
            // Valider que le statut existe
            $newStatus = ProjectStatus::from($this->projectStatus);

            // Mettre à jour en base
            Project::query()->where('id', $this->project->id)
                ->update(['status' => $this->projectStatus]);

            // Mettre à jour le modèle local
            $this->project->status = $newStatus;

            Flux::toast('Statut du projet mis à jour', variant: 'success');

        } catch (\ValueError $e) {
            // Si la valeur n'est pas valide, reset à l'ancienne valeur
            $this->projectStatus = $this->project->status->value;
            Flux::toast('Statut de projet invalide', variant: 'error');
        }
    }

    /**
     * Gérer les mises à jour d'accès des contacts - Version optimisée
     */
    public function handleContactAccessUpdate($data = null)
    {
        if ($data && $data['refresh_needed']) {
            // Recharger seulement les relations nécessaires
            $this->project->load([
                'customers.contacts',
                'contacts.customer'
            ]);

            // Clear computed properties cache
            unset($this->contactsStats);
        }
    }

    /**
     * Méthode appelée quand une recette est créée
     */
    public function refreshProject($data = null)
    {
        if($data && $data['refresh_needed']) {
            $this->project->refresh();
            $this->project->load('recipes.answers');
        }
    }

    /**
     * Alias pour la compatibilité
     */
    public function refreshProjectContacts($data = null)
    {
        $this->handleContactAccessUpdate($data);
    }

    public function setSelectedType(string $type)
    {
        $this->selectedType = $type;
    }

    public function setSelectedStatus(string $status)
    {
        $this->selectedStatus = $status;
    }

    public function getRecipeTypesProperty()
    {
        $types = ['all' => 'Tous les types'];

        foreach (RecipeType::cases() as $type) {
            $types[$type->value] = $type->label();
        }

        return $types;
    }

    public function getRecipeStatusesProperty()
    {
        // Ordre personnalisé des statuts
        $orderedStatuses = [
            RecipeStatus::IN_PROGRESS,
            RecipeStatus::UPDATED,
            RecipeStatus::QUESTION,
            RecipeStatus::PENDING,
            RecipeStatus::COMPLETED,
            RecipeStatus::REJECTED,
        ];

        $statuses = ['all' => 'Tous les statuts'];

        foreach ($orderedStatuses as $status) {
            $statuses[$status->value] = $status->label();
        }

        return $statuses;
    }

    public function getFilteredRecipesProperty()
    {
        $recipes = $this->project->recipes()->with(['answers'])->get();

        // Filtre par type
        if ($this->selectedType !== 'all') {
            $recipes = $recipes->filter(function ($recipe) {
                return $recipe->type->value === $this->selectedType;
            });
        }

        // Filtre par statut
        if ($this->selectedStatus !== 'all') {
            $recipes = $recipes->filter(function ($recipe) {
                return $recipe->status->value === $this->selectedStatus;
            });
        }

        return $recipes;
    }

    // Méthodes pour les statistiques du tab "informations"
    public function getRecipesStatsProperty()
    {
        $recipes = $this->project->recipes;

        return [
            'en_cours' => $recipes->where('status', RecipeStatus::IN_PROGRESS)->count(),
            'terminees' => $recipes->where('status', RecipeStatus::COMPLETED)->count(),
            'total' => $recipes->count(),
        ];
    }

    public function getContactsStatsProperty()
    {
        $allProjectContacts = $this->project->contacts;

        return [
            'avec_token' => $allProjectContacts->filter(function ($contact) {
                return $contact->pivot->access_token !== null && $contact->pivot->is_active;
            })->count(),
            'sans_token' => $allProjectContacts->filter(function ($contact) {
                return $contact->pivot->access_token === null || !$contact->pivot->is_active;
            })->count(),
            'total' => $allProjectContacts->count(),
            'possibles_a_ajouter' => $this->project->customers->sum(function ($customer) {
                return $customer->contacts->whereNotIn('id', $this->project->contacts->pluck('id'))->count();
            }),
        ];
    }

    public function getLatestRecipesProperty()
    {
        return $this->project->recipes()
            ->with(['answers'])
            ->whereNotIn('status', [RecipeStatus::COMPLETED, RecipeStatus::REJECTED])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function getContactByCustomer(Customer $customer)
    {
        $customer->load('contacts');

        return $customer->contacts()
            ->paginate(5)
            ->withQueryString();
    }

    public function render()
    {
        return view('livewire.project.show')
            ->title('Projet: ' . $this->project->name);
    }
}
