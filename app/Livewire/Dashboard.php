<?php

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Project;
use App\Traits\AuthHelper;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination, AuthHelper;

    public $selectedYear;
    public $search = '';
    public $perPage = 10;
    public $data = [];

    // Nouvelles propriétés pour le système hybride
    public $authMethod;
    public $currentContact;
    public $showCreateAccountBanner = false;

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->search = '';

        // Détecter le type d'authentification
        $this->authMethod = $this->getAuthMethod();
        $this->currentContact = $this->getCurrentContact();

        // Si c'est un accès client temporaire sans compte, proposer la création
        if ($this->isTempAccess() && $this->currentContact && !$this->currentContact->hasAccount()) {
            $this->showCreateAccountBanner = true;
        }

        $this->loadChartData();
    }

    public function updatedSelectedYear()
    {
        $this->loadChartData();
    }

    private function loadChartData()
    {
        $this->data = $this->getChartData($this->selectedYear);
    }

    public function render()
    {
        // Si c'est un accès client, montrer seulement ses données
        if ($this->isClientAccess()) {
            return $this->renderClientDashboard();
        }

        // Sinon, dashboard staff normal
        return $this->renderStaffDashboard();
    }

    /**
     * Dashboard pour les clients (connectés ou via URL signée)
     */
    private function renderClientDashboard()
    {
        $contact = $this->currentContact;
        $projects = $contact ? $contact->validProjects : collect();

        return view('livewire.client-dashboard', [
            'contact' => $contact,
            'projects' => $projects,
            'authMethod' => $this->authMethod,
            'showCreateAccountBanner' => $this->showCreateAccountBanner,
            'isTempAccess' => $this->isTempAccess(),
        ]);
    }

    /**
     * Dashboard normal pour les staff (admin/dev)
     */
    private function renderStaffDashboard()
    {
        // Statistiques générales
        $totalCustomers = Customer::count();
        $totalContacts = Contact::count();

        // Statistiques des projets par statut
        $projectStats = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            });

        // Projets en cours avec pagination et recherche
        $ongoingProjects = Project::where('status', ProjectStatus::ONGOING)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customers', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->with('customers')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalContacts' => $totalContacts,
            'projectStats' => $projectStats,
            'ongoingProjects' => $ongoingProjects,
            'availableYears' => $this->getAvailableYears(),
        ]);
    }

    private function getChartData(int $year)
    {
        $data = [];

        // Créer les données pour chaque mois
        for ($month = 1; $month <= 12; $month++) {
            // Créer une date pour le premier jour du mois
            $date = sprintf('%04d-%02d-01', $year, $month);

            // Compter les projets par statut pour ce mois
            $monthData = Project::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status->value => $item->count];
                });

            // Structure attendue par Flux Chart
            $data[] = [
                'date' => $date,
                'ongoing' => (int) ($monthData['on_going'] ?? 0),
                'completed' => (int) ($monthData['completed'] ?? 0),
                'paused' => (int) ($monthData['paused'] ?? 0),
                'cancelled' => (int) ($monthData['cancelled'] ?? 0),
            ];
        }

        return $data;
    }

    private function getAvailableYears()
    {
        $years = Project::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->toArray();

        return !empty($years) ? $years : [now()->year];
    }

    /**
     * Rediriger vers la création de compte
     */
    public function redirectToCreateAccount()
    {
        return redirect()->route('client.create-account');
    }

    /**
     * Masquer la bannière de création de compte
     */
    public function dismissCreateAccountBanner()
    {
        $this->showCreateAccountBanner = false;
    }
}
