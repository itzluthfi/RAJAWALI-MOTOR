@php
    $rekap = [
        ['kode' => 'DISVCBSTK', 'nama' => 'DISC PAD VARIO CBS', 'awal' => 26, 'masuk' => 50, 'keluar' => 6, 'akhir' => 70, 'nilai' => 1050000],
        ['kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'awal' => 15, 'masuk' => 20, 'keluar' => 32, 'akhir' => 3, 'nilai' => 108000],
    ];
@endphp
<x-app-layout title="Rekap Stok">
    <x-filter-bar class="no-print">
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
        <div class="ml-auto flex gap-2">
            <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-rekap-stok', 'Laporan_Rekap_Stok', 'Laporan Rekapitulasi Stok Barang')">
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
                <p class="text-sm font-bold text-ink mt-0.5">Laporan Rekapitulasi Stok Barang · Periode 01–22 Juli 2026</p>
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
            <table class="w-full text-sm" id="tabel-rekap-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kode</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nama Barang</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Awal</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Akhir</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nilai HPP (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $r)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs text-steel">{{ $r['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-ink">{{ $r['nama'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink">{{ $r['awal'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-lunas font-bold">{{ $r['masuk'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rajawali font-bold">{{ $r['keluar'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ $r['akhir'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ number_format($r['nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
