@php $peranSaya = auth()->user()->peran; @endphp
<x-app-layout title="Dashboard">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
        <x-card>
            <p class="text-xs font-semibold text-steel uppercase tracking-wide">Omzet Hari Ini</p>
            <p class="font-mono font-medium text-2xl mt-1">Rp 4.850.000</p>
            <p class="text-xs text-lunas mt-1">+12% dari kemarin</p>
        </x-card>
        <x-card>
            <p class="text-xs font-semibold text-steel uppercase tracking-wide">Jumlah Nota</p>
            <p class="font-mono font-medium text-2xl mt-1">38</p>
            <p class="text-xs text-steel mt-1">Transaksi hari ini</p>
        </x-card>
        @if($peranSaya === 'owner')
            <x-card>
                <p class="text-xs font-semibold text-steel uppercase tracking-wide">Laba Kotor</p>
                <p class="font-mono font-medium text-2xl mt-1 text-rajawali">Rp 1.120.000</p>
                <p class="text-xs text-steel mt-1">Hanya tampil untuk Owner</p>
            </x-card>
        @endif
        @if(in_array($peranSaya, ['owner', 'admin']))
            <x-card>
                <p class="text-xs font-semibold text-steel uppercase tracking-wide">Piutang Jatuh Tempo</p>
                <p class="font-mono font-medium text-2xl mt-1">Rp 2.340.000</p>
                <p class="text-xs text-marka mt-1">5 customer minggu ini</p>
            </x-card>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="lg:col-span-2">
            <h3 class="font-display font-semibold text-sm mb-3">Penjualan 30 Hari Terakhir</h3>
            <div class="h-56 flex items-end gap-1">
                @foreach ([40,55,35,60,50,70,65,45,80,60,55,75,68,50,90,60,55,70,65,80,75,60,55,85,70,65,90,80,75,95] as $tinggi)
                    <div class="flex-1 bg-rajawali/15 hover:bg-rajawali/30 transition rounded-t-sm" style="height: {{ $tinggi }}%"></div>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <h3 class="font-display font-semibold text-sm mb-3">Stok Menipis</h3>
            <ul class="space-y-2 text-sm">
                @foreach ([
                    ['nama' => 'Oli Federal 1L', 'stok' => 3, 'min' => 10],
                    ['nama' => 'Kampas Rem Vario', 'stok' => 2, 'min' => 8],
                    ['nama' => 'Busi NGK CPR8EA', 'stok' => 5, 'min' => 15],
                ] as $b)
                    <li class="flex justify-between items-center border-b border-line pb-2 last:border-0">
                        <span class="text-ink truncate">{{ $b['nama'] }}</span>
                        <span class="font-mono text-rajawali text-xs">{{ $b['stok'] }}/{{ $b['min'] }}</span>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <x-card>
            <h3 class="font-display font-semibold text-sm mb-3">Service Belum Diambil</h3>
            <ul class="space-y-2 text-sm">
                <li class="flex justify-between border-b border-line pb-2"><span>SV2026000045 — Honda Beat, Andi</span><x-badge status="proses">Selesai</x-badge></li>
                <li class="flex justify-between border-b border-line pb-2"><span>SV2026000041 — Yamaha NMax, Sinta</span><x-badge status="proses">Dikerjakan</x-badge></li>
            </ul>
        </x-card>
        @if(in_array($peranSaya, ['owner', 'admin']))
            <x-card>
                <h3 class="font-display font-semibold text-sm mb-3">Piutang Jatuh Tempo Minggu Ini</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between border-b border-line pb-2"><span>Toko Jaya Motor</span><span class="font-mono">Rp 1.200.000</span></li>
                    <li class="flex justify-between border-b border-line pb-2"><span>Bengkel Sumber Rejeki</span><span class="font-mono">Rp 1.140.000</span></li>
                </ul>
            </x-card>
        @endif
    </div>

</x-app-layout>
