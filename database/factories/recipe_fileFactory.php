<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\recipe_file;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class recipe_fileFactory extends Factory
{
    protected $model = recipe_file::class;

    public function definition()
    {
        return [
            'filename' => $this->faker->word(),
            'original_name' => $this->faker->name(),
            'size' => $this->faker->word(),
            'mime_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'recipe_id' => Recipe::factory(),
        ];
    }
}
