<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Customer;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer qu'on a des customers et contacts
        $customers = Customer::all();
        $contacts = Contact::all();

        if ($customers->isEmpty()) {
            $customers = Customer::factory()->count(5)->create();
        }

        if ($contacts->isEmpty()) {
            $contacts = Contact::factory()->count(10)->create();
        }

        // Créer différents types de projets

        // === PROJETS SITES WEB ===
        $websiteProjects = Project::factory()
            ->website()
            ->count(4)
            ->create();

        // === PROJETS MOBILE APPS ===
        $mobileProjects = Project::factory()
            ->mobileApp()
            ->count(3)
            ->create();

        // === PROJETS WEB APPS ===
        $webAppProjects = Project::factory()
            ->webApp()
            ->count(3)
            ->create();

        // === PROJETS URGENTS ===
        $urgentProjects = Project::factory()
            ->urgent()
            ->website()
            ->withDetailedDescription()
            ->count(2)
            ->create();

        // === PROJETS SANS DESCRIPTION ===
        $simpleProjects = Project::factory()
            ->withoutDescription()
            ->count(2)
            ->create();

        // === CRÉER LES RELATIONS ===

        // Collecter tous les projets
        $allProjects = collect()
            ->merge($websiteProjects)
            ->merge($mobileProjects)
            ->merge($webAppProjects)
            ->merge($urgentProjects)
            ->merge($simpleProjects);

        // Pour chaque projet, assigner des customers et contacts
        $allProjects->each(function ($project) use ($customers, $contacts) {

            // === RELATION CUSTOMER-PROJECT ===
            // 1-2 customers par projet (80% un seul, 20% deux)
            $customerCount = fake()->boolean(80) ? 1 : 2;
            $projectCustomers = $customers->random($customerCount);
            $project->customers()->attach($projectCustomers);

            // === RELATION CONTACT-PROJECT ===
            // Pour chaque customer du projet, prendre 2-4 de ses contacts
            $projectContacts = collect();

            $projectCustomers->each(function ($customer) use (&$projectContacts) {
                $customerContacts = $customer->contacts;
                if ($customerContacts->isNotEmpty()) {
                    $contactCount = fake()->numberBetween(2, min(4, $customerContacts->count()));
                    $selectedContacts = $customerContacts->random($contactCount);
                    $projectContacts = $projectContacts->merge($selectedContacts);
                }
            });

            // Attacher les contacts au projet (éviter les doublons)
            if ($projectContacts->isNotEmpty()) {
                $project->contacts()->attach($projectContacts->unique('id')->pluck('id'));
            }
        });

        // === PROJET TEST SPÉCIFIQUE ===
        $testProject = Project::factory()->create([
            'name' => 'Projet Test',
            'description' => 'Projet de test pour le développement',
        ]);

        // Lui assigner le premier customer et ses contacts
        $firstCustomer = $customers->first();
        $testProject->customers()->attach($firstCustomer);
        $testProject->contacts()->attach($firstCustomer->contacts->pluck('id'));
    }
}
