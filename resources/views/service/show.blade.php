@php
    $tahapan = ['Masuk', 'Dikerjakan', 'Selesai', 'Diambil', 'Lunas'];
    $tahapMap = [
        'masuk' => 0,
        'dikerjakan' => 1,
        'selesai' => 2,
        'diambil' => 3,
        'lunas' => 4,
    ];
    $tahapAktif = $tahapMap[$service->status] ?? 0;
    
    $statusNextMap = [
        'masuk' => 'dikerjakan',
        'dikerjakan' => 'selesai',
        'selesai' => 'diambil',
        'diambil' => 'lunas',
    ];
    $nextStatus = $statusNextMap[$service->status] ?? null;
@endphp
<x-app-layout title="Detail Service {{ $id }}">
    
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('sukses') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-display font-bold text-lg text-rajawali font-mono">{{ $service->nomor_dokumen }}</h2>
            <p class="text-sm text-steel">
                Motor: <strong class="text-ink">{{ $service->merk_type ?? '-' }}</strong> · 
                Customer: <strong class="text-ink">{{ $service->customer->nama }}</strong> · 
                Montir: <strong class="text-ink">{{ $service->montir->name ?? '-' }}</strong>
            </p>
        </div>
        @php
            $badgeStatus = match($service->status) {
                'lunas' => 'lunas',
                'masuk' => 'batal',
                default => 'proses',
            };
        @endphp
        <x-badge :status="$badgeStatus">{{ ucfirst($service->status) }}</x-badge>
    </div>

    {{-- PROGRESS WORK ORDER --}}
    <x-card class="mb-4">
        <div class="flex items-center">
            @foreach($tahapan as $i => $t)
                <div class="flex items-center flex-1 last:flex-none">
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold {{ $i <= $tahapAktif ? 'bg-rajawali text-white' : 'bg-line text-steel' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-[11px] {{ $i <= $tahapAktif ? 'text-ink font-medium' : 'text-steel' }}">{{ $t }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 mx-2 {{ $i < $tahapAktif ? 'bg-rajawali' : 'bg-line' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        {{-- METODE & OUTSOURCE --}}
        <x-card class="lg:col-span-2">
            <h3 class="font-display font-semibold text-sm mb-3">Detail Diagnosa &amp; Pengerjaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-steel block text-xs">Pengerjaan:</span>
                    <strong class="text-ink">{{ ucfirst($service->repaired_by) }}</strong>
                </div>
                @if($service->repaired_by === 'extern')
                    <div>
                        <span class="text-steel block text-xs">Supplier Rekanan:</span>
                        <strong class="text-ink">{{ $service->supplier->nama ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-steel block text-xs">Tanggal Kirim:</span>
                        <strong class="text-ink">{{ $service->tanggal_kirim?->format('d M Y') ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-steel block text-xs">Estimasi Kembali:</span>
                        <strong class="text-ink">{{ $service->tanggal_kembali?->format('d M Y') ?? '-' }}</strong>
                    </div>
                @endif
                <div class="sm:col-span-2 border-t pt-2 mt-2">
                    <span class="text-steel block text-xs">Keluhan Pelanggan:</span>
                    <p class="text-ink italic">{{ $service->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
                </div>
                <div class="sm:col-span-2 border-t pt-2">
                    <span class="text-steel block text-xs">Catatan Tambahan:</span>
                    <p class="text-ink">{{ $service->catatan ?? '-' }}</p>
                </div>
            </div>
        </x-card>

        {{-- SUMMARY KEUANGAN --}}
        <x-card class="flex flex-col justify-between">
            <h3 class="font-display font-semibold text-sm mb-3">Margin &amp; Ringkasan Biaya</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-steel">Total Harga Supplier</span><span class="font-mono font-medium text-ink">Rp {{ number_format($service->grand_total_supplier, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-steel">Total Harga Nett</span><span class="font-mono font-medium text-ink">Rp {{ number_format($service->grand_total_nett, 0, ',', '.') }}</span></div>
                <div class="flex justify-between pt-2 border-t border-line">
                    <span class="font-medium text-ink">Laba (Margin)</span>
                    <span class="font-mono font-bold text-lunas">Rp {{ number_format($service->grand_total_nett - $service->grand_total_supplier, 0, ',', '.') }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- JASA --}}
        <x-card :padded="false">
            <h3 class="font-display font-semibold text-sm p-4 pb-2">Jasa Service</h3>
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-y border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2">Jenis Service</th>
                        <th class="text-right font-semibold px-4 py-2 w-32">Hrg Supplier</th>
                        <th class="text-right font-semibold px-4 py-2 w-32">Hrg Nett</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($service->jasas as $jasa)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas">
                            <td class="px-4 py-2">{{ $jasa->nama_jasa }}</td>
                            <td class="px-4 py-2 text-right font-mono">Rp {{ number_format($jasa->harga_supplier, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-mono">Rp {{ number_format($jasa->harga_nett, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-steel py-6">Tidak ada komponen jasa service</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{-- SPAREPARTS --}}
        <x-card :padded="false">
            <h3 class="font-display font-semibold text-sm p-4 pb-2">Sparepart Yang Digunakan</h3>
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-y border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2">Barang</th>
                        <th class="text-right font-semibold px-4 py-2 w-20">Qty</th>
                        <th class="text-right font-semibold px-4 py-2 w-28">Harga</th>
                        <th class="text-right font-semibold px-4 py-2 w-28">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($service->details as $detail)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas">
                            <td class="px-4 py-2 font-medium text-ink">{{ $detail->barang->nama }}</td>
                            <td class="px-4 py-2 text-right font-mono">{{ rtrim(rtrim(number_format((float) $detail->qty, 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-2 text-right font-mono">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-mono font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-steel py-6">Tidak ada sparepart digunakan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    <div class="flex justify-end gap-2 mt-6 no-print">
        <x-button as="a" href="{{ route('service.index') }}" variant="secondary">Kembali</x-button>
        <x-button as="a" href="{{ route('cetak.tanda-terima-service', $service->id) }}" target="_blank" variant="secondary">
            <x-icon name="printer" class="w-4 h-4" /> Cetak Nota
        </x-button>

        @if($nextStatus)
            <form method="POST" action="{{ route('service.status', $service) }}">
                @csrf
                @method('PATCH')
                <x-button type="submit" variant="primary">
                    Lanjutkan Ke: {{ ucfirst($nextStatus) }}
                </x-button>
            </form>
        @endif
    </div>

</x-app-layout>
