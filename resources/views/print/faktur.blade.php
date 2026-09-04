@php
    $realId = \App\Services\IdHasher::decode($id);
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<x-print-layout title="Faktur Penjualan {{ $id }}">
<style>
    @media print {
        @page {
            size: A5 landscape;
            margin: 5mm;
        }
        html, body {
            width: 210mm;
            height: 148mm;
            background-color: #fff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .faktur-box {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@if($penjualan)
<div class="faktur-box w-full max-w-[210mm] mx-auto p-5 bg-white text-slate-900 font-sans border border-slate-200 rounded-xl shadow-md my-4 text-xs">
    <!-- Header Kop Toko & Faktur -->
    <div class="flex justify-between items-start border-b border-slate-300 pb-3 mb-3">
        <div class="flex items-center gap-3">
            @if($pengaturan->logo_url)
                <img alt="{{ $pengaturan->nama_toko }} Logo" class="h-10 w-auto object-contain shrink-0" src="{{ $pengaturan->logo_url }}"/>
            @endif
            <div>
                <h1 class="font-black text-lg text-[#B0181C] leading-tight">{{ strtoupper($pengaturan->nama_toko) }}</h1>
                <p class="text-[11px] text-slate-600 font-medium">{{ $pengaturan->alamat }} | WA: {{ $pengaturan->telepon }}</p>
                @if($pengaturan->slogan)
                    <p class="text-[10px] text-slate-500">{{ $pengaturan->slogan }}</p>
                @endif
            </div>
        </div>
        <div class="text-right">
            <h2 class="font-black text-base text-slate-800 tracking-wider">FAKTUR PENJUALAN</h2>
            <p class="font-mono text-xs font-bold text-[#B0181C] mt-0.5">{{ $penjualan->nomor_nota }}</p>
            <p class="text-[11px] text-slate-500">Tanggal: {{ $penjualan->created_at->translatedFormat('d F Y H:i') }}</p>
        </div>
    </div>

    <!-- Info Customer & Status Transaksi -->
    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-2.5 rounded-lg mb-3 border border-slate-200 text-xs">
        <div>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kepada Yth:</p>
            <p class="font-bold text-slate-900 text-sm">{{ $penjualan->customer->nama ?? 'Pelanggan Umum' }}</p>
            <p class="text-[11px] text-slate-600 font-medium">{{ $penjualan->customer->alamat ?? 'Sidoarjo / Surabaya' }}</p>
        </div>
        <div class="text-right space-y-0.5 text-[11px]">
            <p><span class="text-slate-500">Status Pembayaran:</span> <span class="font-bold {{ $penjualan->status_bayar === 'lunas' ? 'text-emerald-600' : 'text-rajawali' }}">{{ $penjualan->status_bayar === 'lunas' ? 'LUNAS' : 'TEMPO / PIUTANG' }}</span></p>
            <p><span class="text-slate-500">Kasir / Staf:</span> <span class="font-bold text-slate-900">{{ $penjualan->user->name ?? 'Staff' }}</span></p>
        </div>
    </div>

    <!-- Tabel Detail Barang -->
    <table class="w-full text-xs mb-3 border-collapse">
        <thead>
            <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-y border-slate-300">
                <th class="py-1.5 px-2 text-left w-8">No</th>
                <th class="py-1.5 px-2 text-left">Deskripsi Produk / Sparepart</th>
                <th class="py-1.5 px-2 text-center w-16">Qty</th>
                <th class="py-1.5 px-2 text-right w-28">Harga Satuan</th>
                @if($penjualan->details->sum('diskon') > 0)
                    <th class="py-1.5 px-2 text-right w-24">Diskon</th>
                @endif
                <th class="py-1.5 px-2 text-right w-32">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-800">
            @foreach($penjualan->details as $index => $item)
                <tr>
                    <td class="py-1.5 px-2 text-slate-500">{{ $index + 1 }}</td>
                    <td class="py-1.5 px-2 font-bold text-slate-900">{{ $item->barang->nama }}</td>
                    <td class="py-1.5 px-2 text-center font-mono font-bold">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }}</td>
                    <td class="py-1.5 px-2 text-right font-mono">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    @if($penjualan->details->sum('diskon') > 0)
                        <td class="py-1.5 px-2 text-right font-mono text-rajawali">-Rp {{ number_format($item->diskon, 0, ',', '.') }}</td>
                    @endif
                    <td class="py-1.5 px-2 text-right font-mono font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Ringkasan & Catatan -->
    <div class="flex justify-between items-start border-t border-slate-300 pt-3">
        <div class="text-[10px] text-slate-500 space-y-0.5 max-w-xs">
            <p class="font-bold text-slate-700">Catatan &amp; Garansi:</p>
            <p>• Suku cadang &amp; jasa terbukti original &amp; bergaransi resmi.</p>
            <p>• Pembayaran sah setelah dana diterima Rajawali Motor.</p>
        </div>
        <div class="w-64 space-y-1 text-xs font-bold text-right">
            <div class="flex justify-between text-slate-700">
                <span>SUBTOTAL:</span>
                <span class="font-mono">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
                <div class="flex justify-between text-rajawali">
                    <span>DISKON NOTA:</span>
                    <span class="font-mono">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($penjualan->pajak > 0)
                <div class="flex justify-between text-slate-700">
                    <span>PAJAK:</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-[#B0181C] text-sm pt-1 border-t border-slate-300">
                <span>TOTAL AKHIR:</span>
                <span class="font-mono">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->metode_pembayaran === 'tempo')
                <div class="flex justify-between text-slate-600 text-[11px]">
                    <span>UANG MUKA (DP):</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-rajawali text-[11px]">
                    <span>SISA PIUTANG:</span>
                    <span class="font-mono">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Tanda Tangan Side-by-Side -->
    <div class="grid grid-cols-2 gap-6 text-center text-xs text-slate-600 mt-6 pt-4 border-t border-slate-200">
        <div>
            <p class="mb-8 font-bold">Penerima / Pelanggan,</p>
            <p class="font-bold text-slate-900 border-b border-slate-400 w-36 mx-auto pb-0.5">{{ $penjualan->customer->nama ?? '..........................' }}</p>
        </div>
        <div>
            <p class="mb-8 font-bold">Hormat Kami (Rajawali Motor),</p>
            <p class="font-bold text-slate-900 border-b border-slate-400 w-36 mx-auto pb-0.5">{{ $penjualan->user->name ?? 'Staff' }}</p>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center text-slate-600 font-bold">Faktur Penjualan tidak ditemukan.</div>
@endif
</x-print-layout>
