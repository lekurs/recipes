<div
    x-data="{
        showAccessForm: false,
        init() {
            $wire.on('access-action-completed', () => {
                this.showAccessForm = false;
            });
        }
    }"
    class="border border-gray-200 dark:border-gray-600 rounded-lg h-full flex flex-col"
>
    <!-- Card du contact - cliquable -->
    <div
        class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex-1 flex flex-col"
        @click="showAccessForm = !showAccessForm"
    >
        <div class="flex items-start justify-between mb-2">
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 dark:text-white truncate">
                    {{ $contact->name }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">
                    {{ $contact->email }}
                </div>
            </div>

            <!-- Test avec l'icône Flux - c'est peut-être ça le problème -->
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0 ml-2"
                 :class="{ 'rotate-180': showAccessForm }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        @if($contact->phone)
            <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                {{ $contact->phone }}
            </div>
        @endif

        @if($contact->job_area)
            <div class="text-sm text-gray-500 dark:text-gray-500 mt-1 truncate">
                {{ $contact->job_area }}
            </div>
        @endif

        <!-- Statut d'accès avec les badges Flux -->
        @php
            $projectContact = $project->contacts->find($contact->id);
        @endphp
        <div class="mt-auto pt-2">
            @if($projectContact)
                @if($project->hasValidAccess($contact))
                    <flux:badge color="green" size="sm">
                        Accès valide
                    </flux:badge>
                    @if($projectContact->pivot->expires_at)
                        <div class="text-xs text-gray-500 mt-1 truncate">
                            Expire le {{ \Carbon\Carbon::parse($projectContact->pivot->expires_at)->format('d/m/Y') }}
                        </div>
                    @endif
                @else
                    <flux:badge color="red" size="sm">
                        Accès expiré
                    </flux:badge>
                @endif
            @else
                <flux:badge color="gray" size="sm">
                    Pas d'accès
                </flux:badge>
            @endif
        </div>
    </div>

    <!-- Section masquée pour gérer l'accès -->
    <div
        x-show="showAccessForm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="border-t border-gray-200 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-800"
    >
        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
            Gérer l'accès pour {{ $contact->name }}
        </h5>

        <div class="space-y-3">
            @if(!($projectContact && $project->hasValidAccess($contact)))
                <!-- Le champ durée n'apparaît que si on va donner un accès -->
                <flux:field>
                    <flux:label>Durée d'accès (en jours)</flux:label>
                    <flux:input
                        type="number"
                        wire:model.live="days"
                        min="1"
                        max="365"
                        placeholder="30"
                    />
                </flux:field>
            @endif

            <div class="flex flex-col gap-2">
                @if($projectContact && $project->hasValidAccess($contact))
                    <!-- Si l'accès est valide, on propose seulement la révocation -->
                    <flux:button
                        size="sm"
                        variant="danger"
                        wire:click="revokeAccess"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50"
                        icon="x-mark"
                        class="w-full"
                    >
                        <span wire:loading.remove wire:target="revokeAccess">Révoquer accès</span>
                        <span wire:loading wire:target="revokeAccess">Traitement...</span>
                    </flux:button>
                @else
                    <!-- Si pas d'accès ou accès expiré, on propose de donner l'accès -->
                    <flux:button
                        size="sm"
                        variant="primary"
                        wire:click="grantAccess"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50"
                        icon="key"
                        class="w-full"
                    >
                        <span wire:loading.remove wire:target="grantAccess">Donner accès</span>
                        <span wire:loading wire:target="grantAccess">Traitement...</span>
                    </flux:button>
                @endif

                <!-- Le bouton annuler est toujours présent -->
                <flux:button
                    size="sm"
                    variant="ghost"
                    @click="showAccessForm = false"
                    class="w-full"
                >
                    Annuler
                </flux:button>
            </div>
        </div>
    </div>
</div>
