@php
    $realId = \App\Services\IdHasher::decode($id);
    $service = \App\Models\Service::with(['customer', 'montir'])
        ->where('nomor_dokumen', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<x-print-layout title="Tanda Terima Service {{ $id }}">
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
        .tanda-terima-box {
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
@if($service)
<div class="tanda-terima-box w-full max-w-[210mm] mx-auto p-5 bg-white text-slate-900 font-sans border border-slate-200 rounded-xl shadow-md my-4 text-xs">
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
            <h2 class="font-black text-base text-slate-800 tracking-wider">TANDA TERIMA SERVICE</h2>
            <p class="font-mono text-xs font-bold text-[#B0181C] mt-0.5">{{ $service->nomor_dokumen }}</p>
            <p class="text-[11px] text-slate-500">Tanggal: {{ $service->tanggal_masuk->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl mb-6 text-sm border border-slate-200/80 font-bold">
        <div class="space-y-1">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Customer:</p>
            <p class="font-bold text-slate-900">{{ $service->customer->nama }}</p>
            <p class="text-xs text-slate-600 font-medium">HP: {{ $service->customer->telepon ?? '-' }}</p>
        </div>
        <div class="space-y-1 text-right">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Kendaraan:</p>
            <p class="font-bold text-[#B0181C]">{{ $service->merk_type ?? '-' }}</p>
            <p class="text-xs text-slate-600 font-medium">Teknisi: {{ $service->montir->name ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-4 mb-6">
        <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Keluhan / Keluhan Pelanggan:</p>
            <p class="text-sm font-medium text-slate-800">{{ $service->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
        </div>
        <div class="border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Biaya Service &amp; Spareparts:</p>
            <p class="text-sm font-bold text-slate-900 font-mono">Rp {{ number_format($service->grand_total_nett, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8 text-center text-xs text-slate-600 mt-12 pt-8 border-t border-slate-200">
        <div>
            <p class="mb-16 font-bold">Pemilik Kendaraan,</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">{{ $service->customer->nama }}</p>
        </div>
        <div>
            <p class="mb-16 font-bold">Penerima (Rajawali Motor),</p>
            <p class="font-bold text-slate-900 border-b border-slate-300 w-40 mx-auto pb-1">{{ $service->montir->name ?? 'Staff' }}</p>
        </div>
    </div>
</div>
@else
<div class="p-8 text-center text-slate-600">Dokumen Service tidak ditemukan.</div>
@endif
</x-print-layout>
