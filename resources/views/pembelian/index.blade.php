@php
    $pembelian = [
        ['no' => 'PB2026000045', 'tanggal' => '22 Jul 2026', 'supplier' => 'PT Astra Otoparts', 'faktur' => 'INV-9021', 'total' => 3500000, 'status' => 'tempo'],
        ['no' => 'PB2026000044', 'tanggal' => '20 Jul 2026', 'supplier' => 'CV Sinar Motor Jaya', 'faktur' => 'SJ-441', 'total' => 1200000, 'status' => 'lunas'],
    ];
@endphp
<x-app-layout title="Pembelian">
    <x-filter-bar>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-input label="Cari No / Supplier" class="min-w-64" />
        <div class="ml-auto">
            <x-button as="a" href="{{ route('pembelian.create') }}" variant="primary"><x-icon name="plus" class="w-4 h-4" /> Pembelian Baru</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">No Pembelian</th>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Supplier</th>
                    <th class="text-left font-semibold px-4 py-2.5">No Faktur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembelian as $p)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $p['no'] }}</td>
                        <td class="px-4 py-2.5">{{ $p['tanggal'] }}</td>
                        <td class="px-4 py-2.5">{{ $p['supplier'] }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $p['faktur'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($p['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5"><x-badge :status="$p['status']">{{ ucfirst($p['status']) }}</x-badge></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
