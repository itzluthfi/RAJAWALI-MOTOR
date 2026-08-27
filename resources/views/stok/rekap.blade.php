<x-app-layout title="Rekap Stok">
    <form method="GET" action="{{ route('stok.rekap') }}">
        <x-filter-bar class="no-print">
            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $dariTanggal }}" />
            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $sampaiTanggal }}" />
            <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
            <div class="ml-auto flex gap-2">
                <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-rekap-stok', 'Laporan_Rekap_Stok', 'Laporan Rekapitulasi Stok Barang')">
                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                </x-button>
                <x-button variant="secondary" type="button" onclick="window.print()">
                    <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
                </x-button>
            </div>
        </x-filter-bar>
    </form>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Rekapitulasi Mutasi Stok Barang · Periode {{ date('d M Y', strtotime($dariTanggal)) }} s/d {{ date('d M Y', strtotime($sampaiTanggal)) }}</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
                <p class="text-xs font-bold text-emerald-700 mt-1">Total Nilai Persediaan: Rp {{ number_format($totalValuasiStok, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-rekap-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kode Barang</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nama Barang / Sparepart</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Awal</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Akhir</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nilai HPP Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($rekap as $r)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-rajawali">{{ $r['kode'] }}</td>
                            <td class="px-4 py-3 font-bold text-ink">{{ $r['nama'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-medium text-steel">{{ number_format($r['awal'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600">+{{ number_format($r['masuk'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rajawali">-{{ number_format($r['keluar'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink {{ $r['akhir'] <= 0 ? 'text-rajawali' : '' }}">{{ number_format($r['akhir'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($r['nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-steel italic">Tidak ada pergerakan stok pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
