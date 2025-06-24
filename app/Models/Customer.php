<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function getStatusBadgeColor(): string
    {
        return $this->is_active ? 'green' : 'red';
    }

    public function getStatusLabel(): string
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function ongoingProjects()
    {
        return $this->projects()->where('projects.status', ProjectStatus::ONGOING);
    }

    public function completedProjects()
    {
        return $this->projects()->where('projects.status', ProjectStatus::COMPLETED);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'customer_project', 'customer_id', 'project_id');
    }
}
