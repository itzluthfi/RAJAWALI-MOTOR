<x-app-layout title="Nota Penjualan">

    <form method="GET" action="{{ route('penjualan.index') }}">
        <x-filter-bar class="no-print">
            <x-input label="Cari No Nota / Customer" name="search" value="{{ request('search') }}" placeholder="PJ2026... atau nama customer" class="w-full sm:min-w-64" />
            <x-select label="Status" name="status">
                <option value="semua" @selected(request('status') === 'semua')>Semua Status</option>
                <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                <option value="piutang" @selected(request('status') === 'piutang')>Piutang / Tempo</option>
                <option value="batal" @selected(request('status') === 'batal')>Batal</option>
            </x-select>
            <x-button type="submit" variant="primary" class="w-full sm:w-auto"><x-icon name="search" class="w-4 h-4" /> Cari Nota</x-button>
            <div class="ml-auto flex gap-2">
                <x-button type="button" variant="secondary" onclick="exportTableToExcel('tabel-penjualan', 'Laporan_Nota_Penjualan', 'Daftar Transaksi Nota Penjualan')">
                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                </x-button>
                <x-button type="button" variant="secondary" onclick="cetakLaporanPdf()">
                    <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
                </x-button>
            </div>
        </x-filter-bar>
    </form>

    <!-- Mobile Card View (Tampil Hanya di Layar HP < 768px) -->
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($penjualans as $p)
            <div class="bg-surface p-4 rounded-xl border border-line shadow-sm space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono font-bold text-sm text-rajawali block">{{ $p->nomor_nota }}</span>
                        <span class="text-xs text-steel">{{ $p->created_at->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    <x-badge :status="$p->status_bayar === 'lunas' ? 'lunas' : ($p->status_bayar === 'piutang' ? 'tempo' : 'batal')">
                        {{ ucfirst($p->status_bayar) }}
                    </x-badge>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-medium text-ink">{{ $p->customer?->nama ?? 'Umum / Tunai' }}</p>
                        <p class="text-steel text-[11px] uppercase">{{ $p->metode_pembayaran }}</p>
                    </div>
                    <p class="font-mono font-bold text-base text-ink">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</p>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-end gap-2">
                    <a href="{{ route('penjualan.show', $p->id) }}" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1">
                        <x-icon name="eye" class="w-3.5 h-3.5" /> Detail
                    </a>
                    <a href="{{ route('cetak.nota', $p->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1">
                        <x-icon name="printer" class="w-3.5 h-3.5" /> Cetak
                    </a>
                </div>
            </div>
        @empty
            <x-empty-state icon="receipt" judul="Belum ada transaksi penjualan" />
        @endforelse
    </div>

    <!-- Desktop Table View (Tampil di Tablet/Desktop >= 768px) -->
    <x-card :padded="false" class="hidden md:block overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Daftar Transaksi Nota Penjualan</p>
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
            <table class="w-full text-sm" id="tabel-penjualan">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Nota</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Customer</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Kasir</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Metode</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total Akhir</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $p)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $p->nomor_nota }}</td>
                            <td class="px-4 py-2.5 text-xs text-steel">{{ $p->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $p->customer?->nama ?? 'Umum / Tunai' }}</td>
                            <td class="px-4 py-2.5 text-xs text-steel">{{ $p->user?->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-2.5 text-xs uppercase font-mono">{{ $p->metode_pembayaran }}</td>
                            <td class="px-4 py-2.5 text-right font-mono font-semibold">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5">
                                <x-badge :status="$p->status_bayar === 'lunas' ? 'lunas' : ($p->status_bayar === 'piutang' ? 'tempo' : 'batal')">
                                    {{ ucfirst($p->status_bayar) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('penjualan.show', $p->id) }}" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Lihat Detail"><x-icon name="eye" class="w-4 h-4" /></a>
                                    <a href="{{ route('cetak.nota', $p->id) }}" target="_blank" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Cetak Ulang Nota"><x-icon name="printer" class="w-4 h-4" /></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="receipt" judul="Belum ada transaksi penjualan" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$penjualans" />
    </x-card>

</x-app-layout>
