@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            {{-- Mobile Previous/Next --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    Précédent
                </span>
            @else
                <button wire:click="previousPage" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    Précédent
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    Suivant
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    Suivant
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    Affichage de
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    à
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    sur
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    résultats
                </p>
            </div>

            <div>
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <flux:button size="sm" variant="outline" disabled icon="chevron-left">
                            Précédent
                        </flux:button>
                    @else
                        <flux:button size="sm" variant="outline" wire:click="previousPage" icon="chevron-left">
                            Précédent
                        </flux:button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <flux:button size="sm" variant="filled">
                                        {{ $page }}
                                    </flux:button>
                                @else
                                    <flux:button size="sm" variant="outline" wire:click="gotoPage({{ $page }})">
                                        {{ $page }}
                                    </flux:button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <flux:button size="sm" variant="outline" wire:click="nextPage" icon-trailing="chevron-right">
                            Suivant
                        </flux:button>
                    @else
                        <flux:button size="sm" variant="outline" disabled icon-trailing="chevron-right">
                            Suivant
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
