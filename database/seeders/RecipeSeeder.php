<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer qu'on a des projets
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $projects = Project::factory()->count(5)->create();
        }

        // Pour chaque projet, créer plusieurs recipes
        $projects->each(function ($project) {
            $recipeCount = fake()->numberBetween(3, 8); // 3 à 8 recipes par projet

            // === BUGS CRITIQUES (toujours au moins 1-2) ===
            $bugCount = fake()->numberBetween(1, 2);
            Recipe::factory()
                ->count($bugCount)
                ->bug()
                ->sequence(
                    ['type' => \App\Enums\RecipeType::DESKTOP],
                    ['type' => \App\Enums\RecipeType::MOBILE]
                )
                ->forProject($project)
                ->create();

            // === DEMANDES DE FEATURES ===
            $featureCount = fake()->numberBetween(1, 3);
            Recipe::factory()
                ->count($featureCount)
                ->feature()
                ->withDetailedDescription()
                ->forProject($project)
                ->create();

            // === AMÉLIORATIONS UI/UX ===
            if (fake()->boolean(70)) { // 70% de chance d'avoir des améliorations UI
                Recipe::factory()
                    ->count(fake()->numberBetween(1, 2))
                    ->uiux()
                    ->sequence(
                        ['type' => \App\Enums\RecipeType::DESKTOP],
                        ['type' => \App\Enums\RecipeType::MOBILE]
                    )
                    ->forProject($project)
                    ->create();
            }

            // === RECIPES MOBILES SPÉCIFIQUES ===
            Recipe::factory()
                ->count(fake()->numberBetween(1, 2))
                ->mobile()
                ->forProject($project)
                ->create();

            // === RECIPES DESKTOP SPÉCIFIQUES ===
            Recipe::factory()
                ->count(fake()->numberBetween(1, 2))
                ->desktop()
                ->forProject($project)
                ->create();
        });

        // === RECIPES ORPHELINES (pour tester) ===
        // Quelques recipes qui créent leurs propres projets
        Recipe::factory()
            ->count(3)
            ->bug()
            ->withDetailedDescription()
            ->create();

        // === RECIPES TEST SPÉCIFIQUES ===
        $firstProject = $projects->first();

        // Bug mobile urgent
        Recipe::factory()->create([
            'type' => \App\Enums\RecipeType::MOBILE,
            'title' => '[BUG] App crash au login',
            'description' => 'L\'application se ferme brutalement lors de la tentative de connexion sur iOS 15+',
            'project_id' => $firstProject->id,
        ]);

        // Feature desktop
        Recipe::factory()->create([
            'type' => \App\Enums\RecipeType::DESKTOP,
            'title' => 'Ajouter export Excel',
            'description' => 'Permettre l\'export des données en format Excel depuis le dashboard',
            'project_id' => $firstProject->id,
        ]);

        // Amélioration UI sans description
        Recipe::factory()->create([
            'type' => \App\Enums\RecipeType::MOBILE,
            'title' => 'Améliorer les boutons tactiles',
            'description' => null,
            'project_id' => $firstProject->id,
        ]);
    }
}
