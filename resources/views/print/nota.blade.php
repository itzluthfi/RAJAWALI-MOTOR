@php
    $items = [
        ['nama' => 'DISC PAD VARIO CBS', 'qty' => 2, 'harga' => 25000],
        ['nama' => 'OLI FEDERAL MATIC 1L', 'qty' => 1, 'harga' => 45000],
        ['nama' => 'JASA PASANG KAMPAS REM', 'qty' => 1, 'harga' => 15000],
    ];
    $total = collect($items)->sum(fn($i) => $i['qty'] * $i['harga']);
    $bayar = 120000;
    $kembali = $bayar - $total;
@endphp
<x-print-layout title="Nota {{ $id }}">
<div class="max-w-[80mm] mx-auto p-4 font-mono text-xs leading-relaxed bg-white text-black border border-slate-200 rounded-lg shadow-sm">
    <div class="text-center space-y-1 mb-3">
        <img alt="Rajawali Motor Logo" class="h-10 w-auto mx-auto object-contain mb-1" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
        <p class="font-bold text-sm tracking-wide">RAJAWALI MOTOR</p>
        <p class="text-[11px]">Siwalankerto Timur, Surabaya</p>
        <p class="text-[11px]">WA: +62 856-4888-8441 | Telp: (031) 8431234</p>
    </div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="flex justify-between"><span>No Nota:</span><span class="font-bold">{{ $id }}</span></div>
    <div class="flex justify-between"><span>Tanggal:</span><span>{{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span></div>
    <div class="flex justify-between"><span>Kasir:</span><span>Sari Wulandari</span></div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="space-y-1.5">
        @foreach($items as $i)
            <div>
                <div class="font-bold">{{ $i['nama'] }}</div>
                <div class="flex justify-between pl-2">
                    <span>{{ $i['qty'] }} x Rp {{ number_format($i['harga'], 0, ',', '.') }}</span>
                    <span class="font-bold">Rp {{ number_format($i['qty'] * $i['harga'], 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="flex justify-between font-bold text-sm"><span>TOTAL:</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
    <div class="flex justify-between"><span>Tunai:</span><span>Rp {{ number_format($bayar, 0, ',', '.') }}</span></div>
    <div class="flex justify-between font-bold"><span>Kembali:</span><span>Rp {{ number_format($kembali, 0, ',', '.') }}</span></div>
    <div class="border-t border-dashed border-black/40 my-3"></div>
    <div class="text-center space-y-1">
        <p class="font-bold">Terima kasih atas kunjungan Anda!</p>
        <p class="text-[10px] text-black/70">Garansi Servis 14 Hari — Simpan Nota Ini</p>
    </div>
</div>
</x-print-layout>
