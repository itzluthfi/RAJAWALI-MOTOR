@php
    $judul = ucwords(str_replace('-', ' ', $jenis));
    $tampilkanHpp = auth()->user()->peran === 'owner';
    $data = [
        ['label' => 'DISC PAD VARIO CBS', 'qty' => 24, 'total' => 480000, 'hpp' => 360000, 'laba' => 120000],
        ['label' => 'OLI FEDERAL MATIC 1L', 'qty' => 18, 'total' => 810000, 'hpp' => 648000, 'laba' => 162000],
    ];
@endphp
<x-app-layout title="Laporan {{ $judul }}">

    <x-filter-bar class="no-print">
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
        <div class="ml-auto flex gap-2">
            <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-laporan', 'Laporan_{{ $jenis }}', 'Laporan {{ $judul }}')">
                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
            </x-button>
            <x-button variant="secondary" type="button" onclick="cetakLaporanPdf()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
            </x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Laporan {{ $judul }} · Periode 01–22 Juli 2026</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-laporan">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Barang / Keterangan</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Qty</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total (Rp)</th>
                        @if($tampilkanHpp)
                            <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">HPP (Rp)</th>
                            <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Laba Kotor (Rp)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-medium text-ink">{{ $d['label'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ $d['qty'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ number_format($d['total'], 0, ',', '.') }}</td>
                            @if($tampilkanHpp)
                                <td class="px-4 py-3 text-right font-mono text-steel">{{ number_format($d['hpp'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-lunas">{{ number_format($d['laba'], 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
