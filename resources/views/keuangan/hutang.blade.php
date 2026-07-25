@php
    $hutang = [
        ['supplier' => 'PT Astra Otoparts', 'total' => 3500000, 'jatuhTempo' => '05 Agu 2026', 'umur' => -14, 'lewat' => false],
    ];
@endphp
<x-app-layout title="Hutang">
    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Supplier</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total Hutang</th>
                    <th class="text-left font-semibold px-4 py-2.5">Jatuh Tempo Terdekat</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hutang as $h)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-medium">{{ $h['supplier'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($h['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5">{{ $h['jatuhTempo'] }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <x-button variant="primary" class="text-xs" onclick="window.toastSukses('Pembayaran hutang berhasil disimpan.')">Bayar</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
