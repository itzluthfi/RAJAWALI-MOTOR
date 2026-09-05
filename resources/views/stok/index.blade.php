<x-app-layout title="Pusat Stok Terpadu">
    <div class="space-y-6">
        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 no-print">
            <div>
                <h1 class="text-2xl font-black font-display tracking-tight text-ink flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-rajawali/10 text-rajawali">
                        <x-icon name="layers" class="w-6 h-6" />
                    </span>
                    Pusat Stok Terpadu
                </h1>
                <p class="text-xs text-steel mt-1 font-medium">
                    Monitoring nilai persediaan, peringatan stok menipis, kartu mutasi kronologis, dan penyesuaian stok opname.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-steel" />
                    {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>

        <!-- Alerts Flash Messages -->
        @if(session('sukses'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2.5 no-print">
                <x-icon name="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('sukses') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-bold flex items-center gap-2.5 no-print">
                <x-icon name="alert-circle" class="w-5 h-5 text-red-600 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- 4 Overview Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 no-print">
            <!-- Total Valuasi Persediaan -->
            <x-card class="bg-gradient-to-br from-white to-emerald-50/40 border-emerald-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-emerald-700">Total Valuasi Persediaan</p>
                        <p class="text-xl font-black font-mono text-emerald-700 mt-1">Rp {{ number_format($totalValuasiStok, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-steel mt-0.5">Berdasarkan HPP seluruh stok aktual</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <x-icon name="landmark" class="w-5 h-5" />
                    </div>
                </div>
            </x-card>

            <!-- Total Item Aktif -->
            <x-card class="bg-gradient-to-br from-white to-blue-50/40 border-blue-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-blue-700">Total Produk Aktif</p>
                        <p class="text-xl font-black font-mono text-blue-800 mt-1">{{ number_format($totalItemAktif, 0, ',', '.') }} <span class="text-xs font-bold text-steel">SKU</span></p>
                        <p class="text-[10px] text-steel mt-0.5">Barang &amp; sparepart terdaftar</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                        <x-icon name="package" class="w-5 h-5" />
                    </div>
                </div>
            </x-card>

            <!-- Peringatan Stok Menipis -->
            <a href="{{ route('stok.index', ['tab' => 'menipis']) }}" class="block">
                <x-card class="bg-gradient-to-br from-white to-amber-50/40 border-amber-200 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wider text-amber-700">Stok Menipis (Peringatan)</p>
                            <p class="text-xl font-black font-mono text-amber-600 mt-1">
                                {{ $jumlahMenipis }} <span class="text-xs font-bold text-steel">Item</span>
                            </p>
                            <p class="text-[10px] text-amber-700 mt-0.5 font-medium">&le; Batas stok minimum</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                            <x-icon name="triangle-alert" class="w-5 h-5" />
                        </div>
                    </div>
                </x-card>
            </a>

            <!-- Stok Habis (0) -->
            <x-card class="bg-gradient-to-br from-white to-red-50/40 border-red-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-rajawali">Stok Kosong / Habis</p>
                        <p class="text-xl font-black font-mono text-rajawali mt-1">{{ $jumlahHabis }} <span class="text-xs font-bold text-steel">Item</span></p>
                        <p class="text-[10px] text-steel mt-0.5">Saldo stok &le; 0</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-red-500/10 text-rajawali flex items-center justify-center shrink-0">
                        <x-icon name="circle-x" class="w-5 h-5" />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Main Tabbed Interface -->
        <div class="bg-surface rounded-2xl border border-line shadow-sm overflow-hidden">
            <!-- Tabs Navigation Bar -->
            <div class="flex border-b border-line bg-canvas/60 p-1.5 overflow-x-auto gap-1 no-print">
                <!-- Tab 1: Rekap & Valuasi -->
                <a
                    href="{{ route('stok.index', ['tab' => 'rekap', 'dari_tanggal' => $dariTanggal, 'sampai_tanggal' => $sampaiTanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all whitespace-nowrap {{ $tab === 'rekap' ? 'bg-white text-rajawali shadow-sm border border-line/60' : 'text-steel hover:text-ink hover:bg-white/50' }}"
                >
                    <x-icon name="layers" class="w-4 h-4 {{ $tab === 'rekap' ? 'text-rajawali' : 'text-steel' }}" />
                    <span>Rekap &amp; Nilai Stok</span>
                </a>

                <!-- Tab 2: Stok Menipis -->
                <a
                    href="{{ route('stok.index', ['tab' => 'menipis']) }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all whitespace-nowrap {{ $tab === 'menipis' ? 'bg-white text-rajawali shadow-sm border border-line/60' : 'text-steel hover:text-ink hover:bg-white/50' }}"
                >
                    <x-icon name="triangle-alert" class="w-4 h-4 {{ $tab === 'menipis' ? 'text-amber-500' : 'text-steel' }}" />
                    <span>Peringatan Stok Menipis</span>
                    @if($jumlahMenipis > 0)
                        <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-black {{ $tab === 'menipis' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800' }}">
                            {{ $jumlahMenipis }}
                        </span>
                    @endif
                </a>

                <!-- Tab 3: Kartu Stok -->
                <a
                    href="{{ route('stok.index', ['tab' => 'kartu', 'barang_id' => $barangId, 'dari_tanggal' => $dariTanggal, 'sampai_tanggal' => $sampaiTanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all whitespace-nowrap {{ $tab === 'kartu' ? 'bg-white text-rajawali shadow-sm border border-line/60' : 'text-steel hover:text-ink hover:bg-white/50' }}"
                >
                    <x-icon name="notebook-text" class="w-4 h-4 {{ $tab === 'kartu' ? 'text-rajawali' : 'text-steel' }}" />
                    <span>Kartu Riwayat Mutasi</span>
                </a>

                <!-- Tab 4: Penyesuaian Stok Opname -->
                <a
                    href="{{ route('stok.index', ['tab' => 'opname']) }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all whitespace-nowrap {{ $tab === 'opname' ? 'bg-white text-rajawali shadow-sm border border-line/60' : 'text-steel hover:text-ink hover:bg-white/50' }}"
                >
                    <x-icon name="clipboard-check" class="w-4 h-4 {{ $tab === 'opname' ? 'text-emerald-600' : 'text-steel' }}" />
                    <span>Penyesuaian Stok Opname</span>
                </a>
            </div>

            <!-- Tab 1: Rekap & Nilai Stok -->
            @if($tab === 'rekap')
                <div class="p-4 sm:p-6 space-y-4">
                    <form method="GET" action="{{ route('stok.index') }}">
                        <input type="hidden" name="tab" value="rekap">
                        <x-filter-bar class="no-print !p-0 !bg-transparent !border-0 flex-wrap">
                            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $dariTanggal }}" />
                            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $sampaiTanggal }}" />
                            <x-button type="submit" variant="primary">
                                <x-icon name="search" class="w-4 h-4" /> Tampilkan Periode
                            </x-button>
                            <div class="ml-auto flex gap-2 w-full sm:w-auto justify-end">
                                <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-rekap-stok', 'Laporan_Rekap_Stok', 'Laporan Rekapitulasi Stok Barang')">
                                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                                </x-button>
                                <x-button variant="secondary" type="button" onclick="window.print()">
                                    <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak
                                </x-button>
                            </div>
                        </x-filter-bar>
                    </form>

                    <div class="overflow-hidden rounded-xl border border-line">
                        <div class="p-5 border-b border-line bg-canvas/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 print-header">
                            <div>
                                <p class="font-display font-black text-lg text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                                <p class="text-xs font-bold text-ink mt-0.5">Rekapitulasi Mutasi Stok Barang · Periode {{ date('d M Y', strtotime($dariTanggal)) }} s/d {{ date('d M Y', strtotime($sampaiTanggal)) }}</p>
                                <p class="text-[11px] text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" id="tabel-rekap-stok">
                                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                                    <tr>
                                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kode</th>
                                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nama Barang / Sparepart</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Awal</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Masuk</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keluar</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Akhir</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nilai HPP Total (Rp)</th>
                                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Aksi</th>
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
                                            <td class="px-4 py-3 text-center no-print">
                                                <a
                                                    href="{{ route('stok.index', ['tab' => 'kartu', 'barang_id' => $r['id'], 'dari_tanggal' => $dariTanggal, 'sampai_tanggal' => $sampaiTanggal]) }}"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-rajawali bg-rajawali/10 hover:bg-rajawali hover:text-white transition"
                                                    title="Lihat Kartu Riwayat"
                                                >
                                                    <x-icon name="notebook-text" class="w-3.5 h-3.5" />
                                                    Kartu
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-12 text-center text-steel italic">Tidak ada pergerakan stok pada periode terpilih.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab 2: Stok Menipis -->
            @if($tab === 'menipis')
                <div class="p-4 sm:p-6 space-y-4">
                    <x-filter-bar class="no-print !p-0 !bg-transparent !border-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-700 text-xs font-bold border border-amber-500/20">
                                <x-icon name="triangle-alert" class="w-4 h-4 text-amber-500" />
                                Peringatan Stok Di Bawah Minimum ({{ $jumlahMenipis }} Barang Perlu Restock)
                            </span>
                        </div>
                        <div class="ml-auto flex gap-2">
                            <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-stok-menipis', 'Peringatan_Stok_Menipis', 'Peringatan Stok Barang Di Bawah Minimum')">
                                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                            </x-button>
                            <x-button variant="secondary" type="button" onclick="window.print()">
                                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak
                            </x-button>
                        </div>
                    </x-filter-bar>

                    <div class="overflow-hidden rounded-xl border border-line">
                        <div class="p-5 border-b border-line bg-canvas/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 print-header">
                            <div>
                                <p class="font-display font-black text-lg text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                                <p class="text-xs font-bold text-ink mt-0.5">Laporan Peringatan Stok Barang Di Bawah Batas Minimum</p>
                                <p class="text-[11px] text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
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
                                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kategori</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Sisa Stok</th>
                                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Stok Min</th>
                                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status</th>
                                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @forelse($stokMenipis as $m)
                                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                                            <td class="px-4 py-3 font-mono text-xs font-bold text-steel">{{ $m->kode }}</td>
                                            <td class="px-4 py-3 font-bold text-ink">{{ $m->nama }}</td>
                                            <td class="px-4 py-3 text-xs text-steel">{{ $m->group?->nama ?? '-' }}</td>
                                            <td class="px-4 py-3 text-right font-mono text-base font-black {{ $m->stok_saat_ini <= 0 ? 'text-rajawali' : 'text-amber-600' }}">
                                                {{ number_format((float) $m->stok_saat_ini, 0, ',', '.') }} {{ $m->satuan?->nama ?? '' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-steel font-bold">{{ number_format((float) $m->stok_minimum, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if($m->stok_saat_ini <= 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800">
                                                        Habis (Kosong)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">
                                                        Kritis
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center no-print">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <x-button as="a" href="{{ route('pembelian.create') }}" variant="secondary" class="text-xs px-2.5 py-1">
                                                        <x-icon name="shopping-cart" class="w-3.5 h-3.5" /> Order
                                                    </x-button>
                                                    <a
                                                        href="{{ route('stok.index', ['tab' => 'kartu', 'barang_id' => $m->id]) }}"
                                                        class="p-1.5 rounded-lg text-steel hover:text-rajawali hover:bg-rajawali/10 transition"
                                                        title="Kartu Riwayat"
                                                    >
                                                        <x-icon name="notebook-text" class="w-4 h-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-12 text-center text-steel italic">
                                                <x-icon name="check-circle-2" class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
                                                Kondisi stok aman! Tidak ada barang di bawah stok minimum saat ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab 3: Kartu Stok -->
            @if($tab === 'kartu')
                <div class="p-4 sm:p-6 space-y-4">
                    <form method="GET" action="{{ route('stok.index') }}">
                        <input type="hidden" name="tab" value="kartu">
                        <x-filter-bar class="no-print !p-0 !bg-transparent !border-0 flex-wrap">
                            <div class="min-w-64">
                                <x-select name="barang_id" label="Pilih Barang / Sparepart" required>
                                    @foreach($daftarBarang as $b)
                                        <option value="{{ $b->id }}" {{ $barangId == $b->id ? 'selected' : '' }}>
                                            {{ $b->kode }} - {{ $b->nama }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>
                            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $dariTanggal }}" />
                            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $sampaiTanggal }}" />
                            <x-button type="submit" variant="primary">
                                <x-icon name="search" class="w-4 h-4" /> Tampilkan Kartu
                            </x-button>
                            <div class="ml-auto flex gap-2 w-full sm:w-auto justify-end">
                                <x-button variant="secondary" type="button" onclick="exportTableToExcel('tabel-kartu-stok', 'Laporan_Kartu_Stok', 'Kartu Mutasi Stok Barang')">
                                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                                </x-button>
                                <x-button variant="secondary" type="button" onclick="window.print()">
                                    <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak
                                </x-button>
                            </div>
                        </x-filter-bar>
                    </form>

                    <div class="overflow-hidden rounded-xl border border-line">
                        <div class="p-5 border-b border-line bg-canvas/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 print-header">
                            <div>
                                <p class="font-display font-black text-lg text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                                <p class="text-xs font-bold text-ink mt-0.5">
                                    Kartu Mutasi Riwayat Stok · <span class="text-rajawali font-black">{{ $barangTerpilih?->nama ?? 'Semua' }}</span> ({{ $barangTerpilih?->kode ?? '-' }})
                                </p>
                                <p class="text-[11px] text-steel mt-0.5 italic">Periode {{ date('d M Y', strtotime($dariTanggal)) }} s/d {{ date('d M Y', strtotime($sampaiTanggal)) }}</p>
                            </div>
                            <div class="text-left sm:text-right font-bold">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                                </span>
                                <p class="text-xs text-steel mt-1">Saldo Awal Sebelum Periode: <span class="font-mono text-ink font-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</span></p>
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
                                            <td class="px-4 py-3 text-steel text-xs font-mono">{{ $m['tanggal'] }}</td>
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
                                            <td colspan="7" class="px-4 py-10 text-center text-steel italic">Tidak ada transaksi mutasi stok untuk barang ini pada periode terpilih.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab 4: Penyesuaian Stok Opname -->
            @if($tab === 'opname')
                <div class="p-4 sm:p-6" x-data="opnameApp(@js($barangListJson))">
                    <div class="max-w-2xl mx-auto space-y-6">
                        <div class="border-b border-line pb-4">
                            <h2 class="font-display font-black text-lg text-ink">Penyesuaian Stok Opname Fisik</h2>
                            <p class="text-xs text-steel mt-0.5">
                                Samakan jumlah stok fisik hasil hitungan riil di toko/gudang dengan angka pencatatan di sistem. Sistem akan otomatis membuat jurnal mutasi opname.
                            </p>
                        </div>

                        <form action="{{ route('stok.opname.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label>Pilih Barang / Sparepart</x-label>
                                    <select
                                        name="barang_id"
                                        x-model="barangId"
                                        x-on:change="pilihBarang()"
                                        class="w-full text-xs font-bold rounded-lg border border-line bg-white px-3 py-2.5 focus:ring-2 focus:ring-rajawali"
                                        required
                                    >
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($daftarBarang as $b)
                                            <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-label>Alasan Penyesuaian (Wajib Diisi)</x-label>
                                    <x-input name="alasan" placeholder="Contoh: Barang rusak, selisih hitung, hilang" required />
                                </div>
                            </div>

                            <div class="p-5 bg-canvas border border-line rounded-2xl grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <span class="text-[11px] text-steel font-black uppercase tracking-wider">Stok Sistem</span>
                                    <p class="font-mono font-black text-2xl text-ink mt-1" x-text="stokSistem"></p>
                                </div>
                                <div>
                                    <span class="text-[11px] text-steel font-black uppercase tracking-wider">Stok Fisik Riil</span>
                                    <input
                                        type="number"
                                        name="stok_fisik"
                                        x-model.number="stokFisik"
                                        min="0"
                                        class="w-full mt-1 text-center font-mono font-black text-2xl rounded-xl border border-line bg-white p-1.5 focus:ring-2 focus:ring-rajawali"
                                        required
                                    >
                                </div>
                                <div>
                                    <span class="text-[11px] text-steel font-black uppercase tracking-wider">Selisih Mutasi</span>
                                    <p
                                        class="font-mono font-black text-2xl mt-1"
                                        :class="selisih < 0 ? 'text-rajawali' : (selisih > 0 ? 'text-emerald-600' : 'text-steel')"
                                        x-text="(selisih > 0 ? '+' : '') + selisih"
                                    ></p>
                                </div>
                            </div>

                            <div class="flex justify-end pt-3 border-t border-line">
                                <x-button type="submit" variant="primary">
                                    <x-icon name="save" class="w-4 h-4" /> Simpan Penyesuaian Stok
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function opnameApp(daftarBarang) {
        return {
            daftarBarang: daftarBarang,
            barangId: '',
            stokSistem: 0,
            stokFisik: 0,

            pilihBarang() {
                const b = this.daftarBarang.find(item => item.id == this.barangId);
                if (b) {
                    this.stokSistem = Number(b.stok);
                    this.stokFisik = Number(b.stok);
                } else {
                    this.stokSistem = 0;
                    this.stokFisik = 0;
                }
            },

            get selisih() {
                return this.stokFisik - this.stokSistem;
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
