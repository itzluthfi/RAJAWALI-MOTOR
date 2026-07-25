@if($paginator->hasPages())
    <div class="flex items-center justify-between px-4 py-3 border-t border-line text-sm">
        <p class="text-steel">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>
        <div class="flex items-center gap-1">
            @if($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-md text-steel/40 cursor-not-allowed">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-md text-steel hover:bg-canvas hover:text-ink transition duration-150">Sebelumnya</a>
            @endif

            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded-md bg-rajawali text-white font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-md text-steel hover:bg-canvas hover:text-ink transition duration-150">{{ $page }}</a>
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-md text-steel hover:bg-canvas hover:text-ink transition duration-150">Berikutnya</a>
            @else
                <span class="px-3 py-1.5 rounded-md text-steel/40 cursor-not-allowed">Berikutnya</span>
            @endif
        </div>
    </div>
@endif
