<?php

namespace App\Livewire\Project;

use App\Models\Customer;
use Livewire\Component;

class Index extends Component
{
    public $projects;
    public $customer;
    public $search = '';

    public function mount(Customer $customer = null)
    {
        $this->customer = $customer;
    }

    public function render()
    {
        $query = \App\Models\Project::query()
            ->with(['customers', 'contacts.customer', 'validContacts']);

        if($this->customer) {
            $query->whereHas('customers', function ($q) {
                $q->where('id', $this->customer->id);
            });
        }

        if($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }


        $this->projects = $query->orderBy('created_at', 'desc')->get();

        return view('livewire.project.index');
    }
}
