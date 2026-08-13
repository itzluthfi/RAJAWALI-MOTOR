<x-app-layout title="Retur Barang">
    <x-filter-bar class="no-print" action="{{ route('retur.index') }}" method="GET">
        <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ request('dari_tanggal') }}" />
        <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ request('sampai_tanggal') }}" />
        <x-select name="jenis" label="Jenis Retur">
            <option value="semua">Semua Retur</option>
            <option value="penjualan" {{ request('jenis') === 'penjualan' ? 'selected' : '' }}>Retur Penjualan</option>
            <option value="pembelian" {{ request('jenis') === 'pembelian' ? 'selected' : '' }}>Retur Pembelian</option>
        </x-select>
        <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Filter</x-button>
        <div class="ml-auto flex gap-2">
            <x-button as="a" href="{{ route('retur.penjualan.create') }}" variant="secondary"><x-icon name="undo-2" class="w-4 h-4" /> Retur Penjualan</x-button>
            <x-button as="a" href="{{ route('retur.pembelian.create') }}" variant="primary"><x-icon name="undo-2" class="w-4 h-4" /> Retur Pembelian</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SIDOARJO</p>
                <p class="text-sm font-bold text-ink mt-0.5">Daftar Transaksi Retur Penjualan &amp; Pembelian</p>
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
            <table class="w-full text-sm">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">No Retur</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Jenis</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Referensi / Pihak Terkait</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Alasan Retur</th>
                        <th class="text-right font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returs as $r)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-rajawali">{{ $r->nomor_retur }}</td>
                            <td class="px-4 py-3 text-steel">{{ $r->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-bold">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $r->jenis === 'penjualan' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $r->jenis === 'penjualan' ? 'Retur Penjualan' : 'Retur Pembelian' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ink">
                                @if($r->jenis === 'penjualan')
                                    <span class="font-bold block">{{ $r->customer->nama ?? 'Customer Umum' }}</span>
                                    <span class="font-mono text-xs text-steel">Nota: {{ $r->penjualan->nomor_nota ?? '-' }}</span>
                                @else
                                    <span class="font-bold block">{{ $r->supplier->nama ?? '-' }}</span>
                                    <span class="font-mono text-xs text-steel">Faktur: {{ $r->pembelian->nomor_pembelian ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-steel text-xs italic">{{ $r->alasan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-ink">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-steel italic">Belum ada data transaksi retur barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returs->hasPages())
            <div class="p-4 border-t border-line no-print">
                {{ $returs->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
