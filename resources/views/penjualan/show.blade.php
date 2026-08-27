<x-app-layout title="Detail Nota {{ $penjualan->nomor_nota }}">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('penjualan.index') }}" class="p-1 text-steel hover:text-ink rounded-md hover:bg-canvas transition">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <h2 class="font-display font-black text-xl text-ink">Detail Nota: <span class="font-mono text-rajawali">{{ $penjualan->nomor_nota }}</span></h2>
            </div>
            <p class="text-xs text-steel mt-1">
                Tanggal: <strong class="text-ink font-mono">{{ $penjualan->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }} WIB</strong> · 
                Customer: <strong class="text-ink font-bold">{{ $penjualan->customer?->nama ?? 'Pelanggan Umum' }}</strong> · 
                Kasir: <strong class="text-ink font-bold">{{ $penjualan->user?->name ?? 'Staff' }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-badge :status="$penjualan->status_bayar === 'lunas' ? 'lunas' : ($penjualan->status_bayar === 'piutang' ? 'tempo' : 'batal')">
                {{ strtoupper($penjualan->status_bayar) }}
            </x-badge>

            @php
                $teksWa = \App\Services\WhatsAppReceiptService::buatTeksNota($penjualan);
                $waUrl = \App\Services\WhatsAppReceiptService::buatUrlWhatsApp($penjualan->customer->telepon ?? '', $teksWa);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="px-3 py-2 bg-emerald-600 text-white font-bold rounded-lg text-xs hover:bg-emerald-700 transition flex items-center gap-1.5 shadow-sm">
                <x-icon name="message-circle" class="w-4 h-4" /> Kirim WhatsApp
            </a>

            <a href="{{ route('cetak.nota', $penjualan->nomor_nota) }}" target="_blank" class="px-3 py-2 bg-rajawali text-white font-bold rounded-lg text-xs hover:bg-rajawali-dark transition flex items-center gap-1.5 shadow-sm">
                <x-icon name="printer" class="w-4 h-4" /> Struk Thermal (58/80mm)
            </a>
            <a href="{{ route('cetak.faktur', $penjualan->nomor_nota) }}" target="_blank" class="px-3 py-2 bg-slate-800 text-white font-bold rounded-lg text-xs hover:bg-slate-900 transition flex items-center gap-1.5 shadow-sm">
                <x-icon name="file-text" class="w-4 h-4" /> Faktur Penjualan (A5)
            </a>
        </div>
    </div>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80 mb-6">
        <div class="px-6 py-4 border-b border-line bg-canvas font-bold text-sm text-steel flex justify-between items-center">
            <span>Rincian Barang / Produk</span>
            <span class="text-xs font-mono">Metode Bayar: <strong class="text-ink uppercase">{{ $penjualan->metode_pembayaran }}</strong></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-steel text-xs uppercase tracking-wide border-b border-line font-bold">
                    <tr>
                        <th class="text-left px-4 py-3">No</th>
                        <th class="text-left px-4 py-3">Kode & Nama Barang</th>
                        <th class="text-right px-4 py-3">Qty</th>
                        <th class="text-right px-4 py-3">Harga Satuan</th>
                        <th class="text-right px-4 py-3">Diskon</th>
                        <th class="text-right px-4 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($penjualan->details as $idx => $d)
                        <tr class="hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 text-steel font-mono text-xs">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-ink">{{ $d->barang?->nama ?? 'Barang' }}</div>
                                <div class="text-xs text-steel font-mono">{{ $d->barang?->kode ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold">{{ rtrim(rtrim(number_format((float) $d->qty, 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rajawali">
                                {{ $d->diskon > 0 ? '-Rp ' . number_format($d->diskon, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-end px-6 py-5 border-t border-line bg-canvas">
            <div class="w-80 space-y-2 text-right text-sm">
                <div class="flex justify-between font-medium text-steel">
                    <span>Subtotal:</span>
                    <span class="font-mono text-ink font-bold">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($penjualan->diskon > 0)
                    <div class="flex justify-between text-rajawali font-bold">
                        <span>Diskon Nota:</span>
                        <span class="font-mono">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($penjualan->pajak > 0)
                    <div class="flex justify-between text-steel font-bold">
                        <span>Pajak:</span>
                        <span class="font-mono">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg text-rajawali font-black pt-3 border-t border-line">
                    <span>GRAND TOTAL:</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
                </div>

                @if($penjualan->metode_pembayaran === 'tempo')
                    <div class="flex justify-between text-xs text-steel pt-2 border-t border-line/60">
                        <span>Telah Dibayar (DP/Cicilan):</span>
                        <span class="font-mono font-bold text-lunas">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-rajawali font-bold">
                        <span>Sisa Piutang:</span>
                        <span class="font-mono font-bold">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </x-card>

</x-app-layout>
