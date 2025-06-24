<div>
    <!-- Messages flash -->
    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header avec bouton d'ajout -->
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="lg">Contacts ({{ $contacts->count() }})</flux:heading>
        <div class="flex items-center gap-2">
            <flux:input
                icon="magnifying-glass"
                placeholder="Rechercher un contact"
                wire:model.live.debounce.500ms="search"
            />
            <flux:separator vertical />
            <flux:button color="green" wire:click="createContact">
                + Ajouter un contact
            </flux:button>
        </div>

    </div>

    <!-- Liste des contacts -->
    @if($contacts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                <flux:card class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <flux:heading size="md">{{ $contact->name }}</flux:heading>
                            <flux:subheading>{{ $contact->job_area ?? 'Poste non renseigné' }}</flux:subheading>

                            <div class="mt-3 space-y-1 text-sm text-gray-600">
                                @if($contact->email)
                                    <div class="flex items-center space-x-2">
                                        <span>📧</span>
                                        <a href="mailto:{{ $contact->email }}" class=" hover:underline">
                                            {{ $contact->email }}
                                        </a>
                                    </div>
                                @endif

                                @if($contact->phone)
                                    <div class="flex items-center space-x-2">
                                        <span>📞</span>
                                        <a href="tel:{{ $contact->phone }}" class="text-blue-600 hover:underline">
                                            {{ $contact->phone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <flux:dropdown>
                            <flux:button size="sm" variant="outline" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item icon="eye" wire:click="showContact({{ $contact->id }})">
                                    Voir détails
                                </flux:menu.item>
                                <flux:menu.item icon="pencil" wire:click="editContact({{ $contact->id }})">
                                    Modifier
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:modal.trigger name="delete-contact-{{ $contact->id }}">
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

        <!-- MODALES DE SUPPRESSION EN DEHORS DE LA GRID -->
        @foreach($contacts as $contact)
            <flux:modal name="delete-contact-{{ $contact->id }}" class="min-w-[22rem]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Supprimer ce contact ?</flux:heading>
                        <flux:text class="mt-2">
                            <p>Vous allez supprimer <strong>{{ $contact->name }}</strong>.</p>
                            <p>Cette action ne peut pas être annulée.</p>
                        </flux:text>
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">Annuler</flux:button>
                        </flux:modal.close>
                        <flux:button wire:click="deleteContact({{ $contact->id }})" variant="danger">
                            Supprimer
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
        @endforeach
    @else
        <div class="text-center py-12">
            <flux:subheading>Aucun contact pour ce client</flux:subheading>
            <flux:button class="mt-4" color="green" wire:click="createContact">
                Ajouter le premier contact
            </flux:button>
        </div>
    @endif

    <!-- Modale de détails du contact -->
    <flux:modal wire:model="showContactModal" variant="flyout" class="!min-w-2xl">
        @if(isset($selectedContact))
            <flux:heading size="lg">{{ $selectedContact->name }}</flux:heading>

            <div class="mt-6 space-y-4">
                <div>
                    <flux:subheading>Poste</flux:subheading>
                    <flux:text>{{ $selectedContact->job_area ?? 'Non renseigné' }}</flux:text>
                </div>

                @if($selectedContact->email)
                    <div>
                        <flux:subheading>Email</flux:subheading>
                        <flux:text>
                            <a href="mailto:{{ $selectedContact->email }}" class="text-blue-600 hover:underline">
                                {{ $selectedContact->email }}
                            </a>
                        </flux:text>
                    </div>
                @endif

                @if($selectedContact->phone)
                    <div>
                        <flux:subheading>Téléphone</flux:subheading>
                        <flux:text>
                            <a href="tel:{{ $selectedContact->phone }}" class="text-blue-600 hover:underline">
                                {{ $selectedContact->phone }}
                            </a>
                        </flux:text>
                    </div>
                @endif

                <div>
                    <flux:subheading>Ajouté le</flux:subheading>
                    <flux:text>{{ $selectedContact->created_at->format('d/m/Y à H:i') }}</flux:text>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <flux:button variant="outline" wire:click="closeModal">
                    Fermer
                </flux:button>
                <flux:button color="blue" wire:click="editContact({{ $selectedContact->id }})">
                    Modifier
                </flux:button>
            </div>
        @endif
    </flux:modal>

    <!-- Modale d'édition/création -->
    <flux:modal wire:model="showEditModal" class="max-w-lg !min-w-lg">
        <flux:heading size="lg">
            {{ isset($selectedContact) ? 'Modifier le contact' : 'Nouveau contact' }}
        </flux:heading>

        <form wire:submit="saveContact" class="mt-6 space-y-4">
            <flux:input
                wire:model="name"
                label="Nom complet *"
                placeholder="Nom et prénom du contact"
                required
            />

            <flux:input
                wire:model="email"
                label="Email"
                type="email"
                placeholder="contact@entreprise.com"
            />

            <flux:input
                wire:model="phone"
                label="Téléphone"
                placeholder="01 23 45 67 89"
            />

            <flux:input
                wire:model="job_area"
                label="Poste/Fonction"
                placeholder="Directeur, Chef de projet, etc."
            />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button type="button" variant="outline" wire:click="closeModal">
                    Annuler
                </flux:button>
                <flux:button type="submit" color="blue">
                    {{ isset($selectedContact) ? 'Modifier' : 'Créer' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
