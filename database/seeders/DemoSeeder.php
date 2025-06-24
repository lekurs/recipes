<?php

namespace Database\Seeders;

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
        $this->command->info('👥 Création des utilisateurs...');

        $admin = User::factory()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@demo.com',
            'role' => Role::ADMIN,
        ]);

        $developer = User::factory()->create([
            'name' => 'Dev Principal',
            'email' => 'dev@demo.com',
            'role' => Role::DEVELOPER,
        ]);

        // Quelques autres users
        $additionalDevs = User::factory()->count(2)->create(['role' => Role::DEVELOPER]);
        $additionalAdmins = User::factory()->count(1)->create(['role' => Role::ADMIN]);

        $allUsers = collect([$admin, $developer])->merge($additionalDevs)->merge($additionalAdmins);

        // === ÉTAPE 2 : CRÉER LES CUSTOMERS ===
        $this->command->info('🏢 Création des entreprises clientes...');

        $techCustomers = Customer::factory()->tech()->count(4)->create();
        $traditionalCustomers = Customer::factory()->traditional()->count(3)->create();
        $regularCustomers = Customer::factory()->count(3)->create();

        $allCustomers = $techCustomers->merge($traditionalCustomers)->merge($regularCustomers);

        // === ÉTAPE 3 : CRÉER LES CONTACTS ===
        $this->command->info('📞 Création des contacts...');

        $allContacts = collect();

        $allCustomers->each(function ($customer) use (&$allContacts) {
            // Chaque customer a 2-4 contacts
            $contactCount = fake()->numberBetween(2, 4);

            // Premier contact = manager
            $manager = Contact::factory()
                ->manager()
                ->frenchPhone()
                ->forCustomer($customer)
                ->create();

            $allContacts->push($manager);

            // Contacts additionnels
            $additionalContacts = Contact::factory()
                ->count($contactCount - 1)
                ->forCustomer($customer)
                ->create();

            $allContacts = $allContacts->merge($additionalContacts);

            // 40% de chance d'avoir un contact technique
            if (fake()->boolean(40)) {
                $techContact = Contact::factory()
                    ->technical()
                    ->frenchPhone()
                    ->forCustomer($customer)
                    ->create();

                $allContacts->push($techContact);
            }
        });

        // === ÉTAPE 4 : CRÉER LES PROJETS AVEC RELATIONS ===
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

        // === ÉTAPE 5 : ASSIGNER CUSTOMERS ET CONTACTS AUX PROJETS ===
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

        // === ÉTAPE 6 : CRÉER LES RECIPES ===
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

        // === ÉTAPE 7 : CRÉER LES FICHIERS ===
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

        // === ÉTAPE 8 : CRÉER LES RÉPONSES ===
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

        // === STATISTIQUES FINALES ===
        $this->command->info('');
        $this->command->info('✅ Jeu de données créé avec succès !');
        $this->command->info('📊 Statistiques :');
        $this->command->info("   👥 Users: {$allUsers->count()}");
        $this->command->info("   🏢 Customers: {$allCustomers->count()}");
        $this->command->info("   📞 Contacts: {$allContacts->count()}");
        $this->command->info("   📂 Projects: {$projects->count()}");
        $this->command->info("   📝 Recipes: {$allRecipes->count()}");
        $this->command->info("   📁 Files: " . RecipeFile::count());
        $this->command->info("   💬 Answers: " . Answer::count());
        $this->command->info('');
        $this->command->info('🎯 Comptes de test :');
        $this->command->info('   Admin: admin@demo.com');
        $this->command->info('   Dev: dev@demo.com');
        $this->command->info('   Password: password');
    }
}
