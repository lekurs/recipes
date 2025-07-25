<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <!-- Header avec bouton d'ajout -->
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="lg">Projets ({{ $projects->count() }})</flux:heading>
        <div class="flex items-center gap-2">
            <flux:input
                icon="magnifying-glass"
                placeholder="Rechercher un projet"
                wire:model.live.debounce.500ms="search"
            />
            <flux:separator vertical />
            <flux:button color="green" wire:click="createProject">
                + Ajouter un projet
            </flux:button>
        </div>
    </div>


    <!-- Liste des contacts -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($projects as $project)
                <flux:card class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <flux:heading size="md">{{ $project->name }}</flux:heading>
                            <flux:subheading>{{ $project->description ?? 'Pas de description renseignée' }}</flux:subheading>

                            <div class="mt-3 space-y-1 text-sm text-gray-600">
                                @if($project->status)
                                    <flux:badge color="{{ $project->status->color() }}">{{ $project->status->label() }}</flux:badge>
                                @endif
                            </div>

                            <div class="mt-3 space-y-1 text-sm text-gray-600">
                                @if($project->contacts->count() > 0)
                                    <div class="space-y-1">
                                        <div>👥 Contacts actifs :
                                            <flux:badge color="green">{{ $project->validContacts->count() }}</flux:badge>
                                        </div>
                                        <div>🔄 Contacts activables :
                                            <flux:badge color="orange">{{ $project->contacts->count() - $project->validContacts->count() }}</flux:badge>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Aucun contact associé</span>
                                @endif
                            </div>
                        </div>

                        <flux:dropdown>
                            <flux:button size="sm" variant="outline" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item icon="eye" wire:click="showContact({{ $project->id }})">
                                    Voir détails
                                </flux:menu.item>
                                <flux:menu.item icon="pencil" wire:click="editContact({{ $project->id }})">
                                    Modifier
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:modal.trigger name="delete-contact-{{ $project->id }}">
                                    <flux:menu.item icon="trash" variant="danger">
                                        Supprimer
                                    </flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
