<x-app-layout title="Laporan {{ $judul }}">
    <!-- Filter Bar Interaktif -->
    <form method="GET" action="{{ route('laporan.tampil', $jenis) }}">
        <x-filter-bar class="no-print">
            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $dariTanggal }}" />
            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $sampaiTanggal }}" />
            <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Filter Laporan</x-button>
            <div class="ml-auto flex gap-2">
                <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-laporan', 'Laporan_{{ $jenis }}_{{ $dariTanggal }}', 'Laporan {{ $judul }}')">
                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                </x-button>
                <x-button as="a" href="{{ route('laporan.pdf', array_merge(['jenis' => $jenis], request()->query())) }}" target="_blank" variant="primary" class="bg-blue-600 hover:bg-blue-700 text-white">
                    <x-icon name="file-down" class="w-4 h-4" /> Download PDF
                </x-button>
                <x-button as="a" href="{{ route('laporan.index') }}" variant="secondary">
                    <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
                </x-button>
            </div>
        </x-filter-bar>
    </form>

    <!-- Kartu Ringkasan Metrik Laporan -->
    @if(count($ringkasan) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
            @foreach($ringkasan as $rk)
                <x-card class="bg-white border-l-4 border-l-rajawali p-4 shadow-sm">
                    <p class="text-xs font-bold text-steel uppercase tracking-wider">{{ $rk['label'] }}</p>
                    <p class="font-mono font-black text-xl mt-1 {{ $rk['warna'] ?? 'text-ink' }}">{{ $rk['nilai'] }}</p>
                </x-card>
            @endforeach
        </div>
    @endif

    <!-- Tabel Data Laporan Realtime -->
    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SIDOARJO</p>
                <p class="text-sm font-bold text-ink mt-0.5">Laporan {{ $judul }} · Periode {{ date('d M Y', strtotime($dariTanggal)) }} s/d {{ date('d M Y', strtotime($sampaiTanggal)) }}</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo | Realtime Database Report</p>
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
                        @foreach($headers as $idx => $hdr)
                            <th class="px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 font-bold {{ $idx >= count($headers) - 2 ? 'text-right' : 'text-left' }}">
                                {{ $hdr }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($rows as $row)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-medium">
                            <td class="px-4 py-3 text-ink font-bold">{{ $row['kolom1'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-steel">{{ $row['kolom2'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-ink font-mono text-xs">{{ $row['kolom3'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-ink font-mono text-xs">{{ $row['kolom4'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-xs">{{ $row['kolom5'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rajawali">{{ $row['kolom6'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="px-4 py-10 text-center text-steel italic">
                                <div class="max-w-sm mx-auto text-center space-y-2">
                                    <x-icon name="file-search" class="w-8 h-8 mx-auto text-steel/60" />
                                    <p class="font-bold text-sm text-ink">Tidak ada data transaksi pada rentang periode ini.</p>
                                    <p class="text-xs text-steel">Coba ubah filter tanggal pencarian di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
