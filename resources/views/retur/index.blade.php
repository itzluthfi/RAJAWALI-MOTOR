@php
    $retur = [
        ['no' => 'RJ2026000012', 'tanggal' => '22 Jul 2026', 'jenis' => 'Retur Penjualan', 'referensi' => 'PJ2026000098', 'total' => 40000],
        ['no' => 'RB2026000004', 'tanggal' => '19 Jul 2026', 'jenis' => 'Retur Pembelian', 'referensi' => 'PB2026000041', 'total' => 150000],
    ];
@endphp
<x-app-layout title="Retur">
    <x-filter-bar>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <div class="ml-auto flex gap-2">
            <x-button as="a" href="{{ route('retur.penjualan.create') }}" variant="secondary"><x-icon name="undo-2" class="w-4 h-4" /> Retur Penjualan</x-button>
            <x-button as="a" href="{{ route('retur.pembelian.create') }}" variant="primary"><x-icon name="undo-2" class="w-4 h-4" /> Retur Pembelian</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">No Retur</th>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Jenis</th>
                    <th class="text-left font-semibold px-4 py-2.5">Referensi</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($retur as $r)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $r['no'] }}</td>
                        <td class="px-4 py-2.5">{{ $r['tanggal'] }}</td>
                        <td class="px-4 py-2.5">{{ $r['jenis'] }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $r['referensi'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($r['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
