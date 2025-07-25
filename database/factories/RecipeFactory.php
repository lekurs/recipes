<?php

namespace Database\Factories;

use App\Enums\RecipeStatus;
use App\Models\Recipe;
use App\Models\Project;
use App\Enums\RecipeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(RecipeStatus::cases()),
            'type' => fake()->randomElement(RecipeType::cases()),
            'title' => fake()->sentence(4), // Titre court
            'description' => fake()->optional(0.8)->paragraph(), // 80% ont une description
            'project_id' => Project::factory(), // Crée un projet si besoin
        ];
    }

    /**
     * Indicate that the recipe is for desktop.
     */
    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => RecipeType::DESKTOP,
            'title' => fake()->randomElement([
                'Problème d\'affichage sur grand écran',
                'Bug dans le menu de navigation',
                'Erreur lors du téléchargement de fichier',
                'Interface non responsive sur desktop',
                'Problème de performance sur Firefox',
                'Bouton de validation qui ne fonctionne pas',
                'Pagination défaillante',
                'Formulaire qui ne s\'envoit pas'
            ]),
        ]);
    }

    /**
     * Indicate that the recipe is for mobile.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => RecipeType::MOBILE,
            'title' => fake()->randomElement([
                'Interface cassée sur iPhone',
                'Menu burger ne s\'ouvre pas',
                'Problème de scroll sur mobile',
                'Boutons trop petits sur tablette',
                'App qui crash au démarrage',
                'Notifications push défaillantes',
                'Géolocalisation ne fonctionne pas',
                'Mise en page déformée sur Android'
            ]),
        ]);
    }

    /**
     * Recipe for a specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    /**
     * Pending recipe (initial state, no response yet).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::PENDING,
            'title' => fake()->randomElement([
                'Nouveau problème signalé',
                'Bug à corriger',
                'Demande d\'amélioration',
                'Problème à investiguer',
                'Feature request en attente',
            ]),
        ]);
    }

    /**
     * In progress recipe (client has seen/responded).
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::IN_PROGRESS,
            'title' => fake()->randomElement([
                'Correction en cours d\'analyse',
                'Problème pris en compte',
                'Investigation démarrée',
                'Développement initié',
            ]),
        ]);
    }

    /**
     * Updated recipe (dev/admin provided update).
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::UPDATED,
            'title' => fake()->randomElement([
                'Correction déployée',
                'Mise à jour disponible',
                'Problème résolu',
                'Feature implémentée',
                'Solution proposée',
            ]),
        ]);
    }

    /**
     * Question recipe (dev/admin asking for clarification).
     */
    public function question(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::QUESTION,
            'title' => fake()->randomElement([
                'Clarifications nécessaires',
                'Questions sur le problème',
                'Informations supplémentaires requises',
                'Besoin de précisions',
            ]),
        ]);
    }

    /**
     * Completed recipe (client marked as done).
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::COMPLETED,
            'title' => fake()->randomElement([
                'Problème résolu',
                'Feature validée',
                'Correction acceptée',
                'Développement finalisé',
            ]),
        ]);
    }

    /**
     * Rejected recipe (client not satisfied with solution).
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RecipeStatus::REJECTED,
            'title' => fake()->randomElement([
                'Solution non satisfaisante',
                'Correction à revoir',
                'Problème persistant',
                'Demande rejetée',
            ]),
        ]);
    }

    /**
     * Bug recipe (critical issue).
     */
    public function bug(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => '[BUG] ' . $attributes['title'],
            'description' => 'Bug critique nécessitant une correction rapide. ' . ($attributes['description'] ?? ''),
        ]);
    }

    /**
     * Feature request recipe.
     */
    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement([
                'Ajouter un système de filtres',
                'Implémenter la recherche avancée',
                'Créer un dashboard analytics',
                'Ajouter l\'export PDF',
                'Intégrer un chat en temps réel',
                'Développer l\'API REST',
                'Ajouter l\'authentification sociale',
                'Créer un système de notifications'
            ]),
            'description' => 'Demande d\'évolution pour améliorer l\'expérience utilisateur. ' . ($attributes['description'] ?? ''),
        ]);
    }

    /**
     * UI/UX recipe.
     */
    public function uiux(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement([
                'Améliorer l\'ergonomie du formulaire',
                'Revoir la hiérarchie visuelle',
                'Optimiser le parcours utilisateur',
                'Harmoniser la charte graphique',
                'Améliorer la lisibilité des textes',
                'Revoir les couleurs d\'état',
                'Optimiser l\'espacement des éléments',
                'Améliorer les micro-interactions'
            ]),
            'description' => 'Amélioration de l\'interface et de l\'expérience utilisateur. ' . ($attributes['description'] ?? ''),
        ]);
    }

    /**
     * Recipe with detailed description.
     */
    public function withDetailedDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraphs(2, true) . "\n\nÉtapes pour reproduire :\n" .
                "1. " . fake()->sentence() . "\n" .
                "2. " . fake()->sentence() . "\n" .
                "3. " . fake()->sentence() . "\n\n" .
                "Résultat attendu : " . fake()->sentence(),
        ]);
    }
}
