@php
    $laporan = [
        'Penjualan' => ['penjualan-harian', 'penjualan-per-barang', 'penjualan-per-customer', 'penjualan-per-sales'],
        'Pembelian' => ['pembelian-harian', 'pembelian-per-supplier'],
        'Stok' => ['rekap-stok', 'kartu-stok', 'stok-menipis'],
        'Keuangan' => ['laba-rugi-kotor', 'piutang', 'hutang', 'kas-bank'],
        'Service' => ['service-belum-selesai', 'komisi-montir'],
    ];
@endphp
<x-app-layout title="Laporan">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($laporan as $kelompok => $items)
            <x-card>
                <h3 class="font-display font-semibold text-sm mb-3">{{ $kelompok }}</h3>
                <ul class="space-y-1">
                    @foreach($items as $jenis)
                        <li>
                            <a href="{{ route('laporan.tampil', $jenis) }}" class="flex items-center justify-between text-sm text-steel hover:text-rajawali px-2 py-1.5 rounded-md hover:bg-canvas transition duration-150">
                                {{ ucwords(str_replace('-', ' ', $jenis)) }}
                                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endforeach
    </div>
</x-app-layout>
