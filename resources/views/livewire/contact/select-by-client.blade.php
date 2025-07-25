<div>
    <div>
        <flux:heading size="lg">Gérer les accès des contacts</flux:heading>
        <flux:subheading>Gérer les accès des contacts pour le projet {{ $this->project->name }}.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 mt-4">
        @foreach($contacts as $contact)
            <livewire:contact.access
                :project="$project"
                :contact="$contact"
                :key="'contact-access-'.$contact->id" />
        @endforeach
    </div>

    @if($contacts->hasPages())
        <div class="mt-4">
            {{ $contacts->links() }}
        </div>
    @endif
</div>
