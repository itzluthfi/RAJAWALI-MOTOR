@php
    $nota = [
        ['no' => 'PJ2026000123', 'tanggal' => '22 Jul 2026', 'customer' => 'Toko Jaya Motor', 'jenis' => 'Tempo', 'total' => 450000, 'status' => 'tempo'],
        ['no' => 'PJ2026000122', 'tanggal' => '22 Jul 2026', 'customer' => 'Umum', 'jenis' => 'Tunai', 'total' => 85000, 'status' => 'lunas'],
        ['no' => 'PJ2026000121', 'tanggal' => '21 Jul 2026', 'customer' => 'Bengkel Sumber Rejeki', 'jenis' => 'Tempo', 'total' => 1140000, 'status' => 'lunas'],
        ['no' => 'PJ2026000120', 'tanggal' => '21 Jul 2026', 'customer' => 'Umum', 'jenis' => 'Tunai', 'total' => 220000, 'status' => 'batal'],
    ];
@endphp
<x-app-layout title="Nota Penjualan">

    <x-filter-bar>
        <x-input type="date" label="Dari Tanggal" value="2026-07-22" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-input label="Cari No Nota / Customer" placeholder="PJ2026... atau nama customer" class="w-full sm:min-w-64" />
        <x-select label="Status">
            <option>Semua Status</option>
            <option>Lunas</option>
            <option>Tempo</option>
            <option>Batal</option>
        </x-select>
        <x-button variant="primary" class="w-full sm:w-auto"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
    </x-filter-bar>

    <!-- Mobile Card View (Tampil Hanya di Layar HP < 768px) -->
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @foreach($nota as $n)
            <div class="bg-surface p-4 rounded-xl border border-line shadow-sm space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono font-bold text-sm text-rajawali block">{{ $n['no'] }}</span>
                        <span class="text-xs text-steel">{{ $n['tanggal'] }}</span>
                    </div>
                    <x-badge :status="$n['status']">{{ ucfirst($n['status']) }}</x-badge>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-medium text-ink">{{ $n['customer'] }}</p>
                        <p class="text-steel text-[11px]">{{ $n['jenis'] }}</p>
                    </div>
                    <p class="font-mono font-bold text-base text-ink">Rp {{ number_format($n['total'], 0, ',', '.') }}</p>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-end gap-2">
                    <a href="{{ route('penjualan.show', $n['no']) }}" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1">
                        <x-icon name="eye" class="w-3.5 h-3.5" /> Detail
                    </a>
                    <a href="{{ route('cetak.nota', $n['no']) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1">
                        <x-icon name="printer" class="w-3.5 h-3.5" /> Cetak
                    </a>
                    @if($n['status'] !== 'batal')
                        <button type="button" onclick="batalkanNota('{{ $n['no'] }}', {{ $n['total'] }})" class="px-3 py-1.5 rounded-lg bg-rajawali/10 text-rajawali text-xs font-semibold hover:bg-rajawali/20 flex items-center gap-1">
                            <x-icon name="ban" class="w-3.5 h-3.5" /> Batal
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop Table View (Tampil di Tablet/Desktop >= 768px) -->
    <x-card :padded="false" class="hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2.5">No Nota</th>
                        <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                        <th class="text-left font-semibold px-4 py-2.5">Customer</th>
                        <th class="text-left font-semibold px-4 py-2.5">Jenis</th>
                        <th class="text-right font-semibold px-4 py-2.5">Total</th>
                        <th class="text-left font-semibold px-4 py-2.5">Status</th>
                        <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nota as $n)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $n['no'] }}</td>
                            <td class="px-4 py-2.5">{{ $n['tanggal'] }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $n['customer'] }}</td>
                            <td class="px-4 py-2.5">{{ $n['jenis'] }}</td>
                            <td class="px-4 py-2.5 text-right font-mono font-semibold">Rp {{ number_format($n['total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5">
                                <x-badge :status="$n['status']">{{ ucfirst($n['status']) }}</x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('penjualan.show', $n['no']) }}" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" title="Lihat Detail" data-tooltip="Lihat Detail"><x-icon name="eye" class="w-4 h-4" /></a>
                                    <a href="{{ route('cetak.nota', $n['no']) }}" target="_blank" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" title="Cetak Ulang Nota" data-tooltip="Cetak Ulang Nota"><x-icon name="printer" class="w-4 h-4" /></a>
                                    @if($n['status'] !== 'batal')
                                        <button
                                            type="button"
                                            class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5"
                                            title="Batalkan Nota"
                                            data-tooltip="Batalkan Nota"
                                            onclick="batalkanNota('{{ $n['no'] }}', {{ $n['total'] }})"
                                        ><x-icon name="ban" class="w-4 h-4" /></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

</x-app-layout>

<script>
async function batalkanNota(noNota, total) {
    const { value: alasan } = await Swal.fire({
        icon: 'warning',
        title: `Batalkan nota ${noNota}?`,
        html: `Nota senilai <b>Rp ${total.toLocaleString('id-ID')}</b> akan dibatalkan dan stok dikembalikan.`,
        input: 'text',
        inputLabel: 'Alasan pembatalan',
        inputPlaceholder: 'Minimal 5 huruf...',
        showCancelButton: true,
        confirmButtonText: 'Batalkan Nota',
        cancelButtonText: 'Tutup',
        confirmButtonColor: '#B0181C',
        reverseButtons: true,
        inputValidator: (value) => {
            if (!value || value.trim().length < 5) {
                return 'Alasan wajib diisi, minimal 5 huruf.';
            }
        },
    });

    if (alasan) {
        window.toastSukses(`Nota ${noNota} berhasil dibatalkan.`);
    }
}
</script>
