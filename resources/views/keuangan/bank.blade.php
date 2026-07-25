@php
    $mutasi = [
        ['tanggal' => '21 Jul 2026', 'jenis' => 'masuk', 'jumlah' => 1140000, 'ket' => 'Transfer piutang Bengkel Sumber Rejeki'],
        ['tanggal' => '20 Jul 2026', 'jenis' => 'keluar', 'jumlah' => 1200000, 'ket' => 'Bayar hutang PT Astra Otoparts'],
    ];
@endphp
<x-app-layout title="Bank">
    <x-card class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <x-select class="mb-2 w-56">
                    <option>BCA — 123-456-7890</option>
                    <option>Mandiri — 998-877-6655</option>
                </x-select>
                <p class="text-xs font-semibold text-steel uppercase tracking-wide">Saldo Berjalan</p>
                <p class="font-mono font-bold text-2xl mt-1">Rp 28.640.000</p>
            </div>
            <x-button variant="primary" onclick="window.toastSukses('Mutasi bank berhasil disimpan.')"><x-icon name="plus" class="w-4 h-4" /> Transaksi Bank</x-button>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Keterangan</th>
                    <th class="text-right font-semibold px-4 py-2.5">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasi as $m)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5">{{ $m['tanggal'] }}</td>
                        <td class="px-4 py-2.5 text-steel">{{ $m['ket'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m['jenis'] === 'masuk' ? 'text-lunas' : 'text-rajawali' }}">
                            {{ $m['jenis'] === 'masuk' ? '+' : '-' }}{{ number_format($m['jumlah'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
