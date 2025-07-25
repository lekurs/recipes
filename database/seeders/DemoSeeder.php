<?php

namespace Database\Seeders;

use App\Models\AnswerFile;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Recipe;
use App\Models\RecipeFile;
use App\Models\Answer;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Création du jeu de données démo...');

        // === ÉTAPE 1 : CRÉER LES USERS ADMIN/DEV ===
        $this->command->info('👥 Création des utilisateurs staff...');

        $admin = User::factory()->admin()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@demo.com',
        ]);

        $developer = User::factory()->developer()->create([
            'name' => 'Dev Principal',
            'email' => 'dev@demo.com',
        ]);

        // Quelques autres users staff
        $additionalDevs = User::factory()->developer()->count(2)->create();
        $additionalAdmins = User::factory()->admin()->count(1)->create();

        $allStaffUsers = collect([$admin, $developer])->merge($additionalDevs)->merge($additionalAdmins);

        // === ÉTAPE 2 : CRÉER LES CUSTOMERS ===
        $this->command->info('🏢 Création des entreprises clientes...');

        $techCustomers = Customer::factory()->tech()->count(4)->create();
        $traditionalCustomers = Customer::factory()->traditional()->count(3)->create();
        $regularCustomers = Customer::factory()->count(3)->create();

        $allCustomers = $techCustomers->merge($traditionalCustomers)->merge($regularCustomers);

        // === ÉTAPE 3 : CRÉER LES CONTACTS (MIX AVEC/SANS COMPTE) ===
        $this->command->info('📞 Création des contacts...');

        $allContacts = collect();
        $clientUsers = collect(); // Pour tracker les users clients créés

        $allCustomers->each(function ($customer) use (&$allContacts, &$clientUsers) {
            // Chaque customer a 2-4 contacts
            $contactCount = fake()->numberBetween(2, 4);

            // Premier contact = manager avec compte (70% de chance)
            $manager = Contact::factory()
                ->manager()
                ->frenchPhone()
                ->forCustomer($customer);

            if (fake()->boolean(70)) {
                $manager = $manager->withAccount()->create();
                $clientUsers->push($manager->user);
            } else {
                $manager = $manager->create();
            }

            $allContacts->push($manager);

            // Contacts additionnels (50% de chance d'avoir un compte)
            for ($i = 0; $i < $contactCount - 1; $i++) {
                $contact = Contact::factory()->forCustomer($customer);

                if (fake()->boolean(50)) {
                    $contact = $contact->withAccount()->create();
                    $clientUsers->push($contact->user);
                } else {
                    $contact = $contact->create();
                }

                $allContacts->push($contact);
            }

            // 40% de chance d'avoir un contact technique (30% avec compte)
            if (fake()->boolean(40)) {
                $techContact = Contact::factory()
                    ->technical()
                    ->frenchPhone()
                    ->forCustomer($customer);

                if (fake()->boolean(30)) {
                    $techContact = $techContact->withAccount()->create();
                    $clientUsers->push($techContact->user);
                } else {
                    $techContact = $techContact->create();
                }

                $allContacts->push($techContact);
            }
        });

        // === ÉTAPE 4 : CRÉER QUELQUES USERS CLIENTS SUPPLÉMENTAIRES (pour tester) ===
        $this->command->info('👤 Création d\'utilisateurs clients de test...');

        $testClientUser = User::factory()->client()->create([
            'name' => 'Client Test',
            'email' => 'client@demo.com',
        ]);
        $clientUsers->push($testClientUser);

        // Contact associé au client test
        $testContact = Contact::factory()
            ->manager()
            ->withUser($testClientUser)
            ->forCustomer($allCustomers->first())
            ->create();
        $allContacts->push($testContact);

        $allUsers = $allStaffUsers->merge($clientUsers);

        // === ÉTAPE 5 : CRÉER LES PROJETS AVEC RELATIONS ===
        $this->command->info('📂 Création des projets...');

        $projects = collect();

        // Projets variés
        $websiteProjects = Project::factory()->website()->count(5)->create();
        $mobileProjects = Project::factory()->mobileApp()->count(4)->create();
        $webAppProjects = Project::factory()->webApp()->count(3)->create();
        $urgentProjects = Project::factory()->urgent()->website()->withDetailedDescription()->count(2)->create();

        $projects = $projects
            ->merge($websiteProjects)
            ->merge($mobileProjects)
            ->merge($webAppProjects)
            ->merge($urgentProjects);

        // === ÉTAPE 6 : ASSIGNER CUSTOMERS ET CONTACTS AUX PROJETS ===
        $this->command->info('🔗 Création des relations projets...');

        $projects->each(function ($project) use ($allCustomers, $allContacts) {
            // 1-2 customers par projet
            $customerCount = fake()->boolean(85) ? 1 : 2;
            $projectCustomers = $allCustomers->random($customerCount);
            $project->customers()->attach($projectCustomers);

            // Contacts des customers assignés
            $projectContacts = collect();
            $projectCustomers->each(function ($customer) use (&$projectContacts) {
                $customerContacts = $customer->contacts;
                if ($customerContacts->isNotEmpty()) {
                    $contactCount = fake()->numberBetween(2, min(4, $customerContacts->count()));
                    $selectedContacts = $customerContacts->random($contactCount);
                    $projectContacts = $projectContacts->merge($selectedContacts);
                }
            });

            if ($projectContacts->isNotEmpty()) {
                $project->contacts()->attach($projectContacts->unique('id')->pluck('id'));
            }
        });

        // === ÉTAPE 7 : CRÉER LES RECIPES ===
        $this->command->info('📝 Création des recipes...');

        $allRecipes = collect();

        $projects->each(function ($project) use (&$allRecipes) {
            // 4-8 recipes par projet
            $recipeCount = fake()->numberBetween(4, 8);

            // Mix de types de recipes
            $bugs = Recipe::factory()->bug()->count(fake()->numberBetween(1, 2))->forProject($project)->create();
            $features = Recipe::factory()->feature()->withDetailedDescription()->count(fake()->numberBetween(1, 3))->forProject($project)->create();
            $uiux = Recipe::factory()->uiux()->count(fake()->numberBetween(0, 2))->forProject($project)->create();
            $mobile = Recipe::factory()->mobile()->count(fake()->numberBetween(1, 2))->forProject($project)->create();
            $desktop = Recipe::factory()->desktop()->count(fake()->numberBetween(1, 2))->forProject($project)->create();

            $projectRecipes = collect()
                ->merge($bugs)
                ->merge($features)
                ->merge($uiux)
                ->merge($mobile)
                ->merge($desktop);

            $allRecipes = $allRecipes->merge($projectRecipes);
        });

        // === ÉTAPE 8 : CRÉER LES FICHIERS ===
        $this->command->info('📁 Création des fichiers...');

        $allRecipes->each(function ($recipe) {
            $fileCount = fake()->numberBetween(1, 4); // 1-4 fichiers par recipe

            for ($i = 0; $i < $fileCount; $i++) {
                // Varier les types de fichiers
                $fileType = fake()->randomElement(['screenshot', 'photo', 'mobile', 'desktop']);

                match($fileType) {
                    'screenshot' => RecipeFile::factory()->screenshot()->forRecipe($recipe)->create(),
                    'photo' => RecipeFile::factory()->photo()->forRecipe($recipe)->create(),
                    'mobile' => RecipeFile::factory()->mobileScreenshot()->forRecipe($recipe)->create(),
                    'desktop' => RecipeFile::factory()->desktopScreenshot()->forRecipe($recipe)->create(),
                };
            }
        });

        // === ÉTAPE 9 : CRÉER LES RÉPONSES ===
        $this->command->info('💬 Création des réponses...');

        $allRecipes->each(function ($recipe) use ($allUsers) {
            // 60% des recipes ont au moins une réponse
            if (fake()->boolean(60)) {
                $answerCount = fake()->numberBetween(1, 3);

                for ($i = 0; $i < $answerCount; $i++) {
                    $answerType = fake()->randomElement(['pending', 'inProgress', 'updated', 'question', 'completed', 'rejected']);
                    $randomUser = $allUsers->random();

                    match($answerType) {
                        'pending' => Answer::factory()->pending()->forRecipe($recipe)->create(),
                        'inProgress' => Answer::factory()->inProgress()->byUser($randomUser)->forRecipe($recipe)->create(),
                        'updated' => Answer::factory()->updated()->byUser($randomUser)->forRecipe($recipe)->create(),
                        'question' => Answer::factory()->question()->byUser($randomUser)->forRecipe($recipe)->create(),
                        'completed' => Answer::factory()->completed()->byUser($randomUser)->forRecipe($recipe)->create(),
                        'rejected' => Answer::factory()->rejected()->byUser($randomUser)->forRecipe($recipe)->create(),
                    };
                }
            }
        });

        // === ÉTAPE 10 : CRÉER LES FICHIERS DE RÉPONSES ===
        $this->command->info('📁 Création des fichiers de réponses...');

        $allAnswers = Answer::all();
        $allAnswers->each(function ($answer) {
            $fileCount = fake()->numberBetween(1, 4); // 1-4 fichiers par recipe

            for ($i = 0; $i < $fileCount; $i++) {
                // Varier les types de fichiers
                $fileType = fake()->randomElement(['screenshot', 'photo', 'mobile', 'desktop']);

                match($fileType) {
                    'screenshot' => AnswerFile::factory()->screenshot()->forAnswer($answer)->create(),
                    'photo' => AnswerFile::factory()->photo()->forAnswer($answer)->create(),
                    'mobile' => AnswerFile::factory()->mobileScreenshot()->forAnswer($answer)->create(),
                    'desktop' => AnswerFile::factory()->desktopScreenshot()->forAnswer($answer)->create(),
                };
            }
        });

        // === STATISTIQUES FINALES ===
        $this->command->info('');
        $this->command->info('✅ Jeu de données créé avec succès !');
        $this->command->info('📊 Statistiques :');
        $this->command->info("   👥 Users Staff: {$allStaffUsers->count()}");
        $this->command->info("   👤 Users Clients: {$clientUsers->count()}");
        $this->command->info("   🏢 Customers: {$allCustomers->count()}");
        $this->command->info("   📞 Contacts: {$allContacts->count()}");
        $this->command->info("   📞 Contacts avec compte: " . $allContacts->where('user_id', '!=', null)->count());
        $this->command->info("   📞 Contacts sans compte: " . $allContacts->where('user_id', null)->count());
        $this->command->info("   📂 Projects: {$projects->count()}");
        $this->command->info("   📝 Recipes: {$allRecipes->count()}");
        $this->command->info("   📁 Files: " . RecipeFile::count());
        $this->command->info("   💬 Answers: " . Answer::count());
        $this->command->info('');
        $this->command->info('🎯 Comptes de test :');
        $this->command->info('   Admin: admin@demo.com');
        $this->command->info('   Dev: dev@demo.com');
        $this->command->info('   Client: client@demo.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('📝 Note : Certains contacts ont des comptes, d\'autres non.');
        $this->command->info('     Ceux sans compte utiliseront les URLs signées !');
    }
}
