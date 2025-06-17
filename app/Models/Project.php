<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_project', 'project_id', 'contact_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_project', 'project_id', 'customer_id');
    }
}
