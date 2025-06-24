<?php

namespace App\Livewire\Project;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public bool $showProjectModal = false;
    public bool $deleteProjectModal = false;
    public bool $editProjectModal = false;

    public function render()
    {
        return view('livewire.project.show');
    }
}
