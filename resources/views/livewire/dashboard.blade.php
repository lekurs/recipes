<div class="space-y-6">
    <!-- Header du Dashboard -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading>Vue d'ensemble de vos projets et clients</flux:subheading>
        </div>
        <div class="text-sm text-gray-500">
            Dernière mise à jour : {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="text-gray-500">Total Clients</flux:subheading>
                    <flux:heading size="lg" class="text-blue-600">{{ $totalCustomers }}</flux:heading>
                </div>
                <div class="p-3 px-4 flex justify-center items-center bg-blue-100 rounded-full">
                    <span class="text-2xl">👥</span>
                </div>
            </div>
        </flux:card>

        <!-- Total Contacts -->
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="text-gray-500">Total Contacts</flux:subheading>
                    <flux:heading size="lg" class="text-green-600">{{ $totalContacts }}</flux:heading>
                </div>
                <div class="p-3 px-4 flex justify-center items-center bg-green-100 rounded-full">
                    <span class="text-2xl">📞</span>
                </div>
            </div>
        </flux:card>

        <!-- Projets En Cours -->
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="text-gray-500">Projets En Cours</flux:subheading>
                    <flux:heading size="lg" class="text-orange-600">{{ $projectStats['on_going'] ?? 0 }}</flux:heading>
                </div>
                <div class="p-3 px-4 flex justify-center items-center bg-orange-100 rounded-full">
                    <span class="text-2xl">🚀</span>
                </div>
            </div>
        </flux:card>

        <!-- Projets Terminés -->
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="text-gray-500">Projets Terminés</flux:subheading>
                    <flux:heading size="lg" class="text-emerald-600">{{ $projectStats['completed'] ?? 0 }}</flux:heading>
                </div>
                <div class="p-3 px-4 flex justify-center items-center bg-emerald-100 rounded-full">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Graphique et Liste des projets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique des projets -->
        <flux:card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <flux:heading size="lg">Évolution des Projets</flux:heading>
                <flux:select wire:model.live="selectedYear" size="sm">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Graphique avec Flux Pro - style amélioré -->
            <flux:chart wire:model="data">
                <flux:chart.viewport class="min-h-[20rem]">
                    <flux:chart.svg>
                        <!-- Projets en cours - Orange -->
                        <flux:chart.line field="ongoing" class="text-blue-500" curve="none" />
                        <flux:chart.point field="ongoing" class="text-blue-500" r="4" stroke-width="2" />

                        <!-- Projets terminés - Vert -->
                        <flux:chart.line field="completed" class="text-green-500" curve="none" />
                        <flux:chart.point field="completed" class="text-green-500" r="4" stroke-width="2" />

                        <!-- Projets en pause - Jaune -->
                        <flux:chart.line field="paused" class="text-yellow-500" curve="none" />
                        <flux:chart.point field="paused" class="text-yellow-500" r="4" stroke-width="2" />

                        <!-- Projets annulés - Rouge -->
                        <flux:chart.line field="cancelled" class="text-red-500" curve="none" />
                        <flux:chart.point field="cancelled" class="text-red-500" r="4" stroke-width="2" />

                        <!-- Axe X (dates) -->
                        <flux:chart.axis axis="x" field="date">
                            <flux:chart.axis.tick />
                            <flux:chart.axis.line />
                        </flux:chart.axis>

                        <!-- Axe Y (nombres) -->
                        <flux:chart.axis axis="y">
                            <flux:chart.axis.grid />
                            <flux:chart.axis.tick />
                        </flux:chart.axis>
                    </flux:chart.svg>
                </flux:chart.viewport>

                <!-- Légende -->
                <div class="flex justify-center gap-6 pt-4">
                    <flux:chart.legend label="En cours">
                        <flux:chart.legend.indicator class="bg-blue-500" />
                    </flux:chart.legend>
                    <flux:chart.legend label="Terminés">
                        <flux:chart.legend.indicator class="bg-green-500" />
                    </flux:chart.legend>
                    <flux:chart.legend label="En pause">
                        <flux:chart.legend.indicator class="bg-yellow-500" />
                    </flux:chart.legend>
                    <flux:chart.legend label="Annulés">
                        <flux:chart.legend.indicator class="bg-red-500" />
                    </flux:chart.legend>
                </div>
            </flux:chart>
        </flux:card>

        <!-- Statistiques détaillées -->
        <flux:card class="p-6">
            <flux:heading size="lg" class="mb-6">Répartition des Projets</flux:heading>

            <div class="space-y-4">
                <!-- En cours -->
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">🚀</span>
                        <div>
                            <div class="font-medium">En cours</div>
                            <div class="text-sm text-gray-500">Projets actifs</div>
                        </div>
                    </div>
                    <flux:badge color="blue">{{ $projectStats['on_going'] ?? 0 }}</flux:badge>
                </div>

                <!-- Terminés -->
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <div class="font-medium">Terminés</div>
                            <div class="text-sm text-gray-500">Projets finalisés</div>
                        </div>
                    </div>
                    <flux:badge color="green">{{ $projectStats['completed'] ?? 0 }}</flux:badge>
                </div>

                <!-- En pause -->
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">⏸️</span>
                        <div>
                            <div class="font-medium">En pause</div>
                            <div class="text-sm text-gray-500">Projets suspendus</div>
                        </div>
                    </div>
                    <flux:badge color="orange">{{ $projectStats['paused'] ?? 0 }}</flux:badge>
                </div>

                <!-- Annulés -->
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl">❌</span>
                        <div>
                            <div class="font-medium">Annulés</div>
                            <div class="text-sm text-gray-500">Projets abandonnés</div>
                        </div>
                    </div>
                    <flux:badge color="red">{{ $projectStats['cancelled'] ?? 0 }}</flux:badge>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Liste des projets en cours -->
    <flux:card class="p-6">
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="lg">Projets En Cours ({{ $ongoingProjects->count() }})</flux:heading>
            <div class="flex items-center space-x-3">
                <flux:input
                    wire:model.live="search"
                    placeholder="Rechercher un projet..."
                    size="sm"
                    iconless
                    class="w-64"
                />
            </div>
        </div>

        @if($ongoingProjects->count() > 0)
            <div class="space-y-3">
                @foreach($ongoingProjects as $project)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <flux:heading size="md">{{ $project->name }}</flux:heading>
                                <flux:badge color="blue">En cours</flux:badge>
                            </div>

                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                <span>👤 {{ $project->customers->first()?->name ?? 'Client non défini' }}</span>
                                <span>📅 {{ $project->created_at->format('d/m/Y') }}</span>
                                @if($project->contacts_count > 0)
                                    <span>👥 {{ $project->contacts_count }} contact(s)</span>
                                @endif
                            </div>

                            @if($project->description)
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ Str::limit($project->description, 100) }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2">
                            @if($project->customers->first())
                                <flux:link href="{{ route('projects.show', $project) }}" wire:navigate>
                                    <flux:button size="sm" variant="outline">
                                        Voir
                                    </flux:button>
                                </flux:link>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $ongoingProjects->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <span class="text-6xl mb-4 block">🎯</span>
                <flux:subheading>
                    @if($search)
                        Aucun projet trouvé pour "{{ $search }}"
                    @else
                        Aucun projet en cours
                    @endif
                </flux:subheading>

                @if(!$search)
                    <flux:link href="{{ route('customers.index') }}" wire:navigate>
                        <flux:button class="mt-4" color="blue">
                            Créer un projet
                        </flux:button>
                    </flux:link>
                @endif
            </div>
        @endif
    </flux:card>
</div>
