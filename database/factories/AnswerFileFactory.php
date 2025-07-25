<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\AnswerFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AnswerFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = AnswerFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = fake()->randomElement($extensions);
        $filename = fake()->uuid() . '.' . $extension;
        $originalName = fake()->words(2, true) . '.' . $extension;

        return [
            'answer_id' => Answer::factory(),
            'filename' => $filename, // Nom de fichier sécurisé
            'original_name' => $originalName, // Nom original de l'utilisateur
            'size' => fake()->numberBetween(50000, 5000000), // 50KB à 5MB
            'mime_type' => $this->getMimeType($extension),
        ];
    }

    /**
     * Get MIME type based on extension.
     */
    private function getMimeType(string $extension): string
    {
        return match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    /**
     * File for a specific Answer.
     */
    public function forAnswer(Answer $answer): static
    {
        return $this->state(fn (array $attributes) => [
            'answer_id' => $answer->id,
        ]);
    }

    /**
     * PNG screenshot file.
     */
    public function screenshot(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => 'screenshot_' . fake()->uuid() . '.png',
            'original_name' => fake()->randomElement([
                'capture_ecran.png',
                'screenshot_bug.png',
                'erreur_affichage.png',
                'probleme_interface.png',
                'bug_mobile.png'
            ]),
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(100000, 2000000), // 100KB à 2MB
        ]);
    }

    /**
     * JPEG photo file.
     */
    public function photo(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => 'photo_' . fake()->uuid() . '.jpg',
            'original_name' => fake()->randomElement([
                'photo_probleme.jpg',
                'IMG_' . fake()->numberBetween(1000, 9999) . '.jpg',
                'erreur_ecran.jpg',
                'bug_affichage.jpg'
            ]),
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(200000, 8000000), // 200KB à 8MB
        ]);
    }

    /**
     * Small file (under 500KB).
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => fake()->numberBetween(10000, 500000), // 10KB à 500KB
        ]);
    }

    /**
     * Large file (over 2MB).
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => fake()->numberBetween(2000000, 10000000), // 2MB à 10MB
        ]);
    }

    /**
     * Mobile screenshot with specific naming.
     */
    public function mobileScreenshot(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => 'mobile_' . fake()->uuid() . '.png',
            'original_name' => fake()->randomElement([
                'iPhone_screenshot.png',
                'Android_capture.png',
                'tablet_bug.png',
                'mobile_error.png',
                'app_crash.png'
            ]),
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(150000, 1500000), // 150KB à 1.5MB
        ]);
    }

    /**
     * Desktop screenshot with specific naming.
     */
    public function desktopScreenshot(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => 'desktop_' . fake()->uuid() . '.png',
            'original_name' => fake()->randomElement([
                'bureau_capture.png',
                'desktop_bug.png',
                'erreur_firefox.png',
                'chrome_probleme.png',
                'site_casse.png'
            ]),
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(300000, 3000000), // 300KB à 3MB
        ]);
    }

    /**
     * GIF animation file.
     */
    public function gif(): static
    {
        return $this->state(fn (array $attributes) => [
            'filename' => 'animation_' . fake()->uuid() . '.gif',
            'original_name' => fake()->randomElement([
                'bug_animation.gif',
                'erreur_loop.gif',
                'probleme_ui.gif',
                'demo_bug.gif'
            ]),
            'mime_type' => 'image/gif',
            'size' => fake()->numberBetween(500000, 15000000), // 500KB à 15MB
        ]);
    }
}
