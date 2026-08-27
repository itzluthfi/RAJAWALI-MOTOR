<x-app-layout title="Hutang Dagang Supplier">
<div class="-m-3 p-3" x-data="{
    openPelunasan: false,
    pembelianId: null,
    nomorDokumen: '',
    supplierNama: '',
    total: 0,
    sumber: 'kas',
    keterangan: ''
}">
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm font-bold">
            {{ session('sukses') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <x-card class="mb-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Total Outstanding Hutang Supplier</p>
                <p class="font-mono font-black text-2xl mt-1 text-rajawali">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                <p class="text-xs text-steel mt-0.5">Pembelian Stok: <strong>Rp {{ number_format($totalHutangPembelian, 0, ',', '.') }}</strong> · Service Rekanan: <strong>Rp {{ number_format($totalHutangService, 0, ',', '.') }}</strong></p>
            </div>
            <form method="GET" action="{{ route('keuangan.hutang') }}" class="flex items-center gap-2">
                <x-input name="cari" value="{{ $filter['cari'] }}" placeholder="No Dokumen / Supplier" class="w-64" />
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            </form>
        </div>
    </x-card>

    <x-card :padded="false">
        <div class="p-4 border-b border-line bg-surface flex justify-between items-center">
            <h3 class="font-bold text-sm text-ink flex items-center gap-2">
                <x-icon name="truck" class="w-4 h-4 text-rajawali" />
                <span>Hutang Pembelian Stok Barang</span>
            </h3>
            <span class="text-xs font-mono font-bold text-steel bg-canvas px-2.5 py-1 rounded border border-line">{{ $pembelianHutangs->count() }} faktur belum lunas</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-bold px-4 py-2.5">No Faktur Pembelian</th>
                    <th class="text-left font-bold px-4 py-2.5">Supplier / Vendor</th>
                    <th class="text-left font-bold px-4 py-2.5">Tanggal & Jatuh Tempo</th>
                    <th class="text-right font-bold px-4 py-2.5">Total Tagihan</th>
                    <th class="text-right font-bold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembelianHutangs as $p)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                        <td class="px-4 py-2.5 font-mono text-xs text-rajawali">
                            <a href="{{ route('pembelian.show', $p->id) }}" class="hover:underline">{{ $p->nomor_pembelian }}</a>
                            @if($p->nomor_faktur_supplier)
                                <span class="block text-[11px] text-steel font-mono">Faktur Ref: {{ $p->nomor_faktur_supplier }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-ink font-bold">{{ $p->supplier->nama ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-steel font-medium text-xs">
                            <div>Trans: {{ $p->tanggal->format('d M Y') }}</div>
                            @if($p->jatuh_tempo)
                                <div class="text-rajawali font-bold">Jatuh Tempo: {{ $p->jatuh_tempo->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right font-mono text-rajawali">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <x-button
                                type="button"
                                variant="primary"
                                class="text-xs px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white"
                                x-on:click="
                                    openPelunasan = true;
                                    pembelianId = {{ $p->id }};
                                    nomorDokumen = '{{ $p->nomor_pembelian }}';
                                    supplierNama = '{{ addslashes($p->supplier->nama ?? 'Supplier') }}';
                                    total = {{ $p->total }};
                                "
                            >
                                <x-icon name="check-circle" class="w-3.5 h-3.5" /> Bayar Lunas
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-steel font-medium italic">Tidak ada hutang pembelian stok supplier yang belum lunas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    @if($serviceHutangs->count() > 0)
        <x-card :padded="false" class="mt-4">
            <div class="p-4 border-b border-line bg-surface flex justify-between items-center">
                <h3 class="font-bold text-sm text-ink flex items-center gap-2">
                    <x-icon name="wrench" class="w-4 h-4 text-blue-600" />
                    <span>Hutang Service Rekanan (Outsourcing)</span>
                </h3>
                <span class="text-xs font-mono font-bold text-steel bg-canvas px-2.5 py-1 rounded border border-line">{{ $serviceHutangs->count() }} service belum lunas</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                    <tr>
                        <th class="text-left font-bold px-4 py-2.5">No Dokumen</th>
                        <th class="text-left font-bold px-4 py-2.5">Bengkel Rekanan</th>
                        <th class="text-left font-bold px-4 py-2.5">Tanggal</th>
                        <th class="text-right font-bold px-4 py-2.5">Total Tagihan</th>
                        <th class="text-right font-bold px-4 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceHutangs as $h)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                            <td class="px-4 py-2.5 font-mono text-xs text-rajawali">{{ $h->nomor_dokumen }}</td>
                            <td class="px-4 py-2.5 text-ink font-bold">{{ $h->supplier->nama ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-steel font-medium">{{ $h->tanggal_masuk->format('d M Y') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono text-rajawali">Rp {{ number_format($h->grand_total_supplier, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <form method="POST" action="{{ route('keuangan.hutang.bayar', $h) }}" onsubmit="return confirm('Apakah Anda yakin ingin melunasi hutang service rekanan {{ $h->nomor_dokumen }}?')">
                                    @csrf
                                    <x-button type="submit" variant="primary" class="text-xs px-3 py-1">Bayar Lunas</x-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    @endif

    <!-- Modal Pelunasan Hutang Pembelian -->
    <div x-show="openPelunasan" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div x-show="openPelunasan" x-transition.opacity class="absolute inset-0 bg-ink/40 backdrop-blur-xs" x-on:click="openPelunasan = false"></div>
        <div x-show="openPelunasan" x-transition.scale.95 class="relative bg-surface rounded-xl shadow-2xl border border-line w-full max-w-md p-6 z-10">
            <div class="flex justify-between items-center pb-3 mb-4 border-b border-line">
                <h3 class="font-display font-bold text-base text-ink flex items-center gap-2">
                    <x-icon name="credit-card" class="w-5 h-5 text-emerald-600" />
                    <span>Pelunasan Hutang Pembelian</span>
                </h3>
                <button type="button" x-on:click="openPelunasan = false" class="text-steel hover:text-ink"><x-icon name="x" class="w-5 h-5" /></button>
            </div>

            <form :action="`/admin/pembelian/${pembelianId}/pelunasan`" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-canvas border border-line rounded-lg text-xs space-y-1.5">
                    <div class="flex justify-between"><span class="text-steel">No Faktur Pembelian:</span> <strong class="font-mono text-rajawali" x-text="nomorDokumen"></strong></div>
                    <div class="flex justify-between"><span class="text-steel">Supplier / Vendor:</span> <strong class="text-ink" x-text="supplierNama"></strong></div>
                    <div class="flex justify-between pt-1 border-t border-line"><span class="text-steel font-bold">Total Pelunasan:</span> <strong class="font-mono text-sm text-emerald-600" x-text="'Rp ' + Number(total).toLocaleString('id-ID')"></strong></div>
                </div>

                <div>
                    <x-label>Sumber Kas / Bank Pembayaran</x-label>
                    <x-select name="sumber" x-model="sumber" required class="w-full">
                        <option value="kas">Kas Tunai Kasir / Toko</option>
                        <option value="bank">Rekening Bank Toko (Transfer)</option>
                    </x-select>
                </div>

                <div>
                    <x-input name="keterangan" label="Catatan / Keterangan (Opsional)" placeholder="Contoh: Lunas via transfer BCA" />
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-line">
                    <x-button type="button" variant="secondary" x-on:click="openPelunasan = false">Batal</x-button>
                    <x-button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        <x-icon name="check-circle" class="w-4 h-4" /> Simpan Pelunasan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
