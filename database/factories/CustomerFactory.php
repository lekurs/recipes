<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'name' => fake()->company(),
        ];
    }

    /**
     * Indicate that the customer is a tech company.
     */
    public function tech(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'TechFlow Solutions',
                'Digital Nexus',
                'InnovateLab',
                'ByteCraft',
                'CodeForge Industries',
                'DataStream Corp',
                'CloudPeak Technologies',
                'SmartBridge Solutions',
                'NeuralNet Systems',
                'QuantumLeap Software'
            ]),
        ]);
    }

    /**
     * Indicate that the customer is a traditional business.
     */
    public function traditional(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'Cabinet Martin & Associés',
                'Boulangerie des Délices',
                'Garage AutoExpert',
                'Pharmacie du Centre',
                'Restaurant Le Petit Bistrot',
                'Librairie des Arts',
                'Coiffure Élégance',
                'Immobilier Prestige',
                'Avocat & Partners',
                'Comptabilité Excellence'
            ]),
        ]);
    }

    /**
     * Indicate that the customer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
