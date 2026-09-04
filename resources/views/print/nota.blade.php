@php
    $realId = \App\Services\IdHasher::decode($id);
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<x-print-layout title="Nota {{ $id }}">
    <x-slot:actions>
        <div class="inline-flex rounded-xl bg-slate-800 p-1 border border-slate-700 text-xs" x-data="{
            size: new URLSearchParams(window.location.search).get('size') || localStorage.getItem('thermal_paper_size') || '80',
            pilihSize(s) {
                this.size = s;
                localStorage.setItem('thermal_paper_size', s);
                const url = new URL(window.location);
                url.searchParams.set('size', s);
                window.history.replaceState({}, '', url);
                // Update pdf link
                const pdfBtn = document.querySelector('a[href*=\'/pdf\']');
                if (pdfBtn) {
                    const pdfUrl = new URL(pdfBtn.href);
                    pdfUrl.searchParams.set('size', s);
                    pdfBtn.href = pdfUrl.toString();
                }
                // Dispatch event
                window.dispatchEvent(new CustomEvent('change-paper-size', { detail: s }));
            }
        }">
            <button type="button" @click="pilihSize('80')" :class="size === '80' ? 'bg-[#B0181C] text-white shadow font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1 rounded-lg transition text-xs flex items-center gap-1">
                <span>Thermal 80mm</span>
            </button>
            <button type="button" @click="pilihSize('58')" :class="size === '58' ? 'bg-[#B0181C] text-white shadow font-bold' : 'text-slate-300 hover:text-white'" class="px-2.5 py-1 rounded-lg transition text-xs flex items-center gap-1">
                <span>Thermal 58mm</span>
            </button>
        </div>
    </x-slot:actions>

    <div x-data="{
        paperSize: new URLSearchParams(window.location.search).get('size') || localStorage.getItem('thermal_paper_size') || '80',
        init() {
            window.addEventListener('change-paper-size', (e) => {
                this.paperSize = e.detail;
            });
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => window.print(), 500);
            }
        }
    }" class="w-full flex justify-center">

    <style>
        @media print {
            @page {
                size: var(--print-paper-size, 80mm) auto;
                margin: 0 !important;
            }
            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .struk-box {
                border: none !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 1.5mm !important;
                width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    @if($penjualan)
    <div :class="paperSize === '58' ? 'max-w-[58mm] text-[10.5px] leading-tight p-2' : 'max-w-[80mm] text-xs leading-relaxed p-4'"
         :style="'--print-paper-size: ' + (paperSize === '58' ? '58mm' : '80mm')"
         class="struk-box mx-auto font-mono bg-white text-black border border-slate-200 rounded-lg shadow-sm transition-all duration-200">
        
        <div class="text-center space-y-1 mb-2">
            @if($pengaturan->logo_url)
                <img alt="{{ $pengaturan->nama_toko }} Logo" class="h-9 w-auto mx-auto object-contain mb-1" src="{{ $pengaturan->logo_url }}"/>
            @endif
            <p class="font-bold text-sm tracking-wide leading-tight">{{ strtoupper($pengaturan->nama_toko) }}</p>
            @if($pengaturan->slogan)
                <p class="text-[9px] text-slate-600 italic leading-tight">{{ $pengaturan->slogan }}</p>
            @endif
            <p class="text-[10px] leading-tight">{{ $pengaturan->alamat }}</p>
            @if($pengaturan->telepon)
                <p class="text-[10px] leading-tight">Telp/WA: {{ $pengaturan->telepon }}</p>
            @endif
        </div>

        <div class="border-t border-dashed border-black my-1.5"></div>

        <div class="space-y-0.5 text-[10.5px]">
            <div class="flex justify-between"><span>No Nota:</span><span class="font-bold font-mono">{{ $penjualan->nomor_nota }}</span></div>
            <div class="flex justify-between"><span>Tanggal:</span><span>{{ $penjualan->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Customer:</span><span class="font-bold">{{ $penjualan->customer->nama ?? 'Umum' }}</span></div>
            <div class="flex justify-between"><span>Kasir:</span><span>{{ $penjualan->user->name ?? 'Staff' }}</span></div>
        </div>

        <div class="border-t border-dashed border-black my-1.5"></div>

        <!-- Daftar Barang -->
        <div class="space-y-1.5">
            @foreach($penjualan->details as $d)
                <div>
                    <div class="font-bold leading-tight">{{ $d->barang->nama }}</div>
                    <div class="flex justify-between pl-1 text-[10px]">
                        <span>
                            {{ rtrim(rtrim(number_format((float) $d->qty, 3, ',', ''), '0'), ',') }} x 
                            {{ number_format($d->harga_satuan, 0, ',', '.') }}
                            @if($d->diskon > 0)
                                <span class="text-slate-600">(-{{ number_format($d->diskon, 0, ',', '.') }})</span>
                            @endif
                        </span>
                        <span class="font-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-dashed border-black my-1.5"></div>

        <!-- Perhitungan Total -->
        <div class="space-y-0.5">
            <div class="flex justify-between font-bold">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
                <div class="flex justify-between text-slate-700">
                    <span>Diskon Nota:</span>
                    <span>-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($penjualan->pajak > 0)
                <div class="flex justify-between">
                    <span>Pajak:</span>
                    <span>Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-black text-sm text-[#B0181C] pt-0.5 border-t border-black">
                <span>GRAND TOTAL:</span>
                <span>Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="border-t border-dashed border-black my-1.5"></div>

        <!-- Status Pembayaran -->
        @if($penjualan->status_bayar === 'lunas')
            <div class="flex justify-between font-black text-xs">
                <span>Status:</span>
                <span class="text-emerald-700 font-bold">LUNAS ({{ strtoupper($penjualan->metode_pembayaran) }})</span>
            </div>
        @else
            <div class="flex justify-between font-black text-xs">
                <span>Status:</span>
                <span class="text-[#B0181C] font-bold">TEMPO / PIUTANG</span>
            </div>
            @if($penjualan->uang_muka > 0)
                <div class="flex justify-between text-[10px]">
                    <span>Uang Muka (DP):</span>
                    <span>Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-[#B0181C]">
                <span>Sisa Piutang:</span>
                <span>Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span>
            </div>
            @if($penjualan->jatuh_tempo)
                <div class="flex justify-between text-[10px] text-slate-600">
                    <span>Jatuh Tempo:</span>
                    <span>{{ $penjualan->jatuh_tempo->format('d/m/Y') }}</span>
                </div>
            @endif
        @endif

        <div class="border-t border-dashed border-black my-2"></div>

        <div class="text-center space-y-0.5 text-[9.5px]">
            <p class="font-bold">Terima kasih atas kunjungan Anda!</p>
            <p class="text-black/75 leading-tight">{{ $pengaturan->footer_struk ?? 'Garansi Servis & Sparepart Original — Simpan Nota Ini' }}</p>
        </div>
    </div>
    @else
    <div class="p-8 text-center text-slate-600">Nota Penjualan tidak ditemukan.</div>
    @endif
    </div>
</x-print-layout>
