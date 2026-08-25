@php
    $peranSaya = auth()->user()->peran;
    $omzetHariIni = $omzetHariIni ?? 0;
    $notaHariIniCount = $notaHariIniCount ?? 0;
    $growth = $growth ?? 0;
    $labaKotorHariIni = $labaKotorHariIni ?? 0;
    $pembelianHariIni = $pembelianHariIni ?? 0;
    $totalPiutang = $totalPiutang ?? 0;
    $totalHutang = $totalHutang ?? 0;
    $notaSayaHariIniCount = $notaSayaHariIniCount ?? 0;
    $omzetSayaHariIni = $omzetSayaHariIni ?? 0;
    $penjualanBulanan = $penjualanBulanan ?? [];
    $auditLogs = $auditLogs ?? collect();
    $piutangJatuhTempoList = $piutangJatuhTempoList ?? collect();
    $hutangJatuhTempoList = $hutangJatuhTempoList ?? collect();
    $transaksiSayaTerakhir = $transaksiSayaTerakhir ?? collect();
    $stokMenipisList = $stokMenipisList ?? collect();
    $serviceAktif = $serviceAktif ?? collect();
@endphp
<x-app-layout title="Dashboard {{ ucfirst($peranSaya) }}">



    <!-- METRIK UTAMA KASIR -->
    @if($peranSaya === 'kasir')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <x-card class="bg-white border-l-4 border-l-rajawali">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-steel uppercase tracking-wider">Omzet Kasir Saya Hari Ini</p>
                        <p class="font-mono font-black text-2xl mt-1 text-ink">Rp {{ number_format($omzetSayaHariIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-2.5 bg-rajawali/10 rounded-xl text-rajawali">
                        <x-icon name="wallet" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs text-steel mt-2">Total pendapatan dari transaksi Anda</p>
            </x-card>

            <x-card class="bg-white border-l-4 border-l-emerald-600">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-steel uppercase tracking-wider">Nota Ditangani Saya</p>
                        <p class="font-mono font-black text-2xl mt-1 text-ink">{{ $notaSayaHariIniCount }} <span class="text-sm text-steel font-normal">Nota</span></p>
                    </div>
                    <div class="p-2.5 bg-emerald-100 rounded-xl text-emerald-700">
                        <x-icon name="receipt" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs text-steel mt-2">Transaksi berhasil diproses hari ini</p>
            </x-card>

            <x-card class="bg-white border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-steel uppercase tracking-wider">Service Aktif Bengkel</p>
                        <p class="font-mono font-black text-2xl mt-1 text-ink">{{ $serviceAktif->count() }} <span class="text-sm text-steel font-normal">Motor</span></p>
                    </div>
                    <div class="p-2.5 bg-amber-100 rounded-xl text-amber-700">
                        <x-icon name="wrench" class="w-6 h-6" />
                    </div>
                </div>
                <p class="text-xs text-steel mt-2">Dalam pengerjaan / siap diambil</p>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transaksi Terbaru Kasir Saya -->
            <x-card :padded="false" class="lg:col-span-2 overflow-hidden">
                <div class="p-4 border-b border-line bg-canvas flex justify-between items-center">
                    <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                        <x-icon name="history" class="w-4 h-4 text-rajawali" /> 5 Transaksi Terbaru Saya Hari Ini
                    </h3>
                    <a href="{{ route('penjualan.index') }}" class="text-xs font-bold text-rajawali hover:underline">Lihat Semua Nota →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-steel text-xs uppercase tracking-wider border-b border-line">
                            <tr>
                                <th class="text-left px-4 py-2.5">No Nota</th>
                                <th class="text-left px-4 py-2.5">Customer</th>
                                <th class="text-right px-4 py-2.5">Total</th>
                                <th class="text-center px-4 py-2.5">Status</th>
                                <th class="text-right px-4 py-2.5">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse($transaksiSayaTerakhir as $t)
                                <tr class="hover:bg-canvas transition">
                                    <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $t->nomor_nota }}</td>
                                    <td class="px-4 py-2.5 font-medium text-xs">{{ $t->customer?->nama ?? 'Umum' }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-xs">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <x-badge :status="$t->status_bayar === 'lunas' ? 'lunas' : 'tempo'">{{ strtoupper($t->status_bayar) }}</x-badge>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <a href="{{ route('cetak.nota', $t->nomor_nota) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs font-bold text-steel hover:text-ink transition inline-flex items-center gap-1">
                                            <x-icon name="printer" class="w-3.5 h-3.5" /> Struk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-6 text-center text-xs text-steel italic">Belum ada transaksi diproses hari ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Stok Menipis Fast Moving -->
            <x-card>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                        <x-icon name="triangle-alert" class="w-4 h-4 text-amber-500" /> Stok Menipis Perlu Perhatian
                    </h3>
                </div>
                <ul class="divide-y divide-line text-xs">
                    @forelse($stokMenipisList as $b)
                        <li class="py-2.5 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-ink">{{ $b->nama }}</p>
                                <p class="text-[11px] text-steel font-mono">Kode: {{ $b->kode }} | Rak: {{ $b->lokasi_rak ?? '-' }}</p>
                            </div>
                            <span class="px-2 py-1 rounded bg-red-100 text-red-800 font-mono font-bold text-xs">Sisa {{ $b->stok }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-steel italic">Seluruh stok barang aman.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    @endif

    <!-- METRIK UTAMA ADMIN & OWNER -->
    @if(in_array($peranSaya, ['owner', 'admin'], true))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-card class="bg-white border-l-4 border-l-rajawali">
                <p class="text-xs font-bold text-steel uppercase tracking-wider">Omzet Penjualan Hari Ini</p>
                <p class="font-mono font-black text-2xl mt-1 text-ink">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</p>
                <div class="flex justify-between items-center mt-2 text-xs">
                    <span class="text-steel">{{ $notaHariIniCount }} nota dibuat</span>
                    @if($growth != 0)
                        <span class="{{ $growth >= 0 ? 'text-emerald-600' : 'text-rajawali' }} font-bold font-mono">
                            {{ $growth >= 0 ? '+' : '' }}{{ $growth }}% vs kemarin
                        </span>
                    @endif
                </div>
            </x-card>

            @if($peranSaya === 'owner')
                <x-card class="bg-white border-l-4 border-l-emerald-600">
                    <p class="text-xs font-bold text-steel uppercase tracking-wider">Laba Kotor Hari Ini</p>
                    <p class="font-mono font-black text-2xl mt-1 text-emerald-700">Rp {{ number_format($labaKotorHariIni, 0, ',', '.') }}</p>
                    <p class="text-xs text-steel mt-2">Selisih total harga jual &amp; HPP modal</p>
                </x-card>
            @else
                <x-card class="bg-white border-l-4 border-l-blue-600">
                    <p class="text-xs font-bold text-steel uppercase tracking-wider">Pembelian Stok Hari Ini</p>
                    <p class="font-mono font-black text-2xl mt-1 text-blue-700">Rp {{ number_format($pembelianHariIni, 0, ',', '.') }}</p>
                    <p class="text-xs text-steel mt-2">Total pengadaan dari supplier</p>
                </x-card>
            @endif

            <x-card class="bg-white border-l-4 border-l-amber-500">
                <p class="text-xs font-bold text-steel uppercase tracking-wider">Total Piutang Customer</p>
                <p class="font-mono font-black text-2xl mt-1 text-amber-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                <p class="text-xs text-steel mt-2">Belum lunas dari transaksi tempo</p>
            </x-card>

            <x-card class="bg-white border-l-4 border-l-purple-600">
                <p class="text-xs font-bold text-steel uppercase tracking-wider">Total Hutang Supplier</p>
                <p class="font-mono font-black text-2xl mt-1 text-purple-700">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                <p class="text-xs text-steel mt-2">Kewajiban bayar ke supplier</p>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Grafik Tren Penjualan 30 Hari (Khusus Owner & Admin) -->
            <x-card class="lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-display font-bold text-base text-ink">Grafik Penjualan 30 Hari Terakhir</h3>
                        <p class="text-xs text-steel">Grafik omzet harian realtime</p>
                    </div>
                </div>
                <div class="h-64 flex items-end gap-1 pt-6 border-b border-line">
                    @php
                        $maxTotal = collect($penjualanBulanan)->max('total') ?: 1;
                    @endphp
                    @foreach ($penjualanBulanan as $item)
                        @php
                            $persen = round(($item['total'] / $maxTotal) * 100);
                            $height = max(8, $persen);
                        @endphp
                        <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                            <!-- Tooltip Floating -->
                            <div class="opacity-0 group-hover:opacity-100 transition absolute -top-8 bg-slate-900 text-white text-[10px] font-mono px-2 py-1 rounded pointer-events-none whitespace-nowrap z-20 shadow-lg">
                                {{ $item['tgl'] }}: Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </div>
                            <div class="w-full bg-rajawali/20 group-hover:bg-rajawali transition rounded-t-sm" style="height: {{ $height }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-[11px] text-steel font-mono mt-2">
                    <span>{{ $penjualanBulanan[0]['tgl'] ?? '' }}</span>
                    <span>{{ $penjualanBulanan[14]['tgl'] ?? '' }}</span>
                    <span>{{ $penjualanBulanan[29]['tgl'] ?? '' }}</span>
                </div>
            </x-card>

            <!-- Status Peringatan Stok Menipis -->
            <x-card>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                        <x-icon name="triangle-alert" class="w-4 h-4 text-amber-500" /> Peringatan Reorder Stok
                    </h3>
                    <a href="{{ route('stok.menipis') }}" class="text-xs font-bold text-rajawali hover:underline">Lihat Semua →</a>
                </div>
                <ul class="divide-y divide-line text-xs">
                    @forelse($stokMenipisList as $b)
                        <li class="py-2.5 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-ink">{{ $b->nama }}</p>
                                <p class="text-[11px] text-steel font-mono">Min: {{ $b->stok_minimum }} | Rak: {{ $b->lokasi_rak ?? '-' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-md bg-red-100 text-red-800 font-mono font-bold text-xs">Sisa {{ $b->stok }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-center text-steel italic">Semua stok berada di atas batas minimum.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Piutang Customer Jatuh Tempo -->
            <x-card :padded="false" class="overflow-hidden">
                <div class="p-4 border-b border-line bg-canvas flex justify-between items-center">
                    <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                        <x-icon name="hand-coins" class="w-4 h-4 text-amber-600" /> Piutang Customer Belum Lunas
                    </h3>
                    <a href="{{ route('keuangan.piutang') }}" class="text-xs font-bold text-rajawali hover:underline">Kelola Piutang →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-steel text-xs uppercase border-b border-line">
                            <tr>
                                <th class="text-left px-4 py-2.5">Customer</th>
                                <th class="text-left px-4 py-2.5">No Nota</th>
                                <th class="text-right px-4 py-2.5">Sisa Piutang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse($piutangJatuhTempoList as $p)
                                @php $sisa = max(0, $p->total_akhir - $p->uang_muka); @endphp
                                <tr class="hover:bg-canvas transition">
                                    <td class="px-4 py-2.5 font-bold text-xs text-ink">{{ $p->customer?->nama ?? 'Umum' }}</td>
                                    <td class="px-4 py-2.5 font-mono text-xs text-rajawali font-bold">{{ $p->nomor_nota }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-xs text-amber-700">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="p-6 text-center text-xs text-steel italic">Tidak ada piutang customer aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Audit Log Staf Terbaru (Khusus Owner) / Quick Links Admin -->
            @if($peranSaya === 'owner')
                <x-card :padded="false" class="overflow-hidden">
                    <div class="p-4 border-b border-line bg-canvas flex justify-between items-center">
                        <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                            <x-icon name="shield-check" class="w-4 h-4 text-purple-600" /> Audit Log Aktivitas Staf Terbaru
                        </h3>
                        <a href="{{ route('pengaturan.audit') }}" class="text-xs font-bold text-rajawali hover:underline">Semua Log →</a>
                    </div>
                    <ul class="divide-y divide-line text-xs">
                        @forelse($auditLogs as $log)
                            <li class="p-3 hover:bg-canvas transition">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-ink">{{ $log->aksi }}</span>
                                    <span class="text-[10px] font-mono text-steel">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-steel text-[11px] mt-0.5">{{ $log->modul }}: <strong class="text-ink">{{ $log->referensi }}</strong> — {{ $log->deskripsi }}</p>
                            </li>
                        @empty
                            <li class="p-6 text-center text-steel italic">Belum ada riwayat aktivitas staf.</li>
                        @endforelse
                    </ul>
                </x-card>
            @else
                <x-card :padded="false" class="overflow-hidden">
                    <div class="p-4 border-b border-line bg-canvas flex justify-between items-center">
                        <h3 class="font-display font-bold text-sm text-ink flex items-center gap-1.5">
                            <x-icon name="truck" class="w-4 h-4 text-blue-600" /> Hutang Pembelian Supplier
                        </h3>
                        <a href="{{ route('keuangan.hutang') }}" class="text-xs font-bold text-rajawali hover:underline">Kelola Hutang →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-steel text-xs uppercase border-b border-line">
                                <tr>
                                    <th class="text-left px-4 py-2.5">Supplier</th>
                                    <th class="text-left px-4 py-2.5">No Faktur</th>
                                    <th class="text-right px-4 py-2.5">Sisa Hutang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @forelse($hutangJatuhTempoList as $h)
                                    @php $sisaHutang = (float) $h->total; @endphp
                                    <tr class="hover:bg-canvas transition">
                                        <td class="px-4 py-2.5 font-bold text-xs text-ink">{{ $h->supplier?->nama ?? '-' }}</td>
                                        <td class="px-4 py-2.5 font-mono text-xs text-blue-700 font-bold">{{ $h->nomor_pembelian }}</td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-xs text-purple-700">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-6 text-center text-xs text-steel italic">Tidak ada hutang supplier aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endif
        </div>
    @endif

</x-app-layout>
