@php
    $mutasi = [
        ['tanggal' => '20 Jul 2026', 'jenis' => 'Pembelian', 'dok' => 'PB2026000045', 'masuk' => 50, 'keluar' => 0, 'saldo' => 74, 'hpp' => 15000],
        ['tanggal' => '21 Jul 2026', 'jenis' => 'Penjualan', 'dok' => 'PJ2026000119', 'masuk' => 0, 'keluar' => 2, 'saldo' => 72, 'hpp' => 15000],
        ['tanggal' => '22 Jul 2026', 'jenis' => 'Penjualan', 'dok' => 'PJ2026000123', 'masuk' => 0, 'keluar' => 2, 'saldo' => 70, 'hpp' => 15000],
    ];
@endphp
<x-app-layout title="Kartu Stok">
    <x-filter-bar class="no-print">
        <x-select label="Barang" class="min-w-64">
            <option>DISC PAD VARIO CBS</option>
            <option>OLI FEDERAL MATIC 1L</option>
        </x-select>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
        <div class="ml-auto flex gap-2">
            <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-kartu-stok', 'Laporan_Kartu_Stok', 'Kartu Mutasi Stok Barang')">
                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
            </x-button>
            <x-button variant="secondary" type="button" onclick="cetakLaporanPdf()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
            </x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Kartu Mutasi Stok Barang · Periode 01–22 Juli 2026</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Siwalankerto Timur No. 231A, Surabaya</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-kartu-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Jenis Mutasi</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Dokumen</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Saldo</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">HPP (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mutasi as $m)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-medium text-ink">{{ $m['tanggal'] }}</td>
                            <td class="px-4 py-3 text-steel">{{ $m['jenis'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs font-bold text-ink">{{ $m['dok'] }}</td>
                            <td class="px-4 py-3 text-right font-mono {{ $m['masuk'] ? 'text-lunas font-bold' : 'text-steel/40' }}">{{ $m['masuk'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono {{ $m['keluar'] ? 'text-rajawali font-bold' : 'text-steel/40' }}">{{ $m['keluar'] ?: '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ $m['saldo'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ number_format($m['hpp'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
