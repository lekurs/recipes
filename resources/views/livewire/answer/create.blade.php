<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <flux:icon.plus class="w-4 h-4 text-gray-600 dark:text-gray-400" />
            </div>
        </div>
        <div class="flex-1">
            <flux:textarea
                wire:model="contentAnswer"
                placeholder="Ajouter une réponse..."
                rows="3"
                class="w-full"
            />
            <div class="mt-2">
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Image(s) 5Mo maxi</p>
                <input
                    type="file"
                    wire:model.live="attachments"
                    multiple
                    accept="image/*,application/pdf"
                    class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />

                <!-- Aperçu des fichiers sélectionnés -->
                @if($attachments)
                    <div class="mt-2 space-y-1">
                        @foreach($attachments as $attachment)
                            <div class="text-xs text-gray-600 flex items-center">
                                <flux:icon.paper-clip class="w-3 h-3 mr-1" />
                                {{ $attachment->getClientOriginalName() }}
                                <span class="ml-2 text-gray-400">({{ round($attachment->getSize() / 1024, 1) }} Ko)</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Connecté en tant que {{ ucfirst(auth()->user()->role->value) }}
                </span>
                <flux:button
                    wire:click="addAnswer"
                    variant="primary"
                    size="sm"
                    icon="paper-airplane"
                >
                    Répondre
                </flux:button>
            </div>
        </div>
    </div>
</div>
