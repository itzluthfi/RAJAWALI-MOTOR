@php
    $dariTanggal = request('dari_tanggal', now()->startOfMonth()->toDateString());
    $sampaiTanggal = request('sampai_tanggal', now()->toDateString());

    $daftarBarang = \App\Models\Barang::where('aktif', true)->orderBy('nama')->get();
    $rekap = [];

    foreach ($daftarBarang as $b) {
        // Hitung stok awal sebelum dariTanggal
        $stokAwal = (float) \App\Models\StokMutasi::query()
            ->where('barang_id', $b->id)
            ->where('tanggal', '<', $dariTanggal)
            ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
            ->value('saldo');

        // Hitung total masuk periode
        $totalMasuk = (float) \App\Models\StokMutasi::query()
            ->where('barang_id', $b->id)
            ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
            ->sum('masuk');

        // Hitung total keluar periode
        $totalKeluar = (float) \App\Models\StokMutasi::query()
            ->where('barang_id', $b->id)
            ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
            ->sum('keluar');

        $stokAkhir = $stokAwal + $totalMasuk - $totalKeluar;
        $nilaiHpp = $stokAkhir * (float) $b->hpp;

        // Skip jika tidak ada pergerakan dan stok kosong
        if ($stokAwal == 0 && $totalMasuk == 0 && $totalKeluar == 0 && $stokAkhir == 0) {
            continue;
        }

        $rekap[] = [
            'kode' => $b->kode,
            'nama' => $b->nama,
            'awal' => $stokAwal,
            'masuk' => $totalMasuk,
            'keluar' => $totalKeluar,
            'akhir' => $stokAkhir,
            'nilai' => $nilaiHpp,
        ];
    }
@endphp
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
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Laporan Rekapitulasi Stok Barang · Periode {{ date('d M Y', strtotime($dariTanggal)) }}–{{ date('d M Y', strtotime($sampaiTanggal)) }}</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Siwalankerto Timur No. 231A, Surabaya</p>
            </div>
            <div class="text-left sm:text-right font-bold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm font-bold" id="tabel-rekap-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kode</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nama Barang</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Awal</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Akhir</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nilai HPP (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $r)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs text-steel">{{ $r['kode'] }}</td>
                            <td class="px-4 py-3 font-bold text-ink">{{ $r['nama'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink">{{ rtrim(rtrim(number_format($r['awal'], 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-lunas font-bold">{{ $r['masuk'] > 0 ? rtrim(rtrim(number_format($r['masuk'], 3, ',', ''), '0'), ',') : '0' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rajawali font-bold">{{ $r['keluar'] > 0 ? rtrim(rtrim(number_format($r['keluar'], 3, ',', ''), '0'), ',') : '0' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ rtrim(rtrim(number_format($r['akhir'], 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($r['nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-steel font-medium">Tidak ada pergerakan stok barang pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
