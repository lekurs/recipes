<?php

namespace App\Livewire\Answer;

use App\Models\Answer;
use App\Models\Recipe;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Recipe $recipe;
    public $imageUrl = '';
    public $imageFilename = '';

    public function mount(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

//    public function openImageModal($url, $filename)
//    {
//        $this->imageUrl = $url;
//        $this->imageFilename = $filename;
////        $this->dispatch('open-modal', name: 'image-modal');
//    }

    public function getRecipeAnswers()
    {
        return $this->recipe->answers()
            ->with('files')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[On('answer-added')]
    public function refreshAnswers()
    {
        $this->recipe = $this->recipe->fresh();
    }

    public function render()
    {
        return view('livewire.answer.show');
    }
}
