<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'status' => $this->faker->word(),
            'comment' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'recipe_id' => Recipe::factory(),
            'user_id' => User::factory(),
        ];
    }
}
