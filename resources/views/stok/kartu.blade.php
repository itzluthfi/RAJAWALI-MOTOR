@php
    $daftarBarang = \App\Models\Barang::where('aktif', true)->orderBy('nama')->get();
    
    $barangId = request('barang_id', $daftarBarang->first()?->id);
    $dariTanggal = request('dari_tanggal', now()->startOfMonth()->toDateString());
    $sampaiTanggal = request('sampai_tanggal', now()->toDateString());

    $mutasi = [];
    $saldoAwal = 0;
    $barangTerpilih = null;

    if ($barangId) {
        $barangTerpilih = \App\Models\Barang::find($barangId);
        
        // Hitung Saldo Awal sebelum $dariTanggal
        $saldoAwal = (float) \App\Models\StokMutasi::query()
            ->where('barang_id', $barangId)
            ->where('tanggal', '<', $dariTanggal)
            ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
            ->value('saldo');

        // Ambil mutasi dalam periode
        $mutasiRaw = \App\Models\StokMutasi::query()
            ->where('barang_id', $barangId)
            ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $runningSaldo = $saldoAwal;
        foreach ($mutasiRaw as $m) {
            $runningSaldo = $runningSaldo + (float)$m->masuk - (float)$m->keluar;
            $mutasi[] = [
                'tanggal' => $m->tanggal->format('d M Y'),
                'jenis' => ucfirst($m->jenis_mutasi),
                'dok' => $m->no_dokumen,
                'masuk' => (float)$m->masuk,
                'keluar' => (float)$m->keluar,
                'saldo' => $runningSaldo,
                'hpp' => (float)$m->hpp,
                'keterangan' => $m->keterangan ?? '-',
            ];
        }
    }
@endphp
<x-app-layout title="Kartu Stok">
    <form method="GET" action="{{ route('stok.kartu') }}">
        <x-filter-bar class="no-print">
            <x-select name="barang_id" label="Pilih Barang" class="min-w-64" required>
                @foreach($daftarBarang as $b)
                    <option value="{{ $b->id }}" {{ $barangId == $b->id ? 'selected' : '' }}>{{ $b->kode }} - {{ $b->nama }}</option>
                @endforeach
            </x-select>
            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $dariTanggal }}" />
            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $sampaiTanggal }}" />
            <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
            <div class="ml-auto flex gap-2">
                <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-kartu-stok', 'Laporan_Kartu_Stok', 'Kartu Mutasi Stok Barang')">
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
                <p class="text-sm font-bold text-ink mt-0.5">Kartu Mutasi Stok Barang · {{ $barangTerpilih?->nama ?? '' }}</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-left sm:text-right font-bold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm font-bold" id="tabel-kartu-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Mutasi</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No. Dokumen</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Saldo</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">HPP (Rp)</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-slate-50 border-b border-line">
                        <td class="px-4 py-3 text-steel font-medium" colspan="3">SALDO AWAL PERIODE ({{ date('d-m-Y', strtotime($dariTanggal)) }})</td>
                        <td class="px-4 py-3 text-right font-mono" colspan="2">-</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-ink">{{ rtrim(rtrim(number_format($saldoAwal, 3, ',', ''), '0'), ',') }}</td>
                        <td class="px-4 py-3 text-right font-mono" colspan="2">-</td>
                    </tr>
                    @forelse($mutasi as $m)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 text-steel">{{ $m['tanggal'] }}</td>
                            <td class="px-4 py-3">{{ $m['jenis'] }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-rajawali">{{ $m['dok'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-lunas font-bold">{{ $m['masuk'] > 0 ? rtrim(rtrim(number_format($m['masuk'], 3, ',', ''), '0'), ',') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rajawali font-bold">{{ $m['keluar'] > 0 ? rtrim(rtrim(number_format($m['keluar'], 3, ',', ''), '0'), ',') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink font-bold">{{ rtrim(rtrim(number_format($m['saldo'], 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink">Rp {{ number_format($m['hpp'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-steel font-medium">{{ $m['keterangan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-steel font-medium">Tidak ada transaksi mutasi barang pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
