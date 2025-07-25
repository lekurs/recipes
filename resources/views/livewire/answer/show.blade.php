<div>
<!-- Échanges sur la recette -->
    <flux:card class="p-6 bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="md" class="mb-0">
                <flux:icon.chat-bubble-left-right class="w-5 h-5 inline mr-2"/>
                Échanges sur cette recette
            </flux:heading>
            <flux:badge variant="outline" size="sm">
                {{ $this->getRecipeAnswers()->count() }} réponses
            </flux:badge>
        </div>

        @if($this->getRecipeAnswers()->count() > 0)
            <div class="space-y-4">
                @foreach($this->getRecipeAnswers() as $answer)
                    <div class="flex items-start space-x-3">
                        <!-- Avatar selon le rôle -->
                        <div class="flex-shrink-0">
                            @foreach(\App\Enums\Role::cases() as $role)
                                @if($answer->user->role->value === $role->value)
                                    <x-user-avatar :user="$answer->user" />
                                @endif

                            @endforeach
                        </div>

                        <!-- Contenu de la réponse -->
                        <div class="flex-1">
                            @foreach(\App\Enums\Role::cases() as $role)
                                @if($answer->user->role->value === $role->value)
                                    <x-answer-response :answer="$answer" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <flux:icon.chat-bubble-left-right class="w-12 h-12 text-gray-400 mx-auto mb-4"/>
                <p class="text-gray-500 dark:text-gray-400">Aucun échange pour le moment</p>
            </div>
        @endif

        <!-- Zone d'ajout de réponse (si autorisé) -->
        @if(auth()->user()->role->value === 'admin' || auth()->user()->role->value === 'developer')
            <livewire:answer.create :recipe="$recipe"/>
        @endif
    </flux:card>

    <!-- Modal Flux simple -->
    <flux:modal name="image-modal" class="md:max-w-4xl">
        <div class="p-6">
            <img id="modal-image" src="" alt="" class="w-full h-auto rounded" />
            <p id="modal-filename" class="text-center text-sm text-gray-500 mt-2"></p>
        </div>
    </flux:modal>
</div>
