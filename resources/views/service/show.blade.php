@php
    $tahapan = ['Masuk', 'Dikerjakan', 'Selesai', 'Diambil', 'Lunas'];
    $tahapAktif = 2;
@endphp
<x-app-layout title="Service {{ $id }}">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-display font-bold text-lg">{{ $id }}</h2>
            <p class="text-sm text-steel">Honda Beat · Andi · Montir: Wawan</p>
        </div>
        <x-badge status="proses">{{ $tahapan[$tahapAktif] }}</x-badge>
    </div>

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card :padded="false">
            <h3 class="font-display font-semibold text-sm p-4 pb-2">Jasa</h3>
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-y border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2">Jenis Service</th>
                        <th class="text-right font-semibold px-4 py-2">Hrg Supplier</th>
                        <th class="text-right font-semibold px-4 py-2">Hrg Nett</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-line last:border-0">
                        <td class="px-4 py-2">Servis CVT</td>
                        <td class="px-4 py-2 text-right font-mono">50.000</td>
                        <td class="px-4 py-2 text-right font-mono">75.000</td>
                    </tr>
                </tbody>
            </table>
        </x-card>

        <x-card :padded="false">
            <h3 class="font-display font-semibold text-sm p-4 pb-2">Sparepart</h3>
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-y border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2">Barang</th>
                        <th class="text-right font-semibold px-4 py-2">Qty</th>
                        <th class="text-right font-semibold px-4 py-2">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-line last:border-0">
                        <td class="px-4 py-2">Roller CVT Set</td>
                        <td class="px-4 py-2 text-right font-mono">1</td>
                        <td class="px-4 py-2 text-right font-mono">65.000</td>
                    </tr>
                </tbody>
            </table>
        </x-card>
    </div>

    <div class="flex justify-end mt-4">
        <x-card class="w-80">
            <div class="flex justify-between text-sm"><span class="text-steel">Total Harga Supplier</span><span class="font-mono">115.000</span></div>
            <div class="flex justify-between text-sm mt-1"><span class="text-steel">Total Harga Nett</span><span class="font-mono">140.000</span></div>
            <div class="flex justify-between text-sm mt-1 pt-2 border-t border-line"><span class="font-medium">Margin</span><span class="font-mono font-semibold text-lunas">25.000</span></div>
        </x-card>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <x-button as="a" href="{{ route('cetak.tanda-terima-service', $id) }}" target="_blank" variant="secondary"><x-icon name="printer" class="w-4 h-4" /> Cetak Nota Service</x-button>
        <x-button variant="primary" onclick="window.toastSukses('Status service diperbarui.')">Lanjutkan Status</x-button>
    </div>

</x-app-layout>
