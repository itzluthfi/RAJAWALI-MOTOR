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
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Kartu Mutasi Stok Barang · {{ $barangTerpilih?->nama ?? '' }}</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-left sm:text-right font-bold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
                <p class="text-xs text-steel mt-1">Saldo Awal Periode: <span class="font-mono text-ink font-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</span></p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-kartu-stok">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Jenis Mutasi</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No. Dokumen</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Saldo Akhir</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <tr class="bg-canvas font-bold border-b border-line">
                        <td colspan="5" class="px-4 py-2.5 text-steel text-xs uppercase">Saldo Awal Sebelum Periode:</td>
                        <td class="px-4 py-2.5 text-right font-mono text-ink">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @forelse($mutasi as $m)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 text-steel text-xs">{{ $m['tanggal'] }}</td>
                            <td class="px-4 py-3 font-bold text-ink">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $m['masuk'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $m['jenis'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-rajawali font-bold">{{ $m['dok'] }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600">{{ $m['masuk'] > 0 ? '+' . number_format($m['masuk'], 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rajawali">{{ $m['keluar'] > 0 ? '-' . number_format($m['keluar'], 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-black text-ink">{{ number_format($m['saldo'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-steel text-xs">{{ $m['keterangan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-steel italic">Tidak ada transaksi mutasi stok untuk barang ini pada periode terpilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
