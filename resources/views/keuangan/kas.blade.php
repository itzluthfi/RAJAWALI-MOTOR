<x-app-layout title="Buku Harian Kas">
<div x-data="{ jenis: 'masuk' }" class="-m-3 p-3 space-y-4">
    @if(session('sukses'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            <span>{{ session('sukses') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- KARTU RINGKASAN SALDO & ARUS KAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white p-5 rounded-2xl shadow-xl border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Saldo Kas Berjalan</span>
                    <span class="font-mono font-black text-2xl mt-1 block {{ $saldoKas >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        Rp {{ number_format($saldoKas, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-slate-400 mt-1 block">Total uang fisik kasir/laci saat ini</span>
                </div>
                <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700 text-emerald-400">
                    <x-icon name="wallet" class="w-6 h-6" />
                </div>
            </div>
        </x-card>

        <x-card class="bg-surface p-5 rounded-2xl shadow-md border border-slate-200/80">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-steel uppercase tracking-wider block">Pemasukan Periode Ini</span>
                    <span class="font-mono font-black text-xl text-emerald-600 mt-1 block">
                        +Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-steel mt-1 block">Sesuai filter tanggal/kategori</span>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <x-icon name="arrow-down-left" class="w-6 h-6" />
                </div>
            </div>
        </x-card>

        <x-card class="bg-surface p-5 rounded-2xl shadow-md border border-slate-200/80">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-steel uppercase tracking-wider block">Pengeluaran Periode Ini</span>
                    <span class="font-mono font-black text-xl text-rose-600 mt-1 block">
                        -Rp {{ number_format($totalKeluarPeriode, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-steel mt-1 block">Sesuai filter tanggal/kategori</span>
                </div>
                <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                    <x-icon name="arrow-up-right" class="w-6 h-6" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- FILTER BAR PENCARIAN & KATEGORI --}}
    <x-filter-bar class="no-print" action="{{ route('keuangan.kas') }}" method="GET">
        <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $filter['dari_tanggal'] }}" />
        <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $filter['sampai_tanggal'] }}" />
        
        <x-select name="kategori" label="Kategori Modul">
            <option value="semua" @selected($filter['kategori'] === 'semua')>Semua Kategori</option>
            <option value="penjualan" @selected($filter['kategori'] === 'penjualan')>Penjualan POS / Kasir</option>
            <option value="service" @selected($filter['kategori'] === 'service')>Servis Bengkel Motor</option>
            <option value="pembelian" @selected($filter['kategori'] === 'pembelian')>Pembelian Stok / Supplier</option>
            <option value="operasional" @selected($filter['kategori'] === 'operasional')>Beban Operasional Toko</option>
            <option value="gaji" @selected($filter['kategori'] === 'gaji')>Gaji &amp; Komisi</option>
            <option value="piutang" @selected($filter['kategori'] === 'piutang')>Pelunasan Piutang</option>
            <option value="hutang" @selected($filter['kategori'] === 'hutang')>Pelunasan Hutang</option>
            <option value="lainnya" @selected($filter['kategori'] === 'lainnya')>Lain-lain</option>
        </x-select>

        <x-select name="tipe" label="Arus Kas">
            <option value="semua" @selected($filter['tipe'] === 'semua')>Semua Arus</option>
            <option value="masuk" @selected($filter['tipe'] === 'masuk')>Uang Masuk (+)</option>
            <option value="keluar" @selected($filter['tipe'] === 'keluar')>Uang Keluar (-)</option>
        </x-select>

        <x-input name="cari" value="{{ $filter['cari'] }}" label="Cari Keterangan / No Ref" placeholder="No nota, faktur, supplier..." class="w-full sm:min-w-60" />
        
        <x-button type="submit" variant="primary" class="w-full sm:w-auto">
            <x-icon name="search" class="w-4 h-4" /> Cari
        </x-button>

        <div class="ml-auto flex gap-2 w-full sm:w-auto justify-end">
            <x-button type="button" variant="secondary" onclick="exportTableToExcel('tabel-kas', 'Buku_Kas_Harian', 'Laporan Mutasi Buku Kas Harian')">
                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
            </x-button>
            <x-button type="button" variant="secondary" onclick="window.print()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
            </x-button>
            <x-button type="button" variant="primary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-kas'}}))">
                <x-icon name="plus" class="w-4 h-4" /> + Transaksi Kas
            </x-button>
        </div>
    </x-filter-bar>

    {{-- TABEL BUKU KAS HARIAN --}}
    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-5 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Buku Mutasi Kas Masuk &amp; Kas Keluar (Data Terbaru di Atas)</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-kas">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 w-32">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 w-36">Kategori</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Keterangan &amp; No Referensi</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 w-44">Jumlah (Rp)</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 w-36 no-print">Dokumen Terkait</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($mutasi as $m)
                        @php
                            $ref = $m->no_referensi;
                            if (!$ref) {
                                // Ekstrak nomor dokumen dari teks keterangan jika ada
                                if (preg_match('/(PJ\d+|PB\d+|SV\d+|RJ\d+|RB\d+)/', $m->keterangan ?? '', $matches)) {
                                    $ref = $matches[1];
                                }
                            }
                            $isPenjualan = $ref && str_starts_with($ref, 'PJ');
                            $isPembelian = $ref && str_starts_with($ref, 'PB');
                            $isService = $ref && str_starts_with($ref, 'SV');
                            $isRetur = $ref && (str_starts_with($ref, 'RJ') || str_starts_with($ref, 'RB'));
                        @endphp
                        <tr class="hover:bg-canvas transition duration-100">
                            <td class="px-4 py-3 text-steel text-xs font-mono font-bold">
                                <div>{{ $m->tanggal->format('d M Y') }}</div>
                                <span class="text-[10px] text-slate-400 font-normal">{{ $m->created_at ? $m->created_at->format('H:i') . ' WIB' : '' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold inline-block
                                    @if(in_array($m->kategori, ['penjualan', 'service'])) bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif($m->kategori === 'pembelian') bg-blue-100 text-blue-800 border border-blue-300
                                    @elseif(str_contains($m->kategori, 'retur')) bg-amber-100 text-amber-800 border border-amber-300
                                    @else bg-slate-100 text-slate-800 border border-slate-300 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $m->kategori)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-ink text-xs">{{ $m->keterangan ?? '-' }}</div>
                                @if($ref)
                                    <span class="inline-flex items-center gap-1 font-mono text-[11px] text-rajawali font-bold mt-0.5">
                                        <x-icon name="file-text" class="w-3 h-3" /> No Ref: {{ $ref }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-sm {{ $m->tipe === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                <div class="flex items-center justify-end gap-1">
                                    <span>{{ $m->tipe === 'masuk' ? '+' : '-' }}Rp {{ number_format($m->nominal, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider {{ $m->tipe === 'masuk' ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $m->tipe === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right no-print">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($isPenjualan)
                                        <a href="{{ route('penjualan.show', $ref) }}" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition" data-tooltip="Lihat Nota Penjualan {{ $ref }}">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('cetak.nota', $ref) }}" target="_blank" class="p-1.5 rounded-lg bg-red-50 text-rajawali hover:bg-red-100 border border-red-200 transition" data-tooltip="Cetak Struk {{ $ref }}">
                                            <x-icon name="printer" class="w-4 h-4" />
                                        </a>
                                    @elseif($isPembelian)
                                        <a href="{{ route('pembelian.index', ['search' => $ref]) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-bold flex items-center gap-1 transition" data-tooltip="Buka Faktur Pembelian {{ $ref }}">
                                            <x-icon name="truck" class="w-3.5 h-3.5" /> Faktur
                                        </a>
                                    @elseif($isService)
                                        <a href="{{ route('service.show', $ref) }}" class="px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 text-xs font-bold flex items-center gap-1 transition" data-tooltip="Buka SPK Servis {{ $ref }}">
                                            <x-icon name="wrench" class="w-3.5 h-3.5" /> Servis
                                        </a>
                                    @elseif($isRetur)
                                        <a href="{{ route('retur.index') }}" class="px-2.5 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold flex items-center gap-1 transition" data-tooltip="Buka Arsip Retur {{ $ref }}">
                                            <x-icon name="undo-2" class="w-3.5 h-3.5" /> Retur
                                        </a>
                                    @else
                                        <span class="text-xs text-steel font-mono italic">Kas Manual</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-steel font-medium italic">
                                Belum ada transaksi kas pada periode atau kriteria pencarian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$mutasi" />
    </x-card>

    {{-- MODAL TRANSAKSI KAS BARU --}}
    <x-modal name="form-kas" title="Transaksi Kas Masuk / Keluar Baru">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2 p-1 bg-canvas rounded-xl border border-line text-xs font-bold">
                <button
                    type="button"
                    x-on:click="jenis = 'masuk'"
                    :class="jenis === 'masuk' ? 'bg-emerald-600 text-white shadow-xs' : 'text-steel hover:text-ink'"
                    class="py-2.5 rounded-lg text-center transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <x-icon name="arrow-down-left" class="w-4 h-4" /> Kas Masuk (+)
                </button>
                <button
                    type="button"
                    x-on:click="jenis = 'keluar'"
                    :class="jenis === 'keluar' ? 'bg-rose-600 text-white shadow-xs' : 'text-steel hover:text-ink'"
                    class="py-2.5 rounded-lg text-center transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <x-icon name="arrow-up-right" class="w-4 h-4" /> Kas Keluar (-)
                </button>
            </div>

            <form method="POST" action="{{ route('keuangan.transaksi.store') }}" class="space-y-4 font-bold">
                @csrf
                <input type="hidden" name="tipe" :value="jenis">
                <input type="hidden" name="sumber" value="kas">
                
                <x-input type="date" name="tanggal" label="Tanggal Transaksi *" value="{{ date('Y-m-d') }}" required />
                
                <div>
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Kategori Perkiraan *</label>
                    <select name="kategori" class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white" required>
                        <option value="operasional">Beban Operasional Toko (Kebersihan, Plastik, ATK, dsb)</option>
                        <option value="gaji">Beban Gaji &amp; Komisi Karyawan</option>
                        <option value="listrik">Beban Listrik, Air &amp; Internet</option>
                        <option value="penjualan">Pendapatan Penjualan Tambahan</option>
                        <option value="service">Pendapatan Servis Tambahan</option>
                        <option value="piutang">Pelunasan Piutang Customer</option>
                        <option value="hutang">Pelunasan Hutang Supplier</option>
                        <option value="lainnya">Pendapatan / Pengeluaran Lainnya</option>
                    </select>
                </div>
                
                <x-input label="Nominal Uang (Rp) *" name="nominal" type="number" min="1" step="any" placeholder="cth. 50000" required mono />
                
                <x-input label="Keterangan / Catatan Tambahan *" name="keterangan" placeholder="cth. Beli token listrik toko Rp 100.000" required />
                
                <div class="flex justify-end gap-2 pt-4 border-t border-line">
                    <x-button type="button" variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-kas'}}))">Batal</x-button>
                    <x-button type="submit" variant="primary">
                        <x-icon name="save" class="w-4 h-4" /> Simpan Transaksi Kas
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
</x-app-layout>
