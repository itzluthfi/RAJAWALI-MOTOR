<x-app-layout title="Detail Faktur Pembelian {{ $pembelian->nomor_pembelian }}">
    <div class="max-w-4xl mx-auto space-y-4">
        <x-card>
            <div class="border-b border-line pb-4 mb-4 flex justify-between items-start flex-wrap gap-2">
                <div>
                    <span class="text-xs font-mono font-bold text-rajawali uppercase tracking-wider">FAKTUR PEMBELIAN STOK</span>
                    <h1 class="font-display font-black text-2xl text-ink tracking-tight">{{ $pembelian->nomor_pembelian }}</h1>
                    <p class="text-xs text-steel mt-0.5">Tanggal: {{ $pembelian->tanggal->format('d F Y') }} · Input oleh: {{ $pembelian->user->name ?? 'Staff' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-badge :status="$pembelian->status_bayar">{{ strtoupper($pembelian->status_bayar) }}</x-badge>
                    <x-button as="a" href="{{ route('pembelian.index') }}" variant="secondary" size="xs">
                        <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
                    </x-button>
                    <x-button type="button" variant="primary" size="xs" onclick="window.print()">
                        <x-icon name="printer" class="w-4 h-4" /> Cetak
                    </x-button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-canvas mb-6 border border-line">
                <div>
                    <p class="text-xs text-steel font-bold uppercase">Informasi Supplier / Vendor</p>
                    <p class="font-bold text-sm text-ink mt-1">{{ $pembelian->supplier->nama ?? '-' }}</p>
                    <p class="text-xs text-steel mt-0.5">No Faktur Vendor: <span class="font-mono text-ink font-bold">{{ $pembelian->nomor_faktur_supplier ?? '-' }}</span></p>
                    <p class="text-xs text-steel">Telepon: {{ $pembelian->supplier->telepon ?? '-' }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs text-steel font-bold uppercase">Status Pembayaran</p>
                    <p class="font-mono font-bold text-lg text-rajawali mt-1">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</p>
                    @if($pembelian->status_bayar === 'tempo' && $pembelian->jatuh_tempo)
                        <p class="text-xs text-marka font-bold">Jatuh Tempo: {{ $pembelian->jatuh_tempo->format('d M Y') }}</p>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto border border-line rounded-lg mb-4">
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
                            <td colspan="3" class="px-4 py-3 text-right">Total Pembelian:</td>
                            <td class="px-4 py-3 text-right font-mono text-base text-rajawali">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($pembelian->keterangan)
                <div class="p-3 bg-canvas rounded-lg text-xs text-steel border border-line">
                    <strong class="text-ink">Catatan / Keterangan:</strong> {{ $pembelian->keterangan }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
