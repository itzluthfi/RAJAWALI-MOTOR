@php
    $realId = \App\Services\IdHasher::decode($id);
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
@endphp
<x-print-layout title="Surat Jalan {{ $id }}">
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
            <h2 class="font-black text-xl text-slate-800 tracking-wider">SURAT JALAN</h2>
            <p class="font-mono text-sm font-bold text-[#B0181C] mt-1">{{ $penjualan->nomor_nota }}</p>
            <p class="text-xs text-slate-500 mt-1">Tanggal: {{ $penjualan->created_at->format('d F Y') }}</p>
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
