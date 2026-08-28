<x-app-layout title="Pembelian Stok">
    <x-filter-bar class="no-print" action="{{ route('pembelian.index') }}" method="GET">
        <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ request('dari_tanggal') }}" />
        <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ request('sampai_tanggal') }}" />
        <x-select name="status" label="Status Bayar">
            <option value="semua">Semua Status</option>
            <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="tempo" {{ request('status') === 'tempo' ? 'selected' : '' }}>Tempo (Belum Lunas)</option>
            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Lewat Jatuh Tempo (> 1 Bulan)</option>
        </x-select>
        <x-input name="search" label="Cari No / Supplier" value="{{ request('search') }}" class="min-w-64" placeholder="Nomor faktur / nama supplier..." />
        <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
        <div class="ml-auto flex gap-2">
            <x-button type="button" variant="secondary" onclick="exportTableToExcel('tabel-pembelian', 'Laporan_Faktur_Pembelian', 'Daftar Faktur Pembelian Stok')">
                <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
            </x-button>
            <x-button type="button" variant="secondary" onclick="window.print()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
            </x-button>
            <x-button as="a" href="{{ route('pembelian.create') }}" variant="primary"><x-icon name="plus" class="w-4 h-4" /> Pembelian Baru</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SIDOARJO</p>
                <p class="text-sm font-bold text-ink mt-0.5">Daftar Faktur Pembelian &amp; Hutang Stok Barang</p>
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
            <table class="w-full text-sm" id="tabel-pembelian">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Pembelian</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Supplier</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal &amp; Jatuh Tempo</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total Tagihan</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Terbayar</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Sisa Hutang</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status &amp; Tempo</th>
                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($pembelians as $p)
                        @php
                            $sisaHutang = $p->sisa_hutang;
                            $isOverdue = $p->is_overdue;
                        @endphp
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 {{ $isOverdue ? 'bg-red-50/40' : '' }}">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-rajawali">
                                <a href="{{ route('pembelian.show', $p->id) }}" class="hover:underline">{{ $p->nomor_pembelian }}</a>
                                @if($p->nomor_faktur_supplier)
                                    <span class="block text-[11px] text-steel font-mono">Ref: {{ $p->nomor_faktur_supplier }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-ink">
                                <div>{{ $p->supplier->nama ?? '-' }}</div>
                                @if($p->supplier?->telepon)
                                    <div class="text-[11px] text-steel">{{ $p->supplier->telepon }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-bold text-ink">Trans: {{ $p->tanggal->format('d M Y') }}</div>
                                @if($p->jatuh_tempo)
                                    <div class="font-mono mt-0.5 {{ $isOverdue ? 'text-red-700 font-bold' : 'text-steel' }}">
                                        Tempo: {{ $p->jatuh_tempo->format('d M Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($p->terbayar ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rajawali">
                                {{ $sisaHutang > 0 ? 'Rp ' . number_format($sisaHutang, 0, ',', '.') : 'Rp 0 (Lunas)' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($p->status_bayar === 'lunas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <x-icon name="check-circle" class="w-3 h-3 mr-1" /> Lunas
                                    </span>
                                    @if($p->tanggal_lunas)
                                        <span class="block text-[10px] text-steel font-mono mt-0.5">{{ $p->tanggal_lunas->format('d M Y') }}</span>
                                    @endif
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-300 animate-pulse">
                                        <x-icon name="triangle-alert" class="w-3 h-3 mr-1 text-red-600" /> Lewat Tempo ({{ $p->hari_terlambat }} hr)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <x-icon name="clock" class="w-3 h-3 mr-1" /> Tempo ({{ $p->sisa_hari_jatuh_tempo }} hr lagi)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center no-print">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button as="a" href="{{ route('pembelian.show', $p->id) }}" variant="secondary" size="xs" data-tooltip="Lihat Detail &amp; Riwayat Cicilan">
                                        <x-icon name="eye" class="w-3.5 h-3.5" />
                                    </x-button>

                                    @if($p->status_bayar === 'tempo' && $sisaHutang > 0)
                                        <x-button
                                            type="button"
                                            variant="primary"
                                            size="xs"
                                            x-data
                                            x-on:click="$dispatch('buka-modal-pelunasan-pembelian', {
                                                id: {{ $p->id }},
                                                nomor: '{{ $p->nomor_pembelian }}',
                                                supplier: '{{ addslashes($p->supplier->nama ?? 'Supplier') }}',
                                                total: {{ $p->total }},
                                                terbayar: {{ $p->terbayar ?? 0 }},
                                                sisa: {{ $sisaHutang }}
                                            })"
                                            data-tooltip="Bayar Cicilan / Pelunasan Hutang"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white"
                                        >
                                            <x-icon name="credit-card" class="w-3.5 h-3.5" />
                                            <span class="hidden sm:inline">Bayar / Cicil</span>
                                        </x-button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-steel italic">Belum ada data faktur pembelian barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembelians->hasPages())
            <div class="p-4 border-t border-line no-print">
                {{ $pembelians->links() }}
            </div>
        @endif
    </x-card>

    <!-- Modal Pelunasan / Cicilan Hutang Pembelian -->
    <div x-data="{
        open: false,
        pembelianId: null,
        nomorPembelian: '',
        supplierNama: '',
        total: 0,
        terbayar: 0,
        sisa: 0,
        nominalBayar: 0,
        tanggalBayar: '{{ now()->format('Y-m-d\TH:i') }}',
        sumber: 'kas',
        keterangan: ''
    }"
    x-on:buka-modal-pelunasan-pembelian.window="
        open = true;
        pembelianId = $event.detail.id;
        nomorPembelian = $event.detail.nomor;
        supplierNama = $event.detail.supplier;
        total = $event.detail.total;
        terbayar = $event.detail.terbayar;
        sisa = $event.detail.sisa;
        nominalBayar = $event.detail.sisa;
        tanggalBayar = '{{ now()->format('Y-m-d\TH:i') }}';
        keterangan = '';
    "
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/40 backdrop-blur-xs" x-on:click="open = false"></div>
        <div x-show="open" x-transition.scale.95 class="relative bg-surface rounded-xl shadow-2xl border border-line w-full max-w-md p-6 z-10">
            <div class="flex justify-between items-center pb-3 mb-4 border-b border-line">
                <h3 class="font-display font-bold text-base text-ink flex items-center gap-2">
                    <x-icon name="credit-card" class="w-5 h-5 text-emerald-600" />
                    <span>Pembayaran / Cicilan Hutang Pembelian</span>
                </h3>
                <button type="button" x-on:click="open = false" class="text-steel hover:text-ink"><x-icon name="x" class="w-5 h-5" /></button>
            </div>

            <form :action="`{{ url('/admin/pembelian') }}/${pembelianId}/pelunasan`" method="POST" class="space-y-4">
                @csrf
                <div class="p-3.5 bg-canvas border border-line rounded-lg text-xs space-y-1.5 font-medium">
                    <div class="flex justify-between"><span class="text-steel">No Faktur Pembelian:</span> <strong class="font-mono text-rajawali" x-text="nomorPembelian"></strong></div>
                    <div class="flex justify-between"><span class="text-steel">Supplier / Vendor:</span> <strong class="text-ink font-bold" x-text="supplierNama"></strong></div>
                    <div class="flex justify-between"><span class="text-steel">Total Tagihan Awal:</span> <strong class="font-mono text-ink" x-text="'Rp ' + Number(total).toLocaleString('id-ID')"></strong></div>
                    <div class="flex justify-between"><span class="text-steel">Telah Dicicil:</span> <strong class="font-mono text-emerald-700" x-text="'Rp ' + Number(terbayar).toLocaleString('id-ID')"></strong></div>
                    <div class="flex justify-between pt-1.5 border-t border-line"><span class="text-steel font-bold">Sisa Hutang Saat Ini:</span> <strong class="font-mono text-sm text-rajawali font-black" x-text="'Rp ' + Number(sisa).toLocaleString('id-ID')"></strong></div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-xs font-bold text-steel">Nominal Pembayaran / Cicilan (Rp)</label>
                        <button type="button" x-on:click="nominalBayar = sisa" class="text-[11px] font-bold text-emerald-700 hover:underline">
                            Bayar Lunas Penuh (Rp <span x-text="Number(sisa).toLocaleString('id-ID')"></span>)
                        </button>
                    </div>
                    <input
                        type="number"
                        name="nominal_bayar"
                        x-model.number="nominalBayar"
                        min="1"
                        :max="sisa"
                        required
                        class="w-full text-sm font-mono font-bold rounded-lg border border-line bg-white px-3 py-2.5 focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    >
                    <p class="text-[11px] text-steel mt-1" x-show="nominalBayar < sisa">
                        Sisa hutang setelah pembayaran ini: <strong class="text-rajawali font-mono font-bold">Rp <span x-text="Number(Math.max(0, sisa - nominalBayar)).toLocaleString('id-ID')"></span></strong>
                    </p>
                </div>

                <div>
                    <x-label>Tanggal &amp; Jam Pembayaran</x-label>
                    <input
                        type="datetime-local"
                        name="tanggal_bayar"
                        x-model="tanggalBayar"
                        required
                        class="w-full text-xs font-mono font-bold rounded-lg border border-line bg-white px-3 py-2 focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    >
                </div>

                <div>
                    <x-label>Sumber Kas / Bank Pembayaran</x-label>
                    <x-select name="sumber" x-model="sumber" required class="w-full">
                        <option value="kas">Kas Tunai Kasir / Toko</option>
                        <option value="bank">Rekening Bank Toko (Transfer)</option>
                    </x-select>
                </div>

                <div>
                    <x-input name="keterangan" label="Catatan / Keterangan (Opsional)" placeholder="Contoh: Cicilan ke-1 via transfer BCA" />
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-line">
                    <x-button type="button" variant="secondary" x-on:click="open = false">Batal</x-button>
                    <x-button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        <x-icon name="check-circle" class="w-4 h-4" /> Simpan Pembayaran
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
