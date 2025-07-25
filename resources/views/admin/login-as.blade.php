<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Login As - Switch User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('original_user_id'))
                        <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 rounded">
                            <div class="flex items-center justify-between">
                                <p class="text-yellow-700 dark:text-yellow-300">
                                    <strong>Mode impersonation actif!</strong>
                                    Vous êtes connecté en tant que <strong>{{ auth()->user()->name }}</strong>
                                </p>
                                <form action="{{ route('admin.switch-back') }}" method="POST" class="inline">
                                    @csrf
                                    <flux:button variant="outline" size="sm" type="submit">
                                        ← Revenir à mon compte
                                    </flux:button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Choisir un utilisateur</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Cliquez sur un utilisateur pour vous connecter en tant que cette personne.
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($users as $user)
                            <div class="border dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium">{{ $user->name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        @if($user->role ?? null)
                                            <flux:badge size="sm" class="mt-1">
                                                {{ $user->role->value ?? 'N/A' }}
                                            </flux:badge>
                                        @endif
                                    </div>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.login-as.switch', $user) }}" method="POST">
                                            @csrf
                                            <flux:button size="sm" variant="primary" type="submit">
                                                Switch
                                            </flux:button>
                                        </form>
                                    @else
                                        <flux:badge color="green" size="sm">Vous</flux:badge>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
