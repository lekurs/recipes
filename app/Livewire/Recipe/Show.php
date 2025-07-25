<?php

namespace App\Livewire\Recipe;

use App\Enums\RecipeStatus;
use App\Models\Project;
use App\Models\Recipe;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Project $project;
    public Recipe $recipe;
    public ?int $recipeId = null;
    public string $recipeStatus = '';

    public function mount(Project $project, Recipe $recipe, $recipeId = null): void
    {
        $this->project = $project;
        $this->recipeId = $recipeId;
        $this->recipe = $recipe;
        $this->recipeStatus = $this->recipe->status->value;
    }

    public function updatedRecipeStatus()
    {
        Recipe::query()
            ->where('id', $this->recipe->id)
            ->update(['status' => $this->recipeStatus]);

        $this->recipe->status = RecipeStatus::from($this->recipeStatus);
    }

    #[On('answer-added')]
    public function refreshRecipe()
    {
        $this->recipe = $this->recipe->fresh();
        $this->recipeStatus = $this->recipe->status->value; // 👈 IMPORTANT !
    }

    public function render()
    {
        return view('livewire.recipe.show');
    }
}
