<?php

namespace App\Livewire\Recipe;

use App\Enums\RecipeStatus;
use App\Enums\RecipeType;
use App\Models\Project;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Project $project;

    // Propriétés pour le formulaire de création de recette
    public string $title = '';
    public string $description = '';
    public string $type = '';
    public string $status = '';
    public $recipeFile = '';

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->type = RecipeType::ALL->value;
        $this->status = 'in_progress';
    }

    public function createRecipe()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recipeFile' => 'nullable|file|max:10240',
        ]);

        $recipe = $this->project->recipes()->create([
            'title' => $this->title,
            'description' => $this->description,
            'type' => RecipeType::from($this->type),
            'status' => RecipeStatus::from($this->status),
        ]);

        if ($this->recipeFile && $this->recipeFile instanceof \Illuminate\Http\UploadedFile) {
            $path = $this->recipeFile->store('recipes', 'public');
            $recipe->file_path = $path;
            $recipe->save();
        }

        // Reset
        $this->reset(['title', 'description', 'recipeFile']);
        $this->type = RecipeType::ALL->value;
        $this->status = RecipeStatus::IN_PROGRESS->value;

        session()->flash('message', 'Recette ajoutée avec succès !');
//        $this->project->load('recipes.answers');
        // Dispatch avec toutes les infos utiles
        $this->dispatch('recipe-created', [
            'recipe_id' => $recipe->id,
            'refresh_needed' => true
        ]);

        //Ferme la modale
        Flux::modal('add-recipe')->close();
    }

    public function render()
    {
        return view('livewire.recipe.create');
    }
}
