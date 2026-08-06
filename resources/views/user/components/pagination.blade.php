{{--
    Pagination Component
    Usage: @include('user.components.pagination', ['paginator' => $items])
    or: <x-user.pagination :paginator="$items" />
--}}
@if ($paginator->hasPages())
<nav class="flex items-center justify-between" role="navigation" aria-label="Navigasi Halaman">
    <div class="text-sm text-gray-500">
        Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    </div>

    <div class="flex items-center gap-1">
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors">
                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        <div class="hidden sm:flex items-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-primary-600 rounded-lg">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Indikator Halaman Mobile --}}
        <span class="sm:hidden inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Tombol Selanjutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors">
                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
            </a>
        @else
            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
            </span>
        @endif
    </div>
</nav>
@endif
