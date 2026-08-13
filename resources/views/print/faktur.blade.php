@php
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $id)
        ->first();
@endphp
<x-print-layout title="Faktur Penjualan {{ $id }}">
@if($penjualan)
<div class="max-w-4xl mx-auto p-8 bg-white text-slate-900 font-sans border border-slate-200 rounded-2xl shadow-sm my-6">
    <!-- Header -->
    <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
        <div class="flex items-center gap-4">
            <img alt="Rajawali Motor Logo" class="h-14 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            <div>
                <h1 class="font-extrabold text-2xl text-[#B0181C]">RAJAWALI MOTOR</h1>
                <p class="text-xs text-slate-600 font-bold">Jl. Samanhudi No.102, Jasem, Sidoarjo | WA: +62 856-4888-8441</p>
                <p class="text-xs text-slate-500 font-medium">Spesialis Injeksi, Tune Up, Ganti Oli &amp; Body Repair</p>
            </div>
        </div>
        <div class="text-right font-bold">
            <h2 class="font-black text-xl text-slate-800 tracking-wider">FAKTUR PENJUALAN</h2>
            <p class="font-mono text-sm font-bold text-[#B0181C] mt-1">{{ $penjualan->nomor_nota }}</p>
            <p class="text-xs text-slate-500 mt-1">Tanggal: {{ $penjualan->created_at->format('d F Y') }}</p>
        </div>
    </div>

    <!-- Info Customer & Transaksi -->
    <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl mb-6 text-sm border border-slate-200/80 font-bold">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kepada Yth:</p>
            <p class="font-bold text-slate-900">{{ $penjualan->customer->nama ?? 'Pelanggan Umum' }}</p>
            <p class="text-xs text-slate-600 font-medium">{{ $penjualan->customer->alamat ?? 'Surabaya, Jawa Timur' }}</p>
        </div>
        <div class="text-right space-y-1 text-xs">
            <p><span class="text-slate-500">Metode Pembayaran:</span> <span class="font-bold text-slate-900">{{ strtoupper($penjualan->metode_pembayaran) }}</span></p>
            <p><span class="text-slate-500">Status Pembayaran:</span> <span class="font-bold {{ $penjualan->status_bayar === 'lunas' ? 'text-emerald-600' : 'text-rajawali' }}">{{ strtoupper($penjualan->status_bayar) }}</span></p>
            <p><span class="text-slate-500">Kasir / Staf:</span> <span class="font-bold text-slate-900">{{ $penjualan->user->name ?? 'Staff' }}</span></p>
        </div>
    </div>

    <!-- Table Barang -->
    <table class="w-full text-sm mb-6 border-collapse">
        <thead>
            <tr class="bg-slate-100 text-slate-700 font-bold text-xs uppercase border-b border-slate-200">
                <th class="py-3 px-4 text-left">No</th>
                <th class="py-3 px-4 text-left">Deskripsi Produk</th>
                <th class="py-3 px-4 text-center">Qty</th>
                <th class="py-3 px-4 text-right">Harga Satuan</th>
                @if($penjualan->details->sum('diskon') > 0)
                    <th class="py-3 px-4 text-right">Diskon</th>
                @endif
                <th class="py-3 px-4 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-800 font-medium">
            @foreach($penjualan->details as $index => $item)
                <tr>
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-bold">{{ $item->barang->nama }}</td>
                    <td class="py-3 px-4 text-center font-mono">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }}</td>
                    <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    @if($penjualan->details->sum('diskon') > 0)
                        <td class="py-3 px-4 text-right font-mono text-rajawali">-Rp {{ number_format($item->diskon, 0, ',', '.') }}</td>
                    @endif
                    <td class="py-3 px-4 text-right font-mono font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="flex justify-between items-end border-t border-slate-200 pt-6">
        <div class="text-xs text-slate-500 space-y-1">
            <p class="font-bold text-slate-700">Catatan:</p>
            <p>• Suku cadang &amp; jasa terbukti original &amp; memiliki garansi resmi.</p>
            <p>• Pembayaran sah setelah dana efektif masuk ke kas/bank Rajawali Motor.</p>
        </div>
        <div class="w-72 space-y-2 text-sm font-bold">
            <div class="flex justify-between font-bold text-slate-700">
                <span>SUBTOTAL:</span>
                <span class="font-mono">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
                <div class="flex justify-between text-rajawali font-bold">
                    <span>DISKON NOTA:</span>
                    <span class="font-mono">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($penjualan->pajak > 0)
                <div class="flex justify-between text-slate-700 font-bold">
                    <span>PAJAK:</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-[#B0181C] text-base pt-2 border-t border-slate-200">
                <span>TOTAL AKHIR:</span>
                <span class="font-mono">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->metode_pembayaran === 'tempo')
                <div class="flex justify-between text-slate-700">
                    <span>UANG MUKA (DP):</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-rajawali">
                    <span>SISA PIUTANG:</span>
                    <span class="font-mono">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="grid grid-cols-2 gap-8 text-center text-xs text-slate-600 mt-12 pt-8 border-t border-slate-200">
        <div>
            <p class="mb-16 font-bold">Penerima / Pelanggan,</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">{{ $penjualan->customer->nama ?? '' }}</p>
        </div>
        <div>
            <p class="mb-16 font-bold">Hormat Kami (Rajawali Motor),</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">{{ $penjualan->user->name ?? 'Staff' }}</p>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center text-slate-600 font-bold">Faktur Penjualan tidak ditemukan.</div>
@endif
</x-print-layout>
