@php
    $pembelian = [
        ['no' => 'PB2026000045', 'tanggal' => '22 Jul 2026', 'supplier' => 'PT Astra Otoparts', 'faktur' => 'INV-9021', 'total' => 3500000, 'status' => 'tempo'],
        ['no' => 'PB2026000044', 'tanggal' => '20 Jul 2026', 'supplier' => 'CV Sinar Motor Jaya', 'faktur' => 'SJ-441', 'total' => 1200000, 'status' => 'lunas'],
    ];
@endphp
<x-app-layout title="Pembelian">
    <x-filter-bar class="no-print">
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-input label="Cari No / Supplier" class="min-w-64" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
        <div class="ml-auto flex gap-2">
            <x-button type="button" variant="secondary" onclick="exportTableToExcel('tabel-pembelian', 'Laporan_Faktur_Pembelian', 'Daftar Faktur Pembelian Stok')">
                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
            </x-button>
            <x-button type="button" variant="secondary" onclick="cetakLaporanPdf()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
            </x-button>
            <x-button as="a" href="{{ route('pembelian.create') }}" variant="primary"><x-icon name="plus" class="w-4 h-4" /> Pembelian Baru</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Daftar Faktur Pembelian Stok Barang</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-pembelian">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Pembelian</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Supplier</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Faktur</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total (Rp)</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian as $p)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-rajawali">{{ $p['no'] }}</td>
                            <td class="px-4 py-3 text-steel">{{ $p['tanggal'] }}</td>
                            <td class="px-4 py-3 font-medium text-ink">{{ $p['supplier'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-ink">{{ $p['faktur'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ number_format($p['total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-badge :status="$p['status']">{{ ucfirst($p['status']) }}</x-badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
