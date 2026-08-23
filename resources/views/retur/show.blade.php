<x-app-layout title="Detail Retur {{ $retur->nomor_retur }}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('retur.index') }}" class="p-1 text-steel hover:text-ink rounded-md hover:bg-canvas transition">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <h2 class="font-display font-black text-xl text-ink">Detail Retur: <span class="font-mono text-rajawali">{{ $retur->nomor_retur }}</span></h2>
            </div>
            <p class="text-xs text-steel mt-1">
                Tanggal: <strong class="text-ink font-mono">{{ $retur->tanggal->format('d F Y') }}</strong> · 
                Tipe: <strong class="text-ink font-bold uppercase">{{ $retur->jenis }}</strong> · 
                Staff: <strong class="text-ink font-bold">{{ $retur->user?->name ?? 'Staff' }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-badge status="batal">{{ strtoupper($retur->jenis) }}</x-badge>
            
            @if($retur->jenis === 'penjualan' && $retur->penjualan)
                <a href="{{ route('penjualan.show', $retur->penjualan->nomor_nota) }}" class="px-3 py-2 bg-rajawali text-white font-bold rounded-lg text-xs hover:bg-rajawali-dark transition flex items-center gap-1.5 shadow-sm">
                    <x-icon name="file-text" class="w-4 h-4" /> Buka Nota Penjualan Asli
                </a>
            @elseif($retur->jenis === 'pembelian' && $retur->pembelian)
                <a href="{{ route('pembelian.show', $retur->pembelian->nomor_pembelian) }}" class="px-3 py-2 bg-blue-700 text-white font-bold rounded-lg text-xs hover:bg-blue-800 transition flex items-center gap-1.5 shadow-sm">
                    <x-icon name="file-text" class="w-4 h-4" /> Buka Faktur Pembelian Asli
                </a>
            @endif
        </div>
    </div>

    <x-card class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-steel font-bold">Pihak Terkait:</p>
                <p class="font-bold text-ink text-sm">{{ $retur->customer?->nama ?? $retur->supplier?->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-steel font-bold">Dokumen Transaksi Asli:</p>
                @if($retur->penjualan)
                    <a href="{{ route('penjualan.show', $retur->penjualan->nomor_nota) }}" class="font-bold text-rajawali text-sm font-mono hover:underline flex items-center gap-1">
                        <span>Nota: {{ $retur->penjualan->nomor_nota }}</span>
                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                    </a>
                @elseif($retur->pembelian)
                    <a href="{{ route('pembelian.show', $retur->pembelian->nomor_pembelian) }}" class="font-bold text-blue-700 text-sm font-mono hover:underline flex items-center gap-1">
                        <span>Faktur: {{ $retur->pembelian->nomor_pembelian }}</span>
                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                    </a>
                @else
                    <p class="text-steel italic">Tidak ada referensi dokumen</p>
                @endif
            </div>
            <div>
                <p class="text-steel font-bold">Alasan Retur:</p>
                <p class="font-medium text-ink italic">{{ $retur->alasan ?? '-' }}</p>
            </div>
        </div>
    </x-card>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="px-6 py-4 border-b border-line bg-canvas font-bold text-sm text-steel">Rincian Barang Diretur</div>
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-steel text-xs uppercase tracking-wide border-b border-line font-bold">
                <tr>
                    <th class="text-left px-4 py-3">No</th>
                    <th class="text-left px-4 py-3">Barang</th>
                    <th class="text-right px-4 py-3">Jumlah (Qty)</th>
                    <th class="text-right px-4 py-3">Harga Beli/Jual</th>
                    <th class="text-right px-4 py-3">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach($retur->details as $idx => $d)
                    <tr class="hover:bg-canvas transition">
                        <td class="px-4 py-3 text-steel font-mono text-xs">{{ $idx + 1 }}</td>
                        <td class="px-4 py-3 font-bold text-ink">{{ $d->barang?->nama ?? 'Barang' }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold">{{ $d->jumlah }}</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex justify-end px-6 py-4 border-t border-line bg-canvas">
            <div class="text-right">
                <p class="text-xs text-steel font-bold uppercase">Total Refund Retur</p>
                <p class="font-mono font-black text-xl text-rajawali">Rp {{ number_format($retur->total, 0, ',', '.') }}</p>
            </div>
        </div>
    </x-card>
</x-app-layout>
