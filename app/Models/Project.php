<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
        ];
    }

    public function getStatusBadgeColor(): string
    {
        return $this->status->color();
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class)
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    // Relation avec seulement les contacts actifs
    public function activeContacts()
    {
        return $this->belongsToMany(Contact::class)
            ->wherePivot('is_active', true)
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Contacts avec accès expiré
     */
    public function expiredContacts()
    {
        return $this->belongsToMany(Contact::class)
            ->wherePivot('expires_at', '<', now())
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Contacts avec accès valide (actif + non expiré)
     */
    public function validContacts()
    {
        return $this->belongsToMany(Contact::class)
            ->wherePivot('is_active', true)
            ->where(function ($query) {
                $query->whereNull('contact_project.expires_at')
                    ->orWhere('contact_project.expires_at', '>', now());
            })
            ->withPivot(['access_token', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Donner accès à un contact avec durée optionnelle
     */
    public function giveAccessToContact(Contact $contact, int $daysValid = null)
    {
        $accessToken = Str::random(32);
        $expiresAt = $daysValid ? now()->addDays($daysValid) : null;

        $this->contacts()->syncWithoutDetaching([
            $contact->id => [
                'access_token' => $accessToken,
                'expires_at' => $expiresAt,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        return $accessToken;
    }

    /**
     * Révoquer l'accès d'un contact
     */
    public function revokeAccessFromContact(Contact $contact)
    {
        $this->contacts()->updateExistingPivot($contact->id, [
            'is_active' => false,
            'updated_at' => now(),
        ]);
    }

    /**
     * Prolonger l'accès d'un contact
     */
    public function extendContactAccess(Contact $contact, int $additionalDays)
    {
        $currentExpiry = $this->contacts()
            ->where('contact_id', $contact->id)
            ->first()
            ->pivot
            ->expires_at;

        $newExpiry = $currentExpiry
            ? Carbon::parse($currentExpiry)->addDays($additionalDays)
            : now()->addDays($additionalDays);

        $this->contacts()->updateExistingPivot($contact->id, [
            'expires_at' => $newExpiry,
            'updated_at' => now(),
        ]);
    }

    /**
     * Vérifier si un contact a un accès valide
     */
    public function hasValidAccess(Contact $contact): bool
    {
        $projectContact = $this->contacts()->where('contact_id', $contact->id)->first();

        if (!$projectContact) {
            return false;
        }

        $pivot = $projectContact->pivot;

        // Vérifier si l'accès est actif
        if (!$pivot->is_active) {
            return false;
        }

        // Vérifier si l'accès n'a pas expiré
        if ($pivot->expires_at && \Carbon\Carbon::parse($pivot->expires_at)->isPast()) {
            return false;
        }

        return true;
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_project', 'project_id', 'customer_id');
    }

    public function recipes(): HasMany
    {
        return  $this->hasMany(Recipe::class);
    }
}
