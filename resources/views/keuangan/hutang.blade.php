<x-app-layout title="Hutang Dagang / Supplier">
<div class="-m-3 p-3">
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm font-bold">
            {{ session('sukses') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <x-card class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Total Outstanding Hutang (Extern)</p>
                <p class="font-mono font-black text-2xl mt-1 text-rajawali">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
            </div>
            <form method="GET" action="{{ route('keuangan.hutang') }}" class="flex items-center gap-2">
                <x-input name="cari" value="{{ $filter['cari'] }}" placeholder="No Dokumen / Supplier" class="w-64" />
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            </form>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-bold px-4 py-2.5">No Dokumen</th>
                    <th class="text-left font-bold px-4 py-2.5">Supplier / Bengkel Rekanan</th>
                    <th class="text-left font-bold px-4 py-2.5">Tanggal</th>
                    <th class="text-right font-bold px-4 py-2.5">Estimasi Kembali</th>
                    <th class="text-right font-bold px-4 py-2.5">Total Tagihan</th>
                    <th class="text-right font-bold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hutangs as $h)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                        <td class="px-4 py-2.5 font-mono text-xs text-rajawali">{{ $h->nomor_dokumen }}</td>
                        <td class="px-4 py-2.5 text-ink font-bold">{{ $h->supplier->nama ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-steel font-medium">{{ $h->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-4 py-2.5 text-right text-steel font-medium">{{ $h->tanggal_kembali?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rajawali">Rp {{ number_format($h->grand_total_supplier, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <form method="POST" action="{{ route('keuangan.hutang.bayar', $h) }}" onsubmit="return confirm('Apakah Anda yakin ingin melunasi hutang service rekanan {{ $h->nomor_dokumen }}?')">
                                @csrf
                                <x-button type="submit" variant="primary" class="text-xs">Bayar Lunas</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-steel font-medium">Tidak ada outstanding hutang supplier saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
</x-app-layout>
