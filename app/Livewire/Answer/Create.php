<?php

namespace App\Livewire\Answer;

use App\Models\Answer;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Recipe $recipe;
    public Answer $answer;
    public $contentAnswer = '';
    public $question = '';
    public $attachments = [];

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    public function updatedAttachments()
    {
        // Cette méthode se déclenche quand attachments change
        dump('Fichiers uploadés:', $this->attachments);
    }

    public function addAnswer()
    {
        if(empty($this->contentAnswer)) {
            session()->flash('error', 'La réponse ne peut pas être vide.');
            return;
        }

        $answer = Answer::create([
            'comment' => $this->contentAnswer,
            'user_id' => auth()->id(),
            'question' => $this->question,
            'recipe_id' => $this->recipe->id,
        ]);

        // Sauvegarde les fichiers
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('answer-files', 'public');

                // Extraire le nom généré du path
                $generatedFilename = basename($path);

                // Crée l'enregistrement dans answer_files
                $answer->files()->create([
                    'filename' => $generatedFilename,
                    'original_name' => $attachment->getClientOriginalName(),
                    'mime_type' => $attachment->getMimeType(),
                    'size' => $attachment->getSize(),
                    'url' => Storage::url($path),
                ]);
            }
        }

        $this->recipe->update(['status' => 'updated']);

        $this->contentAnswer = '';
        $this->question = '';
        $this->attachments = [];

        $this->dispatch('answer-added');
    }

    public function render()
    {
        return view('livewire.answer.create');
    }
}
