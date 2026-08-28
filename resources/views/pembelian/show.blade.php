<x-app-layout title="Detail Faktur Pembelian {{ $pembelian->nomor_pembelian }}">
    @php
        $sisaHutang = $pembelian->sisa_hutang;
        $isOverdue = $pembelian->is_overdue;
    @endphp

    <div class="max-w-4xl mx-auto space-y-4" x-data="{
        openPelunasan: false,
        nominalBayar: {{ $sisaHutang }},
        sisa: {{ $sisaHutang }},
        tanggalBayar: '{{ now()->format('Y-m-d\TH:i') }}',
        sumber: 'kas'
    }">
        @if(session('sukses'))
            <div class="p-3.5 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-bold flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                <span>{{ session('sukses') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-bold flex items-center gap-2">
                <x-icon name="triangle-alert" class="w-4 h-4 text-red-600 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <x-card>
            <div class="border-b border-line pb-4 mb-4 flex justify-between items-start flex-wrap gap-3">
                <div>
                    <span class="text-xs font-mono font-bold text-rajawali uppercase tracking-wider">FAKTUR PEMBELIAN STOK</span>
                    <h1 class="font-display font-black text-2xl text-ink tracking-tight">{{ $pembelian->nomor_pembelian }}</h1>
                    <p class="text-xs text-steel mt-0.5">Tanggal Faktur: <strong class="text-ink">{{ $pembelian->tanggal->format('d F Y') }}</strong> · Input oleh: <strong class="text-ink">{{ $pembelian->user->name ?? 'Staff' }}</strong></p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if($pembelian->status_bayar === 'lunas')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <x-icon name="check-circle" class="w-3.5 h-3.5 mr-1" /> LUNAS
                        </span>
                    @elseif($isOverdue)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-300 animate-pulse">
                            <x-icon name="triangle-alert" class="w-3.5 h-3.5 mr-1 text-red-600" /> LEWAT TEMPO ({{ $pembelian->hari_terlambat }} HARI)
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            <x-icon name="clock" class="w-3.5 h-3.5 mr-1" /> TEMPO (SISA {{ $pembelian->sisa_hari_jatuh_tempo }} HARI)
                        </span>
                    @endif

                    <x-button as="a" href="{{ route('pembelian.index') }}" variant="secondary" size="xs">
                        <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
                    </x-button>
                    @if($pembelian->status_bayar === 'tempo' && $sisaHutang > 0)
                        <x-button type="button" variant="primary" size="xs" x-on:click="openPelunasan = true" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold">
                            <x-icon name="credit-card" class="w-4 h-4" /> Bayar / Cicil Hutang
                        </x-button>
                    @endif
                    <x-button type="button" variant="secondary" size="xs" onclick="window.print()">
                        <x-icon name="printer" class="w-4 h-4" /> Cetak
                    </x-button>
                </div>
            </div>

            <!-- Panel Ringkasan Finansial & Supplier -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-xl bg-canvas mb-6 border border-line">
                <div>
                    <p class="text-xs text-steel font-bold uppercase">Supplier / Vendor</p>
                    <p class="font-bold text-sm text-ink mt-1">{{ $pembelian->supplier->nama ?? '-' }}</p>
                    <p class="text-xs text-steel mt-0.5">No Faktur Vendor: <span class="font-mono text-ink font-bold">{{ $pembelian->nomor_faktur_supplier ?? '-' }}</span></p>
                    <p class="text-xs text-steel">Telepon: {{ $pembelian->supplier->telepon ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase">Jatuh Tempo (30 Hari)</p>
                    @if($pembelian->jatuh_tempo)
                        <p class="font-mono font-bold text-sm mt-1 {{ $isOverdue ? 'text-red-700' : 'text-ink' }}">
                            {{ $pembelian->jatuh_tempo->format('d M Y') }}
                        </p>
                        <p class="text-xs mt-0.5 {{ $isOverdue ? 'text-red-600 font-bold' : 'text-steel' }}">
                            {{ $isOverdue ? "Terlambat {$pembelian->hari_terlambat} hari" : "Sisa {$pembelian->sisa_hari_jatuh_tempo} hari lagi" }}
                        </p>
                    @else
                        <p class="text-xs text-steel mt-1 font-bold">Pembelian Tunai Langsung</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase">Total Tagihan Awal</p>
                    <p class="font-mono font-bold text-base text-ink mt-1">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-700 font-medium">Telah Dibayar: <strong class="font-mono font-bold">Rp {{ number_format($pembelian->terbayar ?? 0, 0, ',', '.') }}</strong></p>
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase">Sisa Hutang</p>
                    <p class="font-mono font-black text-lg mt-1 {{ $sisaHutang > 0 ? 'text-rajawali' : 'text-emerald-700' }}">
                        {{ $sisaHutang > 0 ? 'Rp ' . number_format($sisaHutang, 0, ',', '.') : 'Rp 0 (LUNAS)' }}
                    </p>
                    @if($pembelian->tanggal_lunas)
                        <p class="text-[11px] text-emerald-700 font-medium">Lunas pada: {{ $pembelian->tanggal_lunas->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>

            <!-- Tabel Detail Item Barang -->
            <div class="overflow-x-auto border border-line rounded-lg mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-steel text-xs uppercase font-bold border-b border-line">
                        <tr>
                            <th class="px-4 py-3 text-left">Kode / Nama Barang</th>
                            <th class="px-4 py-3 text-center">Jumlah (Qty)</th>
                            <th class="px-4 py-3 text-right">Harga Beli</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach($pembelian->details as $d)
                            <tr class="hover:bg-canvas/50">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-bold text-rajawali block">{{ $d->barang->kode ?? '-' }}</span>
                                    <span class="font-bold text-ink">{{ $d->barang->nama ?? 'Barang Terhapus' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-ink">{{ $d->jumlah }}</td>
                                <td class="px-4 py-3 text-right font-mono text-steel">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-canvas border-t border-line font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right">Total Tagihan Faktur:</td>
                            <td class="px-4 py-3 text-right font-mono text-base text-rajawali">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tabel Riwayat Pembayaran & Cicilan -->
            <div class="mt-6 border border-line rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-surface border-b border-line flex justify-between items-center">
                    <h3 class="font-bold text-sm text-ink flex items-center gap-2">
                        <x-icon name="history" class="w-4 h-4 text-emerald-600" />
                        <span>Riwayat Pembayaran &amp; Cicilan</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-steel">{{ $pembelian->pembayarans->count() }} transaksi pembayaran</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                            <tr>
                                <th class="px-4 py-2.5 text-left">No</th>
                                <th class="px-4 py-2.5 text-left">Tanggal &amp; Waktu</th>
                                <th class="px-4 py-2.5 text-right">Nominal Bayar / Cicil</th>
                                <th class="px-4 py-2.5 text-center">Sumber Kas</th>
                                <th class="px-4 py-2.5 text-left">Dicatat Oleh</th>
                                <th class="px-4 py-2.5 text-left">Catatan / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-medium">
                            @forelse($pembelian->pembayarans as $idx => $byr)
                                <tr class="hover:bg-canvas transition">
                                    <td class="px-4 py-2.5 font-mono text-steel">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-2.5 font-mono text-ink font-bold">{{ $byr->tanggal_bayar->format('d M Y, H:i') }} WIB</td>
                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($byr->nominal, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase {{ $byr->sumber === 'bank' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $byr->sumber }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-steel">{{ $byr->user->name ?? 'Admin' }}</td>
                                    <td class="px-4 py-2.5 text-steel italic">{{ $byr->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-steel italic">Belum ada riwayat cicilan/pembayaran tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($pembelian->keterangan)
                <div class="mt-4 p-3 bg-canvas rounded-lg text-xs text-steel border border-line">
                    <strong class="text-ink">Catatan / Keterangan Pembelian:</strong> {{ $pembelian->keterangan }}
                </div>
            @endif
        </x-card>

        <!-- Modal Pelunasan / Cicilan Hutang Pembelian Detail -->
        @if($pembelian->status_bayar === 'tempo' && $sisaHutang > 0)
            <div x-show="openPelunasan" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div x-show="openPelunasan" x-transition.opacity class="absolute inset-0 bg-ink/40 backdrop-blur-xs" x-on:click="openPelunasan = false"></div>
                <div x-show="openPelunasan" x-transition.scale.95 class="relative bg-surface rounded-xl shadow-2xl border border-line w-full max-w-md p-6 z-10">
                    <div class="flex justify-between items-center pb-3 mb-4 border-b border-line">
                        <h3 class="font-display font-bold text-base text-ink flex items-center gap-2">
                            <x-icon name="credit-card" class="w-5 h-5 text-emerald-600" />
                            <span>Pembayaran / Cicilan Hutang</span>
                        </h3>
                        <button type="button" x-on:click="openPelunasan = false" class="text-steel hover:text-ink"><x-icon name="x" class="w-5 h-5" /></button>
                    </div>

                    <form action="{{ route('pembelian.pelunasan', $pembelian->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3.5 bg-canvas border border-line rounded-lg text-xs space-y-1.5 font-medium">
                            <div class="flex justify-between"><span class="text-steel">No Pembelian:</span> <strong class="font-mono text-rajawali">{{ $pembelian->nomor_pembelian }}</strong></div>
                            <div class="flex justify-between"><span class="text-steel">Supplier / Vendor:</span> <strong class="text-ink font-bold">{{ $pembelian->supplier->nama ?? '-' }}</strong></div>
                            <div class="flex justify-between"><span class="text-steel">Total Tagihan Awal:</span> <strong class="font-mono text-ink">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</strong></div>
                            <div class="flex justify-between"><span class="text-steel">Telah Dicicil:</span> <strong class="font-mono text-emerald-700">Rp {{ number_format($pembelian->terbayar ?? 0, 0, ',', '.') }}</strong></div>
                            <div class="flex justify-between pt-1.5 border-t border-line"><span class="text-steel font-bold">Sisa Hutang Saat Ini:</span> <strong class="font-mono text-sm text-rajawali font-black">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</strong></div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-steel">Nominal Pembayaran / Cicilan (Rp)</label>
                                <button type="button" x-on:click="nominalBayar = {{ $sisaHutang }}" class="text-[11px] font-bold text-emerald-700 hover:underline">
                                    Bayar Lunas Penuh (Rp {{ number_format($sisaHutang, 0, ',', '.') }})
                                </button>
                            </div>
                            <input
                                type="number"
                                name="nominal_bayar"
                                x-model.number="nominalBayar"
                                min="1"
                                max="{{ $sisaHutang }}"
                                required
                                class="w-full text-sm font-mono font-bold rounded-lg border border-line bg-white px-3 py-2.5 focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                            >
                            <p class="text-[11px] text-steel mt-1" x-show="nominalBayar < {{ $sisaHutang }}">
                                Sisa hutang setelah pembayaran ini: <strong class="text-rajawali font-mono font-bold">Rp <span x-text="Number(Math.max(0, {{ $sisaHutang }} - nominalBayar)).toLocaleString('id-ID')"></span></strong>
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
                            <x-button type="button" variant="secondary" x-on:click="openPelunasan = false">Batal</x-button>
                            <x-button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                                <x-icon name="check-circle" class="w-4 h-4" /> Simpan Pembayaran
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
