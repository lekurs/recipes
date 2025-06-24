<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->catchPhrase(),
            'description' => fake()->optional(0.7)->paragraph(), // 70% de chance d'avoir une description
            'status' => fake()->randomElement(ProjectStatus::cases()),
        ];
    }

    /**
     * Indicate that the project is a website project.
     */
    public function website(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'Refonte site web corporate',
                'E-commerce Prestashop',
                'Site vitrine WordPress',
                'Plateforme de réservation',
                'Portail client',
                'Site institutionnel',
                'Landing page produit',
                'Blog d\'entreprise'
            ]),
            'description' => fake()->randomElement([
                'Développement d\'un nouveau site web moderne et responsive.',
                'Refonte complète de l\'identité numérique avec interface utilisateur optimisée.',
                'Création d\'une plateforme e-commerce avec système de paiement intégré.',
                'Site vitrine professionnel avec CMS pour gestion autonome du contenu.',
            ]),
        ]);
    }

    /**
     * Indicate that the project is a mobile app project.
     */
    public function mobileApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'Application mobile iOS/Android',
                'App de gestion interne',
                'Application de livraison',
                'App de fidélité client',
                'Application de réservation',
                'App mobile e-commerce',
                'Outil de reporting mobile',
                'App de suivi projet'
            ]),
            'description' => fake()->randomElement([
                'Développement d\'une application mobile native pour iOS et Android.',
                'Application de gestion interne pour optimiser les processus métier.',
                'App mobile avec géolocalisation et notifications push.',
                'Solution mobile pour améliorer l\'expérience client.',
            ]),
        ]);
    }

    /**
     * Indicate that the project is a web application.
     */
    public function webApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement([
                'Dashboard analytics',
                'Plateforme de gestion',
                'CRM personnalisé',
                'Outil de facturation',
                'Système de ticketing',
                'Plateforme collaborative',
                'Interface d\'administration',
                'Outil de reporting'
            ]),
            'description' => fake()->randomElement([
                'Développement d\'une application web sur mesure avec tableau de bord.',
                'Plateforme de gestion intégrée pour optimiser les workflows.',
                'Solution web personnalisée avec interface intuitive et moderne.',
                'Outil métier développé spécifiquement pour les besoins de l\'entreprise.',
            ]),
        ]);
    }

    /**
     * Project with detailed description.
     */
    public function withDetailedDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraphs(3, true) . "\n\nObjectifs :\n" .
                "- " . fake()->sentence() . "\n" .
                "- " . fake()->sentence() . "\n" .
                "- " . fake()->sentence(),
        ]);
    }

    /**
     * Project without description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Urgent project.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '[URGENT] ' . $attributes['name'],
            'description' => 'Projet prioritaire avec délais serrés. ' . ($attributes['description'] ?? ''),
        ]);
    }
}
