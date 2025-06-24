<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 5 entreprises tech
        Customer::factory()->tech()->count(5)->create();

        // Créer 3 entreprises traditionnelles
        Customer::factory()->traditional()->count(3)->create();

        // Créer 2 entreprises random (noms génériques)
        Customer::factory()->count(2)->create();

        // Optionnel : Créer une entreprise spécifique pour les tests
        Customer::factory()->create([
            'name' => 'Mon Client Test'
        ]);
    }
}
