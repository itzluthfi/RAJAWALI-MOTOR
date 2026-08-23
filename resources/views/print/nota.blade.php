@php
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $id)
        ->first();
@endphp
<x-print-layout title="Nota {{ $id }}">
<style>
    @media print {
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            background-color: #fff !important;
            padding: 0 !important;
        }
        .struk-box {
            border: none !important;
            box-shadow: none !important;
            padding: 2mm !important;
            max-width: 100% !important;
        }
    }
</style>
@if($penjualan)
<div class="struk-box max-w-[80mm] mx-auto p-4 font-mono text-xs leading-relaxed bg-white text-black border border-slate-200 rounded-lg shadow-sm">
    <div class="text-center space-y-1 mb-3">
        <img alt="Rajawali Motor Logo" class="h-10 w-auto mx-auto object-contain mb-1" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
        <p class="font-bold text-sm tracking-wide">RAJAWALI MOTOR</p>
        <p class="text-[11px]">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
        <p class="text-[11px]">WA: +62 856-4888-8441 | Telp: (031) 8431234</p>
    </div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="flex justify-between"><span>No Nota:</span><span class="font-bold">{{ $penjualan->nomor_nota }}</span></div>
    <div class="flex justify-between"><span>Tanggal:</span><span>{{ $penjualan->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span></div>
    <div class="flex justify-between"><span>Customer:</span><span>{{ $penjualan->customer->nama ?? 'Umum' }}</span></div>
    <div class="flex justify-between"><span>Kasir:</span><span>{{ $penjualan->user->name ?? 'Staff' }}</span></div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="space-y-1.5">
        @foreach($penjualan->details as $d)
            <div>
                <div class="font-bold">{{ $d->barang->nama }}</div>
                <div class="flex justify-between pl-2">
                    <span>
                        {{ rtrim(rtrim(number_format((float) $d->qty, 3, ',', ''), '0'), ',') }} x 
                        Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}
                        @if($d->diskon > 0)
                            (Disc: Rp {{ number_format($d->diskon, 0, ',', '.') }})
                        @endif
                    </span>
                    <span class="font-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="flex justify-between font-bold text-sm"><span>TOTAL:</span><span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span></div>
    @if($penjualan->diskon > 0)
        <div class="flex justify-between"><span>Diskon Nota:</span><span>Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span></div>
    @endif
    @if($penjualan->pajak > 0)
        <div class="flex justify-between"><span>Pajak:</span><span>Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span></div>
    @endif
    <div class="flex justify-between font-bold text-sm text-[#B0181C]"><span>GRAND TOTAL:</span><span>Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span></div>
    
    <div class="border-t border-dashed border-black/40 my-2"></div>
    <div class="flex justify-between"><span>Metode:</span><span>{{ strtoupper($penjualan->metode_pembayaran) }}</span></div>
    @if($penjualan->metode_pembayaran === 'tempo')
        <div class="flex justify-between"><span>Uang Muka (DP):</span><span>Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span></div>
        <div class="flex justify-between font-bold text-rajawali"><span>Sisa Piutang:</span><span>Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span></div>
    @endif
    <div class="border-t border-dashed border-black/40 my-3"></div>
    <div class="text-center space-y-1">
        <p class="font-bold">Terima kasih atas kunjungan Anda!</p>
        <p class="text-[10px] text-black/70 font-bold">Garansi Servis & Sparepart Original — Simpan Nota Ini</p>
    </div>
</div>
@else
<div class="p-8 text-center text-slate-600">Nota Penjualan tidak ditemukan.</div>
@endif
</x-print-layout>
