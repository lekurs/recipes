<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Recipe;
use App\Models\User;
use App\Enums\AnswerStatus;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'comment' => fake()->optional(0.8)->paragraph(), // 80% ont un commentaire
            'question' => fake()->optional(0.3)->sentence(), // 30% ont une question
            'user_id' => User::factory(), // Crée un user si besoin
            'responded_at' => fake()->optional(0.9)->dateTimeBetween('-1 month', 'now'), // 90% ont une date de réponse
        ];
    }

    /**
     * Answer for a specific recipe.
     */
    public function forRecipe(Recipe $recipe): static
    {
        return $this->state(fn (array $attributes) => [
            'recipe_id' => $recipe->id,
        ]);
    }

    /**
     * Answer by a specific user.
     */
    public function byUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Pending answer (no response yet).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => null,
            'question' => null,
            'user_id' => null,
            'responded_at' => null,
        ]);
    }

    /**
     * In progress answer (client started working).
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->randomElement([
                'Nous avons commencé à regarder le problème.',
                'Investigation en cours.',
                'Reproduction du bug confirmée.',
                'Analyse de la situation en cours.',
            ]),
            'question' => null,
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Updated answer (dev/admin provided update).
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->randomElement([
                'Correction déployée en pré-production. Merci de tester.',
                'Bug corrigé. La mise à jour sera disponible demain.',
                'Problème résolu côté serveur.',
                'Feature implémentée selon vos spécifications.',
                'Interface mise à jour. Veuillez vérifier le résultat.',
            ]),
            'question' => null,
            'responded_at' => fake()->dateTimeBetween('-3 days', 'now'),
        ]);
    }

    /**
     * Question answer (dev/admin asking for clarification).
     */
    public function question(): static
    {
        return $this->state(fn (array $attributes) => [
//            'status' => AnswerStatus::QUESTION,
            'comment' => fake()->randomElement([
                'Nous avons besoin de plus d\'informations.',
                'Pouvez-vous nous donner plus de détails ?',
                'Investigation en cours, quelques questions.',
            ]),
            'question' => fake()->randomElement([
                'Sur quel navigateur rencontrez-vous ce problème ?',
                'Avez-vous des captures d\'écran supplémentaires ?',
                'À quelle fréquence ce bug se produit-il ?',
                'Quel est le comportement attendu exactement ?',
                'Ce problème existe-t-il sur d\'autres pages ?',
                'Pouvez-vous reproduire le problème systématiquement ?',
            ]),
            'responded_at' => fake()->dateTimeBetween('-2 days', 'now'),
        ]);
    }

    /**
     * Completed answer (client marked as done).
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
//            'status' => AnswerStatus::COMPLETED,
            'comment' => fake()->randomElement([
                'Parfait, le problème est résolu. Merci !',
                'Correction validée, tout fonctionne bien.',
                'Impeccable, exactement ce que nous attendions.',
                'Bug corrigé, nous validons la solution.',
            ]),
            'question' => null,
            'responded_at' => fake()->dateTimeBetween('-1 day', 'now'),
        ]);
    }

    /**
     * Rejected answer (client not satisfied).
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
//            'status' => AnswerStatus::REJECTED,
            'comment' => fake()->randomElement([
                'Le problème persiste malheureusement.',
                'La correction ne fonctionne pas comme attendu.',
                'Ce n\'est pas exactement ce que nous demandions.',
                'Le bug est toujours présent.',
            ]),
            'question' => fake()->optional(0.5)->randomElement([
                'Pouvez-vous regarder à nouveau ?',
                'Y a-t-il une autre approche possible ?',
                'Le délai peut-il être revu ?',
            ]),
            'responded_at' => fake()->dateTimeBetween('-1 day', 'now'),
        ]);
    }

    /**
     * Recent answer (last 24 hours).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'responded_at' => fake()->dateTimeBetween('-1 day', 'now'),
        ]);
    }

    /**
     * Old answer (more than 1 week).
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'responded_at' => fake()->dateTimeBetween('-2 months', '-1 week'),
        ]);
    }

    /**
     * Answer with detailed comment.
     */
    public function withDetailedComment(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->paragraphs(2, true) . "\n\nActions réalisées :\n" .
                "- " . fake()->sentence() . "\n" .
                "- " . fake()->sentence() . "\n" .
                "- " . fake()->sentence(),
        ]);
    }
}
