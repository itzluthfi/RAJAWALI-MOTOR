@php
    $realId = \App\Services\IdHasher::decode($id);
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<x-print-layout title="Surat Jalan {{ $id }}">
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
        .surat-jalan-box {
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
<div class="surat-jalan-box w-full max-w-[210mm] mx-auto p-5 bg-white text-slate-900 font-sans border border-slate-200 rounded-xl shadow-md my-4 text-xs">
    <!-- Header -->
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
            <h2 class="font-black text-base text-slate-800 tracking-wider">SURAT JALAN PENGIRIMAN</h2>
            <p class="font-mono text-xs font-bold text-[#B0181C] mt-0.5">{{ $penjualan->nomor_nota }}</p>
            <p class="text-[11px] text-slate-500">Tanggal: {{ $penjualan->created_at->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <!-- Info Customer & Pengiriman -->
    <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl mb-6 text-sm border border-slate-200/80 font-bold">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kepada Yth / Alamat Kirim:</p>
            <p class="font-bold text-slate-900">{{ $penjualan->customer->nama ?? 'Pelanggan Umum' }}</p>
            <p class="text-xs text-slate-600 font-medium">{{ $penjualan->customer->alamat ?? 'Surabaya, Jawa Timur' }}</p>
        </div>
        <div class="text-right space-y-1 text-xs">
            <p><span class="text-slate-500">No. Referensi:</span> <span class="font-bold text-slate-900">Nota {{ $penjualan->nomor_nota }}</span></p>
            <p><span class="text-slate-500">Petugas Gudang:</span> <span class="font-bold text-slate-900">{{ $penjualan->user->name ?? 'Staff' }}</span></p>
        </div>
    </div>

    <!-- Table Barang (No Prices) -->
    <table class="w-full text-sm mb-6 border-collapse">
        <thead>
            <tr class="bg-slate-100 text-slate-700 font-bold text-xs uppercase border-b border-slate-200">
                <th class="py-3 px-4 text-left w-12">No</th>
                <th class="py-3 px-4 text-left">Kode Barang</th>
                <th class="py-3 px-4 text-left">Nama / Deskripsi Barang</th>
                <th class="py-3 px-4 text-center w-28">Qty</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-800 font-medium">
            @foreach($penjualan->details as $index => $item)
                <tr>
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-mono text-xs font-bold text-rajawali">{{ $item->barang->kode }}</td>
                    <td class="py-3 px-4 font-bold">{{ $item->barang->nama }}</td>
                    <td class="py-3 px-4 text-center font-mono font-bold">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-xs text-slate-500 mt-4 italic font-bold">
        Catatan: Harap periksa barang kiriman dengan seksama sebelum menandatangani surat jalan ini.
    </div>

    <!-- Tanda Tangan Tiga Kolom (Penerima, Pengirim, Sopir) -->
    <div class="grid grid-cols-3 gap-6 text-center text-xs text-slate-600 mt-16 pt-8 border-t border-slate-200">
        <div>
            <p class="mb-16 font-bold">Penerima / Customer,</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-32 mx-auto pb-1"></p>
        </div>
        <div>
            <p class="mb-16 font-bold">Pengirim / Sopir,</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-32 mx-auto pb-1"></p>
        </div>
        <div>
            <p class="mb-16 font-bold">Hormat Kami (Rajawali Motor),</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-32 mx-auto pb-1">{{ $penjualan->user->name ?? 'Staff' }}</p>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center text-slate-600 font-bold">Surat Jalan tidak ditemukan.</div>
@endif
</x-print-layout>
