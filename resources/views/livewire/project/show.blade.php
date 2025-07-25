<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header du projet -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->description }}</p>
            </div>
            <div class="flex items-center gap-4">
                <flux:badge
                    :color="$project->getStatusBadgeColor()"
                    size="lg"
                >
                    {{ $project->getStatusLabel() }}
                </flux:badge>

                @if(auth()->user()->role->value === "admin")
                    <!-- Action toggle -->
                    <flux:select variant="listbox" class="w-full" wire:model.change="projectStatus">
                        @foreach(App\Enums\ProjectStatus::cases() as $status)
                            <flux:select.option
                                value="{{ $status->value }}"
                                :selected="$projectStatus === $status->value"
                            >
                                <flux:badge
                                    :color="$status->color()"
                                    size="lg"
                                    class="mr-2"
                                >
                                </flux:badge>
                                {{ $status->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif
            </div>
        </div>

        <!-- Système de tabs Flux Pro -->
        <flux:tab.group wire:model="activeTab">
            <flux:tabs>
                <flux:tab name="informations">
                    <flux:icon.information-circle class="w-5 h-5 inline mr-2" />
                    Informations
                </flux:tab>
                <flux:tab name="client">
                    <flux:icon.building-office class="w-5 h-5 inline mr-2" />
                    Client
                </flux:tab>
                <flux:tab name="recipes">
                    <flux:icon.document-text class="w-5 h-5 inline mr-2" />
                    Recettes ({{ $this->recipesStats['total'] }})
                </flux:tab>
            </flux:tabs>

            <!-- TAB INFORMATIONS -->
            <flux:tab.panel name="informations">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Statistiques -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Stats Recipes -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <flux:icon.document-text class="w-5 h-5 inline mr-2" />
                                Recettes
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $this->recipesStats['en_cours'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">En cours</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ $this->recipesStats['terminees'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Terminées</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                        {{ $this->recipesStats['total'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Contacts -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <flux:icon.users class="w-5 h-5 inline mr-2" />
                                Contacts
                            </h3>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ $this->contactsStats['avec_token'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Avec accès</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                        {{ $this->contactsStats['sans_token'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">À inviter</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $this->contactsStats['possibles_a_ajouter'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Possibles</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                        {{ $this->contactsStats['total'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                                </div>
                            </div>
                        </div>

                        <!-- Dernières Recipes -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-h-[700px] overflow-y-auto"
                             x-data="{
                                 openRecipe(id) {
                                     $wire.set('selectedRecipeId', id);
                                     $flux.modal('show-recipe').show();
                                 }
                             }">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <flux:icon.clock class="w-5 h-5 inline mr-2" />
                                10 dernières recipes en cours
                            </h3>
                            @if($this->latestRecipes->count() > 0)
                                <div class="space-y-3">
                                    @foreach($this->latestRecipes as $recipe)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded" wire:key="recipe-{{ $recipe->id }}">
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $recipe->title }}
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $recipe->type->label() }} • {{ $recipe->created_at->diffForHumans() }}
                                                </div>
                                                <!-- Statut de la recipe uniquement -->
                                                <div class="flex gap-1 mt-1">
                                                    <flux:badge
                                                        :color="match($recipe->status) {
                                                            App\Enums\RecipeStatus::PENDING => 'gray',
                                                            App\Enums\RecipeStatus::IN_PROGRESS => 'blue',
                                                            App\Enums\RecipeStatus::UPDATED => 'yellow',
                                                            App\Enums\RecipeStatus::QUESTION => 'orange',
                                                            App\Enums\RecipeStatus::COMPLETED => 'green',
                                                            App\Enums\RecipeStatus::REJECTED => 'red',
                                                            default => 'gray'
                                                        }"
                                                        size="xs"
                                                    >
                                                        {{ $recipe->status->label() }}
                                                    </flux:badge>

                                                    <!-- Nombre d'answers pour info -->
                                                    @if($recipe->answers->count() > 0)
                                                        <span class="text-xs text-gray-500">
                                                            {{ $recipe->answers->count() }} réponse(s)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <flux:button wire:navigate href="{{ route('recipes.show', ['project' => $project, 'recipe' => $recipe]) }}" size="sm" variant="outline">
                                                Voir
                                            </flux:button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                    Aucune recipe en cours
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar avec infos client -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <flux:icon.building-office class="w-5 h-5 inline mr-2" />
                                Client
                            </h3>
                            @foreach($project->customers as $customer)
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $customer->name }}
                                        </div>
                                        <flux:badge
                                            :color="$customer->getStatusBadgeColor()"
                                            size="sm"
                                        >
                                            {{ $customer->getStatusLabel() }}
                                        </flux:badge>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Actions rapides -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Actions rapides
                            </h3>
                            <div class="space-y-2">
                                <flux:modal.trigger name="add-recipe">
                                    <flux:button variant="primary" class="w-full mb-2">
                                        <flux:icon.plus class="w-4 h-4 mr-2" />
                                        Nouvelle Recette
                                    </flux:button>
                                </flux:modal.trigger>

                                <flux:button variant="outline" class="w-full">
                                    <flux:icon.user-plus class="w-4 h-4 mr-2" />
                                    Inviter Contact
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </flux:tab.panel>

            <!-- TAB CLIENT avec le système de clic et gestion d'accès -->
            <!-- TAB CLIENT modifié avec le système de clic et gestion d'accès -->
            <flux:tab.panel name="client">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                        Informations Client
                    </h2>

                    @foreach($project->customers as $customer)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $customer->name }}
                                    </h3>
                                    <flux:badge
                                        :color="$customer->getStatusBadgeColor()"
                                        size="lg"
                                    >
                                        {{ $customer->getStatusLabel() }}
                                    </flux:badge>
                                </div>

                                <flux:link href="{{ route('customers.show', $customer->id) }}" wire:navigate>
                                    <flux:button size="sm" variant="outline">
                                        Voir
                                    </flux:button>
                                </flux:link>
                            </div>

                            <!-- Contacts du client avec système de clic -->
                            <div class="mt-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">
                                        Contacts ({{ $customer->contacts->count() }})
                                    </h4>
                                    <flux:modal.trigger name="create-contact">
                                        <flux:button variant="primary" class="mb-2 cursor-pointer">
                                            <flux:icon.plus class="w-4 h-4 mr-2" />
                                            Créer un contact
                                        </flux:button>
                                    </flux:modal.trigger>

                                </div>
                                @if($customer->contacts->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($this->getContactByCustomer($customer) as $contact)
                                            <!-- Utilisation du composant Contact.Access séparé avec key stable -->
                                            <livewire:contact.access
                                                :project="$project"
                                                :contact="$contact"
                                                wire:key="access-{{ $contact->id }}" />
                                        @endforeach
                                    </div>

                                    <div class="mt-4">
                                        {{ $this->getContactByCustomer($customer)->links() }}
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">
                                        Aucun contact enregistré pour ce client.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:tab.panel>

            <!-- TAB RECIPES -->
            <flux:tab.panel name="recipes">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Toutes les recettes
                        </h2>
                        <div class="flex items-center gap-4">
                            <!-- Select pour filtrer par type -->
                            <div class="min-w-48">
                                <select
                                    wire:change="setSelectedType($event.target.value)"
                                    class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-600"
                                >
                                    @foreach($this->recipeTypes as $value => $label)
                                        <option value="{{ $value }}" @if($selectedType === $value) selected @endif>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Select pour filtrer par statut -->
                            <div class="min-w-48">
                                <select
                                    wire:change="setSelectedStatus($event.target.value)"
                                    class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-600"
                                >
                                    @foreach($this->recipeStatuses as $value => $label)
                                        <option value="{{ $value }}" @if($selectedStatus === $value) selected @endif>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <flux:modal.trigger name="add-recipe">
                                <flux:button variant="primary" class="w-full mb-2">
                                    <flux:icon.plus class="w-4 h-4 mr-2" />
                                    Nouvelle Recipe
                                </flux:button>
                            </flux:modal.trigger>
                        </div>
                    </div>

                    @if($this->filteredRecipes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Titre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Créée le
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($this->filteredRecipes as $recipe)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $recipe->title }}
                                            </div>
                                            @if($recipe->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($recipe->description, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $recipe->type->label() }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <!-- Statut de la recipe -->
                                            <flux:badge
                                                :color="match($recipe->status) {
                                                        App\Enums\RecipeStatus::PENDING => 'gray',
                                                        App\Enums\RecipeStatus::IN_PROGRESS => 'blue',
                                                        App\Enums\RecipeStatus::UPDATED => 'yellow',
                                                        App\Enums\RecipeStatus::QUESTION => 'orange',
                                                        App\Enums\RecipeStatus::COMPLETED => 'green',
                                                        App\Enums\RecipeStatus::REJECTED => 'red',
                                                        default => 'gray'
                                                    }"
                                                size="sm"
                                            >
                                                {{ $recipe->status->label() }}
                                            </flux:badge>

                                            <!-- Nombre d'answers pour info -->
                                            @if($recipe->answers->count() > 0)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $recipe->answers->count() }} réponse(s)
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $recipe->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <flux:button wire:navigate href="{{ route('recipes.show', ['project' => $project, 'recipe' => $recipe]) }}" size="sm" variant="outline">
                                                Voir
                                            </flux:button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <flux:icon.document-text class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                @if($selectedType === 'all')
                                    Aucune recipe
                                @else
                                    Aucune recipe {{ $this->recipeTypes[$selectedType] }}
                                @endif
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                @if($selectedType === 'all')
                                    Commencez par créer votre première recipe pour ce projet.
                                @else
                                    Aucune recipe de type {{ $this->recipeTypes[$selectedType] }} trouvée pour ce projet.
                                @endif
                            </p>
                            <flux:modal.trigger name="add-recipe">
                                <flux:button variant="primary">
                                    <flux:icon.plus class="w-4 h-4 mr-2" />
                                    Créer une recipe
                                </flux:button>
                            </flux:modal.trigger>
                        </div>
                    @endif
                </div>
            </flux:tab.panel>
        </flux:tab.group>
    </div>

    <!-- CREATION D'UNE NOUVELLE RECIPE -->
    <flux:modal name="add-recipe" variant="flyout" class="!min-w-2xl">
        <livewire:recipe.create :project="$project" />
    </flux:modal>

    <!-- MODALE DE CREATION D'UN NOUVEAU CONTACT -->
    <flux:modal name="create-contact" variant="flyout" class="!min-w-2xl">
        <livewire:contact.create :customer="$customer" :project="$project" />
    </flux:modal>
</div>
