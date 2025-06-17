<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'customer_contact', 'customer_id', 'contact_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'customer_project', 'customer_id', 'project_id');
    }
}
