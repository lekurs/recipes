<?php

namespace App\Models;

use App\Enum\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_area',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_contact', 'contact_id', 'customer_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'contact_project', 'contact_id', 'project_id');
    }

    protected function casts(): array
    {
        return [
            'role' => Role::class,
        ];
    }
}
