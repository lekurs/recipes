<div>
    <!-- Header avec infos principales -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <flux:button
                    size="sm"
                    variant="outline"
                    icon="arrow-left"
                    href="{{ route('customers.index') }}"
                    wire:navigate
                >
                    Retour
                </flux:button>

                <div>
                    <flux:heading size="xl">{{ $customer->name }}</flux:heading>
                    <flux:subheading>
                        Client depuis le {{ $customer->created_at->format('d/m/Y') }}
                    </flux:subheading>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Badge statut -->
                <flux:badge
                    color="{{ $customer->getStatusBadgeColor() }}"
                    size="lg"
                >
                    {{ $customer->getStatusLabel() }}
                </flux:badge>

                <!-- Action toggle -->
                <flux:button
                    size="sm"
                    color="{{ $customer->is_active ? 'orange' : 'green' }}"
                    wire:click="toggleStatus"
                >
                    {{ $customer->is_active ? 'Désactiver' : 'Activer' }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabs avec Flux Pro - syntaxe correcte -->
    <flux:tab.group>
        <flux:tabs wire:model="activeTab">
            <flux:tab name="infos" class="cursor-pointer">📋 Informations</flux:tab>
            <flux:tab name="contacts" class="cursor-pointer">👥 Contacts ({{ $customer->contacts->count() }})</flux:tab>
            <flux:tab name="projects" class="cursor-pointer">📁 Projets ({{ $customer->projects->count() }})</flux:tab>
        </flux:tabs>

        <!-- Panel Informations -->
        <flux:tab.panel name="infos">
            <div class="space-y-6 mt-6">
                <flux:heading size="lg">Informations générales</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Infos principales -->
                    <flux:card class="p-6">
                        <flux:heading size="md" class="mb-4">Détails du client</flux:heading>

                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom de l'entreprise</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $customer->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd>
                                    <flux:badge color="{{ $customer->getStatusBadgeColor() }}">
                                        {{ $customer->getStatusLabel() }}
                                    </flux:badge>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                                <dd class="text-gray-900">{{ $customer->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                                <dd class="text-gray-900">{{ $customer->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        </dl>
                    </flux:card>

                    <!-- Card Statistiques -->
                    <flux:card class="p-6">
                        <flux:heading size="md" class="mb-4">Statistiques</flux:heading>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Contacts</span>
                                <flux:badge>{{ $customer->contacts->count() }}</flux:badge>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Projets totaux</span>
                                <flux:badge>{{ $customer->projects->count() }}</flux:badge>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Projets en cours</span>
                                <flux:badge color="blue">{{ $customer->ongoingProjects->count() }}</flux:badge>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Projets terminés</span>
                                <flux:badge color="green">{{ $customer->completedProjects->count() }}</flux:badge>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <!-- Panel Contacts -->
        <flux:tab.panel name="contacts">
            <div class="mt-6">
                <livewire:contact.show :customer="$customer" />
            </div>

        </flux:tab.panel>

        <!-- Panel Projets -->
        <flux:tab.panel name="projects">
            <div class="mt-6">
                <livewire:project.index :customer="$customer" />
            </div>
        </flux:tab.panel>
{{--        <flux:tab.panel name="projects">--}}
{{--            <div class="mt-6">--}}
{{--                <livewire:project.show :customer="$customer" />--}}
{{--            </div>--}}
{{--        </flux:tab.panel>--}}
    </flux:tab.group>
</div>
