{{-- Wrapper toujours présent pour Livewire --}}
<div>
    @if($originalUser)
        <div class="flex items-center gap-2 px-3 py-1.5 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-full text-sm">

            <flux:icon.exclamation-triangle class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />

            <span class="text-yellow-800 dark:text-yellow-200 font-medium">
                {{ auth()->user()->name }}
            </span>

            <form action="{{ route('admin.switch-back') }}" method="POST" class="inline">
                @csrf
                <button
                    type="submit"
                    class="ml-1 text-xs bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 dark:hover:bg-yellow-700 px-2 py-0.5 rounded-full transition-colors text-yellow-800 dark:text-yellow-200"
                    title="Retour à {{ $originalUser->name }}"
                >
                    ← {{ $originalUser->name }}
                </button>
            </form>

        </div>
    @endif
</div>
