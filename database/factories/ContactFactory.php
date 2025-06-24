<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
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
        ];
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
