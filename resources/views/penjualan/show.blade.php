@php
    $items = [
        ['nama' => 'DISC PAD VARIO CBS', 'qty' => 2, 'harga' => 20000, 'diskon' => 0],
        ['nama' => 'OLI FEDERAL MATIC 1L', 'qty' => 1, 'harga' => 45000, 'diskon' => 5000],
    ];
    $total = collect($items)->sum(fn($i) => $i['qty'] * $i['harga'] - $i['diskon']);
@endphp
<x-app-layout title="Detail Nota {{ $id }}">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-display font-bold text-lg">{{ $id }}</h2>
            <p class="text-sm text-steel">22 Juli 2026 · Toko Jaya Motor · Kasir: Budi Santoso</p>
        </div>
        <div class="flex gap-2">
            <x-badge status="tempo">Tempo</x-badge>
            <x-button as="a" href="{{ route('cetak.faktur', $id) }}" target="_blank" variant="secondary"><x-icon name="printer" class="w-4 h-4" /> Cetak</x-button>
        </div>
    </div>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty</th>
                    <th class="text-right font-semibold px-4 py-2.5">Harga</th>
                    <th class="text-right font-semibold px-4 py-2.5">Diskon</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-4 py-2.5">{{ $i['nama'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $i['qty'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($i['harga'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($i['diskon'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-medium">{{ number_format($i['qty'] * $i['harga'] - $i['diskon'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex justify-end px-4 py-3 border-t border-line">
            <div class="text-right">
                <p class="text-xs text-steel uppercase tracking-wide">Grand Total</p>
                <p class="font-mono font-bold text-xl text-rajawali">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>
        </div>
    </x-card>

</x-app-layout>
