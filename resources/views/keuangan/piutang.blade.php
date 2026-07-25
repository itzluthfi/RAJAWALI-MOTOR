@php
    $piutang = [
        ['customer' => 'Toko Jaya Motor', 'total' => 1200000, 'jatuhTempo' => '25 Jul 2026', 'umur' => 3, 'lewat' => false],
        ['customer' => 'Bengkel Sumber Rejeki', 'total' => 1140000, 'jatuhTempo' => '15 Jul 2026', 'umur' => 8, 'lewat' => true],
    ];
    $faktur = [
        ['no' => 'PJ2026000098', 'tanggal' => '11 Jul 2026', 'jatuhTempo' => '25 Jul 2026', 'jumlah' => 1200000, 'terbayar' => 0, 'sisa' => 1200000],
    ];
@endphp
<x-app-layout title="Piutang">
<div x-data="{ pilih: null }">
    <x-card :padded="false" class="mb-4">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Customer</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total Piutang</th>
                    <th class="text-left font-semibold px-4 py-2.5">Jatuh Tempo Terdekat</th>
                    <th class="text-right font-semibold px-4 py-2.5">Umur (hari)</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($piutang as $p)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 {{ $p['lewat'] ? 'bg-rajawali/5' : '' }}">
                        <td class="px-4 py-2.5 font-medium">{{ $p['customer'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($p['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 {{ $p['lewat'] ? 'text-rajawali font-medium' : '' }}">{{ $p['jatuhTempo'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $p['umur'] }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <x-button variant="primary" class="text-xs" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'bayar-piutang'}}))">Bayar</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <x-modal name="bayar-piutang" title="Bayar Piutang — Toko Jaya Motor" wide>
        <table class="w-full text-sm mb-4">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-y border-line">
                <tr>
                    <th class="w-8 px-3 py-2"></th>
                    <th class="text-left font-semibold px-3 py-2">No Faktur</th>
                    <th class="text-left font-semibold px-3 py-2">Jatuh Tempo</th>
                    <th class="text-right font-semibold px-3 py-2">Sisa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faktur as $f)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-3 py-2"><input type="checkbox" checked class="rounded border-line text-rajawali focus:ring-rajawali"></td>
                        <td class="px-3 py-2 font-mono text-xs">{{ $f['no'] }}</td>
                        <td class="px-3 py-2">{{ $f['jatuhTempo'] }}</td>
                        <td class="px-3 py-2 text-right font-mono">{{ number_format($f['sisa'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Jumlah Dibayar" type="number" mono value="1200000" />
            <x-select label="Cara Bayar">
                <option>Tunai</option>
                <option>Transfer Bank BCA</option>
            </x-select>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <x-button variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'bayar-piutang'}}))">Batal</x-button>
            <x-button variant="primary" onclick="window.toastSukses('Pembayaran piutang berhasil disimpan.'); window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'bayar-piutang'}}))">Simpan Pembayaran</x-button>
        </div>
    </x-modal>
</div>
</x-app-layout>
