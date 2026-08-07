<x-app-layout title="Service / Bengkel">
    <form method="GET" action="{{ route('service.index') }}">
        <x-filter-bar class="no-print">
            <x-input type="date" name="dari_tanggal" label="Dari Tanggal" value="{{ $filter['dari_tanggal'] }}" />
            <x-input type="date" name="sampai_tanggal" label="Sampai Tanggal" value="{{ $filter['sampai_tanggal'] }}" />
            <x-select name="status" label="Status">
                <option value="semua" {{ $filter['status'] === 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="masuk" {{ $filter['status'] === 'masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="dikerjakan" {{ $filter['status'] === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                <option value="selesai" {{ $filter['status'] === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="diambil" {{ $filter['status'] === 'diambil' ? 'selected' : '' }}>Diambil</option>
                <option value="lunas" {{ $filter['status'] === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </x-select>
            <x-input name="cari" value="{{ $filter['cari'] }}" label="Cari" placeholder="No. Dokumen / Customer" class="min-w-64" />
            <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Filter</x-button>
            <div class="ml-auto">
                <x-button as="a" href="{{ route('service.create') }}" variant="primary"><x-icon name="plus" class="w-4 h-4" /> Terima Service Baru</x-button>
            </div>
        </x-filter-bar>
    </form>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">No Service</th>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal Masuk</th>
                    <th class="text-left font-semibold px-4 py-2.5">Customer</th>
                    <th class="text-left font-semibold px-4 py-2.5">Motor</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $s)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $s->nomor_dokumen }}</td>
                        <td class="px-4 py-2.5">{{ $s->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-4 py-2.5 font-medium text-ink">{{ $s->customer->nama }}</td>
                        <td class="px-4 py-2.5">{{ $s->merk_type ?? '-' }}</td>
                        <td class="px-4 py-2.5">
                            @php
                                $badgeStatus = match($s->status) {
                                    'lunas' => 'lunas',
                                    'masuk' => 'batal',
                                    default => 'proses',
                                };
                            @endphp
                            <x-badge :status="$badgeStatus">{{ ucfirst($s->status) }}</x-badge>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('service.show', $s->nomor_dokumen) }}" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas inline-block" title="Lihat Detail Service" data-tooltip="Lihat Detail Service">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="wrench" judul="Tidak ada data service" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$services" />
    </x-card>
</x-app-layout>
