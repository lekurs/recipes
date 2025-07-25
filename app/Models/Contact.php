<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_area',
        'customer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAccount(): bool
    {
        return !is_null($this->user_id);
    }

    public function createAccount(string $password): User
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($password),
            'email_verified_at' => now(),
            'role' => Role::CLIENT,
        ]);

        $this->update(['user_id' => $user->id]);

        return $user;
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeProjects()
    {
        return $this->belongsToMany(Project::class)
            ->wherePivot('is_active', true)
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Projets avec accès valide (actif + non expiré)
     */
    public function validProjects()
    {
        return $this->belongsToMany(Project::class)
            ->wherePivot('is_active', true)
            ->where(function ($query) {
                $query->whereNull('contact_project.expires_at')
                    ->orWhere('contact_project.expires_at', '>', now());
            })
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'role' => Role::class,
        ];
    }
}
