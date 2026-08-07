<x-app-layout title="Piutang Dagang">
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
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Total Outstanding Piutang</p>
                <p class="font-mono font-black text-2xl mt-1 text-rajawali">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
            </div>
            <form method="GET" action="{{ route('keuangan.piutang') }}" class="flex items-center gap-2">
                <x-input name="cari" value="{{ $filter['cari'] }}" placeholder="No Nota / Customer" class="w-64" />
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            </form>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-bold px-4 py-2.5">No Nota</th>
                    <th class="text-left font-bold px-4 py-2.5">Customer</th>
                    <th class="text-left font-bold px-4 py-2.5">Tanggal</th>
                    <th class="text-right font-bold px-4 py-2.5">Grand Total</th>
                    <th class="text-right font-bold px-4 py-2.5">Uang Muka (DP)</th>
                    <th class="text-right font-bold px-4 py-2.5">Sisa Piutang</th>
                    <th class="text-right font-bold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($piutangs as $p)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                        <td class="px-4 py-2.5 font-mono text-xs text-rajawali">{{ $p->nomor_nota }}</td>
                        <td class="px-4 py-2.5 text-ink font-bold">{{ $p->customer->nama }}</td>
                        <td class="px-4 py-2.5 text-steel font-medium">{{ $p->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-ink">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-lunas">Rp {{ number_format($p->uang_muka, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rajawali">Rp {{ number_format($p->total_akhir - $p->uang_muka, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <form method="POST" action="{{ route('keuangan.piutang.bayar', $p) }}" onsubmit="return confirm('Apakah Anda yakin ingin melunasi piutang nota {{ $p->nomor_nota }}?')">
                                @csrf
                                <x-button type="submit" variant="primary" class="text-xs">Bayar Lunas</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-steel font-medium">Tidak ada outstanding piutang saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
</x-app-layout>
