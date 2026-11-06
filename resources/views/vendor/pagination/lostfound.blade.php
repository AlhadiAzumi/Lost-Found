@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Paginasi">
        {{-- Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="btn" aria-disabled="true">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn" rel="prev" aria-label="Halaman sebelumnya">‹</a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="btn btn--ghost" aria-hidden="true">{{ $element }}</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn--primary" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn--ghost">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Berikutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn" rel="next" aria-label="Halaman berikutnya">›</a>
        @else
            <span class="btn" aria-disabled="true">›</span>
        @endif
    </nav>
@endif
