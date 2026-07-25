<x-print-layout title="Tanda Terima Service {{ $id }}">
<div class="max-w-3xl mx-auto p-8 bg-white text-slate-900 font-sans border border-slate-200 rounded-2xl shadow-sm my-6">
    <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
        <div class="flex items-center gap-4">
            <img alt="Rajawali Motor Logo" class="h-12 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            <div>
                <h1 class="font-extrabold text-xl text-[#B0181C]">RAJAWALI MOTOR</h1>
                <p class="text-xs text-slate-600">Siwalankerto Timur, Surabaya | WA: +62 856-4888-8441</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="font-black text-lg text-slate-800 tracking-wider">TANDA TERIMA SERVICE</h2>
            <p class="font-mono text-sm font-bold text-[#B0181C] mt-1">{{ $id }}</p>
            <p class="text-xs text-slate-500 mt-1">Tanggal: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl mb-6 text-sm border border-slate-200/80">
        <div class="space-y-1">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Customer:</p>
            <p class="font-bold text-slate-900">Budi Santoso</p>
            <p class="text-xs text-slate-600">HP: 0812-3456-7890</p>
        </div>
        <div class="space-y-1 text-right">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Kendaraan:</p>
            <p class="font-bold text-[#B0181C]">L 1234 ABC — Honda Vario 150</p>
            <p class="text-xs text-slate-600">Teknisi: Wawan Setiawan (Montir 1)</p>
        </div>
    </div>

    <div class="space-y-4 mb-6">
        <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Keluhan / Keluhan Pelanggan:</p>
            <p class="text-sm font-medium text-slate-800">Mesin brebet saat tarikan awal, ganti oli rutin, dan cek rem belakang.</p>
        </div>
        <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Estimasi Pengerjaan &amp; Biaya:</p>
            <p class="text-sm font-bold text-slate-900">Rp 185.000 — Estimasi Selesai: 2 Jam</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8 text-center text-xs text-slate-600 mt-12 pt-8 border-t border-slate-200">
        <div>
            <p class="mb-16">Pemilik Kendaraan,</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">Budi Santoso</p>
        </div>
        <div>
            <p class="mb-16">Penerima (Rajawali Motor),</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">Wawan Setiawan</p>
        </div>
    </div>
</div>
</x-print-layout>
