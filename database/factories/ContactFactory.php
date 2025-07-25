<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'job_area' => fake()->jobTitle(),
            'customer_id' => Customer::factory(),
            'user_id' => null, // Initialement sans compte utilisateur
        ];
    }

    public function withAccount(): static
    {
        return $this->afterCreating(function (Contact $contact) {
            $user = User::factory()->client()->create([
                'name' => $contact->name,
                'email' => $contact->email,
            ]);

            $contact->update(['user_id' => $user->id]);
        });
    }

    public function withUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $user->name,
            'email' => $user->email,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Attach this contact to a project with a random token status
     */
    public function withProjectTokens(): static
    {
        return $this->afterCreating(function (Contact $contact) {
            // Créer 1-3 projets liés avec des tokens
            $projectsCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $projectsCount; $i++) {
                $project = Project::factory()->create();

                // 70% de chance d'avoir un token actif
                $hasActiveToken = fake()->boolean(70);

                if ($hasActiveToken) {
                    // Token actif avec expiration future
                    $contact->projects()->attach($project->id, [
                        'access_token' => \Str::random(64),
                        'expires_at' => now()->addDays(fake()->numberBetween(1, 365)),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Token inactif ou expiré
                    $contact->projects()->attach($project->id, [
                        'access_token' => fake()->boolean(60) ? \Str::random(64) : null,
                        'expires_at' => fake()->boolean(50) ? now()->subDays(fake()->numberBetween(1, 100)) : null,
                        'is_active' => fake()->boolean(30), // 30% de chance d'être actif même si expiré
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    /**
     * Indicate that the contact is a CEO/Manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_area' => fake()->randomElement([
                'CEO',
                'Directeur Général',
                'Manager',
                'Chef de Projet',
                'Responsable Marketing',
                'Directeur Commercial',
                'Chef d\'équipe'
            ]),
        ]);
    }

    /**
     * Indicate that the contact is a technical person.
     */
    public function technical(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_area' => fake()->randomElement([
                'Développeur',
                'Lead Developer',
                'CTO',
                'Architecte Logiciel',
                'DevOps Engineer',
                'Product Owner',
                'Tech Lead',
                'Administrateur Système'
            ]),
        ]);
    }

    /**
     * Contact for a specific customer.
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $customer->id,
        ]);
    }

    /**
     * Contact without phone number.
     */
    public function withoutPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => null,
        ]);
    }

    /**
     * Contact with French phone number format.
     */
    public function frenchPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => fake()->randomElement([
                '06 12 34 56 78',
                '07 98 76 54 32',
                '01 23 45 67 89',
                '+33 6 11 22 33 44',
                '09 87 65 43 21'
            ]),
        ]);
    }
}
