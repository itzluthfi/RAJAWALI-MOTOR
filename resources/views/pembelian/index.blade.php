<x-app-layout title="Pembelian Stok">
    <x-filter-bar class="no-print" action="{{ route('pembelian.index') }}" method="GET">
        <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ request('dari_tanggal') }}" />
        <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ request('sampai_tanggal') }}" />
        <x-select name="status" label="Status Bayar">
            <option value="semua">Semua Status</option>
            <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="tempo" {{ request('status') === 'tempo' ? 'selected' : '' }}>Jatuh Tempo</option>
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
                <p class="text-sm font-bold text-ink mt-0.5">Daftar Faktur Pembelian Stok Barang</p>
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
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Supplier</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Faktur Supplier</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total (Rp)</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Status</th>
                        <th class="text-center font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900 no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelians as $p)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-rajawali">
                                <a href="{{ route('pembelian.show', $p->id) }}" class="hover:underline">{{ $p->nomor_pembelian }}</a>
                            </td>
                            <td class="px-4 py-3 text-steel">{{ $p->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-ink">{{ $p->supplier->nama ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-ink">{{ $p->nomor_faktur_supplier ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-badge :status="$p->status_bayar">{{ ucfirst($p->status_bayar) }}</x-badge></td>
                            <td class="px-4 py-3 text-center no-print">
                                <x-button as="a" href="{{ route('pembelian.show', $p->id) }}" variant="secondary" size="xs" data-tooltip="Lihat Detail Faktur">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-steel italic">Belum ada data faktur pembelian barang.</td>
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
</x-app-layout>
