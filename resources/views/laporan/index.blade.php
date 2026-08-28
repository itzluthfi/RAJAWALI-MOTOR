@php
    $laporan = [
        'Penjualan' => ['penjualan-harian', 'penjualan-per-barang', 'penjualan-per-customer'],
        'Pembelian' => ['pembelian-harian', 'pembelian-per-supplier'],
        'Stok' => ['rekap-stok', 'kartu-stok', 'stok-menipis'],
        'Keuangan' => ['laba-rugi-kotor', 'piutang', 'hutang', 'kas-bank'],
        'Service' => ['service-bengkel'],
    ];

    $labelMap = [
        'kas-bank' => 'Arus Kas Toko',
        'service-bengkel' => 'Laporan Servis Bengkel',
    ];
@endphp
<x-app-layout title="Laporan">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($laporan as $kelompok => $items)
            <x-card class="shadow-sm border border-slate-200/80">
                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-line">
                    <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">{{ $kelompok }}</h3>
                </div>
                <ul class="space-y-1.5">
                    @foreach($items as $jenis)
                        <li>
                            <a href="{{ route('laporan.tampil', $jenis) }}" class="flex items-center justify-between text-sm font-bold text-slate-700 hover:text-rajawali px-3 py-2 rounded-xl hover:bg-slate-100/80 transition duration-150 group">
                                <span>{{ $labelMap[$jenis] ?? ucwords(str_replace('-', ' ', $jenis)) }}</span>
                                <x-icon name="chevron-right" class="w-4 h-4 text-steel group-hover:text-rajawali transition transform group-hover:translate-x-0.5" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endforeach
    </div>
</x-app-layout>
