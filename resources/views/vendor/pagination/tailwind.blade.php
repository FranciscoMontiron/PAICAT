@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        {{-- Mobile pagination --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <span class="text-sm text-gray-700">
                <span class="font-medium">{{ $paginator->currentPage() }}</span> / <span class="font-medium">{{ $paginator->lastPage() }}</span>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop pagination --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-semibold">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1">
                    {{-- First Page Link --}}
                    @if (!$paginator->onFirstPage())
                        <a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors" title="Primera página">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-lg" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors" aria-label="Anterior">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 border border-indigo-600 rounded-lg cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors" aria-label="Ir a página {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors" aria-label="Siguiente">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Siguiente">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-lg" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </span>
                    @endif

                    {{-- Last Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors" title="Última página">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
