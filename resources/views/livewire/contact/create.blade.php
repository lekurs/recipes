<div>
    <div>
        <flux:heading size="lg">Ajouter une nouveau contact</flux:heading>
        <flux:subheading>Ajouter un nouveau contact pour le client {{ $this->project->name }}.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 mt-4">
        <form wire:submit="createContactWithProject" class="space-y-4">
            <flux:field>
                <flux:label badge="Requis">Nom</flux:label>
                <flux:input
                    wire:model="name"
                    placeholder="Entrez le nom du contact"
                    required
                />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label badge="Requis">Email</flux:label>
                <flux:input
                    wire:model="email"
                    placeholder="Entrez l'email du contact"
                    required
                />
                <flux:error name="email" />
            </flux:field>
            <flux:field>
                <flux:label>Téléphone</flux:label>
                <flux:input
                    wire:model="phone"
                    placeholder="Entrez le téléphone du contact"
                />
                <flux:error name="phone" />
            </flux:field>
            <flux:field>
                <flux:label>Poste dans l'entreprise</flux:label>
                <flux:input
                    wire:model="job_area"
                    placeholder="Entrez le nom du poste du contact"
                    required
                />
                <flux:error name="job_area" />
            </flux:field>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">Annuler</flux:button>
                </flux:modal.close>

                <flux:button type="submit" icon="plus" variant="primary">
                    Créer le contact
                </flux:button>
            </div>
        </form>
    </div>
</div>
