<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header du projet -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $recipe->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <flux:badge
                    :color="$recipe->getStatusColor()"
                    size="lg"
                >
                    {{ $recipe->getStatusLabel() }}
                </flux:badge>

                <flux:select variant="listbox" class="w-full" wire:model.change="recipeStatus">
                    @foreach(App\Enums\RecipeStatus::getVisibleCasesForRole(auth()->user()->role->value) as $status)
                        <flux:select.option
                            value="{{ $status->value }}"
                            :selected="$recipe->status->value === $status->value"
                        >
                            <flux:badge
                                :color="$status->badgeColor()"
                                size="lg"
                                class="mr-2"
                            >
                            </flux:badge>
                            {{ $status->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <!-- RECETTES -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Description de la recette -->
                <flux:card class="p-6 bg-white dark:bg-gray-800">
                    <flux:heading size="lg" class="mb-4">
                        <flux:icon.book-open class="w-5 h-5 inline mr-2"/>
                        {{ $recipe->description }}
                    </flux:heading>
                </flux:card>

                <!-- Détails de la recette -->
                <flux:card class="p-6 bg-white dark:bg-gray-800">
                    <flux:heading size="md" class="mb-4">Détails de la recette</flux:heading>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type de recette</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $recipe->type->label() }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création</dt>
                            <dd class="text-gray-900 dark:text-white">
                                {{ $recipe->created_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dernière modification</dt>
                            <dd class="text-gray-900 dark:text-white">
                                {{ $recipe->updated_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                    </dl>
                </flux:card>
                <!-- Retour des recettes -->
                <livewire:answer.show :recipe="$recipe" />
            </div>
            <!-- SIDEBAR -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <flux:icon.rocket-launch class="w-5 h-5 inline mr-2"/>
                        Projet
                    </h3>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">
                                <flux:badge
                                    :color="$project->getStatusBadgeColor()"
                                    size="sm"
                                >
                                    {{ $project->getStatusLabel() }}
                                </flux:badge> {{ $project->name }}
                            </div>
                        </div>
                    </div>
                    <div class="my-6">
                        <flux:button wire:navigate href="{{ route('projects.show', $project->id) }}"
                                     variant="primary"
                                     class="w-full">
                            Voir le projet
                        </flux:button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
