<x-app-layout title="Riwayat Transaksi - Nota Penjualan">

    {{-- SUB-NAV TABS ARSIP RIWAYAT TRANSAKSI --}}
    <div class="flex items-center gap-2 mb-3 bg-surface p-1.5 rounded-xl border border-line no-print">
        <a href="{{ route('penjualan.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-rajawali text-white shadow-xs">
            <x-icon name="receipt" class="w-4 h-4" />
            <span>Nota Penjualan</span>
        </a>
        <a href="{{ route('service.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-steel hover:text-ink hover:bg-canvas">
            <x-icon name="wrench" class="w-4 h-4" />
            <span>Antrean &amp; Servis Bengkel</span>
        </a>
        <a href="{{ route('retur.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-steel hover:text-ink hover:bg-canvas">
            <x-icon name="undo-2" class="w-4 h-4" />
            <span>Retur Barang</span>
        </a>
    </div>

    <x-filter-bar class="no-print" action="{{ route('penjualan.index') }}" method="GET">
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
                        <p class="text-steel text-[11px] uppercase">{{ $p->metode_pembayaran }} · Kasir: {{ $p->user?->name ?? 'Staff' }}</p>
                    </div>
                    <p class="font-mono font-bold text-base text-ink">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</p>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-end gap-1.5 items-center">
                    @php
                        $teksWa = \App\Services\WhatsAppReceiptService::buatTeksNota($p);
                        $waUrl = \App\Services\WhatsAppReceiptService::buatUrlWhatsApp($p->customer->telepon ?? '', $teksWa);
                    @endphp
                    <a href="{{ $waUrl }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-[#25D366] hover:bg-[#1ebd59] text-white text-xs font-bold flex items-center gap-1 shadow-xs transition">
                        <x-icon name="whatsapp" class="w-3.5 h-3.5 text-white" /> WhatsApp
                    </a>
                    <a href="{{ route('penjualan.show', $p->nomor_nota) }}" class="px-2.5 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1">
                        <x-icon name="eye" class="w-3.5 h-3.5" /> Detail
                    </a>
                    <a href="{{ route('cetak.nota', $p->nomor_nota) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg border border-red-200 text-xs font-semibold text-rajawali hover:bg-red-50 flex items-center gap-1">
                        <x-icon name="printer" class="w-3.5 h-3.5" /> Struk
                    </a>
                    <a href="{{ route('cetak.faktur', $p->nomor_nota) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg border border-blue-200 text-xs font-semibold text-blue-600 hover:bg-blue-50 flex items-center gap-1">
                        <x-icon name="file-text" class="w-3.5 h-3.5" /> Faktur A5
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
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Nota &amp; Waktu</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Customer &amp; Kasir</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total &amp; Metode</th>
                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $p)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-2.5">
                                <span class="font-mono text-xs font-bold text-rajawali block">{{ $p->nomor_nota }}</span>
                                <span class="text-[11px] text-steel">{{ $p->created_at->translatedFormat('d M Y H:i') }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="font-medium text-ink text-sm">{{ $p->customer?->nama ?? 'Umum / Tunai' }}</div>
                                <div class="text-[11px] text-steel">Kasir: {{ $p->user?->name ?? 'Kasir' }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="font-mono font-bold text-sm text-ink">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-steel uppercase font-mono">{{ $p->metode_pembayaran }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <x-badge :status="$p->status_bayar === 'lunas' ? 'lunas' : ($p->status_bayar === 'piutang' ? 'tempo' : 'batal')">
                                    {{ ucfirst($p->status_bayar) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-right no-print">
                                <div class="flex justify-end items-center gap-1.5">
                                    @php
                                        $teksWa = \App\Services\WhatsAppReceiptService::buatTeksNota($p);
                                        $waUrl = \App\Services\WhatsAppReceiptService::buatUrlWhatsApp($p->customer->telepon ?? '', $teksWa);
                                    @endphp
                                    <a href="{{ $waUrl }}" target="_blank" class="p-1.5 rounded-lg bg-[#25D366] text-white hover:bg-[#1ebd59] shadow-xs transition flex items-center justify-center" data-tooltip="Kirim Struk WhatsApp">
                                        <x-icon name="whatsapp" class="w-4 h-4 text-white" />
                                    </a>
                                    <a href="{{ route('penjualan.show', $p->nomor_nota) }}" class="p-1.5 rounded-lg text-steel hover:text-ink hover:bg-slate-100 transition" data-tooltip="Lihat Detail">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('cetak.nota', $p->nomor_nota) }}" target="_blank" class="p-1.5 rounded-lg text-steel hover:text-rajawali hover:bg-red-50 transition" data-tooltip="Cetak Struk Thermal (58/80mm)">
                                        <x-icon name="printer" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('cetak.faktur', $p->nomor_nota) }}" target="_blank" class="p-1.5 rounded-lg text-steel hover:text-blue-600 hover:bg-blue-50 transition" data-tooltip="Cetak Faktur A5 (NCR)">
                                        <x-icon name="file-text" class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="receipt" judul="Belum ada transaksi penjualan" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$penjualans" />
    </x-card>

</x-app-layout>
