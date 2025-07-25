<div class="relative">
    @if(app()->environment(['local', 'staging']) && auth()->user()->role?->value === 'admin')

        {{-- Mode impersonation actif --}}
        @if(session()->has('original_user_id'))
            <div class="flex items-center gap-2 p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg border border-yellow-300 dark:border-yellow-700">
                <flux:icon.exclamation-triangle class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                <span class="text-sm text-yellow-800 dark:text-yellow-200">
                    Mode impersonation: <strong>{{ auth()->user()->name }}</strong>
                </span>
                <button
                    wire:click="switchBack"
                    class="ml-auto text-xs bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 dark:hover:bg-yellow-700 px-2 py-1 rounded transition-colors"
                >
                    ← Revenir
                </button>
            </div>
        @else
            {{-- CTA de recherche rapide --}}
            <div class="flex items-center gap-2">
                <flux:input
                    wire:model.live="search"
                    placeholder="Login as... (tapez un nom)"
                    class="text-sm"
                    size="sm"
                />
                <flux:link
                    :href="route('admin.login-as')"
                    wire:navigate
                    class="text-xs whitespace-nowrap"
                >
                    Voir tous
                </flux:link>
            </div>

            {{-- Dropdown des résultats --}}
            @if(strlen($search) >= 2 && $users->count() > 0)
                <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                    @foreach($users as $user)
                        <button
                            wire:click="loginAs({{ $user->id }})"
                            class="w-full px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-sm">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                </div>
                                @if($user->role ?? null)
                                    <flux:badge size="xs">{{ $user->role->value }}</flux:badge>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            @if(strlen($search) >= 2 && $users->count() === 0)
                <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-50 p-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun utilisateur trouvé</p>
                </div>
            @endif
        @endif

    @endif
</div>
