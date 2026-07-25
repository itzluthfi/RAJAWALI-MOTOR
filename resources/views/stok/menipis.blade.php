@php
    $menipis = [
        ['kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'stok' => 3, 'min' => 10],
        ['kode' => 'KMPRMVAR', 'nama' => 'KAMPAS REM VARIO', 'stok' => 2, 'min' => 8],
        ['kode' => 'BUSINGKC', 'nama' => 'BUSI NGK CPR8EA', 'stok' => 5, 'min' => 15],
    ];
@endphp
<x-app-layout title="Stok Menipis">
    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Kode</th>
                    <th class="text-left font-semibold px-4 py-2.5">Nama Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Stok</th>
                    <th class="text-right font-semibold px-4 py-2.5">Minimum</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menipis as $m)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $m['kode'] }}</td>
                        <td class="px-4 py-2.5 font-medium">{{ $m['nama'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rajawali font-semibold">{{ $m['stok'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $m['min'] }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <x-button as="a" href="{{ route('pembelian.create') }}" variant="secondary" class="text-xs">Buat Pembelian</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
