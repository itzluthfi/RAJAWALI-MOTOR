@php
    $menipis = [
        ['kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'stok' => 3, 'min' => 10],
        ['kode' => 'KMPRMVAR', 'nama' => 'KAMPAS REM VARIO', 'stok' => 2, 'min' => 8],
        ['kode' => 'BUSINGKC', 'nama' => 'BUSI NGK CPR8EA', 'stok' => 5, 'min' => 15],
    ];
@endphp
<x-app-layout title="Stok Menipis">
    <x-filter-bar class="no-print">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-600 text-xs font-bold border border-amber-500/20">
                <x-icon name="triangle-alert" class="w-4 h-4" />
                Peringatan Stok Di Bawah Minimum
            </span>
        </div>
        <div class="ml-auto flex gap-2">
            <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-stok-menipis', 'Peringatan_Stok_Menipis', 'Peringatan Stok Barang Di Bawah Minimum')">
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
                <p class="text-sm font-bold text-ink mt-0.5">Laporan Peringatan Stok Barang Di Bawah Minimum</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Siwalankerto Timur No. 231A, Surabaya</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-stok-menipis">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kode</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nama Barang / Sparepart</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Sisa Stok</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Min</th>
                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menipis as $m)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-steel">{{ $m['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-ink">{{ $m['nama'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rajawali font-black">{{ $m['stok'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-steel">{{ $m['min'] }}</td>
                            <td class="px-4 py-3 text-center no-print">
                                <x-button as="a" href="{{ route('pembelian.index') }}" variant="secondary" class="text-xs px-2.5 py-1">Buat Pembelian</x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
