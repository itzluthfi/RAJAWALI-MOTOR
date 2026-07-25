@php
    $judul = ucwords(str_replace('-', ' ', $jenis));
    $tampilkanHpp = auth()->user()->peran === 'owner';
    $data = [
        ['label' => 'DISC PAD VARIO CBS', 'qty' => 24, 'total' => 480000, 'hpp' => 360000, 'laba' => 120000],
        ['label' => 'OLI FEDERAL MATIC 1L', 'qty' => 18, 'total' => 810000, 'hpp' => 648000, 'laba' => 162000],
    ];
@endphp
<x-app-layout title="Laporan {{ $judul }}">

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
        <div class="p-4 border-b border-line">
            <p class="font-display font-bold text-base">RAJAWALI MOTOR</p>
            <p class="text-sm text-steel">Laporan {{ $judul }} · Periode 01–22 Juli 2026</p>
            <p class="text-xs text-steel/70 font-mono">Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total</th>
                    @if($tampilkanHpp)
                        <th class="text-right font-semibold px-4 py-2.5">HPP</th>
                        <th class="text-right font-semibold px-4 py-2.5">Laba</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-4 py-2.5">{{ $d['label'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $d['qty'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ number_format($d['total'], 0, ',', '.') }}</td>
                        @if($tampilkanHpp)
                            <td class="px-4 py-2.5 text-right font-mono">{{ number_format($d['hpp'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono text-lunas">{{ number_format($d['laba'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
