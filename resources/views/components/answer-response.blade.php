@props(['answer'])

@php
    $role = $answer->user->role;
    $colorClasses = $role->getAnswerCardClasses(); // Méthode à ajouter dans l'enum
    $badgeVariant = $role->getBadgeVariant(); // Méthode à ajouter dans l'enum
    $bgColor = $role->getAnswerBackground(); // Méthode à ajouter dans l'enum
    $borderColor = $role->getBorderBackground()
@endphp

<div class="{{ $colorClasses }} rounded-r-lg p-4">
    <!-- Header avec rôle et nom -->
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <flux:badge :variant="$badgeVariant" size="sm">
                {{ ucfirst($answer->user->role->value) }}
            </flux:badge>
            <span class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $answer->user->name }}
            </span>
        </div>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ $answer->created_at->diffForHumans() }}
        </span>
    </div>

    <!-- Réponse -->
    <div class="prose prose-sm max-w-none dark:prose-invert">
        <div class="mb-3 p-3 {{ $borderColor }}  rounded border-l-2  dark:border-gray-600">
            <p class="{{ $bgColor }} dark:text-gray-400 text-sm">{{ $answer->comment }}</p>
        </div>
        <!-- Fichiers de CETTE answer -->
        @if($answer->files && $answer->files->count() > 0)
            <!-- Miniature cliquable -->
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($answer->files as $file)
                    @if(str_starts_with($file->mime_type, 'image/'))
                        <img
                            src="{{ $file->getFileUrl() }}"
                            alt="{{ $file->filename }}"
                            class="w-16 h-16 object-contain border {{ $borderColor }} bg-transparent rounded cursor-pointer hover:opacity-75 transition-opacity"
                            x-on:click="
                                document.getElementById('modal-image').src = '{{ $file->getFileUrl() }}';
                                document.getElementById('modal-image').alt = '{{ $file->original_name }}';
                                document.getElementById('modal-filename').textContent = '{{ $file->original_name }}';
                                $flux.modal('image-modal').show();
                            "
                        />
                    @else
                        <!-- Fichier non-image -->
                        <a href="{{ $file->url }}" target="_blank" class="text-blue-600 hover:underline">
                            <flux:icon.paper-clip class="inline mr-1" />
                            {{ $file->filename }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
