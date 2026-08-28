@props(['paginator'])

@if(isset($paginator) && method_exists($paginator, 'total') && $paginator->total() > 0)
    @php
        $currentPerPage = (int) request('per_page', $paginator->perPage());
        $perPageOptions = [10, 15, 25, 50, 100];
        if (!in_array($currentPerPage, $perPageOptions)) {
            $perPageOptions[] = $currentPerPage;
            sort($perPageOptions);
        }
    @endphp
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-line bg-surface text-xs font-bold text-slate-700">
        {{-- BAGIAN KIRI: INFO JUMLAH DATA --}}
        <div class="flex items-center gap-2 flex-wrap text-center sm:text-left">
            <span class="text-steel font-medium">
                Menampilkan
                <strong class="font-mono text-ink font-black">{{ $paginator->firstItem() ?? 0 }}</strong>
                sampai
                <strong class="font-mono text-ink font-black">{{ $paginator->lastItem() ?? 0 }}</strong>
                dari
                <strong class="font-mono text-rajawali font-black">{{ number_format($paginator->total(), 0, ',', '.') }}</strong>
                total data
            </span>
        </div>

        {{-- BAGIAN TENGAH: PILIHAN PER HALAMAN --}}
        <div class="flex items-center gap-1.5 no-print">
            <span class="text-steel text-[11px] font-semibold">Tampilkan:</span>
            <select
                onchange="
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', this.value);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                "
                class="text-xs font-black font-mono rounded-lg border border-slate-300 bg-white px-2 py-1 focus:ring-2 focus:ring-rajawali focus:outline-none cursor-pointer"
            >
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}" @selected($currentPerPage === $opt)>{{ $opt }} / hal</option>
                @endforeach
            </select>
        </div>

        {{-- BAGIAN KANAN: TOMBOL HALAMAN --}}
        @if($paginator->hasPages())
            <div class="flex items-center gap-1 flex-wrap justify-center no-print">
                {{-- Tombol Sebelumnya --}}
                @if($paginator->onFirstPage())
                    <span class="px-2.5 py-1.5 rounded-lg text-slate-400 bg-canvas cursor-not-allowed text-xs font-bold border border-line">
                        « Prev
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-white hover:bg-slate-100 hover:text-rajawali text-xs font-bold border border-slate-300 transition shadow-xs">
                        « Prev
                    </a>
                @endif

                {{-- Nomor Halaman --}}
                @php
                    $start = max(1, $paginator->currentPage() - 2);
                    $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $paginator->url(1) }}" class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-white hover:bg-slate-100 text-xs font-bold font-mono border border-slate-300 transition">1</a>
                    @if($start > 2)
                        <span class="px-1 text-steel font-mono">...</span>
                    @endif
                @endif

                @foreach($paginator->getUrlRange($start, $end) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 rounded-lg bg-rajawali text-white font-black font-mono text-xs shadow-xs">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-white hover:bg-slate-100 hover:text-rajawali text-xs font-bold font-mono border border-slate-300 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <span class="px-1 text-steel font-mono">...</span>
                    @endif
                    <a href="{{ $paginator->url($paginator->lastPage()) }}" class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-white hover:bg-slate-100 text-xs font-bold font-mono border border-slate-300 transition">{{ $paginator->lastPage() }}</a>
                @endif

                {{-- Tombol Berikutnya --}}
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-white hover:bg-slate-100 hover:text-rajawali text-xs font-bold border border-slate-300 transition shadow-xs">
                        Next »
                    </a>
                @else
                    <span class="px-2.5 py-1.5 rounded-lg text-slate-400 bg-canvas cursor-not-allowed text-xs font-bold border border-line">
                        Next »
                    </span>
                @endif
            </div>
        @endif
    </div>
@elseif(isset($paginator) && method_exists($paginator, 'total') && $paginator->total() === 0)
    <div class="px-4 py-3 border-t border-line bg-surface text-xs text-steel font-bold text-center italic">
        Total: 0 data ditemukan.
    </div>
@endif
