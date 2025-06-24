<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <flux:heading size="xl">Liste des Customers</flux:heading>

            <!-- Bouton Ajouter -->
            <flux:button
                variant="filled"
                color="green"
                wire:click="openAddForm"
                size="sm"
            >
                + Ajouter
            </flux:button>
        </div>

        <!-- Formulaire d'ajout (conditionnel) -->
        @if($showAddForm)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <flux:heading size="lg" class="mb-4">Ajouter un nouveau customer</flux:heading>

                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <flux:input
                            wire:model="newCustomerName"
                            placeholder="Nom du customer..."
                            label="Nom de l'entreprise"
                        />
                    </div>

                    <div class="flex space-x-2">
                        <flux:button
                            variant="filled"
                            color="green"
                            wire:click="createCustomer"
                        >
                            Créer
                        </flux:button>

                        <flux:button
                            variant="outline"
                            wire:click="closeAddForm"
                        >
                            Annuler
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modales de confirmation pour chaque customer -->
        @foreach($customers as $customer)
            <flux:modal name="delete-customer-{{ $customer->id }}" class="min-w-[22rem]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Supprimer le customer ?</flux:heading>
                        <flux:text class="mt-2">
                            <p>Vous êtes sur le point de supprimer <strong>{{ $customer->name }}</strong>.</p>
                            <p>Cette action ne peut pas être annulée.</p>
                            @if($customer->contacts()->count() > 0)
                                <p class="text-red-600 font-medium mt-2">
                                    ⚠️ Ce customer a {{ $customer->contacts()->count() }} contact(s) lié(s).
                                </p>
                            @endif
                        </flux:text>
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">Annuler</flux:button>
                        </flux:modal.close>
                        <flux:button
                            variant="danger"
                            wire:click="delete({{ $customer->id }})"
                        >
                            Confirmer la suppression
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
        @endforeach

        <div class="flex items-center space-x-4">
            <!-- Boutons de tri -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Trier par:</span>

                <!-- Tri par nom -->
                <flux:button
                    size="sm"
                    variant="{{ $sortBy === 'name' ? 'filled' : 'outline' }}"
                    wire:click="sort('name')"
                >
                    Nom
                    @if($sortBy === 'name')
                        @if($sortDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    @endif
                </flux:button>

                <!-- Tri par date -->
                <flux:button
                    size="sm"
                    variant="{{ $sortBy === 'created_at' ? 'filled' : 'outline' }}"
                    wire:click="sort('created_at')"
                >
                    Date
                    @if($sortBy === 'created_at')
                        @if($sortDirection === 'asc')
                            ↑
                        @else
                            ↓
                        @endif
                    @endif
                </flux:button>
            </div>

            <!-- Champ de recherche réactif -->
            <div class="w-80">
                <flux:input
                    wire:model.live="search"
                    placeholder="Rechercher un customer..."
                    icon="magnifying-glass"
                />
            </div>
        </div>
    </div>

    <!-- Utiliser div + Tailwind au lieu de flux:card -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <flux:heading size="lg">
                Nos clients ({{ $customers->count() }})

                <!-- Stats dynamiques -->
                <div class="flex space-x-2 mt-2">
                    <flux:badge color="green" size="sm">
                        {{ $customers->where('is_active', true)->count() }} actifs
                    </flux:badge>
                    <flux:badge color="red" size="sm">
                        {{ $customers->where('is_active', false)->count() }} inactifs
                    </flux:badge>
                </div>

                @if($search)
                    <flux:badge color="blue" size="sm" class="ml-2">
                        Recherche: "{{ $search }}"
                    </flux:badge>
                @endif
            </flux:heading>
        </div>

        <div class="p-6">
            <!-- Messages flash -->
            @if(session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($customers->count() > 0)
                <div class="space-y-4">
                    @foreach($customers as $customer)
                        <!-- Card simple avec Tailwind -->
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <div class="flex items-center justify-between">
                                <div>
                                    <flux:heading size="md">{{ $customer->name }}</flux:heading>
                                    <flux:subheading>
                                        Créé le {{ $customer->created_at->format('d/m/Y') }}
                                    </flux:subheading>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Badge dynamique basé sur is_active -->
                                    <flux:badge
                                        color="{{ $customer->getStatusBadgeColor() }}"
                                        size="sm"
                                    >
                                        {{ $customer->getStatusLabel() }}
                                    </flux:badge>

                                    <!-- Dropdown Actions -->
                                    <flux:dropdown>
                                        <flux:button
                                            size="sm"
                                            variant="outline"
                                            icon="ellipsis-horizontal"
                                        >
                                            Actions
                                        </flux:button>

                                        <flux:menu>
                                            <!-- Action Voir -->
                                            <flux:menu.item
                                                icon="eye"
                                                href="{{ route('customers.show', $customer->id) }}"
                                                wire:navigate
                                            >
                                                Voir les détails
                                            </flux:menu.item>

                                            <!-- Action Toggle Status -->
                                            <flux:menu.item
                                                icon="{{ $customer->is_active ? 'pause' : 'play' }}"
                                                wire:click="toggleStatus({{ $customer->id }})"
                                            >
                                                {{ $customer->is_active ? 'Désactiver' : 'Activer' }}
                                            </flux:menu.item>

                                            <!-- Séparateur -->
                                            <flux:menu.separator />

                                            <!-- Action Supprimer (avec modal) -->
                                            <flux:modal.trigger name="delete-customer-{{ $customer->id }}">
                                                <flux:menu.item
                                                    icon="trash"
                                                    variant="danger"
                                                >
                                                    Supprimer
                                                </flux:menu.item>
                                            </flux:modal.trigger>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:subheading class="text-center py-8">
                    Aucun customer trouvé.
                </flux:subheading>
            @endif
        </div>
        <!-- Pagination stylée Flux -->
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $customers->links('pagination.flux') }}
            </div>
        @endif
    </div>
</div>
