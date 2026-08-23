<x-app-layout title="Kas Besar (Owner)">
<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card class="bg-emerald-500/10 border-emerald-500/30">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-500 text-white rounded-xl">
                    <x-icon name="arrow-down-left" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase tracking-wider">Total Kas Masuk</p>
                    <p class="text-xl font-black font-mono text-emerald-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="bg-rajawali/10 border-rajawali/30">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-rajawali text-white rounded-xl">
                    <x-icon name="arrow-up-right" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase tracking-wider">Total Kas Keluar</p>
                    <p class="text-xl font-black font-mono text-rajawali">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="bg-blue-500/10 border-blue-500/30">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-600 text-white rounded-xl">
                    <x-icon name="vault" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-steel font-bold uppercase tracking-wider">Saldo Kas Besar</p>
                    <p class="text-xl font-black font-mono text-blue-800">Rp {{ number_format($saldoKasBesar, 0, ',', '.') }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <form method="GET" action="{{ route('keuangan.kas-besar') }}">
        <x-filter-bar>
            <x-input type="date" name="dari_tanggal" value="{{ $filter['dari_tanggal'] ?? '' }}" label="Dari Tanggal" />
            <x-input type="date" name="sampai_tanggal" value="{{ $filter['sampai_tanggal'] ?? '' }}" label="Sampai Tanggal" />
            <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Filter</x-button>
        </x-filter-bar>
    </form>

    <x-card :padded="false">
        <div class="px-5 py-3 border-b border-line font-bold flex justify-between items-center">
            <span>Jurnal Transaksi Kas Besar</span>
            <span class="text-xs text-steel">Khusus Pengawasan Owner</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Tipe</th>
                    <th class="text-left font-semibold px-4 py-2.5">Kategori</th>
                    <th class="text-left font-semibold px-4 py-2.5">No. Referensi</th>
                    <th class="text-left font-semibold px-4 py-2.5">Keterangan</th>
                    <th class="text-right font-semibold px-4 py-2.5">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($mutasi as $m)
                    <tr class="hover:bg-canvas transition">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $m->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2.5">
                            <span class="px-2 py-0.5 rounded text-xs font-bold font-mono {{ $m->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ strtoupper($m->tipe) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 font-medium text-xs">{{ ucwords(str_replace('_', ' ', $m->kategori ?? '')) }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $m->no_referensi ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-xs text-steel">{{ $m->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-bold {{ $m->tipe === 'masuk' ? 'text-emerald-700' : 'text-rajawali' }}">
                            {{ $m->tipe === 'masuk' ? '+' : '-' }}Rp {{ number_format($m->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"><x-empty-state icon="vault" judul="Belum ada mutasi kas besar" /></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$mutasi" />
    </x-card>
</div>
</x-app-layout>
