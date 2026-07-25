@php
    $rekap = [
        ['kode' => 'DISVCBSTK', 'nama' => 'DISC PAD VARIO CBS', 'awal' => 26, 'masuk' => 50, 'keluar' => 6, 'akhir' => 70, 'nilai' => 1050000],
        ['kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'awal' => 15, 'masuk' => 20, 'keluar' => 32, 'akhir' => 3, 'nilai' => 108000],
    ];
@endphp
<x-app-layout title="Rekap Stok">
    <x-filter-bar>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
        <div class="ml-auto flex gap-2">
            <x-button variant="secondary"><x-icon name="file-spreadsheet" class="w-4 h-4" /> Export Excel</x-button>
            <x-button variant="secondary"><x-icon name="printer" class="w-4 h-4" /> Cetak PDF</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Kode</th>
                    <th class="text-left font-semibold px-4 py-2.5">Nama Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Stok Awal</th>
                    <th class="text-right font-semibold px-4 py-2.5">Masuk</th>
                    <th class="text-right font-semibold px-4 py-2.5">Keluar</th>
                    <th class="text-right font-semibold px-4 py-2.5">Stok Akhir</th>
                    <th class="text-right font-semibold px-4 py-2.5">Nilai (HPP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekap as $r)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $r['kode'] }}</td>
                        <td class="px-4 py-2.5">{{ $r['nama'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $r['awal'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $r['masuk'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $r['keluar'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-medium">{{ $r['akhir'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($r['nilai'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
