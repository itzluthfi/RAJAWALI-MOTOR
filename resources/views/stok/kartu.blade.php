@php
    $mutasi = [
        ['tanggal' => '20 Jul 2026', 'jenis' => 'Pembelian', 'dok' => 'PB2026000045', 'masuk' => 50, 'keluar' => 0, 'saldo' => 74, 'hpp' => 15000],
        ['tanggal' => '21 Jul 2026', 'jenis' => 'Penjualan', 'dok' => 'PJ2026000119', 'masuk' => 0, 'keluar' => 2, 'saldo' => 72, 'hpp' => 15000],
        ['tanggal' => '22 Jul 2026', 'jenis' => 'Penjualan', 'dok' => 'PJ2026000123', 'masuk' => 0, 'keluar' => 2, 'saldo' => 70, 'hpp' => 15000],
    ];
@endphp
<x-app-layout title="Kartu Stok">
    <x-filter-bar>
        <x-select label="Barang" class="min-w-64">
            <option>DISC PAD VARIO CBS</option>
            <option>OLI FEDERAL MATIC 1L</option>
        </x-select>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Jenis Mutasi</th>
                    <th class="text-left font-semibold px-4 py-2.5">No Dokumen</th>
                    <th class="text-right font-semibold px-4 py-2.5">Masuk</th>
                    <th class="text-right font-semibold px-4 py-2.5">Keluar</th>
                    <th class="text-right font-semibold px-4 py-2.5">Saldo</th>
                    <th class="text-right font-semibold px-4 py-2.5">HPP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasi as $m)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5">{{ $m['tanggal'] }}</td>
                        <td class="px-4 py-2.5">{{ $m['jenis'] }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $m['dok'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m['masuk'] ? 'text-lunas' : 'text-steel/40' }}">{{ $m['masuk'] ?: '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m['keluar'] ? 'text-rajawali' : 'text-steel/40' }}">{{ $m['keluar'] ?: '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-medium">{{ $m['saldo'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($m['hpp'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
