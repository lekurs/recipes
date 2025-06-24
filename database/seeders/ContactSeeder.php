<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les customers existants
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            // Si pas de customers, on en crée quelques-uns d'abord
            $customers = Customer::factory()->count(3)->create();
        }

        // Pour chaque customer, créer 2-4 contacts
        $customers->each(function ($customer) {
            $contactCount = fake()->numberBetween(2, 4);

            // Premier contact : toujours un manager
            Contact::factory()
                ->manager()
                ->frenchPhone()
                ->forCustomer($customer)
                ->create();

            // Contacts suivants : mix technique/normal
            Contact::factory()
                ->count($contactCount - 1)
                ->forCustomer($customer)
                ->create([
                    // Ajouter un peu de variété
                    'job_area' => fake()->randomElement([
                        fake()->jobTitle(),
                        'Assistant',
                        'Responsable IT',
                        'Chef de Projet',
                        'Consultant',
                    ])
                ]);

            // 30% de chance d'avoir un contact technique
            if (fake()->boolean(30)) {
                Contact::factory()
                    ->technical()
                    ->frenchPhone()
                    ->forCustomer($customer)
                    ->create();
            }
        });

        // Quelques contacts "orphelins" (créent leurs propres customers)
        Contact::factory()
            ->manager()
            ->withoutPhone()
            ->count(2)
            ->create();

        // Un contact test spécifique
        Contact::factory()->create([
            'name' => 'Contact Test',
            'email' => 'contact@test.com',
            'job_area' => 'Testeur',
            'customer_id' => $customers->first()->id,
        ]);
    }
}
